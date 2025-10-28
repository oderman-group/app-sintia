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
        
    <!-- ✅ Contenedor con scroll horizontal para las actividades -->
    <div class="table-container-fixed" style="overflow-x: auto; overflow-y: visible; position: relative; max-height: 75vh;">
        
        <span id="respRCT"></span>
        <?php
        $arrayEnviar = [
            "tipo"            => 1, 
            "descripcionTipo" => "Para ocultar fila del registro."
        ];
        $arrayDatos = json_encode($arrayEnviar);
        $objetoEnviar = htmlentities($arrayDatos);
        ?>
        
        <table class="table table-striped custom-table table-hover" id="tabla_notas" style="margin-bottom: 0;">
            <thead>
                <tr>
                <th style="text-align:center;">#</th>
                <th style="text-align:center;">ID</th>
                <th style="text-align:left;"><?=$frases[61][$datosUsuarioActual['uss_idioma']];?></th>
                <?php
                    $cA = Actividades::traerActividadesCarga($config, $cargaConsultaActual, $periodoConsultaActual);
                    while($rA = mysqli_fetch_array($cA, MYSQLI_BOTH)){
                    $descripcionLimpia = htmlspecialchars($rA['act_descripcion'], ENT_QUOTES, 'UTF-8');
                    echo '<th class="columna-actividad-header">
                        <a href="calificaciones-editar.php?idR='.base64_encode($rA['act_id']).'" 
                           title="'.$descripcionLimpia.' - Valor: '.$rA['act_valor'].'%" 
                           class="actividad-link">
                            <span class="actividad-id">'.$rA['act_id'].'</span>
                            <span class="actividad-descripcion">'.$descripcionLimpia.'</span>
                            <span class="actividad-porcentaje">('.$rA['act_valor'].'%)</span>
                        </a>
                        <a href="#" 
                           name="calificaciones-eliminar.php?idR='.base64_encode($rA['act_id']).'&idIndicador='.base64_encode($rA['act_id_tipo']).'&carga='.base64_encode($cargaConsultaActual).'&periodo='.base64_encode($periodoConsultaActual).'" 
                           onClick="deseaEliminar(this)" 
                           class="actividad-eliminar" 
                           '.$deleteOculto.'>
                            <i class="fa fa-times"></i>
                        </a>
                        <input 
                            type="text" 
                            style="text-align: center; font-weight: bold;"
                            size="10" 
                            title="Nota masiva para esta actividad"
                            name="'.$rA['act_id'].'" 
                            onChange="notasMasiva(this)" 
                            class="input-nota-masiva"
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
                    <td style="text-align:center;"><?=$contReg;?></td>
                    <td style="text-align:center;"><?=$resultado['mat_id'];?></td>

                    <td style="color: <?=$colorEstudiante;?>; text-align:left;">
                        <?php
                        // ✅ Validar si existe la foto, sino usar imagen por defecto
                        $fotoEstudiante = '../files/fotos/default.png';
                        if(!empty($resultado['uss_foto']) && file_exists(ROOT_PATH.'/main-app/files/fotos/'.$resultado['uss_foto'])){
                            $fotoEstudiante = '../files/fotos/'.$resultado['uss_foto'];
                        }
                        ?>
                        <img src="<?=$fotoEstudiante;?>" width="50" height="50" style="border-radius: 50%; object-fit: cover; border: 2px solid #e0e0e0; margin-right: 8px; vertical-align: middle;">
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
   TABLA CON COLUMNAS FIJAS Y ENCABEZADO FIJO
   ============================================ */
.table-container-fixed {
    overflow-x: auto;
    overflow-y: auto;
    position: relative;
    max-height: 75vh;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

#tabla_notas {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

/* ============================================
   ENCABEZADO FIJO (STICKY HEADER)
   ============================================ */
#tabla_notas thead {
    position: sticky;
    top: 0;
    z-index: 100;
    background: #fff;
}

#tabla_notas thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    padding: 12px 8px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.3);
    position: sticky;
    top: 0;
    white-space: normal;
    word-wrap: break-word;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    line-height: 1.4;
}

/* Estilos específicos para encabezados de actividades (columnas dinámicas) */
#tabla_notas thead th:nth-child(n+4) {
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%) !important;
    color: #ffffff !important;
    font-size: 11px;
    min-height: 80px;
    vertical-align: middle;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
}

/* Texto blanco para todos los elementos dentro de los encabezados de actividades */
#tabla_notas thead th:nth-child(n+4) a {
    color: #ffffff !important;
    text-decoration: none;
    font-weight: 600;
    display: block;
    padding: 2px 0;
    line-height: 1.3;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

#tabla_notas thead th:nth-child(n+4) a:hover {
    color: #fbbf24 !important;
    text-decoration: underline;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

#tabla_notas thead th:nth-child(n+4) input[type="text"] {
    margin-top: 5px;
    width: 90%;
    padding: 4px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.4);
    background: rgba(255, 255, 255, 0.98);
    color: #1a202c;
    font-weight: 600;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

#tabla_notas thead th:nth-child(n+4) input[type="text"]:focus {
    background: #ffffff;
    border-color: #fbbf24;
    box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.3);
    outline: none;
}

#tabla_notas thead th:nth-child(n+4) a i.fa-times {
    color: #fed7d7 !important;
    font-size: 14px;
    margin-top: 3px;
    display: inline-block;
    transition: all 0.2s ease;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

#tabla_notas thead th:nth-child(n+4) a i.fa-times:hover {
    color: #fc8181 !important;
    transform: scale(1.2);
}

/* Asegurar legibilidad en las dos últimas columnas (% y Definitiva) */
#tabla_notas thead th:last-child,
#tabla_notas thead th:nth-last-child(2) {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: #ffffff !important;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    min-height: auto;
}

/* ============================================
   COLUMNAS FIJAS (#, ID, NOMBRE)
   ============================================ */
#tabla_notas thead th:nth-child(1),
#tabla_notas tbody td:nth-child(1) {
    position: sticky;
    left: 0;
    z-index: 10;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    min-width: 45px;
    max-width: 45px;
    width: 45px;
    box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
}

#tabla_notas tbody td:nth-child(1) {
    background: #f8f9fa;
    color: #333;
}

#tabla_notas thead th:nth-child(2),
#tabla_notas tbody td:nth-child(2) {
    position: sticky;
    left: 45px;
    z-index: 10;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    min-width: 55px;
    max-width: 55px;
    width: 55px;
    box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
}

#tabla_notas tbody td:nth-child(2) {
    background: #f8f9fa;
    color: #333;
}

#tabla_notas thead th:nth-child(3),
#tabla_notas tbody td:nth-child(3) {
    position: sticky;
    left: 100px;
    z-index: 10;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    min-width: 250px;
    max-width: 320px;
    width: 280px;
    box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
    text-align: left !important;
    padding-left: 12px !important;
}

#tabla_notas tbody td:nth-child(3) {
    background: #f8f9fa;
    color: #333;
    padding: 10px;
}

/* Asegurar que la primera columna fija del encabezado tenga z-index más alto */
#tabla_notas thead th:nth-child(1) {
    z-index: 101;
}

#tabla_notas thead th:nth-child(2) {
    z-index: 101;
}

#tabla_notas thead th:nth-child(3) {
    z-index: 101;
}

/* ============================================
   ESTILOS PARA FILAS Y CELDAS NORMALES
   ============================================ */
#tabla_notas tbody td {
    padding: 8px;
    border: 1px solid #dee2e6;
    vertical-align: middle;
    background: white;
}

#tabla_notas tbody tr:hover td {
    background-color: #f1f3f5;
}

/* Mantener fondo en columnas fijas al hacer hover */
#tabla_notas tbody tr:hover td:nth-child(1),
#tabla_notas tbody tr:hover td:nth-child(2),
#tabla_notas tbody tr:hover td:nth-child(3) {
    background-color: #e9ecef !important;
}

/* ============================================
   COLUMNAS DE ACTIVIDADES (SCROLL HORIZONTAL)
   ============================================ */
#tabla_notas thead th:nth-child(n+4),
#tabla_notas tbody td:nth-child(n+4) {
    position: relative;
    z-index: 1;
    width: 120px;
    min-width: 120px;
    max-width: 120px;
    height: 80px;
    min-height: 80px;
    max-height: 80px;
    overflow: hidden;
}

/* Encabezados de actividades - ancho y alto fijos */
#tabla_notas thead th:nth-child(n+4) {
    width: 120px !important;
    min-width: 120px !important;
    max-width: 120px !important;
    height: 80px !important;
    min-height: 80px !important;
    max-height: 80px !important;
    padding: 6px 4px !important;
    overflow: hidden;
    text-overflow: ellipsis;
    display: table-cell !important;
    vertical-align: middle;
}

/* Celdas de cuerpo - ancho y alto fijos */
#tabla_notas tbody td:nth-child(n+4) {
    width: 120px;
    min-width: 120px;
    max-width: 120px;
    height: 60px;
    min-height: 60px;
    max-height: 60px;
    padding: 8px 4px;
    text-align: center;
    vertical-align: middle;
}

/* Contenedor de actividad en encabezado */
#tabla_notas thead th.columna-actividad-header {
    display: table-cell !important;
    vertical-align: middle !important;
    text-align: center !important;
}

/* Enlace principal de la actividad */
.actividad-link {
    display: block;
    width: 100%;
    max-width: 100%;
    text-decoration: none;
    color: #ffffff !important;
    line-height: 1.3;
    margin-bottom: 3px;
}

/* ID de actividad - primera línea, más grande */
.actividad-id {
    font-size: 13px;
    font-weight: 700;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-align: center;
    width: 100%;
    margin-bottom: 2px;
}

/* Descripción - truncamiento con puntos suspensivos */
.actividad-descripcion {
    font-size: 10px;
    font-weight: 500;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-word;
    line-height: 1.2;
    max-height: 24px;
    text-align: center;
    width: 100%;
    margin-bottom: 2px;
}

/* Porcentaje - última línea */
.actividad-porcentaje {
    font-size: 10px;
    font-weight: 600;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-align: center;
    width: 100%;
}

/* Botón eliminar */
.actividad-eliminar {
    display: block;
    margin: 2px 0;
    text-align: center;
    line-height: 1;
    color: #fed7d7 !important;
}

.actividad-eliminar:hover {
    color: #fc8181 !important;
}

.actividad-eliminar i {
    font-size: 12px;
    transition: all 0.2s ease;
}

.actividad-eliminar:hover i {
    transform: scale(1.2);
}

/* Ajustar input de nota masiva */
.input-nota-masiva {
    width: 95% !important;
    max-width: 95%;
    margin-top: 4px;
    padding: 3px;
    font-size: 11px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.4);
    background: rgba(255, 255, 255, 0.98);
    color: #1a202c;
    font-weight: 600;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.input-nota-masiva:focus {
    background: #ffffff;
    border-color: #fbbf24;
    box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.3);
    outline: none;
}

/* Ajustar celdas del cuerpo para notas */
#tabla_notas tbody td:nth-child(n+4) input[type="text"] {
    width: 95%;
    max-width: 95%;
    text-align: center;
    padding: 6px;
    font-size: 13px;
    font-weight: 600;
}

/* Excluir las dos últimas columnas (% y Definitiva) del ancho fijo */
#tabla_notas thead th:last-child,
#tabla_notas thead th:nth-last-child(2),
#tabla_notas tbody td:last-child,
#tabla_notas tbody td:nth-last-child(2) {
    width: 80px !important;
    min-width: 80px !important;
    max-width: 80px !important;
    height: auto !important;
    min-height: auto !important;
    max-height: none !important;
}

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