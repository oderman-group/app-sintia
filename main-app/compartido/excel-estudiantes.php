<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0244';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=Estudiantes_".date("d/m/Y")."-SINTIA.xls");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

$consulta = Estudiantes::listarEstudiantes(0, '', '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel</title>
</head>
<body>

<div align="center">  
<table  width="100%" border="1" rules="all">
    <thead>
    	<tr>
        	<th colspan="22" style="background:#6017dc; color:#FFF;">MATRICULAS ACTUALES <?=date('Y');?></th>
        </tr>
    	<tr>
            <th scope="col" align="center">No.</th>
            <th scope="col" align="center">ID.</th>
            <th scope="col" align="center">Estudiante</th>
            <th scope="col" align="center">Grado</th>
            <th scope="col" align="center">Grupo</th>
            <th scope="col" align="center">Estado</th>
            <th scope="col" align="center">Fecha Nacimiento</th>
            <th scope="col" align="center">Lugar Nacimiento</th>
            <th scope="col" align="center">Género</th>
            <th scope="col" align="center">Documento</th>            
        	<th scope="col" align="center">Lugar Expedicion</th>
            <th scope="col" align="center">Direccion</th>
            <th scope="col" align="center">Barrio</th>
            <th scope="col" align="center">Telefono</th>
            <th scope="col" align="center">Email</th>
            <th scope="col" align="center">Folio</th>
            <th scope="col" align="center">Cod. Tesoreria</th>
            <th scope="col" align="center">Num. Matrícula</th>

            <th scope="col" align="center">Tipo de documento</th>
            <th scope="col" align="center">Documento Acudiente</th>
            <th scope="col" align="center">Nombre Acudiente</th>
			<th scope="col" align="center">Email Acudiente</th>
        </tr>
    </thead>
    <tbody>
<?php 
$conta=1;
while($resultado=mysqli_fetch_array($consulta, MYSQLI_BOTH))
{
    $consultaDatosA = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$resultado['mat_acudiente']."'");
	$datosA = mysqli_fetch_array($consultaDatosA, MYSQLI_BOTH);

        $estadoM = $estadosMatriculasEstudiantes[$resultado['mat_estado_matricula']];
?>    
    	<tr>	
            <td align="center"><?=$conta;?></td>
            <td align="center"><?=$resultado['mat_id'];?></td>
            <td><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>
            <td align="center"><?=$resultado['gra_nombre'];?></td>
            <td align="center"><?=$resultado['gru_nombre'];?></td>
            <td align="center"><?=$estadoM;?></td>
            <td align="center"><?=$resultado['mat_fecha_nacimiento'];?></td>
            <td align="center"><?php
            if(!empty($resultado['mat_lugar_nacimiento'])){
                $lugarNacimiento = is_numeric($resultado['mat_lugar_nacimiento']) ?  $resultado['ciu_nombre']
                : $resultado['mat_lugar_nacimiento'];

                echo strtoupper($lugarNacimiento);
            }
            ?></td>
            <td align="center"><?=$resultado['ogen_nombre'];?></td>
            <td align="center"><?=$resultado['mat_documento'];?></td>
            <td align="center"><?=$resultado['mat_lugar_expedicion'];?></td>
            <td align="center"><?=$resultado['mat_direccion'];?></td>
            <td align="center"><?=$resultado['mat_barrio'];?></td>
            <td align="center"><?=$resultado['mat_telefono'];?></td>
            <td><?php
            if(!empty($resultado['mat_email'])){ 
                echo strtolower($resultado['mat_email']);
            }    
            ?></td>
			<td align="center"><?=$resultado['mat_folio'];?></td>
            <td align="center"><?=$resultado['mat_codigo_tesoreria'];?></td>
            <td align="center"><?=$resultado['mat_numero_matricula'];?></td>

            <td><?=$datosA['ogen_nombre'];?></td>
            <td><?=$datosA['uss_usuario'];?></td>
            <td align="center"><?php
                if(!empty($datosA['uss_apellido1'])){ 
                    $nombreCompleto = !empty($datosA['uss_apellido1']) ? 
                    $datosA['uss_apellido1']." ".$datosA['uss_apellido2']." ".$datosA['uss_nombre']." ".$datosA['uss_nombre2'] 
                    :  $datosA['uss_nombre'];

                    echo strtoupper($nombreCompleto);
                }
            ?></td>
			<td><?php
            if(!empty($datosA['uss_email'])){ 
                echo strtolower($datosA['uss_email']);
            }
            ?></td>
        </tr>   

<?php
	$conta++;
}
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>        
    </tbody>
</table>

</body>
</html>