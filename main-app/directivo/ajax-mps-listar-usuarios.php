<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
$instiConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".instituciones WHERE ins_id='".$_POST['insti']."'");
$insti = mysqli_fetch_array($instiConsulta, MYSQLI_BOTH);
$year= date('Y');
?>
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="form-group row">
    <label class="col-sm-2 control-label">Usuario Responsable</label>
    <div class="col-sm-4">
        <select class="form-control select2" style="width: 100%;"  name="responsable" id="responsable" aucomplete="off">
            <?php
            if(!empty($_POST['responsable'])){
                $consultaExiste = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$_POST['responsable']."'");
                $existe = mysqli_fetch_array($consultaExiste, MYSQLI_BOTH);

                if(!is_null($existe)){
                    $nombre = UsuariosPadre::nombreCompletoDelUsuario($existe);
            ?>	
            <option value="<?=$existe['uss_id'];?>" selected><?=$nombre." - ".$existe['pes_nombre'];?></option>
            <?php
                }
            }
            ?>	
        </select>
    </div>
</div>
<script>          
    $(document).ready(function() {
        $('#responsable').select2({
        placeholder: 'Seleccione el usuario...',
        theme: "bootstrap",
            ajax: {
                type: 'GET',
                url: 'ajax-listar-usuarios.php?year=<?=$year?>',
                processResults: function(data) {
                    data = JSON.parse(data);
                    return {
                        results: $.map(data, function(item) {                                  
                            return {
                                id: item.value,
                                text: item.label
                            }
                        })
                    };
                }
            }
        });
    });
</script>

<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
