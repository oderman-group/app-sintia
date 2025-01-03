let interval  = null;
let idRegistro = null;
let finishButton  = null;
let intento = 1;

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
              }
          });

          // Enfocar el siguiente input después de pegar
          const lastFilledInput = inputs[Math.min(pasteData.length - 1, inputs.length - 1)];
          if (lastFilledInput) lastFilledInput.focus();

          verificarCodigo();
      } else {
          alert('Por favor, pega un código válido de 6 dígitos.');
      }
  });

  input.addEventListener('input', (e) => {
    if (e.target.value.length === 1 && index < inputs.length - 1) {
      inputs[index + 1].focus(); // Saltar al siguiente campo automáticamente
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
    }
  });
});

// Función para iniciar la cuenta regresiva
function startCountdown(durationInSeconds) {
  const message = document.getElementById('message');
  const contMinElement = document.getElementById('contMin');
  const textMinElement = document.getElementById('textMin');
  const intNuevoElement = document.getElementById('intNuevo');
  var colorCambio = intNuevoElement.getAttribute('data-color-cambio')
  let remainingTime = durationInSeconds;
  intNuevoElement.style.color = '#000';

  // Actualiza cada segundo
  interval = setInterval(() => {
    const minutes = Math.floor(remainingTime / 60); // Calcula los minutos
    const seconds = remainingTime % 60; // Calcula los segundos

    // Muestra el tiempo en formato MM:SS
    contMinElement.innerHTML = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;

    if (minutes === 1) {
      textMinElement.innerHTML = `minuto`;
    } else if (minutes === 0) {
      textMinElement.innerHTML = `segundos`;
    }

    if (remainingTime === 0) {
      clearInterval(interval); // Detén la cuenta regresiva al llegar a 0

      // Cambiar el color del texto
      intNuevoElement.style.color = colorCambio;
      intNuevoElement.onclick = function () {
        intento++;
        enviarCodigo();
      };
    }
  
    miFuncionConDelay(message, 'alert-success');

    remainingTime -= 1; // Reduce el tiempo restante en 1 segundo
  }, 1000);
}

async function miFuncionConDelay(element, alert = '') {
  await new Promise(resolve => setTimeout(resolve, 10000));
  element.style.visibility = 'hidden';
  element.innerHTML = '-';
  if (alert !== '') {
    element.classList.remove(alert);
  }
  element.classList.remove('animate__animated', 'animate__flash', 'animate__repeat-2');
}

function enviarCodigo() {
  var intputIdRegistro  = document.getElementById('idRegistro');
  var usuarioId         =   document.getElementById('usuarioId').value;

  // Enviar el código al correo electrónico
  fetch('recuperar-clave-enviar-codigo.php?usuarioId=' + usuarioId + '&async=true', {
    method: 'GET'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {

        // Mostrar mensaje si es un nuevo intento
        if (intento > 1) {
          const message = document.getElementById('message');

          message.innerHTML = 'Hemos enviado un nuevo código a tu correo electrónico,<br> si no ves el correo revisa tu carpeta de spam o<br> verifica que hayas ingresado bien tu correo electrónico.';
          message.style.visibility = 'visible';
          message.classList.add('alert-success', 'animate__animated', 'animate__flash', 'animate__repeat-2');
          miFuncionConDelay(message, 'alert-success');
        }

        idRegistro = data.code.idRegistro;
        intputIdRegistro.value = idRegistro;

        startCountdown(10 * 60); // Inicia la cuenta regresiva con 10 minutos
      } else {
        alert(data.message);
      }
    });
}

function verificarCodigo() {
  // Seleccionar todos los inputs
  const inputs = document.querySelectorAll('.code-input');
  const message = document.getElementById('message');
  const btnValidarCodigo = document.getElementById('btnValidarCodigo');
  var idRegistro =   document.getElementById('idRegistro').value;
  btnValidarCodigo.style.visibility = 'hidden';

  // Verificar si todos los inputs están llenos
  let allFilled = true;
  let codigoIngresado = '';

  inputs.forEach(input => {
      if (input.value.trim() === '') {
          allFilled = false;
      }
      codigoIngresado += input.value.trim(); // Construir el código ingresado
  });

  if (allFilled) {
      fetch('validar-codigo.php?code=' + codigoIngresado + '&idRegistro=' + idRegistro, {
        method: 'GET'
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            var usuarioId         =   document.getElementById('usuarioId').value;
            message.innerHTML = 'El proceso de verificación ha sido exitoso, en 2 segundos le redireccionaremos para restaurar su nueva contraseña.';
            message.style.visibility = 'visible';
            message.classList.add('alert-success', 'animate__animated', 'animate__flash', 'animate__repeat-2');
            setTimeout(() => {
                window.location.href = 'recuperar-clave-restaurar.php?usuarioId=' + btoa(usuarioId);
            }, 2000);
          } else {
            btnValidarCodigo.style.visibility = 'visible';
            message.innerHTML = data.message;
            message.style.visibility = 'visible';
            message.classList.add('alert-danger', 'animate__animated', 'animate__flash', 'animate__repeat-2');
            miFuncionConDelay(message, 'alert-danger');
            
            clearInterval(interval);
            const intNuevoElement = document.getElementById('intNuevo');
            var colorCambio = intNuevoElement.getAttribute('data-color-cambio')
            intNuevoElement.style.color = colorCambio;
            intNuevoElement.onclick = function () {
              intento++;
              enviarCodigo();
            };
          }
        })
        .catch(error => {
          btnValidarCodigo.style.visibility = 'visible';
          message.innerHTML = 'Error al validar el código: comunicate con un asesor.';
          message.style.visibility = 'visible';
          message.classList.add('alert-danger', 'animate__animated', 'animate__flash', 'animate__repeat-2');
          miFuncionConDelay(message, 'alert-danger');
        });
  } else {
    btnValidarCodigo.style.visibility = 'visible';
    message.innerHTML = 'Faltan llenar algunos campos.';
    message.style.visibility = 'visible';
    message.classList.add('alert-danger', 'animate__animated', 'animate__flash', 'animate__repeat-2');
    miFuncionConDelay(message, 'alert-danger');
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