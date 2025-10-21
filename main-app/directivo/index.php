<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0004';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>

<style>
/* Estilos para el modal de bienvenida */
.welcome-option-card:hover .card {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
}

.welcome-option-card .card {
    transition: all 0.3s ease;
}

.welcome-option-card .card:hover {
    transform: translateY(-5px);
}

/* Animación de entrada para el modal */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

.modal.show .modal-dialog {
    transform: none;
}

/* Efectos de gradiente animado */
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.gradient-animated {
    background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
}

/* Responsive para móviles */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }
    
    .modal-body {
        padding: 20px !important;
    }
    
    .modal-header {
        padding: 20px !important;
    }
    
    .modal-footer {
        padding: 15px 20px !important;
    }
}
</style>
 </head>
 <!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			
			<?php include("../compartido/menu.php");?>
			
            <!-- start page content -->
            <div class="page-content-wrapper">
                <?php include("../compartido/index-contenido.php");?>
            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>

    <!-- JavaScript para el modal de bienvenida -->
    <script>
    // Esperar a que todo esté cargado
    $(window).on('load', function() {
        console.log('Página completamente cargada, verificando primer ingreso...');
        
        // Función para verificar si es el primer ingreso del directivo
        function verificarPrimerIngreso() {
            console.log('Iniciando verificación de primer ingreso...');
            
            $.ajax({
                url: 'ajax-verificar-primer-ingreso.php',
                type: 'POST',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    console.log('Respuesta del servidor:', response);
                    
                    if (response.esPrimerIngreso) {
                        console.log('Es primer ingreso, mostrando modal...');
                        // Mostrar el modal de bienvenida
                        $('#modalBienvenidaDirectivo').modal('show');
                        
                        // Agregar efecto de entrada
                        setTimeout(function() {
                            $('.welcome-option-card').each(function(index) {
                                $(this).css({
                                    'opacity': '0',
                                    'transform': 'translateY(30px)'
                                }).delay(index * 200).animate({
                                    'opacity': '1'
                                }, 600).css('transform', 'translateY(0)');
                            });
                        }, 300);
                    } else {
                        console.log('No es primer ingreso o no es directivo');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error en AJAX:', status, error);
                    console.log('Respuesta del servidor:', xhr.responseText);
                }
            });
        }
        
        // Verificar al cargar la página
        verificarPrimerIngreso();
    });
    
    // Función para navegar a las opciones
    function irA(url) {
        console.log('Navegando a:', url);
        // Cerrar el modal
        $('#modalBienvenidaDirectivo').modal('hide');
        
        // Redirigir después de un pequeño delay para la animación
        setTimeout(function() {
            window.location.href = url;
        }, 300);
    }
    
    // Función para cerrar el modal sin hacer nada
    function cerrarModal() {
        console.log('Cerrando modal...');
        // Marcar el modal como visto
        $.ajax({
            url: 'ajax-marcar-modal-visto.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Modal marcado como visto');
            },
            error: function() {
                console.log('Error al marcar modal como visto');
            }
        });
        
        $('#modalBienvenidaDirectivo').modal('hide');
    }
    
    // Marcar como visto cuando se cierre el modal por cualquier motivo
    $('#modalBienvenidaDirectivo').on('hidden.bs.modal', function() {
        console.log('Modal cerrado, marcando como visto...');
        $.ajax({
            url: 'ajax-marcar-modal-visto.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Modal marcado como visto');
            },
            error: function() {
                console.log('Error al marcar modal como visto');
            }
        });
    });
    </script>

    <!-- Modal de Bienvenida para Directivos -->
    <div class="modal fade" id="modalBienvenidaDirectivo" tabindex="-1" role="dialog" aria-labelledby="modalBienvenidaDirectivoLabel" data-backdrop="true" data-keyboard="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <!-- Header con gradiente -->
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 30px;">
                    <div class="text-center w-100">
                        <h2 class="modal-title mb-3" id="modalBienvenidaDirectivoLabel">
                            <i class="fa fa-star fa-2x mb-3"></i><br>
                            ¡Bienvenido a Sintia!
                        </h2>
                        <p class="mb-0" style="font-size: 1.1em; opacity: 0.9;">
                            Tu plataforma educativa está lista para comenzar
                        </p>
                        <div class="mt-3">
                            <small style="opacity: 0.8; font-size: 0.9em;">
                                <i class="fa fa-info-circle mr-1"></i>
                                Si no deseas ver este mensaje nuevamente, desplázate hasta abajo y haz clic en "Cerrar"
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Body del modal -->
                <div class="modal-body" style="padding: 40px; background: #f8f9fa;">
                    <!-- Opciones principales - 2 por fila -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="mb-4 text-center" style="color: #333;">¿Qué te gustaría hacer primero?</h4>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Opción 1: Crear Matrícula -->
                        <div class="col-md-6 mb-4">
                            <div class="welcome-option-card" onclick="irA('matriculas-agregar.php')" style="cursor: pointer;">
                                <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; transition: all 0.3s ease;">
                                    <div class="card-body text-center p-4" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                        <i class="fa fa-graduation-cap fa-3x mb-3"></i>
                                        <h5 class="card-title mb-2">Crear Matrícula</h5>
                                        <p class="card-text small mb-0">Registra nuevos estudiantes en el sistema</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Opción 2: Crear Usuarios -->
                        <div class="col-md-6 mb-4">
                            <div class="welcome-option-card" onclick="irA('usuarios-agregar.php')" style="cursor: pointer;">
                                <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; transition: all 0.3s ease;">
                                    <div class="card-body text-center p-4" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                                        <i class="fa fa-users fa-3x mb-3"></i>
                                        <h5 class="card-title mb-2">Crear Usuarios</h5>
                                        <p class="card-text small mb-0">Agrega docentes, directivos y personal</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Opción 3: Configurar Plataforma -->
                        <div class="col-md-6 mb-4">
                            <div class="welcome-option-card" onclick="irA('configuracion.php')" style="cursor: pointer;">
                                <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; transition: all 0.3s ease;">
                                    <div class="card-body text-center p-4" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                                        <i class="fa fa-cogs fa-3x mb-3"></i>
                                        <h5 class="card-title mb-2">Configurar Plataforma</h5>
                                        <p class="card-text small mb-0">Personaliza la configuración del sistema</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Opción 4: Información Institución -->
                        <div class="col-md-6 mb-4">
                            <div class="welcome-option-card" onclick="irA('institucion-editar.php')" style="cursor: pointer;">
                                <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; transition: all 0.3s ease;">
                                    <div class="card-body text-center p-4" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;">
                                        <i class="fa fa-building fa-3x mb-3"></i>
                                        <h5 class="card-title mb-2">Información Institución</h5>
                                        <p class="card-text small mb-0">Completa los datos de tu institución</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Video de YouTube - Ancho completo -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                                <div class="card-header text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                                    <h6 class="mb-0"><i class="fa fa-play-circle mr-2"></i>Video Tutorial</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe class="embed-responsive-item" 
                                                src="https://www.youtube.com/embed/dQw4w9WgXcQ" 
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen
                                                style="border-radius: 0 0 15px 15px;">
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón de documentación - Fila completa -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="text-center">
                                <a href="https://docs.plataformasintia.com" target="_blank" class="btn btn-outline-primary btn-lg">
                                    <i class="fa fa-book mr-2"></i> Documentación del Sistema
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Datos de contacto - Fila completa -->
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <div class="contact-info" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 400px; margin: 0 auto;">
                                    <h6 class="mb-3" style="color: #333;">¿Necesitas Ayuda?</h6>
                                    <div class="mb-2">
                                        <i class="fa fa-phone text-primary mr-2"></i>
                                        <strong>(313) 591-2073</strong>
                                    </div>
                                    <div>
                                        <i class="fa fa-envelope text-primary mr-2"></i>
                                        <strong>soporte@plataformasintia.com</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer simplificado -->
                <div class="modal-footer" style="background: #f8f9fa; border: none; padding: 20px 40px;">
                    <div class="row w-100">
                        <div class="col-12 text-center">
                            <button type="button" class="btn btn-secondary btn-lg" onclick="cerrarModal()">
                                <i class="fa fa-times mr-2"></i> Cerrar y No Mostrar Nuevamente
                            </button>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fa fa-info-circle mr-1"></i>
                                    Este mensaje no volverá a aparecer
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <script src="../../config-general/assets/plugins/sparkline/jquery.sparkline.js" ></script>
	<script src="../../config-general/assets/js/pages/sparkline/sparkline-data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
    <script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
    <!-- material -->
    <script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- chart js -->
    <script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js" ></script>
    <script src="../../config-general/assets/plugins/chart-js/utils.js" ></script>
    <script src="../../config-general/assets/js/pages/chart/chartjs/home-data.js" ></script>
    <!-- summernote -->
    <script src="../../config-general/assets/plugins/summernote/summernote.js" ></script>
    <script src="../../config-general/assets/js/pages/summernote/summernote-data.js" ></script>
    <!-- end js include path -->
	
  </body>

</html>