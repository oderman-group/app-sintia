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
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container-matricula {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            gap: 10px;
            padding: 12px 24px;
            border: none;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            min-width: 160px;
            text-decoration: none;
            color: white;
        }

        .btn-print {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }

        .btn-close {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
        }

        .btn-close:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(149, 165, 166, 0.4);
        }

        /* ============================
           ENCABEZADO
           ============================ */
        .header-matricula {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3498db;
        }

        .logo-institucion {
            max-width: 120px;
            height: auto;
            margin-bottom: 15px;
        }

        .nombre-institucion {
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .titulo-documento {
            font-size: 14pt;
            font-weight: 600;
            color: #7f8c8d;
            margin-top: 10px;
        }

        /* ============================
           DATOS BÁSICOS
           ============================ */
        .datos-basicos {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .dato-item {
            display: flex;
            flex-direction: column;
        }

        .dato-label {
            font-size: 9pt;
            opacity: 0.9;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .dato-valor {
            font-size: 11pt;
            font-weight: bold;
        }

        /* ============================
           SECCIONES
           ============================ */
        .seccion-titulo {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 12px 20px;
            margin: 25px 0 15px 0;
            border-radius: 6px;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .seccion-titulo-secundario {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            color: white;
            padding: 8px 15px;
            margin: 20px 0 10px 0;
            border-radius: 4px;
            font-size: 11pt;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* ============================
           TABLAS
           ============================ */
        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .tabla-datos td {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            font-size: 10pt;
        }

        .tabla-datos td:first-child {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            width: 35%;
        }

        .tabla-datos td b,
        .tabla-datos .valor-dato {
            color: #2c3e50;
            font-weight: bold;
        }

        .tabla-datos tr:hover {
            background-color: #f1f3f5;
        }

        /* ============================
           COMPROMISOS Y FIRMAS
           ============================ */
        .seccion-compromisos {
            margin-top: 30px;
            padding: 20px;
            background: #e8f4f8;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .compromisos-titulo {
            font-size: 12pt;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .compromisos-texto {
            font-size: 10pt;
            text-align: center;
            color: #555;
            line-height: 1.6;
        }

        .seccion-firmas {
            margin-top: 40px;
            padding-top: 30px;
        }

        .contenedor-firmas {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 30px;
        }

        .contenedor-firmas-oficiales {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 50px;
            margin-top: 30px;
        }

        .firma-item {
            text-align: center;
        }

        .firma-linea {
            border-top: 2px solid #2c3e50;
            margin-bottom: 8px;
        }

        .firma-cargo {
            font-size: 9pt;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
        }

        /* ============================
           ESTILOS DE IMPRESIÓN
           ============================ */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .container-matricula {
                max-width: 100%;
                padding: 20px;
                box-shadow: none;
                border-radius: 0;
            }

            .botones-accion {
                display: none !important;
            }

            .seccion-titulo {
                background: #3498db !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .datos-basicos {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .tabla-datos {
                page-break-inside: avoid;
            }

            .seccion-firmas {
                page-break-inside: avoid;
            }

            /* Evitar que se corte el contenido */
            .container-matricula {
                page-break-after: auto;
            }

            .seccion-compromisos {
                page-break-inside: avoid;
            }
        }

        /* ============================
           RESPONSIVE
           ============================ */
        @media (max-width: 768px) {
            .container-matricula {
                padding: 20px;
            }

            .datos-basicos {
                grid-template-columns: 1fr;
            }

            .contenedor-firmas {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .contenedor-firmas-oficiales {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .botones-accion {
                bottom: 20px;
                right: 20px;
            }

            .btn-flotante {
                min-width: 140px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Botones de acción -->
    <div class="botones-accion">
        <button class="btn-flotante btn-print" onclick="window.print()">
            <span>🖨️</span>
            <span>Imprimir</span>
        </button>
        <button class="btn-flotante btn-close" onclick="window.close()">
            <span>✖️</span>
            <span>Cerrar</span>
        </button>
    </div>

    <!-- Contenido principal -->
    <div class="container-matricula">
        <!-- Encabezado -->
        <div class="header-matricula">
            <?php if ($mostrarLogo) { ?>
                <img class="logo-institucion" src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" alt="Logo">
            <?php } else { ?>
                <img class="logo-institucion" src="<?= $logoDefault ?>" alt="Logo por defecto">
            <?php } ?>
            <div class="nombre-institucion"><?= strtoupper($informacion_inst["info_nombre"]) ?></div>
            <div class="titulo-documento">REGISTRO DE MATRÍCULA - AÑO <?= $year ?></div>
        </div>

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
        <div class="seccion-titulo">📋 Datos Personales</div>
        <table class="tabla-datos">
            <tr>
                <td>Nombres</td>
                <td class="valor-dato"><?= strtoupper(($resultado['mat_nombres'] ?? '') . " " . ($resultado['mat_nombre2'] ?? '')); ?></td>
            </tr>
            <tr>
                <td>Apellidos</td>
                <td class="valor-dato"><?= strtoupper(($resultado['mat_primer_apellido'] ?? '') . " " . ($resultado['mat_segundo_apellido'] ?? '')); ?></td>
            </tr>
            <tr>
                <td>Sexo</td>
                <td class="valor-dato"><?= $tipo['ogen_nombre'] ?? 'No especificado'; ?></td>
            </tr>
            <tr>
                <td>Fecha de Nacimiento</td>
                <td class="valor-dato"><?= !empty($resultado['mat_fecha_nacimiento']) ? date('d/m/Y', strtotime($resultado['mat_fecha_nacimiento'])) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Lugar de Nacimiento</td>
                <td class="valor-dato"><?= strtoupper($resultado['mat_lugar_nacimiento'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td>Edad</td>
                <td class="valor-dato"><?= $edad ?: 'N/A'; ?></td>
            </tr>
            <tr>
                <td>NUIP / Documento</td>
                <td class="valor-dato"><?= $resultado['mat_documento'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Lugar de Expedición</td>
                <td class="valor-dato"><?= strtoupper($resultado['mat_lugar_expedicion'] ?? 'N/A'); ?></td>
            </tr>
        </table>

        <!-- DATOS FAMILIARES -->
        <div class="seccion-titulo">👨‍👩‍👧‍👦 Datos Familiares</div>
        <table class="tabla-datos">
            <tr>
                <td>Nombre de la Madre</td>
                <td class="valor-dato"><?= isset($acudiente2['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($acudiente2)) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Nombre del Padre</td>
                <td class="valor-dato"><?= isset($acudiente1['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($acudiente1)) : 'N/A'; ?></td>
            </tr>
        </table>

        <!-- DATOS DEL ACUDIENTE PRINCIPAL -->
        <div class="seccion-titulo">👤 Datos del Acudiente Principal</div>
        <table class="tabla-datos">
            <tr>
                <td>Nombres y Apellidos</td>
                <td class="valor-dato"><?= isset($acudiente1['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($acudiente1)) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td>DNI / Documento</td>
                <td class="valor-dato"><?= $acudiente1['uss_usuario'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Profesión / Ocupación</td>
                <td class="valor-dato"><?= strtoupper($acudiente1['uss_ocupacion'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td>Celular</td>
                <td class="valor-dato"><?= $acudiente1['uss_celular'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Teléfono</td>
                <td class="valor-dato"><?= $acudiente1['uss_telefono'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Dirección</td>
                <td class="valor-dato"><?= strtoupper($acudiente1['uss_direccion'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td>Correo Electrónico</td>
                <td class="valor-dato"><?= $acudiente1['uss_email'] ?? 'N/A'; ?></td>
            </tr>
        </table>

        <!-- DATOS DEL ACUDIENTE SECUNDARIO -->
        <?php if (!empty($acudiente2) && isset($acudiente2['uss_nombre'])) { ?>
        <div class="seccion-titulo-secundario">👤 Acudiente Secundario</div>
        <table class="tabla-datos">
            <tr>
                <td>Nombres y Apellidos</td>
                <td class="valor-dato"><?= strtoupper(UsuariosPadre::nombreCompletoDelUsuario($acudiente2)); ?></td>
            </tr>
            <tr>
                <td>DNI / Documento</td>
                <td class="valor-dato"><?= $acudiente2['uss_usuario'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Profesión / Ocupación</td>
                <td class="valor-dato"><?= strtoupper($acudiente2['uss_ocupacion'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td>Celular</td>
                <td class="valor-dato"><?= $acudiente2['uss_celular'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Teléfono</td>
                <td class="valor-dato"><?= $acudiente2['uss_telefono'] ?? 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Dirección</td>
                <td class="valor-dato"><?= strtoupper($acudiente2['uss_direccion'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td>Correo Electrónico</td>
                <td class="valor-dato"><?= $acudiente2['uss_email'] ?? 'N/A'; ?></td>
            </tr>
        </table>
        <?php } ?>

        <!-- DATOS ESCOLARES -->
        <div class="seccion-titulo">🎓 Datos Escolares</div>
        <table class="tabla-datos">
            <tr>
                <td>Institución de Procedencia</td>
                <td class="valor-dato"><?= strtoupper($resultado['mat_institucion_procedencia'] ?? 'N/A'); ?></td>
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
        <div class="seccion-firmas">
            <div class="contenedor-firmas">
                <div class="firma-item">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">Firma del Estudiante</div>
                </div>
                <div class="firma-item">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">Firma del Acudiente Principal</div>
                </div>
                <div class="firma-item">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">Firma del Acudiente Secundario</div>
                </div>
            </div>

            <div class="contenedor-firmas-oficiales">
                <?php
                $rector = !empty($informacion_inst["info_rector"]) ? UsuariosPadre::sesionUsuario($informacion_inst["info_rector"], "", $config['conf_id_institucion'], $year) : array();
                $secretario = !empty($informacion_inst["info_secretaria_academica"]) ? UsuariosPadre::sesionUsuario($informacion_inst["info_secretaria_academica"], "", $config['conf_id_institucion'], $year) : array();
                ?>
                <div class="firma-item">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">
                        <?= isset($rector['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($rector)) : 'RECTOR(A)'; ?>
                        <br>Rector(a)
                    </div>
                </div>
                <div class="firma-item">
                    <div class="firma-linea"></div>
                    <div class="firma-cargo">
                        <?= isset($secretario['uss_nombre']) ? strtoupper(UsuariosPadre::nombreCompletoDelUsuario($secretario)) : 'SECRETARIO(A)'; ?>
                        <br>Secretario(a) Académico
                    </div>
                </div>
            </div>
        </div>
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
