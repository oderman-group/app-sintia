<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(trim($_POST["codigoM"])=="" or trim($_POST["nombreM"])=="" or trim($_POST["siglasM"])=="" or trim($_POST["areaM"])==""){
        echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
        exit();
    }
    mysqli_query($conexion, "INSERT INTO academico_materias(mat_codigo, mat_nombre, mat_siglas, mat_area, mat_oficial) VALUES ('".$_POST["codigoM"]."','".$_POST["nombreM"]."','".$_POST["siglasM"]."','".$_POST["areaM"]."',1);");
    
    echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
    exit();