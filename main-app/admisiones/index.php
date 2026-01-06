<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once ROOT_PATH."/main-app/class/Modulos.php";

$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);

// Obtener año actual y siguiente
$yearActual = (int)date('Y');
$yearSiguiente = $yearActual + 1;

// Variable para mensaje de error
$errorMensaje = '';

// Valores por defecto para colores (antes de seleccionar institución)
$fondoBarra = '#6017dc';
$colorTexto = '#FFF';

// Validar si se envió un ID de institución
if (isset($_POST['idInstRaw']) && !empty($_POST['idInstRaw'])) {
    $idInstRaw = trim($_POST['idInstRaw']);
    
    // Validar que sea numérico
    if (!is_numeric($idInstRaw)) {
        $errorMensaje = 'El ID de la institución debe ser un número válido.';
    } else {
        $idInsti = (int)$idInstRaw;
        
        // Validar que la institución existe y tiene inscripciones activas
        $validacionConsulta = mysqli_query($conexionBaseDatosServicios, "
        SELECT ins.ins_id, 
               MAX(cfgi.cfgi_year_inscripcion) as cfgi_year_inscripcion,
               MAX(cfgi.cfgi_inscripciones_activas) as cfgi_inscripciones_activas
        FROM instituciones ins
        INNER JOIN ".BD_ADMISIONES.".config_instituciones cfgi 
            ON cfgi_id_institucion=ins_id 
            AND cfgi_inscripciones_activas=1
            AND (cfgi_year_inscripcion = {$yearActual} OR cfgi_year_inscripcion = {$yearSiguiente})
        WHERE 
            ins_id = {$idInsti}
            AND ins_estado = 1 
            AND ins_enviroment='".ENVIROMENT."'
            AND (
                EXISTS (
                    SELECT 1 FROM instituciones_modulos 
                    WHERE ipmod_institucion=ins_id AND ipmod_modulo=".Modulos::MODULO_INSCRIPCIONES."
                )
                OR EXISTS (
                    SELECT 1 FROM instituciones_paquetes_extras 
                    WHERE paqext_institucion=ins_id 
                    AND paqext_id_paquete=".Modulos::MODULO_INSCRIPCIONES." 
                    AND paqext_tipo='".MODULOS."'
                )
            )
        GROUP BY ins_id
        LIMIT 1
        ");
        
        if (mysqli_num_rows($validacionConsulta) > 0) {
            // Institución válida, redirigir a admision.php con el ID codificado
            $idInstCodificado = base64_encode($idInsti);
            header("Location: admision.php?idInst=" . urlencode($idInstCodificado));
            exit();
        } else {
            $errorMensaje = 'El ID de institución ingresado no es válido o no tiene inscripciones activas.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php include(ROOT_PATH."/config-general/analytics/instituciones.php");?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admisiones | Plataforma SINTIA</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="shortcut icon" href="../sintia-icono.png" />
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                background-color: #f5f5f5;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                padding: 40px 20px;
            }
            
            .logo-container {
                text-align: center;
                margin-bottom: 40px;
            }
            
            .logo-container img {
                max-width: 200px;
                height: auto;
            }
            
            .main-container {
                max-width: 1200px;
                width: 100%;
                margin: 0 auto;
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                border-top: 4px solid <?= $fondoBarra; ?>;
            }
            
            .container-content {
                display: flex;
                min-height: 500px;
            }
            
            .left-panel {
                flex: 1;
                padding: 60px 50px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            
            .right-panel {
                flex: 1;
                background: #fafafa;
                padding: 60px 50px;
                border-left: 1px solid #e0e0e0;
            }
            
            .form-title {
                font-size: 28px;
                font-weight: 700;
                color: #2c2c2c;
                margin-bottom: 30px;
            }
            
            .form-group-custom {
                margin-bottom: 20px;
            }
            
            .form-label {
                font-size: 14px;
                font-weight: 600;
                color: #2c2c2c;
                margin-bottom: 10px;
                display: block;
            }
            
            .form-input {
                width: 100%;
                padding: 14px 16px;
                font-size: 16px;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                transition: all 0.3s ease;
                background: #ffffff;
            }
            
            .form-input:focus {
                outline: none;
                border-color: <?= $fondoBarra; ?>;
                box-shadow: 0 0 0 3px rgba(96, 23, 220, 0.1);
            }
            
            .form-hint {
                font-size: 13px;
                color: #888;
                margin-top: 8px;
            }
            
            .btn-submit {
                width: 100%;
                padding: 16px;
                font-size: 16px;
                font-weight: 600;
                background-color: <?= $fondoBarra; ?>;
                color: <?= $colorTexto; ?>;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-top: 10px;
            }
            
            .btn-submit:hover {
                opacity: 0.9;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(96, 23, 220, 0.3);
            }
            
            .btn-submit:active {
                transform: translateY(0);
            }
            
            .info-section {
                margin-bottom: 35px;
            }
            
            .info-title {
                font-size: 18px;
                font-weight: 700;
                color: #2c2c2c;
                margin-bottom: 12px;
            }
            
            .info-text {
                font-size: 15px;
                color: #666;
                line-height: 1.6;
            }
            
            .info-description {
                font-size: 15px;
                color: #666;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            
            .signin-section {
                text-align: center;
                margin-top: 40px;
                margin-bottom: 30px;
            }
            
            .signin-text {
                font-size: 15px;
                color: #666;
                margin-bottom: 15px;
            }
            
            .btn-signin {
                display: inline-block;
                padding: 12px 30px;
                font-size: 15px;
                font-weight: 600;
                background: #ffffff;
                color: #2c2c2c;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                text-decoration: none;
                transition: all 0.3s ease;
            }
            
            .btn-signin:hover {
                border-color: <?= $fondoBarra; ?>;
                color: <?= $fondoBarra; ?>;
                text-decoration: none;
            }
            
            .footer {
                max-width: 1200px;
                width: 100%;
                margin: 40px auto 0;
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                color: #888;
            }
            
            .footer-left,
            .footer-center,
            .footer-right {
                flex: 1;
            }
            
            .footer-center {
                text-align: center;
            }
            
            .footer-left a,
            .footer-center a {
                color: #888;
                text-decoration: none;
            }
            
            .footer-left a:hover,
            .footer-center a:hover {
                color: <?= $fondoBarra; ?>;
                text-decoration: underline;
            }
            
            .footer-right {
                text-align: right;
            }
            
            .footer-right a {
                color: #888;
                text-decoration: none;
            }
            
            .footer-right a:hover {
                color: <?= $fondoBarra; ?>;
                text-decoration: underline;
            }
            
            .alert-error {
                background: #fee;
                border: 1px solid #fcc;
                color: #c33;
                padding: 12px 16px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
            }
            
            @media (max-width: 992px) {
                .container-content {
                    flex-direction: column;
                }
                
                .right-panel {
                    border-left: none;
                    border-top: 1px solid #e0e0e0;
                }
                
                .left-panel,
                .right-panel {
                    padding: 40px 30px;
                }
                
                .footer {
                    flex-direction: column;
                    text-align: center;
                    gap: 15px;
                }
                
                .footer-left,
                .footer-center,
                .footer-right {
                    text-align: center;
                }
            }
            
            @media (max-width: 576px) {
                body {
                    padding: 20px 10px;
                }
                
                .logo-container {
                    margin-bottom: 30px;
                }
                
                .logo-container img {
                    max-width: 150px;
                }
                
                .left-panel,
                .right-panel {
                    padding: 30px 20px;
                }
                
                .form-title {
                    font-size: 24px;
                }
            }
        </style>
    </head>
    
    <body>
        <?php include("alertas.php"); ?>
        
        <!-- Logo -->
        <div class="logo-container">
            <img src="../../config-general/assets-login-2023/img/logo.png" alt="SINTIA">
        </div>
        
        <!-- Contenedor Principal -->
        <div class="main-container">
            <div class="container-content">
                <!-- Panel Izquierdo - Formulario -->
                <div class="left-panel">
                    <h1 class="form-title">Iniciar Proceso de Admisión</h1>
                    
                    <form action="index.php" method="post">
                        <?php if (!empty($errorMensaje)) { ?>
                            <div class="alert-error">
                                <?= htmlspecialchars($errorMensaje); ?>
                            </div>
                        <?php } ?>
                        
                        <div class="form-group-custom">
                            <label for="idInstRaw" class="form-label">Ingrese el ID de la Institución *</label>
                            <input type="text" 
                                   id="idInstRaw" 
                                   name="idInstRaw" 
                                   class="form-input" 
                                   placeholder="Ingrese el ID numérico" 
                                   required 
                                   autocomplete="off"
                                   pattern="[0-9]+"
                                   title="Solo se permiten números">
                            <div class="form-hint">El ID de la institución es sensible a mayúsculas y minúsculas</div>
                        </div>
                        
                        <button type="submit" class="btn-submit">Iniciar Proceso</button>
                    </form>
                </div>
                
                <!-- Panel Derecho - Información -->
                <div class="right-panel">
                    <p class="info-description">
                        Esta plataforma de admisiones proporciona el contenido y formularios necesarios para el proceso de inscripción de las instituciones educativas. El acceso es mediante invitación y requiere un ID de institución válido.
                    </p>
                    
                    <div class="info-section">
                        <h3 class="info-title">¿Qué es el ID de Institución?</h3>
                        <p class="info-text">
                            El ID de Institución es un código numérico único que necesita para acceder al proceso de admisión de su institución educativa. Todos los IDs son numéricos y deben ingresarse correctamente.
                        </p>
                    </div>
                    
                    <div class="info-section">
                        <h3 class="info-title">¿Por qué fui invitado a registrarme?</h3>
                        <p class="info-text">
                            La institución educativa que le envió una invitación le está ofreciendo acceso a su proceso de admisión e inscripción para el año académico correspondiente.
                        </p>
                    </div>
                    
                    <div class="info-section">
                        <h3 class="info-title">¿Hay algún costo para registrarse?</h3>
                        <p class="info-text">
                            El acceso a la plataforma es gratuito, pero la mayoría de instituciones educativas tienen un cobro por formulario o derecho de inscripción, el cual varía dependiendo de cada institución. El proceso de registro es solo por invitación y requiere un ID de institución válido proporcionado por su institución educativa.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección de Consultar Estado -->
        <div class="signin-section">
            <p class="signin-text">¿Ya tiene una solicitud registrada?</p>
            <a href="consultar-estado.php" class="btn-signin">Consultar Estado</a>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">
                © <?= date('Y'); ?> SINTIA <a href="#">Legal</a>
            </div>
            <div class="footer-center">
                <a href="<?= REDIRECT_ROUTE; ?>/index.php">Volver al login</a>
            </div>
            <div class="footer-right">
                Contacto: <a href="mailto:soporte@sintia.com">soporte@sintia.com</a>
            </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        
        <script>
            $(document).ready(function() {
                // Enfocar el campo de ID al cargar la página
                $('#idInstRaw').focus();
                
                // Validar que solo se ingresen números
                $('#idInstRaw').on('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });
        </script>
    </body>
</html>