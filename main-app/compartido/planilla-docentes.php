<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0239';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once("../class/CargaAcademica.php");

if(!empty($_GET["carga"])) {
  $carga = base64_decode($_GET["carga"]);
}

if(!empty($_GET["docente"])) {
  $docente = base64_decode($_GET["docente"]);
}
?>

<head>
	<meta charset="utf-8">
	<title>Planilla de Evaluación - Docentes</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
	<style>
		#saltoPagina {
			PAGE-BREAK-AFTER: always;
		}
		
		body {
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			color: #2c3e50;
		}
		
		.info-header {
			margin-bottom: 20px;
			padding: 15px;
			background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
			border-left: 4px solid <?=$Plataforma->colorUno;?>;
			border-radius: 4px;
		}
		
		.info-row {
			display: flex;
			margin-bottom: 10px;
		}
		
		.info-label {
			font-weight: 700;
			color: #495057;
			min-width: 150px;
			font-size: 11px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		
		.info-value {
			color: #212529;
			font-size: 13px;
			font-weight: 500;
		}
		
		.planilla-title {
			text-align: center;
			margin-bottom: 10px;
			padding: 15px;
			background: <?=$Plataforma->colorUno;?>;
			color: white;
			border-radius: 4px;
		}
		
		.planilla-title h2 {
			margin: 0;
			font-size: 18px;
			font-weight: 600;
			letter-spacing: 1px;
			text-transform: uppercase;
		}
		
		.planilla-title p {
			margin: 5px 0 0 0;
			font-size: 12px;
			opacity: 0.9;
		}
		
		.main-table {
			width: 100%;
			border-collapse: collapse;
			background: white;
			box-shadow: 0 2px 4px rgba(0,0,0,0.08);
		}
		
		.main-table thead tr.header-row-1 {
			background: <?=$Plataforma->colorUno;?>;
			color: white;
			font-weight: 600;
			font-size: 11px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		
		.main-table thead tr.header-row-2 {
			background: <?=$Plataforma->colorDos;?>;
			color: #2c3e50;
			font-weight: 600;
			font-size: 10px;
			height: 150px;
		}
		
		.main-table thead tr.header-row-3 {
			background: <?=$Plataforma->colorUno;?>;
			color: white;
			font-weight: 600;
			font-size: 11px;
			height: 35px;
		}
		
		.main-table thead td {
			padding: 8px 4px;
			border: 1px solid #dee2e6;
			text-align: center;
		}
		
		.main-table tbody tr {
			border-bottom: 1px solid #e9ecef;
			transition: background-color 0.2s ease;
		}
		
		.main-table tbody tr:nth-child(even) {
			background-color: #f8f9fa;
		}
		
		.main-table tbody tr:hover {
			background-color: #e7f1ff;
		}
		
		.main-table tbody td {
			padding: 8px 4px;
			border: 1px solid #dee2e6;
			font-size: 10px;
			text-align: center;
		}
		
		.student-name {
			text-align: left;
			padding-left: 8px;
			font-weight: 500;
			color: #2c3e50;
		}
		
		.signature-area {
			text-align: center;
			padding: 20px 10px;
			border-top: 2px solid #333;
			margin-top: 10px;
			font-size: 11px;
			font-weight: 500;
			color: #495057;
		}
		
		@media print {
			.info-header {
				break-inside: avoid;
			}
			
			.main-table {
				page-break-inside: auto;
			}
			
			.main-table tr {
				page-break-inside: avoid;
				page-break-after: auto;
			}
			
			.main-table thead {
				display: table-header-group;
			}
		}
	</style>
</head>

<body>

<?php
$nombreInforme = "PLANILLA DE EVALUACIÓN E INASISTENCIA";
include("../compartido/head-informes.php");

$filtro = '';

if(!empty($carga)) {$filtro .= " AND car_id='".$carga."'";}	

if(!empty($docente)) {$filtro .= " AND car_docente='".$docente."'";}

if(!empty($_GET["grado"])) {$filtro .= " AND car_curso='".$_GET["grado"]."'";}

if(!empty($_GET["grupo"])) {$filtro .= " AND car_grupo='".$_GET["grupo"]."'";}

if(!empty($_GET["periodo"])) {$filtro .= " AND car_periodo='".$_GET["periodo"]."'";}	

$con = CargaAcademica::listarCargas($conexion, $config, "", $filtro);
while ( $rCargas = mysqli_fetch_array($con, MYSQLI_BOTH) ) {
?>

<!-- Información de la Carga -->
<div class="info-header">
	<div style="display: flex; flex-wrap: wrap; gap: 20px;">
		<div style="flex: 1; min-width: 250px;">
			<div class="info-row">
				<span class="info-label">Docente:</span>
				<span class="info-value"><?=strtoupper($rCargas['uss_nombre']);?></span>
			</div>
			<div class="info-row">
				<span class="info-label">Asignatura:</span>
				<span class="info-value"><?=strtoupper($rCargas['mat_nombre']);?></span>
			</div>
		</div>
		<div style="flex: 1; min-width: 250px;">
			<div class="info-row">
				<span class="info-label">Grado:</span>
				<span class="info-value"><?=$rCargas["gra_nombre"];?> <?=$rCargas["gru_nombre"];?></span>
			</div>
			<div class="info-row">
				<span class="info-label">Periodo:</span>
				<span class="info-value"><?php echo $rCargas['car_periodo']." (".date("Y").")";?></span>
			</div>
		</div>
		<div style="flex: 1; min-width: 250px;">
			<div class="info-row">
				<span class="info-label">Fecha de Impresión:</span>
				<span class="info-value"><?=date("d/m/Y H:i:s");?></span>
			</div>
		</div>
	</div>
</div>

<p>&nbsp;</p>	

<!-- Tabla Principal de Evaluación -->
<table class="main-table">
	<thead>
		<!-- Fila 1: Tipo de Evaluación -->
		<tr class="header-row-1">
			<td colspan="3">Tipo de Evaluación y Porcentaje</td>
			<td colspan="5">&nbsp;</td>
			<td colspan="5">&nbsp;</td>
			<td colspan="5">&nbsp;</td>
			<td>Auto</td>
			<td>Coo</td>
			<td colspan="7"></td>
		</tr>

		<!-- Fila 2: Temas -->
		<tr class="header-row-2">
			<td colspan="3" style="vertical-align: middle;">TEMAS</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="7">
				<div class="signature-area">
					Firma Docente
				</div>
			</td>
		</tr>

		<!-- Fila 3: Encabezados de Columnas -->
		<tr class="header-row-3">
			<td>No</td>
			<td>Código</td>
			<td>Estudiante</td>
			<td colspan="17">&nbsp;</td>
			<td colspan="7">Inasistencia</td>
		</tr>
	</thead>

	<tbody>
		<?php
		$estudiantes = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($rCargas);

		$n = 1;

		while ( $e = mysqli_fetch_array($estudiantes, MYSQLI_BOTH) ) {
		?>
		<tr>
			<td><?=$n;?></td>
			<td><?=$e['mat_id'];?></td>
			<td class="student-name"><?=Estudiantes::NombreCompletoDelEstudiante($e);?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php
		$n++;
		} //fin estudiantes
		?>
	</tbody>
</table>

<?php include("../compartido/footer-informes.php"); ?>

<div id="saltoPagina"></div>

<?php
} //Fin de las cargas
?>

<script type="application/javascript">
print();
</script>

</body>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
</html>
