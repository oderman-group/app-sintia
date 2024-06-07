<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0227';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
    require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
    require_once(ROOT_PATH."/main-app/class/Boletin.php");
    require_once(ROOT_PATH."/main-app/class/Usuarios.php");
    require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
	require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
    require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
    require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
    $Plataforma = new Plataforma;

    $year=$_SESSION["bd"];
	if(isset($_POST["year"])){
		$year=$_POST["year"];
	}
    if(isset($_GET["year"])){
		$year=base64_decode($_GET["year"]);
    }

	$periodoActual = 4;
	if(isset($_POST["periodo"])){
		$periodoActual=$_POST["periodo"];
	}
    if(isset($_GET["periodo"])){
		$periodoActual=base64_decode($_GET["periodo"]);
    }

	$curso='';
	if(isset($_POST["curso"])){
		$curso=$_POST["curso"];
	}
	if(isset($_GET["curso"])){
		$curso=base64_decode($_GET["curso"]);
	}

	$id='';
	if(isset($_POST["id"])){
		$id=$_POST["id"];
	}
	if(isset($_GET["id"])){
		$id=base64_decode($_GET["id"]);
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
	if(!empty($_REQUEST["curso"])){$filtro .= " AND mat_grado='".$curso."'";}

	if(!empty($_REQUEST["id"])){$filtro .= " AND mat_id='".$id."'";}
	
	$grupo="";
	if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".$_REQUEST["grupo"]."'"; $grupo=$_REQUEST["grupo"];}

    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    $numeroEstudiantes = mysqli_num_rows($matriculadosPorCurso);

    if ($numeroEstudiantes == 0) {

        $url= UsuariosPadre::verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'page-info.php?idmsg=306');
        echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
        exit();

    }

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
		$materiasPerdidas=0;
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
        <title>Libro Final</title>
        <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
        <!-- favicon -->
        <link rel="shortcut icon" href="<?=$Plataforma->logo;?>" />
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }

			.divBordeado {
				height: 3px;
				border: 3px solid #9ed8ed;
				background-color: #00ACFB;
			}
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    </head>
    <body style="font-family:Arial; font-size:9px;">
        <div style="margin: 15px 0;">
            <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" style="font-size: 13px;">
                <tr>
                    <td rowspan="3" width="20%"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="100%"></td>
                    <td align="center" rowspan="3" width="25%">
                        <h3 style="font-weight:bold; color: #00adefad; margin: 0"><?=strtoupper($informacion_inst["info_nombre"])?></h3><br>
                        <?=$informacion_inst["info_direccion"]?><br>
                        Informes: <?=$informacion_inst["info_telefono"]?>
                    </td>
                    <td>Código:<br> <b style="color: #00adefad;"><?=number_format($matriculadosDatos["mat_id"],0,",",".");?></b></td>
                    <td>Nombre:<br> <b style="color: #00adefad;"><?=$nombreEstudainte?></b></td>
                </tr>
                <tr>
                    <td>Curso:<br> <b style="color: #00adefad;"><?=strtoupper($matriculadosDatos["gra_nombre"])?></b></td>
                    <td>Sede:<br> <b style="color: #00adefad;"><?=strtoupper($informacion_inst["info_nombre"])?></b></td>
                </tr>
                <tr>
                    <td>Jornada:<br> <b style="color: #00adefad;"><?=strtoupper($informacion_inst["info_jornada"])?></b></td>
                    <td>Documento:<br> <b style="color: #00adefad;">BOLETÍN DEFINITIVO DE NOTAS - EDUCACIÓN BÁSICA <?=strtoupper($educacion)?></b></td>
                </tr>
            </table>
            <p>&nbsp;</p>
        </div>
        <table width="100%">
            <tr><td><div class="divBordeado">&nbsp;</div></td></tr>
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
            <tr><td>&nbsp;</td></tr>
            <tr style="text-align:center; font-size: 20px; font-weight:bold;">
                <td>AÑO LECTIVO: <?=$year?></td>
            </tr>
        </table>
        <table width="100%" rules="all" border="1" style="font-size: 15px;">
            <thead>
                <tr style="font-weight:bold; text-align:center;">
                    <td width="20%" rowspan="2">ASIGNATURAS</td>
                    <td width="3%" rowspan="2">I.H</td>
                    <td width="3%" colspan="4" style="background-color: #00adefad;"><a href="#" style="color:#000; text-decoration:none;">Periodo Cursados</a></td>
                    <td width="3%" colspan="2"><a href="#" style="color:#000; text-decoration:none;">DEFINITIVA</a></td>
                </tr>
                <tr style="font-weight:bold; text-align:center;">
                    <?php
                        for($i=1;$i<=$periodoActual;$i++){
                    ?>
                        <td width="3%" style="background-color: #00adefad;"><?=$i?></td>
                    <?php
                        }
                    ?>
                    <td width="3%">DEF</td>
                    <td width="3%">Desempeño</td>
                </tr>
            </thead>
            <?php
                $notasBoletin = Boletin::traerNotaBoletinEstudiante($config, $matriculadosDatos['mat_id'], $year);

                if (!empty($notasBoletin)) {
            ?>
            <tbody>
                <?php
					$consultaAreas = Asignaturas::consultarAsignaturasCurso($conexion, $config, $gradoActual, $grupoActual, $year);
                    
                    $numAreas=mysqli_num_rows($consultaAreas);
                    $sumaPromedioGeneral=0;
                    $sumaPromedioGeneralPeriodo1=0;
                    $sumaPromedioGeneralPeriodo2=0;
                    $sumaPromedioGeneralPeriodo3=0;
                    $sumaPromedioGeneralPeriodo4=0;
                    while($datosAreas = mysqli_fetch_array($consultaAreas, MYSQLI_BOTH)){

                        $consultaMaterias = CargaAcademica::consultaMaterias($config, $periodoActual, $matriculadosDatos['mat_id'], $datosAreas['car_curso'], $datosAreas['car_grupo'], $datosAreas['ar_id'], $year);
                        $notaArea=0;
                        $notaAreasPeriodos=0;
                        while($datosMaterias = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH)){
                            //DIRECTOR DE GRUPO
                            if($datosMaterias["car_director_grupo"]==1){
                                $idDirector=$datosMaterias["car_docente"];
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
                                                $datosPeriodos = Boletin::traerNotaBoletinCargaPeriodo($config, $i, $matriculadosDatos['mat_id'], $datosMaterias["car_id"], $year);
                                                $notaMateriasPeriodos=$datosPeriodos['bol_nota'];
                                                $notaMateriasPeriodos=round($notaMateriasPeriodos, 1);
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
                                        }//FIN FOR

                                        //ACOMULADO PARA LAS MATERIAS
                                        $notaAcomuladoMateria = $notaMateriasPeriodosTotal / $ultimoPeriodo;
                                        $notaAcomuladoMateria = round($notaAcomuladoMateria,1);
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
                                    <td align="center"><?=$notaAcomuladoMateria?></td>
                                    <td align="center"><?=$estiloNotaAcomuladoMaterias['notip_nombre']?></td>
                                </tr>
                    <?php
                            $ih="";
                            $ausencia="";
                            $background='style="background: #EAEAEA"';
                            }

                            //NOTA PARA LAS AREAS
                            if(!empty($datosMaterias['notaArea'])) $notaArea+=round($datosMaterias['notaArea'], 1);

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
                                $promGeneralPer4=0;
                                $ultimoPeriodoAreas = $config["conf_periodos_maximos"];
                                for($i=1;$i<=$periodoActual;$i++){
                                        $consultaAreasPeriodos = CargaAcademica::consultaAreasPeriodos($config, $i, $matriculadosDatos['mat_id'], $$datosAreas['ar_id'], $year);
                                        $datosAreasPeriodos=mysqli_fetch_array($consultaAreasPeriodos, MYSQLI_BOTH);
                                        $notaAreasPeriodos = !empty($datosAreasPeriodos['notaArea']) ? round($datosAreasPeriodos['notaArea'], 1) : 0;
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
											case 4:
												$promGeneralPer4+=$notaAreasPeriodos;
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
                                }
                        
                                //ACOMULADO PARA LAS AREAS
                                $notaAcomuladoArea = $notaAreasPeriodosTotal / $ultimoPeriodoAreas;
                                $notaAcomuladoArea = round($notaAcomuladoArea,1);
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

								if($notaAcomuladoArea < $config['conf_nota_minima_aprobar']){
									$materiasPerdidas++;
								}
                            ?>
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
                            $sumaPromedioGeneralPeriodo4+=$promGeneralPer4;
                            
                        } //FIN WHILE DE LAS AREAS

                        //PROMEDIO DE LAS AREAS
                        $promedioGeneral+=($sumaPromedioGeneral/$numAreas);
                        $promedioGeneral= round($promedioGeneral,1);
                        $estiloNotaPromedioGeneral = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioGeneral,$year);
                        if($promedioGeneral<10){
                            $estiloNotaPromedioGeneral['notip_nombre']="Bajo";
                        }
                        
                    ?>
            </tbody>
            <tfoot style="font-size: 13px;">
                <tr style="font-weight:bold; background: #EAEAEA">
                    <td colspan="2">PROMEDIO GENERAL</td>
                    <?php
					$promedioGeneralPeriodosTotal = 0;
                    for ($j = 1; $j <= $periodoActual; $j++) {
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
								case 4:
									$sumaPromedioGeneralPeriodos=$sumaPromedioGeneralPeriodo4;
									break;
                            }

                            //PROMEDIO DE LAS AREAS PERIODOS ANTERIORES
                            $promedioGeneralPeriodos=($sumaPromedioGeneralPeriodos/$numAreas);
                            $promedioGeneralPeriodos= round($promedioGeneralPeriodos,1);
							
							$promedioGeneralPeriodosTotal+=$promedioGeneralPeriodos;

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
						}// FIN FOR
                        
						//ACOMULADO GENERAL
						$notaAcomuladoTotal = $promedioGeneralPeriodosTotal / $ultimoPeriodoAreas;
						$notaAcomuladoTotal = round($notaAcomuladoTotal,1);
						if(strlen($notaAcomuladoTotal) === 1 || $notaAcomuladoTotal == 10){
							$notaAcomuladoTotal = $notaAcomuladoTotal.".0";
						}
						$estiloNotaAcomuladoTotal = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoTotal,$year);
						if($notaAcomuladoTotal<10){
							$estiloNotaAcomuladoTotal['notip_nombre']="Bajo";
						}
						if($notaAcomuladoTotal>50){
							$estiloNotaAcomuladoTotal['notip_nombre']="Superior";
						}

						$notaAcomuladoTotalFinal=$notaAcomuladoTotal;
						if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
							$estiloNotaAreas = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaAcomuladoTotal,$year);
							$notaAcomuladoTotalFinal= !empty($estiloNotaAreas['notip_nombre']) ? $estiloNotaAreas['notip_nombre'] : "";
							if($notaAcomuladoTotal<10){
								$notaAcomuladoTotalFinal="Bajo";
							}
							if($notaAcomuladoTotal>50){
								$notaAcomuladoTotalFinal="Superior";
							}
						}
                    ?>
                    <td align="center"><?=$notaAcomuladoTotalFinal?></td>
                    <td align="center"><?=$estiloNotaAcomuladoTotal['notip_nombre']?></td>
                </tr>
				<tr style="color:#000;">
					<td style="padding-left: 10px;" colspan="8">
						<h4 style="font-weight:bold; color: #00adefad;"><b>Observación definitiva:</b></h4>
						<?php
							if($periodoActual == $config["conf_periodos_maximos"]){

								if ($materiasPerdidas >= $config["conf_num_materias_perder_agno"]) {
									$msj = "EL(LA) ESTUDIANTE NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE.";
								} elseif ($materiasPerdidas < $config["conf_num_materias_perder_agno"] && $materiasPerdidas > 0) {
									$msj = "EL(LA) ESTUDIANTE DEBE NIVELAR LAS MATERIAS PERDIDAS.";
								} else {
									$msj = "EL(LA) ESTUDIANTE FUE PROMOVIDO(A) AL GRADO SIGUIENTE.";
								}

								if ($matriculadosDatos['mat_estado_matricula'] == CANCELADO && $ultimoPeriodoAreas < $config["conf_periodos_maximos"]) {
									$msj = "EL(LA) ESTUDIANTE FUE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
								}
							}
							echo "<span style='padding-left: 10px;'>".$msj."</span>";
						?>
						<p>&nbsp;</p>
					</td>
				</tr>
            </tfoot>
            <?php } ?>
        </table>

        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>   
        <!--******FIRMAS******-->   

        <table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
            <tr>
                <td align="left">
                    <?php
                        $rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
                        $nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
                        if(!empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma'])){
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