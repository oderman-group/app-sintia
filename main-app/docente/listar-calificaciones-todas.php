<?php
include("session.php");
$idPaginaInterna = 'DC0011';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

$valores = Actividades::consultarValores($config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajeRestante = 100 - $valores[0];
?>

<?php
	$deleteOculto = 'style="display:none;"';
    $habilitado = 'disabled';
	if( CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) {
		$deleteOculto = 'style="display:block;"';
        $habilitado = '';
	}
?>
</head>

<div class="card card-topline-purple" name="elementoGlobalBloquear">
    <div class="card-head">
        <header><?=$frases[243][$datosUsuarioActual['uss_idioma']];?></header>
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
        // ============================================
        // VALIDACIÓN DE INDICADORES OBLIGATORIOS
        // ============================================
        $indicadoresObligatorios = ($datosCargaActual['car_indicador_automatico'] != 1);
        $tieneIndicadores = false;
        $mensajeIndicadores = '';
        
        if ($indicadoresObligatorios) {
            $consultaIndicadores = Indicadores::traerIndicadoresCargaPeriodo($cargaConsultaActual, $periodoConsultaActual);
            $numIndicadores = mysqli_num_rows($consultaIndicadores);
            
            if ($numIndicadores > 0) {
                $tieneIndicadores = true;
            } else {
                $mensajeIndicadores = '
                <div class="alert alert-warning" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left: 4px solid #f39c12; border-radius: 5px; padding: 15px; margin-bottom: 15px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-exclamation-triangle" style="font-size: 24px; color: #f39c12;"></i>
                        <div style="flex: 1;">
                            <strong style="color: #856404; font-size: 16px;">⚠️ Indicadores Requeridos</strong>
                            <p style="margin: 5px 0 0 0; color: #856404;">
                                Debes registrar al menos un indicador antes de poder agregar actividades.
                                <a href="indicadores.php?carga='.base64_encode($cargaConsultaActual).'&periodo='.base64_encode($periodoConsultaActual).'" class="alert-link" style="font-weight: 600; text-decoration: underline;">
                                    Ir a Indicadores <i class="fa fa-arrow-right"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>';
            }
        }
        
        // Mostrar mensaje si no tiene indicadores
        if ($indicadoresObligatorios && !$tieneIndicadores) {
            echo $mensajeIndicadores;
        }
        // ============================================
        
        if( CargaAcademica::validarAccionAgregarCalificaciones($datosCargaActual, $valores, $periodoConsultaActual, $porcentajeRestante) ) {
        ?>
        
                <div class="btn-group">
                    <button 
                        type="button" 
                        class="btn deepPink-bgcolor <?= ($indicadoresObligatorios && !$tieneIndicadores) ? 'disabled' : ''; ?>" 
                        data-toggle="<?= ($indicadoresObligatorios && !$tieneIndicadores) ? '' : 'modal'; ?>" 
                        data-target="<?= ($indicadoresObligatorios && !$tieneIndicadores) ? '' : '#modalAgregarActividad'; ?>"
                        style="transition: all 0.3s ease; <?= ($indicadoresObligatorios && !$tieneIndicadores) ? 'opacity: 0.6; cursor: not-allowed;' : ''; ?>"
                        <?= ($indicadoresObligatorios && !$tieneIndicadores) ? 'disabled title="Debes registrar indicadores primero"' : ''; ?>
                    >
                        <i class="fa fa-plus-circle"></i> Agregar Actividad
                    </button>
                </div>
                
                
        <?php
        }
        ?>
                
        <?php if($datosCargaActual['car_configuracion']==1 and $porcentajeRestante<=0){?>
            <p style="color: tomato;"> Has alcanzado el 100% de valor para las calificaciones. </p>
        <?php }?>
                    
        <?php if($datosCargaActual['car_maximas_calificaciones']<=$valores[1]){?>
            <p style="color: tomato;"> Has alcanzado el número máximo de calificaciones permitidas. </p>
        <?php }?>
        
        <?php if( CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) {?>
                <div class="btn-group">
                    <a href="calificaciones-todas-rapido.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" class="btn bg-purple">
                        LLenar más rápido las calificaciones
                    </a>
                </div>
        <?php }?>
        
            </div>
        </div>
        
    <div class="table-responsive">
        
        <span id="respRCT"></span>
        <?php
        $arrayEnviar = [
            "tipo"            => 1, 
            "descripcionTipo" => "Para ocultar fila del registro."
        ];
        $arrayDatos = json_encode($arrayEnviar);
        $objetoEnviar = htmlentities($arrayDatos);
        ?>
        
        <table class="table table-striped custom-table table-hover" id="tabla_notas">
            <thead>
                <tr>
                <th style="text-align:center; width: 30px;">#</th>
                <th style="text-align:center; width: 30px;">ID</th>
                <th style="width: 400px;"><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
                <?php
                    $cA = Actividades::traerActividadesCarga($config, $cargaConsultaActual, $periodoConsultaActual);
                    while($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)){
                    echo '<th style="text-align:center; font-size:11px; width:100px;"><a href="calificaciones-editar.php?idR='.base64_encode($rA['act_id']).'" title="'.$rA['act_descripcion'].'">'.$rA['act_id'].'<br>
                    '.$rA['act_descripcion'].'<br>
                    ('.$rA['act_valor'].'%)</a><br>
                    <a href="#" 
                    name="calificaciones-eliminar.php?idR='.base64_encode($rA['act_id']).'&idIndicador='.base64_encode($rA['act_id_tipo']).'&carga='.base64_encode($cargaConsultaActual).'&periodo='.base64_encode($periodoConsultaActual).'" 
                    onClick="deseaEliminar(this)" '.$deleteOculto.'><i class="fa fa-times"></i></a><br>
                    <input 
                        type="text" 
                        style="text-align: center; font-weight: bold;"
                        size="10" 
                        title="1" 
                        name="'.$rA['act_id'].'" 
                        onChange="notasMasiva(this)" 
                        '.$habilitado.'
                    >
                    </th>';
                    }
                ?>
                <th style="text-align:center; width:60px;">%</th>
                <th style="text-align:center; width:60px;"><?=$frases[118][$datosUsuarioActual['uss_idioma']];?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $contReg = 1; 
                $consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
                while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                    //DEFINITIVAS
                    $carga = $cargaConsultaActual;
                    $periodo = $periodoConsultaActual;
                    $estudiante = $resultado['mat_id'];
                    include("../definitivas.php");
                    
                    $colorEstudiante = '#000;';
                    if($resultado['mat_inclusion']==1){$colorEstudiante = 'blue;';}
                ?>
                
                <tr id="fila_<?=$resultado['mat_id'];?>">
                    <td style="text-align:center; width: 100px;"><?=$contReg;?></td>
                    <td style="text-align:center; width: 100px;"><?=$resultado['mat_id'];?></td>

                    <td style="color: <?=$colorEstudiante;?>">
                        <img src="../files/fotos/<?=$resultado['uss_foto'];?>" width="50">
                        <?=Estudiantes::NombreCompletoDelEstudiante($resultado);?>
                    </td>

                    <?php
                        $cA = Actividades::traerActividadesCarga($config, $cargaConsultaActual, $periodoConsultaActual);
                        $contRegActividades = 1;
                        while($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)){
                        //LAS CALIFICACIONES
                        $notasResultado = Calificaciones::traerCalificacionActividadEstudiante($config, $rA['act_id'], $resultado['mat_id']);

                        $arrayEnviar = [
                            "tipo"=>5, 
                            "descripcionTipo"=>"Para ocultar la X y limpiar valor, cuando son diferentes actividades.", 
                            "idInput"=>$resultado['mat_id']."-".$rA['act_id']
                        ];
                        $arrayDatos = json_encode($arrayEnviar);
                        $objetoEnviar = htmlentities($arrayDatos);

                        if(!empty($notasResultado) && $notasResultado['cal_nota']<$config[5]) $colorNota= $config[6]; elseif(!empty($notasResultado) && $notasResultado['cal_nota']>=$config[5]) $colorNota= $config[7]; else $colorNota= "black";
                        
                        $estiloNotaFinal="";
                        if(!empty($notasResultado) && $config['conf_forma_mostrar_notas'] == CUALITATIVA){		
                            $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasResultado['cal_nota']);
                            $estiloNotaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
                        }	
                        ?>

                        <?php include("td-calificaciones.php");?>

                    <?php
                        $contRegActividades ++;
                    }

                    include("td-porcentaje-definitiva.php");
                    ?>

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

<style>
/* ============================================
   RESALTADO DE NOTAS FALTANTES
   ============================================ */
.celda-nota-faltante {
    position: relative;
    animation: pulsoSuave 2s ease-in-out infinite;
}

@keyframes pulsoSuave {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
    }
    50% {
        box-shadow: 0 0 0 5px rgba(245, 158, 11, 0);
    }
}

.celda-nota-faltante input[type="text"] {
    animation: none;
    transition: all 0.3s ease;
}

.celda-nota-faltante input[type="text"]:focus {
    background: #ffffff !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}
</style>

<script>
// ============================================
// FUNCIÓN GLOBAL PARA ACTUALIZAR RESALTADO
// ============================================
window.actualizarResaltadoCelda = function(inputElement) {
    const valor = inputElement.value.trim();
    const celda = inputElement.closest('td');
    
    if (!celda) return;
    
    if (valor === '' || valor === null) {
        // Restaurar resaltado si está vacío Y tiene actividad registrada
        const actividadRegistrada = inputElement.closest('td').classList.contains('celda-nota-faltante') || 
                                   inputElement.getAttribute('data-carga-actividad') !== null;
        
        if (actividadRegistrada) {
            celda.classList.add('celda-nota-faltante');
            celda.style.background = 'linear-gradient(135deg, #fff9e6 0%, #ffedd5 100%)';
            celda.style.borderLeft = '3px solid #f59e0b';
            inputElement.style.background = '#fff7ed';
            inputElement.style.border = '2px solid #fb923c';
            inputElement.style.fontWeight = '600';
            inputElement.placeholder = '⚠️';
        }
    } else {
        // Remover resaltado si tiene valor
        celda.classList.remove('celda-nota-faltante');
        celda.style.background = '';
        celda.style.borderLeft = '';
        inputElement.style.background = '';
        inputElement.style.border = '';
        inputElement.style.fontWeight = '';
        inputElement.placeholder = '';
    }
};

// Inicializar listeners para inputs existentes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[data-cod-estudiante]').forEach(input => {
        input.addEventListener('input', function() {
            window.actualizarResaltadoCelda(this);
        });
        
        input.addEventListener('change', function() {
            window.actualizarResaltadoCelda(this);
        });
    });
    
    // Observer para contenido dinámico
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    const nuevosInputs = node.querySelectorAll ? node.querySelectorAll('input[data-cod-estudiante]') : [];
                    nuevosInputs.forEach(input => {
                        input.addEventListener('input', function() {
                            window.actualizarResaltadoCelda(this);
                        });
                        input.addEventListener('change', function() {
                            window.actualizarResaltadoCelda(this);
                        });
                    });
                }
            });
        });
    });
    
    const tabContent = document.getElementById('nav-calificaciones-todas');
    if (tabContent) {
        observer.observe(tabContent, { childList: true, subtree: true });
    }
});

// Tooltip para botones deshabilitados
$(document).ready(function() {
    $('button[disabled][title]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });
});
</script>

<?php include("../compartido/guardar-historial-acciones.php");?>