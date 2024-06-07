<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
    if(!empty($_GET["guest"])){
        $_POST=$_GET;
    }
    $cantidad = 1;
    if( !empty($_POST['cantidad']) && $_POST['cantidad'] > 0 ) {
        $cantidad = $_POST['cantidad']; 
    }
    $montoFinal = $_POST['monto'] * $cantidad;

    $idProducto = '';
    if(!empty($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto']; 
    }

    $curso = '';
    if(!empty($_POST['curso'])) {
        $curso = $_POST['curso']; 
    }

    $idModulo = '';
    if(!empty($_POST['idModulo'])) {
        $idModulo = $_POST['idModulo']; 
    }

    $idPaquete = '';
    if(!empty($_POST['idPaquete'])) {
        $idPaquete = $_POST['idPaquete']; 
    }

    $tipoPaquete = '';
    if(!empty($_POST['tipoPaquete'])) {
        $tipoPaquete = $_POST['tipoPaquete']; 
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redireccionando</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        @-webkit-keyframes shake {
            0% {
                -webkit-transform: translate(2px, 1px) rotate(0deg);
                transform: translate(2px, 1px) rotate(0deg);
            }
            10% {
                -webkit-transform: translate(-1px, -2px) rotate(-2deg);
                transform: translate(-1px, -2px) rotate(-2deg);
            }
            20% {
                -webkit-transform: translate(-3px, 0) rotate(3deg);
                transform: translate(-3px, 0) rotate(3deg);
            }
            30% {
                -webkit-transform: translate(0, 2px) rotate(0deg);
                transform: translate(0, 2px) rotate(0deg);
            }
            40% {
                -webkit-transform: translate(1px, -1px) rotate(1deg);
                transform: translate(1px, -1px) rotate(1deg);
            }
            50% {
                -webkit-transform: translate(-1px, 2px) rotate(-1deg);
                transform: translate(-1px, 2px) rotate(-1deg);
            }
            60% {
                -webkit-transform: translate(-3px, 1px) rotate(0deg);
                transform: translate(-3px, 1px) rotate(0deg);
            }
            70% {
                -webkit-transform: translate(2px, 1px) rotate(-2deg);
                transform: translate(2px, 1px) rotate(-2deg);
            }
            80% {
                -webkit-transform: translate(-1px, -1px) rotate(4deg);
                transform: translate(-1px, -1px) rotate(4deg);
            }
            90% {
                -webkit-transform: translate(2px, 2px) rotate(0deg);
                transform: translate(2px, 2px) rotate(0deg);
            }
            100% {
                -webkit-transform: translate(1px, -2px) rotate(-1deg);
                transform: translate(1px, -2px) rotate(-1deg);
            }
        }

        @keyframes shake {
            0% {
                -webkit-transform: translate(2px, 1px) rotate(0deg);
                transform: translate(2px, 1px) rotate(0deg);
            }
            10% {
                -webkit-transform: translate(-1px, -2px) rotate(-2deg);
                transform: translate(-1px, -2px) rotate(-2deg);
            }
            20% {
                -webkit-transform: translate(-3px, 0) rotate(3deg);
                transform: translate(-3px, 0) rotate(3deg);
            }
            30% {
                -webkit-transform: translate(0, 2px) rotate(0deg);
                transform: translate(0, 2px) rotate(0deg);
            }
            40% {
                -webkit-transform: translate(1px, -1px) rotate(1deg);
                transform: translate(1px, -1px) rotate(1deg);
            }
            50% {
                -webkit-transform: translate(-1px, 2px) rotate(-1deg);
                transform: translate(-1px, 2px) rotate(-1deg);
            }
            60% {
                -webkit-transform: translate(-3px, 1px) rotate(0deg);
                transform: translate(-3px, 1px) rotate(0deg);
            }
            70% {
                -webkit-transform: translate(2px, 1px) rotate(-2deg);
                transform: translate(2px, 1px) rotate(-2deg);
            }
            80% {
                -webkit-transform: translate(-1px, -1px) rotate(4deg);
                transform: translate(-1px, -1px) rotate(4deg);
            }
            90% {
                -webkit-transform: translate(2px, 2px) rotate(0deg);
                transform: translate(2px, 2px) rotate(0deg);
            }
            100% {
                -webkit-transform: translate(1px, -2px) rotate(-1deg);
                transform: translate(1px, -2px) rotate(-1deg);
            }
        }
        .preloader {
            display: -ms-flexbox;
            display: flex;
            background-color: #f4f6f9;
            height: 100vh;
            width: 100%;
            transition: height 200ms linear;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 9999;
        }
        .animation__shake {
            -webkit-animation: shake 1600ms;
            animation: shake 1600ms;
        }
        form img{
            width: 30% !important;
        }
    </style>
</head>
<body>
    <div class="preloader flex-column justify-content-center align-items-center">
        <form class="animation__shake">
            
            <script
                src="https://checkout.epayco.co/checkout.js"
                class="epayco-button"
                data-epayco-test="<?=EPAYCO_TEST;?>"
                data-epayco-external="true"
                data-epayco-autoclick="true"
                data-epayco-button="<?=REDIRECT_ROUTE?>/sintia-logo-2023.png"

                data-epayco-key="<?=PUBLIC_KEY_EPAYCO?>"
                data-epayco-country="co"
                data-epayco-currency="cop"
                data-epayco-amount="<?=$montoFinal;?>"
                data-epayco-name="<?=$_POST['nombre']?>"
                data-epayco-description="<?=$_POST['nombre']?>"

                data-epayco-extra1="<?=$_POST['idUsuario']?>"
                data-epayco-extra2="<?=$_POST['emailUsuario']?>"
                data-epayco-extra3="<?=$_POST['documentoUsuario']?>"
                data-epayco-extra4="<?=$_POST['nombreUsuario']?>"
                data-epayco-extra5="<?=$_POST['celularUsuario']?>"
                data-epayco-extra6="<?=$_POST['idInstitucion']?>"
                data-epayco-extra7="<?=$idProducto?>"
                data-epayco-extra8="<?=$cantidad?>"
                data-epayco-extra9="<?=$_POST['monto']?>"
                data-epayco-extra10="<?=$montoFinal?>"
                data-epayco-extra11="<?=!empty($_POST['url_origen']) ? $_POST['url_origen'] : "";?>"
                data-epayco-extra12="<?=!empty($_POST['usuario']) ? $_POST['usuario'] : "";?>"
                data-epayco-extra13="<?=!empty($_POST['year']) ? $_POST['year'] : "";?>"
                data-epayco-extra14="<?=!empty($_POST['matricula']) ? $_POST['matricula'] : "";?>"
                data-epayco-extra15="<?=$curso?>"
                data-epayco-extra16="<?=$idModulo?>"
                data-epayco-extra17="<?=$idPaquete?>"
                data-epayco-extra18="<?=$tipoPaquete?>"

                data-epayco-response="<?=REDIRECT_ROUTE?>/pagos-online/respuesta.php"
                data-epayco-methodconfirmation="get"
                data-epayco-confirmation="<?=REDIRECT_ROUTE?>/pagos-online/respuesta.php">
            </script>
        </form>
        <p>Redireccionando.</p>
        <p><b>Por favor espere un momento...</b></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>