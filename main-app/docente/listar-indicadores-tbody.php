<?php
if (empty($_SESSION["id"])) {
    include("session.php");   
    include("verificar-carga.php");
    include("verificar-periodos-diferentes.php");
    require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
}
$saberes = array("", "Saber saber (55%)", "Saber hacer (35%)", "Saber ser (10%)");
$consulta = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$contReg = 1;
$porcentajeActual = 0;
while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
    $porcentajeActual += $resultado['ipc_valor'];
?>
    <tr id="reg<?= $resultado['ipc_id']; ?>">
        <td><?= $contReg; ?></td>
        <td><?= $resultado['ipc_id']; ?></td>
        <td><?= $resultado['ind_nombre']; ?></td>
        <td><?= $resultado['ipc_valor']; ?>%</td>

        <?php if ($datosCargaActual['car_saberes_indicador'] == 1) { ?>
            <th><?= $saberes[$resultado['ipc_evaluacion']]; ?></th>
        <?php } ?>

        <td>

            <?php
            $arrayEnviar = array("tipo" => 1, "descripcionTipo" => "Para ocultar fila del registro.");
            $arrayDatos = json_encode($arrayEnviar);
            $objetoEnviar = htmlentities($arrayDatos);
            ?>
            <div class="btn-group">
                <button class="btn btn-xs btn-info dropdown-toggle center no-margin" type="button" data-toggle="dropdown" aria-expanded="false"> Acciones
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu pull-left" role="menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 23px, 0px); top: 0px; left: 0px; will-change: transform;">

                    <?php if ($resultado['ipc_creado'] == 1 and ($periodoConsultaActual == $datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2'] == 1)) { ?>
                        <li><a href="indicadores-editar.php?idR=<?= base64_encode($resultado['ipc_id']); ?>">Editar</a></li>

                        <li><a href="#" title="<?= $objetoEnviar; ?>" id="<?= $resultado['ipc_id']; ?>" name="indicadores-eliminar.php?idR=<?= base64_encode($resultado['ipc_id']); ?>&idIndicador=<?= base64_encode($resultado['ipc_indicador']); ?>&carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>" onClick="deseaEliminar(this)">Eliminar</a></li>
                    <?php } ?>

                    <?php if ($periodoConsultaActual < $datosCargaActual['car_periodo']) { ?>
                        <li><a href="indicadores-recuperar.php?idR=<?= base64_encode($resultado['ipc_indicador']); ?>">Recuperar</a></li>
                    <?php } ?>
                </ul>
            </div>

        </td>
    </tr>

<?php
    $contReg++;
}
