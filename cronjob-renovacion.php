<?php
include("conexion-datos.php");

//DATOS DE FECHA ACTUAL
date_default_timezone_set("America/New_York");
$fechaActual=date("Y/m/d");  
$valoresSegunda = explode ("/", $fechaActual); 
$diaSegunda   = $valoresSegunda[2];  
$mesSegunda = $valoresSegunda[1];  
$anyoSegunda  = $valoresSegunda[0];
$diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda); 

//CONSULTA INSTITUCIONES ACTIVAS
$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
$institucionConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM instituciones WHERE ins_estado=1");

//CICLO PARA EJECUTAR NOTIFICACION
while($datosInstituciones=mysqli_fetch_array($institucionConsulta, MYSQLI_BOTH)){

  //DATOS FECHA DE RENOVACION
  $fechaR=$datosInstituciones['ins_fecha_renovacion'];
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

  //VALIDAMOS DIAS PARA NOTIFICAR POR CORREO
  if($dfDias==90 || $dfDias==30 || $dfDias==5 || $dfDias==1){

    //CANTIDAD EN MESES
    $falta="";
    if($dfDias==90){$falta="3 meses";}
    if($dfDias==30){$falta="1 mes";}
    if($dfDias==5){$falta="5 dias";}
    if($dfDias==1){$falta="1 dia";}

    //CONSULTAMOS DIRECTIVOS ACTIVOS DE LA INSTITUCION
    $conexionUsuarios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $datosInstituciones['ins_bd']."_".date("Y"));
    $directivosConsulta = mysqli_query($conexionUsuarios, "SELECT * FROM usuarios WHERE uss_tipo=5 AND uss_estado=1");

    //CICLO PARA ENVIAR CORREO A DIRECTIVOS
    while($datosDirectivos=mysqli_fetch_array($directivosConsulta, MYSQLI_BOTH)){

      //INICIO ENVÍO DE MENSAJE
      $tituloMsj = "¡Faltan ".$falta." para vencer su licencia!";
      $bgTitulo = "#4086f4";
      $contenidoMsj = '
      Hola! <b>'.$datosDirectivos['uss_nombre'].'</b><br>
      <b>'.strtoupper($datosInstituciones['ins_nombre']).'</b>, su licencia con la plataforma SINTIA esta por vencer<br>
      faltan <b>'.$falta.'</b> para su vencimiento<br>
      puede hacer la renovacion atraves de la plataforma.';
        
      include("config-general/plantilla-email-1.php");
      // Instantiation and passing `true` enables exceptions
      $mail = new PHPMailer(true);
      echo '<div style="display:block;">';
        try {
          include("config-general/mail.php");

          $mail->addAddress(strtolower($datosDirectivos['uss_email']), $datosDirectivos['uss_nombre']);    
          $mail->addAddress('tecmejia2010@gmail.com', 'Plataforma SINTIA');

          // Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = 'NOTIFICACIÓN DE VENCIMIENTO DE LICENCIA';
          $mail->Body = $fin;
          $mail->CharSet = 'UTF-8';

          $mail->send();
          echo 'Mensaje enviado correctamente.';
        } catch (Exception $e) {echo "Error: {$mail->ErrorInfo}"; exit();}
      echo '</div>';
      //FIN ENVÍO DE MENSAJE
    }
  }
}