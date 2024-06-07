<?php
include("session.php");

$idPaginaInterna = 'DT0334';
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
$listaPaginas = SubRoles::listarPaginas($id, "5", $activasTodas);
?>

<div class="panel-body" id="panel-body">
    <form action="sub-roles-actualizar.php" method="post" enctype="multipart/form-data">
        <i class="bi bi-eye-slash"></i>
        <div class="form-group row">
            <label class="col-sm-2 "><?= $frases[187][$datosUsuarioActual['uss_idioma']]; ?> Sub Rol:</label>
            <div class="col-sm-1">
                <input type="text" name="subr_id" id="idSubRol<?= $rolActual['subr_id']; ?>" class="form-control" value="<?= $rolActual['subr_id']; ?>" readonly>
            </div>
            <div class="col-sm-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="material-icons">group</i></span>
                    </div>
                    <input type="text" class="form-control" name="nombre" id="nombreSubrol<?= $rolActual['subr_id']; ?>" value="<?= $rolActual['subr_nombre']; ?>" onchange="actualizarSubRol(<?= $rolActual['subr_id']; ?>)">
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2">Usuarios:</label>
            <div class="col-sm-6">
                <select class="form-control select2" name="directivos[]" id="directivos<?= $rolActual['subr_id']; ?>" multiple onchange="actualizarSubRol(<?= $rolActual['subr_id']; ?>)">
                    <option value="">Seleccione una opción</option>
                    <?php
                    $consultaDirectivos = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=" . TIPO_DIRECTIVO . " AND uss_bloqueado=0");
                    while ($directivos = mysqli_fetch_array($consultaDirectivos, MYSQLI_BOTH)) {
                        $selected = "";
                        if (SubRoles::validarExistenciaUsuarioRol($directivos["uss_id"], $id) > 0) {
                            $selected = "selected";
                        }
                    ?>
                        <option value="<?= $directivos["uss_id"]; ?>" <?= $selected ?>><?= UsuariosPadre::nombreCompletoDelUsuario($directivos); ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <select id="paginasSeleccionadas<?= $rolActual['subr_id']; ?>" style="width: 100% !important" name="paginas[]" multiple hidden>
                <?php
                foreach ($rolActual["paginas"] as $page) {
                    echo '<option value="' . $page["pagp_id"] . '"  selected >' . $page["pagp_id"] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group row">
            <div class="col-sm-12">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <?php
                            $contMod = 1;
                            $listarModulos = Modulos::ListarModulosConPaginas();
                            while ($resultadoMod = mysqli_fetch_array($listarModulos, MYSQLI_BOTH)) {
                                $active = $contMod == 7 ? "active" : "";
                                $selected = $contMod == 7 ? "true" : "false";
                        ?>
                                <a class="nav-item nav-link <?=$active?>" id="nav-mod<?=$resultadoMod['mod_id']?><?= $rolActual['subr_id']; ?>-tab" data-toggle="tab" href="#nav-mod<?=$resultadoMod['mod_id']?><?= $rolActual['subr_id']; ?>" role="tab" aria-controls="nav-mod<?=$resultadoMod['mod_id']?><?= $rolActual['subr_id']; ?>" aria-selected="<?=$selected?>" onClick="listarInformacion('async-listar-paginas.php?idMod=<?=$resultadoMod['mod_id']?>&idRol=<?=base64_encode($id)?>&activas=<?=$activasTodas?>', 'nav-mod<?=$resultadoMod['mod_id']?><?= $rolActual['subr_id']; ?>', 'POST', null, <?= $rolActual['subr_id']; ?>)"><?=$resultadoMod['mod_nombre']?></a>
                        <?php $contMod++; } ?>
                    </div>
                </nav>

                <div class="card card-topline-purple">
                    <div class="card-head">
                        <header><?= $frases[370][$datosUsuarioActual['uss_idioma']]; ?> ( <label style="font-weight: bold;" id="cantSeleccionadas<?= $rolActual['subr_id']; ?>"></label>/<?= mysqli_num_rows($listaPaginas) ?> )
                            <label class="switchToggle">
                                <input type="checkbox" <?= $checkActivas; ?> onchange="mostrarActivas(this.checked,'<?= $_GET['idRol']; ?>')">
                                <span class="slider green round"></span>
                            </label>
                        </header>
                        Mostrar solo activas
                    </div>
                    <div class="tab-content" id="nav-tabContent">
                        <?php
                            $contMod2 = 1;
                            $listarModulos2 = Modulos::ListarModulosConPaginas();
                            while ($resultadoMod2 = mysqli_fetch_array($listarModulos2, MYSQLI_BOTH)) {
                                $active2 = $contMod2 == 7 ? "active" : "";
                                $show = $contMod2 == 7 ? "show" : "";
                        ?>
                                <div class="tab-pane fade <?=$show?> <?=$active2?>" id="nav-mod<?=$resultadoMod2['mod_id']?><?= $rolActual['subr_id']; ?>" role="tabpanel" aria-labelledby="nav-mod<?=$resultadoMod2['mod_id']?><?= $rolActual['subr_id']; ?>-tab"></div>
                        <?php $contMod2++; } ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>