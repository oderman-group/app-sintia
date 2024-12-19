<?php
require_once ROOT_PATH.'/main-app/class/App/Administrativo/Usuario/Usuario.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once 'Vista_datos_boletin.php';
require_once 'Vista_datos_boletin_indicadores.php';

class Academico_boletin extends BDT_Tablas implements BDT_JoinImplements{

    public static $schema = BD_ACADEMICA;
    public static $tableName = 'academico_boletin';
    public static $tableAs   = 'bol';

    use BDT_Join;

    public static function datosBoletin(
        string  $grado,
        string  $grupo,
        array   $periodos,
        string  $year,
        string  $idEstudiante="",
        bool    $traerIndicadores=false,
        array   $andAdicional =[]){

       global  $config;
       $listaClases=[];
        $year = empty($year) ?  $_SESSION["bd"] : $year;
        $in_periodos2 = implode(', ', $periodos);
        $clasePrincipal=Vista_datos_boletin::class;
        if($traerIndicadores){
            $clasePrincipal=Vista_datos_boletin_indicadores::class;
        }
        $odenNombres    = '';
        switch ($config['conf_orden_nombre_estudiantes']) {
            case '1':
                $odenNombres = "mat_nombres,mat_nombre2,mat_primer_apellido,mat_segundo_apellido,";
                break;
            case '2':
                $odenNombres = "mat_primer_apellido,mat_segundo_apellido,mat_nombres,mat_nombre2,";
                break;
         }

         Administrativo_Usuario_Usuario::foreignKey(  self::LEFT,[                      
            "institucion"       => $clasePrincipal::$tableAs.'.institucion',
            "year"              => $clasePrincipal::$tableAs.'.year',
            "uss_id"            => $clasePrincipal::$tableAs.'.car_docente'
        ]);  
     

        $campos     = $clasePrincipal::$tableAs.".*,".Administrativo_Usuario_Usuario::$tableAs.".*";
        
   
        $predicados =
        [
            $clasePrincipal::$tableAs.".mat_grado"             => $grado,
            $clasePrincipal::$tableAs.".mat_grupo"             => $grupo,
            $clasePrincipal::$tableAs.".institucion"           => $_SESSION["idInstitucion"],
            $clasePrincipal::$tableAs.".year"                  => $year,
            $clasePrincipal::$tableAs.".mat_eliminado"         => 0,
            $clasePrincipal::$tableAs.".bol_periodo IN"        => "(".$in_periodos2.")",
            "AND" => $clasePrincipal::$tableAs.'.mat_estado_matricula = '.MATRICULADO .' OR '.$clasePrincipal::$tableAs.'.mat_estado_matricula='.ASISTENTE
        ];
        if(!empty($andAdicional)){

            $predicados = array_merge($predicados, $andAdicional);
        }
        if(!empty($idEstudiante)){
            $predicados[$clasePrincipal::$tableAs.'.mat_id'] = "'".$idEstudiante."'";
        }
        $order ="$odenNombres mat_id,ar_posicion,car_id,bol_periodo";
        
        $listaClases=[Administrativo_Usuario_Usuario::class]; 

       $result = parent::SelectJoin(
        $predicados,
        $campos,
        $clasePrincipal,
        $listaClases,
        '',
        $order); 
     
        return self::agruparDatos($result,$periodos);

    }

    public static function agruparDatos(array $datos,array $periodos){
        global  $config;
        
        $conteoEstudiante             = 0;
        $mat_id                       = "";
        $mat_ar                       = "";
        $mat_ar_car                   = "";
        $mat_ar_car_periodo           = "";
        $mat_ar_car_periodo_indicador = "";
        $estudiantes                  = [];

        foreach ($datos  as $registro) {

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
                $conteoEstudiante++;
                
                $nombre = Estudiantes::NombreCompletoDelEstudiante($registro);
                Utilidades::valordefecto($registro["mat_genero"],'126');
                $estudiantes[$registro["mat_id"]] = [
                    "mat_id"                            => $registro["mat_id"],
                    "nombre"                            => $nombre,
                    "mat_documento"                     => $registro["mat_documento"],
                    "nro"                               => $conteoEstudiante,
                    "mat_matricula"                     => $registro["mat_matricula"],
                    "gra_id"                            => $registro["mat_grado"],
                    "gra_nombre"                        => $registro["gra_nombre"],
                    "genero"                        => $registro["mat_genero"],
                    "gru_id"                            => $registro["mat_grupo"],
                    "gru_nombre"                        => $registro["gru_nombre"],
                    "mat_estado_matricula"              => $registro["mat_estado_matricula"],
                    "mat_numero_matricula"              => $registro["mat_numero_matricula"],
                    "mat_folio"                         => $registro["mat_folio"],
                    "periodo_selecionado"               => count($periodos),
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
                $contarNotasCarga   = 1;
                $contarFallasCarga  = 0;
                $contarIndicadores  = 0;  

                $contarCargas++;

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
                $porcentajePeriodo      = $registro['periodo_valor'];
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
                    "bol_nota_anterior"         => $registro['bol_nota_anterior'],            
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
            
            if (!empty($registro["dn_id"]) && !empty($registro["dn_observacion"])) {
                $estudiantes[$registro["mat_id"]]["observaciones_generales"][$registro["dn_periodo"]]= [
                    "id"          => $registro["dn_id"],
                    "estudiante"  => $registro["dn_cod_estudiante"],
                    "observacion" => $registro["dn_observacion"],
                    "periodo"     => $registro["dn_periodo"]
                ];
            }
            
            if (!empty($registro["niv_id"]) && !empty($registro["niv_definitiva"])) {
                $estudiantes[$registro["mat_id"]]["nivelaciones"][$registro["niv_id_asg"]]= [
                    "id"          => $registro["niv_id"],
                    "estudiante"  => $registro["niv_cod_estudiante"],
                    "definitiva"  => $registro["niv_definitiva"],
                    "fecha"       => $registro["niv_fecha_nivelacion"]
                ];
            }
        }

        foreach ($estudiantes as $estudiante) {
            $cantidad_materias                  = 0;
            $suma_notas_materias_periodo        = [];
            $suma_notas_areas_periodo           = [];
            $suma_promedios_generales_materias  = 0;
            $suma_promedios_generales_areas     = 0;
            $fallas_periodo = [];
            foreach ($estudiante["areas"] as $area) {
                $nota_area      = [];        
                $suma_nota_area = 0;
                foreach ($periodos as $per) {
                    Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$per],[
                        "periodo"               =>  $per,
                        "porcentaje_periodo"    =>  100/count($periodos), 
                        "ausencia_area"         =>  0
                    ]);
                }
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
                        $suma_notas_materias_periodo[$periodo["bol_periodo"]]                                                       += $periodo["bol_nota"];
                        $nota_area[$periodo["bol_periodo"]]                                                                         += $periodo["bol_nota"] * ($porcentaje_materia/100);
                        $suma_notas_areas_periodo[$periodo["bol_periodo"]]                                                          += $periodo["bol_nota"] * ($porcentaje_materia/100);
                        $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["suma_nota_carga"] += $periodo["bol_nota"] ;
                        $nota_carga_acumulada                                 += $periodo["bol_nota"] * ($periodo["porcentaje_periodo"]/100);         
                    }
                    $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["nota_carga_acumulada"]=$nota_carga_acumulada;
                    foreach ($periodos as $per) {
                        Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["periodos"][$per],[
                            "bol_periodo"        =>  $per,
                            "bol_nota"           => 0,
                            "porcentaje_periodo" => 100/count($periodos), 
                        ]);
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

            foreach ($periodos as $per) {
                $promedio["porcentaje_periodo"] = 100/count($periodos);
                $promedio["periodo"] =$per;
                Utilidades::valordefecto($suma_notas_materias_periodo[$promedio["periodo"]],0);
                Utilidades::valordefecto($suma_notas_areas_periodo[$promedio["periodo"]],0);
                Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["promedios_generales"][$per],[
                    "periodo"                 =>  $per,
                    "porcentaje_periodo"      =>  ($promedio["porcentaje_periodo"]), 
                    "ausencia_area"           =>  0,
                    "cantidad_materias"       => $cantidad_materias,        
                    "suma_notas_materias"     => $suma_notas_materias_periodo[$promedio["periodo"]],
                    "nota_materia_promedio"   => $suma_notas_materias_periodo[$promedio["periodo"]]/$cantidad_materias,
                    "nota_materia_porcentaje" => ($suma_notas_materias_periodo[$promedio["periodo"]]/$cantidad_materias)*($promedio["porcentaje_periodo"]/100) ,                                                                       
                    "cantidad_areas"          => count($estudiante["areas"]),
                    "suma_notas_areas"        => $suma_notas_areas_periodo[$promedio["periodo"]],
                    "nota_area_promedio"      => $suma_notas_areas_periodo[$promedio["periodo"]]/count($estudiante["areas"]),        
                    "nota_area_porcentaje"    => ($suma_notas_areas_periodo[$promedio["periodo"]]/count($estudiante["areas"]))*($promedio["porcentaje_periodo"]/100)  
                ]);
            }
        }
       
    
    return $estudiantes;
    }

}