<?php
$colorNota = $colorNota ?? null;
$estiloNotaFinal = $estiloNotaFinal ?? null;

$infoNotaModificada = null;
if (!empty($notasResultado['cal_fecha_modificada'])) {
    $infoNotaModificada = '<br>
    <i class="fa fa-info-circle" title="Fecha de última modificación: '.$notasResultado['cal_fecha_modificada'].PHP_EOL.'Cantidad de modificaciones: '.$notasResultado['cal_cantidad_modificaciones'].PHP_EOL.'Nota anterior: '.$notasResultado['cal_nota_anterior'].'"></i>';
}

// ============================================
// LÓGICA DE RESALTADO PARA NOTAS FALTANTES
// ============================================
$actividadRegistrada = !empty($rA['act_registrada']) && $rA['act_registrada'] == 1;
$notaVacia = empty($notasResultado['cal_nota']) || $notasResultado['cal_nota'] === '' || $notasResultado['cal_nota'] === null;

$bgCelda = '';
$claseNotaFaltante = '';
$estiloInputNotaFaltante = '';

if ($notaVacia && $actividadRegistrada) {
    $bgCelda = 'background: linear-gradient(135deg, #fff9e6 0%, #ffedd5 100%); border-left: 3px solid #f59e0b;';
    $claseNotaFaltante = 'celda-nota-faltante';
    $estiloInputNotaFaltante = 'background: #fff7ed; border: 2px solid #fb923c; font-weight: 600;';
}
// ============================================
?>

<td style="text-align:center; <?=$bgCelda;?>" class="<?=$claseNotaFaltante;?>" id="columna_<?=$resultado['mat_id']."-".$rA['act_id'];?>">
    <input 
        size="5"
        id="<?=$resultado['mat_id']."-".$rA['act_id'];?>" 
        data-cod-estudiante="<?=$resultado['mat_id'];?>" 
        data-carga-actividad="<?=$rA['act_id'];?>" 
        data-nota-anterior="<?php if(!empty($notasResultado['cal_nota'])) echo $notasResultado['cal_nota'];?>"
        data-color-nota-anterior="<?=$colorNota;?>"
        data-cod-nota="<?=$rA['act_id']?>"
        data-valor-nota="<?=$rA['act_valor'];?>"
        data-nombre-estudiante="<?=$resultado['mat_nombres']." ".$resultado['mat_primer_apellido'];?>"
        data-origen="2"
        value="<?php if(!empty($notasResultado['cal_nota'])) echo $notasResultado['cal_nota'];?>"
        onChange="notasGuardar(this, 'fila_<?=$resultado['mat_id'];?>', 'tabla_notas')" 
        tabindex="<?=10+$contReg;?>" 
        style="font-size: 13px; text-align: center; color:<?=$colorNota;?>; <?=$estiloInputNotaFaltante;?>" 
        <?=$habilitado;?>
        placeholder="<?php if($notaVacia && $actividadRegistrada) echo '⚠️'; ?>"
        title="<?php 
            if(!empty($notasResultado['cal_nota'])) { 
                echo 'Valor en decimal: '.$notasResultado['cal_nota_equivalente_cien']; 
            } else if($notaVacia && $actividadRegistrada) {
                echo 'Nota faltante - Esta actividad requiere calificación';
            }
        ?>"
    >

    <?=$infoNotaModificada;?>

    <br>
    <span 
        id="CU<?=$resultado['mat_id'].$rA['act_id'];?>" 
        style="font-size: 12px; color:<?=$colorNota;?>;"
    >
        <?=$estiloNotaFinal;?>
    </span>

    <?php
    if (isset($notasResultado) && $notasResultado['cal_nota']!="") {
    ?>
        <a 
            href="#" 
            title="<?=$objetoEnviar;?>" 
            id="<?=$notasResultado['cal_id'];?>" 
            name="calificaciones-nota-eliminar.php?id=<?=base64_encode($notasResultado['cal_id']);?>" 
            onClick="deseaEliminar(this)" 
            <?=$deleteOculto;?>
        >
            <i class="fa fa-times"></i>
        </a>
    <?php }?>

    <?php
    $recuperacionVisibilidad = 'hidden';
    if (!empty($notasResultado['cal_nota']) && $notasResultado['cal_nota'] < $config[5]) {
        $recuperacionVisibilidad = 'visible';
    }
    ?>

    <p>
        <input
            data-id="recuperacion_<?=$resultado['mat_id'].$rA['act_id'];?>"
            size="5"
            title="<?=$rA['act_id'];?>" 
            id="<?=$resultado['mat_id'];?>" 
            alt="<?=$resultado['mat_nombres'];?>" 
            name="<?php if (!empty($notasResultado['cal_nota'])) echo $notasResultado['cal_nota'];?>" 
            onChange="notaRecuperacion(this)" 
            tabindex="<?=20+$contReg;?>" 
            style="
                font-size: 13px; 
                text-align: center;
                border-color:tomato;
                visibility:<?=$recuperacionVisibilidad;?>;
            " 
            placeholder="Recup" 
            <?=$habilitado;?>
        >
    </p>

</td>