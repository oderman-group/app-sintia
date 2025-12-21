<?php
include_once(ROOT_PATH."/main-app/compartido/datos-fechas.php");

//VALIDAMOS DIAS PARA NOTIFICAR POR CORREO
if($dfDias==90 || $dfDias==30 || $dfDias==5 || $dfDias==1){

    //CANTIDAD EN MESES
    $falta="";
    if($dfDias==90){$falta="3 meses";}
    if($dfDias==30){$falta="1 mes";}
    if($dfDias==5){$falta="5 dias";}
    if($dfDias==1){$falta="1 dia";}
?>

	<div class="modal fade" id="modalLicencia" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	   <div class="modal-dialog"  style="max-width: 1350px!important;">
		  <div class="modal-content">

			<div class="modal-header">
				<h1 class="modal-title" align="center">Vencimiento de licencia</h1>
				<a href="#" data-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-licencia"><i class="fa fa-window-close"></i></a>
			</div>

			<div class="modal-body" align="center">

				<p>Hola! <b><?=$datosUsuarioActual['uss_nombre'];?></b><br>
				<b><?=strtoupper($datosUnicosInstitucion['ins_nombre'])?></b>, su licencia con la plataforma SINTIA está por vencer<br>
				faltan <b><?=$falta;?></b> para su vencimiento<br></p>

			</div>

			<div class="modal-footer">
                <a href="#" class="btn btn-danger">PAGA AQUÍ</a>
				<a href="#" data-dismiss="modal" class="btn btn-danger" id="boton-cerrar-licencia-2">CERRAR</a>
			</div>

		  </div>
	   </div>
	</div>

<?php }?>