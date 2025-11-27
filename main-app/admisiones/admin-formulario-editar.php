<?php
session_start();
if ($_SESSION["id"] == "") {
    header("Location:index.php?sesion=0");
    exit();
}
?>
<?php
include("bd-conexion.php");
include("php-funciones.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");

$id="";
if(!empty($_GET["id"])){ $id=base64_decode($_GET["id"]);}

if (md5($id) != $_GET['token']) {
    redireccionMal('respuestas-usuario.php', 4);
}

$estQuery = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_acudiente AND uss.institucion= :idInstitucion AND uss.year= :year
WHERE mat.mat_solicitud_inscripcion = :id AND mat.institucion= :idInstitucion AND mat.year= :year";
$est = $pdoI->prepare($estQuery);
$est->bindParam(':id', $id, PDO::PARAM_INT);
$est->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$est->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
$est->execute();
$num = $est->rowCount();
$datos = $est->fetch();

//Documentos
$datosDocumentos = Inscripciones::traerDocumentos($pdoI, $config, $datos['mat_id']);

//Padre
$padreQuery = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id = :id AND institucion= :idInstitucion AND year= :year";
$padre = $pdoI->prepare($padreQuery);
$padre->bindParam(':id', $datos['mat_padre'], PDO::PARAM_STR);
$padre->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$padre->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
$padre->execute();
$datosPadre = $padre->fetch();

//Madre
$madreQuery = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id = :id AND institucion= :idInstitucion AND year= :year";
$madre = $pdoI->prepare($madreQuery);
$madre->bindParam(':id', $datos['mat_madre'], PDO::PARAM_STR);
$madre->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$madre->bindParam(':year', $config['conf_agno'], PDO::PARAM_STR);
$madre->execute();
$datosMadre = $madre->fetch();

//Aspirantes
$aspQuery = "SELECT * FROM aspirantes WHERE asp_id = :id";
$asp = $pdo->prepare($aspQuery);
$asp->bindParam(':id', $id, PDO::PARAM_INT);
$asp->execute();
$datosAsp = $asp->fetch();
?>

<!DOCTYPE html>

<html lang="en">



<head>
    <?php include(ROOT_PATH."/config-general/analytics/instituciones.php");?>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Formulario de inscripción</title>

    <script src="https://cdn.tiny.cloud/1/h8im6efse6a7s9pty8zq9ez1yba05c4w41ke21a0n2vpjj8o/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

<script>
  tinymce.init({
    selector: 'textarea#editor',
    menubar: false
  });
</script>

</head>



<body>

    <div class="container mb-4">

        <?php include("menu.php"); ?>

        <?php include("alertas.php"); ?>
        <?php include("../../config-general/mensajes-informativos.php"); ?>

        <form action="admin-formulario-actualizar.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="idMatricula" value="<?= $datos['mat_id']; ?>">
            <input type="hidden" name="solicitud" value="<?= $id; ?>">
            <input type="hidden" name="emailAcudiente" value="<?= $datos['uss_email']; ?>">
            <input type="hidden" name="nombreAcudiente" value="<?= $datosAsp['asp_nombre_acudiente']; ?>">
            <input type="hidden" name="documentoAspirante" value="<?= $datosAsp['asp_documento']; ?>">
            <input type="hidden" name="idPadre" value="<?= $datos['mat_padre']; ?>">
            <input type="hidden" name="idMadre" value="<?= $datos['mat_madre']; ?>">
            <input type="hidden" name="idInst" value="<?= $_REQUEST['idInst']; ?>">

            <input type="hidden" name="fotoA" value="<?= $datos['mat_foto']; ?>">

            <div class="form-row">



                <div class="form-group col-md-4">

                    <label>Nombres </label>

                    <input type="text" class="form-control" name="nombre" value="<?= $datos['mat_nombres']; ?>" disabled>

                </div>



                <div class="form-group col-md-3">

                    <label>Primer Apellido </label>

                    <input type="text" class="form-control" name="primerApellidos" value="<?= $datos['mat_primer_apellido']; ?>" disabled>

                </div>



                <div class="form-group col-md-3">

                    <label>Segundo Apellido</label>

                    <input type="text" class="form-control" name="segundoApellidos" value="<?= $datos['mat_segundo_apellido']; ?>" disabled>

                </div>





                <div class="form-group col-md-2">

                    <label>Género </label>

                    <select class="form-control" name="genero" disabled>



                        <option value="">Escoger</option>

                        <option value="127" <?php if ($datos['mat_genero'] == 127) echo "selected"; ?>>Femenino</option>

                        <option value="126" <?php if ($datos['mat_genero'] == 126) echo "selected"; ?>>Masculino</option>



                    </select>

                </div>



            </div>







            <div class="form-row">


                <div class="form-group col-md-4">

                    <label>Tipo de documento</label>

                    <select class="form-control" name="tipoDoc" disabled>

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

                    <label>Numero de documento </label>

                    <input type="text" class="form-control" name="numeroDoc" value="<?= $datos['mat_documento']; ?>" disabled>

                </div>



                <div class="form-group col-md-4">

                    <label>Lugar de expedición </label>

                    <input type="text" class="form-control" name="LugarExp" value="<?= $datos['mat_lugar_expedicion']; ?>" disabled required>

                </div>





            </div>


            <hr class="my-4">


            <div class="form-row">





                <div class="form-group col-md-6">

                    <label>Estado de solicitud <span style="color:red;">(*)</span></label>

                    <select class="form-control" name="estadoSolicitud" required>

                        <option value="">Escoger</option>

                        <?php foreach ($estadosSolicitud as $key => $value) {?>
                            <option value="<?=$key;?>" <?php if ($datosAsp['asp_estado_solicitud'] == $key) echo "selected"; ?>><?=$value;?></option>
                        <?php } ?>

                    </select>

                </div>



                <div class="form-group col-md-6">

                    <label>Enviar correo al guardar los cambios </label>

                    <select class="form-control" name="enviarCorreo" required>



                        <option value="">Escoger</option>

                        <option value="1">SI</option>

                        <option value="2" selected>NO</option>



                    </select>

                    <p class="text-info">Si escoge que sí, se enviará un correo al acudiente con la observación y el estado de la solicitud al guardar los cambios.</p>
                    <p class="text-info">El mensaje se enviará al correo <b><?= $datos['uss_email']; ?></b>.</p>

                </div>





            </div>


            <div class="form-group">
                <label>Observación</label>
                <textarea class="form-control" name="observacion" rows="10" id="editor"><?= $datosAsp['asp_observacion']; ?></textarea>
            </div>


            <h3 class="mb-4" style="text-align: center;">ARCHIVOS ADJUNTOS</h3>

            <div class="p-3 mb-2 bg-secondary text-white">Debe cargar solo un archivo por cada campo. Si necesita cargar más de un archivo en un solo campo por favor comprimalos(.ZIP, .RAR) y los carga.</div>

            <div class="form-row">

                <div class="form-group col-md-6">

                    <label>Archivo 1 </label>

                    <input type="file" class="form-control" name="archivo1">
                    <input type="hidden" name="archivo1A" value="<?= $datosAsp['asp_archivo1']; ?>">

                    <?php if ($datosAsp['asp_archivo1'] != "" and file_exists('files/adjuntos/' . $datosAsp['asp_archivo1'])) { ?>
                        <p><a href="files/adjuntos/<?= $datosAsp['asp_archivo1']; ?>" target="_blank" class="link"><?= $datosAsp['asp_archivo1']; ?></a></p>

                        <p><a href="admin-adjuntos-eliminar.php?solicitud=<?= $_GET["id"]; ?>&adj=<?=base64_encode(1)?>&file=<?= base64_encode($datosAsp['asp_archivo1']); ?>&idInst=<?=$_REQUEST['idInst']?>" onclick="if(!confirm('Desea eliminar este adjunto?')) {return false;}" style="text-decoration: underline; color:red;">Eliminar adjunto</a></p>
                    <?php } ?>


                </div>


                <div class="form-group col-md-6">

                    <label>Archivo 2</label>

                    <input type="file" class="form-control" name="archivo2">
                    <input type="hidden" name="archivo2A" value="<?= $datosAsp['asp_archivo2']; ?>">

                    <?php if ($datosAsp['asp_archivo2'] != "" and file_exists('files/adjuntos/' . $datosAsp['asp_archivo2'])) { ?>
                        <p><a href="files/adjuntos/<?= $datosAsp['asp_archivo2']; ?>" target="_blank" class="link"><?= $datosAsp['asp_archivo2']; ?></a></p>

                        <p><a href="admin-adjuntos-eliminar.php?solicitud=<?= $_GET["id"]; ?>&adj=<?=base64_encode(2)?>&file=<?= base64_encode($datosAsp['asp_archivo2']); ?>&idInst=<?=$_REQUEST['idInst']?>" onclick="if(!confirm('Desea eliminar este adjunto?')) {return false;}" style="text-decoration: underline; color:red;">Eliminar adjunto</a></p>
                    <?php } ?>

                </div>


            </div>





            <button type="submit" class="btn btn-success btn-lg btn-block">Guardar cambios</button>

        </form>

    </div>



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

</body>



</html>