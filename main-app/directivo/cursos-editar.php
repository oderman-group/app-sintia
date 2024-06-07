<?php
include("session.php");
$idPaginaInterna = 'DT0064';
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php");
require_once("../class/servicios/CargaServicios.php");
require_once("../class/servicios/MatriculaServicios.php");
require_once("../compartido/includes/includeSelectSearch.php");
require_once(ROOT_PATH . "/main-app/class/Grupos.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

try {
    $resultadoCurso = GradoServicios::consultarCurso(base64_decode($_GET["id"]));
    $resultadoCargaCurso = CargaServicios::cantidadCursos(base64_decode($_GET["id"]));
    $hidden = $resultadoCurso['gra_tipo'] == GRADO_INDIVIDUAL ? "" : "hidden";
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
}
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
<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css" />
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
                            <div class="page-title">Editar Cursos</div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="cursos.php" onClick="deseaRegresar(this)"><?= $frases[5][$datosUsuarioActual['uss_idioma']]; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Editar Cursos</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">





                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">

                        <?php include("../../config-general/mensajes-informativos.php"); ?>
                        <div class="col-md-12">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">

                                    <a class="nav-item nav-link show active" id="nav-informacion-tab" data-toggle="tab" href="#nav-informacion" role="tab" aria-controls="nav-informacion" aria-selected="true">
                                        <h5> <?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </h5>
                                    </a>
                                    <?php if (array_key_exists(10, $arregloModulos)) { ?>
                                        <a <?= $hidden ?> class="nav-item nav-link" onclick="habilitarInput()" id="nav-configuracion-tab" data-toggle="tab" href="#nav-configuracion" role="tab" aria-controls="nav-configuracion" aria-selected="false">
                                            <h5> Configuracion del curso </h5>
                                        </a>

                                        <a <?= $hidden ?> class="nav-item nav-link" id="nav-estudiantes-tab" data-toggle="tab" href="#nav-estudiantes" role="tab" aria-controls="nav-estudiantes" aria-selected="false">
                                            <h5>Estudiantes </h5>
                                        </a>
                                    <?php } ?>
                                </div>
                            </nav>
                            <form id="miFormulario" name="formularioGuardar" action="cursos-actualizar.php" method="post" enctype="multipart/form-data">
                                <div class="tab-content" id="nav-tabContent">

                                    <div class="tab-pane fade show active" id="nav-informacion" role="tabpanel" aria-labelledby="nav-informacion-tab">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <input type="hidden" id="id_curso" name="id_curso" value="<?= base64_decode($_GET["id"]) ?>">

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Codigo</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="codigoC" readonly class="form-control" value="<?= $resultadoCurso["gra_codigo"]; ?>" <?= $disabledPermiso; ?>>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Nombre Curso</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="nombreC" name="nombreC" class="form-control" required value="<?= $resultadoCurso["gra_nombre"]; ?>" <?= $disabledPermiso; ?>>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Formato Boletin</label>
                                                    <div class="col-sm-2">
                                                        <select id="tipoBoletin" class="form-control  select2" name="formatoB" onchange="cambiarTipo()" required <?= $disabledPermiso; ?>>
                                                            <option value="">Seleccione una opción</option>
                                                            <?php
                                                            try {
                                                                $consultaBoletin = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".opciones_generales WHERE ogen_grupo=15");
                                                            } catch (Exception $e) {
                                                                include("../compartido/error-catch-to-report.php");
                                                            }
                                                            while ($datosBoletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH)) {
                                                            ?>
                                                                <option value="<?= $datosBoletin['ogen_id']; ?>" <?php if ($resultadoCurso["gra_formato_boletin"] == $datosBoletin['ogen_id']) {
                                                                                                                        echo 'selected';
                                                                                                                    } ?>><?= $datosBoletin['ogen_nombre']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <button type="button" titlee="Ver formato del boletin" class="btn btn-sm" data-toggle="popover"><i class="fa fa-eye"></i></button>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('[data-toggle="popover"]').popover({
                                                                html: true, // Habilitar contenido HTML
                                                                content: function() {
                                                                    valor = document.getElementById("tipoBoletin");
                                                                    return '<div id="myPopover" class="popover-content"><label id="lbl_tipo">Formato tipo ' + valor.value + '</label>' +
                                                                        '<img id="img-boletin" src="../files/images/boletines/tipo' + valor.value + '.png" class="w-100" />' +
                                                                        '</div>';
                                                                }
                                                            });
                                                        });

                                                        function cambiarTipo() {
                                                            var imagen_boletin = document.getElementById('img-boletin');
                                                            if (imagen_boletin) {
                                                                var valor = document.getElementById("tipoBoletin");
                                                                var lbl_tipo = document.getElementById('lbl_tipo');
                                                                imagen_boletin.src = "../files/images/boletines/tipo" + valor.value + ".png";
                                                                lbl_tipo.textContent = 'Formato tipo ' + valor.value;
                                                            }
                                                        }
                                                    </script>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Nota Minima</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="notaMin" class="form-control" value="<?= $resultadoCurso["gra_nota_minima"]; ?>" <?= $disabledPermiso; ?>>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Periodos</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="periodosC" class="form-control" value="<?= $resultadoCurso["gra_periodos"]; ?>" <?= $disabledPermiso; ?>>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Valor Matricula</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="valorM" class="form-control" value="<?= $resultadoCurso["gra_valor_matricula"]; ?>" <?= $disabledPermiso; ?>>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Valor Pension</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="valorP" class="form-control" value="<?= $resultadoCurso["gra_valor_pension"]; ?>" <?= $disabledPermiso; ?>>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Curso Siguiente</label>
                                                    <div class="col-sm-6">
                                                        <?php
                                                        $opcionesConsulta = Grados::listarGrados(1);
                                                        ?>
                                                        <select class="form-control  select2" name="graSiguiente" <?= $disabledPermiso; ?>>
                                                            <option value="">Seleccione una opción</option>
                                                            <?php
                                                            while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                                                                $select = '';
                                                                if ($resultadoCurso["gra_grado_siguiente"] == $opcionesDatos['gra_id']) {
                                                                    $select = 'selected';
                                                                }
                                                            ?>
                                                                <option value="<?= $opcionesDatos['gra_id']; ?>" <?= $select; ?>><?= strtoupper($opcionesDatos['gra_nombre']); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Curso Anterior</label>
                                                    <div class="col-sm-6">
                                                        <?php
                                                        $opcionesConsulta = Grados::listarGrados(1);
                                                        ?>
                                                        <select class="form-control  select2" name="graAnterior" <?= $disabledPermiso; ?>>
                                                            <option value="">Seleccione una opción</option>
                                                            <?php
                                                            while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                                                                $select = '';
                                                                if ($resultadoCurso["gra_grado_anterior"] == $opcionesDatos['gra_id']) {
                                                                    $select = 'selected';
                                                                }
                                                            ?>
                                                                <option value="<?= $opcionesDatos['gra_id']; ?>" <?= $select; ?>><?= strtoupper($opcionesDatos['gra_nombre']); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Nivel Educativo</label>
                                                    <div class="col-sm-6">
                                                        <select class="form-control  select2" name="nivel" <?= $disabledPermiso; ?>>
                                                            <option value="">Seleccione una opción</option>
                                                            <option value="1" <?php if ($resultadoCurso['gra_nivel'] == 1) {
                                                                                    echo 'selected';
                                                                                } ?>>Educación Precolar</option>
                                                            <option value="2" <?php if ($resultadoCurso['gra_nivel'] == 2) {
                                                                                    echo 'selected';
                                                                                } ?>>Educación Basica Primaria</option>
                                                            <option value="3" <?php if ($resultadoCurso['gra_nivel'] == 3) {
                                                                                    echo 'selected';
                                                                                } ?>>Educación Basica Secundaria</option>
                                                            <option value="4" <?php if ($resultadoCurso['gra_nivel'] == 4) {
                                                                                    echo 'selected';
                                                                                } ?>>Educación Media</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <?php if ($datosUsuarioActual['uss_tipo'] == 1) { ?>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">Estado</label>
                                                        <div class="col-sm-2">
                                                            <select class="form-control  select2" name="estado" <?= $disabledPermiso; ?>>
                                                                <option value="">Seleccione una opción</option>
                                                                <option value="1" <?php if ($resultadoCurso['gra_estado'] == 1) {
                                                                                        echo 'selected';
                                                                                    } ?>>Activo</option>
                                                                <option value="0" <?php if ($resultadoCurso['gra_estado'] == 0) {
                                                                                        echo 'selected';
                                                                                    } ?>>Inactivo</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                if (array_key_exists(10, $arregloModulos)) {
                                                ?>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">Tipo de grado</label>
                                                        <div class="col-sm-2">
                                                            <?php
                                                            if ($resultadoCargaCurso["cargas_curso"] < 1) {
                                                            ?>
                                                                <select class="form-control  select2" name="tipoG" id="tipoG" onchange="mostrarEstudiantes(this.value)">
                                                                    <option value=<?= GRADO_GRUPAL; ?> <?php if ($resultadoCurso['gra_tipo'] == GRADO_GRUPAL) {
                                                                                                            echo 'selected';
                                                                                                        } ?>>Grupal</option>
                                                                    <option value=<?= GRADO_INDIVIDUAL; ?> <?php if ($resultadoCurso['gra_tipo'] == GRADO_INDIVIDUAL) {
                                                                                                                echo 'selected';
                                                                                                            } ?>>Individual</option>
                                                                </select>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <select class="form-control  select2" name="tipoG" id="tipoG" disabled>
                                                                    <?php
                                                                    if ($resultadoCurso['gra_tipo'] == GRADO_GRUPAL) {
                                                                        echo '<option value="' . GRADO_GRUPAL . '" selected>Grupal</option>';
                                                                    } elseif ($resultadoCurso['gra_tipo'] == GRADO_INDIVIDUAL) {
                                                                        echo '<option value="' . GRADO_INDIVIDUAL . '" selected>Individual</option>';
                                                                    } else {
                                                                        echo ' ';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            <?php } ?>
                                                        </div>
                                                    </div>


                                                <?php
                                                }
                                                ?>


                                            </div>
                                        </div>
                                    </div>

                                    <?php if (array_key_exists(10, $arregloModulos)) { ?>
                                        <div <?= $hidden ?> class="tab-pane fade" id="nav-configuracion" role="tabpanel" aria-labelledby="nav-configuracion-tab">

                                            <div class="panel">
                                                <div class="panel-body">

                                                    <div class="form-group row">
                                                        <label class="col-sm-10 control-label"></label>
                                                        <label class="col-sm-1 control-label">Vista Previa</label>
                                                        <div class="col-sm-1">
                                                            <a href="../guest/details.php?course=<?= $resultadoCurso["id_nuevo"] ?>" target="_blank">
                                                                <button type="button" titlee="Ver vista previsa" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></button>
                                                            </a>

                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-sm-2">
                                                        </div>

                                                        <div class="col-sm-8">
                                                            <?php
                                                            $urlImagen = $storage->getBucket()->object(FILE_CURSOS . $resultadoCurso["gra_cover_image"])->signedUrl(new DateTime('tomorrow'));
                                                            $existe = $storage->getBucket()->object(FILE_CURSOS . $resultadoCurso["gra_cover_image"])->exists();
                                                            if (!$existe) {
                                                                $urlImagen = "../files/cursos/curso.png";
                                                            }
                                                            ?>
                                                            <div id="gifCarga" class="gif-carga">
                                                                <img alt="Cargando...">
                                                            </div>
                                                            <img id="imagenSelect" class="cursor-mano" src="<?= $urlImagen ?>" alt="avatar" style="height: 400px;width: 100%;border:3px dashed;padding:10px;border-radius:40px / 30px">
                                                        </div>
                                                        <div class="col-sm-2">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">
                                                            Imagen
                                                            <button type="button" data-toggle="tooltip" data-placement="left" title="Genera una imagen con inteligencia artificial teniendo en cuenta el nombre del curso" onclick="generar('imagen')" class="btn btn-sm btn-info"><i class="fa-regular fa-image"></i></button>

                                                        </label>
                                                        <div class="col-sm-5">
                                                            <input hidden id="imagenCursoAi" name="imagenCursoAi" value="">
                                                            <input type="file" id="imagenCurso" name="imagenCurso" onChange="mostrarImagen('imagenCurso','imagenSelect'),limpiarImagenOpenAi()" accept=".png, .jpg, .jpeg" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">
                                                            Descripcion
                                                            <button type="button" data-toggle="tooltip" data-placement="left" title="Genera una descripcion con inteligencia artificial teniendo en cuenta el nombre del curso" onclick="generar('descripcion')" class="btn btn-sm btn-info"><i class="far fa-comment-alt"></i></button>

                                                        </label>
                                                        <div class="col-sm-10">
                                                            <textarea cols="80" id="editor1" name="descripcion" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?= $disabledPermiso; ?>>

                                                            <?= $resultadoCurso["gra_overall_description"]; ?>
                                                        </textarea>
                                                            <div id="gifCarga2" class="gif-carga">
                                                                <img alt="Cargando...">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">Contenido
                                                            <button type="button" data-toggle="tooltip" data-placement="left" title="Genera un contenido con inteligencia artificial teniendo en cuenta el nombre del curso" onclick="generar('contenido')" class="btn btn-sm btn-info"><i class="far fa-comment-alt"></i></button>
                                                        </label>
                                                        <div class="col-sm-10">
                                                            <textarea cols="80" id="editor2" name="contenido" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?= $disabledPermiso; ?>>
                                                        <?= $resultadoCurso["gra_course_content"]; ?>
                                                        </textarea>
                                                            <div id="gifCarga3" class="gif-carga">
                                                                <img alt="Cargando...">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">Precio</label>
                                                        <div class="col-sm-4">
                                                            <input type="number" name="precio" class="form-control" value="<?= $resultadoCurso["gra_price"]; ?>" <?= $disabledPermiso; ?>>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">Minimo de estudiantes</label>

                                                        <div class="input-group spinner col-sm-2">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-info" data-dir="dwn" type="button">
                                                                    <span class="fa fa-minus"></span>
                                                                </button>
                                                            </span>
                                                            <input type="number" id="minEstudiantes" name="minEstudiantes" disabled class="form-control text-center" value="<?= $resultadoCurso["gra_minimum_quota"]; ?>" <?= $disabledPermiso; ?> min="1">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-danger" data-dir="up" type="button">
                                                                    <span class="fa fa-plus"></span>
                                                                </button>
                                                            </span>
                                                        </div>

                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">Maximo de estudiantes</label>

                                                        <div class="input-group spinner col-sm-2">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-info" data-dir="dwn" type="button">
                                                                    <span class="fa fa-minus"></span>
                                                                </button>
                                                            </span>
                                                            <input type="number" id="maxEstudiantes" name="maxEstudiantes" disabled class="form-control text-center" value="<?= $resultadoCurso["gra_maximum_quota"]; ?>" <?= $disabledPermiso; ?> min="1">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-danger" data-dir="up" type="button">
                                                                    <span class="fa fa-plus"></span>
                                                                </button>
                                                            </span>
                                                        </div>

                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">Duracion en horas</label>
                                                        <div class="input-group spinner col-sm-2">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-info" data-dir="dwn" type="button">
                                                                    <span class="fa fa-minus"></span>
                                                                </button>
                                                            </span>
                                                            <input type="number" id="horas" disabled name="horas" class="form-control text-center" value="<?= !empty($resultadoCurso["gra_duration_hours"]) ? $resultadoCurso["gra_duration_hours"] : "1"; ?>" min="1" <?= $disabledPermiso; ?>>
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-danger" data-dir="up" type="button">
                                                                    <span class="fa fa-plus"></span>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">
                                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Los cursos que estén marcado con esta opción permitirán que cualquiera pueda participar del curso"><i class="fa fa-question"></i></button>
                                                            Auto Matricular
                                                        </label>
                                                        <div class="col-sm-10">
                                                            <label class="switchToggle">
                                                                <input name="autoenrollment" type="checkbox" <?php if ($resultadoCurso['gra_auto_enrollment'] == 1) {
                                                                                                                    echo "checked";
                                                                                                                } ?>>
                                                                <span class="slider green round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">
                                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Los cursos que estén marcados como no activos no podrán ser manipulados"><i class="fa fa-question"></i></button>

                                                            Activo

                                                        </label>
                                                        <div class="col-sm-10">
                                                            <label class="switchToggle">
                                                                <input name="activo" type="checkbox" <?php if ($resultadoCurso['gra_active'] == 1) {
                                                                                                            echo "checked";
                                                                                                        } ?>>
                                                                <span class="slider green round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div <?= $hidden ?> class="tab-pane fade" id="nav-estudiantes" role="tabpanel" aria-labelledby="nav-estudiantes-tab">

                                            <div class="panel">
                                                <div class="panel-body">

                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">Agregar un estudainte:</label>
                                                        <div class="col-sm-8">
                                                            <?php
                                                            $selectEctudiante2 = new includeSelectSearch("SeleccionEstudiante", "ajax-listar-estudiantes.php", "buscar estudiante", "agregarEstudainte");
                                                            $selectEctudiante2->generarComponente();
                                                            ?>
                                                        </div>
                                                        <?php
                                                        $cv = Grupos::traerGrupos($conexion, $config);
                                                        ?>
                                                        <div style="display: none;">
                                                            <select id="grupoBase" multiple class="form-control select2-multiple">
                                                                <?php while ($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)) {
                                                                    echo '<option value="' . $rv['gru_id'] . '" selected >' . $rv['gru_nombre'] . '</option>';
                                                                } ?>

                                                            </select>
                                                            <select id="estadoBase" multiple class="form-control select2-multiple">
                                                                <option value="<?= ESTADO_CURSO_ACTIVO ?>" selected><?= ESTADO_CURSO_ACTIVO ?></option>
                                                                <option value="<?= ESTADO_CURSO_INACTIVO ?>" selected><?= ESTADO_CURSO_INACTIVO ?></option>
                                                                <option value="<?= ESTADO_CURSO_PRE_INSCRITO ?>" selected><?= ESTADO_CURSO_PRE_INSCRITO ?></option>
                                                                <option value="<?= ESTADO_CURSO_NO_APROBADO ?>" selected><?= ESTADO_CURSO_NO_APROBADO ?></option>
                                                                <option value="<?= ESTADO_CURSO_APROBADO ?>" selected><?= ESTADO_CURSO_APROBADO ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <table class="table" id="estudaintesRegistrados">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Nombre</th>
                                                                <th scope="col" width="100px">Grupo</th>
                                                                <th scope="col" width="200px">Estado</th>
                                                                <th scope="col">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $parametros = [
                                                                'matcur_id_curso' => base64_decode($_GET["id"]) . '',
                                                                'matcur_id_institucion' => $config['conf_id_institucion'],
                                                                'matcur_years' => $config['conf_agno'],
                                                                'arreglo' => false
                                                            ];
                                                            $ListaEstudiantes = MediaTecnicaServicios::listarEstudiantes($parametros);
                                                            if (!is_null($ListaEstudiantes)) {
                                                                foreach ($ListaEstudiantes as $idEstudiante) {
                                                                    $matricualaEstudiante = MatriculaServicios::consultar($idEstudiante["matcur_id_matricula"]);
                                                                    $nombre = "";
                                                                    $arrayEnviar = array("tipo" => 1, "descripcionTipo" => "Para ocultar fila del registro.");
                                                                    $arrayDatos = json_encode($arrayEnviar);
                                                                    $objetoEnviar = htmlentities($arrayDatos);
                                                                    if (!is_null($matricualaEstudiante)) {
                                                                        $nombre = Estudiantes::NombreCompletoDelEstudiante($matricualaEstudiante);
                                                                    }
                                                            ?>
                                                                    <tr id="reg<?= $idEstudiante["matcur_id_matricula"]; ?>">
                                                                        <td><?= $idEstudiante["matcur_id_matricula"]; ?></td>
                                                                        <td><?= $nombre; ?></td>
                                                                        <td>
                                                                            <select id="grupo-<?= $idEstudiante["matcur_id_matricula"]; ?>" class="form-control" onchange="editarEstudainte('<?= $idEstudiante['matcur_id_matricula']; ?>')" <?= $disabledPermiso; ?>>
                                                                                <?php
                                                                                $cv = Grupos::traerGrupos($conexion, $config);
                                                                                while ($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)) {
                                                                                    if ($rv[0] == $idEstudiante['matcur_id_grupo'])
                                                                                        echo '<option value="' . $rv['gru_id'] . '" selected>' . $rv['gru_nombre'] . '</option>';
                                                                                    else
                                                                                        echo '<option value="' . $rv['gru_id'] . '">' . $rv['gru_nombre'] . '</option>';
                                                                                } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select id="estado-<?= $idEstudiante["matcur_id_matricula"]; ?>" class="form-control" onchange="editarEstudainte('<?= $idEstudiante['matcur_id_matricula']; ?>')" <?= $disabledPermiso; ?>>
                                                                                <option value="<?= ESTADO_CURSO_ACTIVO ?>" <?php echo $idEstudiante['matcur_estado'] == ESTADO_CURSO_ACTIVO ? 'selected' : ''; ?>>
                                                                                    <?= ESTADO_CURSO_ACTIVO ?></option>
                                                                                <option value="<?= ESTADO_CURSO_INACTIVO ?>" <?php echo $idEstudiante['matcur_estado'] == ESTADO_CURSO_INACTIVO ? 'selected' : ''; ?>><?= ESTADO_CURSO_INACTIVO ?></option>
                                                                                <option value="<?= ESTADO_CURSO_PRE_INSCRITO ?>" <?php echo $idEstudiante['matcur_estado'] == ESTADO_CURSO_PRE_INSCRITO ? 'selected' : ''; ?>><?= ESTADO_CURSO_PRE_INSCRITO ?></option>
                                                                                <option value="<?= ESTADO_CURSO_NO_APROBADO ?>" <?php echo $idEstudiante['matcur_estado'] == ESTADO_CURSO_NO_APROBADO ? 'selected' : ''; ?>><?= ESTADO_CURSO_NO_APROBADO ?></option>
                                                                                <option value="<?= ESTADO_CURSO_APROBADO ?>" <?php echo $idEstudiante['matcur_estado'] == ESTADO_CURSO_APROBADO ? 'selected' : ''; ?>><?= ESTADO_CURSO_APROBADO ?></option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" title="<?= $objetoEnviar; ?>" name="fetch-estudiante-mediatecnica.php?tipo=<?= base64_encode(ACCION_ELIMINAR) ?>&matricula=<?= base64_encode($idEstudiante["matcur_id_matricula"]) ?>&curso=<?= $_GET["id"] ?>" id="<?= $idEstudiante["matcur_id_matricula"]; ?>" onClick="deseaEliminar(this)" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                                                        </td>
                                                                    </tr>
                                                            <?php  }
                                                            } ?>

                                                        </tbody>
                                                    </table>

                                                    <div id="escogerEstudiantes">
                                                    </div>


                                                </div>
                                            </div>

                                        </div>
                                    <?php } ?>
                                    <?php  
                                        $botones = new botonesGuardar("estudiantes.php",Modulos::validarPermisoEdicion()); ?>
                                </div>
                                <!-- end js include path -->
                                <script src="../ckeditor/ckeditor.js"></script>
                                <script type="text/javascript">
                                    function limpiarImagenOpenAi() {
                                        document.getElementById("imagenCursoAi").value = '';
                                    }

                                    function generar(tipo) {
                                        var valor = document.getElementById('nombreC').value;
                                        if (valor) {

                                            switch (tipo) {
                                                case 'imagen':
                                                    generarImagen(valor);
                                                    break;
                                                case 'descripcion':
                                                    generarDescripcion(valor);
                                                    break;
                                                case 'contenido':
                                                    generarContenido(valor)
                                                    break;
                                            }

                                        } else {
                                            Swal.fire({
                                                position: "top-end",
                                                icon: "warning",
                                                title: 'Ingrese el nombre del Curso',
                                                showConfirmButton: false,
                                                timer: 150000
                                            });
                                        }

                                    }

                                    function generarImagen(valor) {
                                        document.getElementById("gifCarga").style.display = "block";
                                        imagenSelect = document.getElementById('imagenSelect');
                                        var data = {
                                            'metodo': '<?php echo TEXT_TO_IMAGE ?>',
                                            'valor': 'Crear una imagen llamativa para un curso que haga referencia al nombre de ' + valor
                                        };
                                        fetch('../openAi/metodos.php', {
                                                method: 'POST', // or 'PUT'
                                                body: JSON.stringify(data), // data can be `string` or {object}!
                                                headers: {
                                                    'Content-Type': 'application/json'
                                                },
                                            })
                                            .then((res) => res.json())
                                            .catch((error) => console.error('Error:', error))
                                            .then(
                                                function(response) {
                                                    {
                                                        document.getElementById("gifCarga").style.display = "none";
                                                        console.log(response);
                                                        if (response["ok"]) {
                                                            url = response["url"];
                                                            imagenSelect.src = url;
                                                            imagenSelect.classList.add('animate__animated', 'animate__fadeIn');
                                                            document.getElementById("imagenCursoAi").value = url;
                                                            document.getElementById("imagenCurso").value = '';
                                                        }
                                                    };
                                                });
                                    }

                                    function generarDescripcion(valor) {
                                        var buscar = "Creame un descripcion para  realizar un curso con el nombre de " + valor + " el resultado en formato html solamente la etiqueta body";
                                        var editor = CKEDITOR.instances.editor1;
                                        ejecutarFetch(buscar, "gifCarga2", editor);
                                    }

                                    function generarContenido(valor) {
                                        var buscar = "Creame una lista de contenido para  realizar un curso con el nombre de " + valor + " el resultado en formato html solamente el contenido la etiqueta body";
                                        var editor = CKEDITOR.instances.editor2;
                                        ejecutarFetch(buscar, "gifCarga3", editor);
                                    }

                                    function ejecutarFetch(valor, carangando, editor) {
                                        var data = {
                                            'metodo': '<?php echo TEXT_TO_TEXT ?>',
                                            'valor': valor
                                        };
                                        document.getElementById(carangando).style.display = "block";
                                        fetch('../openAi/metodos.php', {
                                                method: 'POST', // or 'PUT'
                                                body: JSON.stringify(data), // data can be `string` or {object}!
                                                headers: {
                                                    'Content-Type': 'application/json'
                                                },
                                            })
                                            .then((res) => res.json())
                                            .catch((error) => console.error('Error:', error))
                                            .then(
                                                function(response) {
                                                    {
                                                        document.getElementById(carangando).style.display = "none";
                                                        console.log(response);
                                                        if (response["ok"]) {
                                                            editor.setData(response["valor"]);

                                                        }


                                                    };
                                                });
                                    }
                                    CKEDITOR.replace('editor1');
                                    CKEDITOR.replace('editor2');

                                    function habilitarInput() {
                                        document.getElementById("minEstudiantes").disabled = false;
                                        document.getElementById("maxEstudiantes").disabled = false;
                                        document.getElementById("horas").disabled = false;
                                    }

                                    function mostrarEstudiantes(value) {
                                        const navInfo = document.getElementById("nav-informacion-tab");
                                        const navConfig = document.getElementById("nav-configuracion-tab");
                                        const navEstudiante = document.getElementById("nav-estudiantes-tab");
                                        const contentInfo = document.getElementById("nav-informacion");
                                        const contentConfigure = document.getElementById("nav-configuracion");
                                        const contentEstudiante = document.getElementById("nav-estudiantes");
                                        if (value == "<?= GRADO_INDIVIDUAL ?>") {
                                            navInfo.classList.remove('show', 'active');
                                            contentInfo.classList.remove('show', 'active');

                                            navConfig.hidden = false;
                                            navConfig.style.display = "";
                                            navConfig.classList.add('show', 'active');
                                            contentConfigure.hidden = false;
                                            contentConfigure.style.display = "";
                                            contentConfigure.classList.add('show', 'active');



                                            navEstudiante.hidden = false;
                                            navEstudiante.style.display = "";
                                            contentEstudiante.hidden = false;
                                            contentEstudiante.style.display = "";


                                            habilitarInput();


                                        } else {
                                            navConfig.style.display = "none";
                                            navConfig.classList.remove('show', 'active');
                                            contentConfigure.style.display = "none";
                                            contentConfigure.classList.remove('show', 'active');

                                            navEstudiante.style.display = "none";
                                            contentEstudiante.style.display = "none";


                                            navInfo.classList.add('show', 'active');
                                            contentInfo.classList.add('show', 'active');

                                            document.getElementById("minEstudiantes").disabled = true;
                                            document.getElementById("maxEstudiantes").disabled = true;
                                            document.getElementById("horas").disabled = true;



                                        }
                                    }



                                    function eliminarFila(button) {
                                        var fila = button.parentNode.parentNode; // Obtener la referencia a la fila actual                                                        
                                        var tabla = fila.parentNode; // Obtener la referencia a la tabla                                                        
                                        tabla.deleteRow(fila.rowIndex); // Eliminar la fila de la tabla
                                    }


                                    function agregarEstudainte(dato) {
                                        // se guarda en la base de datos                                        
                                        accionCursoMatricula(dato, '<?php echo ACCION_CREAR ?>');
                                    };

                                    function editarEstudainte(id) {
                                        var grupoSelect = document.getElementById("grupo-" + id);
                                        var estadoSelect = document.getElementById("estado-" + id);

                                        var dato = {};
                                        dato.id = id;
                                        dato.grupo = grupoSelect.value;
                                        dato.estado = estadoSelect.value;
                                        accionCursoMatricula(dato, '<?php echo ACCION_MODIFICAR ?>');
                                    };



                                    function accionCursoMatricula(dato, tipo, actualizar) {
                                        if (dato.grupo == undefined) {
                                            dato.grupo = "";
                                        }
                                        if (dato.estado == undefined) {
                                            dato.estado = "";
                                        }
                                        var data = {
                                            "matricula": dato.id,
                                            "curso": '<?php echo base64_decode($_GET["id"]) ?>',
                                            "tipo": tipo,
                                            "grupo": dato.grupo,
                                            "estado": dato.estado
                                        };
                                        var url = "fetch-estudiante-mediatecnica.php";

                                        console.log(JSON.stringify(data));

                                        fetch(url, {
                                                method: "POST", // or 'PUT'
                                                body: JSON.stringify(data), // data can be `string` or {object}!
                                                headers: {
                                                    "Content-Type": "application/json"
                                                },
                                            })
                                            .then((res) => res.json())
                                            .catch(function(error) {
                                                console.error("Error:", error)
                                            })
                                            .then(
                                                function(response) {
                                                    if (tipo == '<?php echo ACCION_CREAR ?>' && response["ok"]) {
                                                        crearFila(dato);
                                                    }
                                                    if (response["ok"]) {
                                                        $.toast({
                                                            heading: 'Acción realizada',
                                                            text: response["msg"],
                                                            position: 'bottom-right',
                                                            showHideTransition: 'slide',
                                                            loaderBg: '#26c281',
                                                            icon: 'success',
                                                            hideAfter: 5000,
                                                            stack: 6
                                                        });
                                                    } else {
                                                        $.toast({
                                                            heading: 'Acción no realizada',
                                                            text: response["msg"],
                                                            position: 'bottom-right',
                                                            showHideTransition: 'slide',
                                                            loaderBg: '#26c281',
                                                            icon: 'error',
                                                            hideAfter: 5000,
                                                            stack: 6
                                                        });
                                                    }


                                                });
                                    }

                                    function crearFila(seleccion) {
                                        if (seleccion) {
                                            var valor = seleccion.id; // El valor de la opción
                                            var etiqueta = seleccion.text; // La etiqueta de la opción
                                            // se insertan los valores en la tabla
                                            var tabla = document.getElementById("estudaintesRegistrados");
                                            var filas = tabla.getElementsByTagName("tr");

                                            // buscamos si ya se encuentra registrado                                                            
                                            encontro = false;
                                            for (var i = 0; i < filas.length; i++) { // Recorre las filas
                                                var celdas = filas[i].getElementsByTagName("td"); // Obtén todas las celdas de la fila actual

                                                for (var j = 0; j < celdas.length; j++) { // Recorre las celdas
                                                    if (celdas[j].innerHTML == valor) {
                                                        encontro = true; // cambio el estado de  a tru si encuentra un codigo igual
                                                    }
                                                }
                                            }
                                            if (!encontro) {
                                                // creamos el select del grupo
                                                var select1 = document.createElement("select");
                                                select1.id = "grupo-" + valor;
                                                select1.classList.add('form-control');
                                                var opciones = $('#grupoBase').select2('data');
                                                for (var i = 0; i < opciones.length; i++) {
                                                    var opcion = document.createElement("option");
                                                    opcion.text = opciones[i].text;
                                                    opcion.value = opciones[i].id;
                                                    select1.add(opcion);
                                                }
                                                select1.addEventListener('change', function() {
                                                    editarEstudainte(valor);
                                                });
                                                // creamos el select del estado
                                                var select2 = document.createElement("select");
                                                select2.id = "estado-" + valor;
                                                select2.classList.add('form-control');
                                                var opciones2 = $('#estadoBase').select2('data');
                                                for (var i = 0; i < opciones2.length; i++) {
                                                    var opcion = document.createElement("option");
                                                    opcion.text = opciones2[i].text;
                                                    opcion.value = opciones2[i].id;
                                                    select2.add(opcion);
                                                }
                                                select2.value = '<?php echo ESTADO_CURSO_PRE_INSCRITO ?>';
                                                select2.addEventListener('change', function() {
                                                    editarEstudainte(valor);
                                                });

                                                // Crea un elemento de botón
                                                var boton = document.createElement("button");
                                                boton.type = "button";
                                                boton.id = valor;
                                                boton.title = '{"tipo":1,"descripcionTipo":"Para ocultar fila del registro."}';
                                                boton.name = "fetch-estudiante-mediatecnica.php?" +
                                                    "tipo=<?php echo base64_encode(ACCION_ELIMINAR) ?>" +
                                                    "&matricula=" + btoa(valor) +
                                                    "&curso=<?php echo $_GET["id"] ?>";
                                                boton.classList.add('btn', 'btn-danger', 'btn-sm');
                                                var icon = document.createElement('i'); // se crea la icono
                                                icon.classList.add('fa', 'fa-trash');
                                                boton.appendChild(icon);
                                                // Agregar un evento al botón
                                                boton.addEventListener('click', function() {
                                                    var fila = document.getElementById("reg" + valor);
                                                    fila.classList.remove('animate__animated', 'animate__fadeInDown');
                                                    deseaEliminar(boton);
                                                });


                                                // Crear una nueva fila                                                                
                                                var fila = tabla.insertRow();
                                                // Agregar datos a las celdas
                                                fila.id = "reg" + valor;
                                                fila.classList.add('animate__animated', 'animate__fadeInDown');
                                                fila.insertCell(0).innerHTML = valor;
                                                fila.insertCell(1).innerHTML = etiqueta;
                                                fila.insertCell(2).appendChild(select1);
                                                fila.insertCell(3).appendChild(select2);
                                                fila.insertCell(4).appendChild(boton);

                                            } else {
                                                Swal.fire('Estudiante ya se encuentra registrado');
                                            }

                                        } else {
                                            Swal.fire('mo hay opcion selecionada');
                                        }
                                    }
                                </script>
                            </form>

                        </div>

                    </div>

                </div>

            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");
            ?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php"); ?>
    </div>
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
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

</html>