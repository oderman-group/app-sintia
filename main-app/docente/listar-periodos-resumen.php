<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0039';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");?>

</head>

<div class="row">
                                
    <div class="col-md-2">
        
        <div class="panel">
            <header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
            <div class="panel-body">
                <p><b><?=$frases[117][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[120][$datosUsuarioActual['uss_idioma']];?></p>
                
                <p><b><?=$frases[118][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$frases[121][$datosUsuarioActual['uss_idioma']];?></p>
            </div>
        </div>

        
    </div>
    
    <div class="col-md-10">

        <div class="card card-topline-purple">
            <div class="card-head">
                <header><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                
                <span id="respRP"></span>
                
                <table class="table table-striped custom-table table-hover">
                    <thead>
                        <tr>
                            <th style="text-align:center;">#</th>
                            <th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>

                            <?php
                                $p = 1;
                                while($p<=$datosCargaActual['gra_periodos']){
                                    $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $p);
                                    
                                    $porcentajeGrado=25;
                                    if(!empty($periodosCursos['gvp_valor'])){
                                        $porcentajeGrado=$periodosCursos['gvp_valor'];
                                    }
                                    echo '<th style="text-align:center;">'.$p.'P<br>('.$porcentajeGrado.'%)</th>';
                                    $p++;
                                }
                            ?> 
                            <th style="text-align:center;"><?=$frases[117][$datosUsuarioActual['uss_idioma']];?></th>
                            <th style="text-align:center;"><?=$frases[118][$datosUsuarioActual['uss_idioma']];?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $contReg = 1; 
                        $consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
                        while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                            $colorEstudiante = '#000;';
                            if($resultado['mat_inclusion']==1){$colorEstudiante = 'blue;';}
                        ?>
                        
                        <tr>
                            <td style="text-align:center;"><?=$contReg;?></td>
                            <td style="color: <?=$colorEstudiante;?>"><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>

                            <?php
                                $definitiva = 0;
                                $sumatoria = 0;
                                $decimal = 0;
                                $sumaPorcentaje = 0;
                                $n = 0;
                                for($i=1; $i<=$datosCargaActual['gra_periodos']; $i++){
                                    $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $i);
                                    
                                    $porcentajeGrado=25;
                                    if(!empty($periodosCursos['gvp_valor'])){
                                        $porcentajeGrado=$periodosCursos['gvp_valor'];
                                    }
                                    $decimal = $porcentajeGrado/100;
                                    
                                //LAS CALIFICACIONES
                                $notasResultado = Boletin::traerNotaBoletinCargaPeriodo($config, $i, $resultado['mat_id'], $cargaConsultaActual);
                                if(!empty($notasResultado)){
                                    $n++;
                                    $definitiva += $notasResultado['bol_nota']*$decimal;
                                    $sumaPorcentaje += $decimal;
                                }
                                if(isset($notasResultado) && $notasResultado['bol_nota']<$config[5] and $notasResultado['bol_nota']!="")$color = $config[6]; elseif(isset($notasResultado) && $notasResultado['bol_nota']>=$config[5]) $color = $config[7];

                                $notasResultadoFinal="";
                                $notasAnteriorFinal="";
                                $atributosA='';
                                if(!empty($notasResultado)){
                                    $notasResultadoFinal=$notasResultado['bol_nota'];
                                    $notasAnteriorFinal=$notasResultado['bol_nota_anterior'];
                                    $atributosA='style="text-decoration:underline; color:'.$color.';"';
                                    if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                        $atributosA='tabindex="0" role="button" data-toggle="popover" data-trigger="hover" title="Nota Cuantitativa: '.$notasResultado['bol_nota'].'" data-content="<b>Nota Cuantitativa:</b><br>'.$notasResultado['bol_nota'].'" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000; color:'.$color.';"';
                
                                        $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasResultado['bol_nota']);
                                        $notasResultadoFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                
                                        $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasResultado['bol_nota_anterior']);
                                        $notasAnteriorFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                    }
                                }
                                    
                                if(isset($notasResultado) && $notasResultado['bol_tipo']==2) {$tipo = '<span style="color:red; font-size:9px;">Rec. Periodo('.$notasAnteriorFinal.')</span>';}
                                elseif(isset($notasResultado) && $notasResultado['bol_tipo']==3) {$tipo = '<span style="color:red; font-size:9px;">Rec. Indicador('.$notasAnteriorFinal.')</span>';}
                                    elseif(isset($notasResultado) && $notasResultado['bol_tipo']==4) {$tipo = '<span style="color:red; font-size:9px;">Directiva('.$notasAnteriorFinal.')</span>';}
                                elseif(isset($notasResultado) && $notasResultado['bol_tipo']==1) {$tipo = '<span style="color:blue; font-size:9px;">'.$frases[122][$datosUsuarioActual['uss_idioma']].'</span>';} 
                                    else $tipo='';


                            ?>
                                <td style="text-align:center;">
                                    <a href="calificaciones-estudiante.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>&periodo=<?=base64_encode($i);?>&carga=<?=base64_encode($cargaConsultaActual);?>" <?=$atributosA;?>><?=$notasResultadoFinal?></a><br><?=$tipo;?><br>

                                    <?php if(!empty($notasResultado['bol_nota']) && $notasResultado['bol_nota']<$config[5]){?>
                                        <input size="5" name="<?=$i?>_<?=$cargaConsultaActual;?>" id="<?=$resultado['mat_id'];?>" value="" alt="<?=$notasResultado['bol_nota'];?>" onChange="def(this)" tabindex="2" style="text-align: center;"><br>
                                        <span style="font-size:9px; color:rgb(0,0,153);"><?php if(!empty($notasResultado['bol_observaciones'])) echo $notasResultado['bol_observaciones'];?></span>
                                    <?php }?>

                                </td>
                            <?php		
                                }
                                //CALCULAR NOTA MINIMA EN EL ULTIMO PERIODO PARA APROBAR LA MATERIA
                                    //PREGUNTAMOS SI ESTAMOS EN EL PERIODO PENULTIMO O ULTIMO
                                    if($config[2]==$datosCargaActual['gra_periodos']){
                                        $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $i);
                                        
                                        $porcentajeGrado=25;
                                        if(!empty($periodosCursos['gvp_valor'])){
                                            $porcentajeGrado=$periodosCursos['gvp_valor'];
                                        }
                                        $decimal2 = $porcentajeGrado/100;
                                        
                                        $notaMinima = $config[5] - $definitiva;
                                        @$notaMinima = round(($notaMinima / $decimal2), $config['conf_decimales_notas']);
                                        if($notaMinima<=0){
                                        $notaMinima = "-";
                                        $colorFaltante = "green";
                                        }else{
                                        if($notaMinima<=$config[4]) $colorFaltante = "blue"; else $colorFaltante = "red"; 
                                        }
                                    }else{
                                    $notaMinima = "-";
                                    $colorFaltante = "black";
                                }
                            
                                if($sumaPorcentaje > 0){
                                    $definitiva = ($definitiva / $sumaPorcentaje);
                                }
                                $consultaN = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $resultado['mat_id'], $cargaConsultaActual);
                                $numN = mysqli_num_rows($consultaN);
                                $rN = mysqli_fetch_array($consultaN, MYSQLI_BOTH);
                                if($numN==0){
                                    if($n>0)
                                        $definitiva = round(($definitiva), $config['conf_decimales_notas']);
                                        $tN = '<span style="color:blue; font-size:9px;">'.$frases[122][$datosUsuarioActual['uss_idioma']].'</span>';
                                }else{
                                    $definitiva = $rN['niv_definitiva'];
                                    $tN = '<span style="color:red; font-size:9px;">'.$frases[124][$datosUsuarioActual['uss_idioma']].'</span>';
                                }
                                if($definitiva<$config[5])$color = $config[6]; elseif($definitiva>=$config[5]) $color = $config[7];

                                $definitivaFinal=$definitiva;
                                $atributosA='style="text-decoration:underline; color:'.$color.';"';
                                if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                                    $atributosA='tabindex="0" role="button" data-toggle="popover" data-trigger="hover" title="Nota Cuantitativa: '.$definitiva.'" data-content="<b>Nota Cuantitativa:</b><br>'.$definitiva.'" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000; color:'.$color.';"';
            
                                    $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
                                    $definitivaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                                }
                                
                                
                            ?>

                            <td style="text-align:center; color:<?=$colorFaltante;?>; font-weight:bold;"><?=$notaMinima;?></td>

                            <td style="text-align:center; color:<?=$color;?>;">
                                <?=$definitivaFinal."<br>".$tN;?><br>
                                <?php
                                if($n==$datosCargaActual['gra_periodos'] and $definitiva<$config[5]) $e = ''; else $e = 'disabled';
                                ?>
                                <input size="5" name="<?=$i?>_<?=$cargaConsultaActual;?>" id="<?=$resultado['mat_id'];?>" value="" onChange="niv(this)" tabindex="2" <?=$e;?> style="font-size: 13px; text-align: center;">
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
    </div>
</div>

<?php include("../compartido/guardar-historial-acciones.php");?>