<?php
require_once(ROOT_PATH."/main-app/class/Sms.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_codigo_verificacion.php");

class Notificacion {

    public const CANAL_SMS   = 'SMS';
    public const CANAL_EMAIL = 'EMAIL';

    public const CANALES_VALIDOS = [self::CANAL_SMS, self::CANAL_EMAIL];

    public const PROCESO_RECUPERAR_CLAVE = 'RECUPERAR_CLAVE';
    public const PROCESO_ACTIVAR_CUENTA  = 'ACTIVAR_CUENTA';

    public const PROCESOS_VALIDOS = [self::PROCESO_RECUPERAR_CLAVE, self::PROCESO_ACTIVAR_CUENTA];

    /**
     * Envia un código de verificación a un usuario a través del canal especificado.
     *
     * @param array  $data     Datos necesarios para el envío de la notificación, que incluyen:
     *                         - 'usuario_nombre': Nombre del usuario.
     *                         - 'usuario_id': ID del usuario.
     *                         - 'institucion_id': ID de la institución.
     *                         - 'year': Año relacionado con el proceso.
     *                         - 'asunto' (opcional): Asunto para el email (requerido si el canal es email).
     *                         - 'body_template_route' (opcional): Ruta del template para el email (requerido si el canal es email).
     * @param string $canal    Canal a través del cual se enviará la notificación. 
     *                         Debe ser uno de los valores definidos en `self::CANALES_VALIDOS`.
     *                         Ejemplo: `self::CANAL_SMS`, `self::CANAL_EMAIL`.
     * @param string $proceso  Tipo de proceso de la notificación.
     *                         Debe ser uno de los valores definidos en `self::PROCESOS_VALIDOS`.
     *
     * @throws Exception Si el canal no es válido o el proceso no es válido.
     *
     * @return array $datosCodigo retorna un array con el ID y el código registrado
     */
    public function enviarCodigoNotificacion(array $data, $canal, $proceso) {

        if (!in_array($canal, self::CANALES_VALIDOS)) {
            throw new Exception("Canal de notificación inválido.");
        }

        if (!in_array($proceso, self::PROCESOS_VALIDOS)) {
            throw new Exception("Proceso de notificación inválido.");
        }

        $codigo  = $this->generarCodigoValido();
        $mensaje = 'Hola '.$data['usuario_nombre'].', tu código de verificación SINTIA es: '. $codigo;

        $fechaActual = new DateTime();
        $datos = [
            'codv_usuario_asociado'    => $data['usuario_id'],
            'institucion'              => $data['institucion_id'],
            'year'                     => $data['year'],
            'codv_canal'               => $canal,
            'codv_tipo_proceso'        => $proceso,
            'codv_codigo_verificacion' => $codigo,
            'codv_fecha_registro'      => $fechaActual->format('Y-m-d H:i:s'),
            'codv_activo'              => 1,
        ];

        $idRegistro = $this->guardarCodigoValido($datos);

        switch ($canal) {
            case self::CANAL_SMS:
                $sms = new Sms();
                $data['mensaje'] = $mensaje;
                $sms->enviarSms($data);
                break;

            case self::CANAL_EMAIL:
                $asunto            = $data['asunto'] . $codigo;
                $bodyTemplateRoute = $data['body_template_route'];
                $data['codigo']    = $codigo; // Añadir el código al array de datos para el template de email.

                EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
                break;
        }

        $datosCodigo = [
            'idRegistro'     => $idRegistro,
            'codigo'        => $codigo,
        ];

        return $datosCodigo;

    }

    /**
     * Genera un código de verificación válido basado en el tipo especificado.
     *
     * @param string $typeCode Tipo de código a generar. Valores permitidos:
     *                         - 'numeric': Genera un código numérico de 6 dígitos.
     *                         - 'alphanumeric': Genera un código alfanumérico de 6 caracteres.
     *
     * @throws Exception Si se especifica un tipo de código no válido.
     *
     * @return string El código de verificación generado.
     */
    public function generarCodigoValido($typeCode = 'numeric'): string {
        switch ($typeCode) {
            case 'numeric':
                return rand(100000, 999999);
            case 'alphanumeric':
                return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
            default:
                throw new Exception("Tipo de código inválido.");
        }
    }

    /**
     * Guarda un código de verificación en la base de datos.
     *
     * @param array $datos Datos necesarios para registrar el código de verificación, que incluyen:
     *                     - 'codv_usuario_asociado': ID del usuario asociado.
     *                     - 'institucion': ID de la institución relacionada.
     *                     - 'year': Año del proceso.
     *                     - 'codv_canal': Canal utilizado para la notificación.
     *                     - 'codv_tipo_proceso': Tipo de proceso de verificación.
     *                     - 'codv_codigo_verificacion': Código de verificación generado.
     *                     - 'codv_activo': Estado del código (activo/inactivo).
     *
     * @return int Retorna el ID del ultimo registro insertado.
     */
    public function guardarCodigoValido(array $datos) {
        return BDT_CodigoVerificacion::Insert($datos, BD_ADMIN);
    }

    /**
     * Recupera un código de verificación desde la base de datos basado en el predicado especificado.
     *
     * @param array  $predicado     Filtros para la consulta, donde cada clave representa
     *                              un campo de la base de datos y cada valor el criterio de búsqueda.
     *                              Ejemplo: ['codv_usuario_asociado' => 123, 'codv_activo' => 1].
     * @param string $campos        Especifica los campos que se desean recuperar.
     *                              Por defecto, se recuperan todos los campos utilizando "*".
     *                              Ejemplo: 'codv_codigo_verificacion, codv_fecha_creacion'.
     *
     * @return PDOStatement|false   Un objeto PDOStatement que contiene los resultados de la consulta o false en caso de error..
     */
    public function traerCodigoValido(
        array   $predicado,
        string  $campos = "*"
    ) {
        return BDT_CodigoVerificacion::Select($predicado, $campos, BD_ADMIN);
    }

    /**
     * Actualiza un registro de código de verificación en la base de datos.
     *
     * @param array $datos     Datos a actualizar, donde cada clave es el nombre del campo
     *                         y cada valor es el nuevo valor para ese campo.
     *                         Ejemplo: ['codv_activo' => 0, 'codv_fecha_uso' => '2024-11-20 18:00:00'].
     * @param array $predicado Condiciones para identificar el registro a actualizar,
     *                         donde cada clave es el nombre del campo y cada valor es el criterio de búsqueda.
     *                         Ejemplo: ['codv_id' => 123].
     *
     * @return void
     */
    public function actualizarCodigo(
        array  $datos,
        array  $predicado
    ) {
        BDT_CodigoVerificacion::Update($datos, $predicado, BD_ADMIN);
    }

}