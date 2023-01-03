<?php
if(isset($_GET["opt"])&&$_GET["opt"]==2){
	$consulta = mysqli_query($conexion, "SELECT * FROM social_emails
	INNER JOIN usuarios ON uss_id=ema_para
	WHERE ema_de='".$_SESSION["id"]."' AND ema_eliminado_de='0'
	ORDER BY ema_id DESC
	");
	$opt=$_GET["opt"];
}else{
	$consulta = mysqli_query($conexion, "SELECT * FROM social_emails
	INNER JOIN usuarios ON uss_id=ema_de
	WHERE ema_para='".$_SESSION["id"]."' AND ema_eliminado_para='0'
	ORDER BY ema_id DESC
	");
}
$numR = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM social_emails WHERE ema_para='".$_SESSION["id"]."' AND ema_eliminado_para!=1 AND ema_visto=0"));
$numRenviados = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM social_emails WHERE ema_de='".$_SESSION["id"]."' AND ema_eliminado_de!=1 AND ema_visto=0"));
?>
<div class="row">
                        <div class="col-md-12">
                            <div class="card card-topline-gray">
                                <div class="card-body no-padding height-9">
									<div class="inbox">
				                       <div class="row">
				                            <div class="col-md-3">
				                                <div class="inbox-sidebar">
				                                    <a href="mensajes-redactar.php" data-title="Compose" class="btn red compose-btn btn-block">
				                                        <i class="fa fa-edit"></i> Redactar </a>
				                                    <ul class="inbox-nav inbox-divider">
				                                        <li <?php if(!isset($_GET["opt"])){echo 'class="active"';}?> ><a href="mensajes.php"><i class="fa fa-inbox"></i> Recibidos 
															<span class="label mail-counter-style label-danger pull-right"><?=$numR;?></span></a>
				                                        </li>
														
				                                        <li <?php if(isset($_GET["opt"])&&$_GET["opt"]==2){echo 'class="active"';}?>><a href="mensajes.php?opt=2"><i class="fa fa-envelope"></i> Enviados <span class="label mail-counter-style label-danger pull-right"><?=$numRenviados;?></span></a></li>
				                                    </ul>
				                                </div>
				                            </div>
				                            <div class="col-md-9"> 
				                                <div class="inbox-body">
				                                    <div class="inbox-header">
				                                        <div class="mail-option no-pad-left">
				                                            <div class="btn-group group-padding">
				                                                <a class="btn mini tooltips" href="#" data-toggle="dropdown" data-placement="top" data-original-title="Refresh"> <i class=" fa fa-refresh fa-lg"></i>
				                                                </a>
				                                                <a class="btn mini tooltips" href="#" data-original-title="Archive"> <i class=" fa fa-archive fa-lg"></i>
				                                                </a>
				                                                <a class="btn mini tooltips" href="#" data-original-title="Trash"> <i class=" fa fa-trash-o fa-lg"></i>
				                                                </a>
				                                            </div>
				                                            <div class="btn-group res-email-btn">
				                                                <a class="btn mini tooltips" href="#" data-original-title="Folders"> <i class=" fa fa-folder fa-lg"></i>
				                                                </a>
				                                                <a class="btn mini tooltips" href="#" data-original-title="Tag"> <i class=" fa fa-tag fa-lg"></i>
				                                                </a>
				                                            </div>
				                                            <div class="btn-group hidden-phone">
				                                                <a class="btn mini blue-bgcolor" href="#" data-toggle="dropdown" aria-expanded="false"> Más <i
																	class="fa fa-angle-down downcolor"></i>
																</a>
				                                                <ul class="dropdown-menu">
				                                                    <li><a href="#"><i
																			class="fa fa-pencil"></i> Mark as Read</a>
				                                                    </li>
				                                                    <li><a href="#"><i class="fa fa-ban"></i>
																			Spam</a>
				                                                    </li>
				                                                    <li class="divider"></li>
				                                                    <li><a href="#"><i
																			class="fa fa-trash-o"></i> Delete</a>
				                                                    </li>
				                                                </ul>
				                                            </div>
				                                            <div class="btn-group pull-right btn-prev-next">
				                                                <button class="btn btn-sm btn-primary" type="button">
				                                                    <i class="fa fa-chevron-left"></i>
				                                                </button>
				                                                <button class="btn btn-sm btn-primary" type="button">
				                                                    <i class="fa fa-chevron-right"></i>
				                                                </button>
				                                            </div>
				                                        </div>
				                                    </div>
				                                    <div class="inbox-body no-pad table-responsive">
				                                        <table class="table table-inbox table-hover">
				                                            <tbody>
																<?php
																 $contReg = 1;
																 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
																	 $eliminar = 1;
																	 if($resultado['ema_para']==$_SESSION["id"]){ 
																		 $eliminar = 2;
																	 }
																?>
				                                                <tr <?php if($resultado['ema_visto']=='0'){ echo 'class="unread"';}?>>
				                                                    <td class="inbox-small-cells">
			                                                           	<div class="todo-check pull-left">
						                                                    <input type="checkbox" value="None" id="todo-check<?=$contReg;?>">
						                                                    <label for="todo-check<?=$contReg;?>"></label>
						                                                </div>
				                                                    </td>
				                                                    <td>
				                                                        <a href="#" class="avatar">
				                                                            <img src="../files/fotos/<?=$resultado['uss_foto'];?>" alt="">
				                                                        </a>
				                                                    </td>
				                                                    <td class="view-message  dont-show"><?=$resultado['uss_nombre'];?></td>
				                                                    <td class="view-message"><a href="mensajes-ver.php?idR=<?=$resultado['ema_id'];?>&opt=<?php if(isset($opt)){ echo $opt;}?>"><?=$resultado['ema_asunto'];?></a></td>
				                                                    <td class="view-message  text-right"><?=$resultado['ema_fecha'];?></td>
																	
																	<?php if($resultado['ema_de']==$_SESSION["id"]){?>
																		<td class="view-message  text-right"><?php if($resultado['ema_visto']==1) echo '<span style="font-size:10px; color:blue;"><i class="fa fa-check"></i> visto<br>
																		('.$resultado['ema_fecha_visto'].')</span>';?></td>
																	<?php }?>
																	
																	<td class="view-message  text-right"><a href="../compartido/guardar.php?get=17&idR=<?=$resultado['ema_id'];?>&elm=<?=$eliminar;?>"><i class="fa fa-trash"></i></a></td>
				                                                </tr>
																<?php
																	$contReg++;	 
																}
																?>
				                                                
				                                            </tbody>
				                                        </table>
				                                    </div>
				                                </div>
				                            </div>
				                        </div> 
                        			</div>
								</div>
                            </div>
                        </div>
                    </div>