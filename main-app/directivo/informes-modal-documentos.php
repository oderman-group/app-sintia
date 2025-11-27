<?php
require_once("session.php");
$idPaginaInterna = 'DT0347';
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
require_once(ROOT_PATH . "/main-app/class/App/Academico/Matricula.php");
require_once(ROOT_PATH . "/main-app/class/App/Academico/Matriculas_Documentos.php");
require_once(ROOT_PATH . "/main-app/class/App/Admiciones/Aspirantes.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
?>

<!-- END HEAD -->
<div class="col-sm-12">
    <?php include("../../config-general/mensajes-informativos.php"); ?>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Genera el informe de documentos de inscripción por estudiante.
    </div>
    
    <div class="panel">
        <header class="panel-heading panel-heading-purple">
            <i class="fas fa-file-alt"></i> Documentos de Inscripción
        </header>
        <div class="panel-body">
            <form name="formularioGuardar" action="../compartido/informes-documentos-inscripcion.php" method="post" target="_blank">
                
                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-school"></i> Filtrar por Grado <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select id="filtroGradoDoc" class="form-control  select2" onchange="window.cargarEstudiantesDocumentos(this.value, 'selectEstudiantes')">
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
                        <select id="selectEstudiantes" class="form-control  select2" name="estudiante" required disabled>
                            <option value="">Primero seleccione un grado</option>
                        </select>
                        <div id="loadingEstDoc" style="display: none; margin-top: 10px;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando estudiantes...
                        </div>
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search"></i> Consultar Documentación
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
