<?php
include("session.php");
$idPaginaInterna = 'DT0082';
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
?>
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />


<div class="col-sm-12">

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Genera certificados académicos por áreas o por materias. Selecciona el rango de años para el estudiante.
    </div>

    <?php
    switch ($config['conf_certificado']) {
        case 1:
            $ext = '';
        break;

        case 2:
            $ext = '-2';
        break;

        case 3:
            $ext = '-3';
        break;

        default:
            $ext = '';
        break;
    }
    ?>
    <div class="panel">
        <header class="panel-heading panel-heading-purple">
            <i class="fas fa-certificate"></i> Certificado por áreas
        </header>
        <div class="panel-body">
            <form action="estudiantes-formato-certificado.php" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-palette"></i> Estilo de certificado <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-3">
                        <select class="form-control  select2" id="tipoCertificado" name="certificado" <?=$disabledPermiso;?>>
                            <option value="1" <?php if($config['conf_certificado']==1){ echo "selected";} ?>>Certificado 1</option>
                            <option value="2" <?php if($config['conf_certificado']==2){ echo "selected";} ?>>Certificado 2</option>
                            <option value="3" <?php if($config['conf_certificado']==3){ echo "selected";} ?>>Certificado 3</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-school"></i> Filtrar por Grado <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="filtroGradoCert1" class="form-control  select2" onchange="cargarEstudiantesCertificado(this.value, 'selectEstudiantes1')">
                            <option value="">Primero seleccione un grado</option>
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

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-user-graduate"></i> Estudiante <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="selectEstudiantes1" class="form-control  select2" name="id" required disabled>
                            <option value="">Primero seleccione un grado</option>
                        </select>
                        <div id="loadingEst1" style="display: none; margin-top: 10px;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando estudiantes...
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Desde que año</label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="desde" required>
                            <option value=""></option>
                            <?php
                            $yearStartTemp=$yearStart;
                            while ($yearStartTemp <= $yearEnd) {
                                if ($_SESSION["bd"] == $yearStartTemp)
                                    echo "<option value='" . $yearStartTemp . "' selected style='color:blue;'>" . $yearStartTemp . "</option>";
                                else
                                    echo "<option value='" . $yearStartTemp . "'>" . $yearStartTemp . "</option>";
                                $yearStartTemp++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Hasta que año</label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="hasta" required>
                            <option value=""></option>
                            <?php
                            $yearStartTemp = $yearArray[0];
                            while ($yearStartTemp <= $yearEnd) {
                                if ($_SESSION["bd"] == $yearStartTemp)
                                    echo "<option value='" . $yearStartTemp . "' selected style='color:blue;'>" . $yearStartTemp . "</option>";
                                else
                                    echo "<option value='" . $yearStartTemp . "'>" . $yearStartTemp . "</option>";
                                $yearStartTemp++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <?php if ($config['conf_estampilla_certificados'] == SI) { ?>
                    <div class="form-group row">
                        <label class="col-sm-3 control-label">
                            <i class="fas fa-stamp"></i> Estampilla o referente de pago
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="estampilla" value="" placeholder="Ingrese el número de estampilla">
                        </div>
                    </div>
                <?php } ?>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-heading"></i> Formato de impresión
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control select2" name="sin_encabezado">
                            <option value="0" selected>Con encabezado</option>
                            <option value="1">Sin encabezado (Papel membrete)</option>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Seleccione "Sin encabezado" si va a imprimir en papel membrete
                        </small>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-certificate"></i> Generar Certificado
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="panel">
        <header class="panel-heading panel-heading-purple">
            <i class="fas fa-list-alt"></i> Certificado por materias
        </header>
        <div class="panel-body">
            <form action="../compartido/matricula-certificado.php" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-school"></i> Filtrar por Grado <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="filtroGradoCert2" class="form-control  select2" onchange="cargarEstudiantesCertificado(this.value, 'selectEstudiantes2')">
                            <option value="">Primero seleccione un grado</option>
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

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-user-graduate"></i> Estudiante <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="selectEstudiantes2" class="form-control  select2" name="id" required disabled>
                            <option value="">Primero seleccione un grado</option>
                        </select>
                        <div id="loadingEst2" style="display: none; margin-top: 10px;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando estudiantes...
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Desde que año</label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="desde" required>
                            <option value=""></option>
                            <?php
                            $yearStartTemp = $yearArray[0];
                            while ($yearStartTemp <= $yearEnd) {
                                if ($_SESSION["bd"] == $yearStartTemp)
                                    echo "<option value='" . $yearStartTemp . "' selected style='color:blue;'>" . $yearStartTemp . "</option>";
                                else
                                    echo "<option value='" . $yearStartTemp . "'>" . $yearStartTemp . "</option>";
                                $yearStartTemp++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Hasta que año</label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="hasta" required>
                            <option value=""></option>
                            <?php
                            $yearStartTemp = $yearArray[0];
                            while ($yearStartTemp <= $yearEnd) {
                                if ($_SESSION["bd"] == $yearStartTemp)
                                    echo "<option value='" . $yearStartTemp . "' selected style='color:blue;'>" . $yearStartTemp . "</option>";
                                else
                                    echo "<option value='" . $yearStartTemp . "'>" . $yearStartTemp . "</option>";
                                $yearStartTemp++;
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-certificate"></i> Generar Certificado
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
