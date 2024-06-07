<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0080';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php //include("verificar-periodos-diferentes.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
$disabled = '';
if( !CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) { 
    $disabled = 'disabled';
}
?>
</head>

<div class="card card-topline-purple">
    <div class="card-head">
        <header>Observaciones</header>
        <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
        </div>
    </div>
    
    

    
    <div class="card-body">
        

    <span style="color: blue; font-size: 15px;" id="respOBS"></span>
        
        
    <div class="table-responsive">
        <table class="table table-striped custom-table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
                    <th>DEF</th>
                    <th><?=$frases[109][$datosUsuarioActual['uss_idioma']];?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
                    $contReg = 1;
                    $colorNota = "black";
                    while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                    $notas = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoConsultaActual, $resultado['mat_id'], $cargaConsultaActual);
                    $definitiva = isset($notas['bol_nota']) ? $notas['bol_nota'] : null;
                    if($definitiva<$config[5] and $definitiva!="") $colorNota = $config[6]; elseif($definitiva>=$config[5]) $colorNota = $config[7]; else {$colorNota = 'black'; $definitiva='';} 

                    $definitivaFinal=$definitiva;
                    $atributosA='style="text-decoration:underline; color:'.$colorNota.';"';
                    if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
                        $atributosA='tabindex="0" role="button" data-toggle="popover" data-trigger="hover" title="Nota Cuantitativa: '.$definitiva.'" data-content="<b>Nota Cuantitativa:</b><br>'.$definitiva.'" data-html="true" data-placement="top" style="border-bottom: 1px dotted #000; color:'.$colorNota.';"';

                        $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
                        $definitivaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                    }
                    
                    ?>
                
                <tr>
                    <td><?=$contReg;?></td>
                    <td><?=$resultado['mat_id'];?></td>
                    <td width="60%">
                        <img src="../files/fotos/<?=$resultado['uss_foto'];?>" width="50">
                        <?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>
                    </td>
                    
                    <td><a href="calificaciones-estudiante.php?usrEstud=<?=base64_encode($resultado['mat_id_usuario']);?>&periodo=<?=base64_encode($periodoConsultaActual);?>&carga=<?=base64_encode($cargaConsultaActual);?>" <?=$atributosA;?>><?=$definitivaFinal;?></a><br><span style="font-size: 10px;"><?php if(isset($notas)) echo $notas['bol_observaciones'];?></span></td>
                    
                    <td width="25%">
                        
                        <textarea rows="7" cols="80" 
                        id="<?=$resultado['mat_id'];?>" 
                        name="<?=$cargaConsultaActual;?>" title="<?=$periodoConsultaActual;?>" 
                        onChange="observacionesBoletin(this)" 
                        <?=$disabled;?>
                        ><?php if(isset($notas)) echo $notas['bol_observaciones_boletin'];?></textarea>
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