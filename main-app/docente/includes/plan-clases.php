<div class="panel">
        <header class="panel-heading panel-heading-purple">PLAN DE CLASES</header>
        
        <div class="panel-body">
            <p>Puedes reemplazar el plan actual si ya tienes uno montado.</p>
            <form action="plan-clases-guardar.php" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                    <div class="col-sm-12">
                        <input type="file" name="file" class="form-control" onChange="archivoPeso(this)" <?=$disabled;?>>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary" value="Guardar cambios" <?=$disabled;?>>
            </form>
            <?php
            require_once(ROOT_PATH."/main-app/class/Clases.php");
            $pclase = Clases::traerPlanClase($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
            if(!empty($pclase) && file_exists(ROOT_PATH.'/main-app/files/pclase/' . $pclase['pc_plan'])){
            ?>
            <hr>
            <a href="../files/pclase/<?=$pclase['pc_plan'];?>" target="_blank"><i class="fa fa-download"></i> <?=$pclase['pc_plan'];?></a>
            <?php }?>
        </div>
</div>