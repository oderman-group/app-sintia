<?php include("session.php");?>

<?php $idPaginaInterna = 'DC0021';?>

<?php include("../compartido/historial-acciones-guardar.php");?>

<?php include("verificar-carga.php");?>

<?php include("verificar-periodos-diferentes.php");?>

<?php include("../compartido/head.php");?>

<?php
require_once("../class/Estudiantes.php");
?>

<?php
$consultaCalificaciones=mysqli_query($conexion, "SELECT * FROM academico_actividades 
INNER JOIN academico_indicadores ON ind_id=act_id_tipo
WHERE act_id='".$_GET["idR"]."' AND act_estado=1");
$calificacion = mysqli_fetch_array($consultaCalificaciones, MYSQLI_BOTH);

?>

<!-- Theme Styles -->

<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />

<script type="application/javascript">

//CALIFICACIONES
function notasGuardar(enviada){

	var codNota = <?=$_GET["idR"];?>;	 
	var nota = enviada.value;
	var notaAnterior = enviada.name;	
	var codEst = enviada.id;
	var nombreEst = enviada.alt;

	if (nota><?=$config[4];?> || isNaN(nota) || nota < <?=$config[3];?>) {
		alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); 
		return false;
	}

	$('#respNota').empty().hide().html("Guardando la nota, espere por favor...").show(1);

	datos = "nota="+(nota)+
			"&codNota="+(codNota)+
			"&notaAnterior="+(notaAnterior)+
			"&nombreEst="+(nombreEst)+
			"&codEst="+(codEst);
			$.ajax({
				type: "POST",
				url: "ajax-notas-guardar.php",
				data: datos,
				success: function(data){
					$('#respNota').empty().hide().html(data).show(1);
				}
			});
}



function notas(enviada){

  var codNota = <?=$_GET["idR"];?>;	 

  var nota = enviada.value;

  var notaAnterior = enviada.name;	

  var codEst = enviada.id;

  var nombreEst = enviada.alt;

  var operacion = enviada.title;

 

if(operacion == 1 || operacion == 3){

	if (nota><?=$config[4];?> || isNaN(nota) || nota < <?=$config[3];?>) {alert('Ingrese un valor numerico entre <?=$config[3];?> y <?=$config[4];?>'); return false;}

}

	  

$('#respRC').empty().hide().html("Guardando información, espere por favor...").show(1);

	datos = "nota="+(nota)+

			"&codNota="+(codNota)+

			"&notaAnterior="+(notaAnterior)+

			"&operacion="+(operacion)+

			"&nombreEst="+(nombreEst)+

			"&codEst="+(codEst);

		   $.ajax({

			   type: "POST",

			   url: "ajax-calificaciones-registrar.php",

			   data: datos,

			   success: function(data){

			   	$('#respRC').empty().hide().html(data).show(1);

		   	   }

		  });

}

</script>

</head>

<!-- END HEAD -->

<?php include("../compartido/body.php");?>

    <div class="page-wrapper">

        <?php include("../compartido/encabezado.php");?>

		

        <?php include("../compartido/panel-color.php");?>

        <!-- start page container -->

        <div class="page-container">

 			<?php include("../compartido/menu.php");?>

			<!-- start page content -->

            <div class="page-content-wrapper">

                <div class="page-content">

                    <div class="page-bar">

                        <div class="page-title-breadcrumb">

                            <div class=" pull-left">

                                <div class="page-title"><?=$calificacion['act_descripcion']." (".$calificacion['act_valor']."%)";?></div>

								<p style="font-size: 13px; color: darkblue;"><?=$calificacion['ind_nombre'];?></p>

								

								<?php include("../compartido/texto-manual-ayuda.php");?>

                            </div>

							<ol class="breadcrumb page-breadcrumb pull-right">

                                <li><a class="parent-item" href="calificaciones.php"><?=$frases[6][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>

                                <li class="active"><?=$calificacion['act_descripcion'];?></li>

                            </ol>

                        </div>

                    </div>

                    

                    <div class="row">

                        <div class="col-md-12">

                            <div class="row">

                                

								<div class="col-md-4 col-lg-3">

									

									<?php include("info-carga-actual.php");?>

									

									<div class="panel">

										<header class="panel-heading panel-heading-purple">TABLA DE VALORES</header>



										<div class="panel-body">

											  <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">

												<!-- BEGIN -->

												<thead>

												  <tr>

													<th>Desde</th>

													<th>Hasta</th>

													<th>Resultado</th>

												  </tr>

												</thead>

												<tbody>

												 <?php

												 $TablaNotas = mysqli_query($conexion, "SELECT * FROM academico_notas_tipos WHERE notip_categoria='".$config["conf_notas_categoria"]."'");

												 while($tabla = mysqli_fetch_array($TablaNotas, MYSQLI_BOTH)){

												 ?>

												  <tr id="data1" class="odd grade">



													<td><?=$tabla["notip_desde"];?></td>

													<td><?=$tabla["notip_hasta"];?></td>

													<td><?=$tabla["notip_nombre"];?></td>

												  </tr>

												  <?php }

													mysqli_free_result($TablaNotas);

													?>

												</tbody>

											  </table>

										</div>

										

                                    </div>

									

									<div class="panel">

										<header class="panel-heading panel-heading-purple"><?=strtoupper($frases[6][$datosUsuarioActual['uss_idioma']]);?> </header>

										<div class="panel-body">

											<p>Puedes cambiar a otra actividad rápidamente para calificar a tus estudiantes o hacer modificaciones de notas.</p>

											<?php

											$registrosEnComun = mysqli_query($conexion, "SELECT * FROM academico_actividades 

											WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1 AND act_id!='".$_GET["idR"]."'

											ORDER BY act_id DESC

											");

											while($regComun = mysqli_fetch_array($registrosEnComun, MYSQLI_BOTH)){

											?>

												<p><a href="<?=$_SERVER['PHP_SELF'];?>?idR=<?=$regComun['act_id'];?>"><?=$regComun['act_descripcion']." (".$regComun['act_valor']."%)";?></a></p>

											<?php }

											mysqli_free_result($registrosEnComun);

											?>

										</div>

                                    </div>

									

								</div>

									

								<div class="col-md-8 col-lg-9">

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

												<div class="col-sm-12" align="center">

													<p style="color: darkblue;">Utilice esta casilla para colocar la misma nota a todos los estudiantes. Esta opción <mark>reemplazará las notas existentes</mark> en esta actividad.</p>

													<input type="text" style="text-align: center; font-weight: bold;" maxlength="3" size="10" title="3" onChange="notas(this)">

												</div>

											</div>

											

											

										<span style="color: blue; font-size: 15px;" id="respRC"></span>
										<span style="color: blue; font-size: 15px;" id="respNota"></span>

											

											

                                        <div class="table-responsive">

                                            <table class="table table-striped custom-table table-hover">

                                                <thead>

                                                    <tr>

                                                        <th>#</th>

														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>

														<th><?=$frases[108][$datosUsuarioActual[8]];?></th>

														<th>Recup.</th>

														<th><?=$frases[109][$datosUsuarioActual[8]];?></th>

                                                    </tr>

                                                </thead>

                                                <tbody>

													<?php
													$consulta = Estudiantes::listarEstudiantesParaDocentes($filtroDocentesParaListarEstudiantes);
													$contReg = 1;
													$colorNota = "black";
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){

														 if($calificacion['act_registrada']==1){

															 //Consulta de calificaciones si ya la tienen puestas.
															$consultaNotas=mysqli_query($conexion, "SELECT * FROM academico_calificaciones WHERE cal_id_estudiante=".$resultado[0]." AND cal_id_actividad='".$_GET["idR"]."'");
															 $notas = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);

															 if($notas[3]<$config[5] and $notas[3]!="") $colorNota = $config[6]; elseif($notas[3]>=$config[5]) $colorNota = $config[7];

														 }

														 

														 $fotoEst = $usuariosClase->verificarFoto($resultado['uss_foto']);

													 ?>

													

													<?php

													$arrayEnviar = array("tipo"=>2, "descripcionTipo"=>"Para ocultar la X y limpiar valor.", "idInput"=>$resultado['mat_id']);

													$arrayDatos = json_encode($arrayEnviar);

													$objetoEnviar = htmlentities($arrayDatos);

													?>

                                                    

													<tr>

                                                        <td><?=$contReg;?></td>

														<td>

															<img src="<?=$fotoEst;?>" width="50">

															<?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>

														</td>

														<td>

															<input type="text" style="text-align: center; color:<?=$colorNota;?>" size="5" maxlength="3" value="<?=$notas['cal_nota'];?>" name="<?=$notas['cal_nota'];?>" id="<?=$resultado['mat_id'];?>" alt="<?=$resultado['mat_nombres'];?>" onChange="notasGuardar(this)" tabindex="<?=$contReg;?>">

															<?php if($notas['cal_nota']!=""){?>

															<a href="#" title="<?=$objetoEnviar;?>" id="<?=$notas['cal_id'];?>" name="guardar.php?get=21&id=<?=$notas['cal_id'];?>" onClick="deseaEliminar(this)">X</a>

															<?php }?>

														</td>

														<td>

															<?php if($notas['cal_nota']!=""){?>

															<input type="text" style="text-align: center;" size="5" maxlength="3" name="<?=$notas['cal_nota'];?>" id="<?=$resultado['mat_id'];?>" alt="<?=$resultado['mat_nombres'];?>" title="4" onChange="notas(this)">

															<?php }?>

														</td>

														<td><input type="text" value="<?=$notas['cal_observaciones'];?>" name="O<?=$contReg;?>" id="<?=$resultado['mat_id'];?>" alt="<?=$resultado['mat_nombres'];?>" title="2" onChange="notas(this)" tabindex="10<?=$contReg;?>"></td>

                                                    </tr>

													<?php 

														 $contReg++;

													  }

													mysqli_free_result($consulta);

															

													  ?>

                                                </tbody>

                                            </table>

                                            </div>

                                        </div>

                                    </div>

                                </div>

								

							

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- end page content -->

             <?php // include("../compartido/panel-configuracion.php");?>

        </div>

        <!-- end page container -->

        <?php include("../compartido/footer.php");?>

    </div>

    <!-- start js include path -->

    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>

    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>

    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>

	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- bootstrap -->

    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>

    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>

    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>

    <!-- Common js-->

	<script src="../../config-general/assets/js/app.js" ></script>

    <script src="../../config-general/assets/js/layout.js" ></script>

	<script src="../../config-general/assets/js/theme-color.js" ></script>

	<!-- notifications -->

	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>

	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	

	<!-- Material -->

	<script src="../../config-general/assets/plugins/material/material.min.js"></script>

    <!--tags input-->

    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>

    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>

    <!-- end js include path -->

</body>



</html>