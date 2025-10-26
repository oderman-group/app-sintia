<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0060';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>

	<!-- steps -->
	<link rel="stylesheet" href="../../config-general/assets/plugins/steps/steps.css"> 

	<!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">

    <!-- Custom Modern Styles -->
    <style>
        /* Variables */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.15);
            --radius-md: 12px;
            --radius-lg: 16px;
        }
        
        /* Page Header */
        .config-page-header {
            background: var(--info-gradient);
            padding: 40px 30px;
            border-radius: var(--radius-lg);
            margin-bottom: 30px;
            box-shadow: var(--shadow-lg);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .config-page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .config-page-header .header-content {
            position: relative;
            z-index: 1;
        }
        
        .config-page-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .config-page-header h1 i {
            font-size: 36px;
            opacity: 0.9;
        }
        
        .config-page-header p {
            font-size: 16px;
            opacity: 0.95;
            margin: 0;
        }
        
        /* Modern Tabs */
        .nav-tabs-modern {
            background: white;
            padding: 15px 20px 0 20px;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            box-shadow: var(--shadow-sm);
            border: none;
            margin-bottom: 0;
        }
        
        .nav-tabs-modern .nav-item {
            margin-right: 10px;
        }
        
        .nav-tabs-modern .nav-link {
            border: none;
            background: transparent;
            color: #6b7280;
            padding: 12px 24px;
            border-radius: var(--radius-md) var(--radius-md) 0 0;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-tabs-modern .nav-link i {
            font-size: 18px;
        }
        
        .nav-tabs-modern .nav-link:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .nav-tabs-modern .nav-link.active {
            background: var(--info-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .nav-tabs-modern .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: white;
            border-radius: 3px 3px 0 0;
        }
        
        /* Tab Content Container */
        .tab-content-modern {
            background: white;
            padding: 40px;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            box-shadow: var(--shadow-md);
            min-height: 400px;
        }
        
        /* Fix tab overlap issue - force hide inactive tabs */
        .tab-content-modern .tab-pane {
            display: none;
        }
        
        .tab-content-modern .tab-pane.active.show {
            display: block;
        }
        
        /* Form Groups Modern */
        .form-group-modern {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: var(--radius-md);
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .form-group-modern:hover {
            box-shadow: var(--shadow-sm);
            border-color: #d1d5db;
        }
        
        .form-group-modern label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group-modern label i {
            color: #3b82f6;
        }
        
        .form-group-modern .form-control,
        .form-group-modern select,
        .form-group-modern textarea {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 15px;
            min-height: 45px;
            transition: all 0.3s ease;
        }
        
        .form-group-modern .form-control:focus,
        .form-group-modern select:focus,
        .form-group-modern textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        select.form-control {
            padding: 12px 15px;
            min-height: 45px;
            font-size: 15px;
        }
        
        /* Logo Preview */
        .logo-preview-container {
            background: white;
            border: 2px dashed #e5e7eb;
            border-radius: var(--radius-md);
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .logo-preview-container:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.02);
        }
        
        .logo-preview-container img {
            max-width: 250px;
            height: auto;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            margin-bottom: 20px;
        }
        
        .logo-upload-btn {
            display: inline-block;
            background: var(--info-gradient);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .logo-upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .logo-upload-btn i {
            margin-right: 8px;
        }
        
        /* Required Indicator */
        .required-indicator {
            color: #ef4444;
            font-weight: 700;
            margin-left: 3px;
        }
        
        /* Save Button Container */
        .save-button-container {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(37, 99, 235, 0.05) 100%);
            border-top: 2px solid #e5e7eb;
            padding: 25px;
            margin: 30px -40px -40px -40px;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            text-align: right;
        }
        
        .btn-modern-primary {
            background: var(--info-gradient);
            border: none;
            color: white;
            padding: 14px 40px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white;
        }
        
        /* Section Titles */
        .section-title {
            color: #3b82f6;
            font-weight: 700;
            margin: 30px 0 20px 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        /* Primera sección de cada tab sin margin-top */
        .tab-pane > .section-title:first-of-type,
        .tab-pane > .alert-modern + .section-title {
            margin-top: 0;
        }
        
        .section-title i {
            font-size: 22px;
        }
        
        /* Alert Modern */
        .alert-modern {
            border-radius: var(--radius-md);
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-sm);
        }
        
        .alert-modern i {
            font-size: 24px;
        }
        
        .alert-modern.alert-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%);
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .config-page-header {
                padding: 30px 20px;
            }
            
            .config-page-header h1 {
                font-size: 24px;
            }
            
            .nav-tabs-modern {
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .tab-content-modern {
                padding: 25px 20px;
            }
        }
        
        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-overlay.active {
            display: flex;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <!-- start header -->
		<?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
            <div class="page-content-wrapper">
                <div class="page-content">
                    
                    <!-- Modern Page Header -->
                    <div class="config-page-header">
                        <div class="header-content">
                            <h1>
                                <i class="fa fa-building"></i>
                                <?=$frases[17][$datosUsuarioActual['uss_idioma']];?> de la Institución
                            </h1>
                            <p>
                                Gestiona toda la información de tu institución educativa en un solo lugar
                            </p>
                        </div>
                    </div>

                    <?php 
                    try{
                        $consultaDatosInf=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_informacion WHERE info_institucion='" . $config['conf_id_institucion'] . "' AND info_year='" . $_SESSION["bd"] . "';");
                    } catch (Exception $e) {
                        include("../compartido/error-catch-to-report.php");
                    }
                    $datosinf= mysqli_fetch_array($consultaDatosInf, MYSQLI_BOTH);
                    ?>

                    <!-- Modern Tabs -->
                    <nav>
                        <div class="nav nav-tabs nav-tabs-modern" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" data-toggle="tab" href="#info-basica" role="tab" aria-selected="true">
                                <i class="fa fa-info-circle"></i>
                                Información Básica
                            </a>
                            <a class="nav-item nav-link" data-toggle="tab" href="#info-academica" role="tab" aria-selected="false">
                                <i class="fa fa-graduation-cap"></i>
                                Información Académica
                            </a>
                        </div>
                    </nav>

                    <!-- Tab Content -->
                    <div class="tab-content tab-content-modern" id="nav-tabContent">
                        <form name="example_advanced_form" id="example-advanced-form" action="configuracion-institucion-guardar.php" method="post" enctype="multipart/form-data">
                            
                            <input type="hidden" name="idCI" value="<?=$datosinf["info_id"];?>">
                            <input type="hidden" name="logoAnterior" value="<?=$datosinf["info_logo"];?>">
                            
                            <!-- Tab 1: Información Básica -->
                            <div class="tab-pane fade show active" id="info-basica" role="tabpanel">
                                
                                <div class="alert-modern alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    <div>
                                        <strong>Información Básica:</strong> Completa los datos generales y de identificación de tu institución.
                                    </div>
                                </div>

                                <!-- Logo Section -->
                                <div class="section-title">
                                    <i class="fa fa-image"></i>
                                    Logo Institucional
                                </div>

                                <div class="form-group-modern">
                                    <?php
                                        $infoLogo="sintia-logo-2023.png";
                                        if(isset($datosinf["info_logo"]) && $datosinf["info_logo"]!=""){
                                            $infoLogo=$datosinf["info_logo"];
                                        }
                                    ?>
                                    <div class="logo-preview-container">
                                        <div style="margin-bottom: 15px; color: #6b7280; font-weight: 600;">
                                            <i class="fa fa-picture-o"></i> Vista Previa del Logo
                                        </div>
                                        <img src="../files/images/logo/<?=$infoLogo;?>" alt="<?=$infoLogo;?>" id="logo-preview">
                                        <div style="margin-top: 20px;">
                                            <label for="logo-input" class="logo-upload-btn">
                                                <i class="fa fa-upload"></i>
                                                Cambiar Logo
                                            </label>
                                            <input type="file" name="logo" id="logo-input" class="form-control" style="display: none;" accept="image/*">
                                            <div style="margin-top: 10px; font-size: 13px; color: #9ca3af;">
                                                Formatos: JPG, PNG, SVG (Máx. 2MB)
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Identificación Section -->
                                <div class="section-title">
                                    <i class="fa fa-id-card"></i>
                                    Identificación Legal
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-hashtag"></i>
                                                    NIT
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="nitI" class="form-control" value="<?= $datosinf["info_nit"];?>" placeholder="Ej: 900123456-7">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-barcode"></i>
                                                    Código DANE
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="dane" class="form-control" value="<?= $datosinf["info_dane"];?>" placeholder="Código DANE">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información General Section -->
                                <div class="section-title">
                                    <i class="fa fa-building-o"></i>
                                    Información General
                                </div>

                                <div class="form-group-modern">
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">
                                            <i class="fa fa-university"></i>
                                            Nombre de la Institución
                                            <span class="required-indicator">*</span>
                                        </label>
                                        <div class="col-sm-10">
                                            <input name="nomInstI" class="form-control" type="text" required value="<?=$datosinf["info_nombre"];?>" placeholder="Nombre completo de la institución">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-map-marker"></i>
                                                    Dirección
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input name="direccionI" class="form-control" type="text" required value="<?=$datosinf["info_direccion"];?>" placeholder="Dirección completa">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-globe"></i>
                                                    Ciudad
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <select class="form-control select2" name="ciudad" required>
                                                        <option value="">Seleccione una opción</option>
                                                        <?php
                                                        try{
                                                            $opcionesG = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".localidad_ciudades
                                                            INNER JOIN ".BD_ADMIN.".localidad_departamentos ON dep_id=ciu_departamento 
                                                            ORDER BY ciu_nombre ");
                                                        } catch (Exception $e) {
                                                            include("../compartido/error-catch-to-report.php");
                                                        }
                                                        while($opg = mysqli_fetch_array($opcionesG, MYSQLI_BOTH)){
                                                        $selected='';
                                                        if($opg['ciu_id']==$datosinf['info_ciudad']){
                                                            $selected='selected';
                                                        }
                                                        ?>
                                                        <option value="<?=$opg['ciu_id'];?>" <?=$selected;?>><?=$opg['ciu_nombre'].", ".$opg['dep_nombre'];?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label">
                                            <i class="fa fa-phone"></i>
                                            Teléfono
                                            <span class="required-indicator">*</span>
                                        </label>
                                        <div class="col-sm-4">
                                            <input name="telI" class="form-control" type="text" data-mask="999-9999" required value="<?=$datosinf["info_telefono"]?>" placeholder="XXX-XXXX">
                                        </div>
                                    </div>
                                </div>

                                <!-- Características Institucionales Section -->
                                <div class="section-title">
                                    <i class="fa fa-list-alt"></i>
                                    Características Institucionales
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-tags"></i>
                                                    Clase
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input name="calseI" class="form-control" type="text" required value="<?=$datosinf["info_clase"]?>" placeholder="Ej: Oficial, Privado">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-calendar"></i>
                                                    Calendario
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input name="calendarioI" class="form-control" type="text" required value="<?=$datosinf["info_calendario"]?>" placeholder="Ej: A, B">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-clock-o"></i>
                                                    Horario
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input name="horarioI" class="form-control" type="text" required value="<?=$datosinf["info_horario"]?>" placeholder="Ej: 7:00 AM - 2:00 PM">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-cubes"></i>
                                                    Modalidad
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input name="modalidadI" class="form-control" type="text" required value="<?=$datosinf["info_modalidad"]?>" placeholder="Ej: Académico, Técnico">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-star"></i>
                                                    Carácter
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input name="caracterI" class="form-control" type="text" required value="<?=$datosinf["info_caracter"]?>" placeholder="Ej: Mixto, Femenino">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-sun-o"></i>
                                                    Jornada
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input name="jornadaI" class="form-control" type="text" required value="<?=$datosinf["info_jornada"]?>" placeholder="Ej: Mañana, Tarde, Única">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-signal"></i>
                                                    Niveles
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input name="nivelesI" class="form-control" type="text" required value="<?=$datosinf["info_niveles"]?>" placeholder="Ej: Preescolar, Básica, Media">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group-modern">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">
                                                    <i class="fa fa-user"></i>
                                                    Propietario
                                                    <span class="required-indicator">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input name="propietarioI" class="form-control" type="text" required value="<?=$datosinf["info_propietario"]?>" placeholder="Nombre del propietario">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Documentos Legales Section -->
                                <div class="section-title">
                                    <i class="fa fa-file-text-o"></i>
                                    Documentos Legales
                                </div>

                                <div class="form-group-modern">
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">
                                            <i class="fa fa-certificate"></i>
                                            Resolución para Certificados
                                        </label>
                                        <div class="col-sm-9">
                                            <textarea name="resolucion" class="form-control" rows="3" placeholder="Ingrese la resolución completa..."><?=$datosinf["info_resolucion"]?></textarea>
                                            <small class="form-text text-muted">Esta información aparecerá en los certificados oficiales</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">
                                            <i class="fa fa-legal"></i>
                                            Decretos de Plan de Estudio
                                        </label>
                                        <div class="col-sm-9">
                                            <textarea name="decretos" class="form-control" rows="3" placeholder="Ingrese los decretos del plan de estudio..."><?=$datosinf["info_decreto_plan_estudio"]?></textarea>
                                            <small class="form-text text-muted">Decretos aplicables al plan de estudios institucional</small>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Tab 2: Información Académica -->
                            <div class="tab-pane fade" id="info-academica" role="tabpanel">
                                
                                <div class="alert-modern alert-info">
                                    <i class="fa fa-graduation-cap"></i>
                                    <div>
                                        <strong>Información Académica:</strong> Asigna los roles de directivos responsables de la institución.
                                    </div>
                                </div>

                                <div class="section-title">
                                    <i class="fa fa-users"></i>
                                    Equipo Directivo
                                </div>

                                <div class="form-group-modern">
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">
                                            <i class="fa fa-user-md"></i>
                                            Rector(a)
                                            <span class="required-indicator">*</span>
                                        </label>
                                        <div class="col-sm-9">
                                            <?php
                                            $consulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=".TIPO_DIRECTIVO." and uss_bloqueado=0");
                                            ?>
                                            <select class="form-control select2" name="rectorI" required>
                                                <option value="">Seleccione un rector</option>
                                                <?php 
                                                while($r=mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                $selected = ($datosinf["info_rector"]==$r["uss_id"]) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $r["uss_id"]; ?>" <?=$selected;?>><?php echo UsuariosPadre::nombreCompletoDelUsuario($r);?></option>
                                                <?php } ?>
                                            </select>
                                            <small class="form-text text-muted">Representante legal de la institución</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">
                                            <i class="fa fa-user-circle"></i>
                                            Secretario(a) Académico(a)
                                            <span class="required-indicator">*</span>
                                        </label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="secretarioI" required>
                                                <option value="">Seleccione un secretario</option>
                                                <?php 
                                                $consulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=".TIPO_DIRECTIVO." and uss_bloqueado=0");
                                                while($r=mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                $selected = ($datosinf["info_secretaria_academica"]==$r["uss_id"]) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $r["uss_id"]; ?>" <?=$selected;?>><?php echo UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                <?php } ?>
                                            </select>
                                            <small class="form-text text-muted">Responsable de la gestión académica</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">
                                            <i class="fa fa-user-circle-o"></i>
                                            Coordinador(a) Académico(a)
                                        </label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="coordinadorI">
                                                <option value="">Seleccione un coordinador</option>
                                                <?php 
                                                $consulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=".TIPO_DIRECTIVO." and uss_bloqueado=0");
                                                while($r=mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                $selected = ($datosinf["info_coordinador_academico"]==$r["uss_id"]) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $r["uss_id"]; ?>" <?=$selected;?>><?php echo UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                <?php } ?>
                                            </select>
                                            <small class="form-text text-muted">Coordinación del área académica (opcional)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">
                                            <i class="fa fa-money"></i>
                                            Tesorero(a)
                                        </label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="tesoreroI">
                                                <option value="">Seleccione un tesorero</option>
                                                <?php 
                                                $consulta = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo=".TIPO_DIRECTIVO." and uss_bloqueado=0");
                                                while($r=mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                $selected = ($datosinf["info_tesorero"]==$r["uss_id"]) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $r["uss_id"]; ?>" <?=$selected;?>><?php echo UsuariosPadre::nombreCompletoDelUsuario($r); ?></option>
                                                <?php } ?>
                                            </select>
                                            <small class="form-text text-muted">Responsable del área financiera (opcional)</small>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Save Button -->
                            <div class="save-button-container">
                                <button type="submit" class="btn btn-modern-primary">
                                    <i class="fa fa-save"></i>
                                    Guardar Configuración
                                </button>
                            </div>

                        </form>
                    </div>

                    <div id="wizard" style="display: none;"></div>
                     
                </div>
            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <!-- start footer -->
        <?php include("../compartido/footer.php");?>
        <!-- end footer -->
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
	<script src="../../config-general/assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <!-- steps -->
    <script src="../../config-general/assets/plugins/steps/jquery.steps.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>

	<script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- end js include path -->

    <script>
        // Logo preview on file change
        document.getElementById('logo-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Show loading on form submit
        $('form').on('submit', function() {
            $('#loadingOverlay').addClass('active');
        });
        
        // Smooth scroll on tab change
        $('.nav-tabs-modern .nav-link').on('click', function() {
            $('html, body').animate({
                scrollTop: $('.config-page-header').offset().top - 100
            }, 500);
        });
        
        // Initialize select2
        $('.select2').select2();
    </script>

</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/wizard.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:55 GMT -->
</html>
