<?php
include("session.php");
$idPaginaInterna = 'DT0082';
require_once("../class/Estudiantes.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
?>
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<style>
    .select2 {
        width: 100% !important;
    }
</style>


<div class="col-sm-12">

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
        <header class="panel-heading panel-heading-purple">Certificado por áreas</header>
        <div class="panel-body">
            <form action="estudiantes-formato-certificado.php" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Estilo de certificado</label>
                    <div class="col-sm-2">
                        <select class="form-control  select2" id="tipoCertificado" name="certificado" onchange="cambiarTipoCertificado()" <?=$disabledPermiso;?>>
                            <option value="1" <?php if($config['conf_certificado']==1){ echo "selected";} ?>>Certificado 1</option>
                            <option value="2" <?php if($config['conf_certificado']==2){ echo "selected";} ?>>Certificado 2</option>
                            <option value="3" <?php if($config['conf_certificado']==3){ echo "selected";} ?>>Certificado 3</option>
                        </select>
                    </div>
                    <button type="button" titlee="Ver formato certificado" class="btn btn-sm" data-toggle="popoverCertificado" ><i class="fa fa-eye"></i></button>
                    <script>
                            $(document).ready(function(){
                            $('[data-toggle="popoverCertificado"]').popover({
                                html: true, // Habilitar contenido HTML
                                content: function () {
                                    valor = document.getElementById("tipoCertificado");
                                return '<div id="myPopover" class="popover-content"><label id="lbl_tipoCert">Estilo Certificado '+valor.value+'</label>'+
                                '<img id="img-certificado" src="../files/images/certificados/tipo'+valor.value+'.png" class="w-100" />'+                                                       
                                '</div>';}
                                });                                                    
                            });
                            function cambiarTipoCertificado(){  
                                var imagen_boletin = document.getElementById('img-certificado'); 
                                if(imagen_boletin){                                                     
                                var valor = document.getElementById("tipoCertificado");  
                                var lbl_tipoCert = document.getElementById('lbl_tipoCert');
                                imagen_boletin.src ="../files/images/certificados/tipo"+valor.value+".png";
                                lbl_tipoCert.textContent='Estilo Certificado '+valor.value;
                                }
                            }
                    </script>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Estudiante</label>
                    <div class="col-sm-9">
                        <select id="selectEstudiantes1" class="form-control  select2" name="id" multiple required>
                            <option value=""></option>
                            <?php
                            $c = Estudiantes::listarEstudiantesEnGrados('', '');
                            while ($r = mysqli_fetch_array($c, MYSQLI_BOTH)) {
                            ?>
                                <option value="<?php echo $r['mat_id']; ?>"><?= Estudiantes::NombreCompletoDelEstudiante($r) ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <span style="color: darkblue;">Seleccione solo una opción de este listado.</span>
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
                        <label class="col-sm-2 control-label">Estampilla o referente de pago</label>
                        <div class="col-sm-9">
                            <input type="text" name="estampilla" value="">
                        </div>
                    </div>
                <?php } ?>

                <input type="submit" class="btn btn-primary" value="Generar Certificado">
            </form>
        </div>
    </div>

    <div class="panel">
        <header class="panel-heading panel-heading-purple">Certificado por materias</header>
        <div class="panel-body">
            <form action="../compartido/matricula-certificado.php" method="post" class="form-horizontal" enctype="multipart/form-data" target="_blank">

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Estudiante</label>
                    <div class="col-sm-9">
                        <select id="selectEstudiantes2" class="form-control  select2" name="id" multiple required>
                            <option value=""></option>
                            <?php
                            $c = Estudiantes::listarEstudiantesEnGrados('', '');
                            while ($r = mysqli_fetch_array($c, MYSQLI_BOTH)) {
                            ?>
                                <option value="<?php echo $r['mat_id']; ?>"><?= Estudiantes::NombreCompletoDelEstudiante($r) ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <span style="color: darkblue;">Seleccione solo una opción de este listado.</span>
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

                <input type="submit" class="btn btn-primary" value="Generar Certificado">
            </form>
        </div>
    </div>
</div>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>

<script>
// Agregar el evento onchange al select
var miSelect = document.getElementById('selectEstudiantes1');
miSelect.onchange = function() {
    limitarSeleccion(this);
};

// Agregar el evento onchange al select
var miSelect = document.getElementById('selectEstudiantes2');
miSelect.onchange = function() {
    limitarSeleccion(this);
};
</script>