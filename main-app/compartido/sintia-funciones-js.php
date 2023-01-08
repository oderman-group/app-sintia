<script type="application/javascript">

function hayInternet(){

	if(navigator.onLine) {

		if(localStorage.getItem("internet")==0){

			document.getElementById("siInternet").style.display="block";

		}

		

		localStorage.setItem("internet", 1);

		document.getElementById("noInternet").style.display="none";

	} 

	else {

		localStorage.setItem("internet", 0);

		document.getElementById("noInternet").style.display="block";

	}

}

setInterval('hayInternet()', 1000);

	

function cupoNo(dato){

	var opcion = dato;

	document.getElementById("motivoNo").style.display="none";

	if(opcion==1){

		document.getElementById("motivoNo").style.display="none";

		document.formularioCupo.motivo.required="";

	}else{

		document.getElementById("motivoNo").style.display="block";

		document.formularioCupo.motivo.required="required";

	}

}

	

function avisoBancoDatos(dato){	

	valor = dato.value;

	

	document.getElementById("infoCero").style.display="block";

	document.getElementById("infoCeroDos").style.display="block";

	

	//AGREGAR INDICADORES

	if(document.formularioGuardar.id.value==9){

		document.formularioGuardar.contenido.required="required";

		if(document.formularioGuardar.configInd.value==1){

			document.formularioGuardar.valor.required="required";

		}

	}

	

	//AGREGAR CALIFICACIONES

	if(document.formularioGuardar.id.value==10){

		document.formularioGuardar.contenido.required="required";

		document.formularioGuardar.fecha.required="required";

		document.formularioGuardar.indicador.required="required";

		if(document.formularioGuardar.configCal.value==1){

			document.formularioGuardar.valor.required="required";

		}

	}

	

	//AGREGAR PREGUNTAS A LAS EVALUACIONES

	if(document.formularioGuardar.id.value==7){

		document.formularioGuardar.contenido.required="required";

		document.formularioGuardar.valor.required="required";

	}

	

	//AGREGAR EVALUACIONES

	if(document.formularioGuardar.id.value==23){

		document.formularioGuardar.titulo.required="required";

		document.formularioGuardar.desde.required="required";

		document.formularioGuardar.hasta.required="required";

	}

	

	

	//Si escogió del banco de datos

	if(valor!=0){

		document.getElementById("infoCero").style.display="none";

		document.getElementById("infoCeroDos").style.display="none";

		

		//AGREGAR INDICADORES

		if(document.formularioGuardar.id.value==9){

			document.formularioGuardar.contenido.required="";

			if(document.formularioGuardar.configInd.value==1){

				document.formularioGuardar.valor.required="";

			}

		}

		

		//AGREGAR CALIFICACIONES

		if(document.formularioGuardar.id.value==10){

			document.formularioGuardar.contenido.required="";

			document.formularioGuardar.fecha.required="";

			document.formularioGuardar.indicador.required="";

			if(document.formularioGuardar.configCal.value==1){

				document.formularioGuardar.valor.required="";

			}

		}

		

		//AGREGAR PREGUNTAS A LAS EVALUACIONES

		if(document.formularioGuardar.id.value==7){

			document.formularioGuardar.contenido.required="";

			document.formularioGuardar.valor.required="";

		}

		

		//AGREGAR EVALUACIONES

		if(document.formularioGuardar.id.value==23){

			document.formularioGuardar.titulo.required="";

			document.formularioGuardar.desde.required="";

			document.formularioGuardar.hasta.required="";

		}

				

		$.toast({

			heading: 'Banco de datos',  

			text: 'Recuerda que al escoger una opción del banco de datos, ésta será tomada como prioritaria y será la que el sistema use.',

			position: 'mid-center',

			loaderBg:'#ff6849',

       		icon: 'warning',

			hideAfter: 5000, 

			stack: 6

		});

	}

}

	

function tipoPregunta(dato){

	var tipo = dato;

	

	if(tipo==1){

		document.getElementById("multiple").style.display="block";

		document.getElementById("verdadero").style.display="none";

		document.getElementById("archivo").style.display="none";

		

		document.getElementById("opr1").checked="checked";

		document.getElementById("opr2").checked="";

		document.getElementById("opr3").checked="";

	}

	

	if(tipo==2){

		document.getElementById("multiple").style.display="none";

		document.getElementById("verdadero").style.display="block";

		document.getElementById("archivo").style.display="none";

		

		document.getElementById("opr1").checked="";

		document.getElementById("opr2").checked="checked";

		document.getElementById("opr3").checked="";

	}

	if(tipo==3){

		document.getElementById("multiple").style.display="none";

		document.getElementById("verdadero").style.display="none";

		document.getElementById("archivo").style.display="block";

		

		document.getElementById("opr1").checked="";

		document.getElementById("opr2").checked="";

		document.getElementById("opr3").checked="checked";

	}

}

	

function tipoFolder(dato){

	var tipo = dato.value;

	document.getElementById("archivo").style.display="none";

	document.getElementById("nombreCarpeta").style.display="block";

	document.formularioGuardar.nombre.required="required";

	if(tipo==2){

		document.getElementById("archivo").style.display="block";

		document.getElementById("nombreCarpeta").style.display="none";

		document.formularioGuardar.nombre.required="";

	}

	

}	

		

function deseaRegresar(dato){

	var url = dato.name;

	var v = confirm('Si va a regresar verifique que no haya hecho cambios en esta página y estén sin guardar. Desea regresar de todas formas?');

	if(v == true)

	{	

		window.location.href=url;

	}else{

		return false;

	}

}

	

function deseaEliminar(dato){

	//alert(typeof dato.title);

	if(dato.title !== ''){

		let variable = (dato.title);

		var varObjet = JSON.parse(variable);

		console.log(varObjet);

		var input = document.getElementById(parseInt(varObjet.idInput));

	}



	var v = confirm('Al eliminar este registro es posible que se eliminen otros registros que estén relacionados. Desea continuar bajo su responsabilidad?');

	var url = dato.name;

	var id = dato.id;

	var registro = document.getElementById("reg"+id);

	var evaPregunta = document.getElementById("pregunta"+id);

	var publicacion = document.getElementById("PUB"+id);

	console.log("id:"+id);

	

	if(v == true)

	{	

		if(id!=""){

			axios.get(url)

			  .then(function (response) {

				// handle success

				console.log("El registro fue eliminado correctamente.");

				//divRespuesta.innerHTML = response.data;

				

				if(varObjet.tipo === 1){registro.style.display="none";}



				if(varObjet.tipo === 2){

				   document.getElementById(id).style.display="none";

				   input.value="";

				}

				

				if(varObjet.tipo === 3){evaPregunta.style.display="none";}

				

				if(varObjet.tipo === 4){publicacion.style.display="none";}

				

				$.toast({

					heading: 'Acción realizada', text: 'El reigstro fue eliminado correctamente.', position: 'mid-center',

					loaderBg:'#26c281', icon: 'success', hideAfter: 5000, stack: 6

				});

				

			  })

			  .catch(function (error) {

				// handle error

				console.log(error);

			  });



		}else{

			window.location.href=url;

		}

		

	}else{

		return false;

	}

}

	

function archivoPeso(dato){

	var maxPeso = <?=$config['conf_max_peso_archivos'];?>;

	var msj;

	var tama = parseFloat(dato.files[0].size)/1048576; //Estos son Bytes. 1MB = 1.048.576 Bytes

	var extension = dato.files[0].type;

	

	tama = Math.round(tama * 10) / 10;

	if(tama > maxPeso){

		msj = `Este archivo ${extension} pesa ${tama}MB. Lo ideal es que pese menos de ${maxPeso}MB. Intenta comprimirlo o reducir su tamaño.`;

		alert(msj);

		dato.value = '';

	}	

												

}	



	

function notificaciones(){

	var usuario = <?=$_SESSION["id"];?>;

	var consulta = 1;

	  $('#notificaciones').empty().hide().html("...").show(1);

		datos = "usuario="+(usuario)+

				"&consulta="+(consulta);

		$.ajax({

		   type: "POST",

		   url: "../compartido/ajax-notificaciones.php",

		   data: datos,

		   success: function(data){

			   $('#notificaciones').empty().hide().html(data).show(1);

		   }

		});



}

setInterval('notificaciones()',300000);

window.onload = notificaciones();

	



function mensajes(){

	var usuario = <?=$_SESSION["id"];?>;

	var consulta = 2;

	  $('#mensajes').empty().hide().html("...").show(1);

		datos = "usuario="+(usuario)+

				"&consulta="+(consulta);

		$.ajax({

		   type: "POST",

		   url: "../compartido/ajax-mensajes.php",

		   data: datos,

		   success: function(data){

			   $('#mensajes').empty().hide().html(data).show(1);

		   }

		});



}

setInterval('mensajes()',300000);

window.onload = mensajes();			

	



function realizando(){

	var consulta = 1;

	  $('#resp').empty().hide().html("...").show(1);

		datos = "eva="+$("#idE").val()+

				"&consulta="+(consulta);

			   $.ajax({

				   type: "POST",

				   url: "../compartido/ajax-evaluacion.php",

				   data: datos,

				   success: function(data){

				   $('#resp').empty().hide().html(data).show(1);

				   }

			   });



}

setInterval('realizando()',20000);

	

function finalizado(){

	var consulta = 2;

	  $('#fin').empty().hide().html("...").show(1);

		datos = "eva="+$("#idE").val()+

				"&consulta="+(consulta);

			   $.ajax({

				   type: "POST",

				   url: "../compartido/ajax-evaluacion.php",

				   data: datos,

				   success: function(data){

				   $('#fin').empty().hide().html(data).show(1);

				   }

			   });



}

setInterval('finalizado()',20000);

	

window.onload = realizando();

window.onload = finalizado();





<?php

//Mostrar anuncio publicitario

if($publicidadPopUp['pubxub_id']!="" and $numMostrarPopUp<$publicidadPopUp['pubxub_muestras_popup']){

	$tiempoMS = 3000;

	if($publicidadPopUp['pubxub_inicio_popup']!="" and $publicidadPopUp['pubxub_inicio_popup']>=1000) $tiempoMS = $publicidadPopUp['pubxub_inicio_popup'];

	

?>

	function mostrarModalPublicitario(){$("#modalAnuncios").modal("show");}

	setTimeout('mostrarModalPublicitario()',<?php echo $tiempoMS;?>);

<?php }?>

	

	

<?php

//Mostrar modal para solicitar datos

if($datosUsuarioActual['uss_solicitar_datos']==1){	

?>	

	function mostrarModalDatos(){$("#modalDatos").modal("show");}

	setTimeout('mostrarModalDatos()',1000);

<?php }else{?>



	$(document).ready(function() {

		

		if(localStorage.getItem("vGuiada")==1){

		   introJs().start();

		}



	});	

	

<?php	}?>





$(document).ready(function(){

    $('#boton-cerrar').click(function(){                    

    	localStorage.setItem("estado", 1);

    });

	

	$('#boton-cerrar-2').click(function(){                    

    	localStorage.setItem("estado", 1);

    });

	

	$('#boton-cerrar-comentario').click(function(){                    

    	localStorage.setItem("comentario", 1);

    });

	

	$('#boton-cerrar-modal-deuda').click(function(){                    

    	localStorage.setItem("modalDeuda", 1);

    });

	

	$('#boton-cerrar-modal-deuda2').click(function(){                    

    	localStorage.setItem("modalDeuda", 1);

    });

});	



<?php

//Mostrar modal de cumpleaños

if($cumpleUsuario['agno']!=""){

?> 

	if(localStorage.getItem("estado")!=1){

		function mostrarModalCumple(){$("#modalCumple").modal("show");}

		setTimeout('mostrarModalCumple()',2000);	

	}

	

<?php }?>

	

<?php

//Mostrar modal para solicitar comentario sobre la plataforma

if($datosUsuarioActual['uss_preguntar_animo']==1){	

?>	

	if(localStorage.getItem("comentario")!=1){

		function mostrarModalComentario(){$("#modalComentario").modal("show");}

		setTimeout('mostrarModalComentario()',60000);

	}

<?php }?>

	

<?php

//Mostrar modal de DEUDA a DIRECTIVOS

if($config['conf_deuda']==1 and $datosUsuarioActual['uss_tipo']==5){

?>	

	if(localStorage.getItem("modalDeuda") == null){

		function mostrarModalDeuda(){$("#modalDeuda").modal("show");}

		setTimeout('mostrarModalDeuda()',1000);

	}

<?php }?>	

	

<?php

/* Mostrar renovacion de licencia */

if($datosUsuarioActual['uss_tipo']==5){

?>	

	function mostrarModalLicencia(){$("#modalLicencia").modal("show");}

	setTimeout('mostrarModalLicencia()', 2000);

<?php }?>



/* Mostrar términos y condiciones */
function mostrarModalTerminos(){$("#modalTerminos").modal("show");}

setTimeout('mostrarModalTerminos()', 2000);


/* Mostrar TRATAMIENTOS DE DATOS */
function mostrarModalTratamientos(){$("#modalTratamientos").modal("show");}

setTimeout('mostrarModalTratamientos()', 2000);



/* Mostrar POLITICAS */
function mostrarModalPoliticas(){$("#modalPoliticas").modal("show");}

setTimeout('mostrarModalPoliticas()', 2000);
	

<?php
//Mostrar modal de ACEPTACION DE CONTRATO a DIRECTIVOS
if($datosUsuarioActual['uss_tipo']==5){
?>	

	function mostrarModalContrato(){$("#modalContrato").modal("show");}

	setTimeout('mostrarModalContrato()', 2000);

<?php }?>


function axiosAjax(datos){

	let url = datos.id;

	let divRespuestaNombre = 'RESP_'+datos.title;

	let divRespuesta = document.getElementById(divRespuestaNombre);

	

	axios.get(url)

	  .then(function (response) {

		// handle success

		console.log(response.data);

		divRespuesta.innerHTML = response.data;

	  })

	  .catch(function (error) {

		// handle error

		console.log(error);

	  })

	  .then(function () {

		// always executed

	  });

}

	

	

//Modal de mesnajes MarketPlace

function msjMarketplace(datos){

	$("#modalMsjMarketplace").modal("show");

	document.getElementById('asuntoMarketplace').value="MARKETPLACE: Sobre tu publicación de " + datos.title;

	document.getElementById('destinoMarketplace').value=datos.name;

}

	



	

function usuariosChat(){

	var usuario = <?=$_SESSION["id"];?>;

	var institucion = <?=$config['conf_id_institucion'];?>;

	  $('#listaUsuariosChat').empty().hide().html("...").show(1);

		datos = "usuario="+(usuario)+

				"&institucion="+(institucion);

		$.ajax({

		   type: "POST",

		   url: "../compartido/ajax-usuarios-chat.php",

		   data: datos,

		   success: function(data){

			   $('#listaUsuariosChat').empty().hide().html(data).show(1);

		   }

		});



}

	

	

	

	

	

	

	

	

	

	

	

	

	

	

	



	

</script>