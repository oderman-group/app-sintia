<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
$archivoSubido = new Archivos;

$respuesta = array(
        "estado" => "ko",
        "mensaje" => "",
        "datos" => array(
                'ruta' => "",
                'archivo' => "",
                'extension' => "",
                'nombre'  => "",
                'tipo'  => "",
                'nombre_temporal' => "",
                'error'  => "",
                'tamaño'  => "",
            )
    );

if (isset($_POST['opcion']) and $_POST["opcion"] == "adjuntar_documento_estudiante" ) {   

    if (!empty($_FILES['uplDocumento']['name'])) {
        $archivoSubido->validarArchivo($_FILES['uplDocumento']['size'], $_FILES['uplDocumento']['name']);
        $explode=explode(".", $_FILES['uplDocumento']['name']);
        $extension = end($explode);
        $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_documento_') . "." . $extension;        
        while (file_exists($destino)) {
            $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_documento_') . "." . $extension;
        }
        $destino = "../files/documentos_adjuntos_estudiantes/" . $archivo;  
        if (move_uploaded_file($_FILES['uplDocumento']['tmp_name'], $destino)) {
            $respuesta["estado"] = "ok";
            $respuesta["mensaje"] = "informacion del archivo" ;
            $respuesta["datos"]["ruta"] = $destino; 
            $respuesta["datos"]["archivo"] = $archivo;
            $respuesta["datos"]["extension"] = $extension;
            $respuesta["datos"]["nombre"] = $_FILES['uplDocumento']['name'];
            $respuesta["datos"]["tipo"] = $_FILES['uplDocumento']['type'];
            $respuesta["datos"]["nombre_temporal"] = $_FILES['uplDocumento']['tmp_name'];
            $respuesta["datos"]["error"] = $_FILES['uplDocumento']['error'];
            $respuesta["datos"]["tamaño"] = $_FILES['uplDocumento']['size']; 
            
        }else {
            $respuesta["estado"]= "ko";
            $respuesta["mensaje"]= "No se puedo cargar el documento" ;
            $respuesta["datos"]["ruta"] = $destino; 
            $respuesta["datos"]["archivo"] = $archivo;
            $respuesta["datos"]["extension"] = $extension;
            $respuesta["datos"]["nombre"] = $_FILES['uplDocumento']['name'];
            $respuesta["datos"]["tipo"] = $_FILES['uplDocumento']['type'];
            $respuesta["datos"]["nombre_temporal"] = $_FILES['uplDocumento']['tmp_name'];
            $respuesta["datos"]["error"] = $_FILES['uplDocumento']['error'];
            $respuesta["datos"]["tamaño"] = $_FILES['uplDocumento']['size']; 
        }


    }else {
        $respuesta["estado"] = "ko";
        $respuesta["mensaje"] = "Seleccione un documento para cargar";
        $respuesta["datos"] = [];
    }

    echo json_encode($respuesta,512);


}else if (isset($_POST['opcion']) and $_POST["opcion"] == "eliminar_documento_estudiante" ) {   

    try {

        $destino = "../files/documentos_adjuntos_estudiantes/" . $_POST["ama_documento"];
        
        unlink($destino);
        $resultado["estado"] = "ok";
        $resultado["mensaje"] = "Se elimino correctamente el documento" ;
        $resultado["datos"]["archivo"] = $_POST["ama_documento"]; 

    } catch (Exception $e) {

        $resultado["estado"] = "ko";
        $resultado["mensaje"] = $e->getMessage() ;
        $resultado["datos"]["archivo"] = $_POST["ama_documento"];
    }
    
    echo json_encode($resultado,512);    

}else{

    $respuesta["estado"] = "ok";
    $respuesta["mensaje"] = "Esta no es una opcion ".$_POST['opcion'];
    $respuesta["datos"] = [];

    echo json_encode($respuesta,512);
}


?>