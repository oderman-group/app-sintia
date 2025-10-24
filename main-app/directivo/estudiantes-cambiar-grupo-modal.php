<?php

$idPaginaInterna = 'DT0083';

if (empty($_SESSION["id"])) {
    include("session.php");
    $input = json_decode(file_get_contents("php://input"), true);
    if (!empty($input)) {
        $_GET = $input;
    }
}

require_once(ROOT_PATH . "/main-app/class/Grados.php");
require_once(ROOT_PATH . "/main-app/class/Grupos.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/componentes/botones-guardar.php");
?>
    <style>
        /* ========================================
           ESTILOS MEJORADOS PARA MODAL CAMBIAR GRUPO
           ======================================== */
        
        .slider {
            position: absolute;
            cursor: pointer;
            transition: .4s;
            border-radius: 34px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            font-weight: bold;
            color: white;
        }
        
        /* Card para información del estudiante */
        .info-estudiante-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .info-estudiante-card h5 {
            margin: 0 0 10px 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .info-estudiante-card .detalle {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .info-estudiante-card .detalle i {
            margin-right: 10px;
            font-size: 16px;
        }
        
        /* Card para la tabla de notas */
        .notas-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .notas-card h6 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        /* Tabla de notas mejorada */
        .notas-card table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .notas-card table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 12px 8px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
            font-size: 13px;
        }
        
        .notas-card table tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        .notas-card table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .notas-card table tbody tr:hover {
            background: #f8f9fa;
        }
        
        /* Toggle switch mejorado */
        .toggle-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .toggle-container label {
            font-weight: 500;
            color: #495057;
        }
        
        /* Formulario más espaciado */
        .form-group.row {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 500;
            color: #495057;
            display: flex;
            align-items: center;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #667eea;
        }
        
        .form-control {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 10px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        /* Select2 styling */
        .select2-container--default .select2-selection--single {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            height: 42px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
            padding-left: 15px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .info-estudiante-card {
                padding: 15px;
            }
            
            .notas-card {
                padding: 15px;
            }
            
            .notas-card table thead th,
            .notas-card table tbody td {
                padding: 8px 4px;
                font-size: 12px;
            }
        }
    </style>

<div class="col-sm-12">

    <?php
    $id = "";
    if (!empty($_GET["id"])) {
        $id = base64_decode($_GET["id"]);
    }
    

    $e = Estudiantes::obtenerDatosEstudiante($id);

    $cambiar = "";
    if (!empty($_GET["cambiar"])) {
        $cambiar = $_GET["cambiar"];
    }

    ?>

    <form action="estudiantes-cambiar-grupo-estudiante.php" method="post" class="form-horizontal" enctype="multipart/form-data">
        <input type="hidden" value="<?= $e['mat_id']; ?>" name="estudiante">

        <!-- Card con información del estudiante -->
        <div class="info-estudiante-card">
            <h5><i class="fa fa-user-graduate"></i> Información del Estudiante</h5>
            <div class="detalle">
                <i class="fa fa-id-badge"></i>
                <span><strong>ID:</strong> <?= $e['mat_id']; ?> | <strong>Nombre:</strong> <?= Estudiantes::NombreCompletoDelEstudiante($e); ?></span>
            </div>
            <?php $gradoActual = Grados::obtenerGrado($e["mat_grado"]); ?>
            <div class="detalle">
                <i class="fa fa-graduation-cap"></i>
                <span><strong>Curso Actual:</strong> <?= $gradoActual["gra_id"]; ?> - <?= $gradoActual["gra_nombre"]; ?></span>
            </div>
        </div>

        <!-- Selección de grupo -->
        <div class="form-group row">
            <label class="col-sm-3 control-label"><i class="fa fa-users"></i> Nuevo Grupo</label>
            <div class="col-sm-9">
                <select class="form-control select2" id="estudianteGrupo" name="grupoNuevo" onchange="traerCargaCursoGrupo('<?= $gradoActual['gra_id'] ?>',this.value)" required>
                    <?php
                    $opcionesConsulta = CargaAcademica::listarGruposCursos([$e["mat_grado"]]);
                    foreach ($opcionesConsulta as $gru) {
                        if ($gru["car_grupo"] == $e['mat_grupo'])
                            echo '<option value="' . $gru["car_grupo"] . '" selected style="color:blue; font-weight:bold;">✓ Actual: ' . $gru["gru_nombre"] . '</option>';
                        else
                            echo '<option value="' . $gru["car_grupo"] . '">' . $gru["gru_nombre"] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Toggle para transferir notas -->
        <?php if (empty($cambiar)) {?>
        <div class="toggle-container">
            <div class="d-flex align-items-center justify-content-between">
                <label class="mb-0"><i class="fa fa-clipboard-list"></i> ¿Desea transferir las notas?</label>
                <label class="switchToggle"> 
                    <input type="checkbox" id="pasarNotas" onchange="mostrarNotas()" name="pasarNotas" checked>
                    <span class="slider green round">SI &nbsp;&nbsp; NO</span>
                </label>
            </div>
        </div>
        <?php } else { ?> 
            <input type='hidden' name='pasarNotas' value='si'>
        <?php } ?>
        <!-- Tabla de notas -->
        <div id="rowNotas">
            <div class="notas-card">
                <h6><i class="fa fa-clipboard-list"></i> Notas por Período y Relación de Materias</h6>
                <table>

                    <thead>

                        <tr>
                            <th width="5%" rowspan="2">#</th>
                            <th width="30%" rowspan="2">Materia</th>
                            <th colspan="<?= $config["conf_periodos_maximos"] ?>">Notas Periodos</th>
                            <th width="30%" rowspan="2">Materias Relacionada</th>
                        </tr>
                        <tr>
                            <?php for ($i = 1; $i <= $config["conf_periodos_maximos"]; $i++) { ?>
                                <th width="5%">P<?= $i ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $filtroLimite = '';


                        $consulta = CargaAcademica::consultarEstudianteMateriasNotasPeridos($e['mat_grado'], $e['mat_id'], $e['mat_grupo']);
                        $consultaR = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $e['mat_grado'], $e['mat_grupo']);
                        $cargasArray = array();
                        $cargasDisponibles = [];
                        if (!empty($consulta)) {
                            while ($fila = $consultaR->fetch_assoc()) {
                                $cargasArray[]       = $fila;
                                $cargasDisponibles[] = $fila;
                            }
                            $consultaR->free();
                        }
                        // agrupamos los datos por periodos
                        $materiasPeriodos = [];
                        $carga_id        = "";                   


                        foreach ($consulta  as $registro) {
                            $periodo = 1;
                            if ($carga_id != $registro["car_id"]) {
                                $materiasPeriodos[$registro["car_id"]] = [
                                    "car_id"             => $registro["car_id"],
                                    "car_ih"             => $registro["car_ih"],
                                    "car_materia"        => $registro["car_materia"],
                                    "car_docente"        => $registro["car_docente"],
                                    "car_director_grupo" => $registro["car_director_grupo"],
                                    "mat_nombre"         => $registro["mat_nombre"],
                                    "mat_area"           => $registro["mat_area"],
                                    "mat_valor"          => $registro["mat_valor"],
                                    "ar_nombre"          => $registro["ar_nombre"],
                                    "ar_posicion"        => $registro["ar_posicion"],
                                    "bol_estudiante"     => $registro["bol_estudiante"],
                                    "bol_periodo"        => $registro["bol_periodo"],
                                    "bol_nota"           => $registro["bol_nota"],
                                    "bol_id"             => $registro["bol_id"],
                                    "notaArea"           => $registro["notaArea"],
                                    "periodos"           => []
                                ];
                                $carga_id = $registro["car_id"];
                            }
                            $materiasPeriodos[$registro["car_id"]]["periodos"][$registro["bol_periodo"]] = [
                                "bol_periodo"      => $registro["bol_periodo"],
                                "bol_nota"         => $registro["bol_nota"]
                            ];
                            foreach ($cargasArray as $carga => $cargaGrupo) {
                                if ($cargaGrupo['car_materia'] === $registro['car_materia']) {
                                    unset($cargasDisponibles[$carga]); 
                                }
                            }
                        }

                        foreach ($materiasPeriodos as $resultado) { ?>
                            <tr border="1">
                                <td><?= $resultado['ar_posicion'] ?></td>
                                <td>
                                <?= $resultado['mat_nombre'] ?>
                                <input type='hidden' name='selectCargasOrigen[]' value='<?=$resultado['car_id']?>'>
                                </td>
                                <?php for ($i = 1; $i <= $config["conf_periodos_maximos"]; $i++) { ?>
                                    <th width="5%"><?= !empty($resultado['periodos'][$i]['bol_nota']) ? $resultado['periodos'][$i]['bol_nota'] : '-' ?></th>
                                <?php } ?>
                                <td id="<?= $resultado['car_id'] ?>_relacion">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            
                                            <select class="form-control  select2 dynamic-select"" required name="selectCargasDestino[]" id="carga_<?= $resultado['car_id']; ?>_<?= $resultado['car_materia'] ?>">
                                                <option value="">Seleccione una opción</option>
                                                <?php
                                                foreach ($cargasArray as $carga => $cargaGrupo) {
                                                    $selected = '';
                                                    if ($cargaGrupo['car_materia'] === $resultado['car_materia']) {
                                                        $selected='' ;
                                                        unset($cargasDisponibles[$carga]);                                                   
                                                    ?>
                                                    <option value="<?= $cargaGrupo['car_id'].'|'.$cargaGrupo['car_materia'].'|'.$cargaGrupo['mat_nombre'].'|'.$cargaGrupo['uss_nombre']; ?>" <?= 'selected' ?>><?=  strtoupper($cargaGrupo['mat_nombre']). " (" .$cargaGrupo['uss_nombre'].")"; ?></option>
                                                <?php } 
                                                 }
                                                 foreach ($cargasDisponibles as $carga => $cargaGrupo) {?>
                                                 <option value="<?= $cargaGrupo['car_id'].'|'.$cargaGrupo['car_materia'].'|'.$cargaGrupo['mat_nombre'].'|'.$cargaGrupo['uss_nombre']; ?>" ><?= strtoupper($cargaGrupo['mat_nombre']). " (" .$cargaGrupo['uss_nombre'].")"; ?></option>
                                                 <?php } ?>
                                           
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <select class="form-control select2" id="listaCargasGrado" hidden>
                <?php foreach ($cargasArray as $cargaGrupo) {?>
                    <option value="<?= $cargaGrupo['car_id']; ?>"><?= $cargaGrupo['car_id']."$".$cargaGrupo['car_materia'] . "$" . strtoupper($cargaGrupo['mat_nombre']). "$" .$cargaGrupo['uss_nombre']; ?></option>
                <?php } ?>
                </select>
            </div>
        </div>
        <?php $botones = new botonesGuardar(null, Modulos::validarPermisoEdicion()); ?> 
    </form>
</div>
<!-- end js include path -->
<script>
     cargas             = [];
     cargasSelecionadas = [];
     cargasDisponibles  = [];

     valorAnterior = "";
     listarCargas();
     cargarSelecionadas();


    $('.dynamic-select').on('focus', function() {
            valorAnterior = $(this).val(); // Guardar el valor anterior
    });

    $('.dynamic-select').on('change', function() {
        let selectId       = $(this).attr('id');
        let valorNuevo     = $(this).val();
        var clavesAnterior = valorAnterior.split("|");
        itemDisponible     = { car_id:clavesAnterior[0],car_materia: clavesAnterior[1],mat_nombre:clavesAnterior[2],uss_nombre:clavesAnterior[3] };
        cargasSelecionadas = cargasSelecionadas.filter(item => item.car_id !== clavesAnterior[0]);

        if( valorAnterior.trim() != '' ) {
            cargasDisponibles.push(itemDisponible);
        }; 

        if( valorNuevo.trim() != '' ) {  
            var clavesNueva = valorNuevo.split("|");
            itemSelecionado={car_id:clavesNueva[0],car_materia: clavesNueva[1],mat_nombre:clavesNueva[2],uss_nombre:clavesNueva[3]};
            cargasSelecionadas.push(itemSelecionado);
        };
        renderizarCargas();
    });

    async function traerCargaCursoGrupo(cruso, grupo) {
        $.toast({
                heading: 'Consultando Cargas',
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success',
                hideAfter: 3500, 
                stack: 6
            })
        var data = {
            "curso": cruso,
            "grupo": grupo
        };
        limpiarMateriasRelacionadas();

        resultado = await metodoFetchAsync('../compartido/ajax_carga_grupo.php', data, 'json', false);
        resultData = resultado["data"];
        const cargasElements = document.getElementsByName("selectCargasDestino[]");
        cargasResult         = resultData["result"];
        cargasDisponibles    = resultData["result"];

        if (cargasElements.length > 0) {
            if(resultData["result"].length > 0){
                $.toast({
                heading: 'Cargas consultadas',
                text: 'Se cargaron las cargas del grupo: '+grupo,
                position: 'bottom-right',
                showHideTransition: 'slide',
                icon: 'success',
                hideAfter: 3500, 
                stack: 6
            })
            }else{
                Swal.fire({
                position: "top-end",
                title: 'No hay Cargas',
                text: 'No se encontraro cargas para este grupo',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonText: 'Si!',
                cancelButtonText: 'No!',
                timer: 1500
            });
            }
        listarCargas(cargasResult);
        //llenamos las cargas relacionadas
        const cargasElements = document.getElementsByName("selectCargasDestino[]");
        cargasSelecionadas   = [];
        cargasElements.forEach((element) => {
            var selectCargas = $('#' + element.id);
            var clavesCarga = element.id.split("_");
            var selecione = new Option('Relacione una carga', '', true, true);
            selectCargas.append(selecione);
            for (let i = 0; i < cargasResult.length; i++) {
                    var encontrar = cargasResult[i].car_materia === clavesCarga[2];
                    if(encontrar){ 
                        var id_value          = cargasResult[i].car_id+'|'+cargasResult[i].car_materia+'|'+cargasResult[i].mat_nombre+'|'+cargasResult[i].uss_nombre;
                        var nuevaOpcion = new Option(cargasResult[i].mat_nombre +' ('+ cargasResult[i].uss_nombre +')', id_value, true, true);
                        selectCargas.append(nuevaOpcion);
                        if(Array.isArray(cargasSelecionadas) && cargasSelecionadas.length === 0){
                            cargasSelecionadas.push(cargas[i]);
                        }else{
                            if (!cargasSelecionadas.some(item => item.car_materia === clavesCarga[2])) {
                                cargasSelecionadas.push(cargas[i]);                                
                            } 
                        }
                        break;
                    }                   
                };
         });
         renderizarCargas();
        }
    }

    function mostrarNotas(){
        isChecked = $('#pasarNotas').prop('checked');
        var pasarNotas = isChecked ? 1 : 0;
        // var isChecked = $(this).prop('checked');
        console.log(pasarNotas);
        if(isChecked){  
            document.getElementById('rowNotas').style.display = "flex";
        }else{
            document.getElementById('rowNotas').style.display = "none";
        }
    }

    function listarCargas(cargasResult){
        var listaCargasGrado = document.getElementById('listaCargasGrado');
        cargas               = [];

        if (cargasResult != undefined ){
            cargasResult.forEach(function(carga) {
                    var nuevaOpcion = new Option(carga.car_id+'$'+carga.car_materia + '$' + carga.mat_nombre +'$'+ carga.uss_nombre, carga.car_id, false, false);
                    listaCargasGrado.append(nuevaOpcion);
            });       
        }
        let opciones = listaCargasGrado.options;
        for (let i = 0; i < opciones.length; i++) {
                var claves = opciones[i].text.split("$");
                item={car_id: claves[0],car_materia: claves[1],mat_nombre:claves[2],uss_nombre:claves[3]}
                cargas.push(item);
        };
        console.log(cargas);
    }

    function cargarSelecionadas(){
        const cargasElements = document.getElementsByName("selectCargasDestino[]");
        cargasElements.forEach((element) => {
            var valorselecionado = element.value;
            var clavesCarga = valorselecionado.split("|");
            item={car_id: clavesCarga[0],car_materia: clavesCarga[1],mat_nombre:clavesCarga[2],uss_nombre:clavesCarga[3]};
            cargasSelecionadas.push(item);
        });
    }

    function limpiarMateriasRelacionadas() {
        cargasDisponibles  = [];
        cargasSelecionadas = [];
        const cargasElements = document.getElementsByName("selectCargasDestino[]");
        cargasElements.forEach((element) => {
            var selectCargas = $('#' + element.id);
            selectCargas.empty();   
         });
            var listaCargasGrado = $('#listaCargasGrado');
            listaCargasGrado.empty();   
    }

    function renderizarCargas() {
        //se agregan las disponibles
         const cargasElements = document.getElementsByName("selectCargasDestino[]");
         cargasElements.forEach((element) => {
            var selectCargas = $('#' + element.id);
            var valorselecionado = element.value; 
            let opciones = element.options;
            // eliminamos las cargas que esten selecionadas
            cargasSelecionadas.forEach(function(carga) {
                cargasDisponibles = cargasDisponibles.filter(item => item.car_id !== carga.car_id);
                if( opciones.length > 0) {
                    encontro=false;
                    for (let i = 1; i < opciones.length; i++) {
                        var clavesCarga = opciones[i].value.split("|");
                        if( carga.car_id == clavesCarga[0] && opciones[i].value != valorselecionado){
                            encontro = true;
                            selectCargas.find('option[value="' + opciones[i].value + '"]').remove();
                        };
                    };
                };
            });
             // agregamos las cargas disponibles
            cargasDisponibles.forEach(function(carga) {
                var id_value = carga.car_id+'|'+carga.car_materia+'|'+carga.mat_nombre+'|'+carga.uss_nombre;
                if( opciones.length > 0) {
                    encontro=false;
                    for (let i = 1; i < opciones.length; i++) {
                        var clavesCarga = opciones[i].value.split("|");
                        if( carga.car_id == clavesCarga[0]  ){
                            encontro = true;
                            return;
                        };
                    };
                    var nuevaOpcion = new Option(carga.mat_nombre +' ('+ carga.uss_nombre +')', id_value, false, false);
                    selectCargas.append(nuevaOpcion);
                };
            });
        });
}
</script>
</script>