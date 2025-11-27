<?php
include("session.php");
$idPaginaInterna = 'DT0140';
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
?>

<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> Genera el informe de sábanas. Selecciona primero el año, luego los demás filtros se cargarán automáticamente.
</div>

<div class="panel">
    <header class="panel-heading panel-heading-purple">
        <i class="fas fa-table"></i> GENERAR INFORME DE SÁBANAS
    </header>
    <div class="panel-body">
        <form name="formularioGuardar" action="../compartido/reportes-sabanas-fast.php" method="post" target="_blank">
            
            <!-- PASO 1: Año -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-calendar"></i> Año <span class="text-danger">*</span>
                </label>
                <div class="col-sm-4">
                    <select class="form-control  select2" name="year" id="yearSabana" required onchange="window.cargarCursosPorYearSabana(this.value)">
                        <option value="">Seleccione un año</option>
                        <?php
                        $yearStartTemp = $yearStart;
                        while ($yearStartTemp <= $yearEnd) {
                            $selected = ($_SESSION["bd"] == $yearStartTemp) ? 'selected' : '';
                            echo "<option value='" . $yearStartTemp . "' $selected>" . $yearStartTemp . "</option>";
                            $yearStartTemp++;
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-5">
                    <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Primero selecciona el año académico</small>
                </div>
            </div>
            
            <!-- PASO 2: Curso -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-school"></i> Curso <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <select class="form-control  select2" name="curso" id="cursoSabana" required disabled>
                        <option value="">Primero seleccione un año</option>
                    </select>
                    <div id="loadingCursosSabana" style="display: none; margin-top: 5px;">
                        <small><i class="fas fa-spinner fa-spin"></i> Cargando cursos...</small>
                    </div>
                </div>
            </div>

            <!-- PASO 3: Grupo -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-user-friends"></i> Grupo
                </label>
                <div class="col-sm-9">
                    <select class="form-control  select2" name="grupo" id="grupoSabana" disabled>
                        <option value="">Primero seleccione un año</option>
                    </select>
                    <div id="loadingGruposSabana" style="display: none; margin-top: 5px;">
                        <small><i class="fas fa-spinner fa-spin"></i> Cargando grupos...</small>
                    </div>
                </div>
            </div>

            <!-- PASO 4: Periodo -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-calendar-alt"></i> Periodo <span class="text-danger">*</span>
                </label>
                <div class="col-sm-4">
                    <select class="form-control  select2" name="per" required>
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
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-table"></i> Generar Sábana
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
