<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");

$idPaginaInterna = 'DV0067';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

date_default_timezone_set("America/New_York");//Zona horaria

//Variables necesarias
$nueva = $_POST['tipoInsti']; //VALORES: 1 SI ES NUEVA Y 0 SI ES ANTIGUA

//FECHAS NECESARIAS PARA LOS DATOS
$fecha=date("Y-m-d");
$fechaCompleta = date("Y-m-d H:i:s");

if($nueva==0){//PARA ANTIGUAS
    //DATOS BASICOS DE LA INSTITUCIÓN
    $idInsti = $_POST['idInsti'];//LE MODIFICAMOS EL VALOR SOLO CUANDO LA INSTITUCION ES ANTIGUA
    
    try{
        $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".instituciones 
        WHERE ins_id = ".$idInsti." AND ins_enviroment='".ENVIROMENT."'");
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $datosInsti = mysqli_fetch_array($consulta, MYSQLI_BOTH);
    
    $siglasBD = $datosInsti['ins_bd'];//AQUI COLOCAMOS LAS SIGLAS QUE VAN AL INTERMEDIO DEL NOMBRE DE LA BD EJE: monbiliar_{[$siglasBD]}_{[$year]}

    $year = $_POST['yearA'];//AQUI COLOCAMOS EL AÑO QUE VAN AL FINAL DEL NOMBRE DE LA BD EJE: monbiliar_{[$siglasBD]}_{[$year]}
    $yearAnterior=($year-1);//CALCULAMOS AÑO ANTERIOR PARA CUANDO SE CONSULTA LOS DATOS DEL AÑO ANTERIOR DE LA INSTITUCIÓN ANTIGUA

    $bd=$siglasBD.'_'.$year;//BD NUEVA
    $bdAnterior=$siglasBD.'_'.$yearAnterior;//BD ANTIGUA PARA EL TRASPASO DE DATOS

    $bdInstitucion=$siglasBD;//SE USARA PARA VALIDAR EXISTENCIA DE LA INSTITUCIÓN
}

if($nueva==1){//PARA NUEVAS
    //DATOS BASICOS DE LA INSTITUCIÓN
    $siglasBD = $_POST['siglasBD'];//AQUI COLOCAMOS LAS SIGLAS QUE VAN AL INTERMEDIO DEL NOMBRE DE LA BD EJE: monbiliar_{[$siglasBD]}_{[$year]}
    $nombreInsti = $_POST['nombreInsti'];//NOMBRE PARA LA NUEVA INSTITUCIÓN
    $siglasInst = $_POST['siglasInst'];//SIGLAS PARA LA NUEVA INSTITUCIÓN

    $year = $_POST['yearN'];//AQUI COLOCAMOS EL AÑO QUE VAN AL FINAL DEL NOMBRE DE LA BD EJE: monbiliar_{[$siglasBD]}_{[$year]}

    $bd=BD_PREFIX.$siglasBD.'_'.$year;//BD NUEVA

    $bdInstitucion=BD_PREFIX.$siglasBD;//SE USARA PARA VALIDAR EXISTENCIA DE LA INSTITUCIÓN
}

try {

    if(empty($_POST["continue"]) || $_POST["continue"]!=1 || empty($_SERVER['HTTP_REFERER'])){

        include("dev-crear-bd-informacion.php");
        exit();

    }
} catch (Exception $e) {
	echo $e->getMessage();
}

include('ingresar-datos-bd.php');

echo '<script type="text/javascript">window.location.href="dev-instituciones-editar.php?id='.base64_encode($idInsti).'&success=SC_DT_10";</script>';
