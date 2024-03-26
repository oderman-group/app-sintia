<?php
include("session.php");
$idPaginaInterna = 'CM0009';
include("../compartido/historial-acciones-guardar.php");
require_once("../class/UsuariosPadre.php");
require_once("../class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

$config = Plataforma::sesionConfiguracion();
$_SESSION["configuracion"] = $config;

$docentesProgreso = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas 
WHERE car_docente='{$_GET["docente"]}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}
ORDER BY car_periodo");
$numCargas = mysqli_num_rows($docentesProgreso);
?>

<div class="panel">
    <header class="panel-heading panel-heading-blue"><i class="fa fa-list-ul"></i> CARGAS ACADÉMICAS</header>

    <div class="panel-body">
    <?php if($numCargas > 0) { ?>
    <p class="lead">
        Este gráfico te muestra qué porcentaje de notas ha registrado el docente, a los estudiantes, en cada una de sus cargas.
    </p>
    <?php } else { ?>
    <p class="lead">
        No hay cargas asignadas para este docente.
    </p>
    <?php } ?>
        
        <?php
        $profes = array();
        $profesNombre = array();
        $contP = 1;
        while($docProgreso = mysqli_fetch_array($docentesProgreso, MYSQLI_BOTH)){
            $datosCarga = CargaAcademica::datosRelacionadosCarga($docProgreso['car_id']);

            $datosProgreso = Actividades::consultarPorcentajeActividadesRegistradas($config, $docProgreso['car_id'], $docProgreso['car_periodo']);
            $sumasProgreso = round($datosProgreso[0],2);           
            
            if($sumasProgreso <= 50) $colorGrafico = 'danger';
            if($sumasProgreso > 50 and $sumasProgreso <80) $colorGrafico = 'warning';
            if($sumasProgreso > 80) $colorGrafico = 'info';
        ?>
            <div class="work-monitor work-progress">
                    <div class="states">
                        <div class="info">
                            <div class="desc pull-left">
                                <a href="javascript:void(0);" onClick="sweetConfirmacion('Alerta!','Esta acción te permitirá entrar como docente y ver todos los detalles de esta carga. Deseas continuar?','question','auto-login.php?user=<?=base64_encode($datosCarga['car_docente']);?>&tipe=<?=base64_encode(2)?>&carga=<?=base64_encode($datosCarga['car_id']);?>&periodo=<?=base64_encode($datosCarga['car_periodo']);?>')">
                                    <?="<b>".$datosCarga['car_id'].".</b> ".$datosCarga['gra_nombre']." ".$datosCarga['gru_nombre']." - ".$datosCarga['mat_nombre']." ( Periodo Actual: ".$docProgreso['car_periodo']." )";?>
                                </a>
                            </div>
                            <div class="percent pull-right"><?=$sumasProgreso;?>%</div>
                        </div>

                        <div class="progress progress-xs">
                            <div class="progress-bar progress-bar-<?=$colorGrafico;?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$sumasProgreso;?>%">
                                <span class="sr-only">90% </span>
                            </div>
                        </div>
                        
                    </div>
                </div>
        <?php
            $contP++;
        }
        ?>
        
    </div>
</div>
