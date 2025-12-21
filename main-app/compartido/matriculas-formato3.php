<?php
if(!isset($_REQUEST["ref"]) or $_REQUEST["ref"]=="" or $_SERVER['HTTP_REFERER']==""){
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=220";</script>';
	exit();	
}

include("session-compartida.php");
$idPaginaInterna = 'DT0249';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

// Configuraciones para manejo de archivos grandes
set_time_limit(300);
ini_set('memory_limit', '256M');

$year=$_SESSION["bd"];
if(!empty($_POST["year"])){
    $year=$_POST["year"];
}

$ref="";
if(!empty($_GET["ref"])){
    $ref=base64_decode($_GET["ref"]);
}
if(!empty($_POST["ref"])){
    $ref=$_POST["ref"];
}

// Obtener datos del estudiante (ya optimizado)
$resultado = Estudiantes::obtenerDatosEstudiante($ref, $year);

// Obtener acudientes
$acudiente1 = !empty($resultado['mat_acudiente']) ? UsuariosPadre::sesionUsuario($resultado['mat_acudiente'], "", $config['conf_id_institucion'], $year) : array();
$acudiente2 = !empty($resultado['mat_acudiente2']) ? UsuariosPadre::sesionUsuario($resultado['mat_acudiente2'], "", $config['conf_id_institucion'], $year) : array();

// Obtener tipo (sexo) de forma optimizada
$tipo = array('ogen_nombre' => 'No especificado');
if (!empty($resultado['mat_tipo'])) {
    try {
        $consultaTipo = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_id='".$resultado['mat_tipo']."'");
        if ($consultaTipo && mysqli_num_rows($consultaTipo) > 0) {
            $tipo = mysqli_fetch_array($consultaTipo, MYSQLI_BOTH);
        }
    } catch (Exception $e) {
        echo "Error al consultar tipo: ".$e->getMessage();
    }
}

// Calcular edad
$edad = '';
if (!empty($resultado['mat_fecha_nacimiento'])) {
    $fechaNac = new DateTime($resultado['mat_fecha_nacimiento']);
    $hoy = new DateTime();
    $edadObj = $hoy->diff($fechaNac);
    $edad = $edadObj->y . ' años';
}

// Verificar logo
$logoPath = ROOT_PATH . '/main-app/files/images/logo/' . $informacion_inst["info_logo"];
$mostrarLogo = !empty($informacion_inst["info_logo"]) && file_exists($logoPath);

// Logo por defecto SVG si no existe
$logoDefault = 'data:image/svg+xml;base64,' . base64_encode('
<svg width="150" height="150" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect width="150" height="150" fill="url(#grad)" rx="10"/>
    <text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="white" font-size="28" font-weight="bold" font-family="Arial, sans-serif">SINTIA</text>
</svg>
');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de Matrícula - <?= $resultado['mat_nombres'] . ' ' . $resultado['mat_primer_apellido']; ?></title>
    <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
    
    <style>
        /* ============================
           ESTILOS GENERALES
           ============================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container-matricula {
            max-width: 850px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 1px solid #ddd;
        }

        /* ============================
           BOTONES FLOTANTES
           ============================ */
        .botones-accion {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-flotante {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border: 1px solid #999;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            min-width: 140px;
            text-decoration: none;
            background: white;
            color: #333;
        }

        .btn-print {
            border-color: #2c3e50;
            color: #2c3e50;
        }

        .btn-print:hover {
            background: #2c3e50;
            color: white;
        }

        .btn-close {
            border-color: #7f8c8d;
            color: #7f8c8d;
        }

        .btn-close:hover {
            background: #7f8c8d;
            color: white;
        }

        /* ============================
           ENCABEZADO
           ============================ */
        .tabla-encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .tabla-encabezado td {
            text-align: center;
            vertical-align: middle;
            padding: 10px;
        }

        .logo-institucion {
            max-width: 100px;
            height: auto;
        }

        .nombre-institucion {
            font-size: 16pt;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 5px 0;
        }

        .titulo-documento {
            font-size: 11pt;
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
        }

        /* ============================
           DATOS BÁSICOS
           ============================ */
        .datos-basicos {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .dato-item {
            display: flex;
            flex-direction: column;
            border-right: 1px solid #dee2e6;
            padding-right: 15px;
        }

        .dato-item:last-child {
            border-right: none;
        }

        .dato-label {
            font-size: 8pt;
            color: #666;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .dato-valor {
            font-size: 10pt;
            font-weight: bold;
            color: #000;
        }

        /* ============================
           SECCIONES
           ============================ */
        .seccion-titulo {
            background: #2c3e50;
            color: white;
            padding: 10px 15px;
            margin: 20px 0 12px 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-left: 4px solid #000;
        }

        .seccion-titulo-secundario {
            background: #6c757d;
            color: white;
            padding: 8px 12px;
            margin: 15px 0 10px 0;
            font-size: 10pt;
            font-weight: 600;
            text-transform: uppercase;
            border-left: 4px solid #495057;
        }

        /* ============================
           TABLAS
           ============================ */
        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
        }

        .tabla-datos td {
            padding: 8px 12px;
            border: 1px solid #000;
            font-size: 9pt;
            vertical-align: middle;
        }

        .tabla-datos .td-label {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #000;
            width: 20%;
        }

        .tabla-datos .td-valor {
            color: #000;
            font-weight: 600;
            width: 30%;
        }

        /* ============================
           COMPROMISOS Y FIRMAS
           ============================ */
        .seccion-compromisos {
            margin-top: 25px;
            padding: 15px;
            background: white;
            border: 1px solid #000;
        }

        .compromisos-titulo {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            text-align: center;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .compromisos-texto {
            font-size: 9pt;
            text-align: justify;
            color: #000;
            line-height: 1.5;
        }

        .seccion-firmas {
            margin-top: 40px;
            padding-top: 30px;
        }

        .tabla-firmas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }

        .tabla-firmas td {
            text-align: center;
            vertical-align: bottom;
            padding: 15px 20px;
        }

        .firma-linea {
            border-top: 1px solid #000;
            margin-bottom: 5px;
            margin-top: 40px;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }

        .firma-cargo {
            font-size: 8pt;
            font-weight: 600;
            color: #000;
            text-transform: uppercase;
        }

        /* ============================
           ESTILOS DE IMPRESIÓN
           ============================ */
        @media print {
            @page {
                size: letter;
                margin: 1cm;
            }

            body {
                background-color: white;
                padding: 0;
                color: #000;
            }

            .container-matricula {
                max-width: 100%;
                padding: 0;
                border: none;
            }

            .botones-accion {
                display: none !important;
            }

            .seccion-titulo {
                background: #2c3e50 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .seccion-titulo-secundario {
                background: #6c757d !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .datos-basicos {
                background: #f8f9fa !important;
                border: 1px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .tabla-datos {
                page-break-inside: avoid;
                border: 1px solid #000 !important;
            }

            .tabla-datos td {
                border: 1px solid #000 !important;
            }

            .tabla-datos td:first-child {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .tabla-firmas {
                page-break-inside: avoid;
            }

            .seccion-compromisos {
                page-break-inside: avoid;
                border: 1px solid #000 !important;
            }

            .tabla-encabezado {
                page-break-after: avoid;
            }

            .tabla-firmas {
                page-break-inside: avoid;
            }

            /* Evitar que se corte el contenido */
            h4, .seccion-titulo, .seccion-titulo-secundario {
                page-break-after: avoid;
            }
        }

        /* ============================
           RESPONSIVE
           ============================ */
        @media (max-width: 1024px) {
            .datos-basicos {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container-matricula {
                padding: 20px;
            }

            .datos-basicos {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .dato-item {
                border-right: none;
                border-bottom: 1px solid #dee2e6;
                padding-bottom: 10px;
                padding-right: 0;
            }

            .dato-item:last-child {
                border-bottom: none;
            }

            .tabla-firmas td {
                padding: 10px 15px;
            }

            .firma-linea {
                width: 70%;
                margin-top: 30px;
            }

            .botones-accion {
                bottom: 15px;
                right: 15px;
            }

            .btn-flotante {
                min-width: 120px;
                padding: 8px 16px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Botones de acción -->
    <div class="botones-accion">
        <button class="btn-flotante btn-print" onclick="window.print()">
            <span>■</span>
            <span>Imprimir</span>
        </button>
        <button class="btn-flotante btn-close" onclick="window.close()">
            <span>×</span>
            <span>Cerrar</span>
        </button>
    </div>

    <!-- Contenido principal -->
    <div class="container-matricula">
        <!-- Encabezado -->
        <table class="tabla-encabezado">
            <tr>
                <td style="width: 20%;"></td>
                <td style="width: 20%;">
                    <?php if ($mostrarLogo) { ?>
                        <img class="logo-institucion" src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" alt="Logo">
                    <?php } else { ?>
                        <img class="logo-institucion" src="<?= $logoDefault ?>" alt="Logo por defecto">
                    <?php } ?>
                </td>
                <td style="width: 40%;">
                    <div class="nombre-institucion"><?= strtoupper($informacion_inst["info_nombre"]) ?></div>
                    <div class="titulo-documento">REGISTRO DE MATRÍCULA - AÑO <?= $year ?></div>
                </td>
                <td style="width: 20%;"></td>
            </tr>
        </table>

        <!-- Datos básicos -->
        <div class="datos-basicos">
            <div class="dato-item">
                <div class="dato-label">Matrícula</div>
                <div class="dato-valor"><?= $resultado['mat_numero_matricula'] ?? 'N/A'; ?></div>
            </div>
            <div class="dato-item">
                <div class="dato-label">Grado</div>
                <div class="dato-valor"><?= $resultado['gra_nombre'] ?? 'N/A'; ?></div>
            </div>
            <div class="dato-item">
                <div class="dato-label">Folio</div>
                <div class="dato-valor"><?= $resultado['mat_folio'] ?? 'N/A'; ?></div>
            </div>
            <div class="dato-item">
                <div class="dato-label">Fecha de Matrícula</div>
                <div class="dato-valor"><?= !empty($resultado['mat_fecha']) ? date('d/m/Y', strtotime($resultado['mat_fecha'])) : 'N/A'; ?></div>
            </div>
        </div>

        <!-- DATOS PERSONALES -->
        <div class="seccion-titulo">DATOS PERSONALES</div>
        <table class="tabla-datos">
            <tr>
                <td class="td-label">Nombres</td>
                <td class="td-valor"><?= strtoupper(($resultado['mat_nombres'] ?? '') . " " . ($resultado['mat_nombre2'] ?? '')); ?></td>
                <td class="td-label">Apellidos</td>
                <td class="td-valor"><?= strtoupper(($resultado['mat_primer_apellido'] ?? '') . " " . ($resultado['mat_segundo_apellido'] ?? '')); ?></td>
            </tr>
            <tr>
                <td class="td-label">Sexo</td>
                <td class="td-valor"><?= $tipo['ogen_nombre'] ?? 'No especificado'; ?></td>
                <td class="td-label">Edad</td>
                <td class="td-valor"><?= $edad ?: 'N/A'; ?></td>
            </tr>
            <tr>
                <td class="td-label">Fecha de Nacimiento</td>
                <td class="td-valor"><?= !empty($resultado['mat_fecha_nacimiento']) ? date('d/m/Y', strtotime($resultado['mat_fecha_nacimiento'])) : 'N/A'; ?></td>
                <td class="td-label">Lugar de Nacimiento</td>
                <td class="td-valor"><?= strtoupper($resultado['mat_lugar_nacimiento'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="td-label">NUIP / Documento</td>
                <td class="td-valor"><?= $resultado['mat_documento'] ?? 'N/A'; ?></td>
                <td class="td-label">Lugar de Expedición</td>
                <td class="td-valor"><?= strtoupper($resultado['mat_lugar_expedicion'] ?? 'N/A'); ?></td>
            </tr>
        </table>

        <!-- DATOS FAMILIARES -->
        <div class="seccion-titulo">DATOS FAMILIARES</div>
        <table class="tabla-datos">
            <tr>
                <td class="td-label">Nombre de la Madre</td>
                <td class="td-valor"><?= isset($acudiente2['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($acudiente2)) : 'N/A'; ?></td>
                <td class="td-label">Nombre del Padre</td>
                <td class="td-valor"><?= isset($acudiente1['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($acudiente1)) : 'N/A'; ?></td>
            </tr>
        </table>

        <!-- DATOS DEL ACUDIENTE PRINCIPAL -->
        <div class="seccion-titulo">DATOS DEL ACUDIENTE PRINCIPAL</div>
        <table class="tabla-datos">
            <tr>
                <td class="td-label">Nombres y Apellidos</td>
                <td class="td-valor" colspan="3"><?= isset($acudiente1['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($acudiente1)) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td class="td-label">DNI / Documento</td>
                <td class="td-valor"><?= $acudiente1['uss_usuario'] ?? 'N/A'; ?></td>
                <td class="td-label">Profesión / Ocupación</td>
                <td class="td-valor"><?= strtoupper($acudiente1['uss_ocupacion'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="td-label">Celular</td>
                <td class="td-valor"><?= $acudiente1['uss_celular'] ?? 'N/A'; ?></td>
                <td class="td-label">Teléfono</td>
                <td class="td-valor"><?= $acudiente1['uss_telefono'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td class="td-label">Dirección</td>
                <td class="td-valor" colspan="3"><?= strtoupper($acudiente1['uss_direccion'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="td-label">Correo Electrónico</td>
                <td class="td-valor" colspan="3"><?= $acudiente1['uss_email'] ?? 'N/A'; ?></td>
            </tr>
        </table>

        <!-- DATOS DEL ACUDIENTE SECUNDARIO -->
        <?php if (!empty($acudiente2) && isset($acudiente2['uss_nombre'])) { ?>
        <div class="seccion-titulo-secundario">ACUDIENTE SECUNDARIO</div>
        <table class="tabla-datos">
            <tr>
                <td class="td-label">Nombres y Apellidos</td>
                <td class="td-valor" colspan="3"><?= strtoupper(UsuariosPadre::nombreCompletoDelUsuario($acudiente2)); ?></td>
            </tr>
            <tr>
                <td class="td-label">DNI / Documento</td>
                <td class="td-valor"><?= $acudiente2['uss_usuario'] ?? 'N/A'; ?></td>
                <td class="td-label">Profesión / Ocupación</td>
                <td class="td-valor"><?= strtoupper($acudiente2['uss_ocupacion'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="td-label">Celular</td>
                <td class="td-valor"><?= $acudiente2['uss_celular'] ?? 'N/A'; ?></td>
                <td class="td-label">Teléfono</td>
                <td class="td-valor"><?= $acudiente2['uss_telefono'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td class="td-label">Dirección</td>
                <td class="td-valor" colspan="3"><?= strtoupper($acudiente2['uss_direccion'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="td-label">Correo Electrónico</td>
                <td class="td-valor" colspan="3"><?= $acudiente2['uss_email'] ?? 'N/A'; ?></td>
            </tr>
        </table>
        <?php } ?>

        <!-- DATOS ESCOLARES -->
        <div class="seccion-titulo">DATOS ESCOLARES</div>
        <table class="tabla-datos">
            <tr>
                <td class="td-label">Institución de Procedencia</td>
                <td class="td-valor" colspan="3"><?= strtoupper($resultado['mat_institucion_procedencia'] ?? 'N/A'); ?></td>
            </tr>
        </table>

        <!-- COMPROMISOS -->
        <div class="seccion-compromisos">
            <div class="compromisos-titulo">C O M P R O M I S O S &nbsp;&nbsp; F A M I L I A R E S</div>
            <div class="compromisos-texto">
                Nos comprometemos a cumplir con lo estipulado en el Proyecto Educativo Institucional (PEI) 
                y el Manual de Convivencia de la Institución, respetando las normas, valores y directrices 
                establecidas para el desarrollo integral del estudiante.
            </div>
        </div>

        <!-- FIRMAS -->
        <?php
        $rector = !empty($informacion_inst["info_rector"]) ? UsuariosPadre::sesionUsuario($informacion_inst["info_rector"], "", $config['conf_id_institucion'], $year) : array();
        $secretario = !empty($informacion_inst["info_secretaria_academica"]) ? UsuariosPadre::sesionUsuario($informacion_inst["info_secretaria_academica"], "", $config['conf_id_institucion'], $year) : array();
        ?>
        <table class="tabla-firmas">
            <tr>
                <td style="width: 100%;">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">Firma del Estudiante</div>
                </td>
            </tr>
            <tr>
                <td style="width: 100%;">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">Firma del Acudiente Principal</div>
                </td>
            </tr>
            <tr>
                <td style="width: 100%;">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">Firma del Acudiente Secundario</div>
                </td>
            </tr>
            <tr>
                <td style="width: 100%;">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">
                        <?= isset($rector['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($rector)) : 'RECTOR(A)'; ?>
                        <br>Rector(a)
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 100%;">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">
                        <?= isset($secretario['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($secretario)) : 'SECRETARIO(A)'; ?>
                        <br>Secretario(a) Académico
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <script>
        // Atajo de teclado para imprimir
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
        });
    </script>
</body>
</html>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
