<script type="application/javascript">

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

			position: 'bottom-right',
            showHideTransition: 'slide',

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

	/**
     * Esta funcion genera una alerta validadno la nota ingresada
     * 
     * @param nota
     * @return boolean 
     */
function alertValidarNota(nota){	
	if (nota > <?=$config[4];?> || isNaN(nota) || nota < <?=$config[3];?>) {
		Swal.fire('Nota '+nota+' no es valida','Ingrese un valor numérico entre <?=$config[3];?> y <?=$config[4];?>')
		return true;
	} else {
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

		Swal.fire(msj);

		dato.value = '';

	}	

												

}	



	
/*
function notificaciones(){

	var usuario = '<?=$_SESSION["id"];?>';

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
*/
	



function mensajes(){

	var usuario = '<?=$_SESSION["id"];?>';

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

window.onload = mensajes();			

	


<?php 
if(isset($_GET["idE"])){
?>

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

//setInterval('realizando()',20000);

	

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

//setInterval('finalizado()',20000);

	

window.onload = realizando();

window.onload = finalizado();

<?php }?>




<?php

//Mostrar anuncio publicitario

if(!empty($publicidadPopUp['pubxub_id']) and $numMostrarPopUp<$publicidadPopUp['pubxub_muestras_popup']){

	$tiempoMS = 3000;

	if( !empty($publicidadPopUp['pubxub_inicio_popup'])  and $publicidadPopUp['pubxub_inicio_popup']>=1000) $tiempoMS = $publicidadPopUp['pubxub_inicio_popup'];

	

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

	$('#boton-cerrar-licencia').click(function(){                    

		localStorage.setItem("licencia", 1);

	});

	$('#boton-cerrar-licencia-2').click(function(){                    

		localStorage.setItem("licencia", 1);

	});

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
	

	$('#boton-cerrar-modalTerminos').click(function(){                    

    	localStorage.setItem("modalTerminos", 1);

    });
	

	$('#boton-cerrar-modalTratamientos').click(function(){                    

    	localStorage.setItem("modalTratamientos", 1);

    });
	

	$('#boton-cerrar-modalPoliticas').click(function(){                    

    	localStorage.setItem("modalPoliticas", 1);

    });
	

	$('#boton-cerrar-contrato').click(function(){                    

    	localStorage.setItem("modalContratos", 1);

    });

	$('#boton-cerrar-asignaciones').click(function(){                    
		localStorage.setItem("asignaciones", 1);
	});

	$('#boton-cerrar-asignaciones-2').click(function(){                    
		localStorage.setItem("asignaciones", 1);
	});

});	


document.addEventListener("DOMContentLoaded", function() {
<?php

//Mostrar modal de cumpleaños

if(!empty($cumpleUsuario['agno'])){

?> 

	if(localStorage.getItem("estado")!=1){

		function mostrarModalCumple(){$("#modalCumple").modal("show");}

		$(document).ready(function() {
			mostrarModalCumple();
		});

	}

	

<?php }?>

	

<?php

//Mostrar modal para solicitar comentario sobre la plataforma

if($datosUsuarioActual['uss_preguntar_animo']==1){	

?>	

	if(localStorage.getItem("comentario")!=1){

		function mostrarModalComentario(){$("#modalComentario").modal("show");}

		$(document).ready(function() {
			mostrarModalComentario();
		});

	}

<?php }?>

	

<?php

//Mostrar modal de DEUDA a DIRECTIVOS

if($config['conf_deuda']==1 and $datosUsuarioActual['uss_tipo']==5){

?>	

	if(localStorage.getItem("modalDeuda") == null){

		function mostrarModalDeuda(){$("#modalDeuda").modal("show");}

		$(document).ready(function() {
			mostrarModalDeuda();
		});

	}

<?php }?>	

<?php

/* Mostrar renovacion de licencia */

if($datosUsuarioActual['uss_tipo']==5 || $datosUsuarioActual['uss_tipo']==1){

	?>	
	
		if(localStorage.getItem("licencia")!=1){
	
			function mostrarModalLicencia(){$("#modalLicencia").modal("show");}
			$(document).ready(function() {
				mostrarModalLicencia();
			});
	
		}
	
	<?php }?>

<?php if($numAsignacionesEncuesta > 0 && ($idPaginaInterna != 'DC0146' && $idPaginaInterna != 'AC0038' && $idPaginaInterna != 'ES0062' && $idPaginaInterna != 'DT0324' && $idPaginaInterna != 'CM0060')){ ?>	
	if(localStorage.getItem("asignaciones")!=1){
		function mostrarModalAsignaciones() {
			$("#modalAsignaciones").modal("show");
		}
		$(document).ready(function() {
			mostrarModalAsignaciones();
		});
	}
<?php }?>

/* Mostrar términos y condiciones */
<?php
	for($i=1;$i<=3;$i++){

		$datosTerminos = Plataforma::mostrarModalTerminos($i);

		if($datosTerminos['ttp_visible']==='SI'){

			switch($datosTerminos['ttp_id']){
				case 1:
					$modal="modalTerminos";
					break;
				case 2:
					$modal="modalTratamientos";
					break;
				case 3:
					$modal="modalPoliticas";
					break;
			}

			$aceptacion= mysqli_query($conexion, "SELECT ttpxu_fecha_aceptacion FROM ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios WHERE ttpxu_id_usuario='".$idSession."' AND ttpxu_id_termino_tratamiento_politicas='".$datosTerminos['ttp_id']."' AND ttpxu_id_institucion='".$config['conf_id_institucion']."'");
			$datosAceptacion = mysqli_fetch_array($aceptacion, MYSQLI_BOTH);

			//Condición para mostrar o no el modal de T&C
			if(empty($datosAceptacion[0]) AND $datosTerminos['ttp_fecha_modificacion'] > $datosAceptacion[0]){
?>
	if(localStorage.getItem("<?=$modal;?>")!=1){
		function mostrar<?=$modal;?>(){$("#<?=$modal;?>").modal("show");}
		$(document).ready(function() {
			mostrar<?=$modal;?>();
		});
	}
<?php }}} ?>

<?php
//Mostrar modal de ACEPTACION DE CONTRATO a DIRECTIVOS
if($datosUsuarioActual['uss_tipo']==5){
?>	
	if(localStorage.getItem("modalContratos")!=1){
		function mostrarModalContrato(){
			$("#modalContrato").modal("show");
		}
		$(document).ready(function() {
			mostrarModalContrato();
		});
	}
<?php }?>
});


	

	

//Modal de mesnajes MarketPlace

function msjMarketplace(datos){

	$("#modalMsjMarketplace").modal("show");

	document.getElementById('asuntoMarketplace').value="MARKETPLACE: Sobre tu publicación de " + datos.title;

	document.getElementById('destinoMarketplace').value=datos.name;

}

	



	

function usuariosChat(){

	var usuario = '<?=$_SESSION["id"];?>';

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

/**
 * Esta función sirve para aplicar color instantaneo a la nota colocada
 * por el docente a los estudiantes
 * 
 * @param nota
 * @param idInput
 * 
 * return void
 */
function aplicarColorNota(nota, idInput) {

	var aplicarColor = null;
	
	if(nota >= <?=$config['conf_nota_minima_aprobar'];?>) {
		aplicarColor = "<?=$config['conf_color_ganada'];?>";
	} else {
		aplicarColor = "<?=$config['conf_color_perdida'];?>";
	}

	let input = document.getElementById(idInput);

	input.style.color = aplicarColor;

	return aplicarColor;

}

	

	

	

	

	

	

	

	

	

	

	

	

	

	

	



	

</script>