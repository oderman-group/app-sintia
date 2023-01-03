<?php
//Consultas a la BD
$contrato= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".contratos WHERE cont_id=1");
$datosContrato = mysqli_fetch_array($contrato, MYSQLI_BOTH);

$aceptacion= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".contratos_usuarios WHERE cxu_id_contrato='".$datosContrato['cont_id']."'  AND cxu_id_institucion='".$config['conf_id_institucion']."'");
$datosAceptacion = mysqli_fetch_array($aceptacion, MYSQLI_BOTH);

//Condición para mostrar o no el modal
if(isset($datosAceptacion['cxu_fecha_aceptacion']) AND $datosContrato['cont_fecha_modificacion'] > $datosAceptacion['cxu_fecha_aceptacion'] and $datosContrato['cont_visible']==='SI'){
?>

<div class="modal fade" id="modalContrato" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog"  style="max-width: 1350px!important;">
		  <div class="modal-content">
			  
			  <div class="modal-header">
				<h1 class="modal-title" align="center"><?=$datosContrato['cont_nombre'];?></h1>
				<a href="#" data-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-contrato"><i class="fa fa-window-close"></i></a>
			  </div>
			  
			 <div class="modal-body" align="center">

                <?=$datosContrato['cont_descripcion'];?>

			 </div>
			  
			 <div class="modal-footer">
				<form class="form-horizontal" action="../compartido/modal-contrato-guardar.php" method="post">
					<input type="hidden" name="idUsuario" value="<?=$idSession;?>">
                    <input type="hidden" name="id" value="<?=$datosContrato['cont_id'];?>">
					<button type="submit" class="btn btn-info">Aceptar Contrato</button>
				</form>
		 	 </div>
			  
		  </div>
	   </div>
	</div>

<?php }?>