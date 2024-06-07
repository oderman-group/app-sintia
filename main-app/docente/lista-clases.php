<?php
include("session.php");
$idPaginaInterna = 'DC0046';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("../compartido/head.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");
?>
</head>

<div class="card card-topline-purple">
    <div class="card-head">
        <header><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></header>
        <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
        </div>
    </div>
    
    

    
    <div class="card-body">
        
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-sm-12">
                
        <?php
        if( CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) {
        ?>
        
                <div class="btn-group">
                    <a href="clases-agregar.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" id="addRow" class="btn deepPink-bgcolor">
                        Agregar nueva clase <i class="fa fa-plus"></i>
                    </a>
                </div>
                
                
        <?php
        }
        ?>
                
        
            </div>
        </div>
        
        
    <span id="respuestaGuardar"></span>	
    <div class="table-responsive">
        <table class="table table-striped custom-table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
                    <th>Disponible</th>
                    <th><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></th>
                    <th>Fecha</th>
                    <th>#EC/#ET</th>
                    <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_clases cls 
                    LEFT JOIN ".BD_ACADEMICA.".academico_unidades aca_uni ON aca_uni.uni_id=cls.cls_unidad AND aca_uni.uni_eliminado!=1 AND aca_uni.institucion={$config['conf_id_institucion']} AND aca_uni.year={$_SESSION["bd"]}
                    WHERE cls.cls_id_carga='".$cargaConsultaActual."' AND cls.cls_periodo='".$periodoConsultaActual."' AND cls.cls_estado=1 AND cls.institucion={$config['conf_id_institucion']} AND cls.year={$_SESSION["bd"]} ORDER BY cls.cls_unidad");
                    $contReg = 1;
                    $unidadAnterior=0;
                    while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                        if(!empty($resultado['cls_unidad']) && $resultado['cls_unidad']!=$unidadAnterior){
                            $unidadAnterior=$resultado['cls_unidad'];
                ?>
                <tr style="background-color: antiquewhite; font-weight: bold;">
                    <td colspan="7"><?=$resultado['uni_nombre'];?>.</td>
                </tr>
                <?php
                        }
                        $bg = '';
                        $numerosEstudiantes = Ausencias::consultaNumEstudiantesAusencias($config, $datosCargaActual, $resultado['cls_id']);
                        if($numerosEstudiantes[0]<$cantidadEstudiantesParaDocentes) $bg = '#FCC';
                        
                        $cheked = '';
                        if($resultado['cls_disponible']==1){$cheked = 'checked';}

                        $arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
                        $arrayDatos = json_encode($arrayEnviar);
                        $objetoEnviar = htmlentities($arrayDatos);
                ?>
                <tr id="reg<?=$resultado['cls_id'];?>">
                    <td><?=$contReg;?></td>
                    <td><?=$resultado['cls_id'];?></td>
                    <td>
                        <div class="input-group spinner col-sm-10">
                            <label class="switchToggle">
                                <input type="checkbox" id="<?=$resultado['cls_id'];?>" name="disponible" value="1" onChange="guardarAjaxGeneral(this)" <?=$cheked;?>>
                                <span class="slider yellow round"></span>
                            </label>
                        </div>
                    </td>
                    <td><a href="clases-ver.php?idR=<?=base64_encode($resultado['cls_id']);?>"><?=$resultado['cls_tema'];?></a></td>
                    <td><?=$resultado['cls_fecha'];?></td>
                    <td style="background-color:<?=$bg;?>"><?=$numerosEstudiantes[0];?>/<?=$cantidadEstudiantesParaDocentes;?></td>
                    <td>
                        <?php if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){?>
                        
                        <div class="btn-group">
                            <button class="btn btn-xs btn-info dropdown-toggle center no-margin" type="button" data-toggle="dropdown" aria-expanded="false"> Acciones
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 23px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <li><a href="clases-registrar.php?idR=<?=base64_encode($resultado['cls_id']);?>">Inasistencias</a></li>
                                <li><a href="clases-ver.php?idR=<?=base64_encode($resultado['cls_id']);?>">Acceder</a></li>
                                <li><a href="clases-editar.php?idR=<?=base64_encode($resultado['cls_id']);?>&carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>">Editar</a></li>
                                
                                <li><a href="#" title="<?=$objetoEnviar;?>" id="<?=$resultado['cls_id'];?>" name="clases-eliminar.php?idR=<?=base64_encode($resultado['cls_id']);?>&carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" onClick="deseaEliminar(this)">Eliminar</a></li>
                            </ul>
                        </div>
                        <?php } ?>
                    </td>
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

<?php include("../compartido/guardar-historial-acciones.php");?>