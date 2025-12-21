<?php 
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DV0022';
include("../compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar que la variable baseDatosServicios esté definida
    if (!isset($baseDatosServicios) || empty($baseDatosServicios)) {
        throw new Exception('Variable baseDatosServicios no está definida');
    }
    
    $sql = "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad 
            WHERE pagp_id!=? AND pagp_ruta=? AND pagp_tipo_usuario=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_POST["idPagina"], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["dato"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_POST["tipoUss"], PDO::PARAM_STR);
    $stmt->execute();
    $numDotos = $stmt->rowCount();
    
    if ($numDotos > 0) {
        $datosPaginas = $stmt->fetch(PDO::FETCH_ASSOC);
?>
    <script type="application/javascript">
        document.getElementById('nombrePagina').setAttribute('disabled', 'disabled');
        document.getElementById('tipoUsuario').setAttribute('disabled', 'disabled');
        document.getElementById('modulo').setAttribute('disabled', 'disabled');
        document.getElementById('rutaPagina').style.backgroundColor = "#f8d7da";
        document.getElementById('navegable').setAttribute('disabled', 'disabled');
        document.getElementById('crud').setAttribute('disabled', 'disabled');
        document.getElementById('urlYoutube').setAttribute('disabled', 'disabled');
        document.getElementById('btnGuardar').style.display = 'none';
    </script>   
    
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">&times;</button>

        <p>
            Esta ruta ya se encuentra registrada y asociada a la pagina <b><?=$datosPaginas['pagp_pagina'];?></b>.<br>
            ¿Desea mostrar toda la información de la pagina?
        </p>
        
        <p style="margin-top:10px;">
            <div class="btn-group">
                <a href="dev-paginas-editar.php?idP=<?=$datosPaginas['pagp_id'];?>" id="addRow" class="btn deepPink-bgcolor">
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
        document.getElementById('nombrePagina').removeAttribute('disabled');
        document.getElementById('tipoUsuario').removeAttribute('disabled');
        document.getElementById('modulo').removeAttribute('disabled');
        document.getElementById('rutaPagina').style.backgroundColor = "";
        document.getElementById('navegable').removeAttribute('disabled');
        document.getElementById('crud').removeAttribute('disabled');
        document.getElementById('urlYoutube').removeAttribute('disabled');
        document.getElementById('btnGuardar').style.display = 'block';
    </script> 
<?php    
}
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}