<?php
//DATOS DE FECHA ACTUAL
date_default_timezone_set("America/New_York");
$fechaActual=date("Y/m/d");  
$valoresSegunda = explode ("/", $fechaActual); 
$diaSegunda   = $valoresSegunda[2];  
$mesSegunda = $valoresSegunda[1];  
$anyoSegunda  = $valoresSegunda[0];
$diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);

//DATOS FECHA DE RENOVACION
$fechaR=$datosUnicosInstitucion['ins_fecha_renovacion'];
$fechaRenovacion=date("Y/m/d", strtotime($fechaR));
$valoresPrimera = explode ("/", $fechaRenovacion);
$diaPrimera    = $valoresPrimera[2];  
$mesPrimera  = $valoresPrimera[1];  
$anyoPrimera   = ($valoresPrimera[0]+1);
$diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);

//VALIDA FECHAS
if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera) || !checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){
  echo 'Fecha invalida.';
  exit();
}

//OPERACION PARA SABER DIAS FALTANTES
$dfDias=$diasPrimeraJuliano - $diasSegundaJuliano;