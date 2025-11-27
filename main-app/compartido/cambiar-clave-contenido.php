<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

<style>
/* Estilos modernos para cambio de contraseña */
.password-change-container {
    max-width: 900px;
    margin: 0 auto;
}

.security-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.security-header h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 15px;
    color: white;
}

.security-header p {
    font-size: 16px;
    opacity: 0.95;
    margin-bottom: 0;
}

.security-icon {
    font-size: 60px;
    margin-bottom: 20px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.form-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.form-card-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 3px solid #f0f0f0;
}

.form-card-header h3 {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.form-card-header p {
    font-size: 15px;
    color: #666;
    margin-bottom: 0;
}

.password-field-group {
    margin-bottom: 30px;
}

.password-label {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.password-label i {
    color: #667eea;
}

.password-input-wrapper {
    position: relative;
}

.password-input-wrapper input {
    height: 50px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 12px 50px 12px 20px;
    font-size: 15px;
    transition: all 0.3s ease;
    width: 100%;
}

.password-input-wrapper input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
}

.password-toggle-btn {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    font-size: 18px;
    padding: 5px;
    transition: color 0.3s ease;
}

.password-toggle-btn:hover {
    color: #667eea;
}

.password-feedback {
    margin-top: 12px;
    font-size: 14px;
    padding: 12px 16px;
    border-radius: 10px;
    display: none;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.password-feedback.success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
    display: block;
}

.password-feedback.error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
    display: block;
}

.password-feedback.warning {
    background: #fff3cd;
    color: #856404;
    border-left: 4px solid #ffc107;
    display: block;
}

.password-strength {
    margin-top: 12px;
}

.strength-bar {
    height: 8px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 8px;
}

.strength-bar-fill {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 10px;
}

.strength-bar-fill.weak {
    width: 33%;
    background: linear-gradient(90deg, #ff6b6b, #ee5a6f);
}

.strength-bar-fill.medium {
    width: 66%;
    background: linear-gradient(90deg, #ffd93d, #f9ca24);
}

.strength-bar-fill.strong {
    width: 100%;
    background: linear-gradient(90deg, #6bcf7f, #51cf66);
}

.strength-text {
    font-size: 13px;
    font-weight: 600;
}

.strength-text.weak { color: #ff6b6b; }
.strength-text.medium { color: #ffd93d; }
.strength-text.strong { color: #51cf66; }

.requirements-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.requirements-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.requirements-header i {
    font-size: 24px;
    color: #667eea;
}

.requirements-header h4 {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.requirement-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: white;
    border-radius: 12px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.requirement-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.requirement-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
}

.requirement-icon.pending {
    background: #e0e0e0;
    color: #999;
}

.requirement-icon.valid {
    background: #d4edda;
    color: #28a745;
}

.requirement-text {
    font-size: 14px;
    color: #555;
    flex: 1;
}

.btn-submit-password {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    color: white;
    padding: 15px 40px;
    font-size: 16px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-submit-password:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-submit-password:disabled {
    background: #e0e0e0;
    color: #999;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.security-tip {
    background: linear-gradient(135deg, #f093fb15, #f5576c15);
    border-left: 4px solid #f093fb;
    padding: 20px;
    border-radius: 12px;
    margin-top: 20px;
}

.security-tip h5 {
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.security-tip p {
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
    line-height: 1.6;
}

.security-tip p:last-child {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .security-header {
        padding: 30px 20px;
    }
    
    .form-card {
        padding: 25px 20px;
    }
}
</style>

<div class="col-md-12">
    <div class="password-change-container">
        
        <!-- Encabezado de Seguridad -->
        <div class="security-header">
            <div class="security-icon">
                <i class="fa fa-shield-alt"></i>
            </div>
            <h2><?=$frases[253][$datosUsuarioActual['uss_idioma']];?></h2>
            <p>Mantén tu cuenta segura actualizando tu contraseña regularmente</p>
        </div>

        <div class="row">
            <!-- Requisitos y Tips -->
            <div class="col-lg-4 col-md-12">
                <div class="requirements-card">
                    <div class="requirements-header">
                        <i class="fa fa-info-circle"></i>
                        <h4>Requisitos</h4>
                    </div>
                    
                    <div class="requirement-item">
                        <div class="requirement-icon pending" id="req-length">
                            <i class="fa fa-circle"></i>
                        </div>
                        <div class="requirement-text">Entre 8 y 20 caracteres</div>
                            </div>
                    
                    <div class="requirement-item">
                        <div class="requirement-icon pending" id="req-chars">
                            <i class="fa fa-circle"></i>
                        </div>
                        <div class="requirement-text">Solo a-z, A-Z, 0-9, . y $</div>
                    </div>
                </div>

                <div class="security-tip">
                    <h5><i class="fa fa-lightbulb"></i> Consejos de Seguridad</h5>
                    <p><strong>✓</strong> Usa una combinación de letras mayúsculas y minúsculas</p>
                    <p><strong>✓</strong> Incluye números para mayor seguridad</p>
                    <p><strong>✓</strong> No uses información personal obvia</p>
                    <p><strong>✓</strong> Cambia tu contraseña periódicamente</p>
                </div>
            </div>

            <!-- Formulario -->
            <div class="col-lg-8 col-md-12">
                <?php include("../../config-general/mensajes-informativos.php"); ?>
                
                <div class="form-card">
                    <div class="form-card-header">
                        <h3><i class="fa fa-key"></i> Nueva Contraseña</h3>
                        <p>Completa los campos a continuación para actualizar tu contraseña</p>
                    </div>

                    <form action="../compartido/clave-actualizar.php" method="post" enctype="multipart/form-data" id="formCambiarClave">
                        <?php echo Csrf::campoHTML(); ?>

                        <!-- Contraseña Actual -->
                        <div class="password-field-group">
                            <label class="password-label">
                                <i class="fa fa-lock"></i> Contraseña Actual
                            </label>
                            <div class="password-input-wrapper">
                                <input 
                                    type="password" 
                                    name="claveActual" 
                                    id="claveActual" 
                                    oninput="validarClaveActual(this)" 
                                    data-clave-actual="<?=$datosUsuarioActual['uss_clave']?>" 
                                    class="form-control" 
                                    placeholder="Ingresa tu contraseña actual"
                                    required
                                >
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('claveActual', 'icoVerActual')">
                                    <i class="fa fa-eye" id="icoVerActual"></i>
                                </button>
                            </div>
                            <div id="respuestaClaveActual" class="password-feedback"></div>
                        </div>

                        <!-- Contraseña Nueva -->
                        <div class="password-field-group">
                            <label class="password-label">
                                <i class="fa fa-key"></i> Nueva Contraseña
                            </label>
                            <div class="password-input-wrapper">
                                <input 
                                    type="password" 
                                    name="claveNueva" 
                                    id="claveNueva" 
                                    oninput="validarClaveNueva(this); checkPasswordStrength(this.value)" 
                                    class="form-control" 
                                    placeholder="Ingresa tu nueva contraseña"
                                    required
                                >
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('claveNueva', 'icoVerNueva')">
                                    <i class="fa fa-eye" id="icoVerNueva"></i>
                                </button>
                            </div>
                            <div id="respuestaClaveNueva" class="password-feedback"></div>
                            <div class="password-strength" id="passwordStrength" style="display: none;">
                                <div class="strength-bar">
                                    <div class="strength-bar-fill" id="strengthBarFill"></div>
                                </div>
                                <div class="strength-text" id="strengthText"></div>
                    </div>
                </div>

                        <!-- Confirmar Contraseña -->
                        <div class="password-field-group">
                            <label class="password-label">
                                <i class="fa fa-check-circle"></i> Confirmar Nueva Contraseña
                            </label>
                            <div class="password-input-wrapper">
                                <input 
                                    type="password" 
                                    name="claveNuevaDos" 
                                    id="claveNuevaDos" 
                                    oninput="claveNuevaConfirmar(this)" 
                                    class="form-control" 
                                    placeholder="Confirma tu nueva contraseña"
                                    required
                                >
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('claveNuevaDos', 'icoVerNuevaDos')">
                                    <i class="fa fa-eye" id="icoVerNuevaDos"></i>
                                </button>
                            </div>
                            <div id="respuestaConfirmacionClaveNueva" class="password-feedback"></div>
                </div>

                        <button type="submit" class="btn-submit-password" id="btnEnviar">
                            <i class="fa fa-save"></i>
                            <span>Guardar Cambios</span>
                </button>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

<script src="../js/CambiarClave.js"></script>
<script>
// Función mejorada para toggle de visibilidad
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Función para verificar fortaleza de contraseña
function checkPasswordStrength(password) {
    const strengthContainer = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('strengthBarFill');
    const strengthText = document.getElementById('strengthText');
    const reqLength = document.getElementById('req-length');
    const reqChars = document.getElementById('req-chars');
    
    if (!password || password.length === 0) {
        strengthContainer.style.display = 'none';
        reqLength.className = 'requirement-icon pending';
        reqLength.innerHTML = '<i class="fa fa-circle"></i>';
        reqChars.className = 'requirement-icon pending';
        reqChars.innerHTML = '<i class="fa fa-circle"></i>';
        return;
    }
    
    strengthContainer.style.display = 'block';
    
    // Verificar longitud
    const lengthValid = password.length >= 8 && password.length <= 20;
    if (lengthValid) {
        reqLength.className = 'requirement-icon valid';
        reqLength.innerHTML = '<i class="fa fa-check"></i>';
    } else {
        reqLength.className = 'requirement-icon pending';
        reqLength.innerHTML = '<i class="fa fa-circle"></i>';
    }
    
    // Verificar caracteres válidos
    const charsValid = /^[a-zA-Z0-9.$]+$/.test(password);
    if (charsValid) {
        reqChars.className = 'requirement-icon valid';
        reqChars.innerHTML = '<i class="fa fa-check"></i>';
    } else {
        reqChars.className = 'requirement-icon pending';
        reqChars.innerHTML = '<i class="fa fa-circle"></i>';
    }
    
    // Calcular fortaleza
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[.$]/.test(password)) strength++;
    
    // Actualizar barra
    strengthBar.className = 'strength-bar-fill';
    strengthText.className = 'strength-text';
    
    if (strength <= 2) {
        strengthBar.classList.add('weak');
        strengthText.classList.add('weak');
        strengthText.textContent = 'Débil';
    } else if (strength <= 4) {
        strengthBar.classList.add('medium');
        strengthText.classList.add('medium');
        strengthText.textContent = 'Media';
    } else {
        strengthBar.classList.add('strong');
        strengthText.classList.add('strong');
        strengthText.textContent = 'Fuerte';
    }
}
</script>