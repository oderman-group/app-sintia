<?php
// Evita cualquier salida antes de la redirección
ob_start(); 

// 301 Moved Permanently: Informa a los motores de búsqueda (SEO) y navegadores que la ubicación
// principal es la nueva ruta, no el directorio raíz.
header("Location: /main-app", true, 301); 

// Termina el buffer de salida y el script
ob_end_flush();
exit;