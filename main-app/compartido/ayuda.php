<?php include("../modelo/conexion.php");?>
<?php
$consultaAyuda = mysqli_query($conexion, "SELECT * FROM ayuda");
while($ayuda = mysqli_fetch_array($consultaAyuda, MYSQLI_BOTH)){
?>
    <!-- sample modal content -->
    <aside id="ayuda<?=$ayuda[0];?>" class="modal panoramic hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h3><?=$ayuda[1];?></h3>
        <div>
        <div class="modal-body">
            <h4><?=$ayuda[2];?></h4>
            <p><?=$ayuda[3];?></p>
        </div>
        <div class="modal-footer">
        	<button class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        </div>
    </aside>
<?php
}
?>                                  