<?php
$animate = "";
if (empty($_SESSION["id"])) {
	include("session-compartida.php");
	require_once(ROOT_PATH . "/main-app/class/Clases.php");
	$input = json_decode(file_get_contents("php://input"), true);
	if (!empty($input)) {
		$_POST = $input;
	}
	$animate = "animate__animated animate__flipInX";
	if (!empty($_POST["nivel"])) {
		$nivel = $_POST["nivel"];
	}
}
if (empty($preguntasDatos)) {
	if (empty($codigo)) {
		$codigo = $_POST["idPregunta"];
	}
	$preguntasDatos = Clases::traerPregunta(base64_decode($codigo));
}

$indice = empty($indice) ? 0 : $indice;

$nivel = empty($nivel) ? 0 : $nivel;


$i = empty($i) ? 0 : $i;

$usuarioActual = $_POST["usuarioActual"];
$usuarioDocente = $_POST["usuarioDocente"];
$parametros = ["cpp_id_clase" => $preguntasDatos['cpp_id_clase'], "institucion" => $config['conf_id_institucion'], "year" => $_SESSION["bd"], "cpp_padre" => $preguntasDatos['cpp_id']];
$totalPreguntas = Clases::contar($parametros);

?>

<li id="reg<?= base64_encode($preguntasDatos['cpp_id']) ?>" class="<?= $animate ?>">


	</div>
	<div <?= $nivel == 0 ? 'hidden' : ''; ?> style="  
    width: <?= $nivel == 1 ? 55 : 60; ?>px;
    height: 2px;
    background: #c7cacb;
    position: absolute;
    top: 25px;
    left: -<?= $nivel == 1 ? 55 : 61; ?>px;"></div>
	<div class="comment-main-level">
		<input hidden id="indice<?= base64_encode($preguntasDatos['cpp_id']) ?>" value="<?= $indice ?>">
		<input hidden id="nivel<?= base64_encode($preguntasDatos['cpp_id']) ?>" value="<?= $nivel ?>">
		<!-- Avatar -->
		<div class="comment-avatar" style=" z-index: 1;"><img src="../files/fotos/<?= $preguntasDatos['uss_foto']; ?>" alt=""></div>
		<!-- Contenedor del Comentario -->
		<div class="comment-box" style="width: 87%;">
			<div class="comment-head">
				<!-- i: <?= $i ?> Comentarios: <?= $totalPreguntas ?>= Indice:<?= $indice ?> (Nivel <?= $nivel ?>) -->
				<h6 class="comment-name <?= $preguntasDatos['uss_id'] === $usuarioDocente ? 'by-author' : ""; ?>"><a href="http://creaticode.com/blog"><?= $preguntasDatos['uss_nombre']; ?></a></h6>
				<span><?= $preguntasDatos['cpp_fecha']; ?></span>
				</a>
				<?php if ($usuarioActual === $preguntasDatos['cpp_usuario']) {
					$arrayEnviar = array("tipo" => 1, "descripcionTipo" => "Para ocultar fila del registro.");
					$arrayDatos = json_encode($arrayEnviar);
					$objetoEnviar = htmlentities($arrayDatos);
					$href = '../compartido/clases-eliminar-comentarios.php?idCom=' . base64_encode($preguntasDatos['cpp_id']) . '&idR=' . base64_encode($_POST["claseId"]); ?>

					<a href="javascript:void(0);" class="pull-right" title="<?= $objetoEnviar; ?>" id="<?= base64_encode($preguntasDatos['cpp_id']); ?>" name="<?= $href ?>" onClick="eliminarAnimacion('reg<?= base64_encode($preguntasDatos['cpp_id']) ?>');deseaEliminar(this)">
						<i class="fa fa-trash"></i>
					</a>
				<?php } ?>
			</div>
			<div class="comment-content">
				<?= $preguntasDatos['cpp_contenido']; ?>
				<div class="card-body" style="font-size: 11px;
    padding-bottom: 0px;
    padding-top: 0px;">
					<a id="cantidad-respuestas-<?= $preguntasDatos['cpp_id'] ?>" class="pull-left" data-bs-toggle="collapse" data-bs-target="#lista-respuesta-<?= $preguntasDatos['cpp_id'] ?>" aria-expanded="false" aria-controls="lista-respuesta-<?= $preguntasDatos['cpp_id'] ?>">

						<?php if ($totalPreguntas > 0) { ?> <?= $totalPreguntas ?>
							Respuestas
							<i class="fa fa-comments-o" aria-hidden="true"></i>
						<?php } ?>

					</a>
					<a class="pull-right"><i class="fa fa-reply" data-bs-toggle="collapse" data-bs-target="#div-respuesta-<?= $preguntasDatos['cpp_id']; ?>" aria-expanded="false" aria-controls="div-respuesta-<?= $preguntasDatos['cpp_id']; ?>"></i></a>
				</div>

				<div class="collapse" id="div-respuesta-<?= $preguntasDatos['cpp_id']; ?>">

					<div class="input-group">
						<textarea id="respuesta-<?= $preguntasDatos['cpp_id'] ?>" class="form-control" rows="2" placeholder="<?= UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual); ?> DICE..." style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
						<button id="btnEnviar-<?= $preguntasDatos['cpp_id'] ?>" class="input-group-text btn btn-primary " type="button" onclick="this.disabled=true;guardar('<?= $preguntasDatos['cpp_id']; ?>')"><i class="fa fa-send" aria-hidden="true"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Respuestas de los comentarios -->
	<ul class="comments-list reply-list collapse" id="lista-respuesta-<?= $preguntasDatos['cpp_id'] ?>">
	<?php
	if ($totalPreguntas > 0) {
		$filtro = "AND cpp.cpp_padre ='" . $preguntasDatos['cpp_id'] . "'";
		$respuestasConsulta = Clases::traerPreguntasClases($conexion,$config, $preguntasDatos['cpp_id_clase'], $filtro);
		if ($respuestasConsulta) {
			$nivel++;
	?>

			
				<div style="
    color:brown; 
    width: 2px;
    height: 100%;
    background: #c7cacb;
    position: absolute;
    top: 50px;
    left: 113px;
	z-index: 0;"></div>
				<?php
				$indice = 0;
				foreach ($respuestasConsulta as $preguntasDatos) {
					$codigo = $preguntasDatos["cpp_id"];
					include 'clase-comentario.php';
					$indice++;
				}; ?>

			
	<?php }
	}  ?>
	</ul>

</li>