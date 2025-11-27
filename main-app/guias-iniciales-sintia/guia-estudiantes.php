<?php
// Página pública - No requiere autenticación
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Guía completa paso a paso para estudiantes de SINTIA. Aprende a usar todas las funcionalidades de la plataforma educativa.">
    <meta name="keywords" content="SINTIA, guía, tutorial, estudiantes, calificaciones, actividades, plataforma educativa">
    <link rel="shortcut icon" href="../../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>Guía para Estudiantes - SINTIA | Tutorial Completo</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #e9ecef;
            --success-color: #11998e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text-dark);
            line-height: 1.7;
            background: #ffffff;
            scroll-behavior: smooth;
        }

        /* Header Público */
        .public-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .public-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .public-header .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: white;
        }

        .public-header .logo img {
            height: 50px;
            width: auto;
        }

        .public-header .logo h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }

        .public-header .actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-outline-light {
            border: 2px solid white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            padding: 4rem 0;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-section p {
            font-size: 1.25rem;
            color: var(--text-light);
            max-width: 800px;
            margin: 0 auto 2rem;
        }

        .hero-badges {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .badge-custom {
            padding: 0.5rem 1.25rem;
            background: white;
            border: 2px solid var(--primary-color);
            border-radius: 25px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Main Container */
        .main-container {
            display: flex;
            gap: 2rem;
            max-width: 1400px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        /* Table of Contents */
        .toc-sidebar {
            flex: 0 0 300px;
            position: sticky;
            top: 100px;
            height: fit-content;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .toc-sidebar h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .toc-nav {
            list-style: none;
        }

        .toc-nav li {
            margin-bottom: 0.75rem;
        }

        .toc-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-light);
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .toc-nav a:hover,
        .toc-nav a.active {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            color: var(--primary-color);
            font-weight: 600;
            transform: translateX(5px);
        }

        .toc-nav a i {
            font-size: 0.85rem;
            width: 20px;
            text-align: center;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            max-width: 900px;
        }

        /* Section Styles */
        .content-section {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            scroll-margin-top: 120px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .section-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .section-title-group h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .section-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: var(--accent-color);
            color: white;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.5rem;
        }

        .section-description {
            font-size: 1.1rem;
            color: var(--text-light);
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        /* Path/Navigation Info */
        .section-path {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-left: 4px solid var(--primary-color);
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Media Sections */
        .media-section {
            margin: 2rem 0;
        }

        .media-section h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .media-section h4 i {
            color: var(--primary-color);
        }

        /* Image Gallery */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .image-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .image-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .image-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .image-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            padding: 1rem;
            font-size: 0.9rem;
        }

        /* Video Wrapper */
        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            margin-top: 1rem;
        }

        .video-wrapper iframe,
        .video-wrapper video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Completion Card */
        .completion-card {
            background: linear-gradient(135deg, var(--success-color) 0%, #38ef7d 100%);
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            color: white;
            margin-top: 3rem;
            box-shadow: 0 10px 30px rgba(17, 153, 142, 0.3);
        }

        .completion-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .completion-card p {
            font-size: 1.1rem;
            opacity: 0.95;
            margin-bottom: 1.5rem;
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
        }

        /* Footer */
        .public-footer {
            background: var(--text-dark);
            color: white;
            padding: 2rem 0;
            text-align: center;
            margin-top: 4rem;
        }

        .public-footer p {
            margin: 0;
            color: rgba(255,255,255,0.8);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .main-container {
                flex-direction: column;
            }

            .toc-sidebar {
                position: relative;
                top: 0;
                max-height: none;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .content-section {
                scroll-margin-top: 0;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
            }

            .hero-section h1 {
                font-size: 1.75rem;
            }

            .hero-section p {
                font-size: 1rem;
            }

            .content-section {
                padding: 1.5rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .image-gallery {
                grid-template-columns: 1fr;
            }
        }

        /* Print Styles */
        @media print {
            .public-header,
            .toc-sidebar,
            .back-to-top,
            .public-footer {
                display: none;
            }

            .content-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Public Header -->
    <header class="public-header">
        <div class="container">
            <a href="../../index.php" class="logo">
                <img src="../../sintia-gris.png" alt="SINTIA Logo">
                <h1>SINTIA</h1>
            </a>
            <div class="actions">
                <a href="../../index.php" class="btn btn-outline-light">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-user-graduate"></i> Guía Completa para Estudiantes</h1>
            <p>Bienvenido a la guía paso a paso de SINTIA para estudiantes. Aprende a usar todas las funcionalidades de la plataforma para acceder a tus clases, calificaciones, actividades y más.</p>
            <div class="hero-badges">
                <span class="badge-custom"><i class="fas fa-clock"></i> Tiempo estimado: 20 min</span>
                <span class="badge-custom"><i class="fas fa-list-check"></i> 8 Pasos principales</span>
                <span class="badge-custom"><i class="fas fa-video"></i> Videos incluidos</span>
            </div>
        </div>
    </section>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Table of Contents Sidebar -->
        <aside class="toc-sidebar">
            <h3><i class="fas fa-list"></i> Tabla de Contenido</h3>
            <ul class="toc-nav">
                <li><a href="#introduccion" class="toc-link active"><i class="fas fa-play-circle"></i> Introducción</a></li>
                <li><a href="#paso-1" class="toc-link"><i class="fas fa-circle"></i> Paso 1: Acceder a tus Cargas Académicas</a></li>
                <li><a href="#paso-2" class="toc-link"><i class="fas fa-circle"></i> Paso 2: Ver tus Calificaciones</a></li>
                <li><a href="#paso-3" class="toc-link"><i class="fas fa-circle"></i> Paso 3: Consultar Indicadores</a></li>
                <li><a href="#paso-4" class="toc-link"><i class="fas fa-circle"></i> Paso 4: Realizar Evaluaciones</a></li>
                <li><a href="#paso-5" class="toc-link"><i class="fas fa-circle"></i> Paso 5: Entregar Actividades</a></li>
                <li><a href="#paso-6" class="toc-link"><i class="fas fa-circle"></i> Paso 6: Consultar Clases y Contenido</a></li>
                <li><a href="#paso-7" class="toc-link"><i class="fas fa-circle"></i> Paso 7: Participar en Foros</a></li>
                <li><a href="#paso-8" class="toc-link"><i class="fas fa-circle"></i> Paso 8: Ver Resumen de Periodos</a></li>
                <li><a href="#finalizacion" class="toc-link"><i class="fas fa-check-circle"></i> Finalización</a></li>
            </ul>
        </aside>

        <!-- Content Area -->
        <main class="content-area">
            <!-- Introducción -->
            <section id="introduccion" class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="section-title-group">
                        <h2>Introducción</h2>
                        <span class="section-badge">Inicio</span>
                    </div>
                </div>
                <div class="section-description">
                    <p>Esta guía te llevará paso a paso por las principales funcionalidades de SINTIA diseñadas específicamente para estudiantes. Cada paso está diseñado para ser claro, directo y fácil de seguir.</p>
                    <p><strong>¿Qué encontrarás en esta guía?</strong></p>
                    <ul style="margin-left: 1.5rem; margin-top: 1rem;">
                        <li>Cómo acceder a tus cargas académicas y asignaturas</li>
                        <li>Cómo ver tus calificaciones y notas</li>
                        <li>Cómo consultar los indicadores de evaluación</li>
                        <li>Cómo realizar evaluaciones en línea</li>
                        <li>Cómo entregar actividades y tareas</li>
                        <li>Cómo acceder al contenido de tus clases</li>
                        <li>Cómo participar en foros de discusión</li>
                        <li>Cómo ver tu resumen académico por periodos</li>
                    </ul>
                </div>

                <!-- Video de introducción (opcional) -->
                <div class="media-section">
                    <h4><i class="fas fa-video"></i> Video Tutorial Completo</h4>
                    <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/BcGzd_Sr1oE?si=2xaNY4v-XbnlOVgK" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen></iframe>
                    </div>
                    <p style="margin-top: 1rem; color: var(--text-light); font-size: 0.9rem;">
                        <i class="fas fa-info-circle"></i> Mira este video para una guía visual completa de todos los pasos.
                    </p>
                </div>
            </section>

            <!-- Paso 1 -->
            <section id="paso-1" class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="section-title-group">
                        <h2>Paso 1: Acceder a tus Cargas Académicas</h2>
                        <span class="section-badge">Esencial</span>
                    </div>
                </div>
                <div class="section-description">
                    <p>El primer paso es acceder a tus <strong>cargas académicas</strong>. Aquí encontrarás todas las asignaturas, cursos y grupos en los que estás matriculado durante el año escolar.</p>
                    <p>Desde aquí podrás seleccionar una asignatura específica para ver su contenido, calificaciones, actividades y más.</p>
                </div>
                <div class="section-path">
                    <i class="fas fa-map-marker-alt"></i> Menú principal → Cargas académicas
                </div>

                <div class="media-section">
                    <h4><i class="fas fa-images"></i> Capturas de Pantalla</h4>
                    <div class="image-gallery">
                        <div class="image-item">
                            <img src="https://via.placeholder.com/400x300/667eea/ffffff?text=Cargas+Académicas" alt="Cargas Académicas">
                            <div class="image-caption">Lista de tus asignaturas</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 2 -->
            <section id="paso-2" class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="section-title-group">
                        <h2>Paso 2: Ver tus Calificaciones</h2>
                        <span class="section-badge">Importante</span>
                    </div>
                </div>
                <div class="section-description">
                    <p>En la sección de <strong>Calificaciones</strong> podrás ver todas tus notas organizadas por asignatura, periodo académico e indicador de evaluación.</p>
                    <p>Aquí podrás consultar tus calificaciones de forma detallada y ver tu desempeño en cada asignatura durante el año escolar.</p>
                </div>
                <div class="section-path">
                    <i class="fas fa-map-marker-alt"></i> Menú principal → Calificaciones
                </div>
            </section>

            <!-- Paso 3 -->
            <section id="paso-3" class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <div class="section-title-group">
                        <h2>Paso 3: Consultar Indicadores</h2>
                    </div>
                </div>
                <div class="section-description">
                    <p>Los <strong>Indicadores</strong> son los criterios que tus docentes utilizan para evaluarte. Puedes consultar qué indicadores se están evaluando en cada asignatura, como: participación, trabajos, exámenes, proyectos, etc.</p>
                    <p>Esto te ayudará a entender mejor cómo se está calificando tu desempeño académico.</p>
                </div>
                <div class="section-path">
                    <i class="fas fa-map-marker-alt"></i> Menú principal → Indicadores
                </div>
            </section>

            <!-- Paso 4 -->
            <section id="paso-4" class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="section-title-group">
                        <h2>Paso 4: Realizar Evaluaciones</h2>
                    </div>
                </div>
                <div class="section-description">
                    <p>En la sección de <strong>Evaluaciones</strong> encontrarás las pruebas y exámenes que tus docentes han creado para ti. Podrás realizar las evaluaciones en línea, ver tus resultados y consultar tus respuestas.</p>
                    <p>Algunas evaluaciones pueden tener tiempo límite, así que asegúrate de tener una buena conexión a internet antes de comenzar.</p>
                </div>
                <div class="section-path">
                    <i class="fas fa-map-marker-alt"></i> Menú principal → Evaluaciones
                </div>
            </section>

            <!-- Paso 5 -->
            <section id="paso-5" class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="section-title-group">
                        <h2>Paso 5: Entregar Actividades</h2>
                    </div>
                </div>
                <div class="section-description">
                    <p>Las <strong>Actividades</strong> son las tareas que tus docentes te asignan. Puedes ver las actividades pendientes, su fecha de entrega, y entregar tus trabajos adjuntando archivos.</p>
                    <p>Recuerda revisar regularmente esta sección para no perderte ninguna actividad importante y cumplir con los plazos de entrega.</p>
                </div>
                <div class="section-path">
                    <i class="fas fa-map-marker-alt"></i> Menú principal → Actividades
                </div>
            </section>

            <!-- Paso 6 -->
            <section id="paso-6" class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="section-title-group">
                        <h2>Paso 6: Consultar Clases y Contenido</h2>
                    </div>
                </div>
                <div class="section-description">
                    <p>En la sección de <strong>Clases</strong> encontrarás el contenido educativo que tus docentes han compartido contigo. Podrás acceder a material de estudio, recursos, unidades temáticas y más.</p>
                    <p>Este contenido te ayudará a estudiar y prepararte mejor para tus evaluaciones y actividades.</p>
                </div>
                <div class="section-path">
                    <i class="fas fa-map-marker-alt"></i> Menú principal → Clases
                </div>
            </section>

            <!-- Paso 7 -->
            <section id="paso-7" class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="section-title-group">
                        <h2>Paso 7: Participar en Foros</h2>
                    </div>
                </div>
                <div class="section-description">
                    <p>Los <strong>Foros</strong> son espacios de discusión donde puedes participar con tus compañeros y docentes. Puedes crear nuevos temas de discusión, responder a los temas existentes y compartir tus opiniones e ideas.</p>
                    <p>Los foros son una excelente forma de colaborar, aprender de otros y mantenerte activo en tu aprendizaje.</p>
                </div>
                <div class="section-path">
                    <i class="fas fa-map-marker-alt"></i> Menú principal → Foros
                </div>
            </section>

            <!-- Paso 8 -->
            <section id="paso-8" class="content-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="section-title-group">
                        <h2>Paso 8: Ver Resumen de Periodos</h2>
                        <span class="section-badge">Final</span>
                    </div>
                </div>
                <div class="section-description">
                    <p>El <strong>Resumen de Periodos</strong> te permite ver un resumen completo de tu desempeño académico durante cada periodo. Aquí podrás ver todas tus calificaciones, actividades entregadas, evaluaciones realizadas y tu promedio general.</p>
                    <p>Esta sección te ayuda a tener una visión clara de tu progreso académico a lo largo del año escolar.</p>
                </div>
                <div class="section-path">
                    <i class="fas fa-map-marker-alt"></i> Menú principal → Resumen de periodos
                </div>
            </section>

            <!-- Finalización -->
            <section id="finalizacion" class="content-section">
                <div class="completion-card">
                    <h3><i class="fas fa-check-circle"></i> ¡Felicitaciones!</h3>
                    <p>Ya conoces las principales funcionalidades de SINTIA para estudiantes. Ahora puedes comenzar a usar todas estas herramientas para aprovechar al máximo tu experiencia de aprendizaje. Si necesitas ayuda adicional, no dudes en consultar con tus docentes o contactar con soporte técnico.</p>
                    <div style="margin-top: 2rem;">
                        <a href="../../index.php" class="btn btn-outline-light" style="background: white; color: var(--success-color); border-color: white;">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión en SINTIA
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" aria-label="Volver arriba">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Footer -->
    <footer class="public-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> SINTIA. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Active TOC link on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.content-section');
            const tocLinks = document.querySelectorAll('.toc-link');
            const backToTopBtn = document.getElementById('backToTop');

            // Update active TOC link on scroll
            function updateActiveTOC() {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 150;
                    const sectionHeight = section.clientHeight;
                    if (window.pageYOffset >= sectionTop && window.pageYOffset < sectionTop + sectionHeight) {
                        current = section.getAttribute('id');
                    }
                });

                tocLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + current) {
                        link.classList.add('active');
                    }
                });
            }

            // Smooth scroll for TOC links
            tocLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            // Show/hide back to top button
            function toggleBackToTop() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.add('visible');
                } else {
                    backToTopBtn.classList.remove('visible');
                }
            }

            backToTopBtn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            window.addEventListener('scroll', function() {
                updateActiveTOC();
                toggleBackToTop();
            });

            // Initial call
            updateActiveTOC();
        });
    </script>
</body>
</html>

