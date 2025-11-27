<!-- Modal de selección de múltiples usuarios - Diseño Moderno -->
<style>
    .modal-usuarios-moderno .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    
    .modal-usuarios-moderno .modal-header {
        background: linear-gradient(135deg, #41c4c4 0%, #6017dc 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem 2rem;
        border: none;
    }
    
    .modal-usuarios-moderno .modal-title {
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
    }
    
    .modal-usuarios-moderno .btn-close-modal {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid white;
        color: white;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .modal-usuarios-moderno .btn-close-modal:hover {
        background: white;
        color: #6017dc;
    }
    
    .modal-usuarios-moderno .modal-body {
        padding: 2rem;
    }
    
    .modal-usuarios-moderno .info-text {
        color: #666;
        font-size: 1rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    
    .modal-usuarios-moderno .table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .modal-usuarios-moderno .table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .modal-usuarios-moderno .table thead th {
        border: none;
        padding: 1rem;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.875rem;
        letter-spacing: 0.5px;
    }
    
    .modal-usuarios-moderno .table tbody tr {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .modal-usuarios-moderno .table tbody tr:hover {
        background-color: rgba(65, 196, 196, 0.05);
        transform: scale(1.01);
    }
    
    .modal-usuarios-moderno .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid #dee2e6;
    }
    
    .modal-usuarios-moderno .custom-radio-modern {
        position: relative;
        display: inline-block;
    }
    
    .modal-usuarios-moderno .custom-radio-modern input[type="radio"] {
        opacity: 0;
        position: absolute;
    }
    
    .modal-usuarios-moderno .custom-radio-modern label {
        position: relative;
        padding-left: 35px;
        cursor: pointer;
        line-height: 24px;
        display: inline-block;
        color: transparent;
    }
    
    .modal-usuarios-moderno .custom-radio-modern label:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 24px;
        height: 24px;
        border: 2px solid #41c4c4;
        border-radius: 50%;
        background: white;
        transition: all 0.3s ease;
    }
    
    .modal-usuarios-moderno .custom-radio-modern input[type="radio"]:checked + label:before {
        background: linear-gradient(135deg, #41c4c4 0%, #6017dc 100%);
        border-color: #6017dc;
    }
    
    .modal-usuarios-moderno .custom-radio-modern input[type="radio"]:checked + label:after {
        content: '✓';
        position: absolute;
        left: 6px;
        top: 2px;
        color: white;
        font-size: 14px;
        font-weight: bold;
    }
    
    .modal-usuarios-moderno .badge-institucion {
        background: linear-gradient(135deg, #41c4c4 0%, #6017dc 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .modal-usuarios-moderno .modal-footer {
        border-top: 1px solid #dee2e6;
        padding: 1.5rem 2rem;
        background: #f8f9fa;
        border-radius: 0 0 20px 20px;
    }
    
    .modal-usuarios-moderno .btn-submit-usuarios {
        background: linear-gradient(135deg, #41c4c4 0%, #6017dc 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(65, 196, 196, 0.3);
    }
    
    .modal-usuarios-moderno .btn-submit-usuarios:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(65, 196, 196, 0.4);
    }
    
    .modal-usuarios-moderno .btn-submit-usuarios:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<div class="modal fade modal-usuarios-moderno" id="miModalUsuarios" tabindex="-1" role="dialog" aria-labelledby="modalUsuariosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalUsuariosLabel">
                    <i class="bi bi-people-fill me-2"></i>
                    Múltiples Usuarios Encontrados
                </h4>
                <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <form action="recuperar-clave-enviar-codigo.php" method="post" id="formUsuariosMultiples">
                <div class="modal-body">
                    <p class="info-text">
                        <i class="bi bi-info-circle me-2"></i>
                        Se encontraron varios usuarios con la información ingresada. Por favor, selecciona el usuario para el cual deseas recuperar la contraseña.
                    </p>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;"></th>
                                    <th>Institución</th>
                                    <th>Documento</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once(ROOT_PATH . "/main-app/class/Instituciones.php");
                                foreach ($listaUsuarios as $user) { 
                                    $institucion = Instituciones::getDataInstitution($user["institucion"]);
                                    $institucion = mysqli_fetch_array($institucion, MYSQLI_BOTH);
                                ?>
                                    <tr onclick="document.getElementById('usuario_<?php echo $user['id_nuevo']; ?>').checked = true;">
                                        <td>
                                            <div class="custom-radio-modern">
                                                <input 
                                                    type="radio" 
                                                    id="usuario_<?php echo $user['id_nuevo']; ?>" 
                                                    name="usuarioId" 
                                                    required 
                                                    value="<?php echo $user['id_nuevo']; ?>">
                                                <label for="usuario_<?php echo $user['id_nuevo']; ?>">Seleccionar</label>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-institucion">
                                                <?php echo htmlspecialchars($institucion["ins_siglas"]); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($user["uss_documento"]); ?></strong>
                                        </td>
                                        <td>
                                            <i class="bi bi-person me-1"></i>
                                            <?php echo htmlspecialchars($user["uss_usuario"]); ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-envelope me-1"></i>
                                            <?php echo htmlspecialchars($user["uss_email"]); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-submit-usuarios" id="btnSubmitUsuarios">
                        <i class="bi bi-send me-2"></i>
                        Continuar con Recuperación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Validar que se seleccione un usuario antes de enviar
    document.getElementById('formUsuariosMultiples')?.addEventListener('submit', function(e) {
        const radioSelected = document.querySelector('input[name="usuarioId"]:checked');
        if (!radioSelected) {
            e.preventDefault();
            alert('Por favor selecciona un usuario antes de continuar.');
        }
    });
    
    // Habilitar/deshabilitar botón según selección
    document.querySelectorAll('input[name="usuarioId"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('btnSubmitUsuarios').disabled = false;
        });
    });
</script>
