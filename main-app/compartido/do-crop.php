<?php
session_start();
include("../../config-general/config.php");
$consultaDatos=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$_SESSION["id"]."'");
$datosUsuarioActual = mysqli_fetch_array($consultaDatos, MYSQLI_BOTH);

if(isset($_POST['crop_image']))
{
  $y1=$_POST['top'];
  $x1=$_POST['left'];
  $w=$_POST['right'];
  $h=$_POST['bottom'];
  $image="../files/fotos/".$datosUsuarioActual['uss_foto'];

  list( $width,$height ) = getimagesize( $image );
  $newwidth = 800;
  $newheight = 600;

  $thumb = imagecreatetruecolor( $newwidth, $newheight );
  $source = imagecreatefromjpeg($image);

  imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
  imagejpeg($thumb,$image,100); 


  $im = imagecreatefromjpeg($image);
  $dest = imagecreatetruecolor($w,$h);
	
  imagecopyresampled($dest,$im,0,0,$x1,$y1,$w,$h,$w,$h);
  imagejpeg($dest,"../files/fotos/".$datosUsuarioActual['uss_foto'], 100);

  switch($_POST['tipoUsuario']){	
		case 2: $url = '../docente/perfil.php'; break;
		case 3: $url = '../acudiente/perfil.php'; break;
		case 4: $url = '../estudiante/perfil.php'; break;
		case 5: $url = '../directivo/perfil.php'; break;
		  
		default: $url = '../controlador/salir.php'; break;
  }
	
  echo '<script type="text/javascript">window.location.href="'.$url.'"</script>';
  exit();
}
?>