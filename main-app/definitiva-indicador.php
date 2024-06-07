<?php
require_once(ROOT_PATH."/main-app/class/Actividades.php");
//CALCULO DEFINITIVA
$consultaD = Actividades::consultaActividadesCargaIndicador($config, $indicador, $carga, $periodo);
$acumulaValor = "";
$sumaNota = "";
$definitiva = "";
while($resultadoD = mysqli_fetch_array($consultaD, MYSQLI_BOTH)){
	$consultaNotas=mysqli_query($conexion,"SELECT * FROM ".BD_ACADEMICA.".academico_calificaciones WHERE cal_id_actividad='".$resultadoD['act_id']."' AND cal_id_estudiante='".$estudiante."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	$nota = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH);
	$porNuevo = ($resultadoD['act_valor'] / 100);
	$acumulaValor = ($acumulaValor + $porNuevo);
	$notaMultiplicada = ($nota['cal_nota'] * $porNuevo);
	$sumaNota = ($sumaNota + $notaMultiplicada);
}
if($acumulaValor>0)// SI EL VALOR ACUMULADO DE LOS PORCENTAJES ES MAYOR QUE 0
	$definitiva = round(($sumaNota / $acumulaValor),1);//NOTA DEFINITIVA
$porcentajeActual = ($acumulaValor * 100);
//REGLA DE 3 PARA LA BARRA DE PROGRESO
$progreso = ($definitiva * 100);
$progreso = ($progreso / $config[4]);
//COLOR DE LA BARRA Y DE LAS DEFINITIVAS
if($definitiva<$config[5]){
	$colorDefinitiva = $config[6];
	$colorProgreso = "danger";
}	
if($definitiva==$config[5]){	
	$colorDefinitiva = $config[7];
	$colorProgreso = "warning";
}	
if($definitiva>$config[5]){	
	$colorDefinitiva = $config[7];
	$colorProgreso = "striped";
}	
//NUMEROS ENTEROS DE DEFINITIVAS AGREGARLES EL .0 AL FINAL 4 = 4.0
for($i=$config[3]; $i<=$config[4]; $i++){
	if($definitiva==$i)
	$definitiva = $definitiva.".0";
}
?>