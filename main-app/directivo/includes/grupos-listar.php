<div class="card card-topline-purple">
    <div class="card-head">
        <header><?= $frases[254][$datosUsuarioActual['uss_idioma']]; ?></header>
        <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
        </div>
    </div>
    <div class="card-body">

        <div class="row" style="margin-bottom: 10px;">
            <div class="col-sm-12">
                <div class="btn-group">
                    <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0196'])) { ?>
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#nuevoGrupoModal" class="btn deepPink-bgcolor">
                            Agregar nuevo <i class="fa fa-plus"></i>
                        </a>
                    <?php
                        $idModal = "nuevoGrupoModal";
                        $contenido = "../directivo/grupos-agregar-modal.php";
                        include("../compartido/contenido-modal.php");
                    } ?>

                </div>

            </div>
        </div>

        <div class="table-scrollable">
            <table id="example1" class="display" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Codigo</th>
                        <th><?= $frases[254][$datosUsuarioActual['uss_idioma']]; ?></th>
                        <?php if (Modulos::validarPermisoEdicion()) { ?>
                            <th style="width:10%;"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $consulta = Grupos::listarGrupos();
                    $contReg = 1;
                    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
                    ?>
                        <tr>
                            <td><?= $contReg; ?></td>
                            <td><?= $resultado["gru_id"]; ?></td>
                            <td><?= $resultado["gru_codigo"]; ?></td>
                            <td><?= $resultado['gru_nombre']; ?></td>
                            <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0197'])) { ?>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary"><?= $frases[54][$datosUsuarioActual['uss_idioma']]; ?></button>
                                        <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="javascript:void(0);" class="btn-editar-grupo-modal" data-grupo-id="<?=$resultado['gru_id'];?>"><i class="fa fa-edit"></i> Edición rápida</a></li>
                                            <li><a href="grupos-editar.php?id=<?= base64_encode($resultado["gru_id"]); ?>"><i class="fa fa-pencil"></i> <?= $frases[165][$datosUsuarioActual['uss_idioma']]; ?> completa</a></li>
                                        </ul>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php
                        $contReg++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>