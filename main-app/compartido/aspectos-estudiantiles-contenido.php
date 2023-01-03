<?php
$datosEditar = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_matriculas
LEFT JOIN usuarios ON uss_id=mat_acudiente
INNER JOIN academico_grados ON gra_id=mat_grado
WHERE mat_id_usuario='".$_GET["idR"]."'"), MYSQLI_BOTH);


$usuarioEstudiante = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM usuarios
WHERE uss_id='".$_GET["idR"]."'"), MYSQLI_BOTH);


$agnoNacimiento = mysqli_fetch_array(mysqli_query($conexion, "SELECT YEAR(mat_fecha_nacimiento) FROM academico_matriculas
WHERE mat_id_usuario='".$_GET["idR"]."'"), MYSQLI_BOTH);


$edad = date("Y") - $agnoNacimiento[0];

$estadoAgno = array("EN CURSO", "SI", "NO");
?>

<div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class="pull-left">
                                <div class="page-title">Aspectos estudiantiles</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12">

                            <?php if($datosUsuarioActual['uss_tipo'] == 5 or $datosUsuarioActual['uss_tipo'] == 2){?>
                                <a href="reportes-lista.php?est=<?=$_GET['idR'];?>&fest=1" class="btn btn-danger" target="_blank">Faltas</a>
                            <?php }?>
                            

                            <?php if($datosUsuarioActual['uss_tipo'] == 5){?>

                                <a href="estudiantes-editar.php?idR=<?=$_GET['idR'];?>" class="btn btn-info" target="_blank">Editar información</a>

                            <?php }?>

                                <div style="text-align: right;">
                                    <img src="../files/fotos/<?=$usuarioEstudiante['uss_foto'];?>" width="150" />
                                </div>


                            <div class="card card-box">
                                
                                <div class="card-body " id="bar-parent6">

                                    <table border="1" rules="group" width="100%">
                                        <tr>
                                            <td style="background-color: lightgray;">Estudiante:</td>
                                            <td colspan="3"><?=$datosEditar['mat_primer_apellido']." ".$datosEditar['mat_segundo_apellido']." ".$datosEditar['mat_nombres'];?></td>
                                            <td style="background-color: lightgray;">Curso:</td>
                                            <td><?=$datosEditar['gra_nombre'];?></td>
                                            <td style="background-color: lightgray;">D.I:</td>
                                            <td><?=$datosEditar['mat_documento'];?></td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Fecha de nacimiento:</td>
                                            <td><?=$datosEditar['mat_fecha_nacimiento'];?></td>
                                            <td style="background-color: lightgray;">Edad:</td>
                                            <td><?=$edad;?></td>
                                            <td style="background-color: lightgray;">Tipo de RH:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;">EPS:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Email acudiente:</td>
                                            <td colspan="3"><?=$datosEditar['uss_email'];?></td>
                                            <td style="background-color: lightgray;">Número de hermanos:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;">Lugar que ocupa entre ellos:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Dirección:</td>
                                            <td colspan="3"><?=$datosEditar['mat_direccion'];?></td>
                                            <td style="background-color: lightgray;">Barrio:</td>
                                            <td><?=$datosEditar['mat_barrio'];?></td>
                                            <td style="background-color: lightgray;">Teléfono fijo:</td>
                                            <td><?=$datosEditar['mat_telefono'];?></td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Nombre Mamá:</td>
                                            <td colspan="3"></td>
                                            <td style="background-color: lightgray;">Teléfono:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;">Dirección:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Nombre Papá:</td>
                                            <td colspan="3"></td>
                                            <td style="background-color: lightgray;">Teléfono:</td>
                                            <td>&nbsp;</td>
                                            <td style="background-color: lightgray;">Dirección:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td style="background-color: lightgray;">Acudiente:</td>
                                            <td><?=$datosEditar['uss_nombre'];?></td>
                                            <td style="background-color: lightgray;">Teléfono:</td>
                                            <td><?=$datosEditar['uss_telefono'];?></td>
                                            <td style="background-color: lightgray;">Dirección:</td>
                                            <td><?=$datosEditar['uss_direccion'];?></td>
                                            <td style="background-color: lightgray;">Parentezco:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                    </table>
                                    
                                </div>
                            </div>


                           <?php if($datosUsuarioActual['uss_tipo'] == 5 or $datosUsuarioActual['uss_tipo'] == 2){?>
                            <div class="card card-box">
                                <div class="card-head">
                                    <header>Aspectos estudiantiles</header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="28">
                                        <input type="hidden" name="estudiante" value="<?=$datosEditar['mat_id'];?>">

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Fecha</label>
                                            <div class="col-sm-4">  
                                                <input type="date" name="fecha" class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Periodo(Coloque el número)</label>
                                            <div class="col-sm-2">  
                                                <input type="number" name="periodo" class="form-control">
                                            </div>
                                        </div>

                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Descripción de la situación</label>
                                            <div class="col-sm-10">  
                                                <textarea name="descripcion" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Aspectos positivos</label>
                                            <div class="col-sm-10">  
                                                <textarea name="positivos" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Aspectos a mejorar</label>
                                            <div class="col-sm-10">  
                                                <textarea name="mejorar" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Tratamiento</label>
                                            <div class="col-sm-10">  
                                                <textarea name="tratamiento" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        
                                        <input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
                                        
                                        <a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>

                                    </form>
                                </div>
                            </div>



                            <div class="card card-box">
                                <div class="card-head">
                                    <header>Aspectos estudiantiles (Docentes)</header>
                                </div>
                                <div class="card-body " id="bar-parent6">
                                    <form class="form-horizontal" action="../compartido/guardar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="29">
                                        <input type="hidden" name="estudiante" value="<?=$datosEditar['mat_id'];?>">
                                        <input type="hidden" name="curso" value="<?=$datosEditar['mat_grado'];?>">



                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Periodo(Coloque el número)</label>
                                            <div class="col-sm-2">  
                                                <input type="number" name="periodo" class="form-control">
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Aspectos académicos</label>
                                            <div class="col-sm-10">  
                                                <textarea name="academicos" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Aspectos convivenciales</label>
                                            <div class="col-sm-10">  
                                                <textarea name="convivenciales" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
                                            </div>
                                        </div>

                                        
                                        <input type="submit" class="btn btn-primary" value="Guardar cambios">&nbsp;
                                        
                                        <a href="#" name="noticias.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>

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
                                                <td colspan="2" align="center">PERIODO <?=$p;?></td>
                                            </tr>

                                            <tr style="font-weight: bold;">
                                                <td align="center" width="40%">ASPECTOS ACADÉMICOS</td>
                                                <td align="center" width="40%">ASPECTOS DISCIPLINARIOS</td>
                                                <td align="right" width="20%">&nbsp;</td>
                                            </tr>

                                            <tr style="height: 60px;">
                                                <td><?=$aspectos['dn_aspecto_academico'];?></td>
                                                <td><?=$aspectos['dn_aspecto_convivencial'];?></td>
                                                <td>
                                                    <?php if($datosUsuarioActual['uss_tipo'] == 5){?>
                                                        <a href="../compartido/guardar.php?get=27&idR=<?=$aspectos['dn_id'];?>" onClick="if(!confirm('Desea eliminar este registro?')){return false;}" class="btn btn-danger">X</a>
                                                    <?php }?>

                                                </td>
                                            </tr>

                                            <?php if($p == 4){?>

                                                <tfoot>
                                                    <tr style="font-weight: bold;">
                                                        <td align="right">PROMOVIDO: </td>
                                                        <td><?=$estadoAgno[$datosEditar['mat_estado_agno']];?></td>
                                                    </tr>  
                                                </tfoot>

                                            <?php }?>


                                            </table>

                                    
                                </div>
                            </div>



                            <div class="card card-box">
                                    <div class="card-head">
                                        <header>OBSERVADOR DEL ALUMNO</header>
                                    </div>

                                    <div class="card-body">

                            <table width="100%">

                                <tr style="font-weight: bold;">
                                    <td>FECHA</td>
                                    <td>NOMBRE DEL USUARIO</td>
                                    <td>DESCRIPCIÓN DE LA SITUACIÓN</td>
                                    <td>ASPECTOS POSITIVOS</td>
                                    <td>ASPECTOS A MEJORAR</td>
                                    <td>TRATAMIENTO</td>
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
                                                                <a href="#reportes-disciplinarios.php?usrEstud=<?=$_GET["usrEstud"];?>&req=1&id=<?=$resultado['dr_id'];?>">Firmar</a>
                                                            <?php } else{?>
                                                                <i class="fa fa-check-circle" title="<?=$resultado['mata_aprobacion_acudiente_fecha'];?>"></i>
                                                            <?php }?>
                                    </td>

                                    <td>
                                        <?php if($datosUsuarioActual['uss_tipo'] == 5){?>
                                            <a href="../compartido/guardar.php?get=26&idR=<?=$aspectos['mata_id'];?>" onClick="if(!confirm('Desea eliminar este registro?')){return false;}" class="btn btn-danger">X</a>
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