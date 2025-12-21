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

$parametrosObligatorios =["id"];

Utilidades::validarParametros($_GET,$parametrosObligatorios);

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

try {
    $resultadoCurso = GradoServicios::consultarCurso(base64_decode($_GET["id"]));
    $resultadoCargaCurso = CargaServicios::cantidadCursos(base64_decode($_GET["id"]));
    $hidden = $resultadoCurso['gra_tipo'] == GRADO_INDIVIDUAL ? "" : "hidden";
    
    // Validar si hay registros académicos en general (no por curso específico)
    $hayNotasRegistradas = Grados::hayRegistrosAcademicos($config);
    $disabledPeriodos = $hayNotasRegistradas ? 'readonly' : '';
    $disabledCamposNotas = $hayNotasRegistradas ? 'readonly' : '';
    $disabledSelectNotas = $hayNotasRegistradas ? 'disabled' : '';
    
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
                                    <a class="nav-item nav-link" id="nav-periodos-tab" data-toggle="tab" href="#nav-periodos" role="tab" aria-controls="nav-periodos" aria-selected="false">
                                        <h5>Periodos y Porcentajes</h5>
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
                                                    <label class="col-sm-2 control-label">
                                                        Codigo
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Identificador único del curso en el sistema. Este campo es de solo lectura y no puede ser modificado.">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="codigoC" readonly class="form-control" value="<?= $resultadoCurso["gra_codigo"]; ?>" <?= $disabledPermiso; ?>>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">
                                                        Nombre Curso
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Nombre completo del curso o grado académico. Este nombre aparecerá en boletines, reportes y listados del sistema.">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="nombreC" name="nombreC" class="form-control" required value="<?= $resultadoCurso["gra_nombre"]; ?>" <?= $disabledPermiso; ?> <?= $disabledCamposNotas; ?>>
                                                        <?php if ($hayNotasRegistradas) { ?>
                                                            <small class="form-text text-warning">
                                                                <i class="fa fa-exclamation-triangle"></i> No se puede modificar porque ya existen notas registradas.
                                                            </small>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">
                                                        Formato Boletin
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Define el formato visual del boletín de calificaciones para este curso. Cada formato tiene un diseño diferente en la presentación de las notas.">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
                                                    <div class="col-sm-10">
                                                        <div style="display: flex; gap: 10px; align-items: center;">
                                                            <select id="tipoBoletin" class="form-control select2" name="formatoB" onchange="cambiarTipoBoletin()" required <?= $disabledPermiso; ?> style="max-width: 300px;">
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
                                                            <button type="button" title="Ver formato boletín" class="btn btn-primary btn-sm" id="btnVistaPreviaBoletin" style="padding: 10px 20px;">
                                                                <i class="fa fa-eye"></i> Vista Previa
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">
                                                        Nota Minima
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Nota mínima requerida para aprobar el curso. Los estudiantes que obtengan una calificación inferior a este valor no aprobarán el curso.">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="notaMin" class="form-control" value="<?= $resultadoCurso["gra_nota_minima"]; ?>" <?= $disabledPermiso; ?> <?= $disabledCamposNotas; ?>>
                                                        <?php if ($hayNotasRegistradas) { ?>
                                                            <small class="form-text text-warning">
                                                                <i class="fa fa-exclamation-triangle"></i> No se puede modificar porque ya existen notas registradas.
                                                            </small>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">
                                                        Periodos
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Número de períodos académicos que tiene el curso durante el año escolar. Generalmente son 4 períodos, pero puede variar según la configuración institucional. <?= $hayNotasRegistradas ? 'Este campo está deshabilitado porque ya existen notas registradas para este curso.' : ''; ?>">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="periodosC" class="form-control" value="<?= $resultadoCurso["gra_periodos"]; ?>" <?= $disabledPermiso; ?> <?= $disabledPeriodos; ?>>
                                                        <?php if ($hayNotasRegistradas) { ?>
                                                            <small class="form-text text-warning">
                                                                <i class="fa fa-exclamation-triangle"></i> No se puede modificar porque ya existen notas registradas.
                                                            </small>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">
                                                        Valor Matricula
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Valor económico de la matrícula para este curso. Este valor se utilizará en procesos de facturación y reportes financieros.">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="valorM" class="form-control" value="<?= $resultadoCurso["gra_valor_matricula"]; ?>" <?= $disabledPermiso; ?>>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">
                                                        Valor Pension
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Valor económico de la pensión mensual para este curso. Este valor se utilizará en procesos de facturación y reportes financieros.">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="valorP" class="form-control" value="<?= $resultadoCurso["gra_valor_pension"]; ?>" <?= $disabledPermiso; ?>>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">
                                                        Curso Siguiente
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Define el curso al que los estudiantes avanzarán después de completar este curso. Útil para establecer la secuencia académica y procesos de promoción automática.">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
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
                                                    <label class="col-sm-2 control-label">
                                                        Curso Anterior
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Define el curso del cual provienen los estudiantes que ingresan a este curso. Útil para establecer la secuencia académica y procesos de promoción automática.">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
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
                                                    <label class="col-sm-2 control-label">
                                                        Nivel Educativo
                                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Nivel educativo al que pertenece el curso según la estructura del sistema educativo: Preescolar, Básica Primaria, Básica Secundaria o Media.">
                                                            <i class="fa fa-info-circle text-info"></i>
                                                        </button>
                                                    </label>
                                                    <div class="col-sm-6">
                                                        <select class="form-control  select2" name="nivel" <?= $disabledPermiso; ?> <?= $disabledSelectNotas; ?>>
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
                                                        <?php if ($hayNotasRegistradas) { ?>
                                                            <small class="form-text text-warning">
                                                                <i class="fa fa-exclamation-triangle"></i> No se puede modificar porque ya existen notas registradas.
                                                            </small>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <?php if ($datosUsuarioActual['uss_tipo'] == 1) { ?>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">
                                                            Estado
                                                            <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Estado del curso en el sistema. Activo: el curso está disponible para uso. Inactivo: el curso está deshabilitado y no se puede utilizar.">
                                                                <i class="fa fa-info-circle text-info"></i>
                                                            </button>
                                                        </label>
                                                        <div class="col-sm-2">
                                                            <select class="form-control  select2" name="estado" <?= $disabledPermiso; ?> <?= $disabledSelectNotas; ?>>
                                                                <option value="">Seleccione una opción</option>
                                                                <option value="1" <?php if ($resultadoCurso['gra_estado'] == 1) {
                                                                                        echo 'selected';
                                                                                    } ?>>Activo</option>
                                                                <option value="0" <?php if ($resultadoCurso['gra_estado'] == 0) {
                                                                                        echo 'selected';
                                                                                    } ?>>Inactivo</option>
                                                            </select>
                                                            <?php if ($hayNotasRegistradas) { ?>
                                                                <small class="form-text text-warning">
                                                                    <i class="fa fa-exclamation-triangle"></i> No se puede modificar porque ya existen notas registradas.
                                                                </small>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                if (array_key_exists(10, $arregloModulos)) {
                                                ?>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">
                                                            Tipo de grado
                                                            <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Grupal: curso tradicional con grupos de estudiantes. Individual: curso personalizado donde cada estudiante tiene su propio ritmo y configuración. Este campo solo se puede modificar si el curso no tiene cargas académicas asignadas.">
                                                                <i class="fa fa-info-circle text-info"></i>
                                                            </button>
                                                        </label>
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

                                    <!-- Tab Periodos y Porcentajes -->
                                    <div class="tab-pane fade" id="nav-periodos" role="tabpanel" aria-labelledby="nav-periodos-tab">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <?php if ($hayNotasRegistradas) { ?>
                                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        <h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Notas Registradas</h4>
                                                        <p class="mb-2">
                                                            <strong>Este curso tiene notas registradas en el sistema.</strong>
                                                        </p>
                                                        <p class="mb-0">
                                                            Los porcentajes de períodos no pueden ser modificados porque ya existen registros en las tablas <strong>academico_boletin</strong> o <strong>academico_actividades</strong>.
                                                        </p>
                                                    </div>
                                                <?php } ?>
                                                
                                                <div class="form-group row">
                                                    <label class="col-sm-12 control-label">
                                                        <h5><i class="fa fa-calendar"></i> Configuración de Porcentajes por Período</h5>
                                                        <p class="text-muted">Configure el porcentaje que representa cada período académico en el cálculo de la nota definitiva del curso.</p>
                                                    </label>
                                                </div>
                                                
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover" id="tablaPeriodos">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width: 100px;">Período</th>
                                                                <th>Porcentaje (%)</th>
                                                                <th style="width: 120px;">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbodyPeriodos">
                                                            <?php
                                                            $numPeriodos = !empty($resultadoCurso["gra_periodos"]) ? (int)$resultadoCurso["gra_periodos"] : 4;
                                                            $cursoId = base64_decode($_GET["id"]);
                                                            
                                                            // Obtener porcentajes existentes
                                                            $porcentajesExistentes = [];
                                                            $year = $_SESSION["bd"];
                                                            $sqlPorcentajes = "SELECT * FROM " . BD_ACADEMICA . ".academico_grados_periodos 
                                                                              WHERE gvp_grado = ? AND institucion = ? AND year = ? 
                                                                              ORDER BY gvp_periodo ASC";
                                                            $parametrosPorcentajes = [$cursoId, (int)$_SESSION["idInstitucion"], $year];
                                                            $resultadoPorcentajes = BindSQL::prepararSQL($sqlPorcentajes, $parametrosPorcentajes);
                                                            while ($filaPorcentaje = mysqli_fetch_array($resultadoPorcentajes, MYSQLI_BOTH)) {
                                                                $porcentajesExistentes[$filaPorcentaje['gvp_periodo']] = $filaPorcentaje;
                                                            }
                                                            
                                                            $sumaPorcentajes = 0;
                                                            for ($p = 1; $p <= $numPeriodos; $p++) {
                                                                $porcentajeExistente = isset($porcentajesExistentes[$p]) ? $porcentajesExistentes[$p] : null;
                                                                $valorPorcentaje = !empty($porcentajeExistente['gvp_valor']) ? $porcentajeExistente['gvp_valor'] : '';
                                                                $gvpId = !empty($porcentajeExistente['gvp_id']) ? $porcentajeExistente['gvp_id'] : '';
                                                                $sumaPorcentajes += !empty($valorPorcentaje) ? (float)$valorPorcentaje : 0;
                                                            ?>
                                                                <tr data-periodo="<?= $p; ?>" data-gvp-id="<?= $gvpId; ?>">
                                                                    <td class="text-center">
                                                                        <strong>Período <?= $p; ?></strong>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="number" 
                                                                                   class="form-control porcentaje-periodo" 
                                                                                   id="porcentaje_<?= $p; ?>" 
                                                                                   value="<?= $valorPorcentaje; ?>" 
                                                                                   min="0" 
                                                                                   max="100" 
                                                                                   step="0.01"
                                                                                   placeholder="Ej: 25.00"
                                                                                   <?= $disabledPermiso; ?> 
                                                                                   <?= $disabledPeriodos; ?>
                                                                                   data-periodo="<?= $p; ?>">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php if (empty($disabledPermiso) && empty($disabledPeriodos)) { ?>
                                                                            <button type="button" 
                                                                                    class="btn btn-sm btn-primary btn-guardar-periodo" 
                                                                                    data-periodo="<?= $p; ?>"
                                                                                    title="Guardar porcentaje">
                                                                                <i class="fa fa-save"></i> Guardar
                                                                            </button>
                                                                        <?php } else { ?>
                                                                            <span class="badge badge-secondary">Solo lectura</span>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td class="text-right"><strong>Total:</strong></td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="text" 
                                                                               class="form-control" 
                                                                               id="totalPorcentajes" 
                                                                               value="<?= number_format($sumaPorcentajes, 2); ?>" 
                                                                               readonly 
                                                                               style="font-weight: bold; <?= abs($sumaPorcentajes - 100) > 0.01 ? 'color: #dc3545;' : 'color: #28a745;'; ?>">
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                    </div>
                                                                    <?php if (abs($sumaPorcentajes - 100) > 0.01) { ?>
                                                                        <small class="form-text text-danger">
                                                                            <i class="fa fa-exclamation-circle"></i> La suma de porcentajes debe ser igual a 100%
                                                                        </small>
                                                                    <?php } else { ?>
                                                                        <small class="form-text text-success">
                                                                            <i class="fa fa-check-circle"></i> La suma de porcentajes es correcta
                                                                        </small>
                                                                    <?php } ?>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                
                                                <div class="alert alert-info mt-3">
                                                    <i class="fa fa-info-circle"></i> 
                                                    <strong>Nota:</strong> La suma de todos los porcentajes debe ser igual a 100%. 
                                                    Si no se especifican porcentajes, el sistema distribuirá equitativamente (100% / número de períodos).
                                                </div>
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
                                                        <label class="col-sm-2 control-label">
                                                            Precio
                                                            <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Precio del curso para cursos individuales. Este valor se utilizará en procesos de facturación y venta de cursos.">
                                                                <i class="fa fa-info-circle text-info"></i>
                                                            </button>
                                                        </label>
                                                        <div class="col-sm-4">
                                                            <input type="number" name="precio" class="form-control" value="<?= $resultadoCurso["gra_price"]; ?>" <?= $disabledPermiso; ?>>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label">
                                                            Minimo de estudiantes
                                                            <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Número mínimo de estudiantes requeridos para que el curso se active. Si no se alcanza este mínimo, el curso puede permanecer inactivo o cancelarse.">
                                                                <i class="fa fa-info-circle text-info"></i>
                                                            </button>
                                                        </label>

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
                                                        <label class="col-sm-2 control-label">
                                                            Maximo de estudiantes
                                                            <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Número máximo de estudiantes permitidos en el curso. Una vez alcanzado este límite, no se podrán inscribir más estudiantes.">
                                                                <i class="fa fa-info-circle text-info"></i>
                                                            </button>
                                                        </label>

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
                                                        <label class="col-sm-2 control-label">
                                                            Duracion en horas
                                                            <button type="button" class="btn btn-sm btn-link p-0 ml-1" data-toggle="tooltip" data-placement="right" title="Duración total del curso expresada en horas. Este valor representa el tiempo estimado que tomará completar el curso.">
                                                                <i class="fa fa-info-circle text-info"></i>
                                                            </button>
                                                        </label>
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
                                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Los cursos que estén marcado con esta opción permitirán que cualquiera pueda participar del curso"><i class="fa fa-info"></i></button>
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
                                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Los cursos que estén marcados como no activos no podrán ser manipulados"><i class="fa fa-info"></i></button>

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
                                                        <label class="col-sm-2 control-label">Agregar un estudiante:</label>
                                                        <div class="col-sm-8">
                                                            <?php
                                                            $selectEctudiante2 = new includeSelectSearch("SeleccionEstudiante", "ajax-listar-estudiantes.php", "buscar estudiante", "agregarEstudainte");
                                                            $selectEctudiante2->generarComponente();
                                                            ?>
                                                        </div>
                                                        <?php
                                                        $cv = Grupos::listarGrupos();
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
                                                                                $cv = Grupos::listarGrupos();
                                                                                while ($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)) {
                                                                                    if ($rv['gru_id'] == $idEstudiante['matcur_id_grupo'])
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

<!-- 🖼️ Lightbox Moderno para Imágenes -->
<div class="lightbox-overlay" id="lightboxOverlay">
    <div class="lightbox-close" id="lightboxClose">
        <i class="fa fa-times"></i>
    </div>
    <div class="lightbox-content">
        <img src="" alt="Vista previa" class="lightbox-image" id="lightboxImage">
    </div>
    <div class="lightbox-title" id="lightboxTitle">Vista Previa</div>
</div>

<style>
/* ========================================
   LIGHTBOX MODERNO
   ======================================== */

.lightbox-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.95);
    z-index: 99999;
    animation: fadeIn 0.3s ease-out;
    backdrop-filter: blur(10px);
}

.lightbox-overlay.active {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.lightbox-content {
    position: relative;
    max-width: 95vw;
    max-height: 95vh;
    animation: zoomIn 0.3s ease-out;
}

.lightbox-image {
    max-width: 100%;
    max-height: 95vh;
    width: auto;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    color: white;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 100000;
}

.lightbox-close:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    transform: rotate(90deg);
}

.lightbox-title {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: 18px;
    font-weight: 600;
    text-align: center;
    background: rgba(0, 0, 0, 0.7);
    padding: 10px 20px;
    border-radius: 20px;
    z-index: 100000;
}

/* Popover preview large */
.popover-preview-large {
    max-width: 600px !important;
    width: 600px;
    border: 2px solid #e0e0e0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    background: #fff;
}

.popover-preview-large .popover-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: 2px solid #e0e0e0;
    font-weight: 600;
}

.popover-preview-large .popover-body {
    padding: 20px;
    max-height: 80vh;
    overflow-y: auto;
}

/* Imagen de preview */
.preview-image-large {
    width: 100%;
    height: auto;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
    cursor: zoom-in;
}

.preview-image-large:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes zoomIn {
    from {
        transform: scale(0.8);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .popover-preview-large {
        max-width: 95vw !important;
        width: 95vw;
    }
    
    .popover-preview-large .popover-body {
        padding: 15px;
        max-height: 70vh;
    }
}
</style>

<script>
$(document).ready(function() {
    // Inicializar tooltips de Bootstrap
    if (typeof $.fn.tooltip !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip({
            html: true,
            placement: 'right',
            container: 'body'
        });
    }
    
    // ========================================
    // POPOVER VISTA PREVIA BOLETÍN
    // ========================================
    // Inicializar popover para vista previa de boletín (después de que Bootstrap esté cargado)
    // Usar setTimeout para asegurar que Bootstrap esté completamente cargado
    setTimeout(function() {
        if (typeof $.fn.popover !== 'undefined') {
        $('#btnVistaPreviaBoletin').popover({
            html: true,
            trigger: 'click',
            placement: 'right',
            template: '<div class="popover popover-preview-large" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
            content: function () {
                var valorB = document.getElementById("tipoBoletin");
                if (!valorB || !valorB.value) {
                    return '<div class="popover-content"><p class="text-muted">Seleccione un formato de boletín primero.</p></div>';
                }
                const imagePath = 'https://main.plataformasintia.com/app-sintia/main-app/files/images/boletines/tipo'+valorB.value+'.png';
                return '<div id="myPopoverBol" class="popover-content"><label id="lbl_tipo_bol" style="font-weight: 600; margin-bottom: 10px; display: block;">Estilo Boletín '+valorB.value+'</label>'+
                    '<img id="img-boletin-true" src="'+imagePath+'" class="preview-image-large" onerror="this.src=\'https://main.plataformasintia.com/app-sintia/main-app/files/images/boletines/default.png\'; this.onerror=null;" />'+'</div>';
            }
        });
        
        // Cerrar popover al hacer click fuera
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#btnVistaPreviaBoletin, .popover').length) {
                $('#btnVistaPreviaBoletin').popover('hide');
            }
        });
    }
    
    // Función para actualizar la imagen del popover cuando cambia el formato
    window.cambiarTipoBoletin = function() {
        var imagen_boletin = document.getElementById('img-boletin-true'); 
        if (imagen_boletin) {
            var valor = document.getElementById("tipoBoletin");  
            var lbl_tipo = document.getElementById('lbl_tipo_bol');
            if (valor && valor.value) {
                const imagePath = "https://main.plataformasintia.com/app-sintia/main-app/files/images/boletines/tipo"+valor.value+".png";
                imagen_boletin.src = imagePath;
                imagen_boletin.onerror = function() {
                    this.src = 'https://main.plataformasintia.com/app-sintia/main-app/files/images/boletines/default.png';
                    this.onerror = null;
                };
                if (lbl_tipo) {
                    lbl_tipo.textContent='Estilo Boletín '+valor.value;
                }
            }
        }
    };
    
    // Actualizar popover cuando cambia el select
    $('#tipoBoletin').on('change', function() {
        cambiarTipoBoletin();
        // Si el popover está abierto, actualizarlo
        if (typeof $.fn.popover !== 'undefined' && $('#btnVistaPreviaBoletin').data('bs.popover')) {
            var popoverInstance = $('#btnVistaPreviaBoletin').data('bs.popover');
            if (popoverInstance && popoverInstance.tip && $(popoverInstance.tip).is(':visible')) {
                $('#btnVistaPreviaBoletin').popover('dispose');
                $('#btnVistaPreviaBoletin').popover({
                    html: true,
                    trigger: 'click',
                    placement: 'right',
                    template: '<div class="popover popover-preview-large" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
                    content: function () {
                        var valorB = document.getElementById("tipoBoletin");
                        if (!valorB || !valorB.value) {
                            return '<div class="popover-content"><p class="text-muted">Seleccione un formato de boletín primero.</p></div>';
                        }
                        const imagePath = 'https://main.plataformasintia.com/app-sintia/main-app/files/images/boletines/tipo'+valorB.value+'.png';
                        return '<div id="myPopoverBol" class="popover-content"><label id="lbl_tipo_bol" style="font-weight: 600; margin-bottom: 10px; display: block;">Estilo Boletín '+valorB.value+'</label>'+
                            '<img id="img-boletin-true" src="'+imagePath+'" class="preview-image-large" onerror="this.src=\'https://main.plataformasintia.com/app-sintia/main-app/files/images/boletines/default.png\'; this.onerror=null;" />'+'</div>';
                    }
                });
            }
        }
    });
    }, 100); // Esperar 100ms para asegurar que Bootstrap esté cargado
    
    // ========================================
    // GESTIÓN DE PORCENTAJES POR PERÍODO
    // ========================================
    
    // Calcular total de porcentajes
    function calcularTotalPorcentajes() {
        var total = 0;
        $('.porcentaje-periodo').each(function() {
            var valor = parseFloat($(this).val()) || 0;
            total += valor;
        });
        
        $('#totalPorcentajes').val(total.toFixed(2));
        
        // Cambiar color según si suma 100
        if (Math.abs(total - 100) > 0.01) {
            $('#totalPorcentajes').css('color', '#dc3545');
            $('#totalPorcentajes').closest('td').find('small').remove();
            $('#totalPorcentajes').closest('td').append(
                '<small class="form-text text-danger">' +
                '<i class="fa fa-exclamation-circle"></i> La suma de porcentajes debe ser igual a 100%' +
                '</small>'
            );
        } else {
            $('#totalPorcentajes').css('color', '#28a745');
            $('#totalPorcentajes').closest('td').find('small').remove();
            $('#totalPorcentajes').closest('td').append(
                '<small class="form-text text-success">' +
                '<i class="fa fa-check-circle"></i> La suma de porcentajes es correcta' +
                '</small>'
            );
        }
    }
    
    // Calcular total al cambiar cualquier porcentaje
    $(document).on('input change', '.porcentaje-periodo', function() {
        calcularTotalPorcentajes();
    });
    
    // Guardar porcentaje de un período
    $(document).on('click', '.btn-guardar-periodo', function() {
        var btn = $(this);
        var periodo = btn.data('periodo');
        var fila = btn.closest('tr');
        var gvpId = fila.data('gvp-id');
        var porcentaje = parseFloat($('#porcentaje_' + periodo).val());
        var cursoId = '<?= base64_decode($_GET["id"]); ?>';
        
        // Validar que el porcentaje esté entre 0 y 100
        if (isNaN(porcentaje) || porcentaje < 0 || porcentaje > 100) {
            $.toast({
                heading: 'Error de validación',
                text: 'El porcentaje debe ser un número entre 0 y 100.',
                showHideTransition: 'slide',
                icon: 'error',
                position: 'top-right',
                hideAfter: 5000
            });
            return;
        }
        
        // Validar que la suma de porcentajes no exceda 100%
        var sumaActual = 0;
        $('.porcentaje-periodo').each(function() {
            var valor = parseFloat($(this).val()) || 0;
            sumaActual += valor;
        });
        
        // Obtener el valor actual del período que se está guardando
        var porcentajeActualPeriodo = parseFloat($('#porcentaje_' + periodo).val()) || 0;
        
        // Calcular la nueva suma (restar el valor actual y sumar el nuevo)
        var nuevaSuma = sumaActual - porcentajeActualPeriodo + porcentaje;
        
        if (nuevaSuma > 100.01) { // Permitir un pequeño margen de error por redondeo
            $.toast({
                heading: 'Error de validación',
                text: 'La suma de porcentajes excedería el 100%. Suma actual: ' + sumaActual.toFixed(2) + '%, nuevo valor: ' + porcentaje.toFixed(2) + '%, total sería: ' + nuevaSuma.toFixed(2) + '%',
                showHideTransition: 'slide',
                icon: 'error',
                position: 'top-right',
                hideAfter: 7000
            });
            btn.prop('disabled', false);
            btn.html(iconoOriginal);
            return;
        }
        
        // Deshabilitar botón y mostrar loading
        btn.prop('disabled', true);
        var iconoOriginal = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
        
        // Enviar datos por AJAX
        $.ajax({
            url: 'cursos-guardar-porcentaje-periodo.php',
            type: 'POST',
            data: {
                curso_id: cursoId,
                periodo: periodo,
                porcentaje: porcentaje,
                gvp_id: gvpId || ''
            },
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false);
                btn.html(iconoOriginal);
                
                if (response.success) {
                    // Actualizar el data-gvp-id de la fila si es nuevo registro
                    if (response.gvp_id) {
                        fila.data('gvp-id', response.gvp_id);
                    }
                    
                    $.toast({
                        heading: 'Éxito',
                        text: response.message || 'Porcentaje guardado correctamente.',
                        showHideTransition: 'slide',
                        icon: 'success',
                        position: 'top-right',
                        hideAfter: 3000
                    });
                    
                    // Recalcular total
                    calcularTotalPorcentajes();
                    
                    // Recargar la página después de 1 segundo para mostrar los datos actualizados
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    $.toast({
                        heading: 'Error',
                        text: response.message || 'No se pudo guardar el porcentaje.',
                        showHideTransition: 'slide',
                        icon: 'error',
                        position: 'top-right',
                        hideAfter: 5000
                    });
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                btn.html(iconoOriginal);
                
                console.error('Error AJAX:', error);
                $.toast({
                    heading: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor. Intente nuevamente.',
                    showHideTransition: 'slide',
                    icon: 'error',
                    position: 'top-right',
                    hideAfter: 5000
                });
            }
        });
    });
    
    // Calcular total inicial
    calcularTotalPorcentajes();
    
    // ========================================
    // INICIALIZACIÓN DE TABS
    // ========================================
    // Asegurar que los tabs funcionen correctamente
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Recalcular porcentajes cuando se muestra el tab de períodos
        if ($(e.target).attr('href') === '#nav-periodos') {
            calcularTotalPorcentajes();
        }
    });
    
    // Sistema de Lightbox para Imágenes
    // Click en cualquier imagen con clase preview-image-large
    $(document).on('click', '.preview-image-large', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const imgSrc = $(this).attr('src');
        const imgAlt = $(this).closest('.popover-content').find('label').text() || 'Vista Previa';
        
        // Configurar lightbox
        $('#lightboxImage').attr('src', imgSrc);
        $('#lightboxTitle').text(imgAlt);
        
        // Mostrar lightbox con animación
        $('#lightboxOverlay').addClass('active');
        
        // Prevenir scroll del body
        $('body').css('overflow', 'hidden');
    });
    
    // Cerrar lightbox al hacer click en X
    $('#lightboxClose').on('click', function() {
        cerrarLightbox();
    });
    
    // Cerrar lightbox al hacer click en el fondo oscuro
    $('#lightboxOverlay').on('click', function(e) {
        if (e.target === this) {
            cerrarLightbox();
        }
    });
    
    // Cerrar lightbox con tecla ESC
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#lightboxOverlay').hasClass('active')) {
            cerrarLightbox();
        }
    });
    
    function cerrarLightbox() {
        $('#lightboxOverlay').removeClass('active');
        $('body').css('overflow', 'auto');
    }
});
</script>

</html>