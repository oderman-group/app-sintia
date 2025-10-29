<?php
// Configuraciones para reportes
set_time_limit(180);
ini_set('memory_limit', '128M');

require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/compartido/session-compartida.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

$id="";
if(!empty($_REQUEST["id"])){ $id=base64_decode($_REQUEST["id"]);}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Paz y Salvo - SINTIA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
    <style>
		body {
			font-family: Arial, Helvetica, sans-serif;
			margin: 0;
			padding: 20px;
			background-color: #f5f5f5;
			line-height: 1.6;
		}
		.container-pazysalvo {
			max-width: 21cm;
			margin: 0 auto;
			background-color: #fff;
			padding: 40px 50px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			min-height: 27cm;
		}
		.encabezado-pazysalvo {
			text-align: center;
			margin-bottom: 40px;
			padding-bottom: 20px;
			border-bottom: 2px solid #333;
		}
		.encabezado-pazysalvo img {
			max-height: 130px;
			max-width: 220px;
			margin-bottom: 15px;
		}
		.encabezado-pazysalvo .nombre-institucion {
			font-size: 16px;
			font-weight: bold;
			color: #333;
			margin: 10px 0;
		}
		.encabezado-pazysalvo h4 {
			font-size: 18px;
			font-weight: 700;
			color: #333;
			margin: 30px 0 0 0;
			letter-spacing: 1px;
			text-transform: uppercase;
		}
		.contenido-pazysalvo {
			text-align: justify;
			margin: 30px 0;
			font-size: 13px;
			color: #333;
		}
		.contenido-pazysalvo p {
			margin: 15px 0;
			line-height: 1.8;
		}
		.contenido-pazysalvo .estudiante-info {
			font-weight: 600;
			color: #000;
		}
		.seccion-firma {
			margin-top: 80px;
		}
		.firma-item {
			display: inline-block;
			text-align: center;
			margin-top: 40px;
		}
		.firma-item p {
			margin: 5px 0;
			font-size: 12px;
		}
		.firma-item .firma-imagen {
			max-width: 150px;
			margin-bottom: 10px;
		}
		.firma-linea {
			border-top: 1px solid #000;
			width: 250px;
			margin: 20px auto 10px;
			padding-top: 5px;
		}
		.firma-nombre {
			font-weight: 600;
			font-size: 13px;
			color: #000;
		}
		.firma-cargo {
			font-size: 12px;
			color: #666;
			margin-top: 3px;
		}
		
		/* Botones flotantes */
		.no-print {
			position: fixed;
			top: 20px;
			right: 20px;
			z-index: 1000;
			display: flex;
			gap: 10px;
		}
		.btn-print, .btn-close {
			padding: 12px 24px;
			border: none;
			border-radius: 5px;
			font-size: 14px;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.3s ease;
			box-shadow: 0 2px 5px rgba(0,0,0,0.2);
		}
		.btn-print {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
		}
		.btn-print:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
		}
		.btn-close {
			background: #f44336;
			color: white;
		}
		.btn-close:hover {
			background: #da190b;
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(244, 67, 54, 0.4);
		}
		
		@media print {
			body {
				background-color: white;
				padding: 0;
			}
			.container-pazysalvo {
				max-width: 100%;
				box-shadow: none;
				padding: 30px;
			}
			.no-print {
				display: none !important;
			}
			@page {
				size: letter;
				margin: 2cm;
			}
		}
    </style>
</head>
<body>

<!-- Botones de Acción -->
<div class="no-print">
    <button class="btn-print" onclick="window.print();">
        <i class="fa fa-print"></i> Imprimir
    </button>
    <button class="btn-close" onclick="window.close();">
        <i class="fa fa-times"></i> Cerrar
    </button>
</div>

<div class="container-pazysalvo">
    <!-- Encabezado -->
    <div class="encabezado-pazysalvo">
        <?php if(!empty($informacion_inst["info_logo"]) && file_exists("../../files/images/logo/".$informacion_inst["info_logo"])){ ?>
            <img src="../../files/images/logo/<?=htmlspecialchars($informacion_inst["info_logo"])?>" alt="Logo Institución">
        <?php } ?>
        <div class="nombre-institucion">
            <?=htmlspecialchars(!empty($informacion_inst["info_nombre"]) ? $informacion_inst["info_nombre"] : 'Institución Educativa')?>
        </div>
        <h4>A QUIEN PUEDA INTERESAR</h4>
    </div>
    
    <?php
    // Obtener datos del estudiante
    $usuario = Estudiantes::obtenerDatosEstudiantePorIdUsuario($id);
    
    if(empty($usuario)){
        echo '<div class="contenido-pazysalvo">
            <p style="text-align: center; color: #dc3545; font-weight: bold;">
                No se encontraron datos del estudiante.
            </p>
        </div>
        </div>
        </body>
        </html>';
        exit();
    }
    
    $nombre = Estudiantes::NombreCompletoDelEstudiante($usuario);
    
    // Formatear documento
    $documento = !empty($usuario["mat_documento"]) ? $usuario["mat_documento"] : 'N/A';
    if(is_numeric($documento) && strpos($documento, '.') === false){
        $documento = number_format($documento, 0, ",", ".");
    }
    
    // Determinar tipo de documento
    $tipoD = 'DOC.';
    if(!empty($usuario['mat_tipo_documento'])){
        switch($usuario['mat_tipo_documento']){
            case 105: $tipoD='CC.'; break;
            case 106: $tipoD='NUIP.'; break;
            case 107: $tipoD='TI.'; break;
            case 108: $tipoD='RC.'; break;
            case 109: $tipoD='CE.'; break;
            case 110: $tipoD='PP.'; break;
            case 139: $tipoD='PEP.'; break;
            default: $tipoD='DOC.'; break;
        }
    }
    
    $institucionNombre = !empty($informacion_inst["info_nombre"]) ? htmlspecialchars($informacion_inst["info_nombre"]) : 'La institución educativa';
    ?>
    
    <!-- Contenido -->
    <div class="contenido-pazysalvo">
        <p>
            El <span class="estudiante-info"><?=$institucionNombre?></span> hace constar que el estudiante 
            <span class="estudiante-info"><?=htmlspecialchars($nombre)?></span> identificado con 
            <span class="estudiante-info"><?=$tipoD?> <?=htmlspecialchars($documento)?></span> 
            se encuentra a <b>PAZ Y SALVO</b> por todo concepto.
        </p>
        
        <p>
            Esta constancia certifica que ha cumplido satisfactoriamente con todos los compromisos 
            y obligaciones financieras con nuestra institución.
        </p>
        
        <p>
            Se expide esta constancia a los <b><?=date("d")?></b> días del mes de 
            <b><?=!empty($mesesAgno[date("m")]) ? $mesesAgno[date("m")] : date("F")?></b> del año <b><?=date("Y")?></b>.
        </p>
        
        <p style="margin-top: 40px;">
            Agradecemos su colaboración y compromiso con nuestra institución.
        </p>
    </div>
    
    <!-- Firma -->
    <div class="seccion-firma">
        <p style="margin-bottom: 30px;"><b>Atentamente,</b></p>
        
        <div class="firma-item">
            <?php
                $tesorero = [];
                $nombreTesorero = 'N/A';
                if(!empty($informacion_inst["info_tesorero"])){
                    $tesorero = UsuariosPadre::sesionUsuario($informacion_inst["info_tesorero"]);
                    $nombreTesorero = !empty($tesorero) ? UsuariosPadre::nombreCompletoDelUsuario($tesorero) : 'N/A';
                }
                
                if(!empty($tesorero["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/'.$tesorero["uss_firma"])){
                    echo '<img class="firma-imagen" src="'.REDIRECT_ROUTE.'/files/fotos/'.htmlspecialchars($tesorero["uss_firma"]).'" alt="Firma">';
                }
            ?>
            <div class="firma-linea"></div>
            <div class="firma-nombre"><?=htmlspecialchars($nombreTesorero)?></div>
            <div class="firma-cargo">Contador(a) / Tesorero(a)</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Atajo de teclado para imprimir
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    });
</script>

</body>
</html>


