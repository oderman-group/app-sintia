<?php
// Script para consultar los IDs reales de grados y grupos
include("session-compartida.php");

echo "<h2>🔍 CONSULTA DE IDs REALES - GRADOS Y GRUPOS</h2>";

$year = $_SESSION["bd"]; // Año actual
echo "<h3>Año actual: $year</h3>";

// Consultar grados disponibles
echo "<h3>📚 GRADOS DISPONIBLES:</h3>";
$consultaGrados = "SELECT gra_id, gra_nombre, gra_codigo FROM academico_grados WHERE institucion = {$config['conf_id_institucion']} AND year = '$year' AND gra_estado = 1 ORDER BY gra_id";
$resultadoGrados = mysqli_query($conexion, $consultaGrados);

if ($resultadoGrados && mysqli_num_rows($resultadoGrados) > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>gra_id</th><th>gra_nombre</th><th>gra_codigo</th></tr>";
    
    while ($grado = mysqli_fetch_array($resultadoGrados, MYSQLI_BOTH)) {
        echo "<tr>";
        echo "<td style='padding: 5px;'><strong>{$grado['gra_id']}</strong></td>";
        echo "<td style='padding: 5px;'>{$grado['gra_nombre']}</td>";
        echo "<td style='padding: 5px;'>{$grado['gra_codigo']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No se encontraron grados</p>";
}

// Consultar grupos disponibles
echo "<h3>👥 GRUPOS DISPONIBLES:</h3>";
$consultaGrupos = "SELECT gru_id, gru_nombre, gru_codigo FROM academico_grupos WHERE institucion = {$config['conf_id_institucion']} AND year = '$year' ORDER BY gru_id";
$resultadoGrupos = mysqli_query($conexion, $consultaGrupos);

if ($resultadoGrupos && mysqli_num_rows($resultadoGrupos) > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>gru_id</th><th>gru_nombre</th><th>gru_codigo</th></tr>";
    
    while ($grupo = mysqli_fetch_array($resultadoGrupos, MYSQLI_BOTH)) {
        echo "<tr>";
        echo "<td style='padding: 5px;'><strong>{$grupo['gru_id']}</strong></td>";
        echo "<td style='padding: 5px;'>{$grupo['gru_nombre']}</td>";
        echo "<td style='padding: 5px;'>{$grupo['gru_codigo']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No se encontraron grupos</p>";
}

// Consultar estudiantes matriculados con los IDs correctos
echo "<h3>🎓 ESTUDIANTES MATRICULADOS (ejemplo con primer grado y grupo):</h3>";

// Obtener el primer grado disponible
$consultaPrimerGrado = "SELECT gra_id FROM academico_grados WHERE institucion = {$config['conf_id_institucion']} AND year = '$year' AND gra_estado = 1 ORDER BY gra_id LIMIT 1";
$resultadoPrimerGrado = mysqli_query($conexion, $consultaPrimerGrado);

if ($resultadoPrimerGrado && mysqli_num_rows($resultadoPrimerGrado) > 0) {
    $primerGrado = mysqli_fetch_array($resultadoPrimerGrado, MYSQLI_BOTH);
    $graId = $primerGrado['gra_id'];
    
    // Obtener el primer grupo disponible
    $consultaPrimerGrupo = "SELECT gru_id FROM academico_grupos WHERE institucion = {$config['conf_id_institucion']} AND year = '$year' ORDER BY gru_id LIMIT 1";
    $resultadoPrimerGrupo = mysqli_query($conexion, $consultaPrimerGrupo);
    
    if ($resultadoPrimerGrupo && mysqli_num_rows($resultadoPrimerGrupo) > 0) {
        $primerGrupo = mysqli_fetch_array($resultadoPrimerGrupo, MYSQLI_BOTH);
        $gruId = $primerGrupo['gru_id'];
        
        echo "<p><strong>Probando con:</strong> gra_id = '$graId', gru_id = '$gruId'</p>";
        
        // Consultar estudiantes con estos IDs
        $consultaEstudiantes = "SELECT mat_id, mat_nombres, mat_primer_apellido, mat_segundo_apellido, mat_grado, mat_grupo 
                                FROM academico_matriculas 
                                WHERE institucion = {$config['conf_id_institucion']} 
                                AND year = '$year' 
                                AND mat_grado = '$graId' 
                                AND mat_grupo = '$gruId' 
                                AND mat_eliminado = 0 
                                LIMIT 5";
        
        $resultadoEstudiantes = mysqli_query($conexion, $consultaEstudiantes);
        
        if ($resultadoEstudiantes && mysqli_num_rows($resultadoEstudiantes) > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr style='background: #f0f0f0;'><th>mat_id</th><th>Nombre Completo</th><th>mat_grado</th><th>mat_grupo</th></tr>";
            
            while ($estudiante = mysqli_fetch_array($resultadoEstudiantes, MYSQLI_BOTH)) {
                $nombreCompleto = trim($estudiante['mat_nombres'] . ' ' . $estudiante['mat_primer_apellido'] . ' ' . $estudiante['mat_segundo_apellido']);
                echo "<tr>";
                echo "<td style='padding: 5px;'>{$estudiante['mat_id']}</td>";
                echo "<td style='padding: 5px;'>$nombreCompleto</td>";
                echo "<td style='padding: 5px;'>{$estudiante['mat_grado']}</td>";
                echo "<td style='padding: 5px;'>{$estudiante['mat_grupo']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ No hay estudiantes matriculados con estos IDs</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No se encontraron grupos</p>";
    }
} else {
    echo "<p style='color: red;'>❌ No se encontraron grados</p>";
}

echo "<hr>";
echo "<h3>💡 CONCLUSIÓN:</h3>";
echo "<p>Los IDs que necesitas usar son los valores de <strong>gra_id</strong> y <strong>gru_id</strong>, NO los nombres literales.</p>";
echo "<p>Por ejemplo, si quieres PRIMERO A, necesitas usar:</p>";
echo "<ul>";
echo "<li><strong>mat_grado</strong> = el valor de gra_id correspondiente a 'PRIMERO'</li>";
echo "<li><strong>mat_grupo</strong> = el valor de gru_id correspondiente a 'A'</li>";
echo "</ul>";
?>
