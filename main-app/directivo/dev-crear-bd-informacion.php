<?php
$idPaginaInterna = 'DV0007';

include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");

//CONSULTA EXISTENCIA DE LA Compañía
try{
    $consultaInstituciones = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".instituciones WHERE ins_bd='".$bdInstitucion."' AND (SUBSTRING_INDEX(ins_years, ',', 1)<='".$year."' AND SUBSTRING_INDEX(ins_years, ',', -1)>='".$year."')");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$numInstituciones=mysqli_num_rows($consultaInstituciones);
$datosInstitucion=mysqli_fetch_array($consultaInstituciones, MYSQLI_BOTH);

$insBD=!empty($_POST['ins_bd']) ? $_POST['ins_bd'] : "";
$variables='?tipoInsti='.base64_encode($_POST['tipoInsti']).'&idInsti='.base64_encode($_POST['idInsti']).'&ins_bd='.base64_encode($insBD).'&yearA='.base64_encode($_POST['yearA']).'&siglasBD='.base64_encode($_POST['siglasBD']).'&nombreInsti='.base64_encode($_POST['nombreInsti']).'&siglasInst='.base64_encode($_POST['siglasInst']).'&yearN='.base64_encode($_POST['yearN']).'&nombre1='.base64_encode($_POST['nombre1']).'&nombre2='.base64_encode($_POST['nombre2']).'&apellido1='.base64_encode($_POST['apellido1']).'&apellido2='.base64_encode($_POST['apellido2']).'&tipoDoc='.base64_encode($_POST['tipoDoc']).'&documento='.base64_encode($_POST['documento']).'&email='.base64_encode($_POST['email']).'&celular='.base64_encode($_POST['celular']);
?>
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
                                <div class="page-title">Confirmación BD nueva</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <?php
                                    if(empty($_POST["confirmacion"]) || $_POST["confirmacion"]!=1){

                                        $msgConfirmacion="
                                            Usted esta creando una Compañía nueva para el año <b>$year</b> con los siguientes datos:<br><br>
                                            Tipo De Compañía:      <b>Nueva</b>.<br>
                                            Nombre De La Compañía: <b>".$_POST['nombreInsti']."</b>.<br>
                                            Siglas De La Compañía: <b>".$_POST['siglasInst']."</b>.<br>
                                            Año a crear:              <b>".$year."</b>.
                                        ";
                                        if($nueva==0){
                                            $msgConfirmacion="
                                                Usted está renovando una Compañía para el año <b>$year</b> con los siguientes datos:<br><br>
                                                Tipo De Compañía:      <b>Antigua</b>.<br>
                                                ID:                       <b>$idInsti</b>.<br>
                                                Nombre De La Compañía: <b>".$datosInsti['ins_nombre']."</b>.<br>
                                                Año a crear:              <b>".$year."</b>.
                                            ";
                                        }
                                ?>

								<div class="col-md-12">
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Confirmación</header>
										<div class="panel-body">
											<p><b>DATOS DE CONFIRMACIÓN:</b></p>
                                            <p><?=$msgConfirmacion;?></p>
                                            <p>Desea Continuar?</p>
                                            <form class="form-horizontal" action="crear-bd.php" method="post">
                                                <input type="hidden" name="tipoInsti" value="<?=$_POST['tipoInsti'];?>">
                                                <input type="hidden" name="idInsti" value="<?=$_POST['idInsti'];?>">
                                                <input type="hidden" name="ins_bd" value="<?=$insBD;?>">
                                                <input type="hidden" name="yearA" value="<?=$_POST['yearA'];?>">
                                                <input type="hidden" name="siglasBD" value="<?=$_POST['siglasBD'];?>">
                                                <input type="hidden" name="nombreInsti" value="<?=$_POST['nombreInsti'];?>">
                                                <input type="hidden" name="siglasInst" value="<?=$_POST['siglasInst'];?>">
                                                <input type="hidden" name="yearN" value="<?=$_POST['yearN'];?>">
                                                <input type="hidden" name="nombre1" value="<?=$_POST['nombre1'];?>">
                                                <input type="hidden" name="nombre2" value="<?=$_POST['nombre2'];?>">
                                                <input type="hidden" name="apellido1" value="<?=$_POST['apellido1'];?>">
                                                <input type="hidden" name="apellido2" value="<?=$_POST['apellido2'];?>">
                                                <input type="hidden" name="tipoDoc" value="<?=$_POST['tipoDoc'];?>">
                                                <input type="hidden" name="documento" value="<?=$_POST['documento'];?>">
                                                <input type="hidden" name="email" value="<?=$_POST['email'];?>">
                                                <input type="hidden" name="celular" value="<?=$_POST['celular'];?>">
                                                <input type="hidden" name="confirmacion" value="1">

                                                <a href="javascript:void(0);" name="dev-crear-nueva-bd.php<?=$variables;?>" class="btn btn-secondary" onclick="deseaRegresar(this)"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>Regresar</a>
                                                <button type="submit" class="btn  deepPink-bgcolor">Continuar 
                                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                                </button>
                                            </form>
										</div>
                                    </div>
                                </div>
                                <?php
                                    }else{
                                        $boton='
                                        <button type="submit" class="btn  deepPink-bgcolor">Crear y finalizar 
                                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                                        </button>
                                        ';
                                        $texto="
                                            El año <b>$year</b> está disponible.<br><br>
                                            Desea continuar?
                                        ";
                                        if($numInstituciones>0){
                                            $boton='';
                                            $texto="
                                                Ya existe en nuestro sistema <b>$numInstituciones</b> Compañía con el año <b>$year</b>.<br>
                                                Por favor, confirmar los datos ingresados para poder continuar.
                                            ";
                                        }
                                ?>
                                <div class="col-md-12">
                                    <div class="panel">
                                        <header class="panel-heading panel-heading-purple">Confirmación BD nueva</header>
                                        <div class="panel-body">
                                            <p><?=$texto?></p>
                                            <form class="form-horizontal" action="crear-bd.php" method="post">
                                                <input type="hidden" name="tipoInsti" value="<?=$_POST['tipoInsti'];?>">
                                                <input type="hidden" name="idInsti" value="<?=$_POST['idInsti'];?>">
                                                <input type="hidden" name="ins_bd" value="<?=$insBD;?>">
                                                <input type="hidden" name="yearA" value="<?=$_POST['yearA'];?>">
                                                <input type="hidden" name="siglasBD" value="<?=$_POST['siglasBD'];?>">
                                                <input type="hidden" name="nombreInsti" value="<?=$_POST['nombreInsti'];?>">
                                                <input type="hidden" name="siglasInst" value="<?=$_POST['siglasInst'];?>">
                                                <input type="hidden" name="yearN" value="<?=$_POST['yearN'];?>">
                                                <input type="hidden" name="nombre1" value="<?=$_POST['nombre1'];?>">
                                                <input type="hidden" name="nombre2" value="<?=$_POST['nombre2'];?>">
                                                <input type="hidden" name="apellido1" value="<?=$_POST['apellido1'];?>">
                                                <input type="hidden" name="apellido2" value="<?=$_POST['apellido2'];?>">
                                                <input type="hidden" name="tipoDoc" value="<?=$_POST['tipoDoc'];?>">
                                                <input type="hidden" name="documento" value="<?=$_POST['documento'];?>">
                                                <input type="hidden" name="email" value="<?=$_POST['email'];?>">
                                                <input type="hidden" name="celular" value="<?=$_POST['celular'];?>">
                                                <input type="hidden" name="continue" value="1">

                                                <a href="javascript:void(0);" name="dev-crear-nueva-bd.php<?=$variables;?>" class="btn btn-secondary" onclick="deseaRegresar(this)"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>Regresar</a>
                                                <?=$boton?>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php }?>

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