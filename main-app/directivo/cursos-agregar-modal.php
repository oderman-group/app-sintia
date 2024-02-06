<?php
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
} ?>

<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />


<div class="panel">
    <header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </header>
    <div class="panel-body">


        <form name="formularioGuardar" action="cursos-guardar.php" method="post">

            <div class="form-group row">
                <label class="col-sm-2 control-label">Nombre Curso <span style="color: red;">(*)</span></label>
                <div class="col-sm-10">
                    <input type="text" name="nombreC" class="form-control" required <?= $disabledPermiso; ?>>
                </div>

                <div class="tab-pane fade" id="nav-configuracion" role="tabpanel" aria-labelledby="nav-configuracion-tab">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">URL imagen</label>
                                <div class="col-sm-10">
                                    <input type="text" name="imagen" class="form-control"  <?= $disabledPermiso; ?>>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Descripcion</label>
                                <div class="col-sm-10">
                                <textarea cols="80" id="editor1" name="descripcion" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?= $disabledPermiso; ?>></textarea>
                                    
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Contenido</label>
                                <div class="col-sm-10">
                                <textarea cols="80" id="editor2" name="contenido" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?= $disabledPermiso; ?>></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Precio</label>
                                <div class="col-sm-10">
                                    <input type="number"  name="precio"  class="form-control" value="0"  <?= $disabledPermiso; ?>>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Minimo de estudiantes</label>
                                <div class="col-sm-10">
                                    <input type="number" name="minEstudiantes" class="form-control" value="1"  min="1" <?= $disabledPermiso; ?>>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Maximo de estudiantes</label>
                                <div class="col-sm-10">
                                    <input type="number" name="maxEstudiantes" disabled class="form-control" value="1" min="1"<?= $disabledPermiso; ?>>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Duracion en horas</label>
                                <div class="col-sm-10">
                                    <input type="number" id="horas" name="horas" class="form-control" value="0"   <?= $disabledPermiso; ?>>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Auto Enrollment</label>
                                <div class="col-sm-10">
                                    <label class="switchToggle">
                                        <input name="autoenrollment" type="checkbox">
                                        <span class="slider green round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Activo</label>
                                <div class="col-sm-10">
                                    <label class="switchToggle">
                                        <input name="activo" type="checkbox">
                                        <span class="slider green round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (Modulos::validarPermisoEdicion()) { ?>
                    <button type="submit" class="btn  btn-info">
                        <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios
                    </button>
                <?php } ?>

            </div>

            <?php
            $opcionesConsulta = Grados::listarGrados(1);
            $numCursos = mysqli_num_rows($opcionesConsulta);
            if ($numCursos > 0) {
            ?>
                <div class="form-group row">
                    <label class="col-sm-2 control-label">Curso Siguiente</label>
                    <div class="col-sm-10">
                        <select class="form-control  select2" name="graSiguiente" <?= $disabledPermiso; ?>>
                            <option value="">Seleccione una opción</option>
                            <?php
                            while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                            ?>
                                <option value="<?= $opcionesDatos['gra_id']; ?>"><?= strtoupper($opcionesDatos['gra_nombre']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Valor Matricula</label>
                <div class="col-sm-10">
                    <input type="text" name="valorM" class="form-control" value="0" <?= $disabledPermiso; ?>>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 control-label">Valor Pension</label>
                <div class="col-sm-10">
                    <input type="text" name="valorP" class="form-control" value="0" <?= $disabledPermiso; ?>>
                </div>
            </div>


            <?php if (Modulos::validarPermisoEdicion()) { ?>
                <button type="submit" class="btn  btn-info">
                    <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                </button>
            <?php } ?>
        </form>
    </div>
</div>