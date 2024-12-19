<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH . "/main-app/class/Notificacion.php");

$notificacion = new Notificacion();

try {
    $predicado = [
        'codv_id' => $_REQUEST["idRegistro"]
    ];

    $campos = "codv_fecha_registro, codv_fecha_uso, codv_codigo_verificacion, codv_activo";

    $consulta = $notificacion->traerCodigoValido($predicado, $campos);
    $datosCodigo = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($datosCodigo['codv_activo'] == 1) {

        // Validar que no hayan pasado más de 10 minutos desde la fecha de registro
        $fechaRegistro = new DateTime($datosCodigo['codv_fecha_registro']);
        $fechaActual = new DateTime();
        $diferenciaMinutos = $fechaActual->getTimestamp() - $fechaRegistro->getTimestamp();

        if ($diferenciaMinutos < 600) { // 10 minutos = 600 segundos

            if (empty($datosCodigo['codv_fecha_uso'])) {

                if ($datosCodigo['codv_codigo_verificacion'] == $_REQUEST["code"]) {

                    $datos = [
                        'codv_fecha_uso'    => $fechaActual->format('Y-m-d H:i:s'),
                        'codv_activo'       => 0
                    ];

                    $predicado = [
                        'codv_id' => $_REQUEST["idRegistro"]
                    ];

                    $notificacion->actualizarCodigo($datos, $predicado);

                    $arrayIdInsercion = [
                        "success" => true,
                        "message" => "El proceso de activación a sido exitoso, da click en finalizar."
                    ];
                } else {
                    $arrayIdInsercion = [
                        "success" => false,
                        "message" => "El código es incorrecto, por favor valida o inténtalo de nuevo."
                    ];
                }
            } else {

                $datos = [
                    'codv_activo'       => 0
                ];

                $predicado = [
                    'codv_id' => $_REQUEST["idRegistro"]
                ];

                $notificacion->actualizarCodigo($datos, $predicado);

                $arrayIdInsercion = [
                    "success" => false,
                    "message" => "El código de verificación ya ha sido utilizado, por favor inténtalo de nuevo."
                ];
            }
        } else {

            $datos = [
                'codv_activo'       => 0
            ];

            $predicado = [
                'codv_id' => $_REQUEST["idRegistro"]
            ];

            $notificacion->actualizarCodigo($datos, $predicado);

            $arrayIdInsercion = [
                "success" => false,
                "message" => "El código de verificación ha expirado (más de 10 minutos desde su generación), por favor inténtalo de nuevo."
            ];
        }
    } else {
        $arrayIdInsercion = [
            "success" => false,
            "message" => "El código de verificación está inactivo, por favor inténtalo de nuevo."
        ];
    }
} catch (Exception $e) {
    $arrayIdInsercion = [
        "success" => false,
        "message" => "Error al validar el código: " . $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($arrayIdInsercion);