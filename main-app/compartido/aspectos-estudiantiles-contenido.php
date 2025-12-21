<?php
$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}
require_once("../class/Estudiantes.php");
require_once("../class/UsuariosPadre.php");

// Optimización: obtener datos del estudiante
$datosEditar = Estudiantes::obtenerDatosEstudiantePorIdUsuario($idR);
$usuarioEstudiante = UsuariosPadre::sesionUsuario($idR);

// Optimización: calcular edad de forma más eficiente
$edad = 'N/A';
if (!empty($datosEditar['mat_fecha_nacimiento'])) {
    $fechaNac = new DateTime($datosEditar['mat_fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNac)->y;
}

$estadoAgno = array("EN CURSO", "SI", "NO");

// Optimización: obtener todos los aspectos disciplinarios de una vez (fuera del bucle)
$aspectosDisciplinarios = [];
$consultaDisciplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
    WHERE dn_cod_estudiante='".$datosEditar['mat_id']."' 
    AND dn_periodo IN (1,2,3,4) 
    AND institucion={$config['conf_id_institucion']} 
    AND year={$_SESSION["bd"]}");

while($aspecto = mysqli_fetch_array($consultaDisciplina, MYSQLI_BOTH)) {
    $aspectosDisciplinarios[$aspecto['dn_periodo']] = $aspecto;
}

// Optimización: obtener todos los aspectos académicos por periodo
$aspectosAcademicos = [];
for ($p = 1; $p <= 4; $p++) {
    $consultaAspectos = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".matriculas_aspectos mata
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mata_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
        WHERE mata_estudiante='".$datosEditar['mat_id']."' 
        AND mata_periodo='".$p."' 
        AND mata.institucion={$config['conf_id_institucion']} 
        AND mata.year={$_SESSION["bd"]}
        ORDER BY mata_id DESC");
    
    $aspectosAcademicos[$p] = [];
    while($aspecto = mysqli_fetch_array($consultaAspectos, MYSQLI_BOTH)) {
        $aspectosAcademicos[$p][] = $aspecto;
    }
}
?>

<style>
    /* Estilos mejorados para aspectos estudiantiles */
    .estudiante-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
    }

    .estudiante-info-card {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .foto-estudiante {
        text-align: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .foto-estudiante img {
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        max-width: 150px;
        height: auto;
    }

    .tabla-info-estudiante {
        width: 100%;
        border-collapse: collapse;
        font-size: 10pt;
    }

    .tabla-info-estudiante td {
        padding: 10px 12px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .tabla-info-estudiante .td-label {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        width: 15%;
    }

    .tabla-info-estudiante .td-valor {
        background-color: white;
        color: #212529;
    }

    .periodo-card {
        margin-bottom: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .periodo-header {
        background: #2c3e50;
        color: white;
        padding: 12px 20px;
        font-weight: bold;
        font-size: 13pt;
        text-align: center;
        border-left: 4px solid #3498db;
    }

    .periodo-body {
        padding: 20px;
        background: white;
    }

    .aspectos-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .aspectos-table th,
    .aspectos-table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #dee2e6;
    }

    .aspectos-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        font-size: 9pt;
        text-transform: uppercase;
    }

    .aspectos-table td {
        font-size: 9pt;
        vertical-align: top;
    }

    .aspectos-table tr:hover {
        background-color: #f1f3f5;
    }

    .btn-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .form-section {
        background: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .form-section-title {
        font-size: 14pt;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #3498db;
    }

    .form-group label {
        font-weight: 600;
        color: #495057;
    }

    .disciplina-row {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .disciplina-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .disciplina-column {
        background: white;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .disciplina-column h5 {
        font-size: 10pt;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .estado-agno {
        background: #e8f4f8;
        padding: 10px 15px;
        border-radius: 6px;
        border-left: 4px solid #3498db;
        margin-top: 15px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .disciplina-columns {
            grid-template-columns: 1fr;
        }

        .btn-actions {
            flex-direction: column;
        }

        .tabla-info-estudiante .td-label {
            width: 30%;
        }
    }
</style>

<div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class="pull-left">
                                <div class="page-title"><?=$frases[292][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
        
                    <?php include("../../config-general/mensajes-informativos.php"); ?>
        
                    <div class="row">
                        <div class="col-sm-12">
                <!-- Botones de acción -->
                <div class="btn-actions">
                            <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO or $datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE){?>
                        <a href="reportes-lista.php?est=<?=$_GET["idR"];?>&fest=<?=base64_encode(1);?>" class="btn btn-danger" target="_blank">
                            <i class="fa fa-file-pdf"></i> <?=strtoupper($frases[248][$datosUsuarioActual['uss_idioma']]);?>
                        </a>
                            <?php }?>

                            <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO){?>
                        <a href="estudiantes-editar.php?id=<?=base64_encode($datosEditar['mat_id']);?>" class="btn btn-info">
                            <i class="fa fa-edit"></i> <?=strtoupper($frases[291][$datosUsuarioActual['uss_idioma']]);?>
                        </a>
                            <?php }?>
                </div>

                <!-- Foto del estudiante -->
                <div class="foto-estudiante">
                    <?php 
                    $fotoPath = ROOT_PATH . '/main-app/files/fotos/' . $usuarioEstudiante['uss_foto'];
                    if (!empty($usuarioEstudiante['uss_foto']) && file_exists($fotoPath)) {
                    ?>
                        <img src="../files/fotos/<?=$usuarioEstudiante['uss_foto'];?>" alt="Foto estudiante" />
                    <?php } else { ?>
                        <img src="../../config-general/assets/images/default-user.png" alt="Foto por defecto" />
                    <?php } ?>
                                </div>

                <!-- Información del estudiante -->
                <div class="card estudiante-info-card">
                    <div class="estudiante-header">
                        <h4 style="margin:0; font-weight:600;">
                            <i class="fa fa-user"></i> Información del Estudiante
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="tabla-info-estudiante">
                            <tr>
                                <td class="td-label"><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></td>
                                <td class="td-valor" colspan="3">
                                    <?=strtoupper($datosEditar['mat_primer_apellido']." ".$datosEditar['mat_segundo_apellido']." ".$datosEditar['mat_nombres']);?>
                                </td>
                                <td class="td-label"><?=$frases[164][$datosUsuarioActual['uss_idioma']];?></td>
                                <td class="td-valor"><?=$datosEditar['gra_nombre'];?></td>
                                        </tr>
                            <tr>
                                <td class="td-label">D.I</td>
                                <td class="td-valor"><?=$datosEditar['mat_documento'];?></td>
                                <td class="td-label"><?=$frases[189][$datosUsuarioActual['uss_idioma']];?></td>
                                <td class="td-valor"><?=$datosEditar['mat_fecha_nacimiento'];?></td>
                                <td class="td-label"><?=$frases[293][$datosUsuarioActual['uss_idioma']];?></td>
                                <td class="td-valor"><?=$edad;?> años</td>
                                        </tr>
                            <tr>
                                <td class="td-label"><?=$frases[297][$datosUsuarioActual['uss_idioma']];?></td>
                                <td class="td-valor" colspan="3"><?=$datosEditar['mat_direccion'];?></td>
                                <td class="td-label"><?=$frases[298][$datosUsuarioActual['uss_idioma']];?></td>
                                <td class="td-valor"><?=$datosEditar['mat_barrio'];?></td>
                                        </tr>
                            <tr>
                                <td class="td-label"><?=$frases[182][$datosUsuarioActual['uss_idioma']];?></td>
                                <td class="td-valor"><?=$datosEditar['mat_telefono'];?></td>
                                <td class="td-label">Email Acudiente</td>
                                <td class="td-valor" colspan="3"><?=$datosEditar['uss_email'];?></td>
                                        </tr>
                            <tr>
                                <td class="td-label">Acudiente</td>
                                <td class="td-valor"><?=$datosEditar['uss_nombre'];?></td>
                                <td class="td-label"><?=$frases[182][$datosUsuarioActual['uss_idioma']];?></td>
                                <td class="td-valor"><?=$datosEditar['uss_telefono'];?></td>
                                <td class="td-label"><?=$frases[297][$datosUsuarioActual['uss_idioma']];?></td>
                                <td class="td-valor"><?=$datosEditar['uss_direccion'];?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                <!-- Formulario para agregar aspectos (Directivos/Docentes) -->
                           <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO or $datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE){?>
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fa fa-clipboard"></i> <?=$frases[292][$datosUsuarioActual['uss_idioma']];?>
                                </div>
                    <form class="form-horizontal" action="../compartido/aspectos-estudiantiles-guardar.php" method="post">
                                        <input type="hidden" name="estudiante" value="<?=$datosEditar['mat_id'];?>">
                                        <input type="hidden" name="idR" value="<?=$_GET["idR"];?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></label>
                                    <input type="date" name="fecha" class="form-control" required>
                                            </div>
                                        </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?=$frases[27][$datosUsuarioActual['uss_idioma']];?></label>
                                    <input type="number" name="periodo" class="form-control" min="1" max="4" required>
                                </div>
                                            </div>
                                        </div>

                        <div class="form-group">
                            <label><?=$frases[302][$datosUsuarioActual['uss_idioma']];?></label>
                            <textarea name="descripcion" class="form-control" rows="3"></textarea>
                                        </div>

                        <div class="form-group">
                            <label><?=$frases[303][$datosUsuarioActual['uss_idioma']];?></label>
                            <textarea name="positivos" class="form-control" rows="3"></textarea>
                                        </div>

                        <div class="form-group">
                            <label><?=$frases[304][$datosUsuarioActual['uss_idioma']];?></label>
                            <textarea name="mejorar" class="form-control" rows="3"></textarea>
                                        </div>

                        <div class="form-group">
                            <label><?=$frases[305][$datosUsuarioActual['uss_idioma']];?></label>
                            <textarea name="tratamiento" class="form-control" rows="3"></textarea>
                                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-save"></i> Guardar cambios
                                        </button>
                        </div>
                                    </form>
                            </div>

                <!-- Formulario aspectos docentes -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fa fa-book"></i> <?=$frases[292][$datosUsuarioActual['uss_idioma']];?> (<?=$frases[28][$datosUsuarioActual['uss_idioma']];?>)
                                </div>
                    <form class="form-horizontal" action="../compartido/aspectos-estudiantiles-guardar-docentes.php" method="post">
                                        <input type="hidden" name="idR" value="<?=$_GET["idR"];?>">
                                        <input type="hidden" name="estudiante" value="<?=$datosEditar['mat_id'];?>">
                                        <input type="hidden" name="curso" value="<?=$datosEditar['mat_grado'];?>">

                        <div class="form-group">
                            <label><?=$frases[27][$datosUsuarioActual['uss_idioma']];?></label>
                            <input type="number" name="periodo" class="form-control" min="1" max="4" required style="max-width: 200px;">
                                        </div>

                        <div class="form-group">
                            <label><?=strtoupper($frases[281][$datosUsuarioActual['uss_idioma']]);?></label>
                            <textarea name="academicos" class="form-control" rows="3"></textarea>
                                        </div>

                        <div class="form-group">
                            <label><?=strtoupper($frases[282][$datosUsuarioActual['uss_idioma']]);?></label>
                            <textarea name="convivenciales" class="form-control" rows="3"></textarea>
                                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-save"></i> Guardar cambios
                                        </button>
                        </div>
                                    </form>
                                </div>
                        <?php }?>

                        </div>
                        
            <!-- Periodos y aspectos -->
                        <div class="col-sm-12">
                                        <?php
                for ($p = 1; $p <= 4; $p++) {
                    $aspectos = isset($aspectosDisciplinarios[$p]) ? $aspectosDisciplinarios[$p] : null;
                ?>
                    <!-- Aspectos Disciplinarios por Periodo -->
                    <div class="periodo-card">
                        <div class="periodo-header">
                            <?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?> <?=$p;?>
                        </div>
                        <div class="periodo-body">
                            <div class="disciplina-row">
                                <div class="disciplina-columns">
                                    <div class="disciplina-column">
                                        <h5><?=strtoupper($frases[281][$datosUsuarioActual['uss_idioma']]);?></h5>
                                        <p><?php if(!empty($aspectos['dn_aspecto_academico'])){ echo nl2br(htmlspecialchars($aspectos['dn_aspecto_academico'])); } else { echo '<em style="color:#999;">Sin registro</em>'; }?></p>
                                    </div>
                                    <div class="disciplina-column">
                                        <h5><?=strtoupper($frases[282][$datosUsuarioActual['uss_idioma']]);?></h5>
                                        <p><?php if(!empty($aspectos['dn_aspecto_convivencial'])){ echo nl2br(htmlspecialchars($aspectos['dn_aspecto_convivencial'])); } else { echo '<em style="color:#999;">Sin registro</em>'; }?></p>
                                    </div>
                                </div>
                                <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !empty($aspectos)){?>
                                <div style="text-align: right; margin-top: 10px;">
                                    <?php $href='../compartido/aspectos-estudiantiles-eliminar-docentes.php?idA='.$aspectos['dn_id'].'&idR='.$_GET["idR"];?>
                                    <button type="button" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','<?= $href ?>')" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </button>
                                </div>
                                <?php }?>
                            </div>

                            <?php if($p == 4 && !empty($datosEditar['mat_estado_agno'])){?>
                            <div class="estado-agno">
                                <?=strtoupper($frases[308][$datosUsuarioActual['uss_idioma']]);?>: 
                                <strong><?php echo $estadoAgno[$datosEditar['mat_estado_agno']] ?? 'N/A';?></strong>
                                    </div>
                            <?php }?>

                            <!-- Tabla de aspectos académicos del periodo -->
                            <?php if(!empty($aspectosAcademicos[$p])){?>
                            <h5 style="margin-top: 20px; margin-bottom: 15px; font-weight: 600; color: #2c3e50;">
                                <?=strtoupper($frases[306][$datosUsuarioActual['uss_idioma']]);?>
                            </h5>
                            <div style="overflow-x: auto;">
                                <table class="aspectos-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;"><?=strtoupper($frases[51][$datosUsuarioActual['uss_idioma']]);?></th>
                                            <th style="width: 15%;"><?=strtoupper($frases[307][$datosUsuarioActual['uss_idioma']]);?></th>
                                            <th style="width: 18%;"><?=strtoupper($frases[302][$datosUsuarioActual['uss_idioma']]);?></th>
                                            <th style="width: 18%;"><?=strtoupper($frases[303][$datosUsuarioActual['uss_idioma']]);?></th>
                                            <th style="width: 18%;"><?=strtoupper($frases[304][$datosUsuarioActual['uss_idioma']]);?></th>
                                            <th style="width: 18%;"><?=strtoupper($frases[305][$datosUsuarioActual['uss_idioma']]);?></th>
                                            <th style="width: 5%;" title="Firma y aprobación del acudiente">F.A</th>
                                            <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO){?>
                                            <th style="width: 5%;">&nbsp;</th>
                                            <?php }?>
                                </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($aspectosAcademicos[$p] as $aspecto) {?>
                                        <tr>
                                            <td><?=$aspecto['mata_fecha_evento'];?></td>
                                            <td><?=$aspecto['uss_nombre'];?></td>
                                            <td><?=nl2br(htmlspecialchars($aspecto['mata_descripcion']));?></td>
                                            <td><?=nl2br(htmlspecialchars($aspecto['mata_aspectos_positivos']));?></td>
                                            <td><?=nl2br(htmlspecialchars($aspecto['mata_aspectos_mejorar']));?></td>
                                            <td><?=nl2br(htmlspecialchars($aspecto['mata_tratamiento']));?></td>
                                            <td style="text-align: center;">
                                                <?php if($aspecto['mata_aprobacion_acudiente']==0 and $datosUsuarioActual['uss_tipo'] == TIPO_ACUDIENTE){?> 
                                                    <a href="#reportes-disciplinarios.php?usrEstud=<?=$_GET["usrEstud"];?>&req=1&id=<?=$aspecto['dr_id'];?>" class="btn btn-sm btn-primary">Firmar</a>
                                                            <?php } else{?>
                                                    <i class="fa fa-check-circle" style="color: #27ae60; font-size: 18px;" title="<?=$aspecto['mata_aprobacion_acudiente_fecha'];?>"></i>
                                                            <?php }?>
                                    </td>
                                            <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO){?>
                                            <td style="text-align: center;">
                                                <?php $href='../compartido/aspectos-estudiantiles-eliminar.php?idA='.$aspecto['mata_id'].'&idR='.$_GET["idR"];?>
                                                <button type="button" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','<?= $href ?>')" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                            <?php }?>
                                        </tr>
                                        <?php }?>
                                    </tbody>
                            </table>
                            </div>
                            <?php } else {?>
                            <p style="text-align: center; color: #999; padding: 20px; font-style: italic;">
                                No hay registros de aspectos académicos para este periodo
                            </p>
                            <?php }?>
                        </div>
                    </div>
                <?php }?>
            </div>
        </div>
                </div>
            </div>
