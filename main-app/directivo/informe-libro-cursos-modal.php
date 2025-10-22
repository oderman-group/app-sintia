<?php
include("session.php");
$idPaginaInterna = 'DT0135';
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
    <i class="fas fa-info-circle"></i> Genera el libro final. Selecciona primero el año, luego los demás filtros se cargarán automáticamente.
</div>

<div class="panel">
    <header class="panel-heading panel-heading-purple">
        <i class="fas fa-users"></i> GENERAR POR CURSO
    </header>
    <div class="panel-body">
        <form name="formularioGuardar" id="formLibroCurso" action="../compartido/matricula-libro-curso-3-mejorado.php" method="post" target="_blank">

            <!-- PASO 1: Formato de Libro -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-palette"></i> Formato de Libro <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <select class="form-control select2" name="formatoLibro" id="formatoLibroCurso" required onchange="cambiarAccionFormulario('formLibroCurso', this.value)">
                        <option value="">Seleccione un formato</option>
                        <option value="mejorado" selected>📱 Formato Nuevo (Moderno y Responsive)</option>
                        <option value="1">Formato 1 (Clásico)</option>
                        <option value="2">Formato 2</option>
                        <option value="3">Formato 3</option>
                        <option value="4">Formato 4</option>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> El formato nuevo incluye diseño moderno, exportación a PDF/Excel y responsive.
                    </small>
                </div>
            </div>

            <!-- PASO 2: Año -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-calendar"></i> Año <span class="text-danger">*</span>
                </label>
                <div class="col-sm-4">
                    <select class="form-control  select2" name="year" id="yearLibroCurso" required onchange="window.cargarCursosPorYearLibro(this.value, 'cursoLibroCurso', 'grupoLibroCurso')">
                        <option value="">Seleccione un año</option>
                        <?php
                        $yearStartTemp=$yearStart;
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

            <!-- PASO 3: Curso -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-school"></i> Curso <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <select class="form-control  select2" name="curso" id="cursoLibroCurso" required disabled>
                        <option value="">Primero seleccione un año</option>
                    </select>
                    <div id="loadingCursosLibro" style="display: none; margin-top: 5px;">
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
                    <select class="form-control  select2" name="grupo" id="grupoLibroCurso" disabled>
                        <option value="">Primero seleccione un año</option>
                    </select>
                    <div id="loadingGruposLibro" style="display: none; margin-top: 5px;">
                        <small><i class="fas fa-spinner fa-spin"></i> Cargando grupos...</small>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-12 text-right">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-book"></i> Generar Informe
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
        <form name="formularioGuardar" id="formLibroEstudiante" action="../compartido/matricula-libro-curso-3-mejorado.php" method="post" target="_blank">
            
            <!-- PASO 1: Formato de Libro -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-palette"></i> Formato de Libro <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <select class="form-control select2" name="formatoLibro" id="formatoLibroEstudiante" required onchange="cambiarAccionFormulario('formLibroEstudiante', this.value)">
                        <option value="">Seleccione un formato</option>
                        <option value="mejorado" selected>📱 Formato Nuevo (Moderno y Responsive)</option>
                        <option value="1">Formato 1 (Clásico)</option>
                        <option value="2">Formato 2</option>
                        <option value="3">Formato 3</option>
                        <option value="4">Formato 4</option>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> El formato nuevo incluye diseño moderno, exportación a PDF/Excel y responsive.
                    </small>
                </div>
            </div>

            <!-- PASO 2: Año -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-calendar"></i> Año <span class="text-danger">*</span>
                </label>
                <div class="col-sm-4">
                    <select class="form-control  select2" name="year" id="yearLibroEst" required onchange="window.habilitarFiltroGrado('yearLibroEst', 'filtroGradoLibro')">
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
            
            <!-- PASO 3: Filtrar por Grado -->
            <div class="form-group row">
                <label class="col-sm-3 control-label">
                    <i class="fas fa-school"></i> Filtrar por Grado <span class="text-danger">*</span>
                </label>
                <div class="col-sm-9">
                    <select id="filtroGradoLibro" class="form-control  select2" onchange="window.cargarEstudiantesLibroYear()" disabled>
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
                    <select id="selectEstudiantesLibro" class="form-control  select2" name="id" required disabled>
                        <option value="">Primero seleccione año y grado</option>
                    </select>
                    <div id="loadingEstLibro" style="display: none; margin-top: 10px;">
                        <i class="fas fa-spinner fa-spin"></i> Cargando estudiantes...
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-12 text-right">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-book"></i> Generar Informe
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Función para cambiar el action del formulario según el formato seleccionado
    function cambiarAccionFormulario(formId, formato) {
        const form = document.getElementById(formId);
        if (!form) return;
        
        let action = '';
        switch(formato) {
            case 'mejorado':
                action = '../compartido/matricula-libro-curso-3-mejorado.php';
                break;
            case '1':
                action = '../compartido/matricula-libro-curso-1.php';
                break;
            case '2':
                action = '../compartido/matricula-libro-curso-2.php';
                break;
            case '3':
                action = '../compartido/matricula-libro-curso-3.php';
                break;
            case '4':
                action = '../compartido/matricula-libro-curso-4.php';
                break;
            default:
                action = '../compartido/matricula-libro-curso-3-mejorado.php';
        }
        
        form.action = action;
        console.log('📄 Formato seleccionado: ' + formato + ' → Action: ' + action);
    }
    
    // Validar y habilitar campos antes de enviar (para formulario por curso)
    $('#formLibroCurso').on('submit', function(e) {
        const curso = $('#cursoLibroCurso');
        const grupo = $('#grupoLibroCurso');
        
        // Si están deshabilitados pero tienen valor, habilitarlos temporalmente
        if (curso.prop('disabled') && curso.val()) {
            curso.prop('disabled', false);
        }
        if (grupo.prop('disabled') && grupo.val()) {
            grupo.prop('disabled', false);
        }
        
        // Validar que curso tenga valor
        if (!curso.val() || curso.val() === '') {
            e.preventDefault();
            alert('⚠️ Campo Requerido\n\nPor favor selecciona un Curso antes de generar el informe.');
            return false;
        }
        
        console.log('✅ Enviando formulario - Curso:', curso.val(), 'Grupo:', grupo.val());
    });
    
    // Validar para formulario por estudiante
    $('#formLibroEstudiante').on('submit', function(e) {
        const estudiante = $('#selectEstudiantesLibro');
        
        // Si está deshabilitado pero tienen valor, habilitarlo temporalmente
        if (estudiante.prop('disabled') && estudiante.val()) {
            estudiante.prop('disabled', false);
        }
        
        // Validar que estudiante tenga valor
        if (!estudiante.val() || estudiante.val() === '') {
            e.preventDefault();
            alert('⚠️ Campo Requerido\n\nPor favor selecciona un Estudiante antes de generar el informe.');
            return false;
        }
        
        console.log('✅ Enviando formulario - Estudiante:', estudiante.val());
    });
    
    // Inicializar al cargar el modal
    $(document).ready(function() {
        // Establecer el action inicial en el formato por defecto (mejorado)
        cambiarAccionFormulario('formLibroCurso', 'mejorado');
        cambiarAccionFormulario('formLibroEstudiante', 'mejorado');
    });
</script>
