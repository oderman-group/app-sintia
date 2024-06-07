<?php
session_start();
include("../../conexion.php");
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>Formulario de Respuesta</title>
  <!-- Bootstrap -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>
  <header id="main-header" style="margin-top:20px">
    <div class="row">
      <div class="col-lg-12 franja">
        <img class="center-block" src="<?= REDIRECT_ROUTE ?>/sintia-logo-2023.png" width="20%">
      </div>
    </div>
  </header>
  <?php
  $service_url = 'https://secure.epayco.co/validation/v1/reference/' . $_REQUEST['ref_payco'];
  $jsonObject = json_decode(file_get_contents($service_url), true);
  $estado = $jsonObject['data']['x_response'];
  $correo = $jsonObject['data']['x_extra2'];
  $descripcion = $jsonObject['data']['x_description'];
  $celular = $jsonObject['data']['x_extra5'];
  $urlOrigen = $jsonObject['data']['x_extra11'];
  $usuario = $jsonObject['data']['x_extra12'];
  $identificacion = $jsonObject['data']['x_extra3'];
  $institucion = $jsonObject['data']['x_extra6'];
  $year = $jsonObject['data']['x_extra13'];
  $matricula = $jsonObject['data']['x_extra14'];
  $curso = $jsonObject['data']['x_extra15'];
  $hidden = "";
  if (empty($urlOrigen)) {
    $hidden = "hidden";
  } else {
    if ($estado == TRANSACCION_ACEPTADA) {
      if (!empty($jsonObject['data']['x_extra15'])) {
        require_once("../class/servicios/MediaTecnicaServicios.php");
        //Insertamos la matrícula en media tecnica
        try {
          MediaTecnicaServicios::editarporCurso($matricula, $curso, 1, ESTADO_CURSO_PRE_INSCRITO, $institucion, $year);
        } catch (Exception $e) {
          include("../compartido/error-catch-to-report.php");
        }
      }

      if (!empty($jsonObject['data']['x_extra16'])) {
        require_once(ROOT_PATH."/main-app/class/Modulos.php");

        mysqli_query($conexion, "INSERT INTO " . BD_ADMIN . ".instituciones_paquetes_extras(paqext_institucion, paqext_id_paquete, paqext_fecha, paqext_tipo) VALUES ('" . $jsonObject['data']['x_extra6'] . "', '" . $jsonObject['data']['x_extra16'] . "', now(), '".MODULOS."')");

        if($jsonObject['data']['x_extra16'] == MODULO_ADMISIONES) {
          require_once(ROOT_PATH."/main-app/class/Plataforma.php");
          
          $Plataforma = new Plataforma;
    
          try{
            $colorBG = $Plataforma->colorUno;
            $yearInscription = date('Y')+1;
    
            $sql = "INSERT INTO " . BD_ADMISIONES . ".config_instituciones(cfgi_id_institucion,
            cfgi_year,
            cfgi_color_barra_superior,
            cfgi_inscripciones_activas,
            cfgi_politicas_texto,
            cfgi_color_texto,
            cfgi_mostrar_banner,
            cfgi_year_inscripcion) VALUES (?, ?, ?, '0', 'Loremp ipsum...', 'white', '0', ?) 
            ON DUPLICATE KEY UPDATE cfgi_year_inscripcion = VALUES(cfgi_year_inscripcion)";
    
            $stmt = mysqli_prepare($conexion, $sql);
    
            if (!$stmt) {
              die("Error al preparar la consulta.");
            }
    
            // Vincular los parámetros
            mysqli_stmt_bind_param($stmt, "iisi", $jsonObject['data']['x_extra6'], date('Y'), $colorBG, $yearInscription);
    
            // Ejecutar la consulta
            $resultado = mysqli_stmt_execute($stmt);
    
            if (!$resultado) {
              die("Error al ejecutar la consulta.");
            }
    
            } catch (Exception $e) {
              include("../compartido/error-catch-to-report.php");
            }
          
        }
    
        $arregloModulos = Modulos::consultarModulosIntitucion($conexion, $jsonObject['data']['x_extra6']);
        $_SESSION["modulos"] = $arregloModulos;
      }

      if (!empty($jsonObject['data']['x_extra17']) && !empty($jsonObject['data']['x_extra18'])) {
          
        mysqli_query($conexion, "INSERT INTO " . BD_ADMIN . ".instituciones_paquetes_extras(paqext_institucion, paqext_id_paquete, paqext_fecha, paqext_tipo) VALUES ('" . $jsonObject['data']['x_extra6'] . "', '" . $jsonObject['data']['x_extra17'] . "', now(), '".PAQUETES."')");

        if ($jsonObject['data']['x_extra18'] == MODULOS) {
            require_once(ROOT_PATH."/main-app/class/Plataforma.php");
            require_once(ROOT_PATH."/main-app/class/Modulos.php");
            $datosPaquetes = Plataforma::contarDatosPaquetes($jsonObject['data']['x_extra6'], PAQUETES);

            $arrayModulos = explode(",", $datosPaquetes['plns_modulos']);
            if(in_array(MODULO_ADMISIONES, $arrayModulos)) {
              
              $Plataforma = new Plataforma;
        
              try{
                $colorBG = $Plataforma->colorUno;
                $yearInscription = date('Y')+1;
        
                $sql = "INSERT INTO " . BD_ADMISIONES . ".config_instituciones(cfgi_id_institucion,
                cfgi_year,
                cfgi_color_barra_superior,
                cfgi_inscripciones_activas,
                cfgi_politicas_texto,
                cfgi_color_texto,
                cfgi_mostrar_banner,
                cfgi_year_inscripcion) VALUES (?, ?, ?, '0', 'Loremp ipsum...', 'white', '0', ?) 
                ON DUPLICATE KEY UPDATE cfgi_year_inscripcion = VALUES(cfgi_year_inscripcion)";
        
                $stmt = mysqli_prepare($conexion, $sql);
        
                if (!$stmt) {
                  die("Error al preparar la consulta.");
                }
        
                // Vincular los parámetros
                mysqli_stmt_bind_param($stmt, "iisi", $jsonObject['data']['x_extra6'], date('Y'), $colorBG, $yearInscription);
        
                // Ejecutar la consulta
                $resultado = mysqli_stmt_execute($stmt);
        
                if (!$resultado) {
                  die("Error al ejecutar la consulta.");
                }
        
                } catch (Exception $e) {
                  include("../compartido/error-catch-to-report.php");
                }
              
            }
    
            $arregloModulos = Modulos::consultarModulosIntitucion($conexion, $jsonObject['data']['x_extra6']);
            $_SESSION["modulos"] = $arregloModulos;
        }
      }
    }
  }

  mysqli_query($conexion, "INSERT INTO " . $baseDatosServicios . ".pasarela_respuestas(psr_cliente, psr_ref, psr_transaccion,	psr_respuesta_nombre,	psr_respuesta_codigo,	psr_documento, psr_nombre, psr_email,	psr_error_codigo,	psr_error_nombre,	psr_celular, psr_ref_epayco, psr_factura, psr_id_institucion, psr_descripcion) VALUES ('" . $jsonObject['data']['x_extra1'] . "', '" . $jsonObject['data']['x_id_invoice'] . "', '" . $jsonObject['data']['x_transaction_id'] . "', '" . $jsonObject['data']['x_response'] . "', '" . $jsonObject['data']['x_cod_response'] . "', '" . $jsonObject['data']['x_extra3'] . "', '" . $jsonObject['data']['x_extra4'] . "', '" . $jsonObject['data']['x_extra2'] . "', '" . $jsonObject['data']['x_errorcode'] . "', '" . $jsonObject['data']['x_response_reason_text'] . "', '" . $jsonObject['data']['x_extra5'] . "', '" . $jsonObject['data']['x_ref_payco'] . "', '" . $jsonObject['data']['x_id_factura'] . "', '" . $jsonObject['data']['x_extra6'] . "', '" . $jsonObject['data']['x_description'] . "')");

  if ($estado == TRANSACCION_ACEPTADA && !empty($jsonObject['data']['x_extra7'])) {
    mysqli_query($conexion, "INSERT INTO " . $baseDatosMarketPlace . ".mis_compras(misc_fecha, misc_institucion, misc_usuario,	misc_producto,	misc_cantidad,	misc_precio_producto, misc_valor_final, misc_estado_compra,	misc_estado_pago) VALUES (now(), '" . $jsonObject['data']['x_extra6'] . "', '" . $jsonObject['data']['x_extra1'] . "', '" . $jsonObject['data']['x_extra7'] . "', '" . $jsonObject['data']['x_extra8'] . "', '" . $jsonObject['data']['x_extra9'] . "', '" . $jsonObject['data']['x_extra10'] . "', 2, '" . $jsonObject['data']['x_response'] . "')");
  }
  ?>
  <div class="container">
    <div class="row" style="margin-top:20px">
      <div class="col-lg-8 col-lg-offset-2 ">
        <h4 style="text-align:left"> Respuesta de la Transacción </h4>
        <hr>
      </div>
      <div class="col-lg-8 col-lg-offset-2 ">
        <div class="table-responsive">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td>Referencia</td>
                <td id="referencia"></td>
              </tr>
              <tr>
                <td class="bold">Fecha</td>
                <td id="fecha" class=""></td>
              </tr>
              <tr>
                <td>Respuesta</td>
                <td id="respuesta"></td>
              </tr>
              <tr <?= $hidden ?>>
                <td>Identificacion</td>
                <td><?= $identificacion ?></td>
              </tr>
              <tr <?= $hidden ?>>
                <td>Descripcion</td>
                <td><?= $descripcion ?></td>
              </tr>
              <tr <?= $hidden ?>>
                <td>Correo</td>
                <td><?= $correo ?></td>
              </tr>
              <tr <?= $hidden ?>>
                <td>Celular</td>
                <td><?= $celular ?></td>
              </tr>
              <tr>
                <td>Motivo</td>
                <td id="motivo"></td>
              </tr>
              <tr>
                <td class="bold">Banco</td>
                <td class="" id="banco">
              </tr>
              <tr>
                <td class="bold">Recibo</td>
                <td id="recibo"></td>
              </tr>
              <tr>
                <td class="bold">Total</td>
                <td class="" id="total">
                </td>
              </tr>
              <tr <?= $hidden ?> style="text-align: center">
                <td class="bold" colspan='2'>
                  <form method="post" id="autenticoFromulario" action="<?= $urlOrigen ?>" class="needs-validation" novalidate>
                    <input type="hidden" class="form-control" name="aut" value="<?= $usuario ?>">
                    <input type="hidden" class="form-control" name="documento" value="<?= $identificacion ?>">
                    <button class="w-75 btn btn-lg btn-primary btn-rounded mt-3" type="submit">Regresar a SINTIA</button>
                  </form>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <footer>
    <div class="row">
      <div class="container">
        <div class="col-lg-8 col-lg-offset-2">
          <img src="https://369969691f476073508a-60bf0867add971908d4f26a64519c2aa.ssl.cf5.rackcdn.com/btns/epayco/pagos_procesados_por_epayco_260px.png" style="margin-top:10px; float:left"> <img src="https://369969691f476073508a-60bf0867add971908d4f26a64519c2aa.ssl.cf5.rackcdn.com/btns/epayco/credibancologo.png" height="40px" style="margin-top:10px; float:right">
        </div>
      </div>
    </div>
  </footer>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <script>
    function getQueryParam(param) {
      location.search.substr(1)
        .split("&")
        .some(function(item) { // returns first occurence and stops
          return item.split("=")[0] == param && (param = item.split("=")[1])
        })
      return param
    }
    $(document).ready(function() {
      //llave publica del comercio

      //Referencia de payco que viene por url
      var ref_payco = getQueryParam('ref_payco');
      //Url Rest Metodo get, se pasa la llave y la ref_payco como paremetro
      var urlapp = "https://secure.epayco.co/validation/v1/reference/" + ref_payco;

      $.get(urlapp, function(response) {
        console.log(response);

        if (response.success) {
          console.log(response.data);
          $('#fecha').html(response.data.x_transaction_date);
          $('#respuesta').html(response.data.x_response);
          $('#referencia').text(response.data.x_id_invoice);
          $('#motivo').text(response.data.x_response_reason_text);
          $('#recibo').text(response.data.x_transaction_id);
          $('#banco').text(response.data.x_bank_name);
          $('#autorizacion').text(response.data.x_approval_code);
          $('#total').text(response.data.x_amount + ' ' + response.data.x_currency_code);
          $('#url_origen').text(response.data.x_approval_code);

        } else {
          alert("Error consultando la información");
        }
      });

    });
  </script>

</body>

</html>