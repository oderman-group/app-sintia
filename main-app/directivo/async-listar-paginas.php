<?php
include("session.php");

$idPaginaInterna = 'DT0333';
require_once(ROOT_PATH."/main-app/class/SubRoles.php");

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

$id = "";
if (!empty($_GET["idRol"])) {
    $id = base64_decode($_GET["idRol"]);
}

$rolActual    = SubRoles::consultar($id);
$activasTodas = empty($_GET["activas"]) ? "0" : "1";
$checkActivas = ($activasTodas == "0") ? "" : "checked";
$listaPaginas = SubRoles::listarPaginas($id, "5", $activasTodas, $_GET["idMod"]);
?>


<div class="card-body">
    <div>
        <table id="example3" class="display" name="tabla1" style="width:100%;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        <div class="input-group spinner col-sm-10">
                            <label class="switchToggle">
                                <input type="checkbox" id="all<?= $rolActual['subr_id']; ?>">
                                <span class="slider green round"></span>
                            </label>
                        </div>
                    </th>
                    <th>Id</th>
                    <th><?= $frases[115][$datosUsuarioActual['uss_idioma']]; ?></th>
                    <th>Modulo</th>
                    <th><?= $frases[228][$datosUsuarioActual['uss_idioma']]; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $contReg = 1;
                while ($pagina = mysqli_fetch_array($listaPaginas, MYSQLI_BOTH)) {
                    $cheked = '';
                    if (!empty($rolActual["paginas"])) {
                        $selecionado = array_key_exists($pagina["pagp_id"], $rolActual["paginas"]);
                        if ($selecionado) {
                            $cheked = 'checked';
                        }
                    }

                ?>
                    <tr>
                        <td><?= $contReg; ?></td>
                        <td>
                            <div class="input-group spinner col-sm-10">
                                <label class="switchToggle">
                                    <input type="checkbox" class="check<?= $rolActual['subr_id']; ?>" data-id-rol="<?= $rolActual['subr_id']; ?>" id="<?= $pagina['pagp_paginas_dependencia']; ?>" onchange="validarPaginasDependencia(this)" value="<?= $pagina['pagp_id']; ?>" <?= $cheked; ?>>
                                    <span class="slider green round"></span>
                                </label>
                            </div>
                        </td>
                        <td><?= $pagina['pagp_id']; ?></td>
                        <td><?= $pagina['pagp_pagina']; ?></td>
                        <td><?= $pagina['mod_nombre']; ?></td>
                        <td><?= $pagina['pagp_palabras_claves']; ?></td>

                    </tr>
                <?php $contReg++;
                } ?>
            </tbody>
        </table>
    </div>
</div>