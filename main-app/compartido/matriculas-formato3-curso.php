<?php
// Configuraciones para reportes grandes
set_time_limit(300);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

if(empty($_SERVER['HTTP_REFERER'])){
	echo '<script type="text/javascript">window.close()</script>';
	exit();
}

include("session-compartida.php");
$idPaginaInterna = 'DT0251';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;
include("head.php");
?>
<style>
	body {
		font-family: Arial, Helvetica, sans-serif;
		margin: 0;
		padding: 20px;
		background-color: #f5f5f5;
	}
	.container-matricula {
		max-width: 21cm;
		margin: 0 auto;
		background-color: #fff;
		padding: 30px;
		box-shadow: 0 2px 10px rgba(0,0,0,0.1);
	}
	.encabezado-matricula {
		text-align: center;
		margin-bottom: 25px;
		padding-bottom: 15px;
		border-bottom: 2px solid #333;
	}
	.encabezado-matricula img {
		max-height: 120px;
		max-width: 200px;
		margin-bottom: 10px;
	}
	.encabezado-matricula .nombre-institucion {
		font-size: 16px;
		font-weight: bold;
		color: #333;
		margin: 10px 0 5px;
	}
	.encabezado-matricula .titulo-formato {
		font-size: 14px;
		font-weight: 600;
		color: #666;
		text-transform: uppercase;
	}
	.tabla-datos {
		width: 100%;
		border-collapse: collapse;
		margin: 20px 0;
		font-size: 12px;
	}
	.tabla-datos td {
		border: 1px solid #333;
		padding: 10px;
		vertical-align: top;
	}
	.tabla-datos td label {
		font-weight: normal;
		color: #555;
		display: inline;
	}
	.tabla-datos td b {
		color: #000;
		font-weight: 600;
	}
	.seccion-firmas {
		margin-top: 60px;
		width: 100%;
		border-collapse: collapse;
	}
	.seccion-firmas td {
		text-align: center;
		padding: 20px 10px;
		border: none;
		width: 25%;
	}
	.firma-linea {
		border-top: 1px solid #000;
		width: 80%;
		margin: 0 auto 5px;
		padding-top: 5px;
	}
	.firma-cargo {
		font-size: 11px;
		color: #666;
	}
	#saltoPagina {
		PAGE-BREAK-AFTER: always;
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
		.container-matricula {
			max-width: 100%;
			box-shadow: none;
			padding: 20px;
		}
		.no-print {
			display: none !important;
		}
		@page {
			size: letter;
			margin: 1.5cm;
		}
	}
</style>
</head>
<body>
<?php
    $year=$_SESSION["bd"];
    if(!empty($_POST["year"])){
        $year=$_POST["year"];
    }

    $curso="";
    if(!empty($_GET["curso"])){
        $curso=base64_decode($_GET["curso"]);
    }
    if(!empty($_POST["curso"])){
        $curso=$_POST["curso"];
    }
    
    $filtroAdicional= "AND mat_grado='".$curso."'";
    if(!empty($_REQUEST["grupo"])){
        $filtroAdicional .= " AND mat_grupo='".$_REQUEST["grupo"]."'";
    }

    // Pre-cargar tipos de estudiante para evitar consultas repetidas
    $tiposEstudiante = [];
    $consultaTipos = mysqli_query($conexion, "SELECT * FROM $baseDatosServicios.opciones_generales WHERE ogen_grupo='Tipo'");
    while($tipo = mysqli_fetch_array($consultaTipos, MYSQLI_BOTH)){
        $tiposEstudiante[$tipo['ogen_id']] = $tipo;
    }

    // Obtener estudiantes (ya optimizado con joins)
    $consultaEstudiantes = Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"", NULL, NULL, $year);
    $estudiantes = [];
    while($est = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH)){
        $estudiantes[] = $est;
    }
?>

<!-- Botones de Acción -->
<div class="no-print">
    <button class="btn-print" onclick="window.print();">
        <i class="fa fa-print"></i> Imprimir
    </button>
    <button class="btn-close" onclick="window.close();">
        <i class="fa fa-times"></i> Cerrar
    </button>
</div>

<?php
    foreach($estudiantes as $resultado){
        // Obtener tipo de estudiante del cache
        $tipoEstudiante = !empty($tiposEstudiante[$resultado['mat_tipo']]) ? $tiposEstudiante[$resultado['mat_tipo']] : ['ogen_nombre' => 'N/A'];
?>
<div class="container-matricula">
    <!-- Encabezado -->
    <div class="encabezado-matricula">
        <?php if(!empty($informacion_inst["info_logo"]) && file_exists("../files/images/logo/".$informacion_inst["info_logo"])){ ?>
            <img src="../files/images/logo/<?=htmlspecialchars($informacion_inst["info_logo"])?>" alt="Logo Institución">
        <?php } ?>
        <div class="nombre-institucion"><?=htmlspecialchars(!empty($informacion_inst["info_nombre"]) ? $informacion_inst["info_nombre"] : 'Institución Educativa')?></div>
        <div class="titulo-formato">Formato de Matrículas</div>
    </div>

    <!-- Tabla de Datos -->
    <table class="tabla-datos">
        <tr>
            <td><label>Fecha Matrícula:</label> <b><?=htmlspecialchars(!empty($resultado['mat_fecha']) ? $resultado['mat_fecha'] : 'N/A')?></b></td>
            <td><label>Código Estudiante:</label> <b><?=htmlspecialchars(!empty($resultado['mat_matricula']) ? $resultado['mat_matricula'] : 'N/A')?></b></td>
            <td><label>No. Documento:</label> <b><?=htmlspecialchars(!empty($resultado['mat_documento']) ? $resultado['mat_documento'] : 'N/A')?></b></td>
            <td><label>No. Matrícula:</label> <b><?=htmlspecialchars(!empty($resultado['mat_numero_matricula']) ? $resultado['mat_numero_matricula'] : 'N/A')?></b></td>
            <td><label>Folio:</label> <b><?=htmlspecialchars(!empty($resultado['mat_folio']) ? $resultado['mat_folio'] : 'N/A')?></b></td>
        </tr>
        
        <tr>
            <td><label>Apellidos:</label> <b><?=strtoupper(trim((!empty($resultado['mat_primer_apellido']) ? $resultado['mat_primer_apellido'] : '')." ".(!empty($resultado['mat_segundo_apellido']) ? $resultado['mat_segundo_apellido'] : '')))?></b></td>
            <td><label>Nombres:</label> <b><?=strtoupper(trim((!empty($resultado['mat_nombres']) ? $resultado['mat_nombres'] : '')." ".(!empty($resultado['mat_nombre2']) ? $resultado['mat_nombre2'] : '')))?></b></td>
            <td><label>Curso:</label> <b><?=htmlspecialchars(!empty($resultado['gra_nombre']) ? $resultado['gra_nombre'] : 'N/A')?></b></td>
            <td colspan="2"><label>Grupo:</label> <b><?=htmlspecialchars(!empty($resultado['gru_nombre']) ? $resultado['gru_nombre'] : 'N/A')?></b></td>
        </tr>
        
        <tr>
            <td colspan="3"><label>Tipo Estudiante:</label> <b><?=htmlspecialchars($tipoEstudiante['ogen_nombre'])?></b></td>
            <td><label>Fecha Nacimiento:</label> <b><?=htmlspecialchars(!empty($resultado['mat_fecha_nacimiento']) ? $resultado['mat_fecha_nacimiento'] : 'N/A')?></b></td>
            <td><label>Género:</label> <b><?=htmlspecialchars(!empty($resultado['ogen_nombre']) ? $resultado['ogen_nombre'] : 'N/A')?></b></td>
        </tr>
        
        <tr>
            <td><label>Dirección:</label> <b><?=htmlspecialchars(!empty($resultado['mat_direccion']) ? $resultado['mat_direccion'] : 'N/A')?></b></td>
            <td><label>Barrio:</label> <b><?=htmlspecialchars(!empty($resultado['mat_barrio']) ? $resultado['mat_barrio'] : 'N/A')?></b></td>
            <td><label>Teléfono:</label> <b><?=htmlspecialchars(!empty($resultado['mat_telefono']) ? $resultado['mat_telefono'] : 'N/A')?></b></td>
            <td colspan="2"><label>Celular:</label> <b><?=htmlspecialchars(!empty($resultado['mat_celular']) ? $resultado['mat_celular'] : 'N/A')?></b></td>
        </tr>
    </table>

    <!-- Firmas -->
    <table class="seccion-firmas">
        <tr>
            <td>
                <div class="firma-linea"></div>
                <div class="firma-cargo">Estudiante</div>
            </td>
            <td>
                <div class="firma-linea"></div>
                <div class="firma-cargo">Acudiente</div>
            </td>
            <td>
                <div class="firma-linea"></div>
                <div class="firma-cargo">Rectoría</div>
            </td>
            <td>
                <div class="firma-linea"></div>
                <div class="firma-cargo">Secretaría Académica</div>
            </td>
        </tr>
    </table>
</div>

<div id="saltoPagina"></div> 
<?php }?>

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
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
</html>
