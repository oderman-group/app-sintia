<?php 
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DV0019';
include("../compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $sql = "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad WHERE pagp_id=? OR pagp_ruta=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_POST["dato"], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["dato"], PDO::PARAM_STR);
    $stmt->execute();
    $numDotos = $stmt->rowCount();
    
    if ($numDotos > 0) {
        $datosPaginas = $stmt->fetch(PDO::FETCH_ASSOC);
?>
    <script type="application/javascript">
        document.getElementById('codigo').style.backgroundColor = "#f8d7da";
        document.getElementById('nombrePagina').disabled = 'disabled';
        document.getElementById('tipoUsuario').disabled = 'disabled';
        document.getElementById('modulo').disabled = 'disabled';
        document.getElementById('rutaPagina').style.backgroundColor = "#f8d7da";
        document.getElementById('navegable').disabled = 'disabled';
        document.getElementById('crud').disabled = 'disabled';
        document.getElementById('btnGuardar').style.display = 'none';
    </script>   
    
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">&times;</button>

        <p>
            Este dato ya se encuentra registrado y asociado a la pagina <b><?=$datosPaginas['pagp_pagina'];?></b>.<br>
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
        document.getElementById('codigo').style.backgroundColor = "";
        document.getElementById('nombrePagina').disabled = '';
        document.getElementById('tipoUsuario').disabled = '';
        document.getElementById('modulo').disabled = '';
        document.getElementById('rutaPagina').style.backgroundColor = "";
        document.getElementById('navegable').disabled = '';
        document.getElementById('crud').disabled = '';
        document.getElementById('btnGuardar').style.display = 'block';
    </script> 
<?php    
}