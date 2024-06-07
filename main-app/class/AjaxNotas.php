<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
class AjaxNotas {

    /**
     * Este metodo sirve para registrar la nota por periodo de un estudiante
     * 
     * @param int $codEstudiante 
     * @param int $carga 
     * @param int $periodo
     * @param double $nota
     * @param double $notaAnterior
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxPeriodosRegistrar($codEstudiante,$carga,$periodo,$nota,$notaAnterior)
    {
        global $conexion, $config, $conexionPDO;        

        if(trim($nota)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una nota correcta."];
            return $datosMensaje;
        }
        if($nota>$config[4]) $nota = $config[4]; if($nota<1) $nota = 1;

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_estudiante=? AND bol_carga=? AND bol_periodo=? AND institucion=? AND year=?";
        $parametros = [$codEstudiante, $carga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consulta = BindSQL::prepararSQL($sql, $parametros);


        $num = mysqli_num_rows($consulta);
        $rB = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        if($num==0){
            $codigoBOL = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_boletin');
    
            $sql = "INSERT INTO ".BD_ACADEMICA.".academico_boletin(bol_id, bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, bol_fecha_registro, bol_actualizaciones, bol_observaciones, institucion, year)VALUES(?, ?, ?, ?, ?, ?, now(), ?, ?, ?, ?)";
            $parametros = [$codigoBOL, $carga, $codEstudiante, $periodo, $nota, 2, 0, 'Recuperación del periodo.', $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
        }else{
            $sql = "UPDATE ".BD_ACADEMICA.".academico_boletin SET bol_nota=?, bol_nota_anterior=?, bol_observaciones='Recuperación del periodo.', bol_tipo=2, bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now() WHERE bol_id=? AND institucion=? AND year=?";
            $parametros = [$nota, $notaAnterior, $rB['bol_id'], $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
        }

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>"Los cambios se ha guardado correctamente!."];
        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar la nivelación desde resumen por periodo de un estudiante
     * 
     * @param int $codEstudiante 
     * @param int $carga 
     * @param double $nota
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxNivelacionesRegistrar($codEstudiante,$carga,$nota)
    {
        global $conexion, $config;        

        if(trim($nota)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una nota correcta."];
            return $datosMensaje;
        }
        if($nota>$config[4]) $nota = $config[4]; if($nota<1) $nota = 1;

        try{
            $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_nivelaciones WHERE niv_cod_estudiante=".$codEstudiante." AND niv_id_asg='".$carga."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        $num = mysqli_num_rows($consulta);
        $rB = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        if($num==0){
            $codigo=Utilidades::generateCode("NIV");
            try{
                mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_nivelaciones(niv_id, niv_id_asg, niv_cod_estudiante, niv_definitiva, niv_fecha, institucion, year)VALUES('".$codigo."', '".$carga."','".$codEstudiante."','".$nota."',now(), {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
        }else{
            try{
                mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_nivelaciones SET niv_definitiva='".$nota."', niv_fecha=now() WHERE niv_id='".$rB['niv_id']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
        }

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>"Los cambios se ha guardado correctamente!."];
        return $datosMensaje;
    }
}