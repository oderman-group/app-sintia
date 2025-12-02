<?php
require_once ROOT_PATH.'/main-app/class/App/Administrativo/Usuario/Estudiante.php';

$Estudiante = new Administrativo_Usuario_Estudiante([
	'mat_id' => $idMatricula
]);

$tieneRegistrosAcademicos = (bool) $Estudiante->tieneRegistrosAcademicos();

$disabledCampoGradoGrupo = $tieneRegistrosAcademicos && $config['conf_puede_cambiar_grado_y_grupo'] != 1 ? 'disabled' : '';
$disabledCamposAcademicos = $tieneRegistrosAcademicos ? 'disabled' : '';
$fondoBarra = $Plataforma->colorUno ?? ($config['conf_color_barra_superior'] ?? '#6017dc');
$colorTexto = $Plataforma->colorTres ?? ($config['conf_color_boton_texto'] ?? '#ffffff');
?>

<fieldset>
	<div class="row">
		<div class="col-sm-12 col-xl-6">

			<?php if ($tieneRegistrosAcademicos && $config['conf_puede_cambiar_grado_y_grupo'] == 1) {?>
				<div id="advertenciaCambioCurso" style="display:none;" class="alert alert-block alert-warning animate__animated animate__flash animate__delay-1s">
					<p>Tenga en cuenta que al cambiar de curso o grupo a un estudiante con notas ya registradas estas podrían perderse.</p>
				</div>
			<?php }?>

			<div class="form-group row">
				<label class="col-sm-3 control-label">Curso <span style="color: red;">(*)</span></label>
				<input type="hidden" name="gradoActual" id="gradoActual" value="<?=$datosEstudianteActual['mat_grado'];?>">
				<div class="col-sm-9">
					<select class="form-control" name="grado" id="gradoMatricula"  <?= $disabledPermiso; ?> onchange="listarGrupos(this.value)">
						<?php 
						$cv = Grados::traerGradosInstitucion($config, GRADO_GRUPAL);
						while ($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)) {
							if ($rv['gra_id'] == $datosEstudianteActual['mat_grado'])
								echo '<option value="' . $rv['gra_id'] . '" selected>' . $rv['gra_nombre'] . '</option>';
							else
								echo '<option value="' . $rv['gra_id'] . '" '.$disabledCampoGradoGrupo.'>' . $rv['gra_nombre'] . '</option>';
						} ?>
					</select>
				</div>
			</div>

			<div class="form-group row">
				<label class="col-sm-3 control-label">Grupo</label>
				<input type="hidden" name="grupoActual" id="grupoActual" value="<?=$datosEstudianteActual['mat_grupo'];?>">
				<div class="col-sm-4">
					<span id="mensajeGrupos" style="color: #6017dc; display:none;">Espere un momento mientras se cargan los grupos.</span>
					<select class="form-control" id="gruposMatricula" name="grupo"  <?= $disabledPermiso; ?>>
						<?php 
                        $cv = Grupos::listarGrupos();
						while ($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)) {
							if ($rv['gru_id'] == $datosEstudianteActual['mat_grupo'])
								echo '<option value="' . $rv['gru_id'] . '" selected>' . $rv['gru_nombre'] . '</option>';
							else
								echo '<option value="' . $rv['gru_id'] . '" '.$disabledCampoGradoGrupo.'>' . $rv['gru_nombre'] . '</option>';
						} ?>
					</select>
				</div>

				<?php 
				$permisoCambiarGrupo      = Modulos::validarSubRol(['DT0083']);
				$moduloMediaTecnica       = Modulos::verificarModulosDeInstitucion(Modulos::MODULO_MEDIA_TECNICA);
				$marcaMediaTecnica        = '';
				if ($datosEstudianteActual['mat_tipo_matricula'] == GRADO_INDIVIDUAL && array_key_exists(10, $arregloModulos) && $moduloMediaTecnica) {
					$marcaMediaTecnica = 'Si';
				}
				if (!empty($datosEstudianteActual['mat_grado']) && $permisoCambiarGrupo  &&  empty($marcaMediaTecnica)) { ?>
				<div class="col-sm-4">
				<button type="button" class="btn btn-info" onclick="cambiarNotas('<?=  base64_encode($datosEstudianteActual['mat_id'])?>',true)" style="background-color:<?=$fondoBarra;?>; color:<?=$colorTexto;?>;">Cambiar notas a otro grupo</button>
				</div>
				<?php } ?>
			</div>

			<div class="form-group row">
				<label class="col-sm-3 control-label">Tipo estudiante</label>
				<div class="col-sm-9">
					<?php
					$op = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".opciones_generales WHERE ogen_grupo=5");
					?>
					<select class="form-control" name="tipoEst" <?= $disabledPermiso; ?>>
						<option value="">Seleccione una opción</option>
						<?php while ($o = mysqli_fetch_array($op, MYSQLI_BOTH)) {
							if ($o[0] == $datosEstudianteActual['mat_tipo'])
								echo '<option value="' . $o[0] . '" selected>' . $o[1] . '</option>';
							else
								echo '<option value="' . $o[0] . '">' . $o[1] . '</option>';
						} ?>
					</select>
				</div>
			</div>

			<div class="form-group row">
				<label class="col-sm-3 control-label">Estado Matricula</label>
				<div class="col-sm-9">
					<select class="form-control" name="matestM" <?= $disabledPermiso; ?>>
						<?php 
						$estadoActualEstudiante = (int)$datosEstudianteActual["mat_estado_matricula"];
						foreach ($estadosMatriculasEstudiantes as $clave => $valor) { 
							$estadoNuevo = (int)$clave;
							
							// Usar el método centralizado para validar el cambio de estado
							$validacion = Estudiantes::validarCambioEstadoMatricula($estadoActualEstudiante, $estadoNuevo);
							
							// Determinar si la opción debe estar deshabilitada
							$disabledEstado = '';
							if (!$validacion['valido']) {
								// Si el cambio no es válido, deshabilitar la opción (excepto si es el estado actual)
								if ($estadoActualEstudiante != $estadoNuevo) {
									$disabledEstado = 'disabled';
								}
							}
							
							// Estados EN_INSCRIPCION (5) y CANCELADO (3) solo lectura si no es el estado actual
							if (($clave == Estudiantes::ESTADO_EN_INSCRIPCION || $clave == Estudiantes::ESTADO_CANCELADO) 
								&& $datosEstudianteActual["mat_estado_matricula"] != $clave) {
								$disabledEstado = 'disabled';
							}
							
							// Si es el estado actual y está restringido, aplicar disabled de campos académicos
							$selected = ($datosEstudianteActual["mat_estado_matricula"] == $clave) ? 'selected' : '';
							$disabledFinal = '';
							if (($clave == Estudiantes::ESTADO_EN_INSCRIPCION || $clave == Estudiantes::ESTADO_CANCELADO) 
								&& $datosEstudianteActual["mat_estado_matricula"] == $clave) {
								$disabledFinal = $disabledCamposAcademicos;
							} else {
								$disabledFinal = $disabledEstado;
							}
						?>
							<option value="<?= $clave; ?>" <?= $selected; ?> <?= $disabledFinal; ?>><?= $valor; ?></option>
						<?php } ?>
					</select>
					<?php 
					// Mostrar mensajes informativos según el estado actual
					$estadoActual = $datosEstudianteActual["mat_estado_matricula"];
					
					if (($estadoActual == Estudiantes::ESTADO_EN_INSCRIPCION || $estadoActual == Estudiantes::ESTADO_CANCELADO) && empty($disabledPermiso)) { ?>
						<small class="form-text text-muted">
							<i class="fa fa-info-circle"></i> Este estado se gestiona desde otros módulos del sistema y no puede ser modificado desde aquí.
						</small>
					<?php } ?>
					<?php if ($estadoActual == Estudiantes::ESTADO_ASISTENTE && empty($disabledPermiso)) { ?>
						<small class="form-text text-muted">
							<i class="fa fa-info-circle"></i> Un estudiante en estado "Asistente" solo puede cambiar a estado "Matriculado".
						</small>
					<?php } ?>
					<?php if ($estadoActual == Estudiantes::ESTADO_NO_MATRICULADO && empty($disabledPermiso)) { ?>
						<small class="form-text text-muted">
							<i class="fa fa-info-circle"></i> Un estudiante en estado "No matriculado" solo puede cambiar a estado "Matriculado".
						</small>
					<?php } ?>
					<?php if ($estadoActual == Estudiantes::ESTADO_MATRICULADO && empty($disabledPermiso)) { ?>
						<small class="form-text text-muted">
							<i class="fa fa-info-circle"></i> Las opciones "No matriculado" y "Asistente" aparecen deshabilitadas (solo lectura) porque el estudiante está en estado "Matriculado". Solo los estudiantes en estado "Asistente" o "No matriculado" pueden cambiar a "Matriculado".
						</small>
					<?php } ?>
				</div>
			</div>

			<div class="form-group row">
				<label class="col-sm-3 control-label">Valor Matricula</label>
				<div class="col-sm-3">
					<input type="text" name="va_matricula" class="form-control" autocomplete="off" value="<?= $datosEstudianteActual['mat_valor_matricula']; ?>" <?= $disabledPermiso; ?>>
				</div>
			</div>

			<div class="form-group row">
				<label class="col-sm-3 control-label">Estado del año</label>
				<div class="col-sm-9">
					<select class="form-control" name="estadoAgno" <?= $disabledPermiso; ?>>
						<option value="0">Seleccione una opción</option>
						<option value="1" <?php if ($datosEstudianteActual['mat_estado_agno'] == 1) {
												echo "selected";
											} ?>>Ganado</option>
						<option value="2" <?php if ($datosEstudianteActual['mat_estado_agno'] == 2) {
												echo "selected";
											} ?>>Perdido</option>
						<option value="3" <?php if ($datosEstudianteActual['mat_estado_agno'] == 3) {
												echo "selected";
											} ?>>En curso</option>
					</select>
				</div>
			</div>

			<?php if (array_key_exists(10, $arregloModulos)) {
				require_once("../class/servicios/MediaTecnicaServicios.php");
				$parametros = ['gra_tipo' => GRADO_INDIVIDUAL, 'gra_estado' => 1, 'institucion' => $config['conf_id_institucion'], 'year' => $_SESSION["bd"]];

				$listaIndividuales = GradoServicios::listarCursos($parametros);
				$parametros = ['matcur_id_matricula' => $idMatricula];
				$listaMediaTenicaActual = MediaTecnicaServicios::listar($parametros);
				$listaMediaActual = array();
				if (!is_null($listaMediaTenicaActual) && count($listaMediaTenicaActual) > 0) {
					foreach ($listaMediaTenicaActual as $llave => $valor) {
						$listaMediaActual[$valor["matcur_id_curso"]] = 'id_curso';
						$listaMediaActual[$valor["matcur_id_grupo"]] = 'id_grupo';
					}
				}
			?>
				<div class="form-group row">
					<label class="col-sm-3 control-label"> Puede estar en multiples cursos? </label>
					<div class="col-sm-3">
						<select class="form-control  select2" name="tipoMatricula" id="tipoMatricula" onchange="mostrarCursosAdicionales(this)">
							<option value="<?= GRADO_GRUPAL; ?>" <?php if ($datosEstudianteActual['mat_tipo_matricula'] == GRADO_GRUPAL) {
																		echo 'selected';
																	} ?>>NO</option>
							<option value="<?= GRADO_INDIVIDUAL; ?>" <?php if ($datosEstudianteActual['mat_tipo_matricula'] == GRADO_INDIVIDUAL) {
																			echo 'selected';
																		} ?>>SI</option>
						</select>
					</div>
				</div>
				<script type="application/javascript">
					$(document).ready(mostrarCursosAdicionales(document.getElementById("tipoMatricula")))

					function mostrarCursosAdicionales(enviada) {
						var valor = enviada.value;
						if (valor == '<?= GRADO_INDIVIDUAL; ?>') {
							document.getElementById("divCursosAdicionales").style.display = 'block';
						} else {
							document.getElementById("divCursosAdicionales").style.display = 'none';
						}
					}
				</script>


			<?php } ?>
		</div>
		<?php if (array_key_exists(10, $arregloModulos)) {
			require_once("../compartido/includes/includeSelectSearch.php");
		?>
		<div class="col-sm-12 col-xl-6">
			<div id="divCursosAdicionales" style="display: none;">
				<div class="form-group row">
					<label class="col-sm-1 control-label">Cursos</label>
					<div class="col-sm-11">
						<?php
						$selectEctudiante2 = new includeSelectSearch("SeleccionCurso", "ajax-listar-cursos.php", "buscar Cursos", "agregarCurso");
						$selectEctudiante2->generarComponente();
						?>
					</div>
				</div>
				<div style="display: none;">
					<select id="grupoBase" multiple class="form-control select2-multiple">
						<?php
                        $cv = Grupos::listarGrupos();
						while ($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)) {
							echo '<option value="' . $rv['gru_id'] . '" selected >' . $rv['gru_nombre'] . '</option>';
						} ?>
					</select>
					<select id="estadoBase" multiple class="form-control select2-multiple">
						<option value="<?= ESTADO_CURSO_ACTIVO ?>" selected><?= ESTADO_CURSO_ACTIVO ?></option>
						<option value="<?= ESTADO_CURSO_INACTIVO ?>" selected><?= ESTADO_CURSO_INACTIVO ?></option>
						<option value="<?= ESTADO_CURSO_PRE_INSCRITO ?>" selected><?= ESTADO_CURSO_PRE_INSCRITO ?></option>
						<option value="<?= ESTADO_CURSO_NO_APROBADO ?>" selected><?= ESTADO_CURSO_NO_APROBADO ?></option>
						<option value="<?= ESTADO_CURSO_APROBADO ?>" selected><?= ESTADO_CURSO_APROBADO ?></option>
					</select>
				</div>
				<div class="form-group row">
					<div class="col-sm-12">
						<table class="table" id="cursosRegistrados">
							<thead>
								<tr>
									<!-- <th scope="col">#</th> -->
									<th scope="col">Nombre</th>
									<th scope="col" width="100px">Grupo</th>
									<th scope="col" width="200px">Estado</th>
									<th scope="col" width="100px">Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$parametros = [
									'matcur_id_matricula' => $idMatricula,
									'matcur_id_institucion' => $config['conf_id_institucion'],
									'matcur_years' => $config['conf_agno'],
									'arreglo' => false
								];
								$ListaGruposRegistrados = MediaTecnicaServicios::listarEstudiantes($parametros);
								if (!is_null($ListaGruposRegistrados)) {
									foreach ($ListaGruposRegistrados as $idCurso) {
										$arrayEnviar = array("tipo" => 1, "descripcionTipo" => "Para ocultar fila del registro.");
										$arrayDatos = json_encode($arrayEnviar);
										$objetoEnviar = htmlentities($arrayDatos);

								?>
										<tr id="reg<?= $idCurso["gra_id"]; ?>">
											<!-- <td><?= $idCurso["gra_id"]; ?></td> -->
											<td><?= $idCurso["gra_nombre"]; ?></td>
											<td>
												<select id="grupo-<?= $idCurso["gra_id"]; ?>" class="form-control" onchange="editarCurso('<?= $idCurso['gra_id']; ?>')" <?= $disabledPermiso; ?>>
													<?php
													$cv = Grupos::listarGrupos();
													while ($rv = mysqli_fetch_array($cv, MYSQLI_BOTH)) {
														if ($rv['gru_id'] == $idCurso['matcur_id_grupo'])
															echo '<option value="' . $rv['gru_id'] . '" selected>' . $rv['gru_nombre'] . '</option>';
														else
															echo '<option value="' . $rv['gru_id'] . '">' . $rv['gru_nombre'] . '</option>';
													} ?>
												</select>
											</td>
											<td>
												<select id="estado-<?= $idCurso["gra_id"]; ?>" class="form-control" onchange="editarCurso('<?= $idCurso['gra_id']; ?>')" <?= $disabledPermiso; ?>>
													<option value="<?= ESTADO_CURSO_ACTIVO ?>" <?php echo $idCurso['matcur_estado'] == ESTADO_CURSO_ACTIVO ? 'selected' : ''; ?>>
														<?= ESTADO_CURSO_ACTIVO ?></option>
													<option value="<?= ESTADO_CURSO_INACTIVO ?>" <?php echo $idCurso['matcur_estado'] == ESTADO_CURSO_INACTIVO ? 'selected' : ''; ?>><?= ESTADO_CURSO_INACTIVO ?></option>
													<option value="<?= ESTADO_CURSO_PRE_INSCRITO ?>" <?php echo $idCurso['matcur_estado'] == ESTADO_CURSO_PRE_INSCRITO ? 'selected' : ''; ?>><?= ESTADO_CURSO_PRE_INSCRITO ?></option>
													<option value="<?= ESTADO_CURSO_NO_APROBADO ?>" <?php echo $idCurso['matcur_estado'] == ESTADO_CURSO_NO_APROBADO ? 'selected' : ''; ?>><?= ESTADO_CURSO_NO_APROBADO ?></option>
													<option value="<?= ESTADO_CURSO_APROBADO ?>" <?php echo $idCurso['matcur_estado'] == ESTADO_CURSO_APROBADO ? 'selected' : ''; ?>><?= ESTADO_CURSO_APROBADO ?></option>
												</select>
											</td>
											<td>
												<button type="button" title="<?= $objetoEnviar; ?>" name="fetch-estudiante-mediatecnica.php?tipo=<?= base64_encode(ACCION_ELIMINAR) ?>&curso=<?= base64_encode($idCurso["gra_id"]) ?>&matricula=<?= $idMatricula?>" id="<?= $idCurso["gra_id"]; ?>" onClick="deseaEliminar(this)" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
											</td>
										</tr>
								<?php  }
								} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<script type="text/javascript">
		function agregarCurso(dato) {
			crearFila(dato);
		};

		var selectcurso = $('#gradoMatricula');
       var selectgrupos = $('#gruposMatricula');

		async function listarGrupos(curso) {
			var advertenciaCambioCurso = document.getElementById("advertenciaCambioCurso");
			var cursoActual = document.getElementById("gradoActual").value;

			if (cursoActual != curso) {
				advertenciaCambioCurso.style.display = "block";
			} else {
				advertenciaCambioCurso.style.display = "none";
			}

			var url = "../compartido/ajax_grupos_curso.php";
			var data = {
				"cursos": curso
			};

        $('#mensajeGrupos').show();
        selectgrupos.empty();
        // selectmaterias.empty();
        resultado = await metodoFetchAsync(url, data, 'json', false);
        resultData = resultado["data"];
        if (resultData["ok"]) {
            resultData["result"];
            // Itera sobre el JSON y añade cada opción
            resultData["result"].forEach(function(opcion) {
                var nuevaOpcion = new Option(opcion.gru_nombre, opcion.car_grupo, false, false);
                selectgrupos.append(nuevaOpcion);
            });
            $('#mensajeGrupos').hide();
		};
    }

	async function cambiarNotas(mat_id,cambiar) {
		var data = {
			"id"   : mat_id,
			"cambiar": cambiar
		};
		abrirModal("Cambiar de grupo", "estudiantes-cambiar-grupo-modal.php", data);
	}

		function editarCurso(id) {
			var grupoSelect = document.getElementById("grupo-" + id);
			var estadoSelect = document.getElementById("estado-" + id);
			var data = {
				"curso": id,
				"grupo": grupoSelect.value,
				"estado": estadoSelect.value,
				"matricula": '<?=$idMatricula?>'
			};
			accionCursoMatricula(data, '<?php echo ACCION_MODIFICAR ?>');
		};

		function accionCursoMatricula(data, tipo) {
			data["tipo"] = tipo;
			var url = "fetch-estudiante-mediatecnica.php";

			

			fetch(url, {
					method: "POST", // or 'PUT'
					body: JSON.stringify(data), // data can be `string` or {object}!
					headers: {
						"Content-Type": "application/json"
					},
				})
				.then((res) => res.json())
				.catch((res) => console.error("Error:"+res))
				.then(
					function(result) {
						$.toast({
							heading: 'Acción realizada',
							text: result["msg"],
							position: 'bottom-right',
							showHideTransition: 'slide',
							loaderBg: '#26c281',
							icon: 'success',
							hideAfter: 5000,
							stack: 6
						});

					});
		}

		function crearFila(seleccion) {
			if (seleccion) {
				var valor = seleccion.id; // El valor de la opción
				var etiqueta = seleccion.text; // La etiqueta de la opción
				// se insertan los valores en la tabla
				var tabla = document.getElementById("cursosRegistrados");
				var filas = tabla.getElementsByTagName("tr");

				// buscamos si ya se encuentra registrado                                                            
				encontro = false;
				for (var i = 0; i < filas.length; i++) { // Recorre las filas
					var celdas = filas[i].getElementsByTagName("td"); // Obtén todas las celdas de la fila actual
				
					for (var j = 0; j < celdas.length; j++) { // Recorre las celdas
						if (filas[i].id == "reg"+valor) {
							encontro = true; // cambio el estado de  a tru si encuentra un codigo igual
						}
					}
				}
				if (!encontro) {
					// creamos el select del grupo
					var select1 = document.createElement("select");
					select1.id = "grupo-" + valor;
					select1.classList.add('form-control');
					var opciones = $('#grupoBase').select2('data');
					for (var i = 0; i < opciones.length; i++) {
						var opcion = document.createElement("option");
						opcion.text = opciones[i].text;
						opcion.value = opciones[i].id;
						select1.add(opcion);
					}
					select1.addEventListener('change', function() {
						editarCurso(valor);
					});
					// creamos el select del estado
					var select2 = document.createElement("select");
					select2.id = "estado-" + valor;
					select2.classList.add('form-control');
					var opciones2 = $('#estadoBase').select2('data');
					for (var i = 0; i < opciones2.length; i++) {
						var opcion = document.createElement("option");
						opcion.text = opciones2[i].text;
						opcion.value = opciones2[i].id;
						select2.add(opcion);
					}
					select2.value='<?php echo ESTADO_CURSO_PRE_INSCRITO ?>';
					select2.addEventListener('change', function() {
						editarCurso(valor);
					});

					// Crea un elemento de botón
					var boton = document.createElement("button");
					boton.type = "button";
					boton.id = valor;
					boton.title = '{"tipo":1,"descripcionTipo":"Para ocultar fila del registro."}';
					boton.name = "fetch-estudiante-mediatecnica.php?" +
						"tipo=<?php echo base64_encode(ACCION_ELIMINAR) ?>" +
						"&curso=" + btoa(valor) +
						"&matricula=<?=$idMatricula?>";
					boton.classList.add('btn', 'btn-danger', 'btn-sm');
					var icon = document.createElement('i'); // se crea la icono
					icon.classList.add('fa', 'fa-trash');
					boton.appendChild(icon);
					// Agregar un evento al botón
					boton.addEventListener('click', function() {
						var fila = document.getElementById("reg" + valor);
						fila.classList.remove('animate__animated', 'animate__fadeInDown');
						deseaEliminar(boton);
					});

					// se guarda en la base de datos
					var data = {
						"curso": valor,
						"matricula": '<?=$idMatricula?>'
					};
					accionCursoMatricula(data, '<?php echo ACCION_CREAR ?>');
					// Crear una nueva fila                                                                
					var fila = tabla.insertRow();
					// Agregar datos a las celdas
					fila.id = "reg" + valor;
					fila.classList.add('animate__animated', 'animate__fadeInDown');
					// fila.insertCell(0).innerHTML = valor;
					fila.insertCell(0).innerHTML = etiqueta;
					fila.insertCell(1).appendChild(select1);
					fila.insertCell(2).appendChild(select2);
					fila.insertCell(3).appendChild(boton);

				} else {
					Swal.fire('Curso ya se encuentra registrado');
				}

			} else {
				Swal.fire('mo hay opcion selecionada');
			}
		}
	</script>
</fieldset>