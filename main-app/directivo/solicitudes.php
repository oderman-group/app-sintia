<?php
include("session.php");
$idPaginaInterna = 'DT0122';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Solicitud_Desbloqueo/General_Solicitud.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Estudiante.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include(ROOT_PATH."/main-app/compartido/encabezado.php");?>
		
        <?php include(ROOT_PATH."/main-app/compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include(ROOT_PATH."/main-app/compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Solicitudes</div>
								<?php include(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                                $filtro="";
                                include("includes/barra-superior-solicitudes.php");
                            ?>
                            <div class="row">
								<div class="col-md-8 col-lg-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Solicitudes</header>
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
														<th>ID</th>
														<th>Fecha</th>
														<th>Remitente</th>
														<th>Estudiante</th>
														<th>Mensaje</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
													</tr>
												</thead>
                                                <tbody>
												<?php
                                                include("includes/consulta-paginacion-solicitudes.php");

                                                $predicado = [
                                                    'soli_institucion' => $_SESSION['idInstitucion'],
                                                    'soli_year'        => $_SESSION['bd']
                                                ];
                                            
                                                $consulta = Administrativo_Solicitud_Desbloqueo_General_Solicitud::Select($predicado, "*", BD_GENERAL);

												while($resultado = $consulta->fetch(PDO::FETCH_ASSOC)){		

                                                    $predicadoRemitente = [
                                                        'uss_id'      => $resultado['soli_remitente'],
                                                        'institucion' => $_SESSION['idInstitucion'],
                                                        'year'        => $_SESSION['bd']
                                                    ];
                                                
                                                    $camposRemitente    = "uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2";
                                                    $consultaRemitente  = Administrativo_Usuario_Usuario::Select($predicadoRemitente, $camposRemitente, BD_GENERAL);
                                                    $resultadoRemitente = $consultaRemitente->fetch(PDO::FETCH_ASSOC);
                                                    $nombreRemitente    = UsuariosPadre::nombreCompletoDelUsuario($resultadoRemitente);

                                                    $nombreRecurso = $nombreRemitente;
                                                    $IDUsuario = $resultado['soli_id_recurso'];
                                                    if ($resultado['soli_id_recurso'] != $resultado['soli_remitente']) {

                                                        $predicadoEstudiante = [
                                                            'mat_id'      => $resultado['soli_id_recurso'],
                                                            'institucion' => $_SESSION['idInstitucion'],
                                                            'year'        => $_SESSION['bd']
                                                        ];
                                                    
                                                        $camposEstudiante    = "mat_nombres, mat_nombre2, mat_primer_apellido, mat_segundo_apellido, mat_id_usuario";
                                                        $consultaEstudiante  = Administrativo_Usuario_Estudiante::Select($predicadoEstudiante, $camposEstudiante, BD_ACADEMICA);
                                                        $resultadoEstudiante = $consultaEstudiante->fetch(PDO::FETCH_ASSOC);
                                                        $nombreRecurso       = Estudiantes::NombreCompletoDelEstudiante($resultadoEstudiante);
                                                        $IDUsuario = $resultadoEstudiante['mat_id_usuario'];
                                                    }
												?>
												<tr>
													<td><?=$resultado['soli_id'];?></td>
													<td><?=$resultado['soli_fecha'];?></td>
													<td><?=$nombreRemitente;?></td>
													<td><?=$nombreRecurso;?></td>
													<td><?=$resultado['soli_mensaje'];?></td>
                                                    <td id="estado<?=$resultado['soli_id'];?>"><?=$estadosSolicitudes[$resultado['soli_estado']];?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button class="btn btn-xs btn-info dropdown-toggle center no-margin" type="button" data-toggle="dropdown" aria-expanded="false"> Acciones
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu pull-left" role="menu" x-placement="bottom-start">
                                                                <li data-id-estado="2" data-id-registro="<?=$resultado['soli_id'];?>" data-id-recurso="<?=$resultado['soli_id_recurso'];?>" data-id-usuario="<?=$IDUsuario;?>" onclick="cambiarEstados(this)"><a href="#">En proceso</a></li>
                                                                <li data-id-estado="3" data-id-registro="<?=$resultado['soli_id'];?>" data-id-recurso="<?=$resultado['soli_id_recurso'];?>" data-id-usuario="<?=$IDUsuario;?>" onclick="cambiarEstados(this)"><a href="#">Aceptar</a></li>
                                                                <li data-id-estado="4" data-id-registro="<?=$resultado['soli_id'];?>" data-id-recurso="<?=$resultado['soli_id_recurso'];?>" data-id-usuario="<?=$IDUsuario;?>" onclick="cambiarEstados(this)"><a href="#">Rechazar</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
												</tr>
												<?php }?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                      				<?php include("enlaces-paginacion.php");?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page container -->
        <?php include(ROOT_PATH."/main-app/compartido/footer.php");?>
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