<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0139';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>

<style>
/* Estilos modernos para la guía de inicio */
.guia-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.guia-header {
    text-align: center;
    margin-bottom: 40px;
}

.guia-header h1 {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 15px;
    color: white;
}

.guia-header p {
    font-size: 18px;
    opacity: 0.95;
    max-width: 800px;
    margin: 0 auto;
}

.timeline-container {
    position: relative;
    padding: 20px 0;
}

.timeline-step {
    position: relative;
    margin-bottom: 40px;
    display: flex;
    gap: 25px;
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.timeline-step:nth-child(1) { animation-delay: 0.1s; }
.timeline-step:nth-child(2) { animation-delay: 0.2s; }
.timeline-step:nth-child(3) { animation-delay: 0.3s; }
.timeline-step:nth-child(4) { animation-delay: 0.4s; }
.timeline-step:nth-child(5) { animation-delay: 0.5s; }
.timeline-step:nth-child(6) { animation-delay: 0.6s; }
.timeline-step:nth-child(7) { animation-delay: 0.7s; }
.timeline-step:nth-child(8) { animation-delay: 0.8s; }

.step-icon {
    flex-shrink: 0;
    width: 70px;
    height: 70px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #667eea;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.timeline-step:hover .step-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 6px 25px rgba(0,0,0,0.15);
}

.step-number {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    color: white;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.step-content {
    flex: 1;
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    position: relative;
    transition: all 0.3s ease;
}

.timeline-step:hover .step-content {
    transform: translateX(5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.step-content::before {
    content: '';
    position: absolute;
    left: -10px;
    top: 30px;
    width: 0;
    height: 0;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    border-right: 10px solid white;
}

.step-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.step-description {
    font-size: 15px;
    color: #555;
    line-height: 1.7;
    margin-bottom: 15px;
}

.step-path {
    display: inline-block;
    background: linear-gradient(135deg, #667eea15, #764ba215);
    border-left: 4px solid #667eea;
    padding: 12px 18px;
    border-radius: 8px;
    font-size: 13px;
    color: #667eea;
    font-weight: 600;
    margin-top: 10px;
    font-family: 'Courier New', monospace;
}

.step-path i {
    margin-right: 8px;
}

.video-section {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.video-header {
    text-align: center;
    margin-bottom: 25px;
}

.video-header h3 {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.video-header p {
    font-size: 15px;
    color: #666;
}

.video-wrapper {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.video-wrapper iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 15px;
}

.progress-indicator {
    text-align: center;
    margin-top: 40px;
    padding: 25px;
    background: rgba(255,255,255,0.2);
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

.progress-indicator h4 {
    color: white;
    font-size: 18px;
    margin-bottom: 15px;
    font-weight: 600;
}

.progress-bar-custom {
    height: 12px;
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
    border-radius: 10px;
    width: 0%;
    transition: width 1s ease-out;
    box-shadow: 0 0 10px rgba(245, 87, 108, 0.5);
}

.step-badge {
    display: inline-block;
    padding: 4px 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-left: 10px;
}

.completion-card {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    margin-top: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(17, 153, 142, 0.3);
}

.completion-card h3 {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 15px;
}

.completion-card p {
    font-size: 16px;
    opacity: 0.95;
}

.btn-action-step {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-action-step:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

@media (max-width: 768px) {
    .timeline-step {
        flex-direction: column;
        gap: 15px;
    }
    
    .step-content::before {
        display: none;
    }
    
    .guia-container {
        padding: 25px;
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
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class="pull-left">
                                <div class="page-title">Cómo Empezar con SINTIA</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            
                            <!-- Video de Guía -->
                            <div class="video-section">
                                <div class="video-header">
                                    <h3><i class="fa fa-play-circle" style="color: #667eea;"></i> Video Tutorial Completo</h3>
                                    <p>Mira este video para una guía visual paso a paso</p>
                                </div>
                                <div class="video-wrapper">
                                    <iframe src="https://www.loom.com/embed/8eac333b167c48d98ca3b459e78faeac" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                </div>
                            </div>

                            <!-- Guía de Pasos -->
                            <div class="guia-container">
                                <div class="guia-header">
                                    <h1><i class="fa fa-rocket"></i> Primeros Pasos en SINTIA</h1>
                                    <p>Sigue esta guía paso a paso para configurar tu institución y comenzar a usar todas las funcionalidades de la plataforma. ¡Es más fácil de lo que piensas!</p>
                                </div>

                                <div class="timeline-container">
                                    <!-- Paso 1 -->
                                    <div class="timeline-step">
                                        <div class="step-icon">
                                            <i class="fa fa-cog"></i>
                                            <span class="step-number">1</span>
                                        </div>
                                        <div class="step-content">
                                            <div class="step-title">
                                                Configuración del Sistema
                                                <span class="step-badge">Esencial</span>
                                            </div>
                                            <div class="step-description">
                                                En primer lugar, organizaremos la configuración del sistema para tu institución. Aquí definirás parámetros importantes como el año académico actual, períodos, configuraciones de calificaciones y más.
                                            </div>
                                            <div class="step-path">
                                                <i class="fa fa-map-marker-alt"></i> Menú principal → Configuración → del sistema
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 2 -->
                                    <div class="timeline-step">
                                        <div class="step-icon">
                                            <i class="fa fa-users-cog"></i>
                                            <span class="step-number">2</span>
                                        </div>
                                        <div class="step-content">
                                            <div class="step-title">
                                                Crear Usuarios
                                                <span class="step-badge">Importante</span>
                                            </div>
                                            <div class="step-description">
                                                Ahora te sugerimos que crees los <strong>usuarios</strong> de tipo <strong>Directivo</strong> y los usuarios de tipo <strong>Docente</strong>. Estos usuarios tendrán acceso a las diferentes funcionalidades según su rol.
                                            </div>
                                            <div class="step-path">
                                                <i class="fa fa-map-marker-alt"></i> Menú principal → G. Administrativa → Usuarios → Agregar nuevo
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 3 -->
                                    <div class="timeline-step">
                                        <div class="step-icon">
                                            <i class="fa fa-school"></i>
                                            <span class="step-number">3</span>
                                        </div>
                                        <div class="step-content">
                                            <div class="step-title">
                                                Información de la Institución
                                                <span class="step-badge">Esencial</span>
                                            </div>
                                            <div class="step-description">
                                                ¡Buen trabajo! En este punto vamos a colocar la <strong>Información de la Institución</strong>. Esta información es importante porque aparecerá en varios informes que la plataforma te permite generar. Aquí también podrás definir los cargos o roles de algunos usuarios directivos.
                                            </div>
                                            <div class="step-path">
                                                <i class="fa fa-map-marker-alt"></i> Menú principal → Configuración → de la Institución
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 4 -->
                                    <div class="timeline-step">
                                        <div class="step-icon">
                                            <i class="fa fa-graduation-cap"></i>
                                            <span class="step-number">4</span>
                                        </div>
                                        <div class="step-content">
                                            <div class="step-title">
                                                Gestión de Cursos
                                            </div>
                                            <div class="step-description">
                                                Lo siguiente que te recomendamos es ir a la opción de <strong>Cursos</strong>, revisar los que la plataforma generó automáticamente y comprobar si esos son todos los que tu institución necesita. Si hace falta alguno, lo puedes crear allí mismo.
                                            </div>
                                            <div class="step-path">
                                                <i class="fa fa-map-marker-alt"></i> Menú principal → G. Académica → Cursos → Agregar nuevo
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 5 -->
                                    <div class="timeline-step">
                                        <div class="step-icon">
                                            <i class="fa fa-layer-group"></i>
                                            <span class="step-number">5</span>
                                        </div>
                                        <div class="step-content">
                                            <div class="step-title">
                                                Configurar Áreas
                                            </div>
                                            <div class="step-description">
                                                Este paso es parecido al anterior. Te recomendamos ir a la opción de <strong>Áreas</strong>, revisar las que la plataforma generó automáticamente y comprobar si esas son todas las que tu institución necesita. Si hace falta alguna, la puedes crear allí mismo.
                                            </div>
                                            <div class="step-path">
                                                <i class="fa fa-map-marker-alt"></i> Menú principal → G. Académica → Áreas → Agregar nuevo
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 6 -->
                                    <div class="timeline-step">
                                        <div class="step-icon">
                                            <i class="fa fa-book"></i>
                                            <span class="step-number">6</span>
                                        </div>
                                        <div class="step-content">
                                            <div class="step-title">
                                                Definir Asignaturas
                                            </div>
                                            <div class="step-description">
                                                Este paso es casi igual al anterior. Te recomendamos ir a la opción de <strong>Asignaturas</strong>, revisar las que la plataforma generó automáticamente y comprobar si esas son todas las que tu institución necesita. Si hace falta alguna, la puedes crear allí mismo.
                                            </div>
                                            <div class="step-path">
                                                <i class="fa fa-map-marker-alt"></i> Menú principal → G. Académica → Asignaturas → Agregar nuevo
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 7 -->
                                    <div class="timeline-step">
                                        <div class="step-icon">
                                            <i class="fa fa-chalkboard-teacher"></i>
                                            <span class="step-number">7</span>
                                        </div>
                                        <div class="step-content">
                                            <div class="step-title">
                                                Crear Cargas Académicas
                                                <span class="step-badge">Crítico</span>
                                            </div>
                                            <div class="step-description">
                                                ¡Felicidades por haber llegado a este punto! Ya casi hemos terminado. En este punto ya deberías tener la información básica para usar la plataforma. Lo siguiente es crear las <strong>Cargas Académicas</strong> que los docentes trabajarán para este año escolar. Allí debes relacionar al docente con un curso, grupo y asignatura.
                                            </div>
                                            <div class="step-path">
                                                <i class="fa fa-map-marker-alt"></i> Menú principal → G. Académica → Cargas académicas → Agregar nuevo
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 8 -->
                                    <div class="timeline-step">
                                        <div class="step-icon">
                                            <i class="fa fa-user-graduate"></i>
                                            <span class="step-number">8</span>
                                        </div>
                                        <div class="step-content">
                                            <div class="step-title">
                                                Gestionar Matrículas
                                                <span class="step-badge">Final</span>
                                            </div>
                                            <div class="step-description">
                                                ¡Excelente trabajo hasta ahora! Podemos pasar a las <strong>Matrículas</strong>. Es momento de crear a los estudiantes uno por uno o también puedes importarlos desde un archivo de Excel para que te sea más fácil y rápido (La plataforma te da la plantilla adecuada para hacer este llenado de datos y la posterior carga del archivo).<br><br>
                                                <strong>Nota importante:</strong> Si has creado cursos nuevos, diferentes a los que la plataforma había generado automáticamente, entonces después de importar el listado de estudiantes, debes relacionarlos con su curso correspondiente.
                                            </div>
                                            <div class="step-path">
                                                <i class="fa fa-map-marker-alt"></i> Menú principal → G. Académica → Matrículas → Agregar nuevo | Importar excel
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tarjeta de Finalización -->
                                <div class="completion-card">
                                    <h3><i class="fa fa-check-circle"></i> ¡Felicitaciones!</h3>
                                    <p>Una vez completados estos pasos, tu institución estará lista para usar todas las funcionalidades de SINTIA. Si necesitas ayuda adicional, no dudes en consultar nuestro manual de usuario o contactar con soporte técnico.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
</body>
</html>