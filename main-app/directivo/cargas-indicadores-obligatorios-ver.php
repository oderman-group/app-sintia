<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0036';?>
<?php include("verificar-permiso-pagina.php");?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    
  <script type="text/javascript">
  function ipc(enviada){
  	var estado = enviada.value;
  	var carga = enviada.id;
  	var indicador = enviada.name;
	  $('#resp').empty().hide().html("Esperando...").show(1);
		datos = "estado="+(estado)+
				   "&carga="+(carga)+
				   "&indicador="+(indicador);
			   $.ajax({
				   type: "POST",
				   url: "../compartido/ajax-ipcym.php",
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
                                <div class="page-title">Grados por asignatura: <b><?=$_GET["indNombre"];?></b></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

								<div class="col-md-4 col-lg-3">
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Grados por asignatura: <b><?=$_GET["indNombre"];?></b></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th width="50%">Materia</th>
                                                        <?php
                                                        $cursos = mysql_query("SELECT * FROM academico_grados",$conexion); 
                                                        while($c = mysql_fetch_array($cursos)){
                                                        ?>
                                                            <th style="font-size:8px; text-align:center;"><?=$c[2];?></th>
                                                        <?php
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <!-- END -->
                                                <!-- BEGIN -->
                                                <tbody>
                                                <?php
                                                $materias = mysql_query("SELECT * FROM academico_materias",$conexion);
                                                while($m = mysql_fetch_array($materias)){
                                                ?>
                                                <tr id="data1" class="odd gradeX">
                                                    <td><?=$m[2];?></td>
                                                    <?php
                                                    $curso = mysql_query("SELECT * FROM academico_grados",$conexion); 
                                                    while($c = mysql_fetch_array($curso)){
                                                        $carga = mysql_fetch_array(mysql_query("SELECT * FROM academico_cargas WHERE car_curso=".$c[0]." AND car_materia=".$m[0]."",$conexion));
                                                        $ipc = mysql_fetch_array(mysql_query("SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$carga[0]."' AND ipc_indicador='".$_GET["ind"]."' AND ipc_creado=0",$conexion));
                                                        
                                                        $cargas = mysql_query("SELECT * FROM academico_cargas WHERE car_curso=".$c[0]." AND car_materia=".$m[0]."",$conexion);
                                                        $indCreados=0;
                                                        while($cgs = mysql_fetch_array($cargas)){
                                                            $ipcC = mysql_num_rows(mysql_query("SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$cgs[0]."' AND ipc_creado=1",$conexion));
                                                            $calC = mysql_num_rows(mysql_query("SELECT * FROM academico_actividades WHERE act_id_carga='".$cgs[0]."' AND act_estado=1",$conexion));
                                                            if($ipcC>0 or $calC>0) $indCreados=1;
                                                        }
                                                        
                                                        if($carga['car_id']=="") {$estadoD = 'disabled'; $fondo = '#FFF';} 
                                                        elseif($indCreados==1){$estadoD = 'disabled'; $fondo = '#F03';} 
                                                        else {$estadoD = ''; $fondo = '#FFF';}
                                                    ?>
                                                        <td style="background:<?=$fondo;?>;"><input type="checkbox" style="width:20px; text-align:center;"  value="1" id="<?=$carga['car_id'];?>" name="<?=$_GET["ind"];?>" onClick="ipc(this)" title="<?=$c[2]." - ".$m[2];?>" <?php if($ipc[0]!=""){echo "checked";}?> <?=$estadoD;?>></td>
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
								
								<div class="col-md-4 col-lg-3">
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>
								
								
								
							
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->
             <?php include("../compartido/panel-configuracion.php");?>
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