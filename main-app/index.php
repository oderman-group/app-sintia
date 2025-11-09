<?php
$logoIndex = "../sintia-gris.png";
$logoWidth = 250;

if(!isset($_GET['nodb'])) {
    require_once("index-logica.php");
    require_once(ROOT_PATH."/main-app/class/App/Mensajes_Informativos/Mensajes_Informativos.php");
    
    // Iniciar sesión temporal para CSRF (no requiere autenticación)
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Lax');
        session_start();
    }
    require_once(ROOT_PATH."/main-app/class/App/Seguridad/Csrf.php");

    if (!empty($_GET['inst']) && !empty($_GET['year'])) {
        try {
            // Validar y sanitizar parámetros
            $institucion = base64_decode($_GET['inst']);
            $year = base64_decode($_GET['year']);
            
            // Validar que sean valores válidos
            if (!is_numeric($year) || $year < 2000 || $year > 2100) {
                throw new Exception("Año inválido");
            }
            
            // Incluir clase Conexion para PDO
            require_once(ROOT_PATH."/main-app/class/Conexion.php");
            
            // Query segura con PDO prepared statement (patrón del proyecto)
            $conexionPDO = Conexion::newConnection('PDO');
            $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "SELECT * FROM ".$baseDatosServicios.".general_informacion WHERE info_institucion=? AND info_year=?";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $institucion, PDO::PARAM_STR);
            $stmt->bindParam(2, $year, PDO::PARAM_INT);
            $stmt->execute();
            $informacion_inst = $stmt->fetch(PDO::FETCH_BOTH);
            if (!empty($informacion_inst["info_logo"]) && file_exists("files/images/logo/".$informacion_inst["info_logo"])) {
                $logoIndex = "files/images/logo/".$informacion_inst["info_logo"];
                $logoWidth = 300;
            }
            $inst = base64_decode($_GET['inst']);
        } catch(Exception $e){
            header("Location:".REDIRECT_ROUTE."?error=".$e->getMessage());
        }
    }
    
    if (!empty($_GET['error']) && $_GET['error'] == Mensajes_Informativos::USUARIO_BLOQUEADO) {
        require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario_Bloqueado.php");
        require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario.php");

        $predicado = [
            'usblo_id_usuario'  => base64_decode($_GET['idU']),
            'usblo_institucion' => base64_decode($_GET['inst']),
            'usblo_year'        => base64_decode($_GET['year'])
        ];
    
        $campos = "usblo_motivo";
        $consultaMotivo = Administrativo_Usuario_Usuario_Bloqueado::SelectOrderLimit($predicado, $campos, "ORDER BY usblo_id DESC", "LIMIT 1");
        $datosMotivo = $consultaMotivo->fetch(PDO::FETCH_ASSOC);
        
        $motivo = !empty($datosMotivo['usblo_motivo']) ? $datosMotivo['usblo_motivo'] : "Motivo no registrado";
        $telefono = !empty($informacion_inst['info_telefono']) ? $informacion_inst['info_telefono'] : "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>Plataforma Educativa SINTIA | Login</title>
    <!-- Google fonts-->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@1,900,700,500,301,701,300,501,401,901,400&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Or for RTL support -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="../config-general/assets-login-2023/css/styles.css" rel="stylesheet" />
    
    <!-- Estilos personalizados con nueva paleta de colores -->
    <style>
        :root {
            --sintia-primary-bg: #ffffff;
            --sintia-secondary: #41c4c4;
            --sintia-accent: #6017dc;
            --sintia-text-dark: #000000;
            --sintia-text-light: #ffffff;
            --sintia-font-family: "Satoshi", sans-serif;
        }
        
        /* Aplicar fuente Satoshi globalmente */
        body, html, * {
            font-family: var(--sintia-font-family) !important;
        }
        
        /* Fondo blanco limpio para el lado izquierdo */
        .login-container {
            background: var(--sintia-primary-bg);
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        
        .vertical-center {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: flex-start;
            overflow-y: auto;
            overflow-x: hidden;
            padding-top: 3rem;
        }
        
        /* Mejorar el formulario de login */
        .btn-primary {
            background-color: var(--sintia-secondary);
            border-color: var(--sintia-secondary);
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--sintia-accent);
            border-color: var(--sintia-accent);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(96, 23, 220, 0.3);
        }
        
        .form-control:focus {
            border-color: var(--sintia-secondary);
            box-shadow: 0 0 0 0.2rem rgba(65, 196, 196, 0.25);
        }
        
        .forgot-password {
            color: var(--sintia-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: var(--sintia-accent);
            text-decoration: underline;
        }
        
        /* Galería de fotos en el lado derecho */
        .photo-gallery {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        
        .photo-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 2s ease-in-out;
        }
        
        .photo-slide.active {
            opacity: 1;
        }
        
        .photo-slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(65, 196, 196, 0.1), rgba(96, 23, 220, 0.1));
        }
        
        /* Estados del botón de login */
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn-primary.loading {
            background-color: var(--sintia-accent);
            border-color: var(--sintia-accent);
        }
        
        .btn-primary.success {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .btn-primary.error {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        /* Mensajes dinámicos */
        .alert-dynamic {
            border-radius: 10px;
            border: none;
            font-weight: 500;
            animation: slideInDown 0.5s ease-out;
            position: relative;
            z-index: 1000;
        }
        
        /* En móviles, mensajes fijos en la parte superior */
        @media (max-width: 768px) {
            .alert-dynamic {
                position: fixed !important;
                top: 20px;
                left: 10px;
                right: 10px;
                z-index: 9999;
                margin: 0;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                animation: fadeIn 0.3s ease-out !important; /* Cambiar animación */
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        }
        
        .alert-dynamic.error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert-dynamic.success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-dynamic.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid var(--sintia-secondary);
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Animación de shake para errores */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        
        /* Mejorar el formulario de login */
        .login-card {
            background: var(--sintia-primary-bg);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 2.5rem;
            border: 1px solid rgba(65, 196, 196, 0.1);
            backdrop-filter: blur(10px);
            margin: 2rem auto;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-title {
            color: var(--sintia-accent);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .login-subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 0;
            font-weight: 400;
        }
        
        .login-logo {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
        }
        
        /* Mejorar campos de entrada */
        .input-group-text {
            background-color: rgba(65, 196, 196, 0.1);
            border-color: rgba(65, 196, 196, 0.2);
            color: var(--sintia-secondary);
            border-right: none;
        }
        
        .form-control {
            border-left: none;
            border-color: rgba(65, 196, 196, 0.2);
            padding-left: 0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--sintia-secondary);
            box-shadow: 0 0 0 0.2rem rgba(65, 196, 196, 0.25);
            border-left: none;
        }
        
        .form-control:focus + .input-group-text {
            border-color: var(--sintia-secondary);
            background-color: rgba(65, 196, 196, 0.15);
        }
        
        /* Botón principal mejorado */
        .btn-login {
            background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(65, 196, 196, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(65, 196, 196, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Divisor */
        .divider {
            position: relative;
            text-align: center;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
        }
        
        .divider-text {
            background: var(--sintia-primary-bg);
            padding: 0 1rem;
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        /* Botones de redes sociales */
        .social-login-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        
        .btn-social {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-social:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-social:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .btn-social small {
            font-size: 0.7rem;
            opacity: 0.8;
        }
        
        /* Sección de captcha mejorada */
        .captcha-section .alert {
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        }
        
        /* Footer mejorado */
        .login-footer {
            border-top: 1px solid rgba(0,0,0,0.1);
            padding-top: 1.5rem;
        }
        
        .forgot-password, .support-link {
            color: var(--sintia-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            font-size: 0.9rem;
        }
        
        .forgot-password:hover, .support-link:hover {
            color: var(--sintia-accent);
            text-decoration: underline;
        }
        
        .registration-section p {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        /* Estados del botón de login */
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn-login.loading {
            background: linear-gradient(135deg, var(--sintia-accent) 0%, #8b5cf6 100%);
        }
        
        .btn-login.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .btn-login.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        /* Mensajes dinámicos mejorados */
        .alert-dynamic {
            border-radius: 12px;
            border: none;
            font-weight: 500;
            animation: slideInDown 0.5s ease-out;
            padding: 1rem 1.25rem;
        }
        
        .alert-dynamic.error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }
        
        .alert-dynamic.success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #16a34a;
            border-left: 4px solid #22c55e;
        }
        
        .alert-dynamic.info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #2563eb;
            border-left: 4px solid var(--sintia-secondary);
        }
        
        /* Responsive para tablets y móviles */
        @media (max-width: 1024px) {
            /* Ocultar galería de fotos en tablets y móviles */
            .photo-gallery {
                display: none !important;
            }
            
            /* Centrar el formulario de login */
            .login-container {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .vertical-center {
                justify-content: center;
                align-items: center;
            }
            
            .login-card {
                max-width: 500px;
                margin: 1.5rem auto;
                padding: 2.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .login-card {
                margin: 1rem;
                margin-top: 80px; /* Espacio extra para mensajes */
                padding: 2rem;
            }
            
            .login-title {
                font-size: 1.75rem;
            }
            
            .social-login-buttons {
                grid-template-columns: 1fr;
            }
            
            .btn-social {
                padding: 0.875rem 1rem;
            }
            
            /* Contenedor con scroll suave */
            .vertical-center {
                padding-top: 80px;
                padding-bottom: 40px;
            }
        }
        
        @media (max-width: 480px) {
            .login-card {
                margin: 0.5rem;
                margin-top: 70px; /* Espacio extra para mensajes en móviles pequeños */
                padding: 1.5rem;
                border-radius: 12px;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
            
            .login-subtitle {
                font-size: 0.875rem;
            }
            
            .login-logo {
                max-width: 180px;
            }
            
            /* Mensajes más compactos en móviles pequeños */
            .alert-dynamic {
                top: 15px;
                left: 8px;
                right: 8px;
                font-size: 0.875rem;
                padding: 0.75rem 1rem;
            }
        }
    </style>

</head>

<body>
    <div class="login-container">
        <div class="vertical-center text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2" id="login">
                        <div class="login-card">
                            <form id="loginForm" method="post" action="controlador/autentico.php" class="needs-validation" novalidate>
                            <?php include("../config-general/mensajes-informativos.php"); ?>
                            
                            <!-- Contenedor para mensajes dinámicos -->
                            <div id="dynamicMessages" class="mt-3"></div>
                            
                            <!-- Token CSRF para protección contra ataques -->
                            <?php echo campoTokenCSRF(); ?>
                            
		                        <input type="hidden" name="urlDefault" value="<?php if(isset($_GET["urlDefault"])) echo htmlspecialchars($_GET["urlDefault"], ENT_QUOTES, 'UTF-8');?>" />
                                <input type="hidden" name="directory"  value="<?php if(isset($_GET["directory"]))  echo htmlspecialchars($_GET["directory"], ENT_QUOTES, 'UTF-8'); ?>" />
                            <header class="login-header">
                                <img class="mb-4 login-logo" src="<?=$logoIndex;?>" width="<?=$logoWidth;?>" alt="Logo SINTIA" loading="eager">
                                <h1 class="login-title">Bienvenido</h1>
                                <p class="login-subtitle">Inicia sesión en tu plataforma educativa</p>
                            </header>
                            
                            <div class="form-floating mt-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                    <input type="text" class="form-control input-login" id="emailInput" name="Usuario"
                                        placeholder="Usuario" required>
                                </div>
                                <div class="invalid-feedback">Por favor ingrese un usuario válido.</div>
                            </div>

                            <div class="form-floating mt-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="password" class="form-control input-login" id="password" name="Clave"
                                        placeholder="Password" required>
                                    <button class="btn btn-outline-secondary input-group-text toggle-password"
                                        type="button" aria-label="Mostrar/ocultar contraseña">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Por favor ingresa tu contraseña para continuar</div>
                                <div class="form-text" id="caps-lock-message" style="display: none;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Mayúsculas activadas
                                </div>
                            </div>

                            <?php
                            if (!empty($_GET["error"]) && $_GET["error"] == 3) {
                            $numA1 = rand(1, 10);
                            $numA2 = rand(1, 10);
                            $resultadoA = $numA1 + $numA2;
                            ?>
                            <div class="captcha-section mt-4">
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="bi bi-shield-check me-2"></i>
                                    <div>
                                        <strong>Verificación de seguridad</strong><br>
                                        <small>Resuelve la operación matemática para continuar</small>
                                    </div>
                                </div>
                                <div class="form-floating">
                                    <input type="hidden" name="sumaReal" value="<?= md5($resultadoA); ?>" />
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calculator"></i>
                                        </span>
                                        <input type="text" class="form-control input-login" id="suma" name="suma" 
                                            placeholder="Cuánto es <?= $numA1 . "+" . $numA2; ?>?" 
                                            autocomplete="off" required>
                                        <label for="suma">Cuánto es <?= $numA1 . "+" . $numA2; ?>?</label>
                                    </div>
                                    <div class="invalid-feedback">Por favor ingrese un número válido.</div>
                                </div>
                            </div>
                            <?php } ?>
                            
                            <div class="form-floating mt-3" style="display: none;">
                                <select class="form-select select-invalid" id="year" name="year"
                                    aria-label="Default select example">
                                    <option value="" disabled selected>Seleccione un año</option>
                                    <option value="2022" selected>2022</option>
                                </select>
                                <label for="year">Año</label>
                                <div class="invalid-feedback">Por favor seleccione un año.</div>
                            </div>

                            <div class="login-actions mt-4">
                                <button id="loginBtn" class="w-100 btn btn-lg btn-primary btn-login" type="submit">
                                    <span id="btnText">Iniciar Sesión</span>
                                    <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true" style="display: none;"></span>
                                </button>
                                
                                <div class="divider my-4">
                                    <span class="divider-text">o continúa con</span>
                                </div>
                                
                                <div class="social-login-buttons">
                                    <button type="button" class="btn btn-outline-danger btn-social" disabled>
                                        <i class="bi bi-google me-2"></i>
                                        Google
                                        <small class="d-block">Próximamente</small>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-social" disabled>
                                        <i class="bi bi-facebook me-2"></i>
                                        Facebook
                                        <small class="d-block">Próximamente</small>
                                    </button>
                                </div>
                            </div>
                        </form>

                            <footer class="login-footer mt-4">
                                <div class="d-flex justify-content-center mb-3">
                                    <a class="forgot-password" id="forgot-password" href="recuperar-clave.php">
                                        <i class="bi bi-key me-1"></i>
                                        ¿Has olvidado tu contraseña?
                                    </a>
                                </div>

                                <div class="d-flex justify-content-center mb-3">
                                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSdiugXhzAj0Ysmt2gthO07tbvjxTA7CHcZqgzBpkefZC6T2qg/viewform" 
                                       class="support-link" target="_blank" rel="noopener noreferrer">
                                        <i class="bi bi-headset me-1"></i>
                                        ¿Necesitas ayuda?
                                    </a>
                                </div>

                                <div class="registration-section text-center">
                                    <p class="mb-2 text-muted">¿Eres una institución educativa?</p>
                                    <a href="registro.php" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-building me-1"></i>
                                        Crear cuenta institucional
                                    </a>
                                </div>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Galería de fotos rotando en el lado derecho -->
        <div class="logo-container vertical-center photo-gallery">
            <div class="photo-slide" style="background-image: url('../files-general/imagen-sintia1.jpg');"></div>
            <div class="photo-slide" style="background-image: url('../files-general/imagen-sintia2.jpg');"></div>
            <div class="photo-slide" style="background-image: url('../files-general/imagen-sintia3.jpg');"></div>
            <div class="photo-slide" style="background-image: url('../files-general/imagen-sintia4.jpg');"></div>
            <div class="photo-slide" style="background-image: url('../files-general/imagen-sintia5.jpg');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80');"></div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <!-- <script src="../config-general/assets-login-2023/js/pages/login.js"></script> -->
    
    <!-- Script para la galería de fotos rotando y login asíncrono -->
    <script>
        // Esperar a que jQuery esté completamente cargado
        $(document).ready(function () {
            console.log('Sistema de login asíncrono iniciado');
            
            // Configurar Select2 si existe
            if (typeof $.fn.select2 !== 'undefined') {
                $('.form-select').select2({
                    theme: 'bootstrap-5'
                });

                $('.select2').on('select2:open', function () {
                    $(this).parent().find('.select2-selection--single').addClass('form-control');
                });
            }
            
            // Galería de fotos rotando
            let currentSlide = 0;
            const slides = $('.photo-slide');
            const totalSlides = slides.length;
            
            function showNextSlide() {
                slides.removeClass('active');
                currentSlide = (currentSlide + 1) % totalSlides;
                slides.eq(currentSlide).addClass('active');
            }
            
            // Cambiar imagen cada 4 segundos
            if (totalSlides > 0) {
                setInterval(showNextSlide, 4000);
            }
            
            // Sistema de login asíncrono
            $('#loginForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                console.log('Formulario enviado');
                
                // Validar formulario
                if (!this.checkValidity()) {
                    this.classList.add('was-validated');
                    showMessage('Por favor completa todos los campos requeridos.', 'error');
                    return false;
                }
                
                // Obtener datos del formulario
                const formData = new FormData(this);
                
                // Cambiar estado del botón a "Enviando petición"
                setButtonState('loading', 'Enviando petición...');
                
                // Limpiar mensajes anteriores
                clearMessages();
                
                // Verificar que jQuery AJAX esté disponible
                if (typeof $.ajax === 'undefined') {
                    console.error('jQuery AJAX no está disponible');
                    setButtonState('error', 'Error de configuración');
                    showMessage('Error de configuración del sistema. Recarga la página.', 'error');
                    setTimeout(() => {
                        resetButton();
                    }, 3000);
                    return false;
                }
                
                // Realizar petición AJAX
                $.ajax({
                    url: 'controlador/autentico-async.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 30000, // 30 segundos timeout
                    
                    beforeSend: function() {
                        console.log('Enviando petición...');
                        // Cambiar estado del botón a "Validando datos"
                        setButtonState('loading', 'Validando datos...');
                    },
                    
                    success: function(response) {
                        console.log('Respuesta recibida:', response);
                        
                        // Verificar si la respuesta ya es un objeto o necesita ser parseada
                        let data;
                        if (typeof response === 'object') {
                            // Ya es un objeto JavaScript
                            data = response;
                        } else if (typeof response === 'string') {
                            try {
                                // Intentar parsear como JSON
                                data = JSON.parse(response);
                            } catch (e) {
                                console.error('Error parseando JSON:', e);
                                // Si no es JSON, verificar si es una redirección HTML
                                if (response.includes && (response.includes('window.location') || response.includes('Location:'))) {
                                    // Es una redirección, proceder normalmente
                                    setButtonState('success', '¡Acceso exitoso!');
                                    showMessage('Redirigiendo...', 'success');
                                    
                                    setTimeout(() => {
                                        window.location.href = 'bienvenida.php';
                                    }, 1500);
                                    return;
                                } else {
                                    // Error desconocido
                                    setButtonState('error', 'Error de conexión');
                                    showMessage('Error inesperado. Intenta nuevamente.', 'error');
                                    
                                    setTimeout(() => {
                                        resetButton();
                                    }, 3000);
                                    return;
                                }
                            }
                        } else {
                            console.error('Tipo de respuesta inesperado:', typeof response);
                            setButtonState('error', 'Error de conexión');
                            showMessage('Error inesperado. Intenta nuevamente.', 'error');
                            
                            setTimeout(() => {
                                resetButton();
                            }, 3000);
                            return;
                        }
                        
                        // Procesar la respuesta
                        if (data.success) {
                            // Login exitoso
                            setButtonState('success', '¡Acceso exitoso!');
                            showMessage('Redirigiendo...', 'success');
                            
                            // Redirigir después de un breve delay
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.href = 'bienvenida.php';
                                }
                            }, 1500);
                        } else {
                            // Error en el login
                            setButtonState('error', 'Error de acceso');
                            showMessage(data.message || 'Credenciales incorrectas', 'error');
                            
                            // Si hay URL de redirección (ej: formulario desbloqueo), redirigir
                            if (data.redirect) {
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 2000);
                                return;
                            }
                            
                            // Restaurar botón después de 3 segundos
                            setTimeout(() => {
                                resetButton();
                            }, 3000);
                        }
                    },
                    
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', status, error);
                        let errorMessage = 'Error de conexión. Verifica tu internet.';
                        
                        if (status === 'timeout') {
                            errorMessage = 'La petición tardó demasiado. Intenta nuevamente.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Error del servidor. Contacta soporte técnico.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Servicio no disponible. Intenta más tarde.';
                        }
                        
                        setButtonState('error', 'Error de conexión');
                        showMessage(errorMessage, 'error');
                        
                        // Agregar animación de shake al formulario
                        $('#loginForm').addClass('shake');
                        setTimeout(() => {
                            $('#loginForm').removeClass('shake');
                        }, 500);
                        
                        // Restaurar botón después de 3 segundos
                        setTimeout(() => {
                            resetButton();
                        }, 3000);
                    }
                });
                
                return false;
            });
            
            // Funciones auxiliares
            function setButtonState(state, text) {
                const btn = $('#loginBtn');
                const btnText = $('#btnText');
                const btnSpinner = $('#btnSpinner');
                
                if (btn.length === 0) {
                    console.error('Botón de login no encontrado');
                    return;
                }
                
                btn.prop('disabled', true);
                btn.removeClass('loading success error');
                
                if (state === 'loading') {
                    btn.addClass('loading');
                    if (btnSpinner.length > 0) {
                        btnSpinner.show();
                    }
                } else if (state === 'success') {
                    btn.addClass('success');
                    if (btnSpinner.length > 0) {
                        btnSpinner.hide();
                    }
                } else if (state === 'error') {
                    btn.addClass('error');
                    if (btnSpinner.length > 0) {
                        btnSpinner.hide();
                    }
                }
                
                if (btnText.length > 0) {
                    btnText.text(text);
                }
            }
            
            function resetButton() {
                const btn = $('#loginBtn');
                const btnText = $('#btnText');
                const btnSpinner = $('#btnSpinner');
                
                btn.prop('disabled', false);
                btn.removeClass('loading success error');
                if (btnSpinner.length > 0) {
                    btnSpinner.hide();
                }
                if (btnText.length > 0) {
                    btnText.text('Iniciar sesión');
                }
            }
            
            function showMessage(message, type) {
                const messageHtml = `
                    <div class="alert alert-dynamic ${type}" role="alert">
                        <i class="bi bi-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                `;
                
                $('#dynamicMessages').html(messageHtml);
                
                // Auto-ocultar mensajes de éxito después de 5 segundos
                if (type === 'success') {
                    setTimeout(() => {
                        $('#dynamicMessages').fadeOut();
                    }, 5000);
                }
            }
            
            function clearMessages() {
                $('#dynamicMessages').empty();
            }
            
            // Funcionalidad del botón de mostrar/ocultar contraseña
            $('.toggle-password').off('click').on('click', function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                }
            });
        });
    </script>

</body>

</html>