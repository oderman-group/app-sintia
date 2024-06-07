<?php
include("session.php");
$idPaginaInterna = 'DT0012';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/head.php");

include(ROOT_PATH."/main-app/admisiones/php-funciones.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");

$id = "";
if (!empty($_GET["id"])) {
    $id = base64_decode($_GET["id"]);
}

if (md5($id) != $_GET['token']) {
    redireccionMal(ROOT_PATH.'/main-app/admisiones/respuestas-usuario.php', 4);
}

//Grados
$gradosConsulta = "SELECT * FROM ".BD_ACADEMICA.".academico_grados
WHERE gra_estado = 1 AND institucion= :idInstitucion AND year= :year";
$grados = $conexionPDO->prepare($gradosConsulta);
$grados->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$grados->bindParam(':year', $_SESSION["bd"], PDO::PARAM_STR);
$grados->execute();
$num = $grados->rowCount();

//Estudiante
$estQuery = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat_acudiente AND uss.institucion= :idInstitucion AND uss.year= :year
WHERE mat_solicitud_inscripcion = :id AND mat.institucion= :idInstitucion AND mat.year= :year";
$est = $conexionPDO->prepare($estQuery);
$est->bindParam(':id', $id, PDO::PARAM_INT);
$est->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$est->bindParam(':year', $_SESSION["bd"], PDO::PARAM_STR);
$est->execute();
$num = $est->rowCount();
$datos = $est->fetch();

//Documentos
$datosDocumentos = Inscripciones::traerDocumentos($conexionPDO, $config, $datos['mat_id'], $_SESSION["bd"]);

//Padre
$padreQuery = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id = :id AND institucion= :idInstitucion AND year= :year";
$padre = $conexionPDO->prepare($padreQuery);
$padre->bindParam(':id', $datos['mat_padre'], PDO::PARAM_STR);
$padre->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$padre->bindParam(':year', $_SESSION["bd"], PDO::PARAM_STR);
$padre->execute();
$datosPadre = $padre->fetch();

//Madre
$madreQuery = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id = :id AND institucion= :idInstitucion AND year= :year";
$madre = $conexionPDO->prepare($madreQuery);
$madre->bindParam(':id', $datos['mat_madre'], PDO::PARAM_STR);
$madre->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$madre->bindParam(':year', $_SESSION["bd"], PDO::PARAM_STR);
$madre->execute();
$datosMadre = $madre->fetch();
?>
    <!-- steps -->
    <link rel="stylesheet" href="../../config-general/assets/plugins/steps/steps.css"> 

    <!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
    <style>
        .link {
            text-decoration: underline;
        }
        .link:hover {
            font-size: 17px;
        }
    </style>
</head>
<!-- END HEAD -->
<?php include(ROOT_PATH."/main-app/compartido/body.php");?>
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
                                <div class="page-title">Formulario de Inscripción</div>
								<?php include(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="inscripciones.php" onClick="deseaRegresar(this)"><?=$frases[390][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Formulario de Inscripción</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php include(ROOT_PATH."/main-app/admisiones/alertas.php"); ?>
                            <div class="card-box">
                                <div class="card-head">
                                    <header>Formulario de Inscripción</header>
                                </div>
                                <div class="card-body">
                                    <form name="example_advanced_form" id="example-advanced-form" action="inscripciones-formulario-guardar.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="idMatricula" value="<?= $datos['mat_id']; ?>">
                                        <input type="hidden" name="solicitud" value="<?= $id; ?>">
                                        <input type="hidden" name="idAcudiente" value="<?= $datos['mat_acudiente']; ?>">
                                        <input type="hidden" name="idPadre" value="<?= $datos['mat_padre']; ?>">
                                        <input type="hidden" name="idMadre" value="<?= $datos['mat_madre']; ?>">
                                        <input type="hidden" name="idInst" value="<?= $_GET['idInst'] ?>">
                                        <input type="hidden" name="fotoA" value="<?= $datos['mat_foto']; ?>">

                                        <h3>INFORMACIÓN PERSONAL DEL ASPIRANTE</h3>
									    <fieldset>
                                            <?php if (!empty($datos['mat_foto']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/fotos/' . $datos['mat_foto'])) {?>
                                                <div class="form-group row">
                                                    <div class="col-sm-4" style="margin: 0 auto 10px">
                                                        <div class="item">
                                                            <img src="<?=REDIRECT_ROUTE?>/admisiones/files/fotos/<?=$datos['mat_foto'];?>" width="150" height="150" />
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }?>
                                            <div class="form-group row">
                                                <div class="form-group col-md-4">
                                                    <label>Nombres <span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="nombre" value="<?= $datos['mat_nombres']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Primer Apellido <span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="primerApellidos" value="<?= $datos['mat_primer_apellido']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Segundo Apellido</label>
                                                    <input type="text" class="form-control" name="segundoApellidos" value="<?= $datos['mat_segundo_apellido']; ?>">
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Género <span style="color:red;">(*)</span> </label>
                                                    <select class="form-control select2" name="genero" required>
                                                        <option value="">Escoger</option>
                                                        <option value="127" <?php if ($datos['mat_genero'] == 127) echo "selected"; ?>>Femenino</option>
                                                        <option value="126" <?php if ($datos['mat_genero'] == 126) echo "selected"; ?>>Masculino</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-4">
                                                    <label>Tipo de documento <span style="color:red;">(*)</span></label>
                                                    <select class="form-control select2" name="tipoDoc" required>
                                                        <option value="">Escoger</option>
                                                        <option value="105" <?php if ($datos['mat_tipo_documento'] == 105) echo "selected"; ?>>Cédula de ciudadanía</option>
                                                        <option value="106" <?php if ($datos['mat_tipo_documento'] == 106) echo "selected"; ?>>NUIP</option>
                                                        <option value="107" <?php if ($datos['mat_tipo_documento'] == 107) echo "selected"; ?>>Tarjeta de identidad</option>
                                                        <option value="108" <?php if ($datos['mat_tipo_documento'] == 108) echo "selected"; ?>>Registro civil o NUIP</option>
                                                        <option value="109" <?php if ($datos['mat_tipo_documento'] == 109) echo "selected"; ?>>Cédula de Extranjería</option>
                                                        <option value="110" <?php if ($datos['mat_tipo_documento'] == 110) echo "selected"; ?>>Pasaporte</option>
                                                        <option value="139" <?php if ($datos['mat_tipo_documento'] == 139) echo "selected"; ?>>PEP</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Numero de documento <span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="numeroDoc" value="<?= $datos['mat_documento']; ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Lugar de expedición <span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="LugarExp" value="<?= $datos['mat_lugar_expedicion']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-6">
                                                    <label>Lugar de nacimiento <span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="LugarNacimiento" value="<?= $datos['mat_lugar_nacimiento']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Fecha de Nacimiento <span style="color:red;">(*)</span></label>
                                                    <input type="date" class="form-control" name="fechaNacimiento" value="<?= $datos['mat_fecha_nacimiento']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-4">
                                                    <label>Dirección <span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="direccion" value="<?= $datos['mat_direccion']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Barrio <span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="barrio" value="<?= $datos['mat_barrio']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Municipio <span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="municipio" value="<?= $datos['mat_ciudad_actual']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-6">
                                                    <label>Curso al que aspira <span style="color:red;">(*)</span></label>
                                                    <select class="form-control select2" name="curso">
                                                        <option value="">Escoger</option>
                                                        <?php
                                                        while ($datosGrado = $grados->fetch()) {
                                                        ?>
                                                            <option value="<?php echo $datosGrado['gra_id']; ?>" <?php if ($datos['mat_grado'] == $datosGrado['gra_id']) echo "selected"; ?>><?php echo $datosGrado['gra_nombre']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Razón por la que desea ingresar al plantel <span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="razonPlantel" value="<?= $datos['mat_razon_ingreso_plantel']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-6">
                                                    <label>Colegio donde cursó su último año</label>
                                                    <input type="text" class="form-control" name="coleAnoAnterior" value="<?= $datos['mat_institucion_procedencia']; ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Lugar</label>
                                                    <input type="text" class="form-control" name="lugar" value="<?= $datos['mat_lugar_colegio_procedencia']; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Motivo de retiro</label>
                                                <input type="text" class="form-control" name="motivo" value="<?= $datos['mat_motivo_retiro_anterior']; ?>">
                                            </div>
                                        </fieldset>

                                        <h3>INFORMACIÓN FAMILIAR</h3>
									    <fieldset>
                                            <h5 class="mb-4">INFORMACIÓN DEL PADRE</h5>
                                            <div class="form-group row">
                                                <div class="form-group col-md-5">
                                                    <label>Nombres y Apellidos del padre <span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="nombrePadre" value="<?= $datosPadre['uss_nombre']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Tipo de Documento <span style="color:red;">(*)</span> </label>
                                                    <select class="form-control select2" style="width: 100%;" name="tipoDocumentoPadre" required>
                                                        <option value="">Escoger</option>
                                                        <option value="105" <?php if ($datosPadre['uss_tipo_documento'] == 105) echo "selected"; ?>>Cédula de ciudadanía</option>
                                                        <option value="106" <?php if ($datosPadre['uss_tipo_documento'] == 106) echo "selected"; ?>>NUIP</option>
                                                        <option value="107" <?php if ($datosPadre['uss_tipo_documento'] == 107) echo "selected"; ?>>Tarjeta de identidad</option>
                                                        <option value="108" <?php if ($datosPadre['uss_tipo_documento'] == 108) echo "selected"; ?>>Registro civil o NUIP</option>
                                                        <option value="109" <?php if ($datosPadre['uss_tipo_documento'] == 109) echo "selected"; ?>>Cédula de Extranjería</option>
                                                        <option value="110" <?php if ($datosPadre['uss_tipo_documento'] == 110) echo "selected"; ?>>Pasaporte</option>
                                                        <option value="139" <?php if ($datosPadre['uss_tipo_documento'] == 139) echo "selected"; ?>>PEP</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Número de Documento<span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" value="<?= $datosPadre['uss_usuario']; ?>" name="documentoPadre">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-4">
                                                    <label>Religión <span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="religionPadre" value="<?= $datosPadre['uss_religion']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Teléfono</label>
                                                    <input type="text" class="form-control" name="telfonoPadre" value="<?= $datosPadre['uss_telefono']; ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Número celular<span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="celularPadre" value="<?= $datosPadre['uss_celular']; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-4">
                                                    <label>Dirección<span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="direccionPadre" value="<?= $datosPadre['uss_direccion']; ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Email<span style="color:red;">(*)</span></label>
                                                    <input type="email" class="form-control" name="emailPadre" value="<?= $datosPadre['uss_email']; ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Ocupación<span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="ocupacionPadre" value="<?= $datosPadre['uss_ocupacion']; ?>">
                                                </div>
                                            </div>
                                            <h5 class="mb-4">INFORMACIÓN DE LA MADRE</h5>
                                            <div class="form-group row">
                                                <div class="form-group col-md-5">
                                                    <label>Nombres y Apellidos de la Madre <span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="nombreMadre" value="<?= $datosMadre['uss_nombre']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Tipo de Documento <span style="color:red;">(*)</span> </label>
                                                    <select class="form-control select2" style="width: 100%;" name="tipoDocumentoMadre" required>
                                                        <option value="">Escoger</option>
                                                        <option value="105" <?php if ($datosMadre['uss_tipo_documento'] == 105) echo "selected"; ?>>Cédula de ciudadanía</option>
                                                        <option value="106" <?php if ($datosMadre['uss_tipo_documento'] == 106) echo "selected"; ?>>NUIP</option>
                                                        <option value="107" <?php if ($datosMadre['uss_tipo_documento'] == 107) echo "selected"; ?>>Tarjeta de identidad</option>
                                                        <option value="108" <?php if ($datosMadre['uss_tipo_documento'] == 108) echo "selected"; ?>>Registro civil o NUIP</option>
                                                        <option value="109" <?php if ($datosMadre['uss_tipo_documento'] == 109) echo "selected"; ?>>Cédula de Extranjería</option>
                                                        <option value="110" <?php if ($datosMadre['uss_tipo_documento'] == 110) echo "selected"; ?>>Pasaporte</option>
                                                        <option value="139" <?php if ($datosMadre['uss_tipo_documento'] == 139) echo "selected"; ?>>PEP</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Número de Documento<span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" value="<?= $datosMadre['uss_usuario']; ?>" name="documentoMadre">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-4">
                                                    <label>Religión <span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="religionMadre" value="<?= $datosMadre['uss_religion']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Teléfono</label>
                                                    <input type="text" class="form-control" value="<?= $datosMadre['uss_telefono']; ?>" name="telfonoMadre">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Número celular<span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="celularMadre" value="<?= $datosMadre['uss_celular']; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-4">
                                                    <label>Dirección<span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="direccionMadre" value="<?= $datosMadre['uss_direccion']; ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Email<span style="color:red;">(*)</span></label>
                                                    <input type="email" class="form-control" name="emailMadre" value="<?= $datosMadre['uss_email']; ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Ocupación<span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="ocupacionMadre" value="<?= $datosMadre['uss_ocupacion']; ?>">
                                                </div>
                                            </div>
                                            <h5 class="mb-4">INFORMACIÓN DEL ACUDIENTE <span style="color:red;">(El acudiente es quien se reportará en la DIAN en la información exógena)</span></h5>
                                            <div class="form-group row">
                                                <div class="form-group col-md-3">
                                                    <label>Documento<span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="documentoAcudiente" value="<?= $datos['uss_usuario']; ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Nombres y Apellidos del Acudiente <span style="color:red;">(*) Completos</span> </label>
                                                    <input type="text" class="form-control" name="nombreAcudiente" value="<?= $datos['uss_nombre']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Parentesco<span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="parentesco" value="<?= $datos['uss_parentezco']; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-4">
                                                    <label>Religión <span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="religionAcudiente" value="<?= $datos['uss_religion']; ?>" required>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Teléfono</label>
                                                    <input type="text" class="form-control" name="telfonoAcudiente" value="<?= $datos['uss_telefono']; ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Número celular<span style="color:red;">(*)</span> </label>
                                                    <input type="text" class="form-control" name="celularAcudiente" value="<?= $datos['uss_celular']; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-6">
                                                    <label>Dirección<span style="color:red;">(*)</span></label>
                                                    <input type="text" class="form-control" name="direccionAcudiente" value="<?= $datos['uss_direccion']; ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Email<span style="color:red;">(*)</span></label>
                                                    <input type="email" class="form-control" name="emailAcudiente" value="<?= $datos['uss_email']; ?>">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <h3>DOCUMENTACIÓN DEL ASPIRANTE</h3>
									    <fieldset>
                                            <div class="p-3 mb-2 bg-secondary text-white">Debe cargar solo un archivo por cada campo. Si necesita cargar más de un archivo en un solo campo por favor comprimalos(.ZIP, .RAR) y los carga.</div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-6">
                                                    <label>1. Foto <span class="text-primary">(En formato .jpg, .png, .jpeg)</span> </label>
                                                    <input type="file" class="form-control" name="foto">
                                                    <?php if (!empty($datos['mat_foto']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/fotos/' . $datos['mat_foto'])) { ?>
                                                        <p><a href="<?=REDIRECT_ROUTE?>/admisiones/files/fotos/<?= $datos['mat_foto']; ?>" target="_blank" class="link"><?= $datos['mat_foto']; ?></a></p>
                                                    <?php } ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>2. Paz y salvo a la fecha del colegio de procedencia</label>
                                                    <input type="file" class="form-control" name="pazysalvo">
                                                    <input type="hidden" name="pazysalvoA" value="<?= $datosDocumentos['matd_pazysalvo']; ?>">
                                                    <?php if (!empty($datosDocumentos['matd_pazysalvo']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/otros/' . $datosDocumentos['matd_pazysalvo'])) { ?>
                                                        <p><a href="<?=REDIRECT_ROUTE?>/admisiones/files/otros/<?= $datosDocumentos['matd_pazysalvo']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_pazysalvo']; ?></a></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-6">
                                                    <label>3. Ficha acumulativa u observador del alumno </label>
                                                    <input type="file" class="form-control" name="observador">
                                                    <input type="hidden" name="observadorA" value="<?= $datosDocumentos['matd_observador']; ?>">
                                                    <?php if (!empty($datosDocumentos['matd_observador']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/otros/' . $datosDocumentos['matd_observador'])) { ?>
                                                        <p><a href="<?=REDIRECT_ROUTE?>/admisiones/files/otros/<?= $datosDocumentos['matd_observador']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_observador']; ?></a></p>
                                                    <?php } ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>4. Fotocopia de la EPS</label>
                                                    <input type="file" class="form-control" name="eps">
                                                    <input type="hidden" name="epsA" value="<?= $datosDocumentos['matd_eps']; ?>">
                                                    <?php if (!empty($datosDocumentos['matd_eps']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/otros/' . $datosDocumentos['matd_eps'])) { ?>
                                                        <p><a href="<?=REDIRECT_ROUTE?>/admisiones/files/otros/<?= $datosDocumentos['matd_eps']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_eps']; ?></a></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-6">
                                                    <label>5. Hoja de recomendación </label>
                                                    <input type="file" class="form-control" name="recomendacion">
                                                    <input type="hidden" name="recomendacionA" value="<?= $datosDocumentos['matd_recomendacion']; ?>">
                                                    <?php if (!empty($datosDocumentos['matd_recomendacion']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/otros/' . $datosDocumentos['matd_recomendacion'])) { ?>
                                                        <p><a href="<?=REDIRECT_ROUTE?>/admisiones/files/otros/<?= $datosDocumentos['matd_recomendacion']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_recomendacion']; ?></a></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-6">
                                                    <label>6. Vacunas </label>
                                                    <input type="file" class="form-control" name="vacunas">
                                                    <input type="hidden" name="vacunasA" value="<?= $datosDocumentos['matd_vacunas']; ?>">
                                                    <?php if (!empty($datosDocumentos['matd_vacunas']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/otros/' . $datosDocumentos['matd_vacunas'])) { ?>
                                                        <p><a href="<?=REDIRECT_ROUTE?>/admisiones/files/otros/<?= $datosDocumentos['matd_vacunas']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_vacunas']; ?></a></p>
                                                    <?php } ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>7. Boletines actuales </label>
                                                    <input type="file" class="form-control" name="boletines">
                                                    <input type="hidden" name="boletinesA" value="<?= $datosDocumentos['matd_boletines_actuales']; ?>">
                                                    <?php if (!empty($datosDocumentos['matd_boletines_actuales']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/otros/' . $datosDocumentos['matd_boletines_actuales'])) { ?>
                                                        <p><a href="<?=REDIRECT_ROUTE?>/admisiones/files/otros/<?= $datosDocumentos['matd_boletines_actuales']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_boletines_actuales']; ?></a></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-group col-md-6">
                                                    <label>8. Documento de identidad (Ambas caras) </label>
                                                    <input type="file" class="form-control" name="documentoIde">
                                                    <input type="hidden" name="documentoIdeA" value="<?= $datosDocumentos['matd_documento_identidad']; ?>">
                                                    <?php if (!empty($datosDocumentos['matd_documento_identidad']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/otros/' . $datosDocumentos['matd_documento_identidad'])) { ?>
                                                        <p><a href="<?=REDIRECT_ROUTE?>/admisiones/files/otros/<?= $datosDocumentos['matd_documento_identidad']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_documento_identidad']; ?></a></p>
                                                    <?php } ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>9. Certificado </label>
                                                    <input type="file" class="form-control" name="certificado">
                                                    <input type="hidden" name="certificadoA" value="<?= $datosDocumentos['matd_certificados']; ?>">
                                                    <?php if (!empty($datosDocumentos['matd_certificados']) and file_exists(ROOT_PATH.'/main-app/admisiones/files/otros/' . $datosDocumentos['matd_certificados'])) { ?>
                                                        <p><a href="<?=REDIRECT_ROUTE?>/admisiones/files/otros/<?= $datosDocumentos['matd_certificados']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_certificados']; ?></a></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <hr class="my-4">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="gridCheck" required>
                                                    <label class="form-check-label" for="gridCheck">
                                                        Estoy suficientemente informado del Manual de Convivencia y del Sistema Institucional de Evaluación que rigen en el <b><?= strtoupper($informacion_inst['info_nombre']) ?></b>, según aparecen en la página web y en caso de ser aceptado me comprometo a acatarlos y cumplirlos fiel y cabalmente.
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="p-2 mt-4 mb-4 bg-warning text-dark" style="text-align: center;">
                                                <p style="font-size: 20px; font-weight: bold;">
                                                    Tenga en cuenta que debe tener completa toda la documentación cargada en la plataforma para que su solicitud continúe el proceso de admisión y sea agendada la respectiva entrevista y examen de admisión según sea el caso.
                                                </p>
                                            </div>
                                        </fieldset>

                                        <?php $botones = new botonesGuardar("inscripciones.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
					<!-- <div id="wizard" style="display: none;"></div> -->
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
	<script src="../../config-general/assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <!-- steps -->
    <script src="../../config-general/assets/plugins/steps/jquery.steps.js" ></script>
    <script src="../../config-general/assets/js/pages/steps/steps-data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>

	<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- end js include path -->
</body>

</html>