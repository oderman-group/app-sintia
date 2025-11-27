<?php
// CONFIGURACIÓN DIRECTA - SIN DEPENDENCIAS QUE REDIRIJAN
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de base de datos (ajusta según tu configuración)
$servidorConexion = "localhost";
$usuarioConexion = "root";
$claveConexion = "";
$baseDatosServicios = "mobiliar_sintia_admin_local";

// Intentar conectar
try {
    $conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    mysqli_set_charset($conexion, "utf8mb4");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Determinar paso actual
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if ($step < 1) $step = 1;
if ($step > 3) $step = 3;

// Logo por defecto
$logoUrl = "../config-general/assets-login-2023/img/logo.png";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SINTIA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 2rem 0;
        }
        
        .card-register {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 900px;
            margin: 0 auto;
        }
        
        .progress-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            color: white;
            border-radius: 20px 20px 0 0;
        }
        
        .steps-indicator {
            display: flex;
            justify-content: space-around;
            margin-top: 1.5rem;
        }
        
        .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .step-indicator.active .step-circle {
            background: white;
            color: #667eea;
            transform: scale(1.1);
        }
        
        .step-indicator.completed .step-circle {
            background: #10b981;
            color: white;
        }
        
        .form-content {
            padding: 2rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .modulo-card {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 0.5rem;
        }
        
        .modulo-card:hover {
            border-color: #667eea;
        }
        
        .modulo-card.selected {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card-register">
            <div class="progress-header">
                <div class="text-center">
                    <img src="<?=$logoUrl?>" width="80" alt="Logo" onerror="this.style.display='none'">
                    <h2 class="mt-3">Registro SINTIA</h2>
                    <p class="mb-0">Paso <?=$step?> de 3</p>
                </div>
                
                <div class="steps-indicator">
                    <div class="step-indicator <?=$step >= 1 ? 'active' : ''?> <?=$step > 1 ? 'completed' : ''?>">
                        <div class="step-circle"><?=$step > 1 ? '✓' : '1'?></div>
                        <small class="d-block text-center mt-1">Datos</small>
                    </div>
                    <div class="step-indicator <?=$step >= 2 ? 'active' : ''?> <?=$step > 2 ? 'completed' : ''?>">
                        <div class="step-circle"><?=$step > 2 ? '✓' : '2'?></div>
                        <small class="d-block text-center mt-1">Institución</small>
                    </div>
                    <div class="step-indicator <?=$step >= 3 ? 'active' : ''?>">
                        <div class="step-circle">3</div>
                        <small class="d-block text-center mt-1">Resumen</small>
                    </div>
                </div>
            </div>
            
            <div class="form-content">
                <?php if ($step == 1): ?>
                    <!-- PASO 1 -->
                    <form method="GET" action="">
                        <input type="hidden" name="step" value="2">
                        
                        <h4 class="mb-4"><i class="bi bi-person-fill me-2"></i>Datos Personales</h4>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombres *</label>
                                <input type="text" class="form-control" name="nombre" 
                                       value="<?=htmlspecialchars($_GET['nombre'] ?? '')?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellidos *</label>
                                <input type="text" class="form-control" name="apellidos" 
                                       value="<?=htmlspecialchars($_GET['apellidos'] ?? '')?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?=htmlspecialchars($_GET['email'] ?? '')?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Celular (10 dígitos) *</label>
                            <input type="tel" class="form-control" name="celular" 
                                   pattern="[0-9]{10}" maxlength="10"
                                   value="<?=htmlspecialchars($_GET['celular'] ?? '')?>" required>
                            <small class="text-muted">Ejemplo: 3001234567</small>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Continuar <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="index.php" class="btn btn-link">Cancelar e ir al login</a>
                        </div>
                    </form>
                    
                <?php elseif ($step == 2): ?>
                    <!-- PASO 2 -->
                    <form method="GET" action="">
                        <input type="hidden" name="step" value="3">
                        <input type="hidden" name="nombre" value="<?=htmlspecialchars($_GET['nombre'] ?? '')?>">
                        <input type="hidden" name="apellidos" value="<?=htmlspecialchars($_GET['apellidos'] ?? '')?>">
                        <input type="hidden" name="email" value="<?=htmlspecialchars($_GET['email'] ?? '')?>">
                        <input type="hidden" name="celular" value="<?=htmlspecialchars($_GET['celular'] ?? '')?>">
                        
                        <h4 class="mb-4"><i class="bi bi-building me-2"></i>Información de la Institución</h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Institución *</label>
                            <input type="text" class="form-control" name="nombreIns" 
                                   value="<?=htmlspecialchars($_GET['nombreIns'] ?? '')?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciudad *</label>
                                <input type="text" class="form-control" name="ciudad" 
                                       value="<?=htmlspecialchars($_GET['ciudad'] ?? '')?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cargo *</label>
                                <input type="text" class="form-control" name="cargo" 
                                       value="<?=htmlspecialchars($_GET['cargo'] ?? '')?>" required>
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3"><i class="bi bi-puzzle me-2"></i>Módulos de Interés</h5>
                        <p class="text-muted small">Selecciona los módulos que más te interesen</p>
                        
                        <div class="row">
                            <?php
                            $query = "SELECT * FROM modulos WHERE mod_estado=1 ORDER BY mod_nombre ASC LIMIT 15";
                            $result = mysqli_query($conexion, $query);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($mod = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <div class="col-md-6">
                                        <div class="modulo-card">
                                            <label class="d-flex align-items-center mb-0 w-100">
                                                <input type="checkbox" name="modulos[]" 
                                                       value="<?=$mod['mod_id']?>" class="me-2">
                                                <div class="flex-grow-1">
                                                    <strong><?=htmlspecialchars($mod['mod_nombre'])?></strong>
                                                    <small class="d-block text-muted">ID: <?=$mod['mod_id']?></small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="col-12"><div class="alert alert-warning">No hay módulos disponibles</div></div>';
                            }
                            ?>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <a href="?step=1&nombre=<?=urlencode($_GET['nombre'] ?? '')?>&apellidos=<?=urlencode($_GET['apellidos'] ?? '')?>&email=<?=urlencode($_GET['email'] ?? '')?>&celular=<?=urlencode($_GET['celular'] ?? '')?>" 
                               class="btn btn-outline-secondary flex-fill">
                                <i class="bi bi-arrow-left"></i> Atrás
                            </a>
                            <button type="submit" class="btn btn-primary flex-fill">
                                Continuar <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                    
                <?php elseif ($step == 3): ?>
                    <!-- PASO 3: RESUMEN -->
                    <div class="text-center">
                        <i class="bi bi-check-circle text-success" style="font-size: 5rem;"></i>
                        <h3 class="mt-3">¡Datos Completados!</h3>
                        <p class="text-muted">Revisa la información antes de continuar</p>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <h6 class="mb-3"><strong>Resumen de tu registro:</strong></h6>
                        <p class="mb-1"><strong>Nombre:</strong> <?=htmlspecialchars($_GET['nombre'] ?? '')?> <?=htmlspecialchars($_GET['apellidos'] ?? '')?></p>
                        <p class="mb-1"><strong>Email:</strong> <?=htmlspecialchars($_GET['email'] ?? '')?></p>
                        <p class="mb-1"><strong>Celular:</strong> <?=htmlspecialchars($_GET['celular'] ?? '')?></p>
                        <hr>
                        <p class="mb-1"><strong>Institución:</strong> <?=htmlspecialchars($_GET['nombreIns'] ?? '')?></p>
                        <p class="mb-1"><strong>Ciudad:</strong> <?=htmlspecialchars($_GET['ciudad'] ?? '')?></p>
                        <p class="mb-1"><strong>Cargo:</strong> <?=htmlspecialchars($_GET['cargo'] ?? '')?></p>
                        <hr>
                        <p class="mb-0"><strong>Módulos seleccionados:</strong> <?=isset($_GET['modulos']) ? count($_GET['modulos']) : 0?></p>
                    </div>
                    
                    <form method="POST" action="registro-guardar.php" class="mt-4">
                        <input type="hidden" name="nombre" value="<?=$_GET['nombre'] ?? ''?>">
                        <input type="hidden" name="apellidos" value="<?=$_GET['apellidos'] ?? ''?>">
                        <input type="hidden" name="email" value="<?=$_GET['email'] ?? ''?>">
                        <input type="hidden" name="celular" value="<?=$_GET['celular'] ?? ''?>">
                        <input type="hidden" name="nombreIns" value="<?=$_GET['nombreIns'] ?? ''?>">
                        <input type="hidden" name="ciudad" value="<?=$_GET['ciudad'] ?? ''?>">
                        <input type="hidden" name="cargo" value="<?=$_GET['cargo'] ?? ''?>">
                        <input type="hidden" name="siglasInst" value="<?=strtoupper(substr($_GET['nombreIns'] ?? '', 0, 4))?>">
                        <?php if (isset($_GET['modulos'])): foreach ($_GET['modulos'] as $mod): ?>
                            <input type="hidden" name="modulos[]" value="<?=htmlspecialchars($mod)?>">
                        <?php endforeach; endif; ?>
                        
                        <div class="d-flex gap-2">
                            <a href="?step=2&<?=http_build_query($_GET)?>" class="btn btn-outline-secondary flex-fill">
                                <i class="bi bi-arrow-left"></i> Atrás
                            </a>
                            <button type="submit" class="btn btn-success btn-lg flex-fill">
                                <i class="bi bi-check-circle"></i> Completar Registro
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-white">Sistema de Registro - SINTIA <?=date('Y')?></p>
        </div>
    </div>
    
    <script>
        // Añadir clase 'selected' a módulos clickeados
        document.querySelectorAll('.modulo-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const cb = this.querySelector('input[type="checkbox"]');
                    cb.checked = !cb.checked;
                }
                this.classList.toggle('selected', this.querySelector('input').checked);
            });
            
            // Marcar como seleccionado si ya está checked
            if (this.querySelector('input').checked) {
                this.classList.add('selected');
            }
        });
        
        console.log('✅ Sistema de registro cargado - Paso:', <?=$step?>);
    </script>
</body>
</html>

