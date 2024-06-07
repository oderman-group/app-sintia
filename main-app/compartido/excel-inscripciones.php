<?php
include("session-compartida.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
$idPaginaInterna = 'DT0243';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=Inscripciones_" . date("d/m/Y") . "-SINTIA.xls");
?>

<html>

<head>
    <meta charset="utf-8">
</head>


<?php
include(ROOT_PATH."/config-general/config-admisiones.php");

$consulta = Estudiantes::listarMatriculasAspirantes($config);
?>
<div align="center">
    <table width="100%" border="1" rules="all">
        <thead>
            <tr>
                <th colspan="7" style="background:#060; color:#FFF;">INSCRIPCIONES ACTUALES</th>
            </tr>
            <tr>
                <th>ID</th>

                <th>Fecha</th>

                <th>Documento</th>

                <th>Aspirante</th>

                <th>Año</th>

                <th>Estado</th>

                <th>Comprobante</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $conta = 1;
            while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
            ?>
                <tr>
                    <td><?= $resultado["mat_id"]; ?></td>

                    <td><?= $resultado["asp_fecha"]; ?></td>

                    <td><?= $resultado["mat_documento"]; ?></td>

                    <td><?= strtoupper($resultado["mat_nombres"] . " " . $resultado["mat_primer_apellido"]); ?></td>

                    <td><?= $resultado["asp_agno"]; ?></td>

                    <td bgcolor="<?= $fondoSolicitud[$resultado["asp_estado_solicitud"]]; ?>"><?= $estadosSolicitud[$resultado["asp_estado_solicitud"]]; ?></td>

                    <td><a href="https://plataformasintia.com/admisiones/files/comprobantes/<?= $resultado["asp_comprobante"]; ?>" target="_blank" style="text-decoration: underline;"><?= $resultado["asp_comprobante"]; ?></a></td>
                </tr>

            <?php
                $conta++;
            }
            include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
            ?>
        </tbody>
    </table>

</html>