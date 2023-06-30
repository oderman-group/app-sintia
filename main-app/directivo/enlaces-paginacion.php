<?php
$paginaActual=$_REQUEST["nume"];
$ultima = $numRegistros / $registros;
$totalPaginas=ceil($ultima);
$ant = $paginaActual - 1;
$sigui = $paginaActual + 1;

$fin=($inicio+$registros);
if($paginaActual==$totalPaginas){
    $fin=$numRegistros;
}

$parametros = "";
if ( isset($_GET) ) {
    foreach ($_GET as $key => $value) {
        if ($key != 'nume') {
            $parametros .= "&{$key}={$value}";
        }    
    }
}
?>

<div style="text-align:center">
    <ul class="pagination pg-dark justify-content-center pb-5 pt-5 mb-0" style="float: none; padding-bottom: 5px!important;">

        <li class="page-item">
            <?php if ($paginaActual > 1) { ?>
                <a class='page-link' aria-label='Previous' href='<?=$nombrePagina?>?nume=<?=$ant."".$parametros;?>'>Previous</a>
            <?php } else { ?>
                <span class='page-link' aria-label='Previous'>Previous</span>
            <?php } ?>
        </li>

        <?php
            for ($i = 1; $i <= $totalPaginas; $i++) {
                if ($i == 1 || $i == $totalPaginas || ($i >= $paginaActual - 2 && $i <= $paginaActual + 2)) {
                    if ($i == $paginaActual) {
        ?>
            <li class="page-item active" style="padding-left: 5px!important;">
                <a class="page-link"><?=$i?></a>
            </li>
        <?php       } else { ?>
            <li class="page-item" style="padding-left: 5px!important;">
                <a href="<?=$nombrePagina?>?nume=<?=$i."".$parametros;?>" class="page-link"><?=$i?></a>
            </li>
        <?php
                    }
                } elseif (($i == 2 && $paginaActual > 3) || ($i == $totalPaginas - 1 && $paginaActual < $totalPaginas - 2)) {
        ?>
            <li class="page-item" style="padding-left: 5px!important;">
                <span class="page-link">...</span>
            </li>
        <?php
                }
            }

        ?>

        <li class="page-item" style="padding-left: 5px!important;">
            <?php if ($paginaActual < $totalPaginas - 0) { ?>
                <a class='page-link' aria-label='Next' href='<?=$nombrePagina?>?nume=<?=$sigui."".$parametros;?>'>Next</a>
            <?php } else { ?>
                <span class='page-link' aria-label='Next'>Next</span>
            <?php } ?>
        </li>
    </ul>

    <p>Mostrando <?=($inicio+1)?> a <?=$fin?> de <?=$numRegistros?> resultados totales</p>
</div>
