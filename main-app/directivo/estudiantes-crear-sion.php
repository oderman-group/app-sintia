<?php
    $modulo = 4;
    include("session.php");
    include("../modelo/conexion.php");
    include("../compartido/config.php");
        
    //ESTUDIANTE ACTUAL
    $consultaEstudianteActual = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id='".$_GET["id"]."'");
    
    $numEstudianteActual = mysqli_num_rows($consultaEstudianteActual);
    $datosEstudianteActual = mysqli_fetch_array($consultaEstudianteActual, MYSQLI_BOTH);
    
    $datosEstudianteActual["mat_ciudad_residencia"]=trim($datosEstudianteActual["mat_ciudad_residencia"]);
    
    //ACUDIENTE
    $consultaAcudiente=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$datosEstudianteActual["mat_acudiente"]."'");
    $acudiente = mysqli_fetch_array($consultaAcudiente, MYSQLI_BOTH);


    // API CREAR ESTUDIANTE
    $student = [
        "Documento" => $datosEstudianteActual["mat_codigo_tesoreria"],
        "Apellido1" => $datosEstudianteActual["mat_primer_apellido"],
        "Apellido2" => $datosEstudianteActual["mat_segundo_apellido"],
        "Nombre1" => $datosEstudianteActual["mat_nombres"],
        "NombreOtros" => $datosEstudianteActual["mat_nombre2"],
        "MunicipioResidencia" => $datosEstudianteActual["mat_ciudad_residencia"],
        "DireccionPrincipal" => $datosEstudianteActual["mat_direccion"],
        "TelefonoPrincipal" => $datosEstudianteActual["mat_telefono"],
        "Correo" => $datosEstudianteActual["mat_email"],
        "TelefonoCelular" => $datosEstudianteActual["mat_celular"],
        "EstratoSocial" => $datosEstudianteActual["mat_estrato"],
        "FechaNacimientoEstudiante" => $datosEstudianteActual["mat_fecha_nacimiento"],
        "SexoEstudiante" => $datosEstudianteActual["mat_genero"],

        "IdTipoDocumentoAcudiente" => $acudiente["uss_tipo_documento"],
        "DocumentoAcudiente" => $acudiente["uss_usuario"],
        "Apellido1Acudiente" => $acudiente["uss_apellido1"],
        "Apellido2Acudiente" => $acudiente["uss_apellido2"],
        "Nombre1Acudiente" => $acudiente["uss_nombre"],
        "NombreOtrosAcuediente" => $acudiente["uss_nombre2"],
        "SexoAcudiente" => $acudiente["uss_genero"],
        "DocumentoUsuario" => "1120354377",
        "Token" => "4E0D689E2DD74471A41611E1D7960EA5"
    ];
        
    $service_url = 'http://sion.icolven.edu.co/Services/SINTIAService.svc/CreateTercero';

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json",
            'method'  => 'POST',
            'content' => json_encode($student),
        )
    );

    $context  = stream_context_create($options);

    $result = file_get_contents($service_url, false, $context);

    $jsonObject = json_decode($result);
    
    foreach ($jsonObject as $k=>$v){
        //echo "$k : $v\n";
        if($k ==='Message'){
            $mensaje = $v;
        }

        if($k ==='State'){
            $estado = $v;
        }
    }

	echo '<script type="text/javascript">window.location.href="estudiantes.php?stadsion='.$estado.'&msgsion='.$mensaje.'";</script>';
	exit();