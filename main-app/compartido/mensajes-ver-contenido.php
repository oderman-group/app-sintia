<?php
$filtro="AND ema_para='".$_SESSION["id"]."'";
if(isset($_GET["opt"]) AND base64_decode($_GET["opt"])==2){
	$filtro='';
}

$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}

$datosConsulta = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".social_emails
INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=ema_de AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
WHERE ema_id='".$idR."' $filtro"), MYSQLI_BOTH);


if($datosConsulta['ema_para']==$_SESSION["id"] and $datosConsulta['ema_visto']=='0'){
	mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".social_emails SET ema_visto=1, ema_fecha_visto=now() WHERE ema_id='".$idR."'");
	
}
?>
<div class="page-bar">
	<div class="page-title-breadcrumb">
		<div class=" pull-left">
			<div class="page-title"><?php if(isset($datosConsulta['ema_asunto'])){ echo $datosConsulta['ema_asunto'];}?></div>
		</div>
		<ol class="breadcrumb page-breadcrumb pull-right">
			<li><a class="parent-item" href="mensajes.php">Mensajes</a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active"><?php if(isset($datosConsulta['ema_asunto'])){ echo $datosConsulta['ema_asunto'];}?></li>
		</ol>
	</div>
</div>
<div class="inbox">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-topline-gray">
                                <div class="card-body no-padding height-9">
									<div class="row">
			                            <div class="col-md-3">
				                                <div class="inbox-sidebar">
				                                    <a href="mensajes-redactar.php" data-title="Compose" class="btn red compose-btn btn-block">
				                                        <i class="fa fa-edit"></i> Redactar </a>
				                                    <ul class="inbox-nav inbox-divider">
				                                        <li class="active"><a href="mensajes.php"><i class="fa fa-inbox"></i> Recibidos</a></li>
				                                        <li><a href="mensajes.php?opt=<?=base64_encode(2)?>"><i class="fa fa-envelope"></i> Enviados</a></li>
				                                    </ul>
				                                </div>
				                            </div>
			                            <div class="col-md-9">
			                                <div class="inbox-body">
			                                    <div class="inbox-header">
			                                        <!-- <h1 class="pull-left">Inbox</h1> -->
			                                        <div class="mail-option">
			                                            <div class="btn-group">
			                                                <a class="btn" href="#"> Reply </a> 
			                                                <a class="btn" href="#"> Reply all </a> 
			                                            </div>
			                                            <div class="btn-group">
			                                                <a class="btn" href="#"> Forward </a> 
			                                                <a class="btn" href="#"> Delete </a>
			                                            </div>
			                                            <div class="btn-group">
			                                                <a class="btn" href="#"> Mark as read </a> 
			                                            </div>
			                                        </div>
			                                    </div>
			                                    <div class="inbox-body no-pad">
			                                        <section class="mail-list">
			                                            <div class="mail-sender">
			                                            	<div class="mail-heading">
			                                                	<h4 class="vew-mail-header"><b><?=$datosConsulta['ema_asunto'];?></b></h4>
			                                                </div>
			                                                <hr>
															<div class="media">
																<a href="#" class="pull-left"> <img alt=""
																	src="../files/fotos/<?=$datosConsulta['uss_foto'];?>" class="img-circle" width="40">
																</a>
																<div class="media-body">
																	<span class="date pull-right"><?=$datosConsulta['ema_fecha'];?></span>
																	<h4 class="text-primary"><?=$datosConsulta['uss_nombre'];?></h4>
																	<small class="text-muted">De: <?=$datosConsulta['uss_email'];?></small>
																</div>
															</div>
														</div>
			                                            <div class="view-mail">
			                                                <p><?=$datosConsulta['ema_contenido'];?></p>
			                                            </div>
														
			                                            <div class="compose-btn pull-left">
			                                                <a href="mensajes-redactar.php?para=<?=base64_encode($datosConsulta['ema_de']);?>&asunto=<?=base64_encode('RE: '.$datosConsulta['ema_asunto'])?>" class="btn btn-sm btn-primary"><i
																class="fa fa-reply"></i> Responder</a>
			                                                <button class="btn btn-sm btn-default">
			                                                    <i class="fa fa-arrow-right"></i> Reenviar
			                                                </button>
			                                                <button class="btn  btn-sm btn-default tooltips" data-original-title="Print" type="button" data-toggle="tooltip" data-placement="top" title="">
			                                                    <i class="fa fa-print"></i>
			                                                </button>
			                                                <button class="btn btn-sm btn-default tooltips" data-original-title="Trash" data-toggle="tooltip" data-placement="top" title="">
			                                                    <i class="fa fa-trash-o"></i>
			                                                </button>
			                                            </div>
			                                        </section>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
								</div>
                            </div>
                        </div>
                    </div>
                </div>