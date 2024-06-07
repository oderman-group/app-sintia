<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/compartido/session-compartida.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

$id="";
if(!empty($_REQUEST["id"])){ $id=base64_decode($_REQUEST["id"]);}
?>
<head>
	<title>SINTIA | Saldos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../../sintia-icono.png">
    <style type="text/css">
	.pieP{
		margin-top:100px;
		display:flex;
		flex-direction:row;
		justify-content:space-between;
	}
	.headerP{
		display:flex;
		flex-direction:row;
		justify-content:space-between;
	}
    </style>
</head>
<body style="font-family:Arial;">
<div class="headerP">
    <div align="center" style="margin: 10px auto;">
        <img src="../../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
        <?=$informacion_inst["info_nombre"]?><br>
        <h4 style="margin-top: 50px;">A QUIEN PUEDA INTERESAR</h4>
    </div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
</div>   
<?php
$usuario = Estudiantes::obtenerDatosEstudiantePorIdUsuario($id);
$nombre = Estudiantes::NombreCompletoDelEstudiante($usuario);
$documento = strpos($usuario["mat_documento"], '.') !== true && is_numeric($usuario["mat_documento"]) ? number_format($usuario["mat_documento"],0,",",".") : $usuario["mat_documento"];
    switch($usuario['mat_tipo_documento']){
        case 105:
            $tipoD='CC.';
        break;
        case 106:
            $tipoD='NUIP.';
        break;
        case 107:
            $tipoD='TI.';
        break;
        case 108:
            $tipoD='RC.';
        break;
        case 109:
            $tipoD='CE.';
        break;
        case 110:
            $tipoD='PP.';
        break;
        case 139:
            $tipoD='PEP.';
        break;
    }
?>
    <div align="justify" style="margin-top: 20px;">
    <p>El <?=$informacion_inst["info_nombre"]?> hace constar que el estudiante <b><?=$nombre?></b> identificado con <?=$tipoD." ".$documento?> se encuentra a PAZ y SALVO por todo concepto.</p>
    <p>Esta constancia certifica que ha cumplido satisfactoriamente con todos los compromisos y obligaciones financieras con nuestra institución.</p>
    <p>Se expide esta constancia a los <?=date("d");?> días del mes de <?=$mesesAgno[date("m")];?> del año <?=date("Y");?>.</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>Agradecemos tu colaboración y compromiso con nuestra institución.</p>
    </div>
    
    <div class="pieP">
    	<div>
            <p>Atentamente,</p>
            <?php
                $tesorero = UsuariosPadre::sesionUsuario($informacion_inst["info_tesorero"]);
                if(!empty($tesorero["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/'.$tesorero["uss_firma"])){
                    echo '<img src="'.REDIRECT_ROUTE.'/files/fotos/'.$tesorero["uss_firma"].'" width="100"><br>';
                }else{
                    echo '<p>&nbsp;</p>
                        <p>&nbsp;</p>';
                }
            ?>
            <p style="height:0px;"></p>_________________________________<br>
            <p>&nbsp;</p>
            <?=UsuariosPadre::nombreCompletoDelUsuario($tesorero);?><br>
            Contador(a)
        </div>
    </div>
     <script type="text/javascript">print();</script>
</body>
</html>


