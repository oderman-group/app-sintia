<?php
$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}
require_once("../class/Estudiantes.php");
require_once("../class/UsuariosPadre.php");
$datosEditar = Estudiantes::obtenerDatosEstudiantePorIdUsuario($idR);

$usuarioEstudiante = UsuariosPadre::sesionUsuario($idR);

$agnoNacimiento = mysqli_fetch_array(mysqli_query($conexion, "SELECT YEAR(mat_fecha_nacimiento) FROM academico_matriculas
WHERE mat_id_usuario='".$idR."'"), MYSQLI_BOTH);


$edad = date("Y") - $agnoNacimiento[0];

$estadoAgno = array("EN CURSO", "SI", "NO");
?>

<div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class="pull-left">
                                <div class="page-title"><?=$frases[292][$datosUsuarioActual[8]];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12">

                            <?php if($datosUsuarioActual['uss_tipo'] == 5 or $datosUsuarioActual['uss_tipo'] == 2){?>
                                <a href="reportes-lista.php?est=<?=$_GET["idR"];?>&fest=<?=base64_encode(1);?>" class="btn btn-danger" target="_blank"><?=strtoupper($frases[248][$datosUsuarioActual[8]]);?></a>
                            <?php }?>
                            

                            <?php if($datosUsuarioActual['uss_tipo'] == 5){?>

                                <a href="estudiantes-editar.php?idR=<?=$_GET["idR"];?>" class="btn btn-info" target="_blank"><?=strtoupper($frases[291][$datosUsuarioActual[8]]);?></a>

                            <?php }?>

                                <div style="text-align: right;">
                                    <img src="../files/fotos/<?=$usuarioEstudiante['uss_foto'];?>" width="150" />
                                </div>


                            <div class="card card-box">
                                
                                <div class="card-body " id="bar-parent6">

                                    <table border="1" rules="group" width="100%">
                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[61][$datosUsuarioActual[8]];?>:</td>
                                            <td colspan="3"><?=$datosEditar['mat_primer_apellido']." ".$datosEditar['mat_segundo_apellido']." ".$datosEditar['mat_nombres'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[164][$datosUsuarioActual[8]];?>:</td>
                                            <td><?=$datosEditar['gra_nombre'];?></td>
                                            <td style="background-color: lightgray;">D.I:</td>
                                            <td><?=$datosEditar['mat_documento'];?></td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[189][$datosUsuarioActual[8]];?>:</td>
                                            <td><?=$datosEditar['mat_fecha_nacimiento'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[293][$datosUsuarioActual[8]];?>:</td>
                                            <td><?=$edad;?></td>
                                            <td style="background-color: lightgray;"><?=$frases[294][$datosUsuarioActual[8]];?> RH:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;">EPS:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Email acudiente:</td>
                                            <td colspan="3"><?=$datosEditar['uss_email'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[295][$datosUsuarioActual[8]];?>:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;"><?=$frases[296][$datosUsuarioActual[8]];?>:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[297][$datosUsuarioActual[8]];?>:</td>
                                            <td colspan="3"><?=$datosEditar['mat_direccion'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[298][$datosUsuarioActual[8]];?>:</td>
                                            <td><?=$datosEditar['mat_barrio'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[182][$datosUsuarioActual[8]];?>:</td>
                                            <td><?=$datosEditar['mat_telefono'];?></td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[301][$datosUsuarioActual[8]];?>:</td>
                                            <td colspan="3"></td>
                                            <td style="background-color: lightgray;"><?=$frases[182][$datosUsuarioActual[8]];?>:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;"><?=$frases[297][$datosUsuarioActual[8]];?>:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;"><?=$frases[300][$datosUsuarioActual[8]];?>:</td>
                                            <td colspan="3"></td>
                                            <td style="background-color: lightgray;"><?=$frases[182][$datosUsuarioActual[8]];?>:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;"><?=$frases[297][$datosUsuarioActual[8]];?>:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Acudiente:</td>
                                            <td><?=$datosEditar['uss_nombre'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[182][$datosUsuarioActual[8]];?>:</td>
                                            <td><?=$datosEditar['uss_telefono'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[297][$datosUsuarioActual[8]];?>:</td>
                                            <td><?=$datosEditar['uss_direccion'];?></td>
                                            <td style="background-color: lightgray;"><?=$frases[299][$datosUsuarioActual[8]];?>:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                    </table>
                                    
                                </div>
                            </div>


                           <?php if($datosUsuarioActual['uss_tipo'] == 5 or $datosUsuarioActual['uss_tipo'] == 2){?>
                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[292][$datosUsuarioActual[8]];?></header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="28">
                                        <input type="hidden" name="estudiante" value="<?=$datosEditar['mat_id'];?>">

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[51][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-4">  
                                                <input type="date" name="fecha" class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[27][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-2">  
                                                <input type="number" name="periodo" class="form-control">
                                            </div>
                                        </div>

                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[302][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="descripcion" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[303][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="positivos" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[304][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="mejorar" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[305][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="tratamiento" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        
                                        <input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
                                        
                                        <a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i><?=$frases[184][$datosUsuarioActual[8]];?></a>

                                    </form>
                                </div>
                            </div>



                            <div class="card card-box">
                                <div class="card-head">
                                    <header><?=$frases[292][$datosUsuarioActual[8]];?> (<?=$frases[28][$datosUsuarioActual[8]];?>)</header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="29">
                                        <input type="hidden" name="estudiante" value="<?=$datosEditar['mat_id'];?>">
                                        <input type="hidden" name="curso" value="<?=$datosEditar['mat_grado'];?>">



                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[27][$datosUsuarioActual[8]];?></label>
                                            <div class="col-sm-2">  
                                                <input type="number" name="periodo" class="form-control">
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=strtoupper($frases[281][$datosUsuarioActual[8]]);?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="academicos" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=strtoupper($frases[282][$datosUsuarioActual[8]]);?></label>
                                            <div class="col-sm-10">  
                                                <textarea name="convivenciales" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        
                                        <input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
                                        
                                        <a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i><?=$frases[184][$datosUsuarioActual[8]];?></a>

                                    </form>
                                </div>
                            </div>

                        <?php }?>

                        </div>
                        

                        <div class="col-sm-12">


                            


                            

                                        <?php
                                        $p=1;
                                        while($p<=4){

                                            $aspectos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM disiplina_nota 
                                            WHERE dn_cod_estudiante='".$datosEditar['mat_id']."' AND dn_periodo='".$p."'"), MYSQLI_BOTH);

                                        ?>

                                            <div class="card card-box">
                                
                                <div class="card-body " id="bar-parent6">


                                    <table width="100%">
                                            <tr style="font-weight: bold;">
                                                <td colspan="2" align="center"><?=strtoupper($frases[27][$datosUsuarioActual[8]]);?> <?=$p;?></td>
                                            </tr>

                                            <tr style="font-weight: bold;">
                                                <td align="center" width="40%"><?=strtoupper($frases[281][$datosUsuarioActual[8]]);?></td>
                                                <td align="center" width="40%"><?=strtoupper($frases[282][$datosUsuarioActual[8]]);?></td>
                                                <td align="right" width="20%">&nbsp;</td>
                                            </tr>

                                            <tr style="height: 60px;">
                                                <td><?php if(!empty($aspectos['dn_aspecto_academico'])){ echo $aspectos['dn_aspecto_academico'];}?></td>
                                                <td><?php if(!empty($aspectos['dn_aspecto_convivencial'])){ echo $aspectos['dn_aspecto_convivencial'];}?></td>
                                                <td>
                                                    <?php if($datosUsuarioActual['uss_tipo'] == 5 && !empty($aspectos)){
                                                        $href='../compartido/guardar.php?get=27&idR='.$aspectos['dn_id'];?>
                                                        <a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','<?= $href ?>')" class="btn btn-danger">X</a>
                                                    <?php }?>

                                                </td>
                                            </tr>

                                            <?php if($p == 4){?>

                                                <tfoot>
                                                    <tr style="font-weight: bold;">
                                                        <td align="right"><?=strtoupper($frases[308][$datosUsuarioActual[8]]);?>: </td>
                                                        <td><?=$estadoAgno[$datosEditar['mat_estado_agno']];?></td>
                                                    </tr>  
                                                </tfoot>

                                            <?php }?>


                                            </table>

                                    
                                </div>
                            </div>



                            <div class="card card-box">
                                    <div class="card-head">
                                        <header><?=strtoupper($frases[306][$datosUsuarioActual[8]]);?></header>
                                    </div>

                                    <div class="card-body">

                            <table width="100%">

                                <tr style="font-weight: bold;">
                                    <td><?=strtoupper($frases[51][$datosUsuarioActual[8]]);?></td>
                                    <td><?=strtoupper($frases[307][$datosUsuarioActual[8]]);?></td>
                                    <td><?=strtoupper($frases[302][$datosUsuarioActual[8]]);?></td>
                                    <td><?=strtoupper($frases[303][$datosUsuarioActual[8]]);?></td>
                                    <td><?=strtoupper($frases[304][$datosUsuarioActual[8]]);?></td>
                                    <td><?=strtoupper($frases[305][$datosUsuarioActual[8]]);?></td>
                                    <th title="Firma y aprobación del acudiente">F.A</th>
                                    <td>&nbsp;</td>
                                </tr>
                                
                            
                            
                                
                            <?php
                            $aspectosCosnulta = mysqli_query($conexion, "SELECT * FROM matriculas_aspectos
                                INNER JOIN usuarios ON uss_id=mata_usuario
                                WHERE mata_estudiante='".$datosEditar['mat_id']."' AND mata_periodo='".$p."' ORDER BY mata_id DESC");
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
                                                            <?php if($aspectos['mata_aprobacion_acudiente']==0 and $datosUsuarioActual['uss_tipo'] == 3){?> 
                                                                <a href="#reportes-disciplinarios.php?usrEstud=<?=$_GET["usrEstud"];?>&req=1&id=<?=$aspectos['dr_id'];?>">Firmar</a>
                                                            <?php } else{?>
                                                                <i class="fa fa-check-circle" title="<?=$aspectos['mata_aprobacion_acudiente_fecha'];?>"></i>
                                                            <?php }?>
                                    </td>

                                    <td>
                                        <?php if($datosUsuarioActual['uss_tipo'] == 5){
                                            $href='../compartido/guardar.php?get=26&idR='.$aspectos['mata_id'];
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
					
                </div>
            </div>