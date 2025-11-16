<?php if($config['conf_solicitar_acudiente_2'] === "SI"){?>
	<hr>
	<hr>
<?php
$acudiente2 = isset($datosEstudianteActual["mat_acudiente2"]) ? UsuariosPadre::sesionUsuario($datosEstudianteActual["mat_acudiente2"]) : null;
?>  
<h2><b>ACUDIENTE 2</b></h2>
<input type="hidden" name="idAcudiente2" value="<?php if(!empty($datosEstudianteActual["mat_acudiente2"])){ echo $datosEstudianteActual["mat_acudiente2"];}?>">

<div class="form-group row">
	<label class="col-sm-2 control-label">Tipo de documento</label>
	<div class="col-sm-3">
		<?php $tiposDocumento = $opcionesGeneralesPorGrupo[1] ?? []; ?>
		<select class="form-control" name="tipoDAcudiente2" <?=$disabledPermiso;?>>
			<?php foreach($tiposDocumento as $opcion){
				$selected = (isset($acudiente2["uss_tipo_documento"]) && $opcion['ogen_id']==$acudiente2["uss_tipo_documento"]) ? 'selected' : '';
				echo '<option value="'.$opcion['ogen_id'].'" '.$selected.'>'.$opcion['ogen_nombre'].'</option>';
			}?>
		</select>
	</div>
	
	<label class="col-sm-2 control-label">Documento</label>
	<div class="col-sm-3">
		<input type="text" name="documentoA2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2['uss_usuario'])){ echo $acudiente2['uss_usuario'];}?>" <?=$disabledPermiso;?>>
	</div>
</div>
	
<div class="form-group row">
	<label class="col-sm-2 control-label">Lugar de expedición</label>
	<div class="col-sm-3">
		<select class="form-control" name="lugardA2" <?=$disabledPermiso;?>>
			<option value="">Seleccione una opción</option>
			<?php foreach(($catalogoCiudades ?? []) as $ciudad){ ?>
			<option value="<?=$ciudad['ciu_id'];?>" <?php if(isset($acudiente2["uss_lugar_expedicion"])&&$ciudad['ciu_id']==$acudiente2["uss_lugar_expedicion"]){echo "selected";}?>><?=$ciudad['ciu_nombre'].", ".$ciudad['dep_nombre'];?></option>
			<?php }?>
		</select>
	</div>	

	<label class="col-sm-2 control-label">Ocupaci&oacute;n</label>
	<div class="col-sm-3">
		<input type="text" name="ocupacionA2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_ocupacion"])){ echo $acudiente2["uss_ocupacion"];}?>" <?=$disabledPermiso;?>>
	</div>
</div>

<div class="form-group row">												
	<label class="col-sm-2 control-label">Primer Apellido</label>
	<div class="col-sm-3">
		<input type="text" name="apellido1A2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_apellido1"])){ echo $acudiente2["uss_apellido1"];}?>" <?=$disabledPermiso;?>>
	</div>
												
	<label class="col-sm-2 control-label">Segundo Apellido</label>
	<div class="col-sm-3">
		<input type="text" name="apellido2A2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_apellido2"])){ echo $acudiente2["uss_apellido2"];}?>" <?=$disabledPermiso;?>>
	</div>
</div>

<div class="form-group row">												
	<label class="col-sm-2 control-label">Nombre</label>
	<div class="col-sm-3">
		<input type="text" name="nombreA2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_nombre"])){ echo $acudiente2["uss_nombre"];}?>" <?=$disabledPermiso;?>>
	</div>
													
	<label class="col-sm-2 control-label">Otro Nombre</label>
	<div class="col-sm-3">
		<input type="text" name="nombre2A2" class="form-control" autocomplete="off" value="<?php if(isset($acudiente2["uss_nombre2"])){ echo $acudiente2["uss_nombre2"];}?>" <?=$disabledPermiso;?>>
	</div>
</div>	
	
<div class="form-group row">
	<label class="col-sm-2 control-label">Genero</label>
	<div class="col-sm-3">
		<?php $opcionesGenero = $opcionesGeneralesPorGrupo[4] ?? []; ?>
		<select class="form-control" name="generoA2" <?=$disabledPermiso;?>>
			<option value="">Seleccione una opción</option>
			<?php foreach($opcionesGenero as $opcion){
				$selected = (isset($acudiente2['uss_genero']) && $opcion['ogen_id']==$acudiente2['uss_genero']) ? 'selected' : '';
				echo '<option value="'.$opcion['ogen_id'].'" '.$selected.'>'.$opcion['ogen_nombre'].'</option>';
			}?>
		</select>
	</div>
</div>
<?php }?>