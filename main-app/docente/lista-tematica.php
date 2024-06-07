<?php

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Indirect;

include("session.php");
$idPaginaInterna = 'DC0046';
include("../compartido/historial-acciones-guardar.php");
include("verificar-carga.php");
include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

$disabled = '';
if( !CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) { 
    $disabled = 'disabled';
}

$consultaTematica = Indicadores::consultaTematica($cargaConsultaActual, $periodoConsultaActual);
$tematica = mysqli_fetch_array($consultaTematica, MYSQLI_BOTH);
?>
</head>

<div class="panel">
    <header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
    <div class="panel-body">

    
    <form name="formularioGuardar" action="tematica-guardar.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" method="post">

        <div class="form-group row">
            <label class="col-sm-2 control-label">Descripción</label>
            <div class="col-sm-10">
                <textarea name="contenido" class="form-control" rows="5" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required <?=$disabled;?>><?=!empty($tematica['ind_nombre'])?$tematica['ind_nombre']:"";?></textarea>
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-sm-2 control-label">Fecha creación</label>
            <div class="col-sm-4">
                <input type="text" name="fecha" class="form-control" value="<?=!empty($tematica['ind_fecha_creacion'])?$tematica['ind_fecha_creacion']:"";?>" readonly>
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-sm-2 control-label">Última edición</label>
            <div class="col-sm-4">
                <input type="text" name="fecha" class="form-control" value="<?=!empty($tematica['ind_fecha_modificacion'])?$tematica['ind_fecha_modificacion']:"";?>" readonly>
            </div>
        </div>

        <input type="submit" class="btn btn-primary" value="Guardar cambios" <?=$disabled;?>>&nbsp;
    </form>
</div>

<?php include("../compartido/guardar-historial-acciones.php");?>