<?php
    $id="";
    if(!empty($_GET["id"])){
        $id=base64_decode($_GET["id"]);
    }
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #41c4c4;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#FFF;">
                    Cambiar año
                    <span class="fa fa-angle-down"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                        $years = explode(",", $_SESSION["datosUnicosInstitucion"]['ins_years']);
                        $start = $years[0];
                        $end = $years[1];
                        while($start <= $end){
                            $estiloResaltado = '';
                            if ($start == $year){ 
                                $estiloResaltado = 'style="color: ' . $Plataforma->colorUno . ';"';
                            }
                    ?>
                        <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?id=<?=base64_encode($id);?>&year=<?=base64_encode($start);?>" <?= $estiloResaltado; ?>><?= $start; ?></a>
                    <?php 
                            $start++;
                        } 
                    ?>
                </div>
            </li>

        </ul>
    </div>
</nav>