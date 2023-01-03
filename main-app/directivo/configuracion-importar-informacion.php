<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0061';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaCfg=mysqli_query($conexion, "SELECT * FROM configuracion WHERE conf_id=1");
$cfg = mysqli_fetch_array($consultaCfg, MYSQLI_BOTH);
?>
<?php
//IMPORTAR CURSOS
if(isset($_POST["cursos"]))
{
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_grados");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_grados(gra_id, gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, gra_estado)SELECT gra_id, gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, gra_estado FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_grados");
	
	//INSERTAR EN EL HISTORIAL
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Importar Información - CURSOS ".$_POST["agnoDesde"]."', now())");
	
	echo '<script type="text/javascript">window.location.href="cursos.php";</script>';
	exit();
}
//IMPORTAR AREAS
if(isset($_POST["areas"]))
{
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_areas");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_areas(ar_id, ar_nombre, ar_posicion)SELECT ar_id, ar_nombre, ar_posicion FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_areas");
	
	//INSERTAR EN EL HISTORIAL
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Importar Información - AREAS ".$_POST["agnoDesde"]."', now())");
	
	echo '<script type="text/javascript">window.location.href="areas.php";</script>';
	exit();
}
//IMPORTAR MATERIAS
if(isset($_POST["materias"]))
{
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_materias");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_materias(mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area)SELECT mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_materias");
	
	//INSERTAR EN EL HISTORIAL
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Importar Información - MATERIAS ".$_POST["agnoDesde"]."', now())");
	
	echo '<script type="text/javascript">window.location.href="asignaturas.php";</script>';
	exit();
}
//IMPORTAR TODOS LOS USUARIOS
if(isset($_POST["usuarios"]))
{
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".usuarios");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro) SELECT uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".usuarios");
	
	
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_matriculas");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_matriculas(mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, mat_uso_sintia, mat_inicio, mat_meses, mat_fin, mat_folio, mat_codigo_tesoreria) SELECT mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, mat_uso_sintia, mat_inicio, mat_meses, mat_fin, mat_folio, mat_codigo_tesoreria FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_matriculas");
	
	
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".usuarios_por_estudiantes");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante) SELECT upe_id, upe_id_usuario, upe_id_estudiante FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".usuarios_por_estudiantes");
	
	
	//INSERTAR EN EL HISTORIAL
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Importar Información - USUARIOS ".$_POST["agnoDesde"]."', now())");
	
	echo '<script type="text/javascript">window.location.href="usuarios.php";</script>';
	exit();
}
//IMPORTAR CARGAS
if(isset($_POST["cargas"]))
{
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_cargas");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_cargas(car_id, car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable)SELECT car_id, car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_cargas");
	
	//INSERTAR EN EL HISTORIAL
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Importar Información - CARGAS ".$_POST["agnoDesde"]."', now())");
	
	echo '<script type="text/javascript">window.location.href="cargas.php";</script>';
	exit();
}

//IMPORTAR TODO
if(isset($_POST["todo"]))
{
	//CURSOS
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_grados");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_grados(gra_id, gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, gra_estado)SELECT gra_id, gra_codigo, gra_nombre, gra_formato_boletin, gra_valor_matricula, gra_valor_pension, gra_estado FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_grados");
	
	//AREAS
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_areas");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_areas(ar_id, ar_nombre, ar_posicion)SELECT ar_id, ar_nombre, ar_posicion FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_areas");
	
	//MATERIAS
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_materias");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_materias(mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area)SELECT mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_materias");
	
	//TODOS LOS USUARIOS
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".usuarios");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".usuarios(uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro) SELECT uss_id, uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_permiso1, uss_celular, uss_genero, uss_ultimo_ingreso, uss_ultima_salida, uss_telefono, uss_bloqueado, uss_fecha_registro, uss_responsable_registro FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".usuarios");
	
	
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_matriculas");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_matriculas(mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, mat_uso_sintia, mat_inicio, mat_meses, mat_fin, mat_folio, mat_codigo_tesoreria) SELECT mat_id, mat_matricula, mat_fecha, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_grado, mat_grupo, mat_genero, mat_fecha_nacimiento, mat_lugar_nacimiento, mat_tipo_documento, mat_documento, mat_lugar_expedicion, mat_religion, mat_direccion, mat_barrio, mat_telefono, mat_celular, mat_estrato, mat_foto, mat_tipo, mat_estado_matricula, mat_id_usuario, mat_eliminado, mat_email, mat_acudiente, mat_privilegio1, mat_privilegio2, mat_privilegio3, mat_uso_sintia, mat_inicio, mat_meses, mat_fin, mat_folio, mat_codigo_tesoreria FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_matriculas");
	
	
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".usuarios_por_estudiantes");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".usuarios_por_estudiantes(upe_id, upe_id_usuario, upe_id_estudiante) SELECT upe_id, upe_id_usuario, upe_id_estudiante FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".usuarios_por_estudiantes");
	
	//CARGAS
	mysqli_query($conexion, "DELETE FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_cargas");
	mysqli_query($conexion, "INSERT INTO ".$cfg["conf_base_datos"]."_".$_POST["agnoPara"].".academico_cargas(car_id, car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable)SELECT car_id, car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable FROM ".$cfg["conf_base_datos"]."_".$_POST["agnoDesde"].".academico_cargas");
	
	
	//INSERTAR EN EL HISTORIAL
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Importar Información - TODO ".$_POST["agnoDesde"]."', now())");
	
	echo '<script type="text/javascript">window.location.href="configuracion-importar-informacion.php";</script>';
	exit();
}
?>

<?php
mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Importar Información', now())");

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
                                <div class="page-title">Importar Información</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active">Importar Información</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						
						<div class="col-sm-3">


                        </div>
						
                        <div class="col-sm-9">
                          
                          <div class="alert alert-block alert-warning">
                              <h4 class="alert-heading">Información importante!</h4>
                              <p>Recuerde que usted debe estar consultando el año al cual desea traer la informaci&oacute;n. El año que usted está consultando actualmente es <a href="cambiar-bd.php" style="font-size:36px; color:#FC0; font-weight:bold; text-decoration:underline;"><?=$_SESSION["bd"];?></a>.</p>
                          </div>


								<div class="panel">
									<header class="panel-heading panel-heading-purple">Importar Información</header>
                                	<div class="panel-body">

                                   
                                    <form action="configuracion-importar-informacion.php" method="post" class="form-horizontal" enctype="multipart/form-data">
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">¿Desde que a&ntilde;o desea traer la informaci&oacute;n?</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="agnoDesde" required>
                                                <?php
                                                $cont=1;
                                                $con = date("Y")-3;
                                                while($cont<=4)
                                                {	
                                                if($_SESSION["bd"]==$con)
                                                    echo "<option value='".$con."' selected style='color:blue;'>".$con."</option>";
                                                else
                                                    echo "<option value='".$con."'>".$con."</option>";
                                                    $con++;
                                                    $cont++;
                                                }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">¿Para cual a&ntilde;o desea importar la informaci&oacute;n?</label>
                                            <div class="col-sm-10">
                                                <select class="form-control  select2" name="agnoPara" required>
                                                <?php
                                                $cont=1;
                                                $con = date("Y")-3;
                                                while($cont<=4)
                                                {	
                                                if($_SESSION["bd"]==$con)
                                                    echo "<option value='".$con."' selected style='color:blue;'>".$con."</option>";
                                                else
                                                    echo "<option value='".$con."'>".$con."</option>";
                                                    $con++;
                                                    $cont++;
                                                }
                                                ?>
                                                </select>
                                            </div>
                                        </div>


                                        <input type="submit" class="btn btn-success" value="Importar Cursos" name="cursos" onClick="if(!confirm('Esta seguro?')){return false;}">
                                        <input type="submit" class="btn btn-warning" value="Importar Areas" name="areas" onClick="if(!confirm('Esta seguro?')){return false;}">
                                        <input type="submit" class="btn btn-primary" value="Importar Materias" name="materias" onClick="if(!confirm('Esta seguro?')){return false;}">
                                        <input type="submit" class="btn btn-danger" value="Importar Usuarios" name="usuarios" onClick="if(!confirm('Esta opción Importa Docentes, directivos, acudientes y estudiantes. Desea Continuar?')){return false;}">
                                        <input type="submit" class="btn btn-success" value="Importar Cargas" name="cargas" onClick="if(!confirm('Esta seguro?')){return false;}">
                                        <input type="submit" class="btn btn-danger" value="IMPORTAR TODO" name="todo" onClick="if(!confirm('Esta opción Importa Cursos, Areas, Materias, Docentes, directivos, acudientes, estudiantes y Cargas. Desea Continuar?')){return false;}">
                                    </form>
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
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>