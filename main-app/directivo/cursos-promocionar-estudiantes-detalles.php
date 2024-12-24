<?php
include("session.php");
$idPaginaInterna = 'DT0145';
include("../compartido/historial-acciones-guardar.php");
    include("../compartido/head.php");

    Utilidades::validarParametros($_GET);

    if(!Modulos::validarSubRol([$idPaginaInterna])){
        echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
        exit();
    }
    require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
    require_once(ROOT_PATH."/main-app/class/Grados.php");
    require_once(ROOT_PATH."/main-app/class/Grupos.php");
    require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

    $disabledPermiso = "";
    if(!Modulos::validarPermisoEdicion()){
        $disabledPermiso = "disabled";
}

$disabled = "";
if(!empty($_POST['escogioCursos']) || !empty($_POST['relacionoMaterias'])){
	$disabled = "disabled";
}

$display = "none";
if(!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1){
	$display = "flex";
}

?>
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!--tagsinput-->
<link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
<!-- data tables -->
<link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<!--select2-->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <?php include("../compartido/texto-manual-ayuda.php");?>
                    <div class="page-title-breadcrumb">
                        <div class=" pull-left">
                                <div class="page-title">Promocionar Estudiantes</div>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="javascript:void(0);" name="cursos.php" onClick="deseaRegresar(this)">Cursos</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Promocionar Estudiantes</li>
                        </ol>
                    </div>
                </div>
                <?php include("../../config-general/mensajes-informativos.php"); ?>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="panel">
                            <header class="panel-heading panel-heading-blue">Paso a paso</header>
                            <div class="panel-body">
                                <p <?php if (empty($_POST['escogioCursos']) && empty($_POST['relacionoMaterias'])) { echo 'style="background-color: #ff572238;"'; } ?>>
                                    <b>1.</b> Debe escoger el curso desde donde desea promover los estudiantes, seguido de esto debe escoger el curso al que desea mover los estudiantes, luego debes seleccionar si deseas relacionar las cargas y dele click al botón continuar.
                                </p>
                                <p <?php if (!empty($_POST['escogioCursos']) && (!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1)) { echo 'style="background-color: #ff572238;"'; } ?>>
                                    <b>2.</b> Relacione las asignaturas del curso a promover con las asignaturas del curso al que se van a mover los estudiantes y dele click al botón continuar, este paso es necesario para pasar las definitivas de cada estudiante.
                                </p>
                                <p <?php if (!empty($_POST['relacionoMaterias']) || (!empty($_POST['escogioCursos']) && empty($_POST['relacionCargas']))) { echo 'style="background-color: #ff572238;"'; } ?>>
                                    <b>3.</b> Finalmente seleccione todos los estudiantes que desea promover, si desea cambiar de grupo a algún estudiante solo debe escoger el grupo en el selector de grupo del estudiante y dele click al botón Realizar promoción.
                                </p>
                            </div>
                        </div>
                        <div class="panel">
                                <header class="panel-heading panel-heading-blue">Consideraciones</header>
                                <div class="panel-body">
                                    <p><b>-></b> <mark>Si escojes la opción de relacionar las cargas,</mark> deberas escojer un grupo del curso desde donde deseas promocionar los estudiantes y un grupo para el curso al que desea mover esos estudiantes.</p>
                                    <p><b>-></b> Si no escojes la opción de relacionar las cargas y desea cambiar el grupo de un estudiante, escoja el nuevo grupo en el selector frente al estudiante en la columna "GRUPO".</p>
                                </div>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="panel">
                            <header class="panel-heading panel-heading-purple">Promocionar Estudiantes</header>
                            <div class="panel-body">
                                <form action="cursos-promocionar-estudiantes-detalles.php" method="post" enctype="multipart/form-data">
                                    <i class="bi bi-eye-slash"></i>

                                    <div class="form-group row">
                                        <label class="col-sm-2">Desde El Curso:</label>
                                        <div class="col-sm-3">
                                            <?php
                                            $opcionesConsulta = Grados::listarGrados();
                                            ?>
                                            <select class="form-control select2" name="desde" required <?=$disabledPermiso;?> <?=$disabled;?>>
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                    if(!empty($_POST['desde']) && $opcionesDatos['gra_id']==$_POST['desde'])
                                                        echo '<option value="'.$opcionesDatos['gra_id'].'" selected>'.$opcionesDatos['gra_nombre'].'</option>';
                                                    else
                                                        echo '<option value="'.$opcionesDatos['gra_id'].'">'.$opcionesDatos['gra_nombre'].'</option>';	
                                                }?>
                                            </select>
                                        </div>
                                        
                                        <label class="col-sm-2">Se Moverán Al Curso:</label>
                                        <div class="col-sm-3">
                                            <?php
                                            $opcionesConsulta = Grados::listarGrados();
                                            ?>
                                            <select class="form-control select2" name="para" required <?=$disabledPermiso;?> <?=$disabled;?>>
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                    if(!empty($_POST['para']) && $opcionesDatos['gra_id']==$_POST['para'])
                                                        echo '<option value="'.$opcionesDatos['gra_id'].'" selected>'.$opcionesDatos['gra_nombre'].'</option>';
                                                    else
                                                        echo '<option value="'.$opcionesDatos['gra_id'].'">'.$opcionesDatos['gra_nombre'].'</option>';	
                                                }?>
                                            </select>
                                        </div>
                                        
                                        <label class="col-sm-1">Relacionar Cargas: 
                                            <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Podrás relacionar las cargas del curso actual con las cargas del curso al que deseas promocionar al estudiante, no podrás cambiar el grupo de los estudiantes de forma individual."><i class="fa fa-info"></i></button>
                                        </label>
                                        <div class="col-sm-1">
                                            <div class="input-group spinner">
                                                <label class="switchToggle">
                                                    <input type="checkbox" name="relacionCargas" id="relacionCargas" <?=!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1 ? "checked" : ""?> onchange="relacionCargasGrupos(this)" value="1" <?=$disabledPermiso;?> <?=$disabled;?>>
                                                    <span class="slider green round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row" id="elementGroup" style="display: <?=$display?>;">
                                        <label class="col-sm-2">Desde El Grupo:</label>
                                        <div class="col-sm-3">
                                            <select class="form-control  select2" style="width: 100%;" name="grupoDesde" id="grupoDesde" <?=$disabledPermiso;?> <?=$disabled;?>>
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                    $opcionesConsulta = Grupos::listarGrupos();
                                                    while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                        if(!empty($_POST['grupoDesde']) && $opcionesDatos['gru_id']==$_POST['grupoDesde'])
                                                            echo '<option value="'.$opcionesDatos['gru_id'].'" selected>'.$opcionesDatos['gru_nombre'].'</option>';
                                                        else
                                                            echo '<option value="'.$opcionesDatos['gru_id'].'">'.$opcionesDatos['gru_nombre'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <label class="col-sm-2">Se Moverán Al Grupo:</label>
                                        <div class="col-sm-3">
                                            <select class="form-control  select2" style="width: 100%;" name="grupoPara" id="grupoPara" <?=$disabledPermiso;?> <?=$disabled;?>>
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                    $opcionesConsulta = Grupos::listarGrupos();
                                                    while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                        if(!empty($_POST['grupoPara']) && $opcionesDatos['gru_id']==$_POST['grupoPara'])
                                                            echo '<option value="'.$opcionesDatos['gru_id'].'" selected>'.$opcionesDatos['gru_nombre'].'</option>';
                                                        else
                                                            echo '<option value="'.$opcionesDatos['gru_id'].'">'.$opcionesDatos['gru_nombre'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if(empty($_POST['escogioCursos']) && empty($_POST['relacionoMaterias']) && Modulos::validarPermisoEdicion()){ ?>
                                        <div class="form-group">
                                            <div class="col-md-9">
                                                <input type="submit" class="btn btn-success" name="escogioCursos" value="Continuar">
                                            </div>
                                        </div>
                                    <?php } ?>
                                </form>
                                <?php 
                                    if(!empty($_POST['escogioCursos']) && (!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1)){ 
                                        $filtro='';
                                        if(!empty($_POST['desde'])) {
                                            $filtro .= " AND car_curso='".$_POST['desde']."'";
                                        }
                                        if(!empty($_POST['grupoDesde'])) {
                                            $filtro .= " AND car_grupo='".$_POST['grupoDesde']."'";
                                        }
                                        $consultaCargas = CargaAcademica::listarCargas($conexion, $config, "", $filtro, "mat_id, car_grupo");
                                        
                                        $filtroPara='';
                                        if(!empty($_POST['para'])) {
                                            $filtroPara .= " AND car_curso='".$_POST["para"]."'";
                                        }
                                        if(!empty($_POST['grupoPara'])) {
                                            $filtroPara .= " AND car_grupo='".$_POST['grupoPara']."'";
                                        }
                                ?>
                                    <form action="cursos-promocionar-estudiantes-detalles.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="desde" value="<?=$_POST["desde"];?>">
                                        <input type="hidden" name="para" value="<?=$_POST["para"];?>">
                                        <input type="hidden" name="grupoDesde" value="<?=$_POST["grupoDesde"];?>">
                                        <input type="hidden" name="grupoPara" value="<?=$_POST["grupoPara"];?>">
                                        <input type="hidden" name="relacionCargas" value="<?=$_POST["relacionCargas"];?>">
                                        <div id="divCargas"></div>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <div class="card card-topline-purple">
                                                    <div class="card-head">
                                                        <header>Relacionar Materias</header>
                                                    </div>
                                                    <div class="card-body">

                                                        <div>  
                                                            <table id="example3" class="display" name="tabla1" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>COD</th>
                                                                        <th>CARGAS ACTUALES</th>
                                                                        <th>CARGAS A RELACIONAR</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="tbody">
                                                                    <?php
                                                                        $contReg=1;
                                                                        while($datosCarga = mysqli_fetch_array($consultaCargas, MYSQLI_BOTH)){
                                                                            $nombreDocente = UsuariosPadre::nombreCompletoDelUsuario($datosCarga);
                                                                    ?>
                                                                        <tr >
                                                                            <td><?= $contReg; ?></td>
                                                                            <td><?=$datosCarga['car_id'];?></td>
                                                                            <td>
                                                                                Grupo: <?=$datosCarga['gru_nombre'];?><br>
                                                                                Materia: <?=$datosCarga['mat_nombre'];?><br>
                                                                                Docente: <?=$nombreDocente;?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group row">
                                                                                    <div class="col-sm-10">
                                                                                        <select class="form-control  select2"  onchange="crearInputCarga(this)" id="carga<?=$datosCarga['car_id'];?>" data-carga="<?=$datosCarga['car_id'];?>" <?=$disabledPermiso;?>>
                                                                                            <option value="">Seleccione una opción</option>
                                                                                            <?php
                                                                                            $consultaCargasPara = CargaAcademica::listarCargas($conexion, $config, "", $filtroPara, "mat_id, car_grupo");
                                                                                            while($opcionesDatos = mysqli_fetch_array($consultaCargasPara, MYSQLI_BOTH)){
                                                                                                $nombreDocentePara = UsuariosPadre::nombreCompletoDelUsuario($datosCarga);
                                                                                            ?>
                                                                                                <option value="<?=$opcionesDatos['car_id'];?>">
                                                                                                Grupo: <?=$opcionesDatos['gru_nombre'];?> Materia: <?=$opcionesDatos['mat_nombre'];?> Docente: <?=$nombreDocentePara;?>
                                                                                                </option>
                                                                                            <?php }?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    <?php $contReg++;
                                                                    } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(Modulos::validarPermisoEdicion()){ ?>
                                            <div class="form-group">
                                                <div class="col-md-9">
                                                <input type="submit" class="btn btn-success" name="relacionoMaterias" value="Continuar">
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </form>
                                <?php 
                                    }

                                    if(!empty($_POST['relacionoMaterias']) || (!empty($_POST['escogioCursos']) && empty($_POST['relacionCargas']))){ 
                                        $filtro = " AND (mat_promocionado=0 OR mat_promocionado=NULL)";
                                        $curso='';
                                        if(!empty($_POST['desde'])) {
                                            $curso=$_POST["desde"];
                                            $filtro .= " AND mat_grado='".$curso."'";
                                        }
                                        $grupo='';
                                        if(!empty($_POST['grupoDesde'])) {
                                            $grupo=$_POST["grupoDesde"];
                                            $filtro .= " AND mat_grupo='".$grupo."'";
                                        }
                                        
                                        $consultaEstudiantes = Estudiantes::listarEstudiantesEnGrados($filtro);
                                        $numeroEstudiantes=mysqli_num_rows($consultaEstudiantes);
                                ?>
                                    <form action="cursos-promocionar-estudiantes.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="desde" value="<?=$_POST["desde"];?>">
                                        <input type="hidden" name="para" value="<?=$_POST["para"];?>">
                                        <input type="hidden" name="grupoDesde" value="<?=!empty($_POST["grupoDesde"]) ? $_POST["grupoDesde"] : "";?>">
                                        <input type="hidden" name="grupoPara" value="<?=!empty($_POST["grupoPara"]) ? $_POST["grupoPara"] : "";?>">
                                        <input type="hidden" name="relacionCargas" value="<?=!empty($_POST["relacionCargas"]) ? $_POST["relacionCargas"] : 0;?>">
                                        <div id="divEstudiante"></div>
                                        <?php
                                            if(!empty($_POST['relacionCargas']) && $_POST['relacionCargas'] == 1){ 
                                            $filtroCarga = " AND car_curso='".$curso."' AND car_grupo='".$grupo."'";
                                            $consultaCargas2 = CargaAcademica::listarCargas($conexion, $config, "", $filtroCarga, "mat_id, car_grupo");
                                            while($datosCarga2 = mysqli_fetch_array($consultaCargas2, MYSQLI_BOTH)){
                                                $fieldName = "carga".$datosCarga2['car_id'];
                                        ?>
                                            <input type="hidden" name="<?=$fieldName;?>" value="<?=$_POST['carga'.$datosCarga2['car_id']];?>">
                                        <?php }} ?>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <div class="card card-topline-purple">
                                                    <div class="card-head">
                                                        <header>Estudiantes ( <label  style="font-weight: bold;" id="cantSeleccionadas" ></label>/<?= $numeroEstudiantes ?> )</header>
                                                    </div>
                                                    <div class="card-body">

                                                        <div>  
                                                            <table id="example3" class="display" name="tabla1" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>
                                                                            <div class="input-group spinner col-sm-10">
                                                                                <label class="switchToggle">
                                                                                    <input type="checkbox" id="all"  <?=$disabledPermiso;?>>
                                                                                    <span class="slider green round"></span>
                                                                                </label>
                                                                            </div>
                                                                        </th>
                                                                        <th>DOCUMENTO</th>
                                                                        <th>NOMBRES Y APELLIDOS</th>
                                                                        <?php if(empty($_POST['relacionCargas']) || $_POST['relacionCargas'] == 0){ ?>
                                                                            <th>GRUPO</th>
                                                                        <?php } ?>
                                                                        <th>EST. MATRICULA</th>
                                                                        <th>CAMBIAR ESTADO<br> A MATRICULADO?</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="tbody">
                                                                    <?php
                                                                        $contReg=1;
                                                                        while($datosEstudiante = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH)){
                                                                            $nombre = Estudiantes::NombreCompletoDelEstudiante($datosEstudiante);
                                                                    ?>
                                                                        <tr >
                                                                            <td><?= $contReg; ?></td>
                                                                            <td>
                                                                                <div class="input-group spinner col-sm-10">
                                                                                    <label class="switchToggle">
                                                                                        <input type="checkbox" class="check" onchange="seleccionarEstudiantes(this)" value="<?=$datosEstudiante['mat_id'];?>" data-grupo="<?=$datosEstudiante['mat_grupo'];?>" <?=$disabledPermiso;?>>
                                                                                        <span class="slider green round"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </td>
                                                                            <td><?=$datosEstudiante['mat_documento'];?></td>
                                                                            <td><?=$nombre;?></td>
                                                                            <?php if(empty($_POST['relacionCargas']) || $_POST['relacionCargas'] == 0){ ?>
                                                                                <td>
                                                                                    <div class="form-group row">
                                                                                        <div class="col-sm-4">
                                                                                            <select class="form-control  select2" onchange="crearInputGrupoEstudiante(this, '<?=$datosEstudiante['mat_id'];?>', 'noGrupo')" id="grupo<?=$datosEstudiante['mat_id'];?>" <?=$disabledPermiso;?>>
                                                                                                <option value="">Seleccione una opción</option>
                                                                                                <?php
                                                                                                $opcionesConsulta = Grupos::listarGrupos();
                                                                                                while($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)){
                                                                                                    $selected="";
                                                                                                    if($datosEstudiante['mat_grupo']==$opcionesDatos['gru_id']){
                                                                                                        $selected="selected";
                                                                                                    }
                                                                                                ?>
                                                                                                    <option value="<?=$opcionesDatos['gru_id'];?>" <?=$selected;?>><?=$opcionesDatos['gru_id'].". ".strtoupper($opcionesDatos['gru_nombre']);?></option>
                                                                                                <?php }?>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            <?php } ?>
                                                                            <td class="<?=$estadosEtiquetasMatriculas[$datosEstudiante['mat_estado_matricula']];?>"><?=$estadosMatriculasEstudiantes[$datosEstudiante['mat_estado_matricula']];?></td>
                                                                            <td>
                                                                                <?php if($datosEstudiante['mat_estado_matricula'] != MATRICULADO){ ?>
                                                                                    <div class="input-group spinner">
                                                                                        <label class="switchToggle">
                                                                                            <input type="checkbox" id="cambiarEstado<?=$datosEstudiante['mat_id'];?>" data-id-estudiante="<?=$datosEstudiante['mat_id'];?>" onchange="crearInputEstadoEstudiante(this)" value="1" disabled>
                                                                                            <span class="slider green round"></span>
                                                                                        </label>
                                                                                    </div>
                                                                                <?php } ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php $contReg++;
                                                                    } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($numeroEstudiantes>0 && Modulos::validarPermisoEdicion()){ ?>
                                            <div class="form-group">
                                                <div class="col-md-9">
                                                    <button type="submit" class="btn btn-success">Realizar promoción</button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <select  id="estudiantesSeleccionados"  style="width: 100% !important" name="estudiantes[]" multiple hidden>
                                        </select>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end page container -->
<?php include("../compartido/footer.php"); ?>
<script src="../js/Cursos.js" ></script>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- data tables -->
<script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
<script src="../../config-general/assets/js/pages/table/table_data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- Material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!--select2-->
<script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
<!-- end js include path -->

</body>

</html>