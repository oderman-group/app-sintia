<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

$numDotos= Estudiantes::validarExistenciaEstudiante($_POST["nDoct"]);
if ($numDotos > 0) {
    $datosEstudianteActual = Estudiantes::obtenerDatosEstudiante($_POST["nDoct"]);
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