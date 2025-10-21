<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reporte de Cambios de Plataforma Sintia</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <style>
    .custom-container {
      margin-left: 250px; /* Space for the fixed sidebar */
      max-width: 800px;
      margin-top: 40px;
    }

    .header {
      text-align: center;
      margin-bottom: 40px;
    }

    .card-header, .badge-release-date {
      background-color: #ffffff;
      color: white;
    }

    .upcoming {
      background-color: #6017dc !important;
      color: white;
    }

    .card-body p {
      margin-bottom: 0;
    }

    .release-item {
      margin-bottom: 20px;
    }

    .release-footer {
      background-color: #f8f9fa;
      padding: 10px 15px;
      border-top: 1px solid #ddd;
      text-align: left;
      color: #6c757d;
      margin-bottom: 25px;
    }

    .badge-release-date {
      margin-left: 10px;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 250px;
      background-color: #f8f9fa;
      padding: 20px;
      overflow-y: auto;
      border-right: 1px solid #ddd;
    }

    .sidebar h5 {
      color: #333;
      margin-bottom: 20px;
    }

    .sidebar a {
      color: #007bff;
      text-decoration: none;
      display: block;
      margin-bottom: 10px;
    }

    .sidebar a:hover {
      text-decoration: underline;
    }

    .hotfix {
        color:tomato;
    }
  </style>
</head>
<body>
  <!-- Sidebar Menu -->
  <div class="sidebar">
    <h5>Navegación</h5>
    <a href="#version-3-0-0">Versión 3.0.0 - 15 de julio, 2024</a>
    <a href="#version-3-0-1">Versión 3.0.1 - 30 de agosto, 2024</a>
    <a href="#version-3-0-2">Versión 3.0.2 - 15 de septiembre, 2024</a>
    <a href="#version-3-0-3">Versión 3.0.3 - 23 de septiembre, 2024</a>
  </div>

  <!-- Main Content -->
  <div class="container custom-container">
    <div class="header">
      <h2>Historial de Cambios de la Plataforma Sintia</h2>
      <p class="text-muted">Consulta los cambios realizados y las fechas de los próximos lanzamientos</p>
    </div>
    
    <!-- Release History Section -->
    <div class="card mb-4">
      <div class="card-header">
        <h5>Historial de Cambios</h5>
      </div>
      <div class="card-body">

        <!-- First Release Item -->
        <div class="release-item">
          <h6 id="version-3-0-2">Versión 3.0.2 <span class="badge badge-pill badge-release-date">15 de septiembre, 2024</span></h6>
          <p>- [PES-1078] - Permitir actualizar documento desde el perfil a los usuarios directivos.</p>
          <p>- [PES-1078] - Dar la posibilidad de ver el listado de sedes y cambiarse entre ellas.</p>
        </div>
        <div class="release-footer">
          Video de presentación: <a href="https://www.loom.com/share/1b05fa3c4a5044fb8104c90622b2ba77" target="_blank">Ver video para la versión 3.0.2</a>
        </div>

        <!-- First Release Item -->
        <div class="release-item">
          <h6 id="version-3-0-1">Versión 3.0.1 <span class="badge badge-pill badge-release-date">30 de agosto, 2024</span></h6>
          <p>- [PES-1056] - Evitar que el docente coloque recuperación de periodo antes de que el periodo esté generado.</p>
          <p>- [PES-1063] - Bloquear tabla mientras el docente elimina indicadores y/o actividades.</p>
          <p>- [PES-1071] - Los directivos solo podrán mover las cargas académicas de los docentes a periodos anteriores.</p>
          <p>- [PES-1073] - Ya no se permitirá tener el permiso abierto en las cargas académicos de modificar en periodos anteriores.</p>
          <p class="hotfix">- [HF] - Incrementar la velocidad en el proceso de generación de informes por parte de los docentes.</p>
        </div>
        <div class="release-footer">
          Video de presentación: <a href="https://www.loom.com/share/2c1815de20c240e788a756cf9e207ab0" target="_blank">Ver video para la versión 3.0.1</a>
        </div>

        <!-- Second Release Item -->
        <div class="release-item">
          <h6 id="version-3-0-0">Versión 3.0.0 <span class="badge badge-pill badge-release-date">29 de mayo, 2024</span></h6>
          <p>- [PES-0680] - Informes - Formato de paz y salvo financiero.</p>
          <p>- [PES-0691] - Responder a comentarios en las clases.</p>
          <p>- [PES-0911] - Pagina para ver los detalles del plan y lo que han ido consumiendo.</p>
          <p>- [PES-1034] - Permitir a los directivos sacar un reporte de los cuestionarios.</p>
        </div>
        <div class="release-footer"></div>

      </div>
    </div>
    
    <!-- Upcoming Releases Section -->
    <div class="card">
      <div class="card-header upcoming">
        <h5>Próximos Lanzamientos</h5>
      </div>
      <div class="card-body">

        <div class="release-item">
          <h6 id="version-3-0-3">Versión 3.0.2 <span class="badge badge-pill badge-warning">23 de septiembre, 2024</span></h6>
          <p>- [PES-1038] - Mejorar la búsqueda en la pagina de matrículas y cargas académicas.</p>
          <p>- [PES-1042] - Permitir a los directivos generar los informes, por periodos, de las cargas académicas desde su panel.</p>
          <p>- [PES-1074] - Permitirle al docente visibilizar la nota anterior y la ultima modificación de las notas de los estudiantes.</p>
          <p>- [PES-1085] - Exportar a excel informe periódico para enviar a SEC. EDUCACIÓN</p>
          <p>- [PES-1086] - Permtir a los docentes asignar estudiantes específicos a las tareas en casa.</p>
        </div>
        <div class="release-footer"></div>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS, Popper.js, and jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
