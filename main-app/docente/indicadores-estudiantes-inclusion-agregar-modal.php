<?php $idPaginaInterna = 'DC0150'; 

$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
} ?>

<div class="modal fade bd-example-modal-lg" id="<?= $idModal; ?>" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="max-width: 1350px!important;" role="document">
		<div class="modal-content shadow" style="max-width: 1350px!important;">
            <div class="panel">
                <header class="panel-heading panel-heading-purple"><?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </header>
                <div class="panel-body">
                    <form >
                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Descripción</label>
                            <div class="col-sm-10">
                                <input type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" <?= $disabledPermiso; ?>>
                            </div>
                        </div>
                        <?php  
                            if (Modulos::validarPermisoEdicion()) {
                               echo " <button  class='btn  btn-info' style='text-transform:uppercase' id='btnSubmitSave'>
                                    <i class='fa fa-save' aria-hidden='true'></i>{$frases[419][$datosUsuarioActual['uss_idioma']]}
                                </button>";
                            } 
                        ?>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn btn-danger">
                    <i class="fa fa fa-window-close"></i> Cerrar
                </a>
            </div>
        </div>
    </div>
</div>