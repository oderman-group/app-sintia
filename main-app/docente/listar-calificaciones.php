<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0035';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

$valores = Actividades::consultarValores($config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajeRestante = 100 - $valores[0];
?>
</head>

<div class="card card-topline-purple">
	<div class="card-head">
		<header><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></header>
		<div class="tools">
			<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
		</div>
	</div>
	
	

	
	<div class="card-body">
		<div class="row" style="margin-bottom: 10px;">
			<div class="col-sm-12">
				
		<?php
		if( CargaAcademica::validarAccionAgregarCalificaciones($datosCargaActual, $valores, $periodoConsultaActual, $porcentajeRestante) ) {
		?>
		
				<div class="btn-group">
					<a href="calificaciones-agregar.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" id="addRow" class="btn deepPink-bgcolor">
						Agregar nuevo <i class="fa fa-plus"></i>
					</a>
				</div>
				
				
		<?php
		}
		?>
				
		<?php if($datosCargaActual['car_configuracion']==1 and $porcentajeRestante<=0){?>
			<p style="color: tomato;"> Has alcanzado el 100% de valor para las calificaciones. </p>
		<?php }?>
					
		<?php if($datosCargaActual['car_maximas_calificaciones']<=$valores[1]){?>
			<p style="color: tomato;"> Has alcanzado el número máximo de calificaciones permitidas. </p>
		<?php }?>
		
		<?php if( CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) {?>
				<div class="btn-group">
					<a href="calificaciones-todas-rapido.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" class="btn bg-purple">
						LLenar más rápido las calificaciones
					</a>
				</div>
		<?php }?>
		
			</div>
		</div>
		
		
		
	<div class="table-responsive">
		<table class="table table-striped custom-table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
					<th><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></th>
					<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
					<th><?=$frases[52][$datosUsuarioActual['uss_idioma']];?></th>
					
					<?php if($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null){?>
						<th><?=$frases[68][$datosUsuarioActual['uss_idioma']];?></th>
					<?php }?>
					
					<?php if($datosCargaActual['car_evidencia']==1){?>
						<th>Evidencia</th>
					<?php }?>
					
					<th>#EC/#ET</th>
					<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$consulta = mysqli_query($conexion, "SELECT aa.id_nuevo AS id_nuevo_act, aa.*, ai.* FROM ".BD_ACADEMICA.".academico_actividades aa
					INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=aa.act_id_tipo AND ai.institucion={$config['conf_id_institucion']} AND ai.year={$_SESSION["bd"]}
					WHERE aa.act_id_carga='".$cargaConsultaActual."' AND aa.act_periodo='".$periodoConsultaActual."' AND aa.act_estado=1 AND aa.institucion={$config['conf_id_institucion']} AND aa.year={$_SESSION["bd"]}
					");
					$contReg = 1;
					$porcentajeActual = 0;
					$cantidadEstudiantes = Estudiantes::contarEstudiantesParaDocentes($filtroDocentesParaListarEstudiantes);
					while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
					$bg = '';
					if($datosCargaActual['gra_tipo'] == GRADO_INDIVIDUAL) {
						$consultaNumEstudiante=mysqli_query($conexion, "SELECT count(*) FROM ".BD_ACADEMICA.".academico_calificaciones aac
						INNER JOIN ".$baseDatosServicios.".mediatecnica_matriculas_cursos ON matcur_id_curso='".$datosCargaActual['car_curso']."' AND matcur_id_grupo='".$datosCargaActual['car_grupo']."' AND matcur_estado='".ACTIVO."' AND matcur_id_institucion='".$config['conf_id_institucion']."'
						INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat_eliminado=0 AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_id=aac.cal_id_estudiante AND mat_id=matcur_id_matricula AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
						WHERE aac.cal_id_actividad='".$resultado['act_id']."' AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$_SESSION["bd"]}
						");
					} else {
						$consultaNumEstudiante=mysqli_query($conexion, "SELECT count(*) FROM ".BD_ACADEMICA.".academico_calificaciones aac
						INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 AND mat_id=aac.cal_id_estudiante AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
						WHERE aac.cal_id_actividad='".$resultado['act_id']."' AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$_SESSION["bd"]}
						");
					}
					$numerosEstudiantes = mysqli_fetch_array($consultaNumEstudiante, MYSQLI_BOTH);
					if($numerosEstudiantes[0]<$cantidadEstudiantesParaDocentes) $bg = '#FCC';
						
						$porcentajeActual +=$resultado['act_valor'];
						
						if($datosCargaActual['car_evidencia']==1){
						$consultaEvidencia=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_evidencias WHERE evid_id='".$resultado['act_id_evidencia']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
						$evidencia = mysqli_fetch_array($consultaEvidencia, MYSQLI_BOTH);
						}
					?>
				
				<tr id="reg<?=$resultado['act_id'];?>">
					<td><?=$contReg;?></td>
					<td><?=$resultado['id_nuevo_act'];?></td>
					<td><a href="calificaciones-registrar.php?idR=<?=base64_encode($resultado['act_id']);?>" style="text-decoration: underline;" title="Calificar"><?=$resultado['act_descripcion'];?></a></td>
					<td><?=$resultado['act_fecha'];?></td>
					<td><?=$resultado['act_valor'];?></td>
					
					<?php if($datosCargaActual['car_indicador_automatico']==0 or $datosCargaActual['car_indicador_automatico']==null){?>
						<td style="font-size: 10px;"><?=$resultado['ind_nombre'];?></td>
					<?php }?>
					
					<?php if($datosCargaActual['car_evidencia']==1){?>
						<td><?=$evidencia['evid_nombre']." (".$evidencia['evid_valor']."%)";?></td>
					<?php }?>
					
					<td style="background-color:<?=$bg;?>"><a href="../compartido/reporte-calificaciones.php?idActividad=<?=base64_encode($resultado['act_id']);?>&grado=<?=base64_encode($datosCargaActual['car_curso']);?>&grupo=<?=base64_encode($datosCargaActual['car_grupo']);?>" target="_blank" style="text-decoration: underline;"><?=$numerosEstudiantes[0];?>/<?=$cantidadEstudiantesParaDocentes;?></a></td>
					<td>
						
						<?php
							$arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
							$arrayDatos = json_encode($arrayEnviar);
							$objetoEnviar = htmlentities($arrayDatos);
							?>
						
						<?php if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){?>
						
						<div class="btn-group">
							<button class="btn btn-xs btn-info dropdown-toggle center no-margin" type="button" data-toggle="dropdown" aria-expanded="false"> Acciones
								<i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu pull-left" role="menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 23px, 0px); top: 0px; left: 0px; will-change: transform;">
								<li><a href="calificaciones-registrar.php?idR=<?=base64_encode($resultado['act_id']);?>">Calificar</a></li>
								<li><a href="calificaciones-editar.php?idR=<?=base64_encode($resultado['act_id']);?>">Editar</a></li>
								<li><a href="#" title="<?=$objetoEnviar;?>" id="<?=$resultado['act_id'];?>" name="calificaciones-eliminar.php?idR=<?=base64_encode($resultado['act_id']);?>&idIndicador=<?=base64_encode($resultado['act_id_tipo']);?>&carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" onClick="deseaEliminar(this)">Eliminar</a></li>
							</ul>
						</div>
						
						<?php } ?>
					</td>
				</tr>
				<?php 
						$contReg++;
					}

					?>
			</tbody>
			<tfoot>
				<tr style="font-weight:bold;">
					<td colspan="4"><?=strtoupper($frases[107][$datosUsuarioActual['uss_idioma']]);?></td>
					<td><?=$porcentajeActual;?>%</td>
					<td colspan="3"></td>
					</tr>
			</tfoot>
		</table>
		</div>
	</div>
</div>

<?php include("../compartido/guardar-historial-acciones.php");?>