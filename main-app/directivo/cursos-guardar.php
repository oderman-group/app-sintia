<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(trim($_POST["codigoC"])=="" or trim($_POST["nombreC"])=="" or trim($_POST["valorM"])=="" or trim($_POST["valorP"])==""){
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO academico_grados (gra_codigo,gra_nombre,gra_formato_boletin,gra_valor_matricula,gra_valor_pension,gra_estado,gra_grado_siguiente)VALUES(".$_POST["codigoC"].",'".$_POST["nombreC"]."','1',".$_POST["valorM"].",".$_POST["valorP"].",1,'".$_POST["graSiguiente"]."')");
	

	echo '<script type="text/javascript">window.location.href="cursos.php";</script>';
	exit();	