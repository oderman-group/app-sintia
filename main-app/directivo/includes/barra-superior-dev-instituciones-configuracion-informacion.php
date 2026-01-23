<?php
    // Obtener ID de institución
    $id="";
    if(!empty($_GET["id"])){
        $id=base64_decode($_GET["id"]);
    } elseif (isset($id) && !empty($id)) {
        // Si ya está definido en el contexto, usarlo
        $id = $id;
    } else {
        // Fallback a SESSION
        $id = $_SESSION["idInstitucion"] ?? '';
    }
    
    // Obtener año actual
    $year = $_SESSION["bd"] ?? date("Y");
    if (!empty($_GET['year'])) {
        $year = base64_decode($_GET['year']);
    }
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ffffff;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
                    Cambiar año
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                        // Intentar obtener años desde datosConfiguracion primero, luego desde SESSION
                        $insYears = '';
                        if (isset($datosConfiguracion['ins_years']) && !empty($datosConfiguracion['ins_years'])) {
                            $insYears = $datosConfiguracion['ins_years'];
                        } elseif (isset($_SESSION["datosUnicosInstitucion"]['ins_years']) && !empty($_SESSION["datosUnicosInstitucion"]['ins_years'])) {
                            $insYears = $_SESSION["datosUnicosInstitucion"]['ins_years'];
                        }
                        
                        if (!empty($insYears)) {
                            $years = explode(",", $insYears);
                            $start = intval($years[0]);
                            $end = intval($years[1] ?? $years[0]);
                            while($start <= $end){
                                $estiloResaltado = '';
                                if ($start == $year){ 
                                    $estiloResaltado = 'style="color: ' . (isset($Plataforma->colorUno) ? $Plataforma->colorUno : '#667eea') . '; font-weight: 600;"';
                                }
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?id=<?=base64_encode($id);?>&year=<?=base64_encode($start);?>" <?= $estiloResaltado; ?>><?= $start; ?></a>
                    <?php 
                            $start++;
                            } 
                        } else {
                            // Si no hay años, mostrar solo el año actual
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?id=<?=base64_encode($id);?>&year=<?=base64_encode($year);?>" style="color: <?= isset($Plataforma->colorUno) ? $Plataforma->colorUno : '#667eea'; ?>; font-weight: 600;"><?= $year; ?></a>
                    <?php
                        }
                    ?>
                </div>
            </li>

        </ul>
    </div>
</nav>