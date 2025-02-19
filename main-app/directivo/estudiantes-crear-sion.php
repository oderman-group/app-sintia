<?php
    $modulo = 4;
    include("session.php");
    require_once("../class/Estudiantes.php");
    require_once("../class/UsuariosPadre.php");

    $id="";
    if(!empty($_GET["id"])){ $id=base64_decode($_GET["id"]);}
        
    //ESTUDIANTE ACTUAL
    $datosEstudianteActual =Estudiantes::obtenerDatosEstudiante($id);
    
    $datosEstudianteActual["mat_ciudad_residencia"]=trim($datosEstudianteActual["mat_ciudad_residencia"]);
    
    //ACUDIENTE
    $acudiente = UsuariosPadre::sesionUsuario($datosEstudianteActual["mat_acudiente"]);


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
        "DocumentoAcudiente" => $acudiente["uss_documento"],
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

	echo '<script type="text/javascript">window.location.href="estudiantes.php?stadsion='.base64_encode($estado).'&msgsion='.base64_encode($mensaje).'";</script>';
	exit();