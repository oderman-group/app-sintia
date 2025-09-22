<?php
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(dirname(dirname(__FILE__))));
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";

$conexionPDO = Conexion::newConnection('PDO');
$conexion    = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion);

require_once(ROOT_PATH."/main-app/class/Sysjobs.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_temp_calculo_boletin_estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_sys_jobs.php");
;

$cantidadActualizados = BDT_SysJobs::updateStautusToPendingWhenLongTimeInProcess();

if ($cantidadActualizados > 0) {
    echo 'Se han actualizado '.$cantidadActualizados.' jobs que tenian demasiado tiempo en proceso.'."<br>";
}

$parametrosBuscar = [
    "tipo"   => JOBS_TIPO_GENERAR_INFORMES,
    "estado" => JOBS_ESTADO_PENDIENTE
];

BindSQL::iniciarTransacion();

echo 'Comenzando el proceso de generación de informes...'."<br>";

try {
    $listadoCrobjobsActualizar = SysJobs::listar($parametrosBuscar);

    echo 'Se encontraron '.mysqli_num_rows($listadoCrobjobsActualizar).' jobs para generar informes.'."<br>";

    // Actualizamos todos los jobs seleccionados a en PROCESO de una vez para evitar colisiones con otro job
    if (mysqli_num_rows($listadoCrobjobsActualizar) > 0) {
        while($resultadoJobsActualizar = mysqli_fetch_array($listadoCrobjobsActualizar, MYSQLI_ASSOC)) {
            $datos = [
                "id"     => $resultadoJobsActualizar['job_id'],
                "estado" => JOBS_ESTADO_PROCESO,
            ];

            SysJobs::actualizar($datos);

            echo 'Actualizado a en PROCESO el job con ID: '.$resultadoJobsActualizar['job_id']."<br>";
        }
        mysqli_free_result($listadoCrobjobsActualizar);
    } else {
        //Evitamos que el job continue si no hay jobs pendientes
        exit();
    }

    $parametrosBuscar = [
        "tipo"   => JOBS_TIPO_GENERAR_INFORMES,
        "estado" => JOBS_ESTADO_PROCESO
    ];

    $listadoCrobjobs = SysJobs::listar($parametrosBuscar);

    while ($resultadoJobs = mysqli_fetch_array($listadoCrobjobs, MYSQLI_BOTH)) {

        echo 'Procesando job ID: '.$resultadoJobs['job_id']."<br>";

        // fecha1 es la primera fecha
        $fechaInicio   = new DateTime();
        $finalizado    = false;
        $parametros    = json_decode($resultadoJobs["job_parametros"], true);
        $institucionId = $resultadoJobs["job_id_institucion"];
        $anio          = $resultadoJobs["job_year"];
        $intento       = intval($resultadoJobs["job_intentos"]);

        $_SESSION["id"]            = $resultadoJobs["job_responsable"];
        $_SESSION["bd"]            = $resultadoJobs["job_year"];
        $_SESSION["idInstitucion"] = $resultadoJobs["job_id_institucion"];

        $grado   = $parametros["grado"];
        $grupo   = $parametros["grupo"];
        $carga   = $parametros["carga"];
        $periodo = $parametros["periodo"];

        $informacionAdicional = [
            'carga'   => $carga,
            'periodo' => $periodo
        ];

        $config = RedisInstance::getSystemConfiguration();

        //Consultamos los estudiantes del grado y grupo
        $filtroAdicional         = "AND mat_grado='".$grado."' 
                                    AND mat_grupo='".$grupo."' 
                                    AND (mat_estado_matricula=1 OR mat_estado_matricula=2)
                                    ";
        $cursoActual             = GradoServicios::consultarCurso($grado);
        $numEstudiantes          = 0;
        $finalizado              = true;
        $erroresNumero           = 0;
        $listadoEstudiantesError = "";
        $mensaje                 = "";

        if ($config['conf_porcentaje_completo_generar_informe'] == Boletin::GENERAR_CON_PORCENTAJE_COMPLETO) {
            echo 'Ingresamos a verificación de porcentaje completo para los estudiantes.'."<br>";

            $consultaListaEstudantesError = Estudiantes::listarEstudiantesNotasFaltantes($carga, $periodo, $cursoActual["gra_tipo"]);

            //Verificamos que el estudiante tenga sus notas al 100%
            if (mysqli_num_rows($consultaListaEstudantesError) > 0) {
                $erroresNumero = mysqli_num_rows($consultaListaEstudantesError);
                $contador      = 0;

                while ($estudianteResultadoError = mysqli_fetch_array($consultaListaEstudantesError, MYSQLI_BOTH)) {
                    $contador ++;

                    $porcentajeAcumulado     = $estudianteResultadoError['acumulado'] > 0 ? $estudianteResultadoError['acumulado'] : 0;
                    $listadoEstudiantesError = $listadoEstudiantesError."<br><br>".$contador."): ".$estudianteResultadoError['mat_nombres']
                    ." ".$estudianteResultadoError['mat_primer_apellido']." ".$estudianteResultadoError['mat_segundo_apellido']
                    ." no tiene notas completas.<br>
                    ID: <b>".$estudianteResultadoError['mat_id']."</b><br>
                    Valor Actual: <b>".$porcentajeAcumulado."% </b>";
                }

                $finalizado = false;

                echo 'Existen estudiantes con notas incompletas por lo tanto no se pudo generar el informe.'."<br>";
            }
        }

        if ($finalizado) {

            $consultaListaEstudante  = Estudiantes::listarEstudiantesEnGrados($filtroAdicional, "", $cursoActual, $grupo, $anio);

            echo '<p>Se encontraron '.mysqli_num_rows($consultaListaEstudante).' estudiantes para generar informes.'."</p>";

            if (mysqli_num_rows($consultaListaEstudante) > Estudiantes::MAXIMOS_ESTUDIANTES_CURSO) {
                echo 'No se puede generar el informe porque la lista de estudiantes es muy grande (máximo '.Estudiantes::MAXIMOS_ESTUDIANTES_CURSO.' estudiantes).'."<br>";

                $datos = [
                    "id"     => $resultadoJobs['job_id'],
                    "estado" => JOBS_ESTADO_ERROR,
                    "mensaje" => 'No se puede generar el informe porque la lista de estudiantes es muy grande (máximo '.Estudiantes::MAXIMOS_ESTUDIANTES_CURSO.' estudiantes).'
                ];
    
                SysJobs::actualizar($datos);
    
                echo 'Actualizado a en PROCESO el job con ID: '.$resultadoJobsActualizar['job_id']."<br>";

                continue;
            }

            while ($estudianteResultado = mysqli_fetch_array($consultaListaEstudante, MYSQLI_ASSOC)) {
                $estudiante = $estudianteResultado["mat_id"];

                echo 'Procesando estudiante ID: '.$estudiante." - ".$estudianteResultado['mat_nombres']."<br>";

                $porcentajeActual = Calificaciones::obtenerPorcentajeActualEstudiante($estudiante, $carga, $periodo);

                //Consultamos si tiene registros en el boletín
                $boletinDatos = Boletin::traerNotaBoletinCargaPeriodo($config, $periodo, $estudiante, $carga, $anio);

                if ($config['conf_porcentaje_completo_generar_informe'] == Boletin::OMITIR_ESTUDIANTES_CON_PORCENTAJE_INCOMPLETO) {

                    //Verificamos que el estudiante tenga sus notas al porcentaje minimo permitido
                    if ($porcentajeActual < Boletin::PORCENTAJE_MINIMO_GENERAR_INFORME && empty($boletinDatos['bol_nota'])) {
                        $erroresNumero ++;

                        $mensaje = $mensaje."<br><br>".$erroresNumero."): ".$estudianteResultado['mat_nombres']." ".$estudianteResultado['mat_primer_apellido']." ".$estudianteResultado['mat_segundo_apellido'] ." no tiene notas completas.<br>
                        ID: <b>".$estudianteResultado['mat_id']."</b><br>
                        Valor Actual: <b>".$porcentajeActual."% </b>";

                        $finalizado = false;

                        continue;
                    }
                }

                //Insertamos en la tabla temporal
                $datosParaInsertar = [
                    'tcbe_id_estudiante'        => $estudiante,
                    'tcbe_id_carga_academica'   => $carga,
                    'tcbe_periodo'              => $periodo,
                    'tcbe_id_job_referencia'    => $resultadoJobs['job_id'],
                    'tcbe_estado'               => BDT_tempCalculoBoletinEstudiantes::ESTADO_ESTUDIANTE_PENDIENTE,
                    'tcbe_datos_estudiante'     => json_encode($estudianteResultado),
                    'tcbe_calcular_indicadores' => $_SESSION["idInstitucion"] == ICOLVEN || //TODO: Esto debe ser una configuración
                                                   $_SESSION["idInstitucion"] == DEVELOPER || //TODO: Esto debe ser una configuración
                                                   $_SESSION["idInstitucion"] == DEVELOPER_PROD ? 1 : 0 //TODO: Esto debe ser una configuración
                ];

                try {
                    BDT_tempCalculoBoletinEstudiantes::Insert($datosParaInsertar, BD_ADMIN);
                    echo 'Estudiante insertado correctamente.'."<br><br>";
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $finalizado = false;
                }

                $numEstudiantes ++;

            }

            $datos = [
                "id"      => $resultadoJobs['job_id'],
                "estado"  => JOBS_ESTADO_PROCESADO,
            ];

            SysJobs::actualizar($datos);

            echo 'Actualizamos el estado del job a procesado.'."<br>";

        } else {
            if ($intento >= 3) {
                $mensaje = "<a target=\"_blank\" href=\"../docente/calificaciones-faltantes.php?
                carga=".base64_encode($carga)."
                &periodo=".base64_encode($periodo)."
                &get=".base64_encode(100)."\">
                    El informe no se pudo generar, coloque las notas a todos los estudiantes y vuelva a intentarlo.
                </a>
                ";

                SysJobs::actualizarMensaje(
                    $resultadoJobs['job_id'], 
                    $intento, 
                    $mensaje, 
                    JOBS_ESTADO_FINALIZADO
                );

                echo 'Actualizamos el estado del job a finalizado porque ya se han hecho '.$intento.' intentos.'."<br>";

                SysJobs::enviarMensaje(
                    $resultadoJobs['job_responsable'],
                    $mensaje.$listadoEstudiantesError,
                    $resultadoJobs['job_id'],
                    JOBS_TIPO_GENERAR_INFORMES,
                    JOBS_ESTADO_ERROR, 
                    $informacionAdicional
                );

                echo 'Enviamos un mensaje al responsable del job.'."<br>";
            } else {
                $texto = "";

                if ($erroresNumero > 1) {
                    $texto = $erroresNumero."  estudiantes que les";
                } else {
                    $texto = $erroresNumero."  estudiante que le";
                }

                $mensaje = "<a target=\"_blank\" href=\"../docente/calificaciones-faltantes.php?carga=".base64_encode($carga)."&periodo=".base64_encode($periodo)."&get=".base64_encode(100)."\"> El informe no se ha podido generar porque hay ".$texto." faltan notas.</a>";

                SysJobs::actualizarMensaje(
                    $resultadoJobs['job_id'],
                    $intento,
                    $mensaje,
                    JOBS_ESTADO_PENDIENTE
                );

                echo 'Actualizamos el estado del job a pendiente y haremos otro intento más tarde.'."<br>";
            }
        }

        echo "Job finalizado."."<br>";

        echo "<hr>";

    }

    BindSQL::finalizarTransacion();
} catch (Exception $e) {

    BindSQL::revertirTransacion();

    echo $e->getMessage();

    Utilidades::logError($e);

    $datos = [
        "id"      => $resultadoJobs['job_id'],
        "estado"  => JOBS_ESTADO_ERROR,
        "mensaje" => $e->getMessage(),
    ];

    SysJobs::actualizar($datos);

}

function minutosTranscurridos($fecha_i, $fecha_f)
{
    $intervalo = $fecha_i->diff($fecha_f);
    $minutos   = $intervalo->i;
    $segundos  = $intervalo->s;

    return " Finaliz&oacute; en: <i> {$minutos} min y {$segundos} seg.</i>";
}