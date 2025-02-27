<?php
require_once ROOT_PATH . '/main-app/class/Tables/BDT_Join.php';
require_once ROOT_PATH . '/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH . '/main-app/compartido/sintia-funciones.php';
require_once ROOT_PATH.  '/main-app/class/UsuariosPadre.php';

class Vista_historial_calificaciones extends BDT_Tablas
{

    public static $schema = BD_ACADEMICA;
    public static $tableName = 'vista_historial_calificaciones2';
    public static $tableAs = 'vhc';


    public static function listarHistorialCalificaiones(
        string $grado,
        string $grupo,
        array $idEstudiantes = [],
        string $year = null,
    ) {
        $year = empty($year) ? $_SESSION["bd"] : $year;
        $campos = "*";
        $predicado =
            [
                "institucion" => $_SESSION["idInstitucion"],
                "year" => $year,
                "mat_grado" => $grado,
                "mat_grupo" => $grupo,
                "id_materia IN" =>"(58,45)"
            ];

        if (!empty($idEstudiantes)) {
            foreach ($idEstudiantes as &$estudiante) {
                $estudiante = parent::formatValor($estudiante);
            }
            ;
            $in_estudiantes = implode(', ', $idEstudiantes);
            $predicado['mat_id IN'] = '(' . $in_estudiantes . ')';
        }
        $sql = parent::Select($predicado, $campos);
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        return self::agruparDatos($result);
    }


    public static function agruparDatos(array $datos)
    {
        global $config;

        $conteoEstudiante = 0;
        $mat_id = "";
        

        $estudiantes = [];
        $periodos    = [];
        $cPeriodo    = $config['conf_periodos_maximos'];
        for ($i = 1; $i <= $cPeriodo; $i++) {
            $periodos[$i] = $i;
        }

        foreach ($datos as $registro) {
            
            Utilidades::valordefecto($registro["ind_id"]);
            Utilidades::valordefecto($registro["ind_nombre"]);
            Utilidades::valordefecto($registro["rind_nota"]);
            Utilidades::valordefecto($registro["indicador_porcentual"], 0);
            Utilidades::valordefecto($registro["valor_indicador"], 0);
            Utilidades::valordefecto($registro["valor_porcentaje_indicador"], 0);


            // Datos del estudiante
            if ($mat_id != $registro["mat_id"]) {
                $mat_ar  = "";
                $contarAreas = 0;              
                $contarCargas = 0;
                $nombre = Estudiantes::NombreCompletoDelEstudiante($registro);
                $fotoEstudiante = UsuariosFunciones::verificarFoto($registro['mat_foto']);
                $conteoEstudiante++;
                Utilidades::valordefecto($registro["mat_genero"], UsuariosPadre::GENERO_MASCULINO);
                
                $estudiantes[$registro["mat_id"]] = [
                    "mat_id"               => $registro["mat_id"],
                    "nombre"               => $nombre,
                    "mat_documento"        => $registro["mat_documento"],
                    "nro"                  => $conteoEstudiante,
                    "mat_matricula"        => $registro["mat_matricula"],
                    "gra_id"               => $registro["mat_grado"],
                    "gra_nombre"           => $registro["grado_actual"],
                    "genero"               => $registro["mat_genero"],
                    "gru_id"               => $registro["mat_grupo"],
                    "gru_nombre"           => $registro["grupo_actual"],
                    "mat_estado_matricula" => $registro["mat_estado_matricula"],
                    "mat_numero_matricula" => $registro["mat_numero_matricula"],
                    "mat_folio"            => $registro["mat_folio"],
                    "foto"                 => $fotoEstudiante,
                    "cursos"               => []
                ];

                $mat_id = $registro["mat_id"];
            }

            // Datos de las areas
            if ($mat_ar != $mat_id . '-' . $registro["ar_id"]) {
                $mat_ar_car = "";
                $contarAreas++;
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']] = [
                    "ar_id"               => $registro['ar_id'],
                    "nro"                 => $contarAreas,
                    "ar_nombre"           => $registro['ar_nombre'],
                    "suma_nota_area"      => 0,
                    "nota_area_acumulada" => 0,
                    "fallas"              => 0,
                    "maneja_porcetaje"    => false,
                    "cargas"              => []
                ];
                $mat_ar = $mat_id . '-' . $registro["ar_id"];
            }
            // Datos de las cargas
            if ($mat_ar_car != $mat_ar . '-' . $registro["car_id"]) {
                $mat_ar_car_periodo = ""; 
                $contarCargas++;
                Utilidades::valordefecto($registro['mat_valor'], 100);
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']] = [
                    "car_id"                    => $registro['car_id'],
                    "car_ih"                    => $registro['car_ih'],
                    "ar_id"                     => $registro['ar_id'],
                    "nro"                       => $contarCargas,
                    "id_materia"                => $registro['id_materia'],
                    "mat_nombre"                => $registro['mat_nombre'],
                    "mat_valor"                 => $registro['mat_valor'],
                    "suma_nota_carga_periodos"  => 0,
                    "nota_carga_acumulada"      => 0,
                    "periodos"                  => []
                ];

                $mat_ar_car = $mat_ar . '-' . $registro["car_id"];
            }
            // // Datos de los periodos
            if ($mat_ar_car_periodo != $mat_ar_car . '-' . $registro["act_periodo"]) {
                $mat_ar_car_periodo_indicador = "";
                $contarIndicadores = 0;
                $porcentaje_carga_realizado= 0;
                $suma_porcentaje_carga = 0 ;
                $porcentaje = $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["mat_valor"];
                $porcentajePeriodo = $registro['periodo_valor'];
                

            //     // valores de los periodos  para el area       
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["periodos"][$registro["act_periodo"]] = [
                    "periodo"            => $registro["act_periodo"],
                    "porcentaje_periodo" => $porcentajePeriodo
                ];

            //     // valores de los periodos  para la carga        
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["act_periodo"]] = [
                    "act_periodo"                => $registro["act_periodo"],
                    "bol_nota"                   => $registro['bol_nota'],
                    "porcentaje_periodo"         => $porcentajePeriodo,
                    "bol_tipo"                   => $registro['bol_tipo'],
                    "bol_nota_anterior"          => $registro['bol_nota_anterior'],
                    "bol_observaciones_boletin"  => $registro['bol_observaciones_boletin'],
                    "nota_indicadores"           => 0,                    
                    "porcentaje_carga_realizado" => $porcentaje_carga_realizado,
                    "suma_porcentaje_carga"      => $suma_porcentaje_carga,
                    "progreso_carga"             => 0,
                    "indicadores"                => []
                ];

                $mat_ar_car_periodo = $mat_ar_car . '-' . $registro["act_periodo"];
            }
            // // Datos de los Indicadores por periodo
            if (!empty($registro["ind_id"]) && $mat_ar_car_periodo_indicador != $mat_ar_car_periodo . '-' . $registro["ind_id"]) {
                $contarActividad = 0;
                $mat_ar_car_periodo_indicador_actividad = "";
                $indicadorRecuperado = false;
                $contarIndicadores++;
                $nota_indicador_equivalente = 0;
                $suma_porcentaje_indicador = 0;
                $porcentaje_indicador_realizado = 0;
                
                Utilidades::valordefecto($registro['rind_nota'], 0);
                $notaIndicador_recuperacion = $registro['rind_nota'];

                if ($notaIndicador_recuperacion > $nota_indicador_equivalente) {
                    $indicadorRecuperado = true;
                }

                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["act_periodo"]]['indicadores'][$registro["ind_id"]] = [
                    "ind_id"                        => $registro["ind_id"],
                    "nro"                           => $contarIndicadores,
                    "ind_nombre"                    => $registro['ind_nombre'],
                    "ipc_valor"                     => floatval($registro['ipc_valor']),
                    "nota_indicador_equivalente"    => 0,
                    "suma_porcentaje_indicador"     => $suma_porcentaje_indicador,
                    "porcentaje_indicador_realizado"=> $porcentaje_indicador_realizado,
                    "progreso_indicador"            => 0,
                    "nota_indicador_recuperado"     => $notaIndicador_recuperacion,
                    "nota_final"                    => 0,
                    "recuperado"                    => $indicadorRecuperado,
                    "actividades"                   => []
                ];
                $mat_ar_car_periodo_indicador = $mat_ar_car_periodo . '-' . $registro["ind_id"];
            }
            // // datos de la actividad
            if (!empty($registro["act_id"]) && $mat_ar_car_periodo_indicador_actividad != $mat_ar_car_periodo_indicador . '-' . $registro["act_id"]) {
                $contarActividad++;
                Utilidades::valordefecto($registro['act_valor'], 100);
                
                 $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["act_periodo"]]['indicadores'][$registro["ind_id"]]["actividades"][$registro["act_id"]] = [
                 "act_id"          => $registro["ind_id"],
                 "nro"             => $contarActividad,
                 "act_valor"       => floatval($registro['act_valor']),
                 "cal_nota"        => $registro['cal_nota'],
                 "act_descripcion" => $registro['act_descripcion']
                ];

                $nota_indicador_equivalente = $nota_indicador_equivalente + $registro['cal_nota_equivalente_cien']; 

                $suma_porcentaje_carga      = $suma_porcentaje_carga  + floatval($registro['act_valor']);
                $suma_porcentaje_indicador  = $suma_porcentaje_indicador  + floatval($registro['act_valor']);
                if(!empty($registro['cal_nota'])) {
                    $porcentaje_indicador_realizado  = $porcentaje_indicador_realizado  + floatval($registro['act_valor']);
                    $porcentaje_carga_realizado      = $porcentaje_carga_realizado      + floatval($registro['act_valor']);
                }       
                
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["act_periodo"]]['indicadores'][$registro["ind_id"]]["nota_indicador_equivalente"]     = $nota_indicador_equivalente ;

                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["act_periodo"]]['indicadores'][$registro["ind_id"]]["suma_porcentaje_indicador"]      = $suma_porcentaje_indicador ;
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["act_periodo"]]['indicadores'][$registro["ind_id"]]["porcentaje_indicador_realizado"] = $porcentaje_indicador_realizado ;

                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["act_periodo"]]['suma_porcentaje_carga']      = $suma_porcentaje_carga ;
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["act_periodo"]]['porcentaje_carga_realizado'] = $porcentaje_carga_realizado ;

                $mat_ar_car_periodo_indicador_actividad = $mat_ar_car_periodo_indicador . '-' . $registro["act_id"];
            }
        }

        foreach ($estudiantes as $estudiante) {
            $cantidad_materias = 0;
            $suma_notas_materias_periodo = [];
            $suma_notas_areas_periodo = [];
            $suma_promedios_generales_materias = 0;
            $suma_promedios_generales_areas = 0;
            $fallas_periodo = [];
            foreach ($estudiante["areas"] as $area) {
                $nota_area = [];
                $suma_nota_area = 0;
                foreach ($periodos as $per) {
                    Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$per], [
                        "periodo" => $per,
                        "porcentaje_periodo" => 0
                    ]);
                }
                foreach ($area["cargas"] as $carga) {
                //     $nota_carga_acumulada = 0;
                //     $cantidad_materias++;
                //     Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["mat_valor"], 100 / count($area["cargas"]));
                //     Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["suma_nota_carga"], 0);
                //     Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["suma_nota_area"], 0);
                //     $porcentaje_materia = $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["mat_valor"];
                    

                       foreach ($carga["periodos"] as $periodo) {
                        
                //         Utilidades::valordefecto($suma_notas_materias_periodo[$periodo["bol_periodo"]], 0);
                //         Utilidades::valordefecto($suma_notas_areas_periodo[$periodo["bol_periodo"]], 0);
                //         Utilidades::valordefecto($nota_area[$periodo["bol_periodo"]], 0);
                //         $suma_notas_materias_periodo[$periodo["bol_periodo"]] += $periodo["bol_nota"];
                //         $nota_area[$periodo["bol_periodo"]] += $periodo["bol_nota"] * ($porcentaje_materia / 100);
                //         $suma_notas_areas_periodo[$periodo["bol_periodo"]] += $periodo["bol_nota"] * ($porcentaje_materia / 100);
                //         $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["suma_nota_carga"] += $periodo["bol_nota"];
                //         $nota_carga_acumulada += $periodo["bol_nota"] * ($periodo["porcentaje_periodo"] / 100);
                //     }
                //     $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["nota_carga_acumulada"] = $nota_carga_acumulada;
                //     foreach ($periodos as $per) {
                //         Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["periodos"][$per], [
                //             "bol_periodo" => $per,
                //             "bol_nota" => 0,
                //             "porcentaje_periodo" => 100 / count($periodos),
                //         ]);

                         $porcentaje_carga_realizado = $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['porcentaje_carga_realizado'];
                         $suma_porcentaje_carga      = $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['suma_porcentaje_carga'];

                         $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]["progreso_carga"] = $suma_porcentaje_carga>0?($porcentaje_carga_realizado / ($suma_porcentaje_carga)) * 100:0;

                            foreach ($periodo["indicadores"] as $indicador) { 
                                $nota_indicador_equivalente=$estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["nota_indicador_equivalente"];
                                
                                $suma_porcentaje_indicador      = $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["suma_porcentaje_indicador"];
                                $porcentaje_indicador_realizado = $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["porcentaje_indicador_realizado"];
                                $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["progreso_indicador"] = $suma_porcentaje_indicador>0?($porcentaje_indicador_realizado / ($suma_porcentaje_indicador)) * 100:0;

                                $nota_indicador = ($suma_porcentaje_indicador/100) > 0 ?$nota_indicador_equivalente/($suma_porcentaje_indicador/100):0;
                                $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["nota_indicador"] = $nota_indicador;
                                
                            }
                        }

                }
                // $nota_area_acumulada = 0;
                // foreach ($area["periodos"] as $periodo) {
                //     Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$periodo["periodo"]]["nota_area"], 0);
                //     Utilidades::valordefecto($fallas_periodo[$periodo["periodo"]], 0);
                //     $fallas_periodo[$periodo["periodo"]] += $periodo["ausencia_area"];
                //     $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$periodo["periodo"]]["nota_area"] = $nota_area[$periodo["periodo"]];
                //     $suma_nota_area += $nota_area[$periodo["periodo"]];
                //     $nota_area_acumulada += $nota_area[$periodo["periodo"]] * ($periodo["porcentaje_periodo"] / 100);
                // }
                // $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["suma_nota_area"] += $suma_nota_area;
                // $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["nota_area_acumulada"] += $nota_area_acumulada;
            }
            // foreach ($estudiante["promedios_generales"] as $promedio) {
            //     $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["cantidad_materias"] = $cantidad_materias;
            //     $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["suma_notas_materias"] = $suma_notas_materias_periodo[$promedio["periodo"]];
            //     $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["nota_materia_promedio"] = $suma_notas_materias_periodo[$promedio["periodo"]] / $cantidad_materias;
            //     $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["nota_materia_porcentaje"] = ($suma_notas_materias_periodo[$promedio["periodo"]] / $cantidad_materias) * ($promedio["porcentaje_periodo"] / 100);
            //     $suma_promedios_generales_materias += $suma_notas_materias_periodo[$promedio["periodo"]] / $cantidad_materias;
            //     $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["suma_ausencias"] = $fallas_periodo[$promedio["periodo"]];


            //     $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["cantidad_areas"] = count($estudiante["areas"]);
            //     $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["suma_notas_areas"] = $suma_notas_areas_periodo[$promedio["periodo"]];
            //     $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["nota_area_promedio"] = $suma_notas_areas_periodo[$promedio["periodo"]] / count($estudiante["areas"]);
            //     $estudiantes[$estudiante["mat_id"]]["promedios_generales"][$promedio["periodo"]]["nota_area_porcentaje"] = ($suma_notas_areas_periodo[$promedio["periodo"]] / count($estudiante["areas"])) * ($promedio["porcentaje_periodo"] / 100);
            //     $suma_promedios_generales_areas += $suma_notas_areas_periodo[$promedio["periodo"]] / count($estudiante["areas"]);
            // }
            // $estudiantes[$estudiante["mat_id"]]["suma_promedios_generales_materias"] = $suma_promedios_generales_materias;
            // $estudiantes[$estudiante["mat_id"]]["suma_promedios_generales_areas"] = $suma_promedios_generales_areas;

            // foreach ($periodos as $per) {
            //     $promedio["porcentaje_periodo"] = 100 / count($periodos);
            //     $promedio["periodo"] = $per;
            //     Utilidades::valordefecto($suma_notas_materias_periodo[$promedio["periodo"]], 0);
            //     Utilidades::valordefecto($suma_notas_areas_periodo[$promedio["periodo"]], 0);
            //     Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["promedios_generales"][$per], [
            //         "periodo" => $per,
            //         "porcentaje_periodo" => ($promedio["porcentaje_periodo"]),
            //         "ausencia_area" => 0,
            //         "cantidad_materias" => $cantidad_materias,
            //         "suma_notas_materias" => $suma_notas_materias_periodo[$promedio["periodo"]],
            //         "nota_materia_promedio" => $suma_notas_materias_periodo[$promedio["periodo"]] / $cantidad_materias,
            //         "nota_materia_porcentaje" => ($suma_notas_materias_periodo[$promedio["periodo"]] / $cantidad_materias) * ($promedio["porcentaje_periodo"] / 100),
            //         "cantidad_areas" => count($estudiante["areas"]),
            //         "suma_notas_areas" => $suma_notas_areas_periodo[$promedio["periodo"]],
            //         "nota_area_promedio" => $suma_notas_areas_periodo[$promedio["periodo"]] / count($estudiante["areas"]),
            //         "nota_area_porcentaje" => ($suma_notas_areas_periodo[$promedio["periodo"]] / count($estudiante["areas"])) * ($promedio["porcentaje_periodo"] / 100)
            //     ]);
            // }
        }


        return $estudiantes;
    }


}