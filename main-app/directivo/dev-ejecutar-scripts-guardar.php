<?php 
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DV0014';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");

define('ENVIROMENT_TWO', $_POST['enviroment']);

switch (ENVIROMENT_TWO) {
	case 'LOCAL':
		include(ROOT_PATH."/conexion-datos.php");
		break;

	case 'DEV':
		include(ROOT_PATH."/conexion-datos-developer.php");
		break;

	case 'PROD':
		include(ROOT_PATH."/conexion-datos-production.php");
		break;

	default:
		include(ROOT_PATH."/conexion-datos.php");
		break;	
}
//AGREGAR/MODIFICAR COLUMNAS
$sql = $_POST['script'];

//consulta a instituciones no bloqueadas
$conexionAdmin = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
try{
    $consultaInstituciones = mysqli_query($conexionAdmin, "SELECT * FROM instituciones
    WHERE ins_bloqueada='0' AND ins_enviroment='".ENVIROMENT."'
    ");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <?php
                                    try{
                                        if(empty($_POST["continue"]) || $_POST["continue"]!=1 || empty($_SERVER['HTTP_REFERER'])){
                                ?>

								<div class="col-md-12">
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Confirmación</header>
										<div class="panel-body">
											<p><pre><?=$sql?></pre></p>
											<p><b>Este es el listado de instituciones, con sus años, a los cuales se aplicará el script mostrado arriba</b></p>
                                            <p>
												<?php
													$num = 1;
													while($listaInstituciones = mysqli_fetch_array($consultaInstituciones, MYSQLI_BOTH)) {
														echo $num.") {$listaInstituciones['ins_siglas']} - {$listaInstituciones['ins_bd']} ({$listaInstituciones['ins_years']})<br>";
														$num++;
													}
												?>
											</p>
                                            <p>Desea Continuar?</p>
                                            <form class="form-horizontal" action="dev-ejecutar-scripts-guardar.php" method="post">
												<input type="hidden" name="enviroment" value="<?=$_POST['enviroment'];?>">
												<input type="hidden" name="script" value="<?=$_POST['script'];?>">
												<input type="hidden" name="continue" value="1">

                                                <input type="submit" class="btn  deepPink-bgcolor" value="Confirmar">
                                                <a href="dev-ejecutar-scripts.php" class="btn btn-round btn-primary">Regresar</a>
                                            </form>
										</div>
                                    </div>
                                </div>
                                <?php 
                                            exit();
                                        }
                                    } catch (Exception $e) {
                                        include("../compartido/error-catch-to-report.php");
                                    }
                                ?>
								<div class="col-md-12">
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Proceso Finalizado</header>
										<div class="panel-body">
                                            <?php
                                                $num = 1;
                                                $bdInstituciones = array();
                                                while($datosInstitucion = mysqli_fetch_array($consultaInstituciones, MYSQLI_BOTH)){
                                                    
                                                    if(empty($datosInstitucion['ins_years']) || empty($datosInstitucion['ins_bd'])) {
                                                        continue;
                                                    }

                                                    $yearArray = explode(",", $datosInstitucion['ins_years']);
                                                    $yearStart = $yearArray[0];
                                                    $yearEnd = $yearArray[1];
                                                    
                                                    while($yearStart <= $yearEnd){
                                                        try {
                                                            $CURRENTDB = $datosInstitucion['ins_bd']."_".$yearStart;

                                                            $conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $CURRENTDB);
                                                            $resultado = mysqli_query($conexion, "SHOW DATABASES LIKE '{$CURRENTDB}';");

                                                            if(mysqli_num_rows($resultado) > 0){

                                                                mysqli_query($conexion, "{$sql}");
                                                                $filasAfectadas = mysqli_affected_rows($conexion);

                                                                if($filasAfectadas > 0){
                                                                    echo $num." <span style='color:blue;'>".$filasAfectadas." filas afectadas para ".$CURRENTDB."</span><br>";
                                                                } else {
                                                                    echo $num." <span style='color:black; background-color:yellow;'>No aparecen columnas afectadas, pero es muy probable que si haya aplicado los cambios para ".$CURRENTDB."</span><br>";
                                                                }

                                                                $bdInstituciones[$datosInstitucion['ins_id']][] = ["BD"=> $CURRENTDB];

                                                            } else {
                                                                echo $num." <span style='color:red; font-weight:bold;'>La base de datos no existe: ".$CURRENTDB."</span><br>";
                                                            }

                                                            echo "<br>";
                                                            $num ++;
                                                            $yearStart ++;

                                                        } catch (Exception $e) {
                                                            echo "<span style='color:black; background-color:gold;'>Exception caught for database: </span><br><b>{$CURRENTDB}</b>:  CODE: {$e->getCode()} - MESSAGE: ".$e->getMessage()."<br>";

                                                            echo "<br>";
                                                            $num ++;
                                                            $yearStart ++;
                                                        }
                                                    }
                                                }

                                                // Migrado a PDO - Consulta preparada
                                                try{
                                                    require_once(ROOT_PATH."/main-app/class/Conexion.php");
                                                    $conexionPDO = Conexion::newConnection('PDO');
                                                    $sqlInsert = "INSERT INTO ".$baseDatosServicios.".ejecucion_scripts(
                                                        spt_fecha_registro, spt_responsable, spt_script, spt_ambiente, spt_bds
                                                    ) VALUES (now(), ?, ?, ?, ?)";
                                                    $stmtInsert = $conexionPDO->prepare($sqlInsert);
                                                    $bdJson = json_encode($bdInstituciones);
                                                    $stmtInsert->bindParam(1, $_SESSION['id'], PDO::PARAM_STR);
                                                    $stmtInsert->bindParam(2, $sql, PDO::PARAM_STR);
                                                    $stmtInsert->bindParam(3, $_POST['enviroment'], PDO::PARAM_STR);
                                                    $stmtInsert->bindParam(4, $bdJson, PDO::PARAM_STR);
                                                    $stmtInsert->execute();
                                                }catch(Exception $e) {
                                                    include("../compartido/error-catch-to-report.php");
                                                }
                                            ?>
                                            <a href="dev-ejecutar-scripts.php" class="btn btn-round btn-primary">EJECUTAR OTRO SCRIPT</a>
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