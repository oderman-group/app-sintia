<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    mysqli_query($conexion, "UPDATE academico_materias SET mat_codigo='".$_POST["codigoM"]."', mat_nombre='".$_POST["nombreM"]."', mat_siglas='".$_POST["siglasM"]."', mat_area=".$_POST["areaM"].", mat_oficial=1 WHERE mat_id='".$_POST["idM"]."'");
    
    echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
		exit();