<?php
$consultaSpcd=mysqli_query($conexion, "SELECT sum(act_valor) FROM academico_actividades WHERE act_id_carga='".$cargaSP."' AND act_estado=1 AND act_periodo='".$periodoSP."'");
$spcd = mysqli_fetch_array($consultaSpcd, MYSQLI_BOTH);
$consultaSpcr=mysqli_query($conexion, "SELECT sum(act_valor) FROM academico_actividades WHERE act_id_carga='".$cargaSP."' AND act_estado=1 AND act_periodo='".$periodoSP."' AND act_registrada=1");
$spcr = mysqli_fetch_array($consultaSpcr, MYSQLI_BOTH);
?>