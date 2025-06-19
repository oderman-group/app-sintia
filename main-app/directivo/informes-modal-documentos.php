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
include_once(ROOT_PATH . "/main-app/compartido/ComponenteModal.php");
$modaInfo = new ComponenteModal('informacion', $frases[115][$datosUsuarioActual['uss_idioma']], '../compartido/page-info-modal.php', null, 5000, '600px', false);
?>

<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
<style>
    .select2 {
        width: 100% !important;
    }

    .modal {
        z-index: 1050 !important;
        outline: 0;
        overflow-y: auto !important
    }
</style>

<!-- END HEAD -->
<div class="col-sm-12">
    <?php include("../../config-general/mensajes-informativos.php"); ?>
    <div class="panel">
        <form name="formularioGuardar" action="../compartido/informes-documentos-inscripcion.php" method="post" target="_blank">
            <div class="form-group row">
                <div class="col-sm-8">
                    <label for="selectEstudiantes" class="control-label">Seleccione el estudiante</label>
                    <select id="selectEstudiantes" class="form-control  select2" name="estudiante" multiple required>
                        <option value="">Seleccione una opción</option>
                        <?php

                        Aspirantes::foreignKey(Aspirantes::INNER, [
                            "asp_id"	=> 'mat_solicitud_inscripcion'
                        ]);
                        Matriculas_Documentos::foreignKey(Matriculas_Documentos::INNER, [
                            "matd_matricula" => 'mat_id',
                            "institucion" => 'matri.institucion',
                            "year" => 'matri.year'
                        ]);

                        $predicado = [
                            "institucion"               => $_SESSION['idInstitucion'],
                            "year"                      => $_SESSION["bd"],
                            Matricula::OTHER_PREDICATE        => "mat_estado_matricula IN (".EN_INSCRIPCION.",".MATRICULADO.")",
                        ];

                        $opcionesConsulta = Matricula::SelectJoin(
                            $predicado,
                            "mat_id, mat_nombres, mat_nombre2, mat_primer_apellido, mat_segundo_apellido",
                            [
                                Aspirantes::class,
                                Matriculas_Documentos::class
                            ],
                            "",
                            "",
                            "",
                            "asp_id DESC"
                        );

                        foreach ($opcionesConsulta as $opcionesDatos) {
                        ?>
                            <option value="<?= $opcionesDatos['mat_id']; ?>">
                                <?= "[" . $opcionesDatos['mat_id'] . "] " . Estudiantes::NombreCompletoDelEstudiante($opcionesDatos); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <span style="color: darkblue;">Seleccione solo una opción de este listado.</span>
                </div>
            </div>
            <input type="submit" class="btn btn-primary" value="Consultar Documentación">&nbsp;
        </form>
    </div>
</div>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<script>
    // Agregar el evento onchange al select
    var miSelect = document.getElementById('selectEstudiantes');
    miSelect.onchange = function() {
        limitarSeleccion(this);
    };
</script>