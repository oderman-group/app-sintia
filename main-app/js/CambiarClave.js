
function cambiarTipoInput(id, icoVer) {
    var campo = document.getElementById(id);
    var divIcoVer = document.getElementById(icoVer);

    if (campo.type === "password") {
        campo.type = "text";
        divIcoVer.classList.remove("fa-eye");
        divIcoVer.classList.add("fa-eye-slash");
    } else {
        campo.type = "password";
        divIcoVer.classList.remove("fa-eye-slash");
        divIcoVer.classList.add("fa-eye");
    }
}

function validarClaveActual(enviada){
    var clave = CryptoJS.SHA1(enviada.value);
    var claveActual = enviada.getAttribute('data-clave-actual');
    var respuesta = document.getElementById("respuestaClaveActual");

    if (clave == claveActual) {
        respuesta.className = 'password-feedback success';
        document.getElementById("btnEnviar").style.display = 'inline-flex';
        $("#respuestaClaveActual").html('<i class="fa fa-check-circle"></i> Contraseña Correcta');
    } else {
        respuesta.className = 'password-feedback error';
        document.getElementById("btnEnviar").style.display = 'none';
        $("#respuestaClaveActual").html('<i class="fa fa-times-circle"></i> La contraseña actual es incorrecta, por favor verifique y vuelva a intentar');
    }
}

function validarClaveNueva(enviada) {
    var clave = enviada.value;
    var regex = /^[A-Za-z0-9.$*]{8,20}$/;
    var respuesta = document.getElementById("respuestaClaveNueva");
    document.getElementById("claveNuevaDos").value = '';
    $("#respuestaConfirmacionClaveNueva").html('').removeClass('password-feedback success error');

    if (regex.test(clave)) {
        respuesta.className = 'password-feedback success';
        document.getElementById("btnEnviar").style.display = 'inline-flex';
        $("#respuestaClaveNueva").html('<i class="fa fa-check-circle"></i> Contraseña Válida');
    } else {
        respuesta.className = 'password-feedback error';
        document.getElementById("btnEnviar").style.display = 'none';
        $("#respuestaClaveNueva").html('<i class="fa fa-times-circle"></i> La clave no cumple con todos los requerimientos:<br><small>• Debe tener entre 8 y 20 caracteres<br>• Solo se admiten: a-z, A-Z, 0-9, . y $</small>');
    }
}

function claveNuevaConfirmar(enviada) {
    var valueConfirmar = enviada.value;
    var claveNueva = document.getElementById("claveNueva");
    var respuesta = document.getElementById("respuestaConfirmacionClaveNueva");

    if (valueConfirmar==claveNueva.value) {
        respuesta.className = 'password-feedback success';
        document.getElementById("btnEnviar").style.display = 'inline-flex';
        $("#respuestaConfirmacionClaveNueva").html('<i class="fa fa-check-circle"></i> Las Contraseñas Coinciden');
    } else {
        respuesta.className = 'password-feedback error';
        document.getElementById("btnEnviar").style.display = 'none';
        $("#respuestaConfirmacionClaveNueva").html('<i class="fa fa-times-circle"></i> Las Contraseñas No Coinciden');
    }
}