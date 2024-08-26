<?php
require_once 'UsuariosPadre.php';
class SysJobs {
     /**
     * Esta funci&oacute;n  crea &oacute; actualiza si ya existe un registro, llamando las funcions de crear o  atualizar 
     * 
     * @param string $tipo
     * @param string $prioridad
     * @param array $parametros
     * @param string $msj
     * 
     * @return String // se retorna el mensaje de la operacion registrada sys_jobs
     */
    public static function registrar($tipo, $prioridad, array $parametros = [], $msj ='')
    {
        global $config;
        $parametrosBuscar = array(
            "tipo" =>$tipo,
            "responsable" => $_SESSION['id'],
            "parametros" => json_encode($parametros),
            "agno"=>$config['conf_agno']
        );
       
        $buscarJobs=self::consultar($parametrosBuscar);
        $cantidad = mysqli_num_rows($buscarJobs);
        if($cantidad<1){
            switch($tipo) {
                case JOBS_TIPO_GENERAR_INFORMES:
                    $msj=" El informe ya se est&aacute; generando.";
                break;

                case JOBS_TIPO_IMPORTAR_ESTUDIANTES_EXCEL:
                    $msj = ' Los estudiantes ya se est&aacute;n subiendo a la plataforma.';
                break;

                default:
                    $msj = ' No se identific&oacute; el tipo de proceso.';
                break;
            }
            

            $idRegistro =self::crear($tipo,$prioridad,$parametros,$msj);
            $mensaje="Se realiz&oacute; exitosamente el proceso de ".$tipo." con el c&oacute;digo ".$idRegistro.". Te enviaremos un mensaje al correo interno de la plataforma cuando haya finalizado este proceso.";
        }else{
            $jobsEncontrado = mysqli_fetch_array($buscarJobs, MYSQLI_BOTH);
            $idRegistro = $jobsEncontrado["job_id"];           
            if($jobsEncontrado["job_estado"]==JOBS_ESTADO_ERROR){          
                $datos = array(
                    "estado" =>JOBS_ESTADO_PENDIENTE,
                    "intentos" =>'0',
                    "id" => $jobsEncontrado['job_id'],
                    "mensaje" => 'La petici&oacute;n de generaci&oacute;n de informe se envi&oacute; nuevamente.'
                );
                self::actualizar($datos);
                $mensaje="Se actualiz&oacute; exitosamente el proceso de ".$tipo." con el c&oacute;digo ".$idRegistro." Te enviaremos un mensaje al correo interno de la plataforma cuando haya finalizado este proceso.";
            }else{
                $mensaje="Proceso ".$tipo." con el c&oacute;digo ".$idRegistro." ya está marcado como".$jobsEncontrado["job_intentos"];               
            }
            
            
            
           
        }
        return $mensaje;
    }
    /**
     * Esta funci&oacute;n  crea  un registro de en la tabla sys_jobs
     * 
     * @param string $tipo
     * @param string $prioridad 
     * @param array $parametros
     * @param string $msj
     * 
     * @return String // se retorna el id del registro
     */
    public static function crear($tipo,$prioridad,array $parametros = [],$mensaje ='')
    {
        global $conexion, $baseDatosServicios,$config;
        $idRegistro = -1;        
        try {
            $sqlUpdate="INSERT INTO ".$baseDatosServicios.".sys_jobs(
                job_estado, 
                job_tipo, 
                job_fecha_creacion, 
                job_responsable, 
                job_id_institucion, 			
                job_mensaje,
                job_parametros,
                job_year, 
                job_intentos,
                job_prioridad,
                job_ambiente)
            VALUES(
                '".JOBS_ESTADO_PENDIENTE."',
                '".$tipo."',
                NOW(), 
                '".$_SESSION['id']."', 
                '".$config['conf_id_institucion']."', 
                '".$mensaje."', 
                '".json_encode($parametros)."', 
                '".$config['conf_agno']."', 
                '0', 
                '".$prioridad."',
                '".ENVIROMENT."'
            )";
            mysqli_query($conexion,$sqlUpdate);
            $idRegistro = mysqli_insert_id($conexion);
       
            
        } catch (Exception $e) {
            echo "Excepci&oacute;n catpurada: ".$e->getMessage();
            exit();
        }
        
        return $idRegistro;
    }
    /**
     * Esta funci&oacute;n  actualiza  un registro de en la tabla sys_jobs
     * 
     * @param array $datos // son los parametros que se va actualizar en la tabla
     * 
     * @return void 
     */
    public static function actualizar(array $datos = [])
    {
        global $conexion, $baseDatosServicios;
        if(isset($datos["intentos"]) && !is_null($datos["intentos"])){
            $intento = intval($datos["intentos"]);
            if($intento>3){               
                $datos["estado"]=JOBS_ESTADO_ERROR;
            }
        }
        
        $setIntentos=empty($datos["intentos"]) ? "" : ",job_intentos='".$datos["intentos"]."'";
        $setEstado=empty($datos["estado"])?"":",job_estado='".$datos["estado"]."'";
        $setMensaje=empty($datos["mensaje"])?"":",job_mensaje='".$datos["mensaje"]."'";
        $setPrioridad=empty($datos["prioriedad"])?"":",job_prioriedad='".$datos["prioriedad"]."'";

        $sqlUpdate="UPDATE ".$baseDatosServicios.".sys_jobs
        SET job_fecha_modificacion=NOW(), job_host = '".$_SERVER['HTTP_HOST']."'"
         .$setIntentos
         .$setEstado
         .$setMensaje
         .$setPrioridad.
         " WHERE job_id='".$datos["id"]."'";

        try {
            mysqli_query($conexion,$sqlUpdate);
        } catch (Exception $e) {
            echo "Excepci&oacute;n catpurada: ".$e->getMessage();
            exit();
        }
    }
     /**
     * Esta funci&oacute;n  consulta el registro en la tabla sys_jobs 
     * hay que tener en ceunta que siempre debe venir en el array $parametros
     *  los parametros["tipo"], parametros["responsable"] , parametros["agno"] 
     * @param array $parametrosBusqueda 
     * 
     * // se retorna el registro consultado
     */
    public static function consultar(array $parametrosBusqueda = [])
    {
        global $conexion, $baseDatosServicios;
        $resultado = [];
        $andEstado=empty($parametrosBusqueda["estado"])?" ":"AND job_estado='".$parametrosBusqueda["estado"]."'";
        $andParametros=empty($parametrosBusqueda["parametros"])?" ":"AND job_parametros='".$parametrosBusqueda["parametros"]."'";
       
        $sqlExecute="SELECT * FROM ".$baseDatosServicios.".sys_jobs
        LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss.institucion=job_id_institucion AND uss_id = job_responsable AND uss.year={$parametrosBusqueda["agno"]}
        LEFT JOIN ".$baseDatosServicios .".instituciones ON ins_id = job_id_institucion
        WHERE job_tipo = '".$parametrosBusqueda["tipo"]."'
        AND job_responsable ='".$parametrosBusqueda["responsable"]."'
        AND job_year='".$parametrosBusqueda["agno"]."'
        AND job_ambiente='".ENVIROMENT."'
        ".$andParametros
         .$andEstado."       
        ORDER BY job_fecha_creacion";
        try {
            $resultado = mysqli_query($conexion,$sqlExecute);
        } catch (Exception $e) {
            echo "Excepci&oacute;n catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }
     /**
     * Esta funci&oacute;n  lista los registros en la tabla sys_jobs 
     * hay que tener en ceunta que siempre debe venir en el array $parametros
     * los parametros["estado"]
     * @param array $parametros 
     * 
     * @return mysqli_result // se retorna el registro para consultar
     */
    public static function listar(array $parametrosBusqueda = []) {
        global $conexion, $baseDatosServicios;
        $resultado = null;
        $andEstado=empty($parametrosBusqueda["estado"])?JOBS_ESTADO_PENDIENTE:$parametrosBusqueda["estado"];
        $andTipo=empty($parametrosBusqueda["tipo"])?" ":"AND job_tipo='".$parametrosBusqueda["tipo"]."' ";
        $andResponsable=empty($parametrosBusqueda["responsable"])?" ":"AND job_responsable='".$parametrosBusqueda["responsable"]."' ";
        $andAgno=empty($parametrosBusqueda["agno"])?" ":"AND job_year='".$parametrosBusqueda["agno"]."' ";
        
        $sqlExecute="SELECT * FROM ".$baseDatosServicios.".sys_jobs
        LEFT JOIN ".$baseDatosServicios .".instituciones ON ins_id = job_id_institucion
        WHERE job_estado = '".$andEstado."'
        AND job_ambiente='".ENVIROMENT."'
        ".$andTipo
        . $andResponsable
        .$andAgno."            
        ORDER BY job_prioridad,job_fecha_creacion
        LIMIT 0, 20
        ";
        try {
            $resultado = mysqli_query($conexion,$sqlExecute);
        } catch (Exception $e) {
            throw new Exception("Excepción capturada: " . $e->getMessage());
        }

        return $resultado;
    }
    /**
     * Esta funci&oacute;n  actauliza los intentos de cada crob jobs ejecutado
     * 
     * @param array $id
     * @param array $intento 
     * @param array $mensaje 
     * 
     * 
     * @return void 
     */
    public static function actualizarMensaje($id, $intento, $mensaje, $estado=JOBS_ESTADO_ERROR) {
        $intento = intval($intento) + 1;
        $datos   = array(
            "id"       => $id,
            "mensaje"  => $mensaje,
            "intentos" => $intento,
            "estado"   => $estado
        );
        self::actualizar($datos);
    }
    /**
     * Esta función  envia mensajes al usuario responsable del cron jobs, notificando el estado
     * 
     * @param String $destinatario
     * @param array $contenido 
     * @param array $idJob 
     * @param array $tipo
     * 
     * @return void 
     */
    public static  function enviarMensaje($destinatario, $contenido, $idJob, $tipo, $estado, $informacionAdicional = ''){
        global $conexion,$baseDatosServicios,$config;       
        
        $para=$destinatario;
        $asunto = '';

        switch($estado) {
            case JOBS_ESTADO_FINALIZADO:

                if( $tipo == JOBS_TIPO_GENERAR_INFORMES) {
                    $asunto = "El informe fue generado correctamente | Carga:{$informacionAdicional['carga']} - Periodo:{$informacionAdicional['periodo']} ";
                } else if( $tipo == JOBS_TIPO_IMPORTAR_ESTUDIANTES_EXCEL ) {
                    $asunto = "La importaci&oacute;n de estudiantes fue completada correctamente ";
                }
                
            break;

            case JOBS_ESTADO_ERROR:
                if( $tipo == JOBS_TIPO_GENERAR_INFORMES) {
                    $asunto = "El informe present&oacute; un error | Carga:{$informacionAdicional['carga']} - Periodo:{$informacionAdicional['periodo']}";
                } else if( $tipo == JOBS_TIPO_IMPORTAR_ESTUDIANTES_EXCEL ) {
                    $asunto = "La importaci&oacute;n de estudiantes present&oacute; un problema. ";
                }
            break;

            default:
            $asunto = 'Asunto inesperado!';
            break;
        }   
        try{
            $remitenteConsulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_permiso1='" .CODE_DEV_MODULE_PERMISSION. "' limit 1");
			$remitente = mysqli_fetch_array($remitenteConsulta); 
			$destinatario = UsuariosPadre::sesionUsuario($destinatario);
            $contenido="<br>Hola, Sr(a) ".$destinatario["uss_nombre"]."<br> 
            ".$asunto."<br> <p>".$contenido."</p>";
			mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".social_emails(ema_de, ema_para, ema_asunto, ema_contenido, ema_fecha, ema_visto, ema_eliminado_de, ema_eliminado_para, ema_institucion, ema_year)
				VALUES('" . $remitente["uss_id"] . "', '" . $para . "', '" . mysqli_real_escape_string($conexion,$asunto) . "', '" . mysqli_real_escape_string($conexion,$contenido) . "', now(), 0, 0, 0,'" . $config['conf_id_institucion'] . "','" . $config["conf_agno"] . "')");
						
        } catch (Exception $e) {
            echo "Excepci&oacute;n catpurada: ".$e->getMessage();
            exit();
         }
    }
}