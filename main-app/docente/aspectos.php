<?php
include("session.php");
$idPaginaInterna = 'DC0063';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
require_once("../class/Estudiantes.php");
include("../compartido/head.php");
?>

<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<!-- dropzone -->
    <link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
    <!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
    <!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />

<script type="application/javascript">
//CALIFICACIONES	
function notas(enviada){
  var carga = <?=$cargaConsultaActual;?>;
  var periodo = <?=$periodoConsultaActual;?>; 
  var nota = enviada.value;
  var codEst = enviada.id;
  var nombreEst = enviada.alt;
  var operacion = enviada.title;
 
if(operacion == 1 || operacion == 3 || operacion == 5){
    if (alertValidarNota(nota)) {
		return false;
	}
}
	  
$('#respRC').empty().hide().html("Guardando información, espere por favor...").show(1);
	datos = "nota="+(nota)+
			"&operacion="+(operacion)+
			"&nombreEst="+(nombreEst)+
			"&carga="+(carga)+
			"&periodo="+(periodo)+
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
                                <div class="page-title">Aspectos estudiantiles</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    <?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
									
								<div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Aspectos estudiantiles</header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
										
										
									
										
                                        <div class="card-body">
											
											
										<span style="color: blue; font-size: 15px;" id="respRC"></span>
											
											
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[61][$datosUsuarioActual[8]];?></th>
														<th>Académicos</th>
														<th>Convivenciales</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
													 $contReg = 1;
													 $colorNota = "black";
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $consultaNotas=mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
                                                         WHERE dn_cod_estudiante='".$resultado['mat_id']."' AND dn_periodo='".$periodoConsultaActual."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
														$notas = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
                                                        if(!empty($notas[4])){
                                                            if($notas[4]<$config[5] and $notas[4]!="") $colorNota = $config[6]; elseif($notas[4]>=$config[5]) $colorNota = $config[7];
                                                        }
														
														
													 ?>
                                                    
													<tr>
                                                        <td><?=$contReg;?></td>
														<td width="60%">
															<img src="../files/fotos/<?=$resultado['uss_foto'];?>" width="50">
															<?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>
														</td>
														<td width="20%">
															
															<textarea rows="7" cols="50" name="A<?=$contReg;?>" id="<?=$resultado['mat_id'];?>" alt="<?=$resultado['mat_nombres'];?>" title="10" onChange="notas(this)"><?=!empty($notas['dn_aspecto_academico'])?$notas['dn_aspecto_academico']:"";?></textarea>
														</td>
														<td width="20%">
															
															<textarea rows="7" cols="50" name="C<?=$contReg;?>" id="<?=$resultado['mat_id'];?>" alt="<?=$resultado['mat_nombres'];?>" title="11" onChange="notas(this)"><?=!empty($notas['dn_aspecto_convivencial'])?$notas['dn_aspecto_convivencial']:"";?></textarea>
														</td>
                                                    </tr>
													<?php 
														 $contReg++;
													  }

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
<!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

</html>