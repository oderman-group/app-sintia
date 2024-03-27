<?php include("session.php"); ?>
<?php
// $_SESSION["bd"] = date("Y");
?>
<?php include("verificar-usuario.php"); ?>
<?php include("verificar-sanciones.php"); ?>
<?php $idPaginaInterna = 'ES0061'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php require_once("../class/servicios/CargaServicios.php"); ?>
<?php require_once("../class/servicios/MediaTecnicaServicios.php"); ?>
<?php require_once("../class/servicios/GradoServicios.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php"); ?>

<?php include("../compartido/head.php"); ?>
</head>
<style type="text/css" src="cardStyle.css">
	/* Cards */
	.swal2-backdrop-show {
		z-index: 99991;
	}

	.postcard {
		flex-wrap: wrap;
		display: flex;

		box-shadow: 0 4px 21px -12px rgba(0, 0, 0, 0.66);
		border-radius: 10px;
		margin: 0 0 2rem 0;
		overflow: hidden;
		position: relative;
		color: #ffffff;



		&.light {
			background-color: #e1e5ea;
		}

		.t-dark {
			color: #18151f;
		}

		a {
			color: inherit;
		}

		h1,
		.h1 {
			margin-bottom: 0.5rem;
			font-weight: 500;
			line-height: 1.2;
		}

		.small {
			font-size: 80%;
		}

		.postcard__title {
			font-size: 1.75rem;
		}

		.postcard__img {
			max-height: 180px;
			width: 100%;
			object-fit: cover;
			position: relative;
		}

		.postcard__img_link {
			display: contents;
		}

		.postcard__bar {
			width: 50px;
			height: 10px;
			margin: 10px 0;
			border-radius: 5px;
			background-color: #424242;
			transition: width 0.2s ease;
		}

		.postcard__text {
			padding: 1.5rem;
			position: relative;
			display: flex;
			flex-direction: column;
		}

		.postcard__preview-txt {
			overflow: hidden;
			text-overflow: ellipsis;
			text-align: justify;
			height: 100%;
		}

		.postcard__tagbox {
			display: flex;
			flex-flow: row wrap;
			font-size: 14px;
			margin: 20px 0 0 0;
			padding: 0;
			justify-content: center;

			.tag__item {
				display: inline-block;
				background: rgba(83, 83, 83, 0.4);
				border-radius: 3px;
				padding: 2.5px 10px;
				margin: 0 5px 5px 0;
				cursor: default;
				user-select: none;
				transition: background-color 0.3s;

				&:hover {
					background: rgba(83, 83, 83, 0.8);
				}
			}
		}

		&:before {
			content: "";
			position: absolute;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			opacity: 1;
			background: white;
			border-radius: 10px;
		}

		&:hover .postcard__bar {
			width: 100px;
		}
	}

	/* DIRECCION */
	@media screen and (min-width: 769px) {
		.postcard {
			flex-wrap: inherit;

			.postcard__title {
				font-size: 2rem;
			}

			.postcard__tagbox {
				justify-content: start;
			}

			.postcard__img {
				max-width: 300px;
				max-height: 100%;
				transition: transform 0.3s ease;
			}

			.postcard__text {
				padding: 3rem;
				width: 100%;
			}


			&:hover .postcard__img {
				transform: scale(1.1);
			}

			&.izquierda {
				flex-direction: row;
			}

			&.derecha {
				flex-direction: row-reverse;
			}

			&.izquierda .postcard__text::before {
				left: -12px !important;
				transform: rotate(4deg);
			}

			&.derecha .postcard__text::before {
				right: -12px !important;
				transform: rotate(-4deg);
			}
		}
	}





	@media screen and (min-width: 1024px) {
		.postcard__text {
			padding: 2rem 3.5rem;
		}

		.postcard__text:before {
			content: "";
			position: absolute;
			display: block;

			top: -20%;
			height: 130%;
			width: 55px;
		}

		.postcard.light {
			.postcard__text:before {
				width: 45px;
				background: white;
			}
		}
	}
</style>
<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css" />
<div id="gifCarga" class="gif-carga">
	<img alt="Cargando...">
</div>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">

	<?php include("../compartido/encabezado.php"); ?>

	<?php include("../compartido/panel-color.php"); ?>
	<!-- start page container -->
	<div class="page-container">

		<?php include("../compartido/menu.php"); ?>

		<!-- start page content -->
		<div class="page-content-wrapper">
			<div class="page-content">
				<div class="page-bar">
					<div class="page-title-breadcrumb">
						<div class=" pull-left">
							<div class="page-title"><?= $frases[429][$datosUsuarioActual['uss_idioma']]; ?></div>
							<?php include("../compartido/texto-manual-ayuda.php"); ?>

						</div>
					</div>
				</div>

				<!-- End course list -->
				<?php if (array_key_exists(10, $arregloModulos)) { ?>
					<div class="row">
						<?php
						$parametros = [
							'gra_active' => 1,
							'gra_estado' => 1,
							'gra_auto_enrollment' => 1,
							'gra_tipo' => GRADO_INDIVIDUAL,
							'institucion' => $config['conf_id_institucion'],
							'year' => $config['conf_agno']
						];
						$listaCursosLibres = GradoServicios::listarCursos($parametros);
						$cont = 0;
						if (!empty($listaCursosLibres)) {
							foreach ($listaCursosLibres as $dato) {
								$cont++;
								$direccion = "izquierda";
								if ($cont % 2 == 0) {
									$direccion = "izquierda";
								} else {
									$direccion = "derecha";
								};
								$limiteCaracteres = 300;

								$textoTruncado = "";
								if (strlen($dato["gra_overall_description"]) > $limiteCaracteres) { // Verificar si la longitud del texto es mayor que el límite
									$textoTruncado = substr($dato["gra_overall_description"], 0, $limiteCaracteres); // Truncar el texto al límite de caracteres
									$textoTruncado .= "..."; // Añadir puntos suspensivos o cualquier otro indicador de truncamiento

								} else {
									$textoTruncado = $dato["gra_overall_description"]; // El texto es igual o más corto que el límite, no es necesario truncar
								}
						?>

								<div class="col-xl-6 col-md-12 animate__animated animate__fadeIn">
									<div class="postcard  light <?= $direccion ?>">
										<a class="postcard__img_link" onclick="buscarCurso('<?= $dato["gra_id"] ?>')">
											<?php
											$urlImagen = $storage->getBucket()->object(FILE_CURSOS . $dato["gra_cover_image"])->signedUrl(new DateTime('tomorrow'));
											$existe = $storage->getBucket()->object(FILE_CURSOS . $dato["gra_cover_image"])->exists();
											if (!$existe) {
												$urlImagen = "../files/cursos/curso.png";
											}
											?>
											<img class="postcard__img" src="<?= $urlImagen ?>" alt="Image Title" />
										</a>
										<div class="postcard__text t-dark ">
											<h1 class="postcard__title " onclick="buscarCurso('<?= $dato["gra_id"] ?>')"><?= $dato["gra_nombre"]; ?></h1>
											<div class="postcard__subtitle small">
												<time datetime="2020-05-25 12:00:00">
													<i class="fas fa-calendar-alt mr-2"></i><?= $dato["gra_id"]; ?>
												</time>
											</div>
											<div class="postcard__bar">

											</div>
											<div class="postcard__preview-txt" data-toggle="modal" data-target="#Modal1"><?= $textoTruncado ?></div>

											<div style="height: 30px;">
												<?php
												$parametros = [
													'matcur_institucion' => $config['conf_id_institucion'],
													'matcur_years' => $config['conf_agno'],
													'matcur_id_curso' => $dato["gra_id"]
												];
												$listaMatriculados = MediaTecnicaServicios::listar($parametros);
												$hidden = '';
												$numInscritos = 0;
												if (!empty($listaMatriculados)) {
													$numInscritos = count($listaMatriculados);
													foreach ($listaMatriculados as $inscrito) {
														if ($inscrito['matcur_id_matricula'] == $datosEstudianteActual['mat_id']) {
															$hidden = "hidden";
														}
													}
												}
												$porcentaje = ($numInscritos / $dato["gra_maximum_quota"]) * 100;
												if ($numInscritos >= $dato["gra_maximum_quota"]) {
													$hidden = "hidden";
												}
												?>
												<i class="fas fa-user mr-2"></i>(<label id="label_<?= $dato["gra_id"] ?>"> <?= $numInscritos ?></label>/<?= $dato["gra_maximum_quota"] ?>)
												<div class="progress">
													<?php
													$color = "#007bff";
													switch ($porcentaje) {
														case ($porcentaje) < 60:
															$color = "#007bff";
															break;
														case (($porcentaje) > 60) && (($porcentaje) < 90):
															$color = "#ffc107";
															break;
														case ($porcentaje) > 90:
															$color = "#dc3545";
															break;
													}
													?>
													<div class="progress-bar" id="bar_progres_<?= $dato["gra_id"] ?>" role="progressbar" style="background-color:<?= $color ?> ;width: <?= $porcentaje ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
												</div>
											</div>
											<ul class="postcard__tagbox">
												<li class="tag__item"><i class="fas fa-tag mr-2"></i>$<?= number_format($dato["gra_price"], 0, ",", "."); ?></li>
												<li class="tag__item"><i class="fas fa-clock mr-2"></i><?= $dato["gra_duration_hours"]; ?> Hrs.</li>
												<li id="btn_iscrito_<?= $dato["gra_id"] ?>" class="tag__item play btn-success" <?= $hidden ?> onclick="confirmar('<?= $dato['gra_id'] ?>','<?= $dato['gra_nombre'] ?>')">
													<a href="#"><i class="fa-regular fa-pen-to-square"></i>Inscribirme</a>
												</li>
											</ul>


										</div>


									</div>


								</div>

						<?php }
						} ?>
					<?php } ?>
					</div>
			</div>
		</div>
		<!-- end page content -->
		<?php // include("../compartido/panel-configuracion.php");
		?>
	</div>
	<script type="application/javascript">
		function buscarCurso(valor) {
			document.getElementById("gifCarga").style.display = "block";
			var url = "fetch-buscar-curso.php";
			var data = {
				"codigo": (valor),
				"year": <?php echo $config['conf_agno'] ?>,
				"institucion": <?php echo $config['conf_id_institucion'] ?>
			};
			fetch(url, {
					method: "POST", // or 'PUT'
					body: JSON.stringify(data), // data can be `string` or {object}!
					headers: {
						"Content-Type": "text/html"
					},
				})
				.then((res) => res.text())
				.catch((error) => console.error("Error:", error))
				.then(
					function(res) {
						document.getElementById("gifCarga").style.display = "none";
						const contenido = document.getElementById('contenidoModal');
						contenido.innerHTML = res;
						$('#Modal1').modal('show');
					});
		}


		function confirmar(idCurso, nombre) {
			const swalWithBootstrapButtons = Swal.mixin({
				customClass: {
					confirmButton: "btn btn-success",
					cancelButton: "btn btn-danger"
				}
			});
			swalWithBootstrapButtons.fire({
				title: "¿Desea registrarse?",
				text: "Recuerde que al aceptar quedara Pre Inscrito en el curso de " + nombre + "!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: "Si, deseo registrarme!",
				cancelButtonText: "No! talvez más tarde!",
				reverseButtons: true
			}).then((result) => {
				if (result.isConfirmed) {
					inscribirse(idCurso);
				}
			});
		}

		function inscribirse(valor) {
			var url = "fetch-inscribirse-curso.php";
			var data = {
				"codigo": valor,
				"matricula": <?php echo $datosEstudianteActual['mat_id'] ?> + ''
			};
			fetch(url, {
					method: "POST", // or 'PUT'
					body: JSON.stringify(data), // data can be `string` or {object}!
					headers: {
						"Content-Type": "application/json"
					},
				})
				.then((res) => res.json())
				.catch((error) => console.error("Error:", error))
				.then(
					function(res) {
						if (res["ok"]) {
							var btInscribirse = document.getElementById("btn_iscrito_" + res["curso"]);
							var barraProgreso = document.getElementById("bar_progres_" + res["curso"]);
							var labelCantidad = document.getElementById("label_" + res["curso"]);
							btInscribirse.style.display = "none";
							barraProgreso.style.width = res["porcentage"] + "%";
							labelCantidad.textContent = res["cantidad"];
							Swal.fire({
								title: "Registro Exitoso!",
								text: res["msg"],
								icon: "success"
							});
						} else {
							Swal.fire({
								position: "top-end",
								icon: "error",
								title: res["msg"],
								showConfirmButton: false,
								timer: 3500
							});
							setTimeout(function() {
								location.reload();
							}, 20000);

						}

					});
		}
	</script>

	<div class="modal fade bd-example-modal-lg" id="Modal1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content" id="contenidoModal">

			</div>
		</div>
	</div>
	<!-- end page container -->
	<?php include("../compartido/footer.php"); ?>
</div>
<!-- start js include path -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="../../config-general/assets/plugins/sparkline/jquery.sparkline.js"></script>
<script src="../../config-general/assets/js/pages/sparkline/sparkline-data.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- notifications -->
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
<!-- material -->
<script src="../../config-general/assets/plugins/material/material.min.js"></script>
<!-- chart js -->
<script src="../../config-general/assets/plugins/chart-js/Chart.bundle.js"></script>
<script src="../../config-general/assets/plugins/chart-js/utils.js"></script>
<script src="../../config-general/assets/js/pages/chart/chartjs/home-data.js"></script>
<!-- summernote -->
<script src="../../config-general/assets/plugins/summernote/summernote.js"></script>
<script src="../../config-general/assets/js/pages/summernote/summernote-data.js"></script>
<!-- end js include path -->
</body>

</html>