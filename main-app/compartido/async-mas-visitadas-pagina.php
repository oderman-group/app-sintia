<?php
include("session-compartida.php");
$idPaginaInterna = 'CM0011';
include("historial-acciones-guardar.php");
require_once("../class/UsuariosPadre.php");
?>

<div class="panel">
    
    <header class="panel-heading panel-heading-blue" align="center"><?=$frases[259][$datosUsuarioActual['uss_idioma']];?> (5)</header>
    <div class="col-sm-12">
    <?php	
        $paginasMasVisitadasConsulta = mysqli_query($conexion, "SELECT count(*) as visitas, pagp_pagina, pagp_ruta FROM ".$baseDatosServicios.".seguridad_historial_acciones
        INNER JOIN ".$baseDatosServicios.".paginas_publicidad ON pagp_id=hil_titulo AND pagp_navegable = 1
        WHERE hil_usuario = '".$datosUsuarioActual['uss_id']."' AND hil_institucion = ".$config['conf_id_institucion']."
        GROUP BY hil_titulo
        ORDER BY count(*) DESC
        LIMIT 5");										 
        while($paginasMasVisitadasDatos = mysqli_fetch_array($paginasMasVisitadasConsulta)){						                       
        ?>
            <li><a href="<?=$paginasMasVisitadasDatos['pagp_ruta'];?>" style="text-decoration: underline;"><?php echo $paginasMasVisitadasDatos["pagp_pagina"]." (".$paginasMasVisitadasDatos["visitas"].")"; ?></a></li>
        <?php }?>
    </div>
</div>
