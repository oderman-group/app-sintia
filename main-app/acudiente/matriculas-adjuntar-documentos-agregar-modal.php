<?php $idPaginaInterna = 'AC0039'; 

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
                    <form enctype="multipart/form-data" id="formAdjuntarDocumento" >
                        <div class="form-group row d-none"><label class="col-sm-2 col-form-label">OpcionForm</label>
                            <div class="col-sm-10"><input type="text" class="form-control" id="opcion" name="opcion" value="adjuntar_documento_estudiante"></div>
                        </div> 
                        <input type="text" class="form-control d-none" id="txtIdDocumento" name="txtIdDocumento" >
                        <div class="form-group row">
                            <label class="col-sm-2 control-label"><?= $frases[127][$datosUsuarioActual['uss_idioma']]; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="txtTitulo" id="txtTitulo" class="form-control" <?= $disabledPermiso; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Descripción</label>
                            <div class="col-sm-10">
                                <input type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" <?= $disabledPermiso; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label"><?= $frases[326][$datosUsuarioActual['uss_idioma']]; ?></label>
                            <div class="col-sm-8">
                                <input type="file" name="uplDocumento" id="uplDocumento" onChange="validarPesoArchivo(this)" accept=".png, .jpg, .jpeg, .pdf, .docx, .doc" class="form-control" <?= $disabledPermiso; ?>>
                            </div>
                            <a class="col-sm-2 d-none" id="txtVerDocumento" target="_blank"> Ver documento</a>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label"><?= $frases[173][$datosUsuarioActual['uss_idioma']]; ?></label>
                            <div class="input-group spinner col-sm-10">
                                <label class="switchToggle">
                                    <input type="checkbox" name="chkVisible" id="chkVisible" <?= $disabledPermiso; ?>>
                                    <span class="slider red round"></span>
                                </label>
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