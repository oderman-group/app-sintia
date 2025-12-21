<?php

// se reparte la informacion en arrays
Utilidades::valordefecto($periodosArray,[]);
Utilidades::valordefecto($year,$_SESSION["bd"]);
Utilidades::valordefecto($traerIndicadores,false);
$conteoEstudiante             = 0;
$contarAreas                  = 0;
$contarCargas                 = 0;
$contarIndicadores            = 0;
$areasPerdidas                = 0;
$materiasPerdidas             = 0;
$indicadoresPerdidos          = 0; 
$mat_id                       = "";
$mat_ar                       = "";
$mat_ar_car                   = "";
$mat_ar_car_periodo           = "";
$mat_ar_car_periodo_indicador = "";
$directorGrupo                = "";
$observacionesConvivencia     = [];
$nivelaciones                 = [];
$estudiantes                  = [];
$porcPeriodo = array(0.25, 0.25, 0.25, 0.25);
foreach ($listaDatos  as $registro) {

    Utilidades::valordefecto($registro["ind_id"]);
    Utilidades::valordefecto($registro["ind_nombre"]);
    Utilidades::valordefecto($registro["rind_nota"]);
    Utilidades::valordefecto($registro["indicador_porcentual"],0);
    Utilidades::valordefecto($registro["valor_indicador"],0);
    Utilidades::valordefecto($registro["valor_porcentaje_indicador"],0);



    // Datos del estudiante
    if ($mat_id != $registro["mat_id"]) {
        $contarAreas         = 0;
        $contarCargas        = 0;
        $contarIndicadores   = 0;
        $periodos            = 0;
        $areasPerdidas       = 0;
        $materiasPerdidas    = 0;
        $indicadoresPerdidos = 0;
        $conteoEstudiante++;
        
        $nombre = Estudiantes::NombreCompletoDelEstudiante($registro);
        Utilidades::valordefecto($registro["mat_genero"],'126');
        $estudiantes[$registro["mat_id"]] = [
            "mat_id"                            => $registro["mat_id"],
            "nombre"                            => $nombre,
            "mat_documento"                     => $registro["mat_documento"],
            "nro"                               => $conteoEstudiante,
            "mat_matricula"                     => $registro["mat_matricula"],
            "mat_tipo_matricula"                => $registro["mat_tipo_matricula"],
            "gra_id"                            => $registro["mat_grado"],
            "gra_nombre"                        => $registro["gra_nombre"],
            "gra_nivel"                         => !empty($registro["gra_nivel"]) ? $registro["gra_nivel"] : null,
            "genero"                        => $registro["mat_genero"],
            "gru_id"                            => $registro["mat_grupo"],
            "gru_nombre"                        => $registro["gru_nombre"],
            "mat_estado_matricula"              => $registro["mat_estado_matricula"],
            "mat_numero_matricula"              => $registro["mat_numero_matricula"],
            "mat_folio"                         => $registro["mat_folio"],
            "periodo_selecionado"               => $periodos,
            "observaciones_generales"           => [],
            "suma_promedios_generales_materias" => 0,
            "suma_promedios_generales_areas"    => 0,
            "promedios_generales"               => [],
            "areas"                             => []

        ];
        $mat_id = $registro["mat_id"];
    }
    // Datos de las areas
    if ($mat_ar != $mat_id . '-' . $registro["ar_id"]) {
        $notaAre           = 0;
        $contarNotasAreas  = 1;
        $contarFallasArea  = 0;
        $contarAreas++;

        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']] = [
            "ar_id"                    => $registro['ar_id'],
            "nro"                      => $contarAreas,
            "ar_nombre"                => $registro['ar_nombre'],
            "suma_nota_area"           => 0,
            "nota_area_acumulada"      => 0,
            "fallas"                   => 0,
            "maneja_porcetaje"         => false,           
            "cargas"                   => []
        ];
        $mat_ar = $mat_id . '-' . $registro["ar_id"];
    }
    // Datos de las cargas
    if ($mat_ar_car !=  $mat_ar . '-' . $registro["car_id"]) {        
        $notaCargaAcumulada = 0; // lleva la nota acumulada de las cargas por los periodos
        $cargaAcumulada     = 0;         
        $contarNotasCarga   = 1;
        $contarFallasCarga  = 0;
        $contarIndicadores  = 0;
        $contarCargas++;
        if ($registro["car_director_grupo"] == 1) {
            $directorGrupo = $registro;
        }
        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']] = [
            "car_id"                    => $registro['car_id'],
            "car_ih"                    => $registro['car_ih'],
            "ar_id"                     => $registro['ar_id'],
            "nro"                       => $contarCargas,
            "id_materia"                => $registro['id_materia'],
            "mat_nombre"                => $registro['mat_nombre'],
            "mat_valor"                 => $registro['mat_valor'],
            "docente"                   => $registro,
            "director_grupo"            => $registro["car_director_grupo"],
            "suma_nota_carga"           => 0,
            "nota_carga_acumulada"      => 0,
            "carga_acumulada"           => 0,
            "fallas"                    => 0,
            "periodos"                  => []
        ];

        $mat_ar_car =   $mat_ar. '-' . $registro["car_id"];
    }
    // Datos de los periodos
    if ($mat_ar_car_periodo != $mat_ar_car . '-' . $registro["bol_periodo"]) {  
        $contarIndicadores      = 0;      
        $notaIndicadorAcumulado = 0; // lleva el conteo de las notas de los indicadores 
        $porcentaje             = $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["mat_valor"];
        $porcentajePeriodo      = !empty($registro['periodo_valor']) ? $registro['periodo_valor'] : (100 / $config['conf_periodos_maximos']);
        Utilidades::valordefecto($porcentaje,100);
        Utilidades::valordefecto($porcentajePeriodo,100 / $config['conf_periodos_maximos']);
        if ($porcentaje != 100) {
            $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["maneja_porcetaje"] = true;  
        }        

        $registro['aus_ausencias'] = empty($registro['aus_ausencias'])?0:$registro['aus_ausencias'];
        
        
        $contarFallasArea   +=  $registro['aus_ausencias'];


        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["fallas"] = $contarFallasArea;     

       
        $notaCargaAcumulada +=  $registro['bol_nota'];
        $contarFallasCarga  +=  $registro['aus_ausencias'];
       
        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["fallas"]               = $contarFallasCarga ;
        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["cantidad_notas"]       = $contarNotasCarga++;
        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["carga_acumulada"]      = !empty($registro['promedio_acumulado']) ? $registro['promedio_acumulado'] : 0;

        
        // valores generales 
        $estudiantes[$registro["mat_id"]]["promedios_generales"][$registro["bol_periodo"]]=[
            "periodo"               =>  $registro["bol_periodo"],
            "porcentaje_periodo"    =>  $porcentajePeriodo,
        ];
        

       // valores area para el area       
        Utilidades::valordefecto($estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["periodos"][$registro["bol_periodo"]]["ausencia_area"],0);
        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["periodos"][$registro["bol_periodo"]] = [
            "periodo"               =>  $registro["bol_periodo"],
            "porcentaje_periodo"    =>  $porcentajePeriodo, 
            "ausencia_area"         =>  $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["periodos"][$registro["bol_periodo"]]["ausencia_area"]+$registro['aus_ausencias']
        ];
       
        // nota para la carga        
        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["bol_periodo"]] = [
            "bol_periodo"               => $registro["bol_periodo"],
            "bol_nota"                  => $registro['bol_nota'],
            "porcentaje_periodo"        => $porcentajePeriodo,
            "bol_tipo"                  => $registro['bol_tipo'], 
            "bol_nota_anterior"         => !empty($registro['bol_nota_anterior']) ? $registro['bol_nota_anterior'] : null,            
            "bol_observaciones_boletin" => $registro['bol_observaciones_boletin'],
            "aus_ausencias"             => $registro['aus_ausencias'],
            "nota_indicadores"          => 0,
            "indicadores"               => []
        ];
        $estudiantes[$registro["mat_id"]]["periodo_selecionado"]=count($estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"]);

        $mat_ar_car_periodo = $mat_ar_car . '-' . $registro["bol_periodo"];
    }
    // Datos de los Indicadores por periodo
    if (!empty($registro["ind_id"]) && $mat_ar_car_periodo_indicador != $mat_ar_car_periodo.'-'.$registro["ind_id"]) {
        $indicadorRecuperado=false;
        $contarIndicadores++;
        $notaIndicador = empty($registro['valor_indicador']) ? 0 : $registro['valor_indicador'];
        $notaIndicador_recuperacion = empty($registro['rind_nota']) ? 0 : $registro['rind_nota'];

        if ($notaIndicador_recuperacion > $notaIndicador) {
            $notaIndicador = $notaIndicador_recuperacion;
            $indicadorRecuperado=true;
        }
        $notaIndicadorAcumulado += $notaIndicador * ($registro['valor_porcentaje_indicador'] / 100);
        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["bol_periodo"]]['nota_indicadores'] = $notaIndicadorAcumulado;
        $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["bol_periodo"]]['indicadores'][$registro["ind_id"]] = [
            "ind_id"                      => $registro["ind_id"],
            "nro"                         => $contarIndicadores,
            "ind_nombre"                  => $registro['ind_nombre'],
            "valor_indicador"             => $registro['valor_indicador'],
            "valor_indicador_recuperado"  => $registro['rind_nota'],            
            "valor_porcentaje_indicador"  => $registro['valor_porcentaje_indicador'],
            "nota_final"                  => $notaIndicador,
            "recuperado"                  => $indicadorRecuperado,
            "indicador_porcentual"        => $registro['indicador_porcentual']
        ];
        $mat_ar_car_periodo_indicador = $mat_ar_car_periodo.'-'.$registro["ind_id"];
    }

        // Observacion por estudainte
        if (!empty($registro["dn_id"]) && !empty($registro["dn_observacion"])) {
            $observacionesConvivencia[$registro["mat_id"]][$registro["dn_periodo"]] = [
                "id"          => $registro["dn_id"],
                "estudiante"  => $registro["dn_cod_estudiante"],
                "observacion" => $registro["dn_observacion"],
                "periodo"     => $registro["dn_periodo"]
            ];
            $estudiantes[$registro["mat_id"]]["observaciones_generales"][$registro["dn_periodo"]]= [
                "id"          => $registro["dn_id"],
                "estudiante"  => $registro["dn_cod_estudiante"],
                "observacion" => $registro["dn_observacion"],
                "periodo"     => $registro["dn_periodo"]
            ];
        }
    
        if (!empty($registro["niv_id"]) && !empty($registro["niv_definitiva"])) {
            $nivelaciones[$registro["mat_id"]][$registro["niv_id_asg"]] = [
                "id"          => $registro["niv_id"],
                "estudiante"  => $registro["niv_cod_estudiante"],
                "definitiva"  => $registro["niv_definitiva"],
                "fecha"       => $registro["niv_fecha_nivelacion"]
            ];
            $estudiantes[$registro["mat_id"]]["nivelaciones"][$registro["niv_id_asg"]]= [
                "id"          => $registro["niv_id"],
                "estudiante"  => $registro["niv_cod_estudiante"],
                "definitiva"  => $registro["niv_definitiva"],
                "fecha"       => $registro["niv_fecha_nivelacion"]
            ];
        }
}
$estudiantesMediatecnica = [];
foreach ($estudiantes as $estudiante) {
    if($estudiante["mat_tipo_matricula"] == GRADO_INDIVIDUAL){
        $estudiantesMediatecnica[]=$estudiante["mat_id"] ;       
    }
    $cantidad_materias                  = 0;
    $suma_notas_materias_periodo        = [];
    $suma_notas_areas_periodo           = [];
    $suma_promedios_generales_materias  = 0;
    $suma_promedios_generales_areas     = 0;
    $fallas_periodo = [];
    foreach ($estudiante["areas"] as $area) {
        $nota_area      = [];        
        $suma_nota_area = 0;
        $suma_porcentajes_area = []; // Para calcular promedio ponderado correcto
        foreach ($area["cargas"] as $carga) {
            $nota_carga_acumulada = 0;
            $cantidad_materias++;
            Utilidades::valordefecto( $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["mat_valor"],100/count($area["cargas"] ));
            Utilidades::valordefecto( $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["suma_nota_carga"],0);
            Utilidades::valordefecto( $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["suma_nota_area"],0);
            $porcentaje_materia = $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["mat_valor"];
            
            foreach ($carga["periodos"] as $periodo) {
                Utilidades::valordefecto( $suma_notas_materias_periodo[$periodo["bol_periodo"]],0);
                Utilidades::valordefecto( $suma_notas_areas_periodo[$periodo["bol_periodo"]],0);
                Utilidades::valordefecto( $nota_area[$periodo["bol_periodo"]],0);
                Utilidades::valordefecto( $suma_porcentajes_area[$periodo["bol_periodo"]],0);
                $suma_notas_materias_periodo[$periodo["bol_periodo"]]                                                       += $periodo["bol_nota"];
                // Acumular nota ponderada (nota * porcentaje) y suma de porcentajes para calcular promedio ponderado después
                $nota_area[$periodo["bol_periodo"]]                                                                         += $periodo["bol_nota"] * ($porcentaje_materia);
                $suma_porcentajes_area[$periodo["bol_periodo"]]                                                              += $porcentaje_materia;
                $suma_notas_areas_periodo[$periodo["bol_periodo"]]                                                          += $periodo["bol_nota"] * ($porcentaje_materia/100);
                $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["suma_nota_carga"] += $periodo["bol_nota"] ;
                $nota_carga_acumulada                                 += $periodo["bol_nota"] * ($periodo["porcentaje_periodo"]/100);         
            }
            $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["nota_carga_acumulada"]=$nota_carga_acumulada;
            
        }
        // Calcular promedio ponderado del área por período
        foreach ($nota_area as $periodoKey => $sumaNotasPonderadas) {
            if (!empty($suma_porcentajes_area[$periodoKey]) && $suma_porcentajes_area[$periodoKey] > 0) {
                // Promedio ponderado = suma de (nota * porcentaje) / suma de porcentajes
                $nota_area[$periodoKey] = $sumaNotasPonderadas / $suma_porcentajes_area[$periodoKey];
            }
        }
        $nota_area_acumulada = 0;
        foreach ($area["periodos"] as $periodo) {
             Utilidades::valordefecto( $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$periodo["periodo"]]["nota_area"],0);
             Utilidades::valordefecto( $fallas_periodo[$periodo["periodo"]],0);
             $fallas_periodo[$periodo["periodo"]]                                                                       += $periodo["ausencia_area"];
             $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$periodo["periodo"]]["nota_area"] =  $nota_area[$periodo["periodo"]];
             $suma_nota_area                                                                                            += $nota_area[$periodo["periodo"]];
             $nota_area_acumulada                                                                                       += $nota_area[$periodo["periodo"]]*($periodo["porcentaje_periodo"]/100);
        }
        $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["suma_nota_area"]      += $suma_nota_area ;
        $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["nota_area_acumulada"] += $nota_area_acumulada ;
    }
    foreach ($estudiante["promedios_generales"] as $promedio) {
        $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["cantidad_materias"]        = $cantidad_materias;        
        $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["suma_notas_materias"]      = $suma_notas_materias_periodo[$promedio["periodo"]];
        $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["nota_materia_promedio"]    = $suma_notas_materias_periodo[$promedio["periodo"]]/$cantidad_materias;
        $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["nota_materia_porcentaje"]  = ($suma_notas_materias_periodo[$promedio["periodo"]]/$cantidad_materias)*($promedio["porcentaje_periodo"]/100) ;
        $suma_promedios_generales_materias                                                                           += $suma_notas_materias_periodo[$promedio["periodo"]]/$cantidad_materias;
        $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["suma_ausencias"]           = $fallas_periodo[$promedio["periodo"]]; 


        $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["cantidad_areas"]           = count($estudiante["areas"]);
        $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["suma_notas_areas"]         = $suma_notas_areas_periodo[$promedio["periodo"]];
        $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["nota_area_promedio"]       = $suma_notas_areas_periodo[$promedio["periodo"]]/count($estudiante["areas"]);        
        $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["nota_area_porcentaje"]     = ($suma_notas_areas_periodo[$promedio["periodo"]]/count($estudiante["areas"]))*($promedio["porcentaje_periodo"]/100);        
        $suma_promedios_generales_areas                                                                              += $suma_notas_areas_periodo[$promedio["periodo"]]/count($estudiante["areas"]);
    }
    $estudiantes[$estudiante["mat_id"]]["suma_promedios_generales_materias"]   = $suma_promedios_generales_materias;
    $estudiantes[$estudiante["mat_id"]]["suma_promedios_generales_areas"]      = $suma_promedios_generales_areas;
    
}
// listar daots de media tecnica
$individuales = [];
if(!empty($estudiantesMediatecnica)){
        // Asegurar que $periodosArray esté inicializado como array
        if(empty($periodosArray) || !is_array($periodosArray)){
            $periodosArray = [];
        }

        // Asegurar que $year esté inicializado
        if(empty($year)){
            $year = $_SESSION["bd"] ?? '';
        }

        // Asegurar que $traerIndicadores esté inicializado como bool
        if(!isset($traerIndicadores) || !is_bool($traerIndicadores)){
            $traerIndicadores = false;
        }

        $datos = Boletin::datosBoletinMediaTecnica($periodosArray, $year, $estudiantesMediatecnica, $traerIndicadores);
     if($datos && is_object($datos)){
         while ($row = $datos->fetch_assoc()) {
             $individuales[] = $row;
         }
     }
    
   
    $matcur_id_matricula                               ='';
    $matcur_id_matricula_curso                         ='';
    $matcur_id_matricula_curso_carga                   ='';
    $matcur_id_matricula_curso_carga_periodo           ='';
    $matcur_id_matricula_curso_carga_periodo_indicador ='';
    foreach ($individuales as $datoMt) { 
       
        if ($matcur_id_matricula != $datoMt["matcur_id_matricula"]) {
            $contCurso = 0;
            $estudiantes[$datoMt["matcur_id_matricula"]]["cursos_adicionales"]=[];
            $matcur_id_matricula=$datoMt["matcur_id_matricula"];
        }
        if($matcur_id_matricula_curso  !=  $matcur_id_matricula.'-'.$datoMt["matcur_id_curso"]){           
            $contCurso       ++ ;
            $contCursoCargas = 0;
            $estudiantes[$datoMt["matcur_id_matricula"]]["cursos_adicionales"][$datoMt["matcur_id_curso"]]=[
                "matcur_id_curso"            => $datoMt["matcur_id_curso"],
                "gra_nombre"                 => $datoMt["gra_nombre"],
                "nro"                        =>  $contCurso ,
                "cargas"                     =>[]
            ];
          $matcur_id_matricula_curso = $matcur_id_matricula.'-'.$datoMt["matcur_id_curso"];
        }
        if($matcur_id_matricula_curso_carga  !=  $matcur_id_matricula_curso.'-'.$datoMt["car_id"]){           
            $contCursoCargas  ++ ;
            $contCursoMateria = 0;
            $estudiantes[$datoMt["matcur_id_matricula"]]["cursos_adicionales"][$datoMt["matcur_id_curso"]]["cargas"][$datoMt["car_id"]]=[
                "car_id"                     => $datoMt["car_id"],
                "car_ih"                     => $datoMt["car_ih"],
                "nro"                        => $contCursoCargas ,                
                "mat_area"                   => $datoMt["mat_area"],
                "ar_nombre"                  => $datoMt["ar_nombre"],
                "car_materia"                => $datoMt["car_materia"],
                "mat_nombre"                 => $datoMt["mat_nombre"],               
                "periodos"                   => []
            ];
          $matcur_id_matricula_curso_carga = $matcur_id_matricula_curso.'-'.$datoMt["car_id"];
        }

        
        if($matcur_id_matricula_curso_carga_periodo  !=  $matcur_id_matricula_curso_carga.'-'.$datoMt["ipc_periodo"]){            
            $contCursoMateria  ++ ;
            $contarIndicadores = 0;
            $estudiantes[$datoMt["matcur_id_matricula"]]["cursos_adicionales"][$datoMt["matcur_id_curso"]]["cargas"][$datoMt["car_id"]]["periodos"][$datoMt["ipc_periodo"]]=[
            "ipc_periodo"               => $datoMt["ipc_periodo"],
            "nro"                       => $contCursoMateria ,
            "bol_nota"                  => $datoMt['bol_nota'],
            "bol_tipo"                  => $datoMt['bol_tipo'], 
            "bol_nota_anterior"         => $datoMt['bol_nota_anterior'], 
            "indicadores"      => [] 

            ];
            $matcur_id_matricula_curso_carga_periodo = $matcur_id_matricula_curso_carga.'-'.$datoMt["ipc_periodo"];
        }
        if($matcur_id_matricula_curso_carga_periodo_indicador  !=  $matcur_id_matricula_curso_carga_periodo.'-'.$datoMt["ipc_indicador"]){
            $contarIndicadores   ++;
            $indicadorRecuperado = false;
            Utilidades::valordefecto($datoMt['act_valor'],100);
            $valor_indicador = ($datoMt['cal_nota_equivalente_cien'] / $datoMt['act_valor'])*100;
            $notaIndicador_recuperacion = empty($datoMt['rind_nota']) ? 0 : $datoMt['rind_nota'];    
            if ($notaIndicador_recuperacion > $valor_indicador) {
                $valor_indicador = $notaIndicador_recuperacion;
                $indicadorRecuperado=true;
            }
            $estudiantes[$datoMt["matcur_id_matricula"]]["cursos_adicionales"][$datoMt["matcur_id_curso"]]["cargas"][$datoMt["car_id"]]["periodos"][$datoMt["ipc_periodo"]]["indicadores"][$datoMt["ipc_indicador"]]=[
               "ipc_indicador"                => $datoMt["ipc_indicador"],
               "nro"                          => $contarIndicadores ,
               "ind_nombre"                   => $datoMt['ind_nombre'],
               "indicador_porcentual"         => $datoMt['cal_nota_equivalente_cien'],
                "valor_indicador"             => ($datoMt['cal_nota_equivalente_cien'] / $datoMt['act_valor'])*100,
                "valor_indicador_recuperado"  => $datoMt['rind_nota'],            
                "nota_final"                  => $valor_indicador,
                "recuperado"                  => $indicadorRecuperado

            ];
            $matcur_id_matricula_curso_carga_periodo_indicador = $matcur_id_matricula_curso_carga_periodo.'-'.$datoMt["ipc_periodo"];
        }
        
        
    }


}
    