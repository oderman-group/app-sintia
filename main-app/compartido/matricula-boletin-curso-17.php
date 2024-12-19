<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
    exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");

$year =isset($_GET["year"])?base64_decode($_GET["year"]):$_SESSION["bd"];;

$periodoActual = empty($_GET["periodo"])?1:base64_decode($_GET["periodo"]);

$grado =!empty($_GET["curso"])? base64_decode($_GET["curso"]):1;

$grupo =!empty($_GET["grupo"])? base64_decode($_GET["grupo"]):1;
    
$periodo = !empty($_GET["periodo"])?base64_decode($_GET["periodo"]):1;

$idEstudiante = '';
if (!empty($_GET["id"])) {

    $filtro = " AND mat_id='" . base64_decode($_GET["id"]) . "'";
    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    $estudiante = $matriculadosPorCurso->fetch_assoc();
    Utilidades::validarInfoBoletin( $matriculadosPorCurso);
    if (!empty($estudiante)) {
        $idEstudiante = $estudiante["mat_id"];
        $grado        = $estudiante["mat_grado"];
        $grupo        = $estudiante["mat_grupo"];
    }
    
}


$tamañoLogo = $_SESSION['idInstitucion'] == ICOLVEN ? 100 : 50;



switch ($periodoActual) {
    case 1:
        $periodoActuales = "Primero";
        break;
    case 2:
        $periodoActuales = "Segundo";
        break;
    case 3:
        $periodoActuales = "Tercero";
        break;
    case 4:
        $periodoActuales = "Final";
        break;
}

?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>



<!doctype html>

<html class="no-js" lang="en">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

<head>

    <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">

    <title>Boletín</title>

    <style>
        #saltoPagina {

            PAGE-BREAK-AFTER: always;

        }
    </style>

</head>
<?php
$listaDatos = [];
$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);

while ($row = $cosnultaTiposNotas->fetch_assoc()) {
    $tiposNotas[] = $row;
}

if (!empty($grado) && !empty($grupo) && !empty($periodo) && !empty($year)) {
    $periodos = [];
	for ($i = $periodoActual; $i <= $periodoActual; $i++) {
		$periodos[$i] = $i;
	}
    $datos = Boletin::datosBoletin($grado, $grupo, $periodos, $year, $idEstudiante,true);
    Utilidades::validarInfoBoletin($datos);
    while ($row = $datos->fetch_assoc()) {
        $listaDatos[] = $row;
    }
}

$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
?>


<?php
$conteoEstudiante = 0;
$contarIndicadores = 0;
$contarCargas = 0;
$mat_id = "";
$mat_car = "";
$mat_car_ind = "";
$directorGrupo = "";
$observacionesConvivencia = [];
$estudiantes = [];
$indicadores = [];
$cargas = [];
foreach ($listaDatos  as $index => $registro) {

    if (!empty($registro["dn_id"]) && !empty($registro["dn_observacion"])) {

        $observacionesConvivencia[$registro["mat_id"]][$registro["dn_periodo"]] = [
            "id" => $registro["dn_id"],
            "estudiante" => $registro["dn_cod_estudiante"],
            "observacion" => $registro["dn_observacion"],
            "periodo" => $registro["dn_periodo"]
        ];
    }

    if ($mat_id != $registro["mat_id"]) {
        $contarCargas = 0;
        $conteoEstudiante++;
        $nombre = Estudiantes::NombreCompletoDelEstudiante($registro);

        $estudiantes[$registro["mat_id"]] = [
            "mat_id"          => $registro["mat_id"],
            "nombre"          => $nombre,
            "documento"       => $registro["mat_documento"],
            "nro"             => $conteoEstudiante,
            "mat_matricula"   => $registro["mat_matricula"],
            "gra_nombre"      => $registro["gra_nombre"],
            "gru_nombre"      => $registro["gru_nombre"],
        ];

        $mat_id = $registro["mat_id"];
    }

    if ($mat_car != $registro["mat_id"] . '-' . $registro["car_id"]) {
        $contarIndicadores = 0;
        $contarCargas++;

        if ($registro["car_director_grupo"] == 1) {
            $directorGrupo = $registro;
        }

        $cargas[$registro["mat_id"]][$registro["car_id"]] = [
            "car_id"                    => $registro['car_id'],
            "nro"                       => $contarCargas,
            "mat_nombre"                => $registro['mat_nombre'],
            "docente"                   => $registro,
            "bol_nota"                  => $registro['bol_nota'],
            "bol_tipo"                  => $registro['bol_tipo'],
            "bol_observaciones_boletin" => $registro['bol_observaciones_boletin'],
            "car_ih"                    => $registro['car_ih'],
        ];

        $mat_car = $registro["mat_id"] . '-' . $registro["car_id"];
    }

    if ($mat_car_ind != $registro["mat_id"] . '-' . $registro["car_id"] . '-' . $registro["ind_id"]) {
        $contarIndicadores++;

        $indicadores[$registro["mat_id"]][$registro["car_id"]][] = [
            "ind_id"          => $registro["ind_id"],
            "car_id"          => $registro['car_id'],
            "nro"             => $contarIndicadores,
            "valor_indicador" => $registro['valor_indicador'],
            "ind_nombre"      => $registro['ind_nombre']
        ];
        
        $mat_car_ind = $registro["mat_id"] . '-' . $registro["car_id"] . '-' . $registro["ind_id"];
    }

}

$observacionesConvivencia;
$estudiantes;
$indicadores;
$cargas;
?>

<body style="font-family:Arial;"></body>
<?php foreach ($estudiantes  as  $estudiante) { 
     $fallasAcumuladas = 0;
    ?>
    <div align="center" style="margin-bottom:20px;">
        <img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="<?= $tamañoLogo ?>%"><br>
    </div>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:12px;">
        <tr>
            <td>C&oacute;digo: <b><?= $estudiante["mat_matricula"]; ?></b></td>
            <td>Nombre: <b><?=$estudiante["nombre"] ?></b></td>
        </tr>
        <tr>
            <td>Grado: <b><?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></b></td>
            <td>Periodo: <b><?= strtoupper($periodoActuales); ?></b></td>
        </tr>
    </table>
    <br>
    <table width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
        <tr style="font-weight:bold; background:#4c9858; border-color:#000; height:20px; color:#000; font-size:12px;">
            <td width="1%" align="center">No.</td>
            <td width="92%" align="center">DIMENSIONES</td>
            <td width="2%" align="center">I.H</td>
        </tr>
        <?php foreach ($cargas[$estudiante["mat_id"]]  as  $carga) {
            $colorFondo = '#FFF;';
            if ($carga['nro'] % 2 == 0) {
                $colorFondo = '#e0e0153b';
            }
        ?>
            <tr style="background-color: <?= $colorFondo; ?>">
                <td width="1%" align="center"><?= $carga['nro']; ?></td>
                <td width="92%">
                    <b style="padding-left: 10px;"><?= $carga['mat_nombre']; ?></b><br>
                    <?php foreach ($indicadores[$estudiante["mat_id"]][$carga['car_id']]  as  $indicador){
                        echo "- ".$indicador['ind_nombre']."<br>";
                      }
                        ?>
                    <?php if (!empty($carga['bol_observaciones_boletin'])) { ?>
                        <hr>
                        <h5 align="center">Observaciones</h5>
                        <p style="margin-left: 20px;">
                            <?= $carga['bol_observaciones_boletin'] ?? ""; ?>
                        </p>
                    <?php  } ?>
                </td>
                <td width="2%" align="center"><?= $carga['car_ih']; ?></td>
            </tr>
        <?php  }  ?>
    </table>
    <p>&nbsp;</p>
    <?php if (!empty($observacionesConvivencia[$estudiante["mat_id"]])) {
        usort($observacionesConvivencia[$estudiante["mat_id"]], function ($a, $b) {
            return $a['periodo'] - $b['periodo']; // Orden ascendente por 'periodo'
        });
    ?>
        <table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
            <tr style="font-weight:bold; background:#4c9858; border-color:#036; height:40px; font-size:12px; text-align:center">
                <td colspan="3">OBSERVACIONES DE CONVIVENCIA</td>
            </tr>
            <tr style="font-weight:bold; background:#e0e0153b; height:25px; font-size:12px; text-align:center">
                <td width="8%">Periodo</td>
                <td>Observaciones</td>
            </tr>
            <?php
            foreach ($observacionesConvivencia[$estudiante["mat_id"]] as $observacion) {
                if ($observacion["estudiante"] == $estudiante["mat_id"]) {
            ?>
                    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">
                        <td><?= $observacion["periodo"] ?></td>
                        <td align="left"><?= $observacion["observacion"] ?></td>
                    </tr>

            <?php  }
            } ?>
        </table>
    <?php } ?>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <?php include("../compartido/firmas-informes.php") ?>
    <?php include("../compartido/footer-informes.php") ?>
    <div id="saltoPagina"></div>

<?php  }  ?>
</body>

<script type="application/javascript">
    print();
</script>

</html>