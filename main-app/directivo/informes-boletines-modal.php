<?php $idPaginaInterna = 'DT0100';
require_once("session.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
require_once("../class/Estudiantes.php");
?>

<!-- END HEAD -->
<div class="col-sm-12">
    <?php include("../../config-general/mensajes-informativos.php"); ?>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Selecciona primero el año, luego los demás filtros se cargarán automáticamente según ese año.
    </div>
    
    <div class="panel">
        <header class="panel-heading panel-heading-purple">
            <i class="fas fa-users"></i> GENERAR POR CURSO
        </header>
        <div class="panel-body">
            <form name="formularioGuardar" action="informes-formato-boletin.php" method="post" target="_blank">
                
                <!-- PASO 1: Año -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-calendar"></i> Año <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="year" id="yearCurso" required onchange="window.cargarCursosPorYear(this.value, 'cursoCurso', 'grupoCurso')">
                            <option value="">Seleccione un año</option>
                            <?php
                            $yearStartC = $yearStart;
                            $yearEndC = $yearEnd;
                            while ($yearStartC <= $yearEndC) {
                                $selected = ($_SESSION["bd"] == $yearStartC) ? 'selected' : '';
                                echo "<option value='" . $yearStartC . "' $selected>" . $yearStartC . "</option>";
                                $yearStartC++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Primero selecciona el año académico</small>
                    </div>
                </div>

                <!-- PASO 2: Formato de Boletín -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-palette"></i> Formato de Boletín <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="tipoBoletin" class="form-control  select2" name="formatoB">
                            <option value="">Seleccione una opción</option>
                            <?php
                            try {
                                $consultaBoletin = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".opciones_generales WHERE ogen_grupo=15");
                            } catch (Exception $e) {
                                include("../compartido/error-catch-to-report.php");
                            }
                            while ($datosBoletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH)) {
                            ?>
                                <option <?php if($config['conf_formato_boletin'] == $datosBoletin['ogen_id']){ echo "selected";} ?> value="<?= $datosBoletin['ogen_id']; ?>" ><?= $datosBoletin['ogen_nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- PASO 3: Curso -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-school"></i> Curso <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="curso" id="cursoCurso" required disabled>
                            <option value="">Primero seleccione un año</option>
                        </select>
                        <div id="loadingCursos" style="display: none; margin-top: 5px;">
                            <small><i class="fas fa-spinner fa-spin"></i> Cargando cursos...</small>
                        </div>
                    </div>
                </div>

                <!-- PASO 4: Grupo -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-user-friends"></i> Grupo
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="grupo" id="grupoCurso" disabled>
                            <option value="">Primero seleccione un año</option>
                        </select>
                        <div id="loadingGrupos" style="display: none; margin-top: 5px;">
                            <small><i class="fas fa-spinner fa-spin"></i> Cargando grupos...</small>
                        </div>
                    </div>
                </div>

                <!-- PASO 5: Periodo -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-calendar-alt"></i> Periodo <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" id="periodo" name="periodo" required>
                            <option value="">Seleccione una opción</option>
                            <?php
                            $p = 1;
                            while ($p <= $config[19]) {
                                $selected = '';
                                if($p == $config['conf_periodo']) {
                                    $selected ='selected';
                                }
                                echo '<option value="' . $p . '" '.$selected.'>Periodo ' . $p . '</option>';
                                $p++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-file-pdf"></i> Generar Boletín
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    
    <div class="panel">
        <header class="panel-heading panel-heading-red">
            <i class="fas fa-user"></i> GENERAR POR ESTUDIANTE
        </header>
        <div class="panel-body">
            <form name="formularioGuardar" action="informes-formato-boletin.php" method="post" target="_blank">
                
                <!-- PASO 1: Año -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-calendar"></i> Año <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control  select2" name="year" id="yearEst" required onchange="window.habilitarFiltroGrado('yearEst', 'filtroGradoEst')">
                            <option value="">Seleccione un año</option>
                            <?php
                            $yearStartE = $yearStart;
                            $yearEndE = $yearEnd;
                            while ($yearStartE <= $yearEndE) {
                                $selected = ($_SESSION["bd"] == $yearStartE) ? 'selected' : '';
                                echo "<option value='" . $yearStartE . "' $selected>" . $yearStartE . "</option>";
                                $yearStartE++;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Primero selecciona el año académico</small>
                    </div>
                </div>

                <!-- PASO 2: Formato de Boletín -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-palette"></i> Formato de Boletín <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="tipoBoletinEst" class="form-control  select2" name="formatoB">
                            <option value="">Seleccione una opción</option>
                            <?php
                            try {
                                $consultaBoletin = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".opciones_generales WHERE ogen_grupo=15");
                            } catch (Exception $e) {
                                include("../compartido/error-catch-to-report.php");
                            }
                            while ($datosBoletin = mysqli_fetch_array($consultaBoletin, MYSQLI_BOTH)) {
                            ?>
                                <option <?php if($config['conf_formato_boletin'] == $datosBoletin['ogen_id']){ echo "selected";} ?> value="<?= $datosBoletin['ogen_id']; ?>" ><?= $datosBoletin['ogen_nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- PASO 3: Filtrar por Grado -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-school"></i> Filtrar por Grado <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="filtroGradoEst" class="form-control  select2" onchange="window.cargarEstudiantesPorYearGrado()" disabled>
                            <option value="">Primero seleccione un año</option>
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

                <!-- PASO 4: Estudiante -->
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-user-graduate"></i> Estudiante <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="selectEstudiantes" class="form-control  select2" name="estudiante" required disabled>
                            <option value="">Primero seleccione año y grado</option>
                        </select>
                        <div id="loadingEstudiantes" style="display: none; margin-top: 10px;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando estudiantes...
                        </div>
                    </div>
                </div>

                <!-- PASO 5: Periodo -->
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
                                $selected = '';
                                if($p == $config['conf_periodo']) {
                                    $selected ='selected';
                                }
                                echo '<option value="' . $p . '" '.$selected.'>Periodo ' . $p . '</option>';
                                $p++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-file-pdf"></i> Generar Boletín
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
