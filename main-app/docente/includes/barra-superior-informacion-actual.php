<nav class="navbar navbar-expand-lg navbar-dark mb-2" style="background-color: #ffffff;">


  <div class="navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">


      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:<?= $Plataforma->colorUno; ?>;">
          <b><?= strtoupper($frases[116][$datosUsuarioActual['uss_idioma']]); ?>: </b> <?= strtoupper($datosCargaActual['mat_nombre']); ?>
          <b><?= strtoupper($frases[26][$datosUsuarioActual['uss_idioma']]); ?>: </b> <?= strtoupper($datosCargaActual['gra_nombre'] . " " . $datosCargaActual['gru_nombre']); ?>
          <b><?= strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]); ?>: </b> <?= $periodoConsultaActual; ?>
          <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?php include("info-carga-actual.php"); ?>
        </div>
      </li>



      <li class="nav-item"> <a class="nav-link" href="#">|</a></li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
          Filtrar por periodos
          <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?php
          require_once(ROOT_PATH."/main-app/class/Grados.php");
          $porcentaje = 0;
          for ($i = 1; $i <= $datosCargaActual['gra_periodos']; $i++) {
            $periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosCargaActual['car_curso'], $i);
            
            $porcentajeGrado=25;
            if(!empty($periodosCursos['gvp_valor'])){
                $porcentajeGrado=$periodosCursos['gvp_valor'];
            }

            if ($i == $datosCargaActual['car_periodo']) $msjPeriodoActual = '- ACTUAL';
            else $msjPeriodoActual = '';
            if ($i == $periodoConsultaActual) $estiloResaltadoP = 'style="color: orange;"';
            else $estiloResaltadoP = '';
          ?>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($i); ?>&get=<?= base64_encode(100); ?>" <?= $estiloResaltadoP; ?>><?= strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]); ?> <?= $i; ?> (<?= $porcentajeGrado; ?>%) <?= $msjPeriodoActual; ?></a>
          <?php } ?>

        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#000;">
          Filtrar por asignaturas
          <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?php
          require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
          $cCargas = CargaAcademica::traerCargasDocentes($config, $_SESSION["id"]);
          $nCargas = mysqli_num_rows($cCargas);
          while ($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)) {
            if ($rCargas['car_id'] == $cargaConsultaActual) $estiloResaltado = 'style="color: orange;"';
            else $estiloResaltado = '';
            if ($rCargas['car_director_grupo'] == 1) {
              $estiloDG = 'style="font-weight: bold;"';
              $msjDG = ' - D.G';
            } else {
              $estiloDG = '';
              $msjDG = '';
            }
          ?>
            <a class="dropdown-item" href="<?= $_SERVER['PHP_SELF']; ?>?carga=<?= base64_encode($rCargas['car_id']); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>&get=<?= base64_encode(100); ?>" <?= $estiloResaltado; ?>><span <?= $estiloDG; ?>><?= $rCargas['car_posicion_docente']; ?>. <?= strtoupper($rCargas['mat_nombre']); ?> (<?= strtoupper($rCargas['gra_nombre'] . " " . $rCargas['gru_nombre']); ?>) <?= $msjDG; ?></span></a>
          <?php } ?>
        </div>
      </li>


    </ul>



  </div>
</nav>

<script>
// ============================================
// MANTENER TAB ACTIVO AL CAMBIAR FILTROS
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Detectar la página actual
    var paginaActual = window.location.pathname.split('/').pop();
    var claveLocalStorage = '';
    
    if (paginaActual.includes('calificaciones.php')) {
        claveLocalStorage = 'calificaciones_tab_activo';
    } else if (paginaActual.includes('clases.php')) {
        claveLocalStorage = 'clases_tab_activo';
    }
    
    // Función para obtener el tab activo
    function obtenerTabActivo() {
        if (!claveLocalStorage) return '';
        
        // Primero intentar desde URL
        var params = new URLSearchParams(window.location.search);
        var tabURL = params.get('tab');
        if (tabURL !== null) {
            return tabURL;
        }
        
        // Luego desde localStorage
        var tabLocalStorage = localStorage.getItem(claveLocalStorage);
        if (tabLocalStorage !== null) {
            return tabLocalStorage;
        }
        
        return '';
    }
    
    // Agregar parámetro tab a los enlaces de filtros
    setTimeout(function() {
        var tabActivo = obtenerTabActivo();
        
        // Modificar enlaces de periodos
        document.querySelectorAll('.dropdown-menu a[href*="periodo="]').forEach(function(link) {
            var hrefOriginal = link.getAttribute('href');
            
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                var tabActivoActual = obtenerTabActivo();
                var newHref = hrefOriginal;
                
                // Agregar parámetro tab si existe y no está ya en el href
                if (tabActivoActual && !newHref.includes('&tab=') && !newHref.includes('?tab=')) {
                    var separator = newHref.includes('?') ? '&' : '?';
                    newHref = hrefOriginal + separator + 'tab=' + tabActivoActual;
                } else if (tabActivoActual && newHref.includes('tab=')) {
                    // Reemplazar tab existente
                    newHref = newHref.replace(/[?&]tab=\d+/, '');
                    var separator = newHref.includes('?') ? '&' : '?';
                    newHref = newHref + separator + 'tab=' + tabActivoActual;
                }
                
                window.location.href = newHref;
            });
        });
        
        // Modificar enlaces de asignaturas
        document.querySelectorAll('.dropdown-menu a[href*="carga="]').forEach(function(link) {
            var hrefOriginal = link.getAttribute('href');
            
            // Solo modificar si no es un enlace de periodo (ya tiene periodo=)
            if (hrefOriginal.includes('periodo=')) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    var tabActivoActual = obtenerTabActivo();
                    var newHref = hrefOriginal;
                    
                    // Agregar parámetro tab si existe y no está ya en el href
                    if (tabActivoActual && !newHref.includes('&tab=') && !newHref.includes('?tab=')) {
                        var separator = newHref.includes('?') ? '&' : '?';
                        newHref = hrefOriginal + separator + 'tab=' + tabActivoActual;
                    } else if (tabActivoActual && newHref.includes('tab=')) {
                        // Reemplazar tab existente
                        newHref = newHref.replace(/[?&]tab=\d+/, '');
                        var separator = newHref.includes('?') ? '&' : '?';
                        newHref = newHref + separator + 'tab=' + tabActivoActual;
                    }
                    
                    window.location.href = newHref;
                });
            }
        });
        
        console.log('✅ Filtros configurados para mantener tab activo:', claveLocalStorage);
    }, 300);
});
</script>