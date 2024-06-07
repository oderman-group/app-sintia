<?php 
include("session-compartida.php");
$idPaginaInterna = 'DT0250';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");


if (empty($_REQUEST["periodo"])) {
    $periodoActual = 1;
} else {
    $periodoActual = base64_decode($_REQUEST["periodo"]);
}

if ($periodoActual == 1) { $periodoActuales = "Primero"; $condicion = "1"; $condicion2 = "1";}
if ($periodoActual == 2) { $periodoActuales = "Segundo"; $condicion = "1,2"; $condicion2 = "2";}
if ($periodoActual == 3) { $periodoActuales = "Tercero"; $condicion = "1,2,3"; $condicion2 = "3";}
if ($periodoActual == 4) { $periodoActuales = "Cuarto"; $condicion = "1,2,3,4"; $condicion2 = "4";}
?>
    <a href="indicadores-perdidos-curso.php?curso=<?php if(!empty($_REQUEST["curso"])) echo $_REQUEST["curso"];?>&periodo=<?=base64_encode(1)?>">Periodo 1</a>&nbsp;&nbsp;
    <a href="indicadores-perdidos-curso.php?curso=<?php if(!empty($_REQUEST["curso"])) echo $_REQUEST["curso"];?>&periodo=<?=base64_encode(2)?>">Periodo 2</a>&nbsp;&nbsp;
    <a href="indicadores-perdidos-curso.php?curso=<?php if(!empty($_REQUEST["curso"])) echo $_REQUEST["curso"];?>&periodo=<?=base64_encode(3)?>">Periodo 3</a>&nbsp;&nbsp;
    <a href="indicadores-perdidos-curso.php?curso=<?php if(!empty($_REQUEST["curso"])) echo $_REQUEST["curso"];?>&periodo=<?=base64_encode(4)?>">Periodo 4</a>&nbsp;&nbsp;
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<?php

$filtroAdicional = " AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
if (!empty($_REQUEST["id"])) {
    $filtroAdicional .= " AND mat_id='" . base64_decode($_REQUEST["id"]) . "'";
}
if (!empty($_REQUEST["curso"])) {
    $filtroAdicional .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
}

$cursoActual=GradoServicios::consultarCurso(base64_decode($_REQUEST["curso"]));
$matriculadosPorCurso =Estudiantes::listarEstudiantes(0,$filtroAdicional,"",$cursoActual);
while ($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)) {

    //contador materias
    $cont_periodos = 0;
    $contador_indicadores = 0;
    $materiasPerdidas = 0;

    $idCurso=$matriculadosDatos["mat_grado"];
    $idGrupo=$matriculadosDatos["mat_grupo"];
    if($cursoActual["gra_tipo"]==GRADO_INDIVIDUAL){
        $idCurso=$matriculadosDatos["matcur_id_curso"];
        $idGrupo=$matriculadosDatos["matcur_id_grupo"];
    }
    $contador_periodos = 0;
    ?>

    <!doctype html>
    <html class="no-js" lang="en">
    <head>
        <meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
        <title>Indicadores perdidos</title>
        <style>
            #saltoPagina {
                PAGE-BREAK-AFTER: always;
            }
        </style>
    </head>
    <body style="font-family:Arial;">
        <?php
        //CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
        $consulta_mat_area_est = CargaAcademica::consultaIndicadoresPerdidos($config, $matriculadosDatos['mat_id'], $condicion2, $idCurso, $idGrupo, $condicion);
        
        $numdatos = mysqli_num_rows($consulta_mat_area_est);
        if ($numdatos > 0) {
        ?>
        <div align="center" style="margin-bottom:20px;">
        </div>

        <table width="100%" cellspacing="5" cellpadding="5" border="0" rules="none">
            <tr>
                <td>Documento:<br> <?=strpos($matriculadosDatos["mat_documento"], '.') !== true && is_numeric($matriculadosDatos["mat_documento"]) ? number_format($matriculadosDatos["mat_documento"],0,",",".") : $matriculadosDatos["mat_documento"];?></td>
                <td>Nombre:<br> <?= Estudiantes::NombreCompletoDelEstudiante($matriculadosDatos); ?></td>
                <td>Grado:<br> <?= $cursoActual["gra_nombre"] . " " . $matriculadosDatos["gru_nombre"]; ?></td>
                <td>Periodo:<br> <b><?= $periodoActuales . " (" . date("Y") . ")"; ?></b></td>
            </tr>
        </table>

        <p>&nbsp;</p>
        <table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
            <tr style="font-weight:bold; background-color:#4c9858; border-color:#000; height:40px; color:#000; font-size:12px;">
                <td width="2%" align="center">NO</td>
                <td width="20%" align="center">ASIGNATURAS</td>
                <td width="2%" align="center">NOTA</td>
            </tr>

            <!-- Aca ira un while con los indiracores, dentro de los cuales debera ir otro while con las notas de los indicadores-->
            <?php
            $contador = 1;
            $idMatAnterior = "";
            while ($fila = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {

                $leyendaRI = '';
                if(!empty($fila['rind_nota']) && $fila['rind_nota']>$fila["nota"]){
                    $nota_indicador = $fila['rind_nota'];
                    $leyendaRI = '<br><span style="color:navy; font-size:9px;">Recuperado.</span>';
                }else{
                    $nota_indicador = $fila["nota"];
                }

                if ($nota_indicador == 1)    $nota_indicador = "1.0";
                if ($nota_indicador == 2)    $nota_indicador = "2.0";
                if ($nota_indicador == 3)    $nota_indicador = "3.0";
                if ($nota_indicador == 4)    $nota_indicador = "4.0";
                if ($nota_indicador == 5)    $nota_indicador = "5.0";

                if ($idMatAnterior != $fila["mat_id"]) {
                    $idMatAnterior = $fila["mat_id"];
                $contador_periodos = 0;
            ?>
                <tr bgcolor="#EAEAEA" style="font-size:12px;">
                    <td align="center"><?= $contador; ?></td>
                    <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;"><?=$fila["mat_nombre"]; ?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php
                    }
                ?>
                    <tr bgcolor="#FFF" style="font-size:12px;">
                        <td align="center">&nbsp;</td>
                        <td style="font-size:12px; height:15px;"><?=$fila["ind_nombre"]; ?></td>
                        <td align="center" style="font-weight:bold; font-size:12px;"><?= $nota_indicador." ".$leyendaRI; ?></td>
                    </tr>
                <?php
                $contador++;
            } //while fin areas
            ?>
        </table>
        <div id="saltoPagina"></div>
    <?php
        }
} // FIN DE TODOS LOS MATRICULADOS
    ?>
    <script type="application/javascript">
        print();
    </script>
    </body>
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
    </html>