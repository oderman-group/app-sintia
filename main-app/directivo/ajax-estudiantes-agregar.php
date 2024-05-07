<?php 
include("session.php");

$doctSinPuntos = strpos($_POST["nDoct"], '.') == true ? str_replace('.', '', $_POST["nDoct"]) : $_POST["nDoct"];
$doctConPuntos = strpos($_POST["nDoct"], '.') !== true && is_numeric($_POST["nDoct"]) ? str_replace('.', '', $_POST["nDoct"]) : $_POST["nDoct"];
try{
    $consultaDoc=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas
    WHERE (mat_documento ='".$doctSinPuntos."' OR mat_documento ='".$doctConPuntos."') AND mat_eliminado=0 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$numDotos=mysqli_num_rows($consultaDoc);
if ($numDotos > 0) {
    require_once("../class/Estudiantes.php");
    $datosEstudianteActual = mysqli_fetch_array($consultaDoc, MYSQLI_BOTH);
    $nombreEstudiante = Estudiantes::NombreCompletoDelEstudiante($datosEstudianteActual);
?>
    <script type="application/javascript">
        document.getElementById('apellido1').disabled = 'disabled';
        document.getElementById('apellido2').disabled = 'disabled';
        document.getElementById('nombres').disabled = 'disabled';
        document.getElementById('nDoc').style.backgroundColor = "#f8d7da";
    </script>   
    
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">&times;</button>

        <p>
            Este número de documento se encuentra registrado y asociado al estudiante <b><?=$nombreEstudiante;?></b>.<br>
            ¿Desea mostrar toda la información del estudiante?
        </p>
        
        <p style="margin-top:10px;">
            <div class="btn-group">
                <a href="estudiantes-editar.php?id=<?=base64_encode($datosEstudianteActual['mat_id']);?>" id="addRow" class="btn deepPink-bgcolor">
                    Sí, deseo mostrar la información
                </a>
            </div>
        </p>

    </div>
<?php
    exit();
}else{
?>
    <script type="application/javascript">
        document.getElementById('apellido1').disabled = '';
        document.getElementById('apellido2').disabled = '';
        document.getElementById('nombres').disabled = '';
        document.getElementById('nDoc').style.backgroundColor = "";
    </script> 
<?php    
}