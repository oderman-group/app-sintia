<?php
include("session.php");
$idPaginaInterna = 'DT0120';
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
} ?>

<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="col-sm-12">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Genera reportes de estudiantes aplicando múltiples filtros según tus necesidades.
    </div>

    <div class="panel">
        <header class="panel-heading panel-heading-purple">
            <i class="fas fa-filter"></i> Filtros de Búsqueda
        </header>
        <div class="panel-body" style="max-height: 600px; overflow-y: auto;">
            <form action="../compartido/reporte-matriculados-estado.php" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-school"></i> Curso
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="cursosR">
                            <option value="">Todos los cursos</option>
                            <?php
                            $c_cursos = Grados::traerGradosInstitucion($config);
                            while ($r_cursos = mysqli_fetch_array($c_cursos, MYSQLI_BOTH)) {
                                echo '<option value="' . $r_cursos["gra_id"] . '">' . $r_cursos["gra_nombre"] . '</option>';
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
                        <select class="form-control  select2" name="gruposR">
                            <option value="">Todos los grupos</option>
                            <?php
                            $opcionesConsulta = Grupos::listarGrupos();
                            while ($r_grupos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                                echo '<option value="' . $r_grupos["gru_id"] . '">' . $r_grupos["gru_nombre"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-check-circle"></i> Estado
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="estadoR">
                            <option value="">Todos los estados</option>
                            <option value="1">Matriculado</option>
                            <option value="2">Asistente</option>
                            <option value="3">Cancelado</option>
                            <option value="4">No matriculado</option>
                            <option value="5">En inscripción</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-user-tag"></i> Tipo de estudiante
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="tipoR">
                            <option value="">Todos los tipos</option>
                            <?php
                            try {
                                $c_testudiante = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre, ogen_grupo FROM " . $baseDatosServicios . ".opciones_generales WHERE ogen_grupo=5;");
                            } catch (Exception $e) {
                                include("../compartido/error-catch-to-report.php");
                            }
                            while ($r_testudiante = mysqli_fetch_array($c_testudiante, MYSQLI_BOTH)) {
                                echo '<option value="' . $r_testudiante["ogen_id"] . '">' . $r_testudiante["ogen_nombre"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-users"></i> Acudiente
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="acudienteR">
                            <option value="">Todos</option>
                            <option value="1">Con acudiente</option>
                            <option value="0">Sin acudiente</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-universal-access"></i> Estudiante de Inclusión
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="inclu">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-globe"></i> Estudiante Extranjero
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="extra">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-camera"></i> Foto
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="fotoR">
                            <option value="">Todos</option>
                            <option value="1">Con foto</option>
                            <option value="0">Sin foto</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-venus-mars"></i> Género
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="generoR">
                            <option value="">Todos los géneros</option>
                            <?php
                            try {
                                $c_testudiante = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre, ogen_grupo FROM " . $baseDatosServicios . ".opciones_generales WHERE ogen_grupo=4;");
                            } catch (Exception $e) {
                                include("../compartido/error-catch-to-report.php");
                            }
                            while ($r_testudiante = mysqli_fetch_array($c_testudiante, MYSQLI_BOTH)) {
                                echo '<option value="' . $r_testudiante["ogen_id"] . '">' . $r_testudiante["ogen_nombre"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-building"></i> Estrato
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="estratoE">
                            <option value="">Todos los estratos</option>
                            <?php
                            try {
                                $c_testudiante = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre, ogen_grupo FROM " . $baseDatosServicios . ".opciones_generales WHERE ogen_grupo=3;");
                            } catch (Exception $e) {
                                include("../compartido/error-catch-to-report.php");
                            }
                            while ($r_testudiante = mysqli_fetch_array($c_testudiante, MYSQLI_BOTH)) {
                                echo '<option value="' . $r_testudiante["ogen_id"] . '">' . $r_testudiante["ogen_nombre"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label">
                        <i class="fas fa-id-card"></i> Tipo de documento
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control  select2" name="tdocumentoR">
                            <option value="">Todos los tipos</option>
                            <?php
                            try {
                                $c_testudiante = mysqli_query($conexion, "SELECT ogen_id, ogen_nombre, ogen_grupo FROM " . $baseDatosServicios . ".opciones_generales WHERE ogen_grupo=1;");
                            } catch (Exception $e) {
                                include("../compartido/error-catch-to-report.php");
                            }
                            while ($r_testudiante = mysqli_fetch_array($c_testudiante, MYSQLI_BOTH)) {
                                echo '<option value="' . $r_testudiante["ogen_id"] . '">' . $r_testudiante["ogen_nombre"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="submit" class="btn btn-primary btn-lg" name="consultas">
                            <i class="fas fa-search"></i> Consultar Informe
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
