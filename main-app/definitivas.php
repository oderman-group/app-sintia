<?php
//CALCULO DEFINITIVA

$consultaD = mysqli_query($conexion, "SELECT * FROM academico_actividades 
WHERE act_id_carga='".$carga."' AND act_registrada=1 AND act_estado=1 AND act_periodo='".$periodo."'");



$numConsultaD = mysqli_num_rows($consultaD);

	$acumulaValor = 0;

	$sumaNota = 0;

	$definitiva = 0;

	$porNuevo = 0;

	$notaMultiplicada = 0;
	$nn=0;

	while($resultadoD = mysqli_fetch_array($consultaD, MYSQLI_BOTH)){

		$nota = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_calificaciones WHERE cal_id_actividad='".$resultadoD[0]."' AND cal_id_estudiante='".$estudiante."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}"), MYSQLI_BOTH);

		

		if(isset($nota['cal_nota'])&&$nota['cal_nota']!=""){

			$porNuevo = ($resultadoD[3] / 100);

			$acumulaValor = ($acumulaValor + $porNuevo);

			$notaMultiplicada = ($nota['cal_nota'] * $porNuevo);

			$sumaNota = ($sumaNota + $notaMultiplicada);

		}

	}

	if($acumulaValor>0){// SI EL VALOR ACUMULADO DE LOS PORCENTAJES ES MAYOR QUE 0

		$definitiva = round(($sumaNota / $acumulaValor),$config['conf_decimales_notas']);//NOTA DEFINITIVA

		$nn++;

	}	

	$porcentajeActual = ($acumulaValor * 100);

	//REGLA DE 3 PARA LA BARRA DE PROGRESO

	@$progreso = ($definitiva * 100);

	@$progreso = ($progreso / $config['conf_nota_hasta']);

	//COLOR DE LA BARRA Y DE LAS DEFINITIVAS

	if($definitiva< $config['conf_nota_minima_aprobar']){

		$colorDefinitiva =  $config['conf_color_perdida'];

		$colorProgreso = "danger";

	}	

	if($definitiva== $config['conf_nota_minima_aprobar']){	

		$colorDefinitiva =  $config['conf_color_ganada'];

		$colorProgreso = "warning";

	}	

	if($definitiva> $config['conf_nota_minima_aprobar']){	

		$colorDefinitiva =  $config['conf_color_ganada'];

		$colorProgreso = "striped";

	}	

	//NUMEROS ENTEROS DE DEFINITIVAS AGREGARLES EL .0 AL FINAL 4 = 4.0

	for($i= $config['conf_nota_desde']; $i<= $config['conf_nota_hasta']; $i++){

		if($definitiva==$i)

		$definitiva = $definitiva.".0";

	}

?>