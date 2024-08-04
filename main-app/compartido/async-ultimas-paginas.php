<?php
include("session-compartida.php");
$idPaginaInterna = 'CM0010';
include("historial-acciones-guardar.php");
require_once("../class/UsuariosPadre.php");
?>

<div class="panel">
								
    <header class="panel-heading panel-heading-purple" align="center"><?=$frases[258][$datosUsuarioActual['uss_idioma']];?> (5)</header>
    <div class="col-sm-12">
        <ul class="feed-blog">
        <?php	
            $ultimasPaginas = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".seguridad_historial_acciones 
            LEFT JOIN ".$baseDatosServicios.".paginas_publicidad ON pagp_id=hil_titulo AND pagp_navegable = 1
            WHERE 
            hil_id IN (SELECT MAX(hil_id) FROM ".$baseDatosServicios.".seguridad_historial_acciones GROUP BY hil_titulo, hil_usuario, hil_institucion)
            AND hil_usuario= '".$datosUsuarioActual['uss_id']."' AND hil_institucion =".$config['conf_id_institucion']."
            ORDER BY hil_id DESC LIMIT 5");										 
            while($consultaReciente = mysqli_fetch_array($ultimasPaginas)){						                       
            ?>
            
            <li class="diactive-feed">
                <div class="feed-user-img">
                    <img src="<?=$_GET["fotoPerfilUsr"];?>" class="img-radius "
                        alt="User-Profile-Image">
                </div>
                <h6>
                    <span class="label label-sm label-success">
                    <a href="<?=$consultaReciente['pagp_ruta'];?>" style="color:#FFF;"><?php echo $consultaReciente["pagp_pagina"]; ?></a>
                    </span> 
                    </span>&nbsp;</span>
                    <small class="text-muted"><?=$consultaReciente['hil_fecha'];?></small>
                </h6>
            </li>

            <?php }?>
        </ul>	
    </div>
</div>
