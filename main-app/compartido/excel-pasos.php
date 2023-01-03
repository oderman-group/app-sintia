<?php
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=MatriculasPasoAPaso_" . date("d/m/Y") . "-SINTIA.xls");
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
?>

<html>

<head>
    <meta charset="utf-8">
</head>


<div align="center">
    <table width="100%" border="1" rules="all">
        <thead>
            <tr>
                <th>Documento</th>
                <th>Estudiante</th>
                <th>Grado</th>
                <th>Proceso</th>
                <th>A. Datos</th>
                <th>Pago M.</th>
                <th>Contrato</th>
                <th>Pagaré</th>
                <th>C. Académico</th>
                <th>C. Convivencia</th>
                <th>Manual</th>
                <th>Mayores de 14</th>
                <th>Firma</th>
                <th>Modalidad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $iniciaProceso = array("NO", "SI");
            $estadoProceso = array("Pendiente", "Listo");
            $modalidadEstudio = array("", "Virtual", "Presencial- alternancia");
            $estadoMatricula = array("", "Matriculado", "No matriculado", "No matriculado", "No matriculado");
            $cont = 1;
            $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
  LEFT JOIN academico_grados ON gra_id=mat_grado
  WHERE  mat_eliminado=0 ORDER BY mat_primer_apellido, mat_segundo_apellido");
            while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                $colorProceso = 'tomato';
                if ($resultado["mat_iniciar_proceso"] == 1) {
                    $colorProceso = '';
                }

                $colorFirma = '';
                if ($resultado["mat_hoja_firma"] == 1) {
                    $colorFirma = 'aquamarine';
                }
            ?>
                <tr>
                    <td><?= $resultado[12]; ?></td>
                    <td><?= strtoupper($resultado[3] . " " . $resultado[4] . " " . $resultado[5]); ?></td>
                    <td><?= $resultado["gra_nombre"]; ?></td>
                    <td align="center" bgcolor="<?= $colorProceso; ?>"><?= $iniciaProceso[$resultado["mat_iniciar_proceso"]]; ?></td>
                    <td align="center"><?= $estadoProceso[$resultado["mat_actualizar_datos"]]; ?></td>
                    <td align="center">
                        <?= $estadoProceso[$resultado["mat_pago_matricula"]]; ?>
                        <?php if ($resultado["mat_pago_matricula"] == 1 and $resultado["mat_soporte_pago"] != "") { ?>
                            <br> <a href="https://plataformasintia.com/main-app/v2.0/files/comprobantes/<?= $resultado["mat_soporte_pago"]; ?>" target="_blank" style="color:blue;">Soporte</a>
                        <?php } ?>
                    </td>
                    <td align="center"><?= $estadoProceso[$resultado["mat_contrato"]]; ?></td>
                    <td align="center"><?= $estadoProceso[$resultado["mat_pagare"]]; ?></td>
                    <td align="center"><?= $estadoProceso[$resultado["mat_compromiso_academico"]]; ?></td>
                    <td align="center"><?= $estadoProceso[$resultado["mat_compromiso_convivencia"]]; ?></td>
                    <td align="center"><?= $estadoProceso[$resultado["mat_manual"]]; ?></td>
                    <td align="center"><?= $estadoProceso[$resultado["mat_mayores14"]]; ?></td>
                    <td align="center" bgcolor="<?= $colorFirma; ?>">
                        <?= $estadoProceso[$resultado["mat_hoja_firma"]]; ?>
                        <?php if ($resultado["mat_hoja_firma"] == 1 and $resultado["mat_firma_adjunta"] != "") { ?>
                            <br> <a href="https://plataformasintia.com/main-app/v2.0/files/comprobantes/<?= $resultado["mat_firma_adjunta"]; ?>" target="_blank" style="color:blue;">Firma</a>
                        <?php } ?>
                    </td>
                    <td align="center"><?= $modalidadEstudio[$resultado["mat_modalidad_estudio"]]; ?></td>
                    <td align="center"><?= $estadoMatricula[$resultado["mat_estado_matricula"]]; ?></td>
                </tr>

            <?php
                $conta++;
            }
            ?>
        </tbody>
    </table>

</html>