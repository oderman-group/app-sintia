<?php
if(!isset($_GET["ref"]) or $_GET["ref"]=="" or !is_numeric($_GET["ref"]) or $_SERVER['HTTP_REFERER']==""){
	echo '<script type="text/javascript">window.close()</script>';
	exit();
}
?>
<?php include("../directivo/session.php");?>
<?php include("../head.php");?>
  <style type="text/css">
  @import url(http://fonts.googleapis.com/css?family=Open+Sans:400,300,700);
  /* Based on The MailChimp Reset INLINE: Yes. */
  /* Client-specific Styles */
  #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
  body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;}
  /* Prevent Webkit and Windows Mobile platforms from changing default font sizes.*/
  .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
  .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}
  /* Forces Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
  #backgroundTable {
    margin:0; padding:0; width:100% !important; line-height: 100% !important;background-color: #e9eaed;
    font-size: 14px;
    line-height: 20px;
    color: #333333
  }
  /* End reset */
      /* Some sensible defaults for images
      Bring inline: Yes. */
      img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}
      a img {border:none;}
      .image_fix {display:block;}
      /* Yahoo paragraph fix
      Bring inline: Yes. */
      p {margin: 1em 0;}
      /* Hotmail header color reset
      Bring inline: Yes. */
      h1, h2, h3, h4, h5, h6 {color: black !important; font-weight: normal;}
      h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}
      h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
        color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
      }
      h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
        color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
      }
      /* Outlook 07, 10 Padding issue fix
      Bring inline: No.*/
      table td {border-collapse: collapse;}
      /* Remove spacing around Outlook 07, 10 tables
      Bring inline: Yes */
      table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
      /* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email and make sure to bring your styles inline.  Your link colors will be uniform across clients when brought inline.
      Bring inline: Yes. */
      a {color: #6d84b4;}
      /***************************************************
      ****************************************************
      MOBILE TARGETING
      ****************************************************
      ***************************************************/
      @media only screen and (max-device-width: 480px) {
        /* Part one of controlling phone number linking for mobile. */
        a[href^="tel"], a[href^="sms"] {
          text-decoration: none;
          color: blue; /* or whatever your want */
          pointer-events: none;
          cursor: default;
        }
        .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
          text-decoration: default;
          color: orange !important;
          pointer-events: auto;
          cursor: default;
        }
      }
      /* More Specific Targeting */
      @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
        /* You guessed it, ipad (tablets, smaller screens, etc) */
        /* repeating for the ipad */
        a[href^="tel"], a[href^="sms"] {
          text-decoration: none;
          color: blue; /* or whatever your want */
          pointer-events: none;
          cursor: default;
        }
        .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
          text-decoration: default;
          color: orange !important;
          pointer-events: auto;
          cursor: default;
        }
      }
      @media only screen and (-webkit-min-device-pixel-ratio: 2) {
        /* Put your iPhone 4g styles in here */
      }
      /* Android targeting */
      @media only screen and (-webkit-device-pixel-ratio:.75){
        /* Put CSS for low density (ldpi) Android layouts in here */
      }
      @media only screen and (-webkit-device-pixel-ratio:1){
        /* Put CSS for medium density (mdpi) Android layouts in here */
      }
      @media only screen and (-webkit-device-pixel-ratio:1.5){
        /* Put CSS for high density (hdpi) Android layouts in here */
      }
      /* end Android targeting */
      </style>
      <!-- Targeting Windows Mobile -->
    <!--[if IEMobile 7]>
    <style type="text/css">
    </style>
    <![endif]-->
    <!-- ***********************************************
      ****************************************************
      END MOBILE TARGETING
      ****************************************************
      ************************************************ -->
    <!--[if gte mso 9]>
    <style>
      /* Target Outlook 2007 and 2010 */
    </style>
    <![endif]-->
  </head>
  <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
  <?php
  $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_matricula='".$_GET["ref"]."'");
  $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
  ?>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#1fbba6">
      <tbody>
        <tr>
          <td align="center">
            <center>
              <table border="0" width="600" cellpadding="0" cellspacing="0">
                <tbody>
                  <tr>
                    <td style="padding-left: 15px;" height="40">
                      <img src="../files/images/logoodermanm.png"  height="78" width="150"/>
                      <!--<a href="#" style="color:#ffffff !important;font-size: 20px;font-weight: 200;text-decoration:none">jAcad&eacute;mico</a>-->
                    </td>
                  </tr>
                </tbody>
              </table>
            </center>
          </td>
        </tr>
      </tbody>
    </table>
    <center>
      <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable">
        <tr>
          <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff;">
              <tr>
                <td align="center" valign="top">
                  <table border="0" cellpadding="0" cellspacing="0" width="600">
                    <tr>
                      <td valign="top">
                        <table border="0" cellpadding="20" cellspacing="0" width="100%">
                          <tr>
                            <td valign="top" style="padding: 15px;">
                              <div>
                                <h2>Confirmaci&oacute;n de la matricula</h2>
                                <strong>Estimado(a), <?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></strong>
                                <br />
                                Este es el comprobante de su matr&iacute;cula!
                                <br />
                                <br />
                                A continuaci&oacute;n sus datos.
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td valign="top">
                              <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                                <tbody><tr style="background:#ffd300;">
                                  <td colspan="2" align="center" height="40">
                                    <div style="margin:0 0 0 10px"><b>Detalles de la mat&iacute;cula</b></div>
                                  </td>
                                </tr>
                                <tr style="background:#F7F7F7">
                                  <td width="190" align="right" height="40">
                                    <b>Codigo: </b>
                                  </td>
                                  <td width="380">
                                    <div style="margin:0 0 0 10px"><?=$resultado[1];?></div>
                                  </td>
                                </tr>
                                <tr style="background:#ffd300;" height="40">
                                  <td width="190" align="right">
                                    <b>Nombres y apellidos : </b>
                                  </td>
                                  <td width="380">
                                    <div style="margin:0 0 0 10px"><?=strtoupper($resultado[3]." ".$resultado[4]." " .$resultado[5]);?></div>
                                  </td>
                                </tr>
                                
                                <tr style="background:#F7F7F7" height="40">
                                  <td width="190" align="right">
                                    <b>Documento: </b>
                                  </td>
                                  <td width="380">
                                    <div style="margin:0 0 0 10px"><?=$resultado[12];?></div>
                                  </td>
                                </tr>
                               
                                
                                
                              </tbody>
                            </table>
                          </td>
                        </tr>

                      </table>
                    </td>
                  </tr>

                  <tr>
                    <td valign="top">
                      <table border="0" cellpadding="20" cellspacing="0" width="100%">
                        <tr>
                          <td valign="top" style="padding: 15px;">
                            <div>
                              Estimado usuario, guarde este comprobante en caso de que se presente alg&uacute;n incoveniente.
                              <br />
                              <br />
                              Si tiene alguna inquietud sobre el manejo de la plataforma comuniquese al correo <a href="">plataforma@oderman.com.co</a> o al n&uacute;mero tel&eacute;fonico (4) 585 3755
                              <br />
                              <br />
                              <b>ODERMAN, Contribuimos a la excelencia educativa.</b>
                            </div>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </center>
  <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#1fbba6">
    <tbody>
      <tr>
        <td align="center">
          <center>
            <table border="0" width="600" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td style="color:#ffffff !important; font-size:20px; padding-left:10px;" height="40">
                    <center>
                      <p style="font-size:12px; line-height:18px;">
                        © <?=date("Y");?> SINTIA  de Oderman<br />
                        Sistema Integral de Gesti&oacute;n Institucional<br />
                        Universidades | Colegios | Institutos<br />
                        info@oderman.com.co<br />
                        www.oderman.com.co<br />
                        (4) 585 3755 - 318 347 9394
                      </p>
                    </center>
                  </td>
                </tr>
              </tbody>
            </table>
          </center>
        </td>
      </tr>
    </tbody>
  </table>
  
        <script>print();</script>
  
</body>

</html>
