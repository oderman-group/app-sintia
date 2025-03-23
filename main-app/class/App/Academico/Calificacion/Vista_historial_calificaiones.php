<?php

use PhpOffice\PhpSpreadsheet\Chart\Exception;

require_once ROOT_PATH . '/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH . '/main-app/compartido/sintia-funciones.php';
require_once ROOT_PATH . '/main-app/class/UsuariosPadre.php';
require_once ROOT_PATH . '/main-app/class/App/Academico/boletin/Boletin.php';
$files = glob(ROOT_PATH . '/main-app/class/App/Academico/*.php');
foreach ($files as $file) {
    require_once $file;
}

class Vista_historial_calificaciones extends BDT_Tablas
{

    public static $schema = BD_ACADEMICA;
    public static $tableName = 'vista_historial_calificaciones_prueba';
    public  static $tableAs = 'vhc';


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
                // "id_materia IN" =>"('56')",
                // "car_id IN" =>"('228')",
                //  "ind_id" =>"IND8217061170902479"
            ];

        if (!empty($idEstudiantes)) {
            foreach ($idEstudiantes as &$estudiante) {
                $estudiante = "'" . $estudiante . "'";
            }
            ;
            $in_estudiantes = implode(', ', $idEstudiantes);
            $predicado['mat_id IN'] = '(' . $in_estudiantes . ')';
        }
        $sql = parent::Select($predicado, $campos);
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        return self::agruparDatos($result);
    }

    public static function listarHistorialCalificaionesEstudiante(
        string $grado,
        string $grupo,
        string $idEstudiante,
        string $year = null,
    ) {
        try {

            $year = empty($year) ? $_SESSION["bd"] : $year;

            $campos = "*,"
                    .Matricula::$tableAs .".mat_id as id_materia,"
                    .Grado_periodo::$tableAs .".gvp_periodo as periodo,"
                    .Grado_periodo::$tableAs .".gvp_valor as periodo_valor";

            $predicado =
                [
                    Carga::$tableAs . ".institucion" => $_SESSION["idInstitucion"],
                    Carga::$tableAs . ".year"        => $year,
                    Carga::$tableAs . ".car_curso"   => $grado,
                    Carga::$tableAs . ".car_grupo"   => $grupo
                ];

            Grado_periodo::foreignKey(self::INNER, [
                "institucion" => Carga::$tableAs . '.institucion',
                "year"        => Carga::$tableAs . '.year',
                "gvp_grado"   => Carga::$tableAs . '.car_curso'
            ]);

            Grado::foreignKey(self::INNER, [
                "institucion" => Carga::$tableAs . '.institucion',
                "year"        => Carga::$tableAs . '.year',
                "gra_id"      => Carga::$tableAs . '.car_curso'
            ]);

            Grupo::foreignKey(self::INNER, [
                "institucion" => Carga::$tableAs . '.institucion',
                "year"        => Carga::$tableAs . '.year',
                "gru_id"      => Carga::$tableAs . '.car_grupo'
            ]);

            Materia::foreignKey(self::INNER, [
                "institucion" => Carga::$tableAs . '.institucion',
                "year"        => Carga::$tableAs . '.year',
                "mat_id"      => Carga::$tableAs . '.car_materia'
            ]);

            Area::foreignKey(self::INNER, [
                "institucion" => Carga::$tableAs . '.institucion',
                "year"        => Carga::$tableAs . '.year',
                "ar_id"       => Materia::$tableAs . '.mat_area'
            ]);

            Indicador_carga::foreignKey(self::LEFT, [
                "institucion" => Carga::$tableAs . '.institucion',
                "year"        => Carga::$tableAs . '.year',
                "ipc_carga"   => Carga::$tableAs . '.car_id',
                "ipc_periodo" => Grado_periodo::$tableAs . '.gvp_periodo'
            ]);

            Indicador::foreignKey(self::LEFT, [
                "institucion" => Indicador_carga::$tableAs . '.institucion',
                "year"        => Indicador_carga::$tableAs . '.year',
                "ind_id"      => Indicador_carga::$tableAs . '.ipc_indicador'
            ]);

            Actividad::foreignKey(self::LEFT, [
                "institucion"  => Carga::$tableAs . '.institucion',
                "year"         => Carga::$tableAs . '.year',
                "act_id_carga" => Carga::$tableAs . '.car_id',
                "act_id_tipo"  => Indicador::$tableAs . '.ind_id',
                "act_periodo"  => Grado_periodo::$tableAs . '.gvp_periodo',
            ]);

            Matricula::foreignKey(self::INNER, [
                "institucion" => Carga::$tableAs . '.institucion',
                "year"        => Carga::$tableAs . '.year',
                "mat_id"      => "'".$idEstudiante."'",
            ]);

            Matricula_curso::foreignKey(self::LEFT, [
                "matcur_id_institucion" => Carga::$tableAs . '.institucion',
                "matcur_years"          => Carga::$tableAs . '.year',
                "matcur_id_matricula"   => Matricula::$tableAs . '.mat_id',
            ]);

            Academico_boletin::foreignKey(self::LEFT, [
                "institucion"    => Carga::$tableAs . '.institucion',
                "year"           => Carga::$tableAs . '.year',
                "bol_carga"      => Carga::$tableAs . '.car_id',
                "bol_estudiante" => Matricula::$tableAs . '.mat_id',
                "bol_periodo"    => Grado_periodo::$tableAs . '.gvp_periodo',
            ]);

            Academico_Calificacion::foreignKey(self::LEFT, [
                "institucion"        => Carga::$tableAs . '.institucion',
                "year"               => Carga::$tableAs . '.year',
                "cal_id_estudiante"  => Matricula::$tableAs . '.mat_id',
                "cal_id_actividad"   => Actividad::$tableAs . '.act_id',
            ]);

            Indicador_recuperacion::foreignKey(self::LEFT, [
                "institucion"        => Matricula::$tableAs . '.institucion',
                "year"               => Matricula::$tableAs . '.year',
                "rind_estudiante"    => Matricula::$tableAs . '.mat_id',
                "rind_carga"         => Carga::$tableAs . '.car_id',
                "rind_nota >"        => Indicador_recuperacion::$tableAs . '.rind_nota_original',
                "rind_indicador"     => Indicador_carga::$tableAs . '.ipc_indicador',
                "rind_periodo"       => Indicador_carga::$tableAs . '.ipc_periodo',
            ]);

            $listaClases = [
                Grado_periodo::class,
                Grado::class,
                Grupo::class,
                Materia::class,
                Area::class,
                Indicador_carga::class,
                Indicador::class,
                Actividad::class,
                Matricula::class,
                Matricula_curso::class,
                Academico_boletin::class,
                Academico_Calificacion::class,
                Indicador_recuperacion::class,
            ];

            $order = Area::$tableAs . ".ar_posicion,".Carga::$tableAs . ".car_id," . Grado_periodo::$tableAs . ".gvp_periodo";

            $result = parent::SelectJoin(
                predicado: $predicado,
                campos: $campos,
                ClasePrincipal: Carga::class,
                clasesJoin: $listaClases,
                joinString: '',
                orderBy: $order
            );

            return self::agruparDatos($result);
        } catch (Exception  $e) {
           return $result =["error"=>$e->getMessage()];
        }

    }


    public static function agruparDatos(array $datos)
    {
        global $config;

        $conteoEstudiante = 0;
        $mat_id = "";


        $estudiantes = [];
        $periodos = [];
        $cPeriodo = $config['conf_periodos_maximos'];
        for ($i = 1; $i <= $cPeriodo; $i++) {
            $periodos[$i] = $i;
        }

        foreach ($datos as $registro) {

            Utilidades::valordefecto($registro["ind_id"]);
            Utilidades::valordefecto($registro["ind_nombre"]);
            Utilidades::valordefecto($registro["rind_nota"]);
            Utilidades::valordefecto($registro["grado_actual"]);
            Utilidades::valordefecto($registro["grupo_actual"]);            
            Utilidades::valordefecto($registro["indicador_porcentual"], 0);
            Utilidades::valordefecto($registro["valor_indicador"], 0);
            Utilidades::valordefecto($registro["valor_porcentaje_indicador"], 0);


            // Datos del estudiante
            if ($mat_id != $registro["mat_id"]) {
                $mat_ar = "";
                $contarAreas = 0;
                $contarCargas = 0;
                $nombre = Estudiantes::NombreCompletoDelEstudiante($registro);
                $fotoEstudiante = UsuariosFunciones::verificarFoto($registro['mat_foto']);
                $conteoEstudiante++;
                Utilidades::valordefecto($registro["mat_genero"], UsuariosPadre::GENERO_MASCULINO);

                $estudiantes[$registro["mat_id"]] = [
                    "mat_id" => $registro["mat_id"],
                    "nombre" => $nombre,
                    "mat_documento" => $registro["mat_documento"],
                    "nro" => $conteoEstudiante,
                    "mat_matricula" => $registro["mat_matricula"],
                    "gra_id" => $registro["mat_grado"],
                    "gra_nombre" => $registro["grado_actual"],
                    "genero" => $registro["mat_genero"],
                    "gru_id" => $registro["mat_grupo"],
                    "year" => $registro["year"],
                    "gru_nombre" => $registro["grupo_actual"],
                    "mat_estado_matricula" => $registro["mat_estado_matricula"],
                    "mat_numero_matricula" => $registro["mat_numero_matricula"],
                    "mat_folio" => $registro["mat_folio"],
                    "foto" => $fotoEstudiante,
                    "cursos" => [],
                    "periodos" => []
                ];

                $mat_id = $registro["mat_id"];
            }

            // Datos de las areas
            if ($mat_ar != $mat_id . '-' . $registro["ar_id"]) {
                $mat_ar_car = "";
                $contarAreas++;
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']] = [
                    "ar_id" => $registro['ar_id'],
                    "nro" => $contarAreas,
                    "ar_nombre" => $registro['ar_nombre'],
                    "suma_nota_area" => 0,
                    "nota_area_acumulada" => 0,
                    "fallas" => 0,
                    "maneja_porcetaje" => false,
                    "cargas" => [],
                    "periodos" => []
                ];
                $mat_ar = $mat_id . '-' . $registro["ar_id"];
            }
            // Datos de las cargas
            if ($mat_ar_car != $mat_ar . '-' . $registro["car_id"]) {
                $mat_ar_car_periodo = "";
                $contarCargas++;
                Utilidades::valordefecto($registro['mat_valor'], 100);
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']] = [
                    "car_id" => $registro['car_id'],
                    "car_ih" => $registro['car_ih'],
                    "ar_id" => $registro['ar_id'],
                    "nro" => $contarCargas,
                    "id_materia" => $registro['id_materia'],
                    "mat_siglas" => $registro['mat_siglas'],
                    "mat_nombre" => $registro['mat_nombre'],
                    "mat_valor" => $registro['mat_valor'],
                    "suma_nota_carga_periodos" => 0,
                    "nota_carga_acumulada" => 0,
                    "periodos" => []
                ];

                $mat_ar_car = $mat_ar . '-' . $registro["car_id"];
            }
            // // Datos de los periodos
            if ($mat_ar_car_periodo != $mat_ar_car . '-' . $registro["periodo"]) {
                $mat_ar_car_periodo_indicador = "";
                $contarIndicadores = 0;
                $porcentaje_carga_realizado = 0;
                $suma_porcentaje_carga = 0;
                $porcentaje = $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["mat_valor"];
                $porcentajePeriodo = $registro['periodo_valor'];

                Utilidades::valordefecto($registro['act_periodo'], 1);

                //     // valores de los periodos del estudioante                
                $estudiantes[$registro["mat_id"]]["periodos"][$registro["periodo"]] = [
                    "periodo" => $registro["periodo"],
                    "porcentaje_periodo" => $porcentajePeriodo
                ];

                //     // valores de los periodos  para el area       
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["periodos"][$registro["periodo"]] = [
                    "periodo" => $registro["periodo"],
                    "porcentaje_periodo" => $porcentajePeriodo
                ];

                //     // valores de los periodos  para la carga        
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["periodo"]] = [
                    "act_periodo" => $registro["periodo"],
                    "bol_nota" => $registro['bol_nota'],
                    "porcentaje_periodo" => $porcentajePeriodo,
                    "bol_tipo" => $registro['bol_tipo'],
                    "bol_nota_anterior" => $registro['bol_nota_anterior'],
                    "bol_observaciones_boletin" => $registro['bol_observaciones_boletin'],
                    "nota_indicadores" => 0,
                    "porcentaje_carga_realizado" => $porcentaje_carga_realizado,
                    "suma_porcentaje_carga" => $suma_porcentaje_carga,
                    "progreso_carga" => 0,
                    "indicadores" => []
                ];

                $mat_ar_car_periodo = $mat_ar_car . '-' . $registro["periodo"];
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

                if ($notaIndicador_recuperacion > 0) {
                    $indicadorRecuperado = true;
                }

                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["periodo"]]['indicadores'][$registro["ind_id"]] = [
                    "ind_id" => $registro["ind_id"],
                    "nro" => $contarIndicadores,
                    "ind_nombre" => $registro['ind_nombre'],
                    "ipc_valor" => floatval($registro['ipc_valor']),
                    "nota_indicador_equivalente" => 0,
                    "suma_porcentaje_indicador" => $suma_porcentaje_indicador,
                    "porcentaje_indicador_realizado" => $porcentaje_indicador_realizado,
                    "progreso_indicador" => 0,
                    "nota_indicador_recuperado" => $notaIndicador_recuperacion,
                    "nota_final" => 0,
                    "recuperado" => $indicadorRecuperado,
                    "nota_indicador" => 0,
                    "nota_carga_calculada" => 0,
                    "actividades" => []
                ];
                $mat_ar_car_periodo_indicador = $mat_ar_car_periodo . '-' . $registro["ind_id"];
            }
            // // datos de la actividad
            if (!empty($registro["act_id"]) && $mat_ar_car_periodo_indicador_actividad != $mat_ar_car_periodo_indicador . '-' . $registro["act_id"]) {
                $contarActividad++;
                Utilidades::valordefecto($registro['act_valor'], 100);

                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["periodo"]]['indicadores'][$registro["ind_id"]]["actividades"][$registro["act_id"]] = [
                    "act_id" => $registro["ind_id"],
                    "nro" => $contarActividad,
                    "act_valor" => floatval($registro['act_valor']),
                    "cal_nota" => $registro['cal_nota'],
                    "act_descripcion" => $registro['act_descripcion']
                ];

                $nota_indicador_equivalente = $nota_indicador_equivalente + $registro['cal_nota_equivalente_cien'];

                $suma_porcentaje_carga = $suma_porcentaje_carga + floatval($registro['act_valor']);
                $suma_porcentaje_indicador = $suma_porcentaje_indicador + floatval($registro['act_valor']);
                if (!empty($registro['cal_nota'])) {
                    $porcentaje_indicador_realizado = $porcentaje_indicador_realizado + floatval($registro['act_valor']);
                    $porcentaje_carga_realizado = $porcentaje_carga_realizado + floatval($registro['act_valor']);
                }

                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["periodo"]]['indicadores'][$registro["ind_id"]]["nota_indicador_equivalente"] = $nota_indicador_equivalente;

                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["periodo"]]['indicadores'][$registro["ind_id"]]["suma_porcentaje_indicador"] = $suma_porcentaje_indicador;
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["periodo"]]['indicadores'][$registro["ind_id"]]["porcentaje_indicador_realizado"] = $porcentaje_indicador_realizado;

                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["periodo"]]['suma_porcentaje_carga'] = $suma_porcentaje_carga;
                $estudiantes[$registro["mat_id"]]["areas"][$registro['ar_id']]["cargas"][$registro['car_id']]["periodos"][$registro["periodo"]]['porcentaje_carga_realizado'] = $porcentaje_carga_realizado;

                $mat_ar_car_periodo_indicador_actividad = $mat_ar_car_periodo_indicador . '-' . $registro["act_id"];
            }
        }

        foreach ($estudiantes as $estudiante) {
            $porcentaje_faltante = 100;
            $paridos_faltntes = [];
            foreach ($periodos as $per) {
                Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["periodos"][$per]["porcentaje_periodo"], 0);
                Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["periodos"][$per]["periodo"], $per);

                $porcentaje_periodo = $estudiantes[$estudiante["mat_id"]]["periodos"][$per]["porcentaje_periodo"];
                $peri = $estudiantes[$estudiante["mat_id"]]["periodos"][$per]["periodo"];

                $porcentaje_faltante = $porcentaje_faltante - $porcentaje_periodo;
                if ($porcentaje_periodo <= 0) {
                    $paridos_faltntes[$peri] = $peri;
                }
            }
            if ($porcentaje_faltante > 0) {
                foreach ($paridos_faltntes as $per) {
                    $estudiantes[$estudiante["mat_id"]]["periodos"][$per]["porcentaje_periodo"] = $porcentaje_faltante / count($paridos_faltntes);
                }
            }

            foreach ($estudiante["areas"] as $area) {

                $nota_area = [];
                $nota_area_calculada = [];

                foreach ($periodos as $per) {
                    Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$per], [
                        "periodo" => $per,
                        "porcentaje_periodo" => 0
                    ]);
                    Utilidades::valordefecto($nota_area[$per], 0);
                    Utilidades::valordefecto($nota_area_calculada[$per], 0);
                    Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$per]["nota_area"], 0);
                    Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$per]["nota_area_calculada"], 0);
                }


                foreach ($area["cargas"] as $carga) {
                    $nota_carga = [];
                    $nota_carga_final = 0;

                    foreach ($periodos as $per) {
                        Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga["car_id"]]["periodos"][$per], [
                            "periodo" => $per,
                            "porcentaje_periodo" => 0,
                            "bol_nota" => 0,
                            "bol_tipo" => 1,
                            "nota_carga_calculada" => 0,
                            "porcentaje_carga_realizado" => 0,
                            "indicadores" => [],
                        ]);

                        Utilidades::valordefecto($nota_carga[$per], 0);
                    }

                    foreach ($carga["periodos"] as $periodo) {

                        $porcentaje_carga_realizado = $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['porcentaje_carga_realizado'];
                        $suma_porcentaje_carga = $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['suma_porcentaje_carga'];

                        Utilidades::valordefecto($periodo["bol_nota"], 0);

                        $nota_area[$periodo["act_periodo"]] += $periodo["bol_nota"] * ($carga["mat_valor"] / 100);

                        foreach ($periodo["indicadores"] as $indicador) {
                            // calcular nota carga
                            Utilidades::valordefecto($nota_carga[$periodo["act_periodo"]], 0);

                            $nota_indicador_equivalente = $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["nota_indicador_equivalente"];

                            $suma_porcentaje_indicador = $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["suma_porcentaje_indicador"];
                            $porcentaje_indicador_realizado = $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["porcentaje_indicador_realizado"];
                            $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["progreso_indicador"] = $suma_porcentaje_indicador > 0 ? ($porcentaje_indicador_realizado / ($suma_porcentaje_indicador)) * 100 : 0;

                            $nota_indicador = ($porcentaje_indicador_realizado / 100) > 0 ? $nota_indicador_equivalente / ($porcentaje_indicador_realizado / 100) : 0;

                            $nota_indicador_recuperado = $indicador["nota_indicador_recuperado"];
                            $nota_final = $nota_indicador;

                            if ($nota_indicador_recuperado > $nota_indicador) {
                                $nota_final = $nota_indicador_recuperado;
                            }

                            $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["nota_indicador"] = $nota_indicador;
                            $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]['indicadores'][$indicador["ind_id"]]["nota_final"] = $nota_final;

                            //Calcular carga
                            Utilidades::valordefecto($nota_carga[$periodo["act_periodo"]], 0);
                            $nota_carga[$periodo["act_periodo"]] += $nota_final * ($indicador["ipc_valor"] / 100);
                        }

                        //Asignar valor carga
                        Utilidades::valordefecto($estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]["nota_carga_calculada"], 0);
                        $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["cargas"][$carga['car_id']]["periodos"][$periodo["act_periodo"]]["nota_carga_calculada"] = $nota_carga[$periodo["act_periodo"]];

                        $nota_area_calculada[$periodo["act_periodo"]] += $nota_carga[$periodo["act_periodo"]] * ($carga["mat_valor"] / 100);

                        $porcentaje = $estudiantes[$estudiante["mat_id"]]["periodos"][$periodo["act_periodo"]]["porcentaje_periodo"];
                        $nota_carga_final += ($porcentaje / 100) * $periodo["bol_nota"];

                        $estudiantes[$estudiante["mat_id"]]["areas"][$area['ar_id']]["cargas"][$carga['car_id']]["nota_final"] = $nota_carga_final;
                    }
                }
                foreach ($area["periodos"] as $periodo) {
                    $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$periodo["periodo"]]["nota_area"] = $nota_area[$periodo["periodo"]];
                    $estudiantes[$estudiante["mat_id"]]["areas"][$area["ar_id"]]["periodos"][$periodo["periodo"]]["nota_area_calculada"] = $nota_area_calculada[$periodo["periodo"]];
                }
            }
        }


        return $estudiantes;
    }


}