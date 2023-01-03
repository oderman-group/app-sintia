<?php
$direccionArchivo = ('../files/archivos');

 "Total : " . Fsize($direccionArchivo);
function Fsize($direccionArchivo)
{
    clearstatcache();
    $contadorByte = 0;
    if (is_dir($direccionArchivo)) {
        if ($gd = opendir($direccionArchivo)) {
            while (($archivo = readdir($gd)) !== false) {
                if ($archivo != "." && $archivo != "..") {
                    if (is_dir($archivo)) {
                        $contadorByte += Fsize($direccionArchivo . "/" . $archivo);
                    } else {
                        $nombreArchivo= "archivo : " . $direccionArchivo . "/" . $archivo . "&nbsp;&nbsp;" . filesize($direccionArchivo . "/" . $archivo) . "<br />";
                        if (strpos($nombreArchivo, 'mobiliar_coalst')){
                            $contadorByte += sprintf("%u", filesize($direccionArchivo . "/" . $archivo));
                              $nombreArchivo;
                        }
                    }
                }
            }
            closedir($gd);
        }
    }
    $espacioDisponible=0.021;
    $gb= $contadorByte/1073741824;
    "Carpeta: ".$direccionArchivo."<br>";
    $porcentaje = ($gb/$espacioDisponible)*100;

    if($porcentaje<=50){$colorGrafico='info';}
    elseif($porcentaje>50 and $porcentaje<=80){$colorGrafico='warning';}
    else{$colorGrafico='danger';}
    return "<div class='work-monitor work-progress'>
    <div class='states'>
        <div class='info'>
            <div class='desc pull-left'><b>".round($gb, 2.)." GB/</b></div>
            <div class='desc pull-left'><b>".round($espacioDisponible, 3.)." GB</b></div>
            <div class='percent pull-right'>".round($porcentaje, 3.)."%</div>
        </div>
    
        <div class='progress progress-xs'>
            <div class='progress-bar progress-bar-".$colorGrafico." progress-bar-striped' role='progressbar' aria-valuenow='40' aria-valuemin='0' aria-valuemax='100' style='width: ".$porcentaje."%'>
                <span class='sr-only'>100% </span>
            </div>
        </div>
    </div>
    </div>";
}

?>

<div class="card" style="margin-left:5px; margin-right:5px; padding:5px;">
<td> Uso Del Disco </td>
   <?=Fsize($direccionArchivo)?>

</div>

