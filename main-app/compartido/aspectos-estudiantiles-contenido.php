<?php
$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}
require_once("../class/Estudiantes.php");
require_once("../class/UsuariosPadre.php");
$datosEditar = Estudiantes::obtenerDatosEstudiantePorIdUsuario($idR);

$usuarioEstudiante = UsuariosPadre::sesionUsuario($idR);

$agnoNacimiento = Estudiantes::traerYearNacimiento($config, $idR);

$edad = date("Y") - $agnoNacimiento[0];

$estadoAgno = array("EN CURSO", "SI", "NO");
?>

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

                            <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO or $datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE){?>
                                <a href="reportes-lista.php?est=<?=$_GET["idR"];?>&fest=<?=base64_encode(1);?>" class="btn btn-danger" target="_blank"><?=strtoupper($frases[248][$datosUsuarioActual['uss_idioma']]);?></a>
                            <?php }?>
                            

                            <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO){?>

                                <a href="estudiantes-editar.php?idR=<?=$_GET["idR"];?>" class="btn btn-info" target="_blank"><?=strtoupper($frases[291][$datosUsuarioActual['uss_idioma']]);?></a>

                            <?php }?>

                                <div style="text-align: right;">
                                    <img src="../files/fotos/<?=$usuarioEstudiante['uss_foto'];?>" width="150" />
                                </div>


                            <div class="card card-box">
                                
                                <div class="card-body " id="bar-parent6">

                                    <table border="1" rules="group" width="100%">
                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[61][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td colspan="3"><?=$datosEditar['mat_primer_apellido']." ".$datosEditar['mat_segundo_apellido']." ".$datosEditar['mat_nombres'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[164][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td><?=$datosEditar['gra_nombre'];?></td>
                                            <td style="background-color: lightgray;">D.I:</td>
                                            <td><?=$datosEditar['mat_documento'];?></td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[189][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td><?=$datosEditar['mat_fecha_nacimiento'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[293][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td><?=$edad;?></td>
                                            <td style="background-color: lightgray;"><?=$frases[294][$datosUsuarioActual['uss_idioma']];?> RH:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;">EPS:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Email acudiente:</td>
                                            <td colspan="3"><?=$datosEditar['uss_email'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[295][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;"><?=$frases[296][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[297][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td colspan="3"><?=$datosEditar['mat_direccion'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[298][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td><?=$datosEditar['mat_barrio'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[182][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td><?=$datosEditar['mat_telefono'];?></td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[301][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td colspan="3"></td>
                                            <td style="background-color: lightgray;"><?=$frases[182][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;"><?=$frases[297][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[300][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td colspan="3"></td>
                                            <td style="background-color: lightgray;"><?=$frases[182][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;"><?=$frases[297][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Acudiente:</td>
                                            <td><?=$datosEditar['uss_nombre'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[182][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td><?=$datosEditar['uss_telefono'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[297][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td><?=$datosEditar['uss_direccion'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[299][$datosUsuarioActual['uss_idioma']];?>:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                    </table>
                                    
                                </div>
                            </div>


                           <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO or $datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE){?>
                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[292][$datosUsuarioActual['uss_idioma']];?></header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/aspectos-estudiantiles-guardar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="estudiante" value="<?=$datosEditar['mat_id'];?>">
                                        <input type="hidden" name="idR" value="<?=$_GET["idR"];?>">

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">  
                                                <input type="date" name="fecha" class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[27][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-2">  
                                                <input type="number" name="periodo" class="form-control">
                                            </div>
                                        </div>

                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[302][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="descripcion" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[303][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="positivos" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[304][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="mejorar" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[305][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="tratamiento" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i><?=$frases[184][$datosUsuarioActual['uss_idioma']];?></a>

                                        <button type="submit" class="btn  btn-info">
                                            <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                                        </button>
                                    </form>
                                </div>
                            </div>



                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[292][$datosUsuarioActual['uss_idioma']];?> (<?=$frases[28][$datosUsuarioActual['uss_idioma']];?>)</header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/aspectos-estudiantiles-guardar-docentes.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="idR" value="<?=$_GET["idR"];?>">
                                        <input type="hidden" name="estudiante" value="<?=$datosEditar['mat_id'];?>">
                                        <input type="hidden" name="curso" value="<?=$datosEditar['mat_grado'];?>">



                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[27][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-2">  
                                                <input type="number" name="periodo" class="form-control">
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=strtoupper($frases[281][$datosUsuarioActual['uss_idioma']]);?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="academicos" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=strtoupper($frases[282][$datosUsuarioActual['uss_idioma']]);?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="convivenciales" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i><?=$frases[184][$datosUsuarioActual['uss_idioma']];?></a>

                                        <button type="submit" class="btn  btn-info">
                                            <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                                        </button>

                                    </form>
                                </div>
                            </div>

                        <?php }?>

                        </div>
                        

                        <div class="col-sm-12">


                            


                            

                                        <?php
                                        $p=1;
                                        while($p<=4){

                                            $aspectos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
                                            WHERE dn_cod_estudiante='".$datosEditar['mat_id']."' AND dn_periodo='".$p."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"), MYSQLI_BOTH);

                                        ?>

                                            <div class="card card-box">
                                
                                <div class="card-body " id="bar-parent6">


                                    <table width="100%">
                                            <tr style="font-weight: bold; font-size:large;">
                                                <td colspan="2" align="center"><?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?> <?=$p;?></td>
                                            </tr>

                                            <tr style="font-weight: bold;">
                                                <td align="center" width="40%"><?=strtoupper($frases[281][$datosUsuarioActual['uss_idioma']]);?></td>
                                                <td align="center" width="40%"><?=strtoupper($frases[282][$datosUsuarioActual['uss_idioma']]);?></td>
                                                <td align="right" width="20%">&nbsp;</td>
                                            </tr>

                                            <tr style="height: 60px;">
                                                <td><?php if(!empty($aspectos['dn_aspecto_academico'])){ echo $aspectos['dn_aspecto_academico'];}?></td>
                                                <td><?php if(!empty($aspectos['dn_aspecto_convivencial'])){ echo $aspectos['dn_aspecto_convivencial'];}?></td>
                                                <td>
                                                    <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !empty($aspectos)){
                                                        $href='../compartido/aspectos-estudiantiles-eliminar-docentes.php?idA='.$aspectos['dn_id'].'&idR='.$_GET["idR"];?>
                                                        <a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','<?= $href ?>')" class="btn btn-danger">X</a>
                                                    <?php }?>

                                                </td>
                                            </tr>

                                            <?php if($p == 4){?>

                                                <tfoot>
                                                    <tr style="font-weight: bold;">
                                                        <td align="right"><?=strtoupper($frases[308][$datosUsuarioActual['uss_idioma']]);?>: </td>
                                                        <td><?php if(!empty($datosEditar['mat_estado_agno'])) echo $estadoAgno[$datosEditar['mat_estado_agno']];?></td>
                                                    </tr>  
                                                </tfoot>

                                            <?php }?>


                                            </table>

                                    
                                </div>
                            </div>



                            <div class="card card-box">
                                    <div class="card-head">
                                        <header><?=strtoupper($frases[306][$datosUsuarioActual['uss_idioma']]);?></header>
                                    </div>

                                    <div class="card-body">

                            <table width="100%">

                                <tr style="font-weight: bold;">
                                    <td><?=strtoupper($frases[51][$datosUsuarioActual['uss_idioma']]);?></td>
                                    <td><?=strtoupper($frases[307][$datosUsuarioActual['uss_idioma']]);?></td>
                                    <td><?=strtoupper($frases[302][$datosUsuarioActual['uss_idioma']]);?></td>
                                    <td><?=strtoupper($frases[303][$datosUsuarioActual['uss_idioma']]);?></td>
                                    <td><?=strtoupper($frases[304][$datosUsuarioActual['uss_idioma']]);?></td>
                                    <td><?=strtoupper($frases[305][$datosUsuarioActual['uss_idioma']]);?></td>
                                    <th title="Firma y aprobación del acudiente">F.A</th>
                                    <td>&nbsp;</td>
                                </tr>
                                
                            
                            
                                
                            <?php
                            $aspectosCosnulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".matriculas_aspectos mata
                                INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mata_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
                                WHERE mata_estudiante='".$datosEditar['mat_id']."' AND mata_periodo='".$p."' AND mata.institucion={$config['conf_id_institucion']} AND mata.year={$_SESSION["bd"]}
                                ORDER BY mata_id DESC");
                            while($aspectos = mysqli_fetch_array($aspectosCosnulta, MYSQLI_BOTH)){
                            ?>
                                


                                <tr style="height: 40px;">
                                    <td><?=$aspectos['mata_fecha_evento'];?></td>
                                    <td><?=$aspectos['uss_nombre'];?></td>
                                    <td><?=$aspectos['mata_descripcion'];?></td>
                                    <td><?=$aspectos['mata_aspectos_positivos'];?></td>
                                    <td><?=$aspectos['mata_aspectos_mejorar'];?></td>
                                    <td><?=$aspectos['mata_tratamiento'];?></td>

                                    <td>
                                                            <?php if($aspectos['mata_aprobacion_acudiente']==0 and $datosUsuarioActual['uss_tipo'] == TIPO_ACUDIENTE){?> 
                                                                <a href="#reportes-disciplinarios.php?usrEstud=<?=$_GET["usrEstud"];?>&req=1&id=<?=$aspectos['dr_id'];?>">Firmar</a>
                                                            <?php } else{?>
                                                                <i class="fa fa-check-circle" title="<?=$aspectos['mata_aprobacion_acudiente_fecha'];?>"></i>
                                                            <?php }?>
                                    </td>

                                    <td>
                                        <?php if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO){
                                            $href='../compartido/aspectos-estudiantiles-eliminar.php?idA='.$aspectos['mata_id'].'&idR='.$_GET["idR"];
                                            ?>
                                            <a href="#" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','<?= $href ?>')" class="btn btn-danger">X</a>
                                        <?php }?>

                                    </td>
                                </tr>
                                

                            <?php }?>

                            </table>

                            </div>

                                    <div class="card-footer">&nbsp;</div>
                                </div>

                                        <?php
                                            $p++;
                                        }
                                        ?>
                                        
                                    


                            
                            
                        </div>
                        
                    </div>
                    <?php 
                            $botones = new botonesGuardar("estudiantes.php", false); ?>
                </div>
            </div>