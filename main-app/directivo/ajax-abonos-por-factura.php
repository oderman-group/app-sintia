<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0104';

header('Content-Type: application/json; charset=utf-8');

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo json_encode([
        'success' => false,
        'message' => 'Acceso no autorizado.'
    ]);
    exit();
}

require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

$idFactura = $_GET['idFactura'] ?? '';

if (empty($idFactura)) {
    echo json_encode([
        'success' => false,
        'message' => 'Factura no especificada.'
    ]);
    exit();
}

try {
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT 
                p.id,
                p.registration_date,
                p.cod_payment,
                p.payment_method,
                p.observation,
                p.note,
                p.responsible_user,
                p.invoiced,
                pi.payment,
                pi.payments,
                u.uss_nombre,
                u.uss_nombre2,
                u.uss_apellido1,
                u.uss_apellido2
            FROM ".BD_FINANCIERA.".payments_invoiced pi
            INNER JOIN ".BD_FINANCIERA.".payments p 
                ON p.cod_payment = pi.payments
                AND p.institucion = :institucion
                AND p.year = :year
                AND p.is_deleted = 0
            LEFT JOIN ".BD_GENERAL.".usuarios u
                ON u.uss_id = p.responsible_user
                AND u.institucion = :institucion
                AND u.year = :year
            WHERE pi.invoiced = :factura
              AND pi.institucion = :institucion
              AND pi.year = :year
            ORDER BY p.registration_date DESC";

    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindValue(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindValue(':year', $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->bindValue(':factura', $idFactura, PDO::PARAM_STR);
    $stmt->execute();

    $abonos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nombreResponsable = UsuariosPadre::nombreCompletoDelUsuario([
            'uss_nombre'    => $row['uss_nombre'] ?? '',
            'uss_nombre2'   => $row['uss_nombre2'] ?? '',
            'uss_apellido1' => $row['uss_apellido1'] ?? '',
            'uss_apellido2' => $row['uss_apellido2'] ?? '',
        ]);

        $abonos[] = [
            'id' => $row['id'],
            'registration_date' => $row['registration_date'],
            'cod_payment' => $row['cod_payment'],
            'payment_method' => $row['payment_method'],
            'observation' => $row['observation'],
            'note' => $row['note'],
            'responsible_user' => $row['responsible_user'],
            'responsible_name' => $nombreResponsable,
            'payment' => $row['payment'],
            'payments_code' => $row['payments']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $abonos
    ]);
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los abonos.'
    ]);
}

