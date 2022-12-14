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

if (md5($_GET['id']) != $_GET['token']) {
    redireccionMal('respuestas-usuario.php', 4);
}

$estQuery = "SELECT * FROM academico_matriculas
LEFT JOIN usuarios ON uss_id=mat_acudiente
WHERE mat_solicitud_inscripcion = :id";
$est = $pdoI->prepare($estQuery);
$est->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$est->execute();
$num = $est->rowCount();
$datos = $est->fetch();

//Documentos
$documentosQuery = "SELECT * FROM academico_matriculas_documentos WHERE matd_matricula = :id";
$documentos = $pdoI->prepare($documentosQuery);
$documentos->bindParam(':id', $datos['mat_id'], PDO::PARAM_INT);
$documentos->execute();
$datosDocumentos = $documentos->fetch();

//Padre
$padreQuery = "SELECT * FROM usuarios WHERE uss_id = :id";
$padre = $pdoI->prepare($padreQuery);
$padre->bindParam(':id', $datos['mat_padre'], PDO::PARAM_INT);
$padre->execute();
$datosPadre = $padre->fetch();

//Madre
$madreQuery = "SELECT * FROM usuarios WHERE uss_id = :id";
$madre = $pdoI->prepare($madreQuery);
$madre->bindParam(':id', $datos['mat_madre'], PDO::PARAM_INT);
$madre->execute();
$datosMadre = $madre->fetch();

//Aspirantes
$aspQuery = "SELECT * FROM aspirantes WHERE asp_id = :id";
$asp = $pdo->prepare($aspQuery);
$asp->bindParam(':id', $_GET["id"], PDO::PARAM_INT);
$asp->execute();
$datosAsp = $asp->fetch();
?>

<!DOCTYPE html>

<html lang="en">



<head>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Formulario de inscripci??n</title>

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

        <form action="admin-formulario-actualizar.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="idMatricula" value="<?= $datos['mat_id']; ?>">
            <input type="hidden" name="solicitud" value="<?= $_GET["id"]; ?>">
            <input type="hidden" name="emailAcudiente" value="<?= $datos['uss_email']; ?>">
            <input type="hidden" name="idPadre" value="<?= $datos['mat_padre']; ?>">
            <input type="hidden" name="idMadre" value="<?= $datos['mat_madre']; ?>">

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

                    <label>G??nero </label>

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

                        <option value="105" <?php if ($datos['mat_tipo_documento'] == 105) echo "selected"; ?>>C??dula de ciudadan??a</option>

                        <option value="106" <?php if ($datos['mat_tipo_documento'] == 106) echo "selected"; ?>>NUIP</option>

                        <option value="107" <?php if ($datos['mat_tipo_documento'] == 107) echo "selected"; ?>>Tarjeta de identidad</option>

                        <option value="108" <?php if ($datos['mat_tipo_documento'] == 108) echo "selected"; ?>>Registro civil o NUIP</option>

                        <option value="109" <?php if ($datos['mat_tipo_documento'] == 109) echo "selected"; ?>>C??dula de Extranjer??a</option>

                        <option value="110" <?php if ($datos['mat_tipo_documento'] == 110) echo "selected"; ?>>Pasaporte</option>

                        <option value="139" <?php if ($datos['mat_tipo_documento'] == 139) echo "selected"; ?>>PEP</option>



                    </select>

                </div>



                <div class="form-group col-md-4">

                    <label>Numero de documento </label>

                    <input type="text" class="form-control" name="numeroDoc" value="<?= $datos['mat_documento']; ?>" disabled>

                </div>



                <div class="form-group col-md-4">

                    <label>Lugar de expedici??n </label>

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

                    <p class="text-info">Si escoge que s??, se enviar?? un correo al acudiente con la observaci??n y el estado de la solicitud al guardar los cambios.</p>
                    <p class="text-info">El mensaje se enviar?? al correo <b><?= $datos['uss_email']; ?></b>.</p>

                </div>





            </div>


            <div class="form-group">
                <label>Observaci??n</label>
                <textarea class="form-control" name="observacion" rows="10" id="editor"><?= $datosAsp['asp_observacion']; ?></textarea>
            </div>


            <h3 class="mb-4" style="text-align: center;">ARCHIVOS ADJUNTOS</h3>

            <div class="p-3 mb-2 bg-secondary text-white">Debe cargar solo un archivo por cada campo. Si necesita cargar m??s de un archivo en un solo campo por favor comprimalos(.ZIP, .RAR) y los carga.</div>

            <div class="form-row">

                <div class="form-group col-md-6">

                    <label>Archivo 1 </label>

                    <input type="file" class="form-control" name="archivo1">
                    <input type="hidden" name="archivo1A" value="<?= $datosAsp['asp_archivo1']; ?>">

                    <?php if ($datosAsp['asp_archivo1'] != "" and file_exists('files/adjuntos/' . $datosAsp['asp_archivo1'])) { ?>
                        <p><a href="files/adjuntos/<?= $datosAsp['asp_archivo1']; ?>" target="_blank" class="link"><?= $datosAsp['asp_archivo1']; ?></a></p>

                        <p><a href="admin-adjuntos-eliminar.php?solicitud=<?= $_GET["id"]; ?>&adj=1&file=<?= $datosAsp['asp_archivo1']; ?>" onclick="if(!confirm('Desea eliminar este adjunto?')) {return false;}" style="text-decoration: underline; color:red;">Eliminar adjunto</a></p>
                    <?php } ?>


                </div>


                <div class="form-group col-md-6">

                    <label>Archivo 2</label>

                    <input type="file" class="form-control" name="archivo2">
                    <input type="hidden" name="archivo2A" value="<?= $datosAsp['asp_archivo2']; ?>">

                    <?php if ($datosAsp['asp_archivo2'] != "" and file_exists('files/adjuntos/' . $datosAsp['asp_archivo2'])) { ?>
                        <p><a href="files/adjuntos/<?= $datosAsp['asp_archivo2']; ?>" target="_blank" class="link"><?= $datosAsp['asp_archivo2']; ?></a></p>

                        <p><a href="admin-adjuntos-eliminar.php?solicitud=<?= $_GET["id"]; ?>&adj=2&file=<?= $datosAsp['asp_archivo2']; ?>" onclick="if(!confirm('Desea eliminar este adjunto?')) {return false;}" style="text-decoration: underline; color:red;">Eliminar adjunto</a></p>
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