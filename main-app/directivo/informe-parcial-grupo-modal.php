<?php
include("session.php");
$idPaginaInterna = 'DT0101';
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
} ?>


<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<div class="panel">
    <header class="panel-heading panel-heading-purple">Informe parcial</header>
    <div class="panel-body">


        <form action="../compartido/informe-parcial-grupo-detalle.php" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">

            <div class="form-group row">
                <label class="col-sm-2 control-label">Curso</label>
                <div class="col-sm-10">
                    <select class="form-control  select2" style="width: 100%;" name="curso">
                        <option value=""></option>
                        <?php
                        $c = Grados::traerGradosInstitucion($config);
                        while ($r = mysqli_fetch_array($c, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?php echo $r['gra_id']; ?>"><?php echo $r['gra_nombre']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Grupos</label>
                <div class="col-sm-10">
                    <select class="form-control  select2" style="width: 100%;" name="grupo">
                        <option value=""></option>
                        <?php
                        $c = Grupos::listarGrupos();
                        while ($r = mysqli_fetch_array($c, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?php echo $r['gru_id']; ?>"><?php echo $r['gru_nombre']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <input type="submit" class="btn btn-info" value="Consultar Informe" name="consultas">
        </form>
    </div>
</div>
    <div class="panel">
        <header class="panel-heading panel-heading-red">POR ESTUDIANTE </header>
        <div class="panel-body">
            <form name="formularioGuardar" action="../compartido/informe-parcial.php" method="post" target="_blank">
                <div class="form-group row">
                    <label class="col-sm-2 control-label">Estudiante</label>
                    <div class="col-sm-8">

                        <select id="selectEstudiantes" class="form-control  select2" name="estudiante" multiple required>
                            <option value="">Seleccione una opción</option>
                            <?php
                            $grados = Grados::traerGradosInstitucion($config, GRADO_GRUPAL);
                            while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
                            ?>

                                <optgroup label="<?= $grado['gra_nombre']; ?>">
                                    <?php
                                    $filtro = ' AND mat_grado="' . $grado['gra_id'].'"';
                                    $opcionesConsulta = Estudiantes::listarEstudiantesEnGrados($filtro, '');
                                    while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                                    ?>

                                        <option value="<?= $opcionesDatos['mat_id']; ?>">
                                            <?= "[" . $opcionesDatos['mat_id'] . "] " . strtoupper($opcionesDatos['mat_primer_apellido'] . " " . $opcionesDatos['mat_segundo_apellido'] . " " . $opcionesDatos['mat_nombres'] . " " . $opcionesDatos['mat_nombre2']); ?>
                                            - <?= strtoupper($opcionesDatos['gra_nombre'] . " " . $opcionesDatos['gru_nombre']); ?>
                                        </option>

                                    <?php } ?>

                                </optgroup>
                            <?php } ?>

                        </select>
                        <span style="color: darkblue;">Seleccione solo una opción de este listado.</span>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Periodo</label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="periodo" required>
                            <option value="">Seleccione una opción</option>
                            <?php
                            $p = 1;
                            while ($p <= $config[19]) {
                                echo '<option value="' . $p . '">Periodo ' . $p . '</option>';
                                $p++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <input type="submit" class="btn btn-info" value="Consultar Informe" name="consultas">
            </form>
        </div>
    </div>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>