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

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> Genera informes parciales de rendimiento académico. Selecciona primero el año.
</div>

<div class="panel">
    <header class="panel-heading panel-heading-purple">
        <i class="fas fa-users"></i> Informe parcial por curso
    </header>
    <div class="panel-body">
        <form action="../compartido/informe-parcial-grupo-detalle.php" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">

            <!-- PASO 1: Año (implícito, se toma del actual por defecto) -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-school"></i> Curso
                </label>
                <div class="col-sm-9">
                    <select class="form-control  select2" name="curso">
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
                <label class="col-sm-3 control-label">
                    <i class="fas fa-user-friends"></i> Grupos
                </label>
                <div class="col-sm-9">
                    <select class="form-control  select2" name="grupo">
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

            <div class="form-group row">
                <div class="col-sm-12 text-right">
                    <button type="submit" class="btn btn-primary btn-lg" name="consultas">
                        <i class="fas fa-chart-line"></i> Consultar Informe
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel">
    <header class="panel-heading panel-heading-red">
        <i class="fas fa-user"></i> Informe parcial por estudiante
    </header>
    <div class="panel-body">
        <form name="formularioGuardar" action="../compartido/informe-parcial.php" method="post" target="_blank">
            
            <!-- PASO 1: Año -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-calendar"></i> Año (implícito: año actual)
                </label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?= $_SESSION["bd"] ?>" readonly>
                    <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Se usará el año académico actual</small>
                </div>
            </div>
            
            <!-- PASO 2: Filtrar por Grado -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-school"></i> Filtrar por Grado <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <select id="filtroGradoParcial" class="form-control  select2" onchange="window.cargarEstudiantesParcial(this.value, 'selectEstudiantesParcial')">
                        <option value="">Seleccione un grado</option>
                        <?php
                        $grados = Grados::traerGradosInstitucion($config, GRADO_GRUPAL);
                        while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
                        ?>
                            <option value="<?= $grado['gra_id']; ?>"><?= $grado['gra_id'] . ". " . strtoupper($grado['gra_nombre']); ?></option>
                        <?php } ?>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Seleccione el grado para filtrar los estudiantes
                    </small>
                </div>
            </div>

            <!-- PASO 3: Estudiante -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-user-graduate"></i> Estudiante <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <select id="selectEstudiantesParcial" class="form-control  select2" name="estudiante" required disabled>
                        <option value="">Primero seleccione un grado</option>
                    </select>
                    <div id="loadingEstParcial" style="display: none; margin-top: 10px;">
                        <i class="fas fa-spinner fa-spin"></i> Cargando estudiantes...
                    </div>
                </div>
            </div>

            <!-- PASO 4: Periodo -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-calendar-alt"></i> Periodo <span class="text-danger">*</span>
                </label>
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

            <div class="form-group row">
                <div class="col-sm-12 text-right">
                    <button type="submit" class="btn btn-primary btn-lg" name="consultas">
                        <i class="fas fa-chart-line"></i> Consultar Informe
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
