<?php
/**
 * ==========================================
 * ENDPOINT OPTIMIZADO PARA CARGAR PUBLICACIONES
 * Retorna JSON con publicaciones paginadas
 * ==========================================
 */

include_once("session-compartida.php");
require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH . "/main-app/class/SocialComentarios.php");
require_once(ROOT_PATH . "/main-app/class/SocialReacciones.php");

// Instanciar clase de usuarios
$usuariosClase = new UsuariosFunciones();

header('Content-Type: application/json');

try {
    // Obtener datos del request
    $input = json_decode(file_get_contents("php://input"), true);
    $pagina = isset($input['pagina']) ? intval($input['pagina']) : 0;
    $limite = isset($input['limite']) ? intval($input['limite']) : 10;
    
    // Validaciones
    if ($limite > 50) $limite = 50; // Máximo 50 posts por petición
    if ($pagina < 0) $pagina = 0;

    // ==========================================
    // CONSTRUIR FILTROS DE BÚSQUEDA
    // ==========================================
    $filtro = '';
    
    // IMPORTANTE: Buscar parámetro de búsqueda
    $busqueda = '';
    if (isset($_GET["busqueda"])) {
        $busqueda = trim($_GET["busqueda"]);
    }
    
    // Logging para debugging
    error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    error_log("🔍 BÚSQUEDA RECIBIDA: '" . $busqueda . "'");
    error_log("📡 GET params: " . print_r($_GET, true));
    
    // Aplicar filtro de búsqueda
    if (!empty($busqueda)) {
        $busquedaEscaped = mysqli_real_escape_string($conexion, $busqueda);
        $filtro .= " AND (not_titulo LIKE '%{$busquedaEscaped}%' OR not_descripcion LIKE '%{$busquedaEscaped}%')";
        error_log("📊 FILTRO APLICADO: " . $filtro);
    } else {
        error_log("⚠️ NO HAY BÚSQUEDA - Mostrando todos los posts");
    }
    
    // Filtro de usuario específico
    if (isset($_GET["usuario"]) && !empty($_GET["usuario"])) {
        $usuario = base64_decode($_GET["usuario"]);
        $usuarioEscaped = mysqli_real_escape_string($conexion, $usuario);
        $filtro .= " AND not_usuario='{$usuarioEscaped}'";
        error_log("👤 Filtro de usuario: " . $usuarioEscaped);
    }
    
    error_log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

    // Consulta optimizada con INNER JOIN y campos específicos
    $sql = "SELECT 
                not_id as id,
                not_usuario as usuario,
                not_titulo as titulo,
                not_descripcion as descripcion,
                not_fecha as fecha,
                not_estado as estado,
                not_imagen as imagen,
                not_video as video,
                not_enlace_video2 as enlace_video2,
                not_archivo as archivo,
                not_descripcion_pie as descripcion_pie,
                not_global as global,
                uss_id,
                uss_nombre as nombreUsuario,
                uss_foto,
                uss_tipo as usuarioTipo
            FROM " . $baseDatosServicios . ".social_noticias
            INNER JOIN " . BD_GENERAL . ".usuarios ON uss_id = not_usuario 
                AND institucion = {$config['conf_id_institucion']} 
                AND year = {$_SESSION["bd"]}
            WHERE not_estado != 2
                AND (not_estado = 1 OR (not_estado = 0 AND not_usuario = '{$_SESSION["id"]}'))
                AND (not_para LIKE '%{$datosUsuarioActual['uss_tipo']}%' OR not_usuario = '{$_SESSION["id"]}')
                AND not_year = '{$_SESSION["bd"]}'
                AND (not_institucion = '{$config['conf_id_institucion']}' OR not_global = 'SI')
                {$filtro}
            ORDER BY not_id DESC
            LIMIT {$limite} OFFSET {$pagina}";
    
    // Log de la consulta completa
    error_log("📝 SQL: " . $sql);

    $consulta = mysqli_query($conexion, $sql);
    
    if (!$consulta) {
        throw new Exception("Error en la consulta: " . mysqli_error($conexion));
    }

    $posts = [];
    
    while ($row = mysqli_fetch_array($consulta, MYSQLI_ASSOC)) {
        
        // Validar permisos para estudiantes
        if ($datosUsuarioActual['uss_tipo'] == 4) {
            include("verificar-usuario.php");
            $noticiasCursos = mysqli_query($conexion, "SELECT notpc_curso FROM " . $baseDatosServicios . ".social_noticias_cursos WHERE notpc_noticia = '{$row['id']}'");
            
            if (mysqli_num_rows($noticiasCursos) > 0) {
                $permitida = false;
                while ($notCurso = mysqli_fetch_array($noticiasCursos, MYSQLI_ASSOC)) {
                    if ($notCurso['notpc_curso'] == $datosEstudianteActual['mat_grado']) {
                        $permitida = true;
                        break;
                    }
                }
                if (!$permitida) continue;
            }
        }

        // Obtener foto del usuario
        $fotoUsr = $usuariosClase->verificarFoto($row['uss_foto']);

        // Obtener URLs de archivos desde Firebase Storage
        $imagenUrl = null;
        $archivoUrl = null;

        if (!empty($row['imagen'])) {
            try {
                $existe = $storage->getBucket()->object(FILE_PUBLICACIONES . $row['imagen'])->exists();
                if ($existe) {
                    $imagenUrl = $storage->getBucket()->object(FILE_PUBLICACIONES . $row['imagen'])->signedUrl(new DateTime('tomorrow'));
                }
            } catch (Exception $e) {
                // Ignorar errores de storage
            }
        }

        if (!empty($row['archivo'])) {
            try {
                $existeArchivo = $storage->getBucket()->object(FILE_PUBLICACIONES . $row['archivo'])->exists();
                if ($existeArchivo) {
                    $archivoUrl = $storage->getBucket()->object(FILE_PUBLICACIONES . $row['archivo'])->signedUrl(new DateTime('tomorrow'));
                }
            } catch (Exception $e) {
                // Ignorar errores de storage
            }
        }

        // Contar reacciones y comentarios
        $parametrosReacciones = ["npr_noticia" => $row['id']];
        $numReacciones = intval(SocialReacciones::contar($parametrosReacciones));
        
        $parametrosReacciones["npr_usuario"] = $_SESSION["id"];
        $usrReaccion = SocialReacciones::consultar($parametrosReacciones);
        
        $parametrosComentarios = ["ncm_noticia" => $row['id'], "ncm_padre" => 0];
        $numComentarios = intval(SocialComentarios::contar($parametrosComentarios));

        // Construir objeto post
        $post = [
            'id' => $row['id'],
            'usuario' => $row['usuario'],
            'nombreUsuario' => $row['nombreUsuario'],
            'usuarioTipo' => $row['usuarioTipo'],
            'foto' => $fotoUsr,
            'titulo' => $row['titulo'],
            'descripcion' => $row['descripcion'],
            'descripcionPie' => $row['descripcion_pie'],
            'fecha' => $row['fecha'],
            'estado' => $row['estado'],
            'global' => $row['global'],
            'imagen' => $row['imagen'],
            'imagenUrl' => $imagenUrl,
            'video' => $row['video'],
            'enlace_video2' => $row['enlace_video2'],
            'archivo' => $row['archivo'],
            'archivoUrl' => $archivoUrl,
            'reacciones' => $numReacciones,
            'comentarios' => $numComentarios,
            'usuarioReaccion' => isset($usrReaccion['npr_reaccion']) ? intval($usrReaccion['npr_reaccion']) : null
        ];

        $posts[] = $post;
    }

    // Log para debugging
    error_log("✅ Posts encontrados: " . count($posts));
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'posts' => $posts,
        'count' => count($posts),
        'pagina' => $pagina,
        'limite' => $limite,
        'busqueda' => $busqueda, // Para debugging
        'filtro' => $filtro // Para debugging
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    // Registrar error
    error_log("Error en noticias-publicaciones-cargar.php: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar las publicaciones',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

