<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
    require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
    require_once(ROOT_PATH."/main-app/class/Boletin.php");
    require_once(ROOT_PATH."/main-app/class/Usuarios.php");
    require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
    require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
    require_once(ROOT_PATH."/main-app/class/Indicadores.php");
    require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
    require_once(ROOT_PATH."/main-app/class/Utilidades.php");
    require_once(ROOT_PATH . "/main-app/class/Disciplina.php");
    $Plataforma = new Plataforma;

    $year=$_SESSION["bd"];
    if(isset($_REQUEST["year"])){
    $year=base64_decode($_REQUEST["year"]);
    }

    $modulo = 1;

    if (empty($_REQUEST["periodo"])) {
        $periodoActual = 1;
    } else {
        $periodoActual = base64_decode($_REQUEST["periodo"]);
    }

    switch($periodoActual){
        case 1:
            $periodoActuales = "Uno";
            break;
        case 2:
            $periodoActuales = "Dos";
            break;
        case 3:
            $periodoActuales = "Tres";
            break;
        case 4:
            $periodoActuales = "Final";
            break;
        case 5:
            $periodoActual = 4;
            $periodoActuales = "Final";
            break;
    }

    $filtro = "";
    if (!empty($_REQUEST["id"])) {
        $filtro .= " AND mat_id='" . base64_decode($_REQUEST["id"]) . "'";
    }

    if (!empty($_REQUEST["curso"])) {
        $filtro .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
    }

    if(!empty($_REQUEST["grupo"])){
        $filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";
    }

    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    Utilidades::validarInfoBoletin($matriculadosPorCurso);
    $numeroEstudiantes = mysqli_num_rows($matriculadosPorCurso);


    $idDirector="";
    $periodosCursados=$periodoActual-1;
    $colspan=7+$periodosCursados;
    $contadorEstudiantes=0;
    while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {
        $promedioGeneral = 0;
        $promedioGeneralPeriodos = 0;
        $gradoActual = $matriculadosDatos['mat_grado'];
        $grupoActual = $matriculadosDatos['mat_grupo'];
        switch($matriculadosDatos["gru_id"]){
            case 1:
                $grupo= "Uno";
            break;
            case 2:
                $grupo= "Dos";
            break;
            case 3:
                $grupo= "Tres";
            break;
            case 4:
                $grupo= "Sin Grupo";
            break;
        }
        //METODO QUE ME TRAE EL NOMBRE COMPLETO DEL ESTUDIANTE
        $nombreEstudainte=Estudiantes::NombreCompletoDelEstudiante($matriculadosDatos);
	
        if($matriculadosDatos["mat_grado"]>=12 && $matriculadosDatos["mat_grado"]<=15) {$educacion = "PREESCOLAR";}	
        elseif($matriculadosDatos["mat_grado"]>=1 && $matriculadosDatos["mat_grado"]<=5) {$educacion = "PRIMARIA";}	
        elseif($matriculadosDatos["mat_grado"]>=6 && $matriculadosDatos["mat_grado"]<=9) {$educacion = "SECUNDARIA";}
        elseif($matriculadosDatos["mat_grado"]>=10 && $matriculadosDatos["mat_grado"]<=11) {$educacion = "MEDIA";}	

?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <title>Boletín</title>
        <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
        <!-- favicon -->
        <link rel="shortcut icon" href="../sintia-icono.png" />
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    </head>
    <body style="font-family:Arial; font-size:9px;">
        <div style="margin: 15px 0;">
            <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 13px;">
                <tr>
                    <td rowspan="2" width="20%"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="100%"></td>
                    <td align="center" rowspan="2" width="25%">
                        <h3 style="font-weight:bold; color: #00adefad; margin: 0"><?=strtoupper($informacion_inst["info_nombre"])?></h3><br>
                        <?=$informacion_inst["info_direccion"]?><br>
                        Informes: <?=$informacion_inst["info_telefono"]?><br><br>
                        AÑO LECTIVO: <?=$year?>
                    </td>
                    <td>Documento:<br> <b style="color: #00adefad;"><?=strpos($matriculadosDatos["mat_documento"], '.') !== true && is_numeric($matriculadosDatos["mat_documento"]) ? number_format($matriculadosDatos["mat_documento"],0,",",".") : $matriculadosDatos["mat_documento"];?></b></td>
                    <td>Nombre:<br> <b style="color: #00adefad;"><?=$nombreEstudainte?></b></td>
                    <td>Grado:<br> <b style="color: #00adefad;"><?=strtoupper($matriculadosDatos["gra_nombre"]." ".$grupo)?></b></td>
                </tr>
                <tr>
                    <td>E. Básica:<br> <b style="color: #00adefad;"><?=$educacion?></b></td>
                    <td>Sede:<br> <b style="color: #00adefad;"><?=strtoupper($informacion_inst["info_nombre"])?></b></td>
                    <td>Jornada:<br> <b style="color: #00adefad;"><?=strtoupper($informacion_inst["info_jornada"])?></b></td>
                </tr>
            </table>
            <p>&nbsp;</p>
        </div>
        <table width="100%" cellspacing="5" cellpadding="5" rules="all" style="font-size: 13px;">
            <tr style="text-align:center; font-size: 13px;">
                <td style="color: #b2adad;">
                    <?php
                        $consultaEstiloNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"],$year);
                        $numEstiloNota=mysqli_num_rows($consultaEstiloNota);
                        $i=1;
                        while($estiloNota = mysqli_fetch_array($consultaEstiloNota, MYSQLI_BOTH)){
                            $diagonal=" / ";
                            if($i==$numEstiloNota){
                                $diagonal="";
                            }
                            echo $estiloNota['notip_nombre'].": ".$estiloNota['notip_desde']." - ".$estiloNota['notip_hasta'].$diagonal;
                            $i++;
                        }
                    ?>
                </td>
            </tr>
        </table>
        <table width="100%" rules="all" border="1" style="font-size: 15px;">
            <thead style="background-color: #00adefad;">
                <tr style="font-weight:bold; text-align:center;">
                    <td width="20%" rowspan="2">ASIGNATURAS</td>
                    <td width="3%" rowspan="2">I.H</td>
                    <?php
                        if($periodoActual!=1){
                    ?>
                    <td width="3%" colspan="<?=$periodosCursados?>"><a href="#" style="color:#000; text-decoration:none;">Periodo Cursados</a></td>
                    <?php
                        }
                    ?>
                    <td width="3%" colspan="2">Periodo Actual (<?=strtoupper($periodoActuales)?>)</td>
                    <td width="3%" colspan="3">TOTAL ACUMULADO</td>
                </tr>
                <tr style="font-weight:bold; text-align:center;">
                    <?php
                        for($i=1;$i<=$periodoActual;$i++){
                            if($i!=$periodoActual){
                    ?>
                        <td width="3%"><?=$i?></td>
                    <?php
                        }else{
                    ?>
                    <td width="3%">Nota</td>
                    <td width="3%">Desempeño</td>
                    <?php
                            }
                        }
                    ?>
                    <td width="3%">Fallas</td>
                    <td width="3%">Nota</td>
                    <td width="3%">Desempeño</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $consultaAreas = Asignaturas::consultarAsignaturasCurso($conexion, $config, $gradoActual, $grupoActual, $year);
                    $numAreas=mysqli_num_rows($consultaAreas);
                    $sumaPromedioGeneral=0;
                    $sumaPromedioGeneralPeriodo1=0;
                    $sumaPromedioGeneralPeriodo2=0;
                    $sumaPromedioGeneralPeriodo3=0;
                    while($datosAreas = mysqli_fetch_array($consultaAreas, MYSQLI_BOTH)){

                        $consultaMaterias = CargaAcademica::consultaMaterias($config, $periodoActual, $matriculadosDatos['mat_id'], $datosAreas['car_curso'], $datosAreas['car_grupo'], $datosAreas['ar_id'], $year);
                        $notaArea=0;
                        $notaAreasPeriodos=0;
                        while($datosMaterias = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH)){
                            //DIRECTOR DE GRUPO
                            if($datosMaterias["car_director_grupo"]==1){
                                $idDirector=$datosMaterias["car_docente"];
                            }

                            //NOTA PARA LAS MATERIAS
                            $notaMateria = !empty($datosMaterias['bol_nota']) ? round($datosMaterias['bol_nota'], $config['conf_decimales_notas']) : 0;
                            $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaMateria,$year);
                            if($notaMateria<10){
                                $estiloNota['notip_nombre']="Bajo";
                            }
                            if($notaMateria>50){
                                $estiloNota['notip_nombre']="Superior";
                            }

                            //AUSENCIAS EN ESTA MATERIA
                            $consultaDatosAusencias = Boletin::obtenerDatosAusencias($gradoActual, $datosMaterias['car_materia'], $periodoActual, $matriculadosDatos['mat_id'], $year);
                            $datosAusencias = mysqli_fetch_array($consultaDatosAusencias, MYSQLI_BOTH);
                            $ausencia="";

                            if ($datosAusencias[0]>0) {
                                $ausencia= round($datosAusencias[0],0);
                            }

                            //VARIABLES NECESARIAS
                            $background='';
                            $ih=$datosMaterias["car_ih"];
                            if($datosAreas['numMaterias']>1){
                ?>
                                <tr>
                                    <td><?=$datosMaterias['mat_nombre']?></td>
                                    <td align="center"><?=$datosMaterias['car_ih']?></td>
                                    <?php
                                        $notaMateriasPeriodosTotal=0;
                                        $ultimoPeriodo = $config["conf_periodos_maximos"];
                                        for($i=1;$i<=$periodoActual;$i++){
                                            if($i!=$periodoActual){
                                                $datosPeriodos = Boletin::traerNotaBoletinCargaPeriodo($config, $i, $matriculadosDatos['mat_id'], $datosMaterias['car_id'], $year);
                                                $notaMateriasPeriodos=$datosPeriodos['bol_nota'];
                                                $notaMateriasPeriodos=round($notaMateriasPeriodos, $config['conf_decimales_notas']);
                                                $notaMateriasPeriodosTotal+=$notaMateriasPeriodos;

                                                $notaMateriasPeriodosFinal=$notaMateriasPeriodos;
                                                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                                    $estiloNotaAreas = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaMateriasPeriodos,$year);
                                                    $notaMateriasPeriodosFinal= !empty($estiloNotaAreas['notip_nombre']) ? $estiloNotaAreas['notip_nombre'] : "";
                                                    if($notaMateriasPeriodos<10){
                                                        $notaMateriasPeriodosFinal="Bajo";
                                                    }
                                                    if($notaMateriasPeriodos>50){
                                                        $notaMateriasPeriodosFinal="Superior";
                                                    }
                                                }
                                                if (empty($datosPeriodos['bol_periodo'])){
                                                    $ultimoPeriodo -= 1;
                                                }
                                    ?>
                                    <td align="center" style="background: #9ed8ed"><?=$notaMateriasPeriodosFinal?></td>
                                    <?php
                                                }else{
                                                    $notaMateriaFinal = $notaMateria;
                                                    if (empty($datosMaterias['bol_periodo'])){
                                                        $notaMateriaFinal = "";
                                                        $estiloNota['notip_nombre'] = "";
                                                        $ultimoPeriodo  -= 1;
                                                    }
                                    ?>
                                    <td align="center"><?=$notaMateriaFinal?></td>
                                    <td align="center"><?=$estiloNota['notip_nombre']?></td>
                                    <?php
                                            }
                                        }//FIN FOR

                                        //ACOMULADO PARA LAS MATERIAS
                                        $notaAcomuladoMateria = ($notaMateria + $notaMateriasPeriodosTotal) / $ultimoPeriodo;
                                        $notaAcomuladoMateria = round($notaAcomuladoMateria,$config['conf_decimales_notas']);
                                        if(strlen($notaAcomuladoMateria) === 1 || $notaAcomuladoMateria == 10){
                                            $notaAcomuladoMateria = $notaAcomuladoMateria.".0";
                                        }
                                        $estiloNotaAcomuladoMaterias = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoMateria,$year);
                                        if($notaAcomuladoMateria<10){
                                            $estiloNotaAcomuladoMaterias['notip_nombre']="Bajo";
                                        }
                                        if($notaAcomuladoMateria>50){
                                            $estiloNotaAcomuladoMaterias['notip_nombre']="Superior";
                                        }
                                    ?>
                                    <td align="center"><?=$ausencia?></td>
                                    <td align="center"><?=$notaAcomuladoMateria?></td>
                                    <td align="center"><?=$estiloNotaAcomuladoMaterias['notip_nombre']?></td>
                                </tr>
                    <?php
                            $ih="";
                            $ausencia="";
                            $background='style="background: #EAEAEA"';
                            }

                            //NOTA PARA LAS AREAS
                            if(!empty($datosMaterias['notaArea'])) $notaArea+=round($datosMaterias['notaArea'], $config['conf_decimales_notas']);

                        } //FIN WHILE DE LAS MATERIAS
                    ?>
                    <!--********SE IMPRIME LO REFERENTE A LAS AREAS*******-->
                        <tr>
                            <td <?=$background?>><?=$datosAreas['ar_nombre']?></td>
                            <td align="center"><?=$ih?></td>
                            <?php
                                $notaAreasPeriodosTotal=0;
                                $promGeneralPer1=0;
                                $promGeneralPer2=0;
                                $promGeneralPer3=0;
                                $ultimoPeriodoAreas = $config["conf_periodos_maximos"];
                                for($i=1;$i<=$periodoActual;$i++){
                                    if($i!=$periodoActual){
                                        $consultaAreasPeriodos = CargaAcademica::consultaAreasPeriodos($config, $i, $matriculadosDatos['mat_id'], $datosAreas['ar_id'], $year, $matriculadosDatos['mat_grupo']);
                                        $datosAreasPeriodos=mysqli_fetch_array($consultaAreasPeriodos, MYSQLI_BOTH);
                                        $notaAreasPeriodos = !empty($datosAreasPeriodos['notaArea']) ? round($datosAreasPeriodos['notaArea'], $config['conf_decimales_notas']) : 0;
                                        $notaAreasPeriodosTotal+=$notaAreasPeriodos;
                                        switch($i){
                                            case 1:
                                                $promGeneralPer1+=$notaAreasPeriodos;
                                                break;
                                            case 2:
                                                $promGeneralPer2+=$notaAreasPeriodos;
                                                break;
                                            case 3:
                                                $promGeneralPer3+=$notaAreasPeriodos;
                                                break;
                                        }

                                        if (empty($datosAreasPeriodos['bol_periodo'])){
                                            $ultimoPeriodoAreas -= 1;
                                        }

                                        $notaAreasPeriodosFinal=$notaAreasPeriodos;
                                        if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                            $estiloNotaAreas = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAreasPeriodos,$year);
                                            $notaAreasPeriodosFinal= !empty($estiloNotaAreas['notip_nombre']) ? $estiloNotaAreas['notip_nombre'] : "";
                                            if($notaAreasPeriodos<10){
                                                $notaAreasPeriodosFinal="Bajo";
                                            }
                                            if($notaAreasPeriodos>50){
                                                $notaAreasPeriodosFinal="Superior";
                                            }
                                        }
                            ?>
                            <td align="center" style="background: #9ed8ed"><?=$notaAreasPeriodosFinal?></td>
                            <?php
                                    }else{
                                        $estiloNotaAreas = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaArea,$year);
                                        if($notaArea<10){
                                            $estiloNotaAreas['notip_nombre']="Bajo";
                                        }
                                        if($notaArea>50){
                                            $estiloNotaAreas['notip_nombre']="Superior";
                                        }

                                        $notaAreaFinal = $notaArea;
                                        if (empty($notaArea) || $notaArea == 0){
                                            $notaAreaFinal = "";
                                            $estiloNotaAreas['notip_nombre'] = "";
                                            $ultimoPeriodoAreas -= 1;
                                        }
                            ?>
                            <td align="center"><?=$notaAreaFinal?></td>
                            <td align="center"><?=$estiloNotaAreas['notip_nombre']?></td>
                            <?php
                                    }
                                }
                        
                                //ACOMULADO PARA LAS AREAS
                                $notaAcomuladoArea = ($notaArea + $notaAreasPeriodosTotal) / $ultimoPeriodoAreas;
                                $notaAcomuladoArea = round($notaAcomuladoArea,$config['conf_decimales_notas']);
                                if(strlen($notaAcomuladoArea) === 1 || $notaAcomuladoArea == 10){
                                    $notaAcomuladoArea = $notaAcomuladoArea.".0";
                                }
                                $estiloNotaAcomuladoAreas = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoArea,$year);
                                if($notaAcomuladoArea<10){
                                    $estiloNotaAcomuladoAreas['notip_nombre']="Bajo";
                                }
                                if($notaAcomuladoArea>50){
                                    $estiloNotaAcomuladoAreas['notip_nombre']="Superior";
                                }
                            ?>
                            <td align="center"><?=$ausencia?></td>
                            <td align="center"><?=$notaAcomuladoArea?></td>
                            <td align="center"><?=$estiloNotaAcomuladoAreas['notip_nombre']?></td>
                        </tr>
                    <?php

                            //SUMA NOTAS DE LAS AREAS
                            $sumaPromedioGeneral+=$notaArea;

                            //SUMA NOTAS DE LAS AREAS PERIODOS ANTERIORES
                            $sumaPromedioGeneralPeriodo1+=$promGeneralPer1;
                            $sumaPromedioGeneralPeriodo2+=$promGeneralPer2;
                            $sumaPromedioGeneralPeriodo3+=$promGeneralPer3;
                            
                        } //FIN WHILE DE LAS AREAS

                        //PROMEDIO DE LAS AREAS
                        $promedioGeneral += !empty($sumaPromedioGeneral) && !empty($numAreas) ? ($sumaPromedioGeneral/$numAreas) : 0;
                        $promedioGeneral= round($promedioGeneral,$config['conf_decimales_notas']);
                        $estiloNotaPromedioGeneral = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioGeneral,$year);
                        if($promedioGeneral<10){
                            $estiloNotaPromedioGeneral['notip_nombre']="Bajo";
                        }
                        
                    ?>
            </tbody>
            <tfoot style="font-weight:bold; font-size: 13px;">
                <tr style="background: #EAEAEA">
                    <td colspan="2">PROMEDIO GENERAL</td>
                    <?php
                    for ($j = 1; $j <= $periodoActual; $j++) {
                        if($j!=$periodoActual){
                            switch($j){
                                case 1:
                                    $sumaPromedioGeneralPeriodos=$sumaPromedioGeneralPeriodo1;
                                    break;
                                case 2:
                                    $sumaPromedioGeneralPeriodos=$sumaPromedioGeneralPeriodo2;
                                    break;
                                case 3:
                                    $sumaPromedioGeneralPeriodos=$sumaPromedioGeneralPeriodo3;
                                    break;
                            }

                            //PROMEDIO DE LAS AREAS PERIODOS ANTERIORES
                            $promedioGeneralPeriodos = !empty($sumaPromedioGeneralPeriodos) && !empty($numAreas) ? ($sumaPromedioGeneralPeriodos/$numAreas) : 0;
                            $promedioGeneralPeriodos= round($promedioGeneralPeriodos,$config['conf_decimales_notas']);

                            $promedioGeneralPeriodosFinal=$promedioGeneralPeriodos;
                            if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                $estiloNotaAreas = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioGeneralPeriodos,$year);
                                $promedioGeneralPeriodosFinal= !empty($estiloNotaAreas['notip_nombre']) ? $estiloNotaAreas['notip_nombre'] : "";
                                if($promedioGeneralPeriodos<10){
                                    $promedioGeneralPeriodosFinal="Bajo";
                                }
                                if($promedioGeneralPeriodos>50){
                                    $promedioGeneralPeriodosFinal="Superior";
                                }
                            }
                    ?>
                    <td align="center"><?=$promedioGeneralPeriodosFinal;?></td>
                    <?php
                        }else{
                    ?>
                    <td align="center"><?=$promedioGeneral;?></td>
                    <td align="center"><?=$estiloNotaPromedioGeneral['notip_nombre']?></td>
                    <?php
                        }
                    }// FIN FOR
                    ?>
                    <td align="center"></td>
                    <td align="center"></td>
                    <td align="center"></td>
                </tr>
            </tfoot>
        </table>

        <p>&nbsp;</p>
        <!--******PUESTO DEL ESTUDIANTE******-->
        <table style="font-size: 15px;" width="80%" cellspacing="5" cellpadding="5" rules="all" border="1" align="right">
            <tr style="background-color: #EAEAEA;">
                <?php
                    if(empty($_REQUEST["curso"])){
                        $filtro = " AND mat_grado='" . $gradoActual . "' AND mat_grupo='".$grupoActual."'";
                        $matriculadosDelCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
                        $numeroEstudiantes = mysqli_num_rows($matriculadosDelCurso);
                    }
                    //Buscamos Puesto del estudiante en el curso
                    $puestoEstudiantesCurso = 0;
                    $puestosCursos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual, $gradoActual, $grupoActual,$year);
                    
                    while($puestoCurso = mysqli_fetch_array($puestosCursos, MYSQLI_BOTH)){
                        if($puestoCurso['bol_estudiante']==$matriculadosDatos['mat_id']){
                            $puestoEstudiantesCurso = $puestoCurso['puesto'];
                        }
                    }
                    
                    //Buscamos Puesto del estudiante en la institución
                    $matriculadosDeLaInstitucion = Estudiantes::estudiantesMatriculados("", $year);
                    $numeroEstudiantesInstitucion = mysqli_num_rows($matriculadosDeLaInstitucion);

                    $puestoEstudiantesInstitucion = 0;
                    $puestosInstitucion = Boletin::obtenerPuestoEstudianteEnInstitucion($periodoActual, $year);
                    
                    while($puestoInstitucion = mysqli_fetch_array($puestosInstitucion, MYSQLI_BOTH)){
                        if($puestoInstitucion['bol_estudiante']==$matriculadosDatos['mat_id']){
                            $puestoEstudiantesInstitucion = $puestoInstitucion['puesto'];
                        }
                    }
                ?>
                <td align="center" width="40%">Puesto en el curso <b><?=$puestoEstudiantesCurso?></b> entre <b><?=$numeroEstudiantes?></b> Estudiantes.</td>
                <td align="center" width="40%">Puesto en el colegio <b><?=$puestoEstudiantesInstitucion?></b> entre <b><?=$numeroEstudiantesInstitucion?></b> Estudiantes.</td>
            </tr>
        </table>

        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <!--******OBSERVACIONES******-->

        <table style="font-size: 15px;" width="100%" cellspacing="5" cellpadding="5" rules="all" border="1" align="center">
            <thead>
                <tr style="font-weight:bold; text-align:left; background-color: #00adefad;">
                    <td><b>Observaciones:</b></td>
                </tr>
            </thead>
            <tbody>
                <tr style="color:#000;">
                    <td style="padding-left: 20px;">
                        <?php 
                            $cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$matriculadosDatos['mat_id']."' AND dn_periodo='".$periodoActual."' AND institucion={$config['conf_id_institucion']} AND year={$year}");
                            while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){

                                if(!empty($rndisiplina['dn_observacion'])){
                                    if($config['conf_observaciones_multiples_comportamiento'] == '1'){
                                        $explode=explode(",",$rndisiplina['dn_observacion']);
                                        $numDatos=count($explode);
                                        for($i=0;$i<$numDatos;$i++){
                                            $observaciones = Disciplina::traerDatosObservacion($config, $explode[$i], "obser_descripcion");
                                            echo "- " . $observaciones['obser_descripcion'] . "<br> ";
                                        }
                                    }else{
                                        echo "- ".$rndisiplina["dn_observacion"]."<br>";
                                    }
                                }
                            }
                            if ($periodoActual == $config["conf_periodos_maximos"] && $ultimoPeriodoAreas < $config["conf_periodos_maximos"]) {
                                Echo "ESTUDIANTE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
                            }
                        ?>
                        <p>&nbsp;</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div id="saltoPagina"></div>
        <!--******SEGUNDA PAGINA******-->
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <!--******INDICADORES POR ASIGNATURA******-->

        <table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1" align="center">
            <thead>
                <tr style="font-weight:bold; text-align:center; background-color: #00adefad;">
                    <td width="30%">Asignatura</td>
                    <td width="70%">Indicadores de desempeño</td>
                </tr>
            </thead>

            <?php
            $conCargasDos = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $gradoActual, $grupoActual, $year);
            while ($datosCargasDos = mysqli_fetch_array($conCargasDos, MYSQLI_BOTH)) {

                
            ?>
                <tbody>
                    <tr style="color:#000;">
                        <td><?= $datosCargasDos['mat_nombre']; ?><br><span style="color:#C1C1C1;"><?= UsuariosPadre::nombreCompletoDelUsuario($datosCargasDos); ?></span></td>
                        <td>
                        
                            <?php
                            //INDICADORES
		                    $indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $datosCargasDos['car_id'], $periodoActual, $year);
                            while ($indicador = mysqli_fetch_array($indicadores, MYSQLI_BOTH)) {
                            ?>
                   
                        <?= $indicador['ind_nombre']; ?><br>
                    
                <?php
                            }
                ?>
                    </td>
                </tr>
                </tbody>
            <?php
            }
            ?>
        </table>

        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>   
        <!--******FIRMAS******-->   

        <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
            <tr>
                <td align="center">
                    <?php
                        $directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
                        $nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
                        if(!empty($directorGrupo["uss_firma"])){
                            echo '<img src="../files/fotos/'.$directorGrupo["uss_firma"].'" width="100"><br>';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>';
                        }
                    ?>
                    <p style="height:0px;"></p>_________________________________<br>
                    <p>&nbsp;</p>
                    <?=$nombreDirectorGrupo?><br>
                    Director(a) de grupo
                </td>
                <td align="center">
                    <?php
                        $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
                        $nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
                        if(!empty($rector["uss_firma"])){
                            echo '<img src="../files/fotos/'.$rector["uss_firma"].'" width="100"><br>';
                        }else{
                            echo '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>';
                        }
                    ?>
                    <p style="height:0px;"></p>_________________________________<br>
                    <p>&nbsp;</p>
                    <?=$nombreRector?><br>
                    Rector(a)
                </td>
            </tr>
        </table>

        <?php
            $contadorEstudiantes++;
            if($contadorEstudiantes!=$numeroEstudiantes && empty($_GET['id'])){
        ?>

        <div id="saltoPagina"></div>
<?php
            }
    }//FIN WHILE MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>

        <script type="application/javascript">
            print();
        </script>
    </body>
</html>