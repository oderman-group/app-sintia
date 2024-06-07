<?php
if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
$disabledPermiso = "";
if (!Modulos::validarPermisoEdicion()) {
    $disabledPermiso = "disabled";
} ?>

<!-- Theme Styles -->
<link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/css/cargando.css" rel="stylesheet" type="text/css" />


<style>
    div:where(.swal2-container).swal2-top-end,
    div:where(.swal2-container).swal2-center-end,
    div:where(.swal2-container).swal2-bottom-end {
        grid-template-columns: auto auto minmax(0, 1fr);
        z-index: 99999;
    }
</style>

<?php include("../../config-general/mensajes-informativos.php"); ?>
<div class="col-md-12">
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">

            <a class="nav-item nav-link show active" id="nav-informacion-tab" data-toggle="tab" href="#nav-informacion" role="tab" aria-controls="nav-informacion" aria-selected="true">
                <h5> <?= $frases[119][$datosUsuarioActual['uss_idioma']]; ?> </h5>
            </a>
            <a style="display: none;" class="nav-item nav-link" id="nav-configuracion-tab" data-toggle="tab" href="#nav-configuracion" role="tab" aria-controls="nav-configuracion" aria-selected="false">
                <h5> Configuracion del curso </h5>
            </a>
        </div>
    </nav>
    <form name="formularioGuardar" action="cursos-guardar.php" method="post" enctype="multipart/form-data">
        <div class="tab-content" id="nav-tabContent">

            <div class="tab-pane fade show active" id="nav-informacion" role="tabpanel" aria-labelledby="nav-informacion-tab">
                <div class="panel">
                    <div class="panel-body">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Nombre Curso <span style="color: red;">(*)</span></label>
                            <div class="col-sm-10">
                                <input type="text" id="nombreC" name="nombreC" class="form-control" required <?= $disabledPermiso; ?>>
                            </div>
                        </div>

                        <?php
                        $opcionesConsulta = Grados::listarGrados(1);
                        $numCursos = mysqli_num_rows($opcionesConsulta);
                        if ($numCursos > 0) {
                        ?>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Curso Siguiente</label>
                                <div class="col-sm-10">
                                    <select class="form-control  select2" name="graSiguiente" <?= $disabledPermiso; ?>>
                                        <option value="">Seleccione una opción</option>
                                        <?php
                                        while ($opcionesDatos = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
                                        ?>
                                            <option value="<?= $opcionesDatos['gra_id']; ?>"><?= strtoupper($opcionesDatos['gra_nombre']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Valor Matricula</label>
                            <div class="col-sm-10">
                                <input type="text" name="valorM" class="form-control" value="0" <?= $disabledPermiso; ?>>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Valor Pension</label>
                            <div class="col-sm-10">
                                <input type="text" name="valorP" class="form-control" value="0" <?= $disabledPermiso; ?>>
                            </div>
                        </div>

                        <?php if (array_key_exists(10, $arregloModulos)) { ?>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Tipo de grado</label>
                                <div class="col-sm-2">
                                    <select class="form-control  select2" name="tipoG" id="tipoG" onchange="mostrarEstudiantes(this)">
                                        <option selected value=<?= GRADO_GRUPAL; ?>><?= GRADO_GRUPAL ?></option>
                                        <option value=<?= GRADO_INDIVIDUAL; ?>><?= GRADO_INDIVIDUAL ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php if (array_key_exists(10, $arregloModulos)) { ?>
                <div class="tab-pane fade" id="nav-configuracion" role="tabpanel" aria-labelledby="nav-configuracion-tab">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="form-group row">
                                <div class="col-sm-2">
                                </div>

                                <div class="col-sm-8">
                                    <div id="gifCarga" class="gif-carga">
                                        <img alt="Cargando...">
                                    </div>
                                    <img id="imagenSelect" class="cursor-mano" src="../files/cursos/curso.png" alt="avatar" style="height: 400px;width: 100%;border:3px dashed;padding:10px;border-radius:40px / 30px">
                                </div>
                                <div class="col-sm-2">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">imagen
                                    <button type="button" data-toggle="tooltip" data-placement="left" title="Genera una imagen con inteligencia artificial teniendo en cuenta el nombre del curso" onclick="generar('imagen')" class="btn btn-sm btn-info"><i class="fa-regular fa-image"></i></button>
                                </label>
                                <div class="col-sm-10">
                                    <input type="file" id="imagenCurso" name="imagenCurso" onChange="mostrarImagen('imagenCurso','imagenSelect')" accept=".png, .jpg, .jpeg" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Descripcion
                                    <button type="button" data-toggle="tooltip" data-placement="left" title="Genera una descripcion con inteligencia artificial teniendo en cuenta el nombre del curso" onclick="generar('descripcion')" class="btn btn-sm btn-info"><i class="far fa-comment-alt"></i></button>
                                </label>
                                <div class="col-sm-10">
                                    <textarea cols="80" id="editor1" name="descripcion" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?= $disabledPermiso; ?>></textarea>
                                    <div id="gifCarga2" class="gif-carga">
                                        <img alt="Cargando...">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Contenido
                                    <button type="button" data-toggle="tooltip" data-placement="left" title="Genera un contenido con inteligencia artificial teniendo en cuenta el nombre del curso" onclick="generar('contenido')" class="btn btn-sm btn-info"><i class="far fa-comment-alt"></i></button>
                                </label>
                                <div class="col-sm-10">
                                    <textarea cols="80" id="editor2" name="contenido" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?= $disabledPermiso; ?>></textarea>
                                    <div id="gifCarga3" class="gif-carga">
                                        <img alt="Cargando...">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Precio</label>
                                <div class="col-sm-4">
                                    <input type="number" name="precio" class="form-control" value="0" <?= $disabledPermiso; ?>>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Minimo de estudiantes</label>
                                <div class="input-group spinner col-sm-2">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info" data-dir="dwn" type="button">
                                            <span class="fa fa-minus"></span>
                                        </button>
                                    </span>
                                    <input type="number" name="minEstudiantes" class="form-control text-center" value="1" min="1">
                                    <span class="input-group-btn">
                                        <button class="btn btn-danger" data-dir="up" type="button">
                                            <span class="fa fa-plus"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Maximo de estudiantes</label>

                                <div class="input-group spinner col-sm-2">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info" data-dir="dwn" type="button">
                                            <span class="fa fa-minus"></span>
                                        </button>
                                    </span>
                                    <input type="number" name="maxEstudiantes" class="form-control text-center" value="1" <?= $disabledPermiso; ?> min="1">
                                    <span class="input-group-btn">
                                        <button class="btn btn-danger" data-dir="up" type="button">
                                            <span class="fa fa-plus"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">Duracion en horas</label>
                                <div class="input-group spinner col-sm-2">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info" data-dir="dwn" type="button">
                                            <span class="fa fa-minus"></span>
                                        </button>
                                    </span>
                                    <input type="number" id="horas" name="horas" class="form-control text-center" value="1" min="1" <?= $disabledPermiso; ?>>
                                    <span class="input-group-btn">
                                        <button class="btn btn-danger" data-dir="up" type="button">
                                            <span class="fa fa-plus"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label">
                                    <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Los cursos que estén marcado con esta opción permitirán que cualquiera pueda participar del curso"><i class="fa fa-question"></i></button>
                                    Auto Enrollment</label>
                                <div class="col-sm-10">
                                    <label class="switchToggle">
                                        <input name="autoenrollment" type="checkbox">
                                        <span class="slider green round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label" title="Los cursos que estén marcados como no activos no podrán ser manipulados">
                                    Activo</label>
                                <div class="col-sm-10" title="Los cursos que estén marcados como no activos no podrán ser manipulados">
                                    <label class="switchToggle" title="Los cursos que estén marcados como no activos no podrán ser manipulados">
                                        <input name="activo" type="checkbox" title="Los cursos que estén marcados como no activos no podrán ser manipulados">
                                        <span class="slider green round" title="Los cursos que estén marcados como no activos no podrán ser manipulados"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php  
              $botones = new botonesGuardar(null,Modulos::validarPermisoEdicion()); ?>
        </div>
    </form>
</div>

<!-- end js include path -->
<script src="../ckeditor/ckeditor.js"></script>

<script type="text/javascript">
    function generar(tipo) {
        var valor = document.getElementById('nombreC').value;
        if (valor) {

            switch (tipo) {
                case 'imagen':
                    generarImagen(valor);
                    break;
                case 'descripcion':
                    generarDescripcion(valor);
                    break;
                case 'contenido':
                    generarContenido(valor)
                    break;
            }

        } else {
            Swal.fire({
                position: "top-end",
                icon: "warning",
                title: 'Ingrese el nombre del Curso',
                showConfirmButton: false,
                timer: 150000
            });
        }

    }

    function generarImagen(valor) {
        document.getElementById("gifCarga").style.display = "block";
        imagenSelect = document.getElementById('imagenSelect');
        var data = {
            'metodo': '<?php echo TEXT_TO_IMAGE ?>',
            'valor': 'Crear una imagen llamativa para un curso que haga referencia al nombre de ' + valor
        };
        fetch('../openAi/metodos.php', {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(data), // data can be `string` or {object}!
                headers: {
                    'Content-Type': 'application/json'
                },
            })
            .then((res) => res.json())
            .catch((error) => console.error('Error:', error))
            .then(
                function(response) {
                    {
                        document.getElementById("gifCarga").style.display = "none";
                        console.log(response);
                        if (response["ok"]) {
                            url = response["url"];
                            imagenSelect.src = url;
                            imagenSelect.classList.add('animate__animated', 'animate__fadeIn');
                            fetch(url)
                                .then(response => response.blob()) // Convertir la respuesta a un objeto Blob
                                .then(blob => {
                                    // Crear un objeto File a partir del Blob
                                    var file = new File([blob], 'imagen.jpg', {
                                        type: 'image/jpeg'
                                    });
                                    if (file) {
                                        var fileList = new DataTransfer(); // Crear un objeto FileList que contenga el archivo
                                        fileList.items.add(file);
                                        var imagenCursoAi = document.getElementById('imagenCurso'); // Establecer el objeto FileList en el campo de entrada de tipo 'file'
                                        imagenCursoAi.files = fileList.files;
                                    }
                                });

                        }
                    };
                });
    }

    function generarDescripcion(valor) {
        var buscar = "Creame un descripcion para  realizar un curso con el nombre de " + valor + " el resultado en formato html solamente la etiqueta body";
        var editor = CKEDITOR.instances.editor1;
        ejecutarFetch(buscar, "gifCarga2", editor);
    }

    function generarContenido(valor) {
        var buscar = "Creame una lista de contenido para  realizar un curso con el nombre de " + valor + " el resultado en formato html solamente el contenido la etiqueta body";
        var editor = CKEDITOR.instances.editor2;
        ejecutarFetch(buscar, "gifCarga3", editor);
    }

    function ejecutarFetch(valor, carangando, editor) {
        var data = {
            'metodo': '<?php echo TEXT_TO_TEXT ?>',
            'valor': valor
        };
        document.getElementById(carangando).style.display = "block";
        fetch('../openAi/metodos.php', {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(data), // data can be `string` or {object}!
                headers: {
                    'Content-Type': 'application/json'
                },
            })
            .then((res) => res.json())
            .catch((error) => console.error('Error:', error))
            .then(
                function(response) {
                    {
                        document.getElementById(carangando).style.display = "none";
                        console.log(response);
                        if (response["ok"]) {
                            editor.setData(response["valor"]);

                        }


                    };
                });
    }
    CKEDITOR.replace('editor1');
    CKEDITOR.replace('editor2');

    function mostrarEstudiantes(data) {
        const navInfo = document.getElementById("nav-informacion-tab");
        const navConfig = document.getElementById("nav-configuracion-tab");
        const contentInfo = document.getElementById("nav-informacion");
        const contentConfigure = document.getElementById("nav-configuracion");
        if (data.value == "<?= GRADO_INDIVIDUAL ?>") {
            navConfig.style.display = "block";
            contentConfigure.classList.add('show', 'active');
            contentInfo.classList.remove('show', 'active');
            navInfo.classList.remove('show', 'active');
            navConfig.classList.add('show', 'active');
            document.getElementById("horas").disabled = false;
        } else {
            navConfig.style.display = "none";
            contentInfo.classList.add('show', 'active');
            contentConfigure.classList.remove('show', 'active');
            navConfig.classList.remove('show', 'active');
            navInfo.classList.add('show', 'active');
            document.getElementById("horas").disabled = true;
        }
    }
</script>