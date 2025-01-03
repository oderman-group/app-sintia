<!--bootstrap -->
<link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<div class="modal fade" id="miModalUsuarios" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	<div class="modal-dialog" style="max-width: 1350px!important;">
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title" id="tituloModulo" align="center">Se econtraron varios usuarios con la misma informacion:</h4>
				<a href="#" data-bs-dismiss="modal" class="btn btn-danger" aria-label="Close" id="boton-cerrar-compra-modulo"><i class="fa fa-window-close"></i></a>
			</div>
			<form action="recuperar-clave-enviar-codigo.php" method="post">
			<div class="modal-body" align="center">
				
				
				<h2>
					Usuarios encontrados
				</h2>
				<label>Selecciona el usaurio a recuperar la contraseña</label>
				
					<div class="form-group">

					
						<table class="table table-striped">
							<thead>
								<tr>
									<th></th>
									<th>Institucion</th>
									<th>Documento</th>
									<th>Usuario</th>
									<th>email</th>
								</tr>
							</thead>
							<tbody>
								<?php
								require_once(ROOT_PATH . "/main-app/class/Instituciones.php");
								foreach ($listaUsuarios as $user) { 
									$institucion=Instituciones::getDataInstitution($user["institucion"]);
									$institucion = mysqli_fetch_array($institucion, MYSQLI_BOTH);
									?>
									<tr>
										<td>
											<div class="custom-control custom-radio">
												<input type="radio" id="<?php echo $user["id_nuevo"] ?>" name="usuarioId" required value="<?php echo $user["id_nuevo"] ?>" class="custom-control-input">
												<label class="custom-control-label" for="<?php echo $user["id_nuevo"] ?>"></label>
											</div>
										</td>
										<td><?php echo $institucion["ins_siglas"] ?></td>
										<td><?php echo $user["uss_documento"] ?></td>
										<td><?php echo $user["uss_usuario"] ?></td>
										<td><?php echo $user["uss_email"] ?></td>
									</tr>

								<?php } ?>
							</tbody>
						</table>
					</div>

				

			</div>

			<div class="modal-footer">			
				
				<button type="submit" class="btn btn-success">Recuperar contraseña</button>
			</div>
			</form>

		</div>
	</div>
</div>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>