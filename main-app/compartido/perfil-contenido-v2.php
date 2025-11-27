
<style>
/* Estilos para el perfil mejorado */
:root {
    --sintia-primary-bg: #ffffff;
    --sintia-secondary: #41c4c4;
    --sintia-accent: #6017dc;
    --sintia-text-primary: #333333;
    --sintia-text-secondary: #666666;
    --sintia-text-muted: #999999;
    --sintia-bg-light: #f8f9fa;
    --sintia-bg-hover: #f4f6f9;
    --sintia-border: #e9ecef;
}

.profile-header {
    background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
    padding: 30px;
    border-radius: 10px 10px 0 0;
    color: white;
    margin-bottom: 0;
}

.profile-avatar-section {
    text-align: center;
    margin-bottom: 20px;
}

.profile-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 5px solid white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
}

.profile-avatar-upload {
    position: relative;
    display: inline-block;
}

.profile-avatar-overlay {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.profile-avatar-upload:hover .profile-avatar-overlay {
    opacity: 1;
}

.profile-avatar-overlay i {
    font-size: 30px;
    color: white;
}

.profile-name {
    font-size: 26px;
    font-weight: 600;
    margin: 15px 0 5px 0;
}

.profile-email {
    font-size: 14px;
    opacity: 0.9;
}

.nav-tabs-custom {
    border-bottom: 2px solid var(--sintia-border);
    margin-bottom: 30px;
}

.nav-tabs-custom .nav-link {
    border: none;
    color: var(--sintia-text-secondary);
    padding: 15px 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
}

.nav-tabs-custom .nav-link:hover {
    color: var(--sintia-secondary);
    background-color: var(--sintia-bg-hover);
}

.nav-tabs-custom .nav-link.active {
    color: var(--sintia-accent);
    background-color: transparent;
}

.nav-tabs-custom .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
}

.form-section {
    background: var(--sintia-primary-bg);
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.form-section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--sintia-accent);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--sintia-border);
    display: flex;
    align-items: center;
}

.form-section-title i {
    margin-right: 10px;
    color: var(--sintia-secondary);
}

.form-group.row {
    margin-bottom: 20px;
}

.form-group label {
    color: var(--sintia-text-primary);
    font-weight: 500;
    margin-bottom: 8px;
}

.form-control:focus {
    border-color: var(--sintia-secondary);
    box-shadow: 0 0 0 0.2rem rgba(65, 196, 196, 0.25);
}

.btn-primary-sintia {
    background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    font-weight: 500;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.btn-primary-sintia:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(96, 23, 220, 0.3);
}

.alert-info-custom {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    border-left: 4px solid var(--sintia-accent);
    padding: 15px 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.signature-preview {
    max-width: 200px;
    border: 2px dashed var(--sintia-border);
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
}

.signature-preview img {
    max-width: 100%;
    height: auto;
}

/* Modal para recorte de imagen */
.modal-croppie {
    z-index: 9999;
}

.croppie-container {
    padding: 20px 0;
}

.required-field::after {
    content: '*';
    color: #dc3545;
    margin-left: 3px;
}

.tab-icon {
    margin-right: 8px;
}

/* Spinner toggle mejorado */
.switchToggle {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
    vertical-align: middle;
}

.switchToggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: var(--sintia-secondary);
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.slider.round {
    border-radius: 24px;
}

.slider.round:before {
    border-radius: 50%;
}

input:checked + .slider.red,
input:checked + .slider.aqua {
    background-color: var(--sintia-secondary);
}

.spinner .btn {
    border-radius: 4px;
    min-width: 40px;
}

/* Fix para Select2 en tabs */
.select2-container {
    width: 100% !important;
}

.select2-container .select2-selection--single {
    height: 38px;
    padding: 6px 12px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 24px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

/* Estilos para el recorte de imagen */
#imagePreview {
    max-width: 100%;
    margin-top: 10px;
    display: none;
}

.file-upload-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
}

.file-upload-label {
    background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-upload-label:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(96, 23, 220, 0.3);
}

.file-upload-input {
    position: absolute;
    left: -9999px;
}
</style>

<link rel="stylesheet" href="../../librerias/croppie/croppie.css">
<script src="../../librerias/croppie/croppie.js"></script>

<?php
$usuarioPerfil = UsuariosPadre::sesionUsuario($_SESSION["id"]);
?>

<div class="col-12">
    <!-- Header del perfil -->
    <div class="profile-header">
        <div class="profile-avatar-section">
            <div class="profile-avatar-upload">
                <img src="../files/fotos/<?=$usuarioPerfil['uss_foto'];?>?v=<?=time();?>" 
                     alt="Foto de perfil" 
                     class="profile-avatar" 
                     id="profileImage">
                <div class="profile-avatar-overlay" onclick="document.getElementById('fotoPerfil').click()">
                    <i class="fa fa-camera"></i>
                </div>
            </div>
            <div class="profile-name">
                <?=$usuarioPerfil["uss_nombre"];?> <?=$usuarioPerfil["uss_apellido1"];?>
            </div>
            <div class="profile-email">
                <?=$usuarioPerfil["uss_email"];?>
            </div>
        </div>
    </div>

    <!-- Contenido del formulario -->
    <div class="card card-box" style="border-radius: 0 0 10px 10px; border-top: none;">
        <div class="card-body">
            
            <div class="alert-info-custom">
                <i class="fa fa-info-circle"></i> 
                <strong>Actualiza tu información:</strong> Los campos marcados con <span style="color: red;">*</span> son obligatorios. El resto de la información es opcional pero nos ayuda a brindarte una mejor experiencia.
            </div>

            <!-- Pestañas -->
            <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-basica" role="tab">
                        <i class="fa fa-user tab-icon"></i>Información Básica
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-personal" role="tab">
                        <i class="fa fa-id-card tab-icon"></i>Información Personal
                    </a>
                </li>
                <?php if($usuarioPerfil['uss_tipo'] != TIPO_ESTUDIANTE){ ?>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-profesional" role="tab">
                        <i class="fa fa-briefcase tab-icon"></i>Información Profesional
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-residencial" role="tab">
                        <i class="fa fa-home tab-icon"></i>Información Residencial
                    </a>
                </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-preferencias" role="tab">
                        <i class="fa fa-cog tab-icon"></i>Preferencias
                    </a>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <form name="formularioGuardar" id="formularioPerfil" action="../compartido/perfil-actualizar.php" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="tipoUsuario" value="<?=$usuarioPerfil['uss_tipo'];?>">
                <input type="hidden" name="fotoRecortada" id="fotoRecortada" value="">

                <div class="tab-content" style="padding: 30px 0;">
                    
                    <!-- TAB 1: INFORMACIÓN BÁSICA -->
                    <div class="tab-pane fade show active" id="tab-basica" role="tabpanel">
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-user"></i>
                                Datos Básicos
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required-field">Primer Nombre</label>
                                        <input type="text" 
                                               value="<?=$usuarioPerfil["uss_nombre"];?>" 
                                               name="nombre" 
                                               class="form-control" 
                                               <?= $usuarioPerfil['uss_tipo'] == TIPO_ESTUDIANTE ? "readonly" : "required";?> 
                                               style="text-transform: uppercase;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Segundo Nombre</label>
                                        <input type="text" 
                                               value="<?=$usuarioPerfil["uss_nombre2"];?>" 
                                               name="nombre2" 
                                               class="form-control" 
                                               <?= $usuarioPerfil['uss_tipo'] == TIPO_ESTUDIANTE ? "readonly" : "";?> 
                                               style="text-transform: uppercase;">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required-field">Primer Apellido</label>
                                        <input type="text" 
                                               value="<?=$usuarioPerfil["uss_apellido1"];?>" 
                                               name="apellido1" 
                                               class="form-control" 
                                               <?= $usuarioPerfil['uss_tipo'] == TIPO_ESTUDIANTE ? "readonly" : "required";?> 
                                               style="text-transform: uppercase;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Segundo Apellido</label>
                                        <input type="text" 
                                               value="<?=$usuarioPerfil["uss_apellido2"];?>" 
                                               name="apellido2" 
                                               class="form-control" 
                                               <?= $usuarioPerfil['uss_tipo'] == TIPO_ESTUDIANTE ? "readonly" : "";?> 
                                               style="text-transform: uppercase;">
                                    </div>
                                </div>
                            </div>

                            <?php if ($usuarioPerfil['uss_tipo'] == TIPO_DIRECTIVO) {?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Documento de Identidad</label>
                                        <input type="text" 
                                               name="documento" 
                                               class="form-control" 
                                               value="<?=$usuarioPerfil['uss_documento'];?>">
                                    </div>
                                </div>
                            </div>
                            <?php }?>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Correo Electrónico</label>
                                        <input type="email" 
                                               value="<?=$usuarioPerfil["uss_email"];?>" 
                                               name="email" 
                                               class="form-control" 
                                               style="text-transform: lowercase;">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Celular</label>
                                        <input type="text" 
                                               value="<?=$usuarioPerfil["uss_celular"];?>" 
                                               name="celular" 
                                               data-mask="(999) 999-9999" 
                                               class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Teléfono Fijo</label>
                                        <input type="text" 
                                               value="<?=$usuarioPerfil["uss_telefono"];?>" 
                                               name="telefono" 
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if($usuarioPerfil['uss_tipo'] != TIPO_ESTUDIANTE || $config['conf_id_institucion'] != ICOLVEN) { ?>
                        <div class="form-section" style="margin-top: 20px;">
                            <h4 class="form-section-title">
                                <i class="fa fa-image"></i>
                                Foto y Firma Digital
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Foto de Perfil</label>
                                        <p class="text-muted small">Puedes subir tu foto en cualquier formato. Se recortará automáticamente en formato cuadrado.</p>
                                        <input type="file" 
                                               name="fotoPerfil" 
                                               id="fotoPerfil"
                                               accept=".png, .jpg, .jpeg" 
                                               class="form-control"
                                               onchange="handleImageUpload(this)">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Firma Digital</label>
                                        <input type="file" 
                                               name="firmaDigital" 
                                               accept=".png, .jpg, .jpeg" 
                                               class="form-control"
                                               onchange="validarPesoArchivo(this)">
                                        <?php if(!empty($usuarioPerfil['uss_firma'])){ ?>
                                        <div class="signature-preview">
                                            <img src="../files/fotos/<?=$usuarioPerfil['uss_firma'];?>?v=<?=time();?>" alt="Firma">
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <!-- TAB 2: INFORMACIÓN PERSONAL -->
                    <div class="tab-pane fade" id="tab-personal" role="tabpanel">
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-id-card"></i>
                                Información Personal
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Género</label>
                                        <select class="form-control select2" name="genero">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=4");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_genero"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha de Nacimiento</label>
                                        <div class="input-group date form_date" data-date-format="dd MM yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
                                            <input class="form-control" size="16" type="text" value="<?=$usuarioPerfil["uss_fecha_nacimiento"];?>" readonly>
                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        </div>
                                        <input type="hidden" id="dtp_input1" value="<?=$usuarioPerfil["uss_fecha_nacimiento"];?>" name="fechaN">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Lugar de Nacimiento</label>
                                        <select class="form-control select2" name="lNacimiento">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
                                            INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
                                            ");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['ciu_id'];?>" <?php if($opg['ciu_id']==$usuarioPerfil["uss_lugar_nacimiento"]){echo "selected";}?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Mostrar edad en el perfil</label>
                                        <div>
                                            <label class="switchToggle">
                                                <input type="checkbox" name="mostrarEdad" value="1" <?php if($usuarioPerfil["uss_mostrar_edad"]==1){echo "checked";}?>>
                                                <span class="slider aqua round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if($usuarioPerfil["uss_tipo"]!=TIPO_ESTUDIANTE){?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Estado Civil</label>
                                        <select class="form-control select2" name="eCivil">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=8");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_estado_civil"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Religión</label>
                                        <select class="form-control select2" name="religion">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=2");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_religion"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $numHijos = $usuarioPerfil["uss_numero_hijos"];
                                    if($usuarioPerfil["uss_numero_hijos"]=="") $numHijos = '0';
                                    ?>
                                    <div class="form-group">
                                        <label>Número de Hijos</label>
                                        <div class="input-group spinner">
                                            <span class="input-group-btn">
                                                <button class="btn btn-info" data-dir="dwn" type="button">
                                                    <span class="fa fa-minus"></span>
                                                </button>
                                            </span>
                                            <input type="text" class="form-control text-center" value="<?=$numHijos;?>" name="numeroHijos"> 
                                            <span class="input-group-btn">
                                                <button class="btn btn-danger" data-dir="up" type="button">
                                                    <span class="fa fa-plus"></span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                        </div>
                    </div>

                    <!-- TAB 3: INFORMACIÓN PROFESIONAL -->
                    <?php if($usuarioPerfil['uss_tipo'] != TIPO_ESTUDIANTE){ ?>
                    <div class="tab-pane fade" id="tab-profesional" role="tabpanel">
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-briefcase"></i>
                                Información Profesional
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nivel Académico</label>
                                        <select class="form-control select2" name="nAcademico">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=7");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_nivel_academico"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Área de Desempeño</label>
                                        <select class="form-control select2" name="profesion">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_profesiones_categorias ORDER BY catp_nombre ASC");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['catp_id'];?>" <?php if($opg['catp_id']==$usuarioPerfil["uss_profesion"]){echo "selected";}?>><?=$opg['catp_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Estado Laboral</label>
                                        <select class="form-control select2" name="eLaboral" onChange="empresario(this)">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=9");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_estado_laboral"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="empresario" style="display: <?=($usuarioPerfil["uss_estado_laboral"]==165)?'block':'none';?>; border: 2px solid var(--sintia-secondary); border-radius: 8px; padding: 20px; margin-top: 20px;">
                                <h5 style="color: var(--sintia-accent); margin-bottom: 15px;">
                                    <i class="fa fa-briefcase"></i> Información del Negocio
                                </h5>
                                <p class="text-muted">Complete esta información si es dueño de un negocio.</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tipo de Negocio</label>
                                            <select class="form-control select2" name="tipoNegocio" id="tipoNegocio">
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=10");
                                                while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                                ?>
                                                    <option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_tipo_negocio"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sitio Web del Negocio</label>
                                            <input type="text" 
                                                   value="<?=$usuarioPerfil["uss_sitio_web_negocio"];?>" 
                                                   name="web" 
                                                   class="form-control" 
                                                   placeholder="https://www.tunegocio.com">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: INFORMACIÓN RESIDENCIAL -->
                    <div class="tab-pane fade" id="tab-residencial" role="tabpanel">
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-home"></i>
                                Información Residencial
                            </h4>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Dirección de Residencia</label>
                                        <input type="text" 
                                               value="<?=$usuarioPerfil["uss_direccion"];?>" 
                                               name="direccion" 
                                               class="form-control" 
                                               placeholder="Ejemplo: Calle 123 #45-67">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Estrato</label>
                                        <select class="form-control select2" name="estrato">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=3");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_estrato"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipo de Vivienda</label>
                                        <select class="form-control select2" name="tipoVivienda">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=12");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_tipo_vivienda"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Medio de Transporte Usual</label>
                                        <select class="form-control select2" name="medioTransporte">
                                            <option value="">Seleccione una opción</option>
                                            <?php
                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=13");
                                            while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                            ?>
                                                <option value="<?=$opg['ogen_id'];?>" <?php if($opg['ogen_id']==$usuarioPerfil["uss_medio_transporte"]){echo "selected";}?>><?=$opg['ogen_nombre'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <!-- TAB 5: PREFERENCIAS -->
                    <div class="tab-pane fade" id="tab-preferencias" role="tabpanel">
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="fa fa-cog"></i>
                                Preferencias de Cuenta
                            </h4>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Recibir Notificaciones</label>
                                        <p class="text-muted small">Activa esta opción para recibir notificaciones por correo electrónico.</p>
                                        <label class="switchToggle">
                                            <input type="checkbox" name="notificaciones" value="1" <?php if($usuarioPerfil["uss_notificacion"]==1){echo "checked";}?>>
                                            <span class="slider red round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Última Actualización</label>
                                        <input type="text" 
                                               value="<?=$usuarioPerfil["uss_ultima_actualizacion"];?>" 
                                               class="form-control" 
                                               disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Botones de acción -->
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--sintia-border);">
                    <a href="#" name="index.php" class="btn btn-secondary" onClick="deseaRegresar(this)">
                        <i class="fa fa-long-arrow-left"></i> Regresar
                    </a>
                    <button type="submit" class="btn btn-primary-sintia" id="btnGuardarPerfil" style="float: right;">
                        <i class="fa fa-save" aria-hidden="true"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para recortar imagen -->
<div class="modal fade" id="cropImageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%); color: white;">
                <h5 class="modal-title">
                    <i class="fa fa-crop"></i> Recortar Imagen
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="croppie-container">
                    <div id="croppie-field"></div>
                </div>
                <div class="text-center" style="margin-top: 15px;">
                    <button class="btn btn-outline-secondary" id="rotate-left" type="button">
                        <i class="fa fa-rotate-left"></i> Rotar Izquierda
                    </button>
                    <button class="btn btn-outline-secondary" id="rotate-right" type="button">
                        <i class="fa fa-rotate-right"></i> Rotar Derecha
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary-sintia" id="crop-save-btn">
                    <i class="fa fa-check"></i> Recortar y Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
var croppieInstance = null;

// Función para validar peso de archivo
function validarPesoArchivo(input) {
    if (input.files && input.files[0]) {
        var fileSize = input.files[0].size / 1024 / 1024; // en MB
        if (fileSize > 1) {
            alert('El archivo es muy pesado. Por favor selecciona una imagen menor a 1 MB.');
            input.value = '';
            return false;
        }
    }
    return true;
}

// Función para manejar la carga de imagen
function handleImageUpload(input) {
    if (!validarPesoArchivo(input)) {
        return;
    }

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            // Inicializar Croppie si no existe
            if (croppieInstance) {
                croppieInstance.destroy();
            }

            croppieInstance = new Croppie($('#croppie-field')[0], {
                enableExif: true,
                enableResize: false,
                enableZoom: true,
                boundary: { width: 600, height: 600 },
                viewport: {
                    width: 400,
                    height: 400,
                    type: 'square'
                },
                enableOrientation: true
            });

            // Cargar la imagen en Croppie
            croppieInstance.bind({
                url: e.target.result
            });

            // Mostrar el modal
            $('#cropImageModal').modal('show');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Eventos del modal de recorte
$(document).ready(function() {
    // Reinicializar Select2 cuando se cambia de pestaña
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Reinicializar todos los select2 en la pestaña activa
        var target = $(e.target).attr("href");
        $(target).find('.select2').each(function() {
            $(this).select2({
                width: '100%',
                language: "es"
            });
        });
    });

    // Inicializar Select2 en la pestaña activa al cargar
    $('.tab-pane.active .select2').select2({
        width: '100%',
        language: "es"
    });

    // Rotar izquierda
    $('#rotate-left').click(function() {
        if (croppieInstance) {
            croppieInstance.rotate(90);
        }
    });

    // Rotar derecha
    $('#rotate-right').click(function() {
        if (croppieInstance) {
            croppieInstance.rotate(-90);
        }
    });

    // Guardar imagen recortada
    $('#crop-save-btn').click(function() {
        if (croppieInstance) {
            croppieInstance.result({
                type: 'base64',
                format: 'jpeg',
                quality: 0.9,
                circle: false
            }).then(function(imgBase64) {
                // Actualizar la imagen de perfil en la vista
                $('#profileImage').attr('src', imgBase64);
                
                // Guardar la imagen recortada en un campo oculto
                $('#fotoRecortada').val(imgBase64);
                
                // Cerrar el modal
                $('#cropImageModal').modal('hide');
                
                // Mostrar mensaje de éxito
                $.toast({
                    heading: 'Imagen recortada',
                    text: 'La imagen ha sido recortada correctamente. Recuerda guardar los cambios.',
                    position: 'top-right',
                    loaderBg: '#41c4c4',
                    icon: 'success',
                    hideAfter: 3000
                });
            });
        }
    });

    // Limpiar Croppie cuando se cierra el modal
    $('#cropImageModal').on('hidden.bs.modal', function() {
        if (croppieInstance) {
            croppieInstance.destroy();
            croppieInstance = null;
        }
    });
});

// Función para mostrar/ocultar sección de empresario
function empresario(datos) {
    var eLaboral = datos.value;
    var empresarioDiv = document.getElementById("empresario");
    var camposEmpresario = empresarioDiv.querySelectorAll("select");

    if (eLaboral == 165) {
        empresarioDiv.style.display = "block";
        // Hacer que los campos sean requeridos
        camposEmpresario.forEach(function(campo) {
            campo.required = true;
        });
    } else {
        empresarioDiv.style.display = "none";
        // Quitar requerimiento de los campos
        camposEmpresario.forEach(function(campo) {
            campo.required = false;
        });
    }
}

// Spinner para número de hijos
$('.spinner .btn:first-of-type').on('click', function() {
    var btn = $(this);
    var input = btn.closest('.spinner').find('input');
    if (btn.attr('data-dir') == 'up') {
        input.val(parseInt(input.val(), 10) + 1);
    } else {
        if (parseInt(input.val(), 10) > 0) {
            input.val(parseInt(input.val(), 10) - 1);
        }
    }
});

$('.spinner .btn:last-of-type').on('click', function() {
    var btn = $(this);
    var input = btn.closest('.spinner').find('input');
    if (btn.attr('data-dir') == 'up') {
        input.val(parseInt(input.val(), 10) + 1);
    } else {
        if (parseInt(input.val(), 10) > 0) {
            input.val(parseInt(input.val(), 10) - 1);
        }
    }
});

// Validación personalizada del formulario
$('#formularioPerfil').on('submit', function(e) {
    e.preventDefault();
    
    var errores = [];
    var primerError = null;
    var pestanaError = null;
    
    // Solo validar campos con required (ahora solo datos básicos)
    var camposRequeridos = $(this).find('[required]');
    
    if (camposRequeridos.length > 0) {
        camposRequeridos.each(function() {
            var campo = $(this);
            var valor = campo.val();
            var nombre = campo.attr('name');
            var label = campo.closest('.form-group').find('label').text().replace('*', '').trim();
            
            // Validar si el campo está vacío
            if (!valor || valor.trim() === '') {
                if (!primerError) {
                    primerError = campo;
                    // Encontrar en qué pestaña está el campo
                    var tabPane = campo.closest('.tab-pane');
                    if (tabPane.length) {
                        pestanaError = tabPane.attr('id');
                    }
                }
                errores.push(label || nombre);
            }
        });
        
        if (errores.length > 0) {
            // Cambiar a la pestaña donde está el primer error
            if (pestanaError) {
                $('.nav-tabs a[href="#' + pestanaError + '"]').tab('show');
                
                // Esperar a que se muestre la pestaña y luego hacer scroll
                setTimeout(function() {
                    if (primerError) {
                        // Scroll al primer campo con error
                        $('html, body').animate({
                            scrollTop: primerError.offset().top - 150
                        }, 500);
                        
                        // Intentar hacer foco en el campo
                        try {
                            primerError.focus();
                        } catch(e) {}
                    }
                }, 300);
            }
            
            // Mostrar mensaje de error
            $.toast({
                heading: 'Campos requeridos',
                text: 'Por favor completa todos los campos obligatorios (nombres y apellidos) antes de guardar.',
                position: 'top-right',
                loaderBg: '#dc3545',
                icon: 'error',
                hideAfter: 5000
            });
            
            return false;
        }
    }
    
    // Si todo está bien, desactivar el listener y enviar el formulario
    $(this).off('submit');
    $(this).submit();
});
</script>

