<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0063';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>

	
	<script type="text/javascript">
  	function ipc(enviada){
  	var ih = enviada.value;
  	var curso = enviada.id;
  	var materia = enviada.name;	
	  $('#resp').empty().hide().html("Esperando...").show(1);
		datos = "ih="+(ih)+
				   "&curso="+(curso)+
				   "&materia="+(materia);
			   $.ajax({
				   type: "POST",
				   url: "../compartido/ajax-ipc.php",
				   data: datos,
				   success: function(data){
				   $('#resp').empty().hide().html(data).show(1);
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
                                <div class="page-title">Intesidad por cursos</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="#" name="cursos.php" onClick="deseaRegresar(this)"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Intesidad por cursos</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								<div class="card card-topline-purple">
									<div class="card-head">
										<header>
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
														<a href="cursos-actualizar-cargas.php" class="btn btn-danger" onClick="if(!confirm('A continuación se buscará la intensidad horaria de los cursos y materias registrados en las cargas académicas para llenar esta tabla. Desea continuar?')){return false;}">
															Actualizar con las cargas <i class="fa fa-plus"></i>
														</a>
													</div>
												</div>
											</div>												
										</header>
										<div class="tools">
											<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
											<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
											<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
										</div>
									</div>
									<div class="card-body">
										<span id="resp"></span>							
										<div class="table-scrollable">
											<table id="example1" class="display" style="width:100%;">
												<thead>
													<tr>
														<th width="50%">Materia</th>
														<?php
														$cursos = mysqli_query($conexion, "SELECT * FROM academico_grados"); 
														while($c = mysqli_fetch_array($cursos, MYSQLI_BOTH)){
														?>
														<th style="font-size:8px; text-align:center;"><?=$c[2];?></th>
														<?php
														}
														?>
													</tr>
												</thead>
												<tbody>
													<?php
													$materias = mysqli_query($conexion, "SELECT * FROM academico_materias");
													while($m = mysqli_fetch_array($materias, MYSQLI_BOTH)){
													?>
													<tr id="data1">
														<td><?=$m[2];?></td>
														<?php
														$curso = mysqli_query($conexion, "SELECT * FROM academico_grados"); 
														while($c = mysqli_fetch_array($curso, MYSQLI_BOTH)){
															$consultaIpc=mysqli_query($conexion, "SELECT * FROM academico_intensidad_curso WHERE ipc_curso=".$c[0]." AND ipc_materia=".$m[0]."");
															$ipc = mysqli_fetch_array($consultaIpc, MYSQLI_BOTH); 
														?>
															<td><input type="text" style="width:20px; text-align:center;" maxlength="2" value="<?=$ipc['ipc_intensidad'];?>" id="<?=$c[0];?>" name="<?=$m[0];?>" onChange="ipc(this)" title="<?=$c[2];?>"></td>
														<?php
														}
														?>
													</tr>
													<?php 
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
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
</body>

</html>