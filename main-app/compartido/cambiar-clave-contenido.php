<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

<div class="col-sm-4">
    <div class="panel">
        <header class="panel-heading panel-heading-blue">Instrucciones</header>
        <div class="panel-body">
            <h4>Especificaciones</h4>
            <p><b>1.</b> Debe tener entre 8 y 20 caracteres.</p>
            <p><b>2.</b> Solo se admiten caracteres de la a-z, A-Z, números(0-9) y los siguientes simbolos <mark>(. y $)</mark>.</p>
        </div>
    </div>

    
</div>  

<div class="col-sm-8">
    <?php include("../../config-general/mensajes-informativos.php"); ?>
    <div class="card card-box">
        <div class="card-head">
            <header><?=$frases[253][$datosUsuarioActual['uss_idioma']];?></header>
        </div>
        <div class="card-body " id="bar-parent6">
            <form action="../compartido/clave-actualizar.php" method="post" enctype="multipart/form-data">
                <?php echo Csrf::campoHTML(); ?>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Contraseña actual</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="input-group-prepend" onclick="cambiarTipoInput('claveActual', 'icoVerActual')">
                                <span class="input-group-text"><i class="fas fa-eye" id="icoVerActual"></i></span>
                            </div>
                            <input type="password" name="claveActual" id="claveActual" oninput="validarClaveActual(this)" data-clave-actual="<?=$datosUsuarioActual['uss_clave']?>" class="form-control" required>
                        </div>
                    </div>
                    <span id="respuestaClaveActual" style="display:none"></span>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-2 control-label">Contraseña nueva</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="input-group-prepend" onclick="cambiarTipoInput('claveNueva', 'icoVerNueva')">
                                <span class="input-group-text"><i class="fas fa-eye" id="icoVerNueva"></i></span>
                            </div>
                            <input type="password" name="claveNueva" id="claveNueva" oninput="validarClaveNueva(this)" class="form-control" required>
                        </div>
                    </div>
                    <span id="respuestaClaveNueva" style="display:none"></span>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 control-label">Confirmar contraseña nueva</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="input-group-prepend" onclick="cambiarTipoInput('claveNuevaDos', 'icoVerNuevaDos')">
                                <span class="input-group-text"><i class="fas fa-eye" id="icoVerNuevaDos"></i></span>
                            </div>
                            <input type="password" name="claveNuevaDos" id="claveNuevaDos" oninput="claveNuevaConfirmar(this)" class="form-control" required>
                        </div>
                    </div>
                    <span id="respuestaConfirmacionClaveNueva" style="display:none"></span>
                </div>

                <button type="submit" class="btn  btn-info" id="btnEnviar">
                    <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                </button>
            </form>
        </div>
    </div>
</div>
<script src="../js/CambiarClave.js" ></script>