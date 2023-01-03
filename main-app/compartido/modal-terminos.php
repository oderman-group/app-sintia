<?php
//Consultas a la BD
$terminos= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politica WHERE ttp_id=1");
$datosTerminos = mysqli_fetch_array($terminos, MYSQLI_BOTH);

$aceptacion= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios WHERE ttpxu_id_usuario='".$idSession."' AND ttpxu_id_termino_tratamiento_politicas='".$datosTerminos['ttp_id']."' AND ttpxu_id_institucion='".$config['conf_id_institucion']."'");
$datosAceptacion = mysqli_fetch_array($aceptacion, MYSQLI_BOTH);

//Condición para mostrar o no el modal de T&C
if(isset($datosAceptacion['ttpxu_fecha_aceptacion']) AND $datosTerminos['ttp_fecha_modificacion'] > $datosAceptacion['ttpxu_fecha_aceptacion'] and $datosTerminos['ttp_visible']==='SI'){
?>

<div class="modal fade" id="modalTerminos" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog"  style="max-width: 1350px!important;">
		  <div class="modal-content">
			  
			  <div class="modal-header">
				<h1 class="modal-title" align="center"><?=$datosTerminos['ttp_nombre'];?></h1>
				<a href="#" data-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-terminos"><i class="fa fa-window-close"></i></a>
			  </div>
			  
			 <div class="modal-body" align="center">
                               
			 <?=$datosTerminos['ttp_descripcion'];?>

			 </div>
			  
			 <div class="modal-footer">
			 	<form class="form-horizontal" action="../compartido/modal-terminos-guardar.php" method="post">
					<input type="hidden" name="idUsuario" value="<?=$idSession;?>">
					<input type="hidden" name="id" value="<?=$datosTerminos['ttp_id'];?>">
					<button type="submit" class="btn btn-info">Aceptar Terminos</button>
				</form>
		 	 </div>
			  
		  </div>
	   </div>
	</div>

<?php }?>

<?php
//Consultas a la BD
$terminos= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politica WHERE ttp_id=2");
$datosTerminos = mysqli_fetch_array($terminos, MYSQLI_BOTH);

$aceptacion= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios WHERE ttpxu_id_usuario='".$idSession."' AND ttpxu_id_termino_tratamiento_politicas='".$datosTerminos['ttp_id']."' AND ttpxu_id_institucion='".$config['conf_id_institucion']."'");
$datosAceptacion = mysqli_fetch_array($aceptacion, MYSQLI_BOTH);

//Condición para mostrar o no el modal de TRATAMIENTO DE DATOS
if(isset($datosAceptacion['ttpxu_fecha_aceptacion']) AND $datosTerminos['ttp_fecha_modificacion'] > $datosAceptacion['ttpxu_fecha_aceptacion'] and $datosTerminos['ttp_visible']==='SI'){
?>

<div class="modal fade" id="modalTratamientos" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog"  style="max-width: 1350px!important;">
		  <div class="modal-content">
			  
			  <div class="modal-header">
				<h1 class="modal-title" align="center"><?=$datosTerminos['ttp_nombre'];?></h1>
				<a href="#" data-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-terminos"><i class="fa fa-window-close"></i></a>
			  </div>
			  
			 <div class="modal-body" align="center">
                               
			 <?=$datosTerminos['ttp_descripcion'];?>

			 </div>
			  
			 <div class="modal-footer">
			 	<form class="form-horizontal" action="../compartido/modal-terminos-guardar.php" method="post">
					<input type="hidden" name="idUsuario" value="<?=$idSession;?>">
					<input type="hidden" name="id" value="<?=$datosTerminos['ttp_id'];?>">
					<button type="submit" class="btn btn-info">Aceptar Tratamiento de datos</button>
				</form>
		 	 </div>
			  
		  </div>
	   </div>
	</div>

<?php }?>

<?php
//Consultas a la BD
$terminos= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politica WHERE ttp_id=3");
$datosTerminos = mysqli_fetch_array($terminos, MYSQLI_BOTH);

$aceptacion= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios WHERE ttpxu_id_usuario='".$idSession."' AND ttpxu_id_termino_tratamiento_politicas='".$datosTerminos['ttp_id']."' AND ttpxu_id_institucion='".$config['conf_id_institucion']."'");
$datosAceptacion = mysqli_fetch_array($aceptacion, MYSQLI_BOTH);

//Condición para mostrar o no el modal de POLITICAS
if(isset($datosAceptacion['ttpxu_fecha_aceptacion']) AND $datosTerminos['ttp_fecha_modificacion'] > $datosAceptacion['ttpxu_fecha_aceptacion'] and $datosTerminos['ttp_visible']==='SI'){
?>

<div class="modal fade" id="modalPoliticas" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog"  style="max-width: 1350px!important;">
		  <div class="modal-content">
			  
			  <div class="modal-header">
				<h1 class="modal-title" align="center"><?=$datosTerminos['ttp_nombre'];?></h1>
				<a href="#" data-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-terminos"><i class="fa fa-window-close"></i></a>
			  </div>
			  
			 <div class="modal-body" align="center">
                               
			 <?=$datosTerminos['ttp_descripcion'];?>

			 </div>
			  
			 <div class="modal-footer">
			 	<form class="form-horizontal" action="../compartido/modal-terminos-guardar.php" method="post">
					<input type="hidden" name="idUsuario" value="<?=$idSession;?>">
					<input type="hidden" name="id" value="<?=$datosTerminos['ttp_id'];?>">
					<button type="submit" class="btn btn-info">Aceptar Politicas</button>
				</form>
		 	 </div>
			  
		  </div>
	   </div>
	</div>

<?php }?>