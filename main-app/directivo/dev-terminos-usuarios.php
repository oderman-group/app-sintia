<?php
include("session.php");

$idPaginaInterna = 'DV0028';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

try{
    $consultaTerminos = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".terminos_tratamiento_politica WHERE ttp_id='".base64_decode($_GET['id'])."'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$resultadoTerminos = mysqli_fetch_array($consultaTerminos, MYSQLI_BOTH);
?>
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                            <div class="page-title">Usuarios que aceptaron <b><?=$resultadoTerminos['ttp_nombre'];?></b></div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <?php
                                $id=base64_decode($_GET["id"]);
                                $year=$agnoBD;
                                if (!empty($_GET["year"])) {
                                    $year=base64_decode($_GET["year"]);
                                } 
                                $filtro = '';
                                $insti="";
                                if (!empty($_GET["insti"])) {
                                    $insti=base64_decode($_GET["insti"]);
                                    $filtro .= " AND ins_id='" . $insti . "'";
                                }
                                $desde="";
                                $hasta="";
                                if (!empty($_GET["fFecha"]) || (!empty($_GET["desde"]) || !empty($_GET["hasta"]))) {
                                    $desde=$_GET["desde"];
                                    $hasta=$_GET["hasta"];
                                    $filtro .= " AND (ttpxu_fecha_aceptacion BETWEEN '" . $_GET["desde"] . "' AND '" . $_GET["hasta"] . "' OR ttpxu_fecha_aceptacion LIKE '%" . $_GET["hasta"] . "%')";
                                }                        
                            ?>

                            <div class="col-md-12">
                                <?php
                                include("../../config-general/mensajes-informativos.php");
                                include("includes/barra-superior-dev-terminos-usuarios.php");
                                ?>

                                <div class="card card-topline-purple">
                                    <div class="card-head">
                                        <header>Usuarios que aceptaron <b><?=$resultadoTerminos['ttp_nombre'];?></b></header>
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
                                                        <th>#</th>
                                                        <th>Cod</th>
                                                        <th>Fecha Aceptación</th>
                                                        <th>Usuario</th>
                                                        <th>Institución</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
													include("includes/consulta-paginacion-dev-terminos-usuarios.php");

                                                    try{
                                                        $consulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios
                                                        INNER JOIN ".$baseDatosServicios.".instituciones ON ins_id=ttpxu_id_institucion AND ins_enviroment='".ENVIROMENT."'
                                                        WHERE ttpxu_id_termino_tratamiento_politicas='".$id."' AND YEAR(ttpxu_fecha_aceptacion) =".$year." ".$filtro."
                                                        ORDER BY ttpxu_id DESC
                                                        LIMIT $inicio,$registros;");
                                                    } catch (Exception $e) {
                                                        include("../compartido/error-catch-to-report.php");
                                                    }
                                                    $contReg = 1;
                                                    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {

                                                        $responsable="";
                                                        if($resultado['ttpxu_id_usuario']!=0){
                                                            $consultaResponsable = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$resultado['ttpxu_id_usuario']."'", $resultado["ins_id"], $year);
                                                            $datosResponsable = mysqli_fetch_array($consultaResponsable, MYSQLI_BOTH);
                                                            $responsable=UsuariosPadre::nombreCompletoDelUsuario($datosResponsable);

                                                        }
                                                    ?>
                                                        <tr>
                                                            <td><?= $contReg; ?></td>
                                                            <td><?= $resultado['ttpxu_id']; ?></td>
                                                            <td><?= $resultado['ttpxu_fecha_aceptacion']; ?></td>
                                                            <td><?= $responsable; ?></td>
                                                            <td><?= $resultado['ins_siglas']; ?></td>
                                                        </tr>
                                                    <?php $contReg++;
                                                    } ?>
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
    <?php include("../compartido/footer.php"); ?>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- data tables -->
<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
<script src="../../config-general/assets/js/pages/table/table_data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- end js include path -->
</body>

</html>