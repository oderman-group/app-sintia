let interval  = null;
let idRegistro = null;
let finishButton  = null;
let intento = 3;

document.querySelectorAll('.code-input').forEach((input, index, inputs) => {
  // Manejar el evento de pegar (paste)
  input.addEventListener('paste', (e) => {
      e.preventDefault(); // Prevenir el comportamiento por defecto

      // Obtener el texto pegado
      const pasteData = e.clipboardData.getData('text');

      // Validar que los datos sean números y de longitud correcta
      if (/^\d{6}$/.test(pasteData)) {
          // Dividir los caracteres y asignarlos a los inputs
          pasteData.split('').forEach((char, i) => {
              if (inputs[i]) {
                  inputs[i].value = char;
                  inputs[i].classList.add('filled');
              }
          });

          // Enfocar el último input después de pegar
          const lastFilledInput = inputs[Math.min(pasteData.length - 1, inputs.length - 1)];
          if (lastFilledInput) lastFilledInput.focus();

          verificarCodigo();
      } else {
          showMessage('Por favor, pega un código válido de 6 dígitos.', 'error');
      }
  });

  input.addEventListener('input', (e) => {
    // Solo permitir números
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
    
    // Agregar clase filled si tiene valor
    if (e.target.value.length === 1) {
      e.target.classList.add('filled');
      if (index < inputs.length - 1) {
        inputs[index + 1].focus(); // Saltar al siguiente campo automáticamente
      }
    } else {
      e.target.classList.remove('filled');
    }

    // Verificar si todos los campos están completos
    const enteredCode = Array.from(inputs).map(input => input.value).join('');
    if (enteredCode.length === 6) {
      verificarCodigo();
    }
  });

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && index > 0 && !e.target.value) {
      inputs[index - 1].focus();
      inputs[index - 1].classList.remove('filled');
    }
  });
  
  // Prevenir entrada de caracteres no numéricos
  input.addEventListener('keypress', (e) => {
    if (!/[0-9]/.test(e.key)) {
      e.preventDefault();
    }
  });
});

// Función para iniciar la cuenta regresiva
function startCountdown(durationInSeconds) {
  const contMinElement = document.getElementById('contMin');
  const textMinElement = document.getElementById('textMin');
  const intNuevoElement = document.getElementById('intNuevo');
  const enviarSMS = document.getElementById('enviarCodigoSMS');
  const timerContainer = document.getElementById('timerContainer');
  var colorCambio = intNuevoElement ? intNuevoElement.getAttribute('data-color-cambio') : '#41c4c4';
  let remainingTime = durationInSeconds;

  // Deshabilitar botones inicialmente
  if (intNuevoElement) {
    intNuevoElement.classList.add('disabled');
    intNuevoElement.onclick = null;
  }
  
  if (enviarSMS) {
    enviarSMS.classList.add('disabled');
    enviarSMS.onclick = null;
  }

  // Actualiza cada segundo
  interval = setInterval(() => {
    const minutes = Math.floor(remainingTime / 60);
    const seconds = remainingTime % 60;

    // Muestra el tiempo en formato MM:SS
    if (contMinElement) {
      contMinElement.innerHTML = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
    }

    if (textMinElement) {
      if (minutes === 1) {
        textMinElement.innerHTML = `minuto`;
      } else if (minutes === 0) {
        textMinElement.innerHTML = `segundos`;
      } else {
        textMinElement.innerHTML = `minutos`;
      }
    }

    // Advertencia cuando quedan 2 minutos
    if (remainingTime === 120 && timerContainer) {
      timerContainer.classList.add('timer-warning');
      showMessage('⚠️ Tu código expirará en 2 minutos.', 'info');
    }

    if (remainingTime === 0) {
      clearInterval(interval);
      
      // Marcar timer como expirado
      if (timerContainer) {
        timerContainer.classList.add('timer-expired');
      }

      if (intento >= 3) {
        notificarDirectivos();
      } else {
        // Habilitar botones de reenvío
        if (intNuevoElement) {
          intNuevoElement.classList.remove('disabled');
          intNuevoElement.onclick = function (e) {
            e.preventDefault();
            intento++;
            enviarCodigo();
          };
        }

        if (enviarSMS) {
          enviarSMS.classList.remove('disabled');
          enviarSMS.onclick = function (e) {
            e.preventDefault();
            enviarCodigoSMS();
          };
        }
        
        showMessage('⏱️ El código ha expirado. Puedes solicitar uno nuevo.', 'error');
      }
    }

    remainingTime -= 1;
  }, 1000);
}

function enviarCodigo() {
  var intputIdRegistro  = document.getElementById('idRegistro');
  var usuarioId         = document.getElementById('usuarioId').value;
  const intNuevoElement = document.getElementById('intNuevo');
  
  // Deshabilitar botón temporalmente
  intNuevoElement.classList.add('disabled');
  intNuevoElement.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';

  // Enviar el código al correo electrónico
  fetch('recuperar-clave-enviar-codigo.php?usuarioId=' + usuarioId + '&async=true', {
    method: 'GET'
  })
    .then(response => {
      // Verificar si la respuesta es JSON
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        throw new TypeError("La respuesta no es JSON válido. Verifica errores PHP.");
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        const emailElement = document.getElementById('emailCode');
        if (emailElement && data.usuarioEmail) {
          emailElement.innerHTML = data.usuarioEmail;
        }

        // Mostrar mensaje de éxito
        const intentosRestantes = 3 - intento;
        let mensaje = '✓ Código reenviado a tu correo electrónico.';
        
        if (intentosRestantes > 0) {
          mensaje += ` Te quedan <strong>${intentosRestantes} ${intentosRestantes === 1 ? 'intento' : 'intentos'}</strong>.`;
        }
        
        showMessage(mensaje, 'success');

        idRegistro = data.code ? data.code.idRegistro : (data.datosCodigo ? data.datosCodigo.idRegistro : '');
        if (intputIdRegistro) {
          intputIdRegistro.value = idRegistro;
        }

        clearInterval(interval);
        startCountdown(10 * 60); // Inicia la cuenta regresiva con 10 minutos
        
        // Restaurar botón
        intNuevoElement.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Reenviar al correo';
        intNuevoElement.classList.add('disabled');
        intNuevoElement.onclick = null;
      } else {
        showMessage(data.message || 'Error al reenviar el código.', 'error');
        intNuevoElement.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Reenviar al correo';
        intNuevoElement.classList.remove('disabled');
      }
    })
    .catch(error => {
      console.error('Error en enviarCodigo:', error);
      showMessage('Error: ' + error.message, 'error');
      intNuevoElement.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Reenviar al correo';
      intNuevoElement.classList.remove('disabled');
    });
}

function enviarCodigoSMS() {
  var intputIdRegistro  = document.getElementById('idRegistro');
  var usuarioId         = document.getElementById('usuarioId').value;
  const enviarSMS       = document.getElementById('enviarCodigoSMS');
  intento++;

  // Deshabilitar botón temporalmente
  enviarSMS.classList.add('disabled');
  enviarSMS.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando por SMS...';

  // Enviar el código por SMS
  fetch('enviar-codigo-sms.php?usuarioId=' + usuarioId, {
    method: 'GET'
  })
    .then(response => {
      // Verificar si la respuesta es JSON
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        throw new TypeError("La respuesta no es JSON válido. Verifica errores PHP.");
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        const emailElement = document.getElementById('emailCode');
        if (emailElement && data.telefono) {
          emailElement.innerHTML = data.telefono;
        }

        // Mostrar mensaje de éxito
        const intentosRestantes = 3 - intento;
        let mensaje = '✓ Código enviado por SMS a tu número registrado.';
        
        if (intentosRestantes > 0) {
          mensaje += ` Te quedan <strong>${intentosRestantes} ${intentosRestantes === 1 ? 'intento' : 'intentos'}</strong>.`;
        }
        
        showMessage(mensaje, 'success');

        idRegistro = data.code ? data.code.idRegistro : (data.datosCodigo ? data.datosCodigo.idRegistro : '');
        if (intputIdRegistro) {
          intputIdRegistro.value = idRegistro;
        }

        clearInterval(interval);
        startCountdown(10 * 60); // Inicia la cuenta regresiva con 10 minutos
        
        // Restaurar botón
        const numeroCelular = data.telefono ? data.telefono.replace(/[()\s-]/g, '') : '';
        const ultimos4 = numeroCelular ? numeroCelular.substr(-4) : '****';
        enviarSMS.innerHTML = '<i class="bi bi-phone"></i> Enviar por SMS (*** *** ' + ultimos4 + ')';
        enviarSMS.classList.add('disabled');
        enviarSMS.onclick = null;
      } else {
        showMessage(data.message || 'Error al enviar SMS.', 'error');
        enviarSMS.innerHTML = '<i class="bi bi-phone"></i> Enviar por SMS';
        enviarSMS.classList.remove('disabled');
      }
    })
    .catch(error => {
      console.error('Error en enviarCodigoSMS:', error);
      showMessage('Error: ' + error.message, 'error');
      enviarSMS.innerHTML = '<i class="bi bi-phone"></i> Enviar por SMS';
      enviarSMS.classList.remove('disabled');
    });
}

function verificarCodigo() {
  // Seleccionar todos los inputs
  const inputs = document.querySelectorAll('.code-input');
  const btnValidarCodigo = document.getElementById('btnValidarCodigo');
  const btnText = document.getElementById('btnText');
  const btnSpinner = document.getElementById('btnSpinner');
  const idRegistroElement = document.getElementById('idRegistro');
  
  if (!btnValidarCodigo || !btnText || !btnSpinner || !idRegistroElement) {
    console.error('Elementos requeridos no encontrados');
    return;
  }
  
  var idRegistro = idRegistroElement.value;

  // Verificar si todos los inputs están llenos
  let allFilled = true;
  let codigoIngresado = '';

  inputs.forEach(input => {
      if (input.value.trim() === '') {
          allFilled = false;
      }
      codigoIngresado += input.value.trim(); // Construir el código ingresado
  });

  if (!allFilled) {
    showMessage('Por favor completa todos los dígitos del código.', 'error');
    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.classList.add('error');
        setTimeout(() => input.classList.remove('error'), 500);
      }
    });
    return;
  }

  // Cambiar estado del botón
  btnValidarCodigo.disabled = true;
  btnValidarCodigo.classList.add('loading');
  btnText.textContent = 'Validando código...';
  btnSpinner.style.display = 'inline-block';

  fetch('validar-codigo.php?code=' + codigoIngresado + '&idRegistro=' + idRegistro, {
    method: 'GET'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        var usuarioId = document.getElementById('usuarioId').value;
        
        // Éxito
        btnValidarCodigo.classList.remove('loading');
        btnValidarCodigo.classList.add('success');
        btnText.textContent = '¡Código válido!';
        btnSpinner.style.display = 'none';
        
        // Marcar todos los inputs como correctos
        inputs.forEach(input => {
          input.classList.add('filled');
          input.classList.remove('error');
        });
        
        showMessage('✓ Verificación exitosa. Redirigiendo para crear tu nueva contraseña...', 'success');
        
        setTimeout(() => {
            window.location.href = 'recuperar-clave-restaurar.php?usuarioId=' + btoa(usuarioId);
        }, 2000);
      } else {
        // Error
        btnValidarCodigo.classList.remove('loading');
        btnValidarCodigo.disabled = false;
        btnText.textContent = 'Validar Código';
        btnSpinner.style.display = 'none';
        
        // Marcar inputs como error
        inputs.forEach(input => {
          input.classList.add('error');
          input.value = '';
          setTimeout(() => input.classList.remove('error'), 500);
        });
        
        showMessage(data.message || 'Código incorrecto. Intenta nuevamente.', 'error');
        
        // Focus en el primer input
        inputs[0].focus();
        
        // Habilitar reenvío
        clearInterval(interval);
        const intNuevoElement = document.getElementById('intNuevo');
        var colorCambio = intNuevoElement.getAttribute('data-color-cambio')
        intNuevoElement.classList.remove('disabled');
        intNuevoElement.onclick = function () {
          intento++;
          enviarCodigo();
        };
      }
    })
    .catch(error => {
      btnValidarCodigo.classList.remove('loading');
      btnValidarCodigo.disabled = false;
      btnText.textContent = 'Validar Código';
      btnSpinner.style.display = 'none';
      
      inputs.forEach(input => {
        input.classList.add('error');
        setTimeout(() => input.classList.remove('error'), 500);
      });
      
      showMessage('Error de conexión. Por favor intenta nuevamente o contacta soporte.', 'error');
    });
}

// Función para mostrar mensajes dinámicos
function showMessage(message, type) {
  const dynamicMessages = document.getElementById('dynamicMessages');
  if (!dynamicMessages) return;
  
  const iconMap = {
    error: 'exclamation-triangle',
    success: 'check-circle',
    info: 'info-circle'
  };
  
  const messageHtml = `
    <div class="alert-dynamic ${type} animate__animated animate__fadeIn" role="alert">
      <i class="bi bi-${iconMap[type]}"></i>
      <span>${message}</span>
    </div>
  `;
  
  dynamicMessages.innerHTML = messageHtml;
  
  // Auto-ocultar después de 8 segundos (excepto para éxito)
  if (type !== 'success') {
    setTimeout(() => {
      if (dynamicMessages.firstElementChild) {
        dynamicMessages.firstElementChild.classList.add('animate__fadeOut');
        setTimeout(() => {
          dynamicMessages.innerHTML = '';
        }, 500);
      }
    }, 8000);
  }
}

function cambiarTipoInput(id, icoVer) {
  var campo = document.getElementById(id);
  var divIcoVer = document.getElementById(icoVer);

  if (campo.type === "password") {
      campo.type = "text";
      divIcoVer.classList.remove("bi-eye");
      divIcoVer.classList.add("bi-eye-slash");
  } else {
      campo.type = "password";
      divIcoVer.classList.remove("bi-eye-slash");
      divIcoVer.classList.add("bi-eye");
  }
}

function validarClaveNueva(enviada) {
  var clave = enviada.value;
  var regex = /^[A-Za-z0-9.$*]{8,20}$/;
  document.getElementById("confirPassword").value = '';
  $("#respuestaConfirmacionClaveNueva").html('');
  disableButton("btnEnviar");

  if (regex.test(clave)) {
      document.getElementById("respuestaClaveNueva").style.color = 'green';
      document.getElementById("respuestaClaveNueva").style.display = 'block';
      $("#respuestaClaveNueva").html('Contraseña Valida');
  } else {
      document.getElementById("respuestaClaveNueva").style.color = 'red';
      document.getElementById("respuestaClaveNueva").style.display = 'block';
      $("#respuestaClaveNueva").html('La clave no cumple con todos los requerimientos:<br>- Debe tener entre 8 y 20 caracteres.<br>- Solo se admiten caracteres de la a-z, A-Z, números(0-9) y los siguientes simbolos(. y $).');
  }
}

function claveNuevaConfirmar(enviada) {
  var valueConfirmar = enviada.value;
  var claveNueva = document.getElementById("password");

  if (valueConfirmar==claveNueva.value) {
      document.getElementById("respuestaConfirmacionClaveNueva").style.color = 'green';
      document.getElementById("respuestaConfirmacionClaveNueva").style.display = 'block';
      $("#respuestaConfirmacionClaveNueva").html('Las Contraseñas Coinciden');
      enableButton("btnEnviar");
  } else {
      document.getElementById("respuestaConfirmacionClaveNueva").style.color = 'red';
      document.getElementById("respuestaConfirmacionClaveNueva").style.display = 'block';
      $("#respuestaConfirmacionClaveNueva").html('Las Contraseñas No Coinciden');
      disableButton("btnEnviar");
  }
}

function disableButton(btn) {
    finishButton = document.getElementById(btn);
    finishButton.classList.add('disabled');
    finishButton.style.pointerEvents = 'none';
    finishButton.style.opacity = '0.5';
}

function enableButton(btn) {
    finishButton = document.getElementById(btn);
    finishButton.classList.remove('disabled');
    finishButton.style.pointerEvents = 'auto';
    finishButton.style.opacity = '1';
}

function notificarDirectivos() {
  var usuarioId         = document.getElementById('usuarioId').value;

  // Enviar el código al correo electrónico
  fetch('recuperar-clave-notificar-directivos.php?usuarioId=' + usuarioId, {
    method: 'GET'
  })
    .then(response => response.json())
    .then(data => {
      disableButton("btnValidarCodigo");
      const message = document.getElementById('message');
      message.style.visibility = 'visible';

      if (data.success) {
        message.classList.add('alert-success');

        setTimeout(() => {
            window.location.href = 'index.php';
        }, 5000);
      } else {
        message.classList.add('alert-danger');
      }

      message.classList.add('animate__animated', 'animate__flash', 'animate__repeat-2');
      message.innerHTML = data.message;
    });
}