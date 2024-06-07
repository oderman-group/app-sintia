<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

class AjaxCalificaciones {

    /**
     * Este metodo sirve para registrar las calificaciones de un estudiante
     * 
     * @param mysqli    $conexion 
     * @param array     $config 
     * @param string    $codEstudiante 
     * @param string    $nombreEst 
     * @param string    $codNota
     * @param double    $nota
     * @param double    $notaAnterior
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarNota($conexion, $config, $codEstudiante, $nombreEst, $codNota, $nota, $notaAnterior)
    {
        global $conexionPDO;

        if(trim($nota)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una nota correcta."];
            return $datosMensaje;
        }
        if($nota>$config[4]) $nota = $config[4]; if($nota<$config[3]) $nota = $config[3];

        $sql = "SELECT cal_id_actividad, cal_id_estudiante FROM ".BD_ACADEMICA.".academico_calificaciones WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
        $parametros = [$codNota, $codEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consultaNum = BindSQL::prepararSQL($sql, $parametros);

        $num = mysqli_num_rows($consultaNum);
    
        if($num==0){
            $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_calificaciones');
    
            $sql = "INSERT INTO ".BD_ACADEMICA.".academico_calificaciones(cal_id, cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones, institucion, year)VALUES(?, ?,?, ?, now(), ?, ?, ?)";
            $parametros = [$codigo, $codEstudiante, $nota, $codNota, 0, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
            
            $sql = "UPDATE ".BD_ACADEMICA.".academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id=? AND institucion=? AND year=?";
            $parametros = [$codNota, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
    
        }else{
            if($notaAnterior==""){$notaAnterior = "0.0";}
            
            $sql = "UPDATE ".BD_ACADEMICA.".academico_calificaciones SET cal_nota=?, cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1, cal_nota_anterior=?, cal_tipo=1 WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
            $parametros = [$nota, $notaAnterior, $codNota, $codEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
            
            $sql = "UPDATE ".BD_ACADEMICA.".academico_actividades SET act_registrada=1 WHERE act_id=? AND institucion=? AND year=?";
            $parametros = [$codNota, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
    
        }

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>"La nota se ha guardado correctamente para el estudiante <b>".strtoupper($nombreEst)."</b>"];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar las observaciones de un estudiante
     * 
     * @param mysqli    $conexion 
     * @param string       $codEstudiante 
     * @param string    $nombreEst 
     * @param string       $codObservacion
     * @param string    $observacion
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarObservacion($conexion, $codEstudiante, $nombreEst, $codObservacion, $observacion)
    {
        global $config, $conexionPDO;
        if(trim($observacion)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una observación correcta."];
            return $datosMensaje;
        }

        $sql = "SELECT cal_id_actividad, cal_id_estudiante FROM ".BD_ACADEMICA.".academico_calificaciones WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
        $parametros = [$codObservacion, $codEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consultaNum = BindSQL::prepararSQL($sql, $parametros);

        $num = mysqli_num_rows($consultaNum);

        if($num==0){
            $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_calificaciones');
    
            $sql = "INSERT INTO ".BD_ACADEMICA.".academico_calificaciones(cal_id, cal_id_estudiante, cal_observaciones, cal_id_actividad, institucion, year)VALUES(?, ?, ?, ?, ?, ?)";
            $parametros = [$codigo, $codEstudiante, mysqli_real_escape_string($conexion,$observacion), $codObservacion, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
            
            $sql = "UPDATE ".BD_ACADEMICA.".academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id=? AND institucion=? AND year=?";
            $parametros = [$codObservacion, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
            
        }else{
            $sql = "UPDATE ".BD_ACADEMICA.".academico_calificaciones SET cal_observaciones=? WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
            $parametros = [mysqli_real_escape_string($conexion,$observacion), $codObservacion, $codEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
            
            $sql = "UPDATE ".BD_ACADEMICA.".academico_actividades SET act_registrada=1 WHERE act_id=? AND institucion=? AND year=?";
            $parametros = [$codObservacion, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
            
        }

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>'La observación se ha guardado correctamente para el estudiante <b>'.strtoupper($nombreEst).'</b>'];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar una nota masiva a los estudiantes
     * 
     * @param mysqli    $conexion 
     * @param PDO       $conexionPDO 
     * @param array     $datosCargaActual
     * @param string    $codNota
     * @param string    $nota
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarNotasMasiva($conexion, $conexionPDO, $datosCargaActual, $codNota, $nota)
    {
        global $config;
        if(trim($nota)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una nota correcta."];
            return $datosMensaje;
        }

        $consultaE = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
        
        $cont = 1;
        while($estudiantes = mysqli_fetch_array($consultaE, MYSQLI_BOTH)){
            
            $sql = "SELECT cal_id_actividad, cal_id_estudiante FROM ".BD_ACADEMICA.".academico_calificaciones WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
            $parametros = [$codNota, $estudiantes['mat_id'], $config['conf_id_institucion'], $_SESSION["bd"]];
            $consultaNumE = BindSQL::prepararSQL($sql, $parametros);
            $numE = mysqli_num_rows($consultaNumE);
            
            if($numE==0){
                $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_calificaciones').$cont; 
		
                $sql = "DELETE FROM ".BD_ACADEMICA.".academico_calificaciones WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
                $parametros = [$codNota, $estudiantes['mat_id'], $config['conf_id_institucion'], $_SESSION["bd"]];
                $resultado = BindSQL::prepararSQL($sql, $parametros);    
            
                $sql = "INSERT INTO ".BD_ACADEMICA.".academico_calificaciones(cal_id, cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones, institucion, year)VALUES(?, ?, ?, ?, now(), ?, ?, ?)";
                $parametros = [$codigo, $estudiantes['mat_id'], $nota, $codNota, 0, $config['conf_id_institucion'], $_SESSION["bd"]];
                $resultado = BindSQL::prepararSQL($sql, $parametros);

                $cont ++;
            }else{
            
                $sql = "UPDATE ".BD_ACADEMICA.".academico_calificaciones SET cal_nota=?, cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 
                WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
                $parametros = [$nota, $codNota, $estudiantes['mat_id'], $config['conf_id_institucion'], $_SESSION["bd"]];
                $resultado = BindSQL::prepararSQL($sql, $parametros);
            }
        }
        
        $sql = "UPDATE ".BD_ACADEMICA.".academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id=? AND institucion=? AND year=?";
        $parametros = [$codNota, $config['conf_id_institucion'], $_SESSION["bd"]];
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>'Se ha guardado la misma nota para todos los estudiantes en esta actividad. La página se actualizará en unos segundos para que vea los cambios...'];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar las notas de recuperación de un estudiante
     * 
     * @param mysqli    $conexion 
     * @param array     $config 
     * @param string    $codEstudiante 
     * @param string    $nombreEst 
     * @param string    $codNota
     * @param double    $nota
     * @param double    $notaAnterior
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarNotaRecuperacion($conexion, $config, $codEstudiante, $nombreEst, $codNota, $nota, $notaAnterior)
    {
        if(trim($nota)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una nota correcta."];
            return $datosMensaje;
        }
        if($nota>$config[4]) $nota = $config[4]; if($nota<$config[3]) $nota = $config[3];
        $codigo=Utilidades::generateCode("REC");
        
        try{
            mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_recuperaciones_notas(rec_id, rec_cod_estudiante, rec_nota, rec_id_nota, rec_fecha, rec_nota_anterior, institucion, year)VALUES('".$codigo."', '".$codEstudiante."','".$nota."','".$codNota."', now(),'".$notaAnterior."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        
        $sql = "UPDATE ".BD_ACADEMICA.".academico_calificaciones SET cal_nota=?, cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1, cal_nota_anterior=?, cal_tipo=2 WHERE cal_id_actividad=? AND cal_id_estudiante=? AND institucion=? AND year=?";
        $parametros = [$nota, $notaAnterior, $codNota, $codEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>"La nota de recuperación se ha guardado correctamente para el estudiante <b>".strtoupper($nombreEst)."</b>"];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar las notas de comportamiento de un estudiante
     * 
     * @param mysqli    $conexion 
     * @param array     $config 
     * @param string    $codEstudiante 
     * @param string    $nombreEst 
     * @param string       $carga
     * @param double    $nota
     * @param int       $periodo
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarNotaDisciplina($conexion, $config, $codEstudiante, $nombreEst, $carga, $nota, $periodo)
    {
        if(trim($nota)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una nota correcta."];
            return $datosMensaje;
        }
        if($nota>$config[4]) $nota = $config[4]; if($nota<$config[3]) $nota = $config[3];

        try{
            $consultaNumD=mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$codEstudiante."' AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        $numD = mysqli_num_rows($consultaNumD);

        if($numD==0){
            $idInsercion=Utilidades::generateCode("DN");
            try{
                mysqli_query($conexion, "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo, institucion, year)VALUES('" .$idInsercion . "', '".$codEstudiante."','".$carga."','".$nota."', now(),'".$periodo."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
            } catch (Exception $e) {
                include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
            }
        }else{
            try{
                mysqli_query($conexion, "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_nota='".$nota."', dn_fecha=now() WHERE dn_cod_estudiante='".$codEstudiante."' AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
            } catch (Exception $e) {
                include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
            }
        }

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>"La nota de comportamiento se ha guardado correctamente para el estudiante <b>".strtoupper($nombreEst)."</b>"];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar las observaciones de comportamiento de un estudiante
     * 
     * @param mysqli    $conexion 
     * @param array     $config 
     * @param string       $codEstudiante 
     * @param string    $nombreEst 
     * @param string       $carga
     * @param string    $observacion
     * @param int       $periodo
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarObservacionDisciplina($conexion, $codEstudiante, $carga, $observacion, $periodo)
    {
        global $config;
        if(trim($observacion)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una observación correcta."];
            return $datosMensaje;
        }

        try{
            $consultaNumD=mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$codEstudiante."' AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        $numD = mysqli_num_rows($consultaNumD);

        if($numD==0){
            $idInsercion=Utilidades::generateCode("DN");
            try{
                mysqli_query($conexion, "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_observacion, dn_fecha, dn_periodo, institucion, year)VALUES('" .$idInsercion . "', '".$codEstudiante."','".$carga."','".mysqli_real_escape_string($conexion,$observacion)."', now(),'".$periodo."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
            } catch (Exception $e) {
                include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
            }
        }else{
            try{
                mysqli_query($conexion, "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_observacion='".mysqli_real_escape_string($conexion,$observacion)."', dn_fecha=now() WHERE dn_cod_estudiante='".$codEstudiante."'  AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
            } catch (Exception $e) {
                include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
            }
        }
        $datosEstudiante =Estudiantes::obtenerDatosEstudiante($codEstudiante);

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>"La observación de comportamiento se ha guardado correctamente para el estudiante <b>".Estudiantes::NombreCompletoDelEstudiante($datosEstudiante)."</b>"];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar una nota de comportamiento masiva a los estudiantes
     * 
     * @param mysqli    $conexion 
     * @param PDO       $conexionPDO 
     * @param array     $datosCargaActual
     * @param string    $carga
     * @param int       $periodo
     * @param string    $nota
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarNotasDisciplinaMasiva($conexion, $conexionPDO, $datosCargaActual, $carga, $periodo, $nota)
    {
        global $config;
        if(trim($nota)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una nota correcta."];
            return $datosMensaje;
        }

        $consultaE = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCargaActual);
    
        $cont = 1;
        while($estudiantes = mysqli_fetch_array($consultaE, MYSQLI_BOTH)){
            $consultaNumE=mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$estudiantes['mat_id']."' AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
            $numE = mysqli_num_rows($consultaNumE);
            
            if($numE==0){
                $idInsercion = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'disiplina_nota').$cont; 
            
                mysqli_query($conexion, "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} AND dn_cod_estudiante='".$estudiantes['mat_id']."'");
            
                mysqli_query($conexion, "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo, institucion, year)VALUES('" .$idInsercion . "', '".$estudiantes['mat_id']."','".$carga."','".$nota."', now(),'".$periodo."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");

                $cont++;
            }else{
                mysqli_query($conexion, "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_nota='".$nota."', dn_fecha=now() WHERE dn_periodo='".$periodo."' AND dn_cod_estudiante='".$estudiantes['mat_id']."' AND (institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]})");
            }
        }

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>'Se ha guardado la misma nota de comportamiento para todos los estudiantes en esta actividad. La página se actualizará en unos segundos para que vea los cambios...'];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar las observaciones que se veran reflejadas en el boletin de un estudiante
     * 
     * @param mysqli    $conexion 
     * @param array     $config 
     * @param string       $codEstudiante 
     * @param string    $nombreEst 
     * @param string       $carga
     * @param double    $observacion
     * @param int       $periodo
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarObservacionBoletin($conexion, $codEstudiante, $carga, $observacion, $periodo)
    {
        global $config, $conexionPDO;

        if(trim($observacion)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una observación correcta."];
            return $datosMensaje;
        }

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_carga=? AND bol_estudiante=? AND bol_periodo=? AND institucion=? AND year=?";
        $parametros = [$carga, $codEstudiante, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consultaNumD = BindSQL::prepararSQL($sql, $parametros);

        $numD = mysqli_num_rows($consultaNumD);

        if($numD==0){
            $codigoBOL = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_boletin');
    
            $sql = "INSERT INTO ".BD_ACADEMICA.".academico_boletin(bol_id, bol_carga, bol_estudiante, bol_periodo, bol_tipo, bol_observaciones_boletin, bol_fecha_registro, bol_actualizaciones, institucion, year)VALUES(?, ?, ?, ?, ?, ?, now(), ?, ?, ?)";
            $parametros = [$codigoBOL, $carga, $codEstudiante, $periodo, 1, mysqli_real_escape_string($conexion,$observacion), 0, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
        }else{
            $sql = "UPDATE ".BD_ACADEMICA.".academico_boletin SET bol_observaciones_boletin=?, bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now() WHERE bol_carga=? AND bol_estudiante=? AND bol_periodo=? AND institucion=? AND year=?";
            $parametros = [mysqli_real_escape_string($conexion,$observacion), $carga, $codEstudiante, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
        }
        $datosEstudiante =Estudiantes::obtenerDatosEstudiante($codEstudiante);

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>"La observación para el boletín de este periodo se ha guardado correctamente para el estudiante <b>".Estudiantes::NombreCompletoDelEstudiante($datosEstudiante)."</b>"];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar las notas de recuperacion de indicadores de un estudiante
     * 
     * @param mysqli    $conexion 
     * @param array     $config 
     * @param string       $codEstudiante 
     * @param string    $nombreEst 
     * @param string       $carga
     * @param int       $periodo
     * @param string       $codNota
     * @param double    $nota
     * @param double    $notaAnterior
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarRecuperacionIndicadores($conexion, $config, $codEstudiante, $carga, $periodo, $codNota, $nota, $notaAnterior)
    {
        global $conexionPDO;

        if(trim($nota)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite una nota correcta."];
            return $datosMensaje;
        }
        if($nota>$config[4]) $nota = $config[4]; if($nota<$config[3]) $nota = $config[3];
        $datosEstudiante =Estudiantes::obtenerDatosEstudiante($codEstudiante);

        //Consultamos si tiene registros en el boletín
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_carga=? AND bol_periodo=? AND bol_estudiante=? AND institucion=? AND year=?";
        $parametros = [$carga, $periodo, $codEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consultaBoletinDatos = BindSQL::prepararSQL($sql, $parametros);
        $boletinDatos = mysqli_fetch_array($consultaBoletinDatos, MYSQLI_BOTH);

        $caso = 1; //Inserta la nueva definitiva del indicador normal
        if(empty($boletinDatos['bol_id'])){
            $caso = 2;
            $mensajeNot = 'El estudiante <b>'.Estudiantes::NombreCompletoDelEstudiante($datosEstudiante).'</b> no presenta registros en el boletín actualmente para este periodo, en esta asignatura.';
            $heading = 'No se generó ningún cambio';
            $tipo = 'warning';
        }

        if($caso == 1){
            try{
                $consultaIndicador=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_indicador='".$codNota."' AND ipc_carga='".$carga."' AND ipc_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
            } catch (Exception $e) {
                include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
            }
            $indicador = mysqli_fetch_array($consultaIndicador, MYSQLI_BOTH);
            $valorIndicador = ($indicador['ipc_valor']/100);
            $rindNotaActual = ($nota * $valorIndicador);

            $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion WHERE rind_carga=? AND rind_estudiante=? AND rind_periodo=? AND rind_indicador=? AND institucion=? AND year=?";
            $parametros = [$carga, $codEstudiante, $periodo, $codNota, $config['conf_id_institucion'], $_SESSION["bd"]];
            $consultaNum = BindSQL::prepararSQL($sql, $parametros);
            $num = mysqli_num_rows($consultaNum);

            if($num==0){
                $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_indicadores_recuperacion');

                $sql = "INSERT INTO ".BD_ACADEMICA.".academico_indicadores_recuperacion(rind_id, rind_fecha_registro, rind_estudiante, rind_carga, rind_nota, rind_indicador, rind_periodo, rind_actualizaciones, rind_nota_actual, rind_valor_indicador_registro, institucion, year)VALUES(?, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $parametros = [$codigo, $codEstudiante, $carga, $nota, $codNota, $periodo, 1, $rindNotaActual, $indicador['ipc_valor'], $config['conf_id_institucion'], $_SESSION["bd"]];
                $resultado = BindSQL::prepararSQL($sql, $parametros);
            }else{
                if($notaAnterior==""){$notaAnterior = "0.0";}
                
                $sql = "UPDATE ".BD_ACADEMICA.".academico_indicadores_recuperacion SET rind_nota=?, rind_nota_anterior=?, rind_actualizaciones=rind_actualizaciones+1, rind_ultima_actualizacion=now(), rind_nota_actual=?, rind_tipo_ultima_actualizacion=2, rind_valor_indicador_actualizacion=? WHERE rind_carga=? AND rind_estudiante=? AND rind_periodo=? AND rind_indicador=? AND institucion=? AND year=?";
                $parametros = [$nota, $notaAnterior, $rindNotaActual, $indicador['ipc_valor'], $carga, $codEstudiante, $periodo, $codNota, $config['conf_id_institucion'], $_SESSION["bd"]];
                $resultado = BindSQL::prepararSQL($sql, $parametros);
            }
            
            //Actualizamos la nota actual a los que la tengan nula.
            $sql = "UPDATE ".BD_ACADEMICA.".academico_indicadores_recuperacion SET rind_nota_actual=rind_nota_original WHERE rind_carga=? AND rind_estudiante=? AND rind_periodo=? AND rind_nota_actual IS NULL AND rind_nota_original=rind_nota AND institucion=? AND year=?";
            $parametros = [$carga, $codEstudiante, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);

            //Se suman los decimales de todos los indicadores para obtener la definitiva de la asignatura
            $sql = "SELECT SUM(rind_nota_actual) FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion WHERE rind_carga=? AND rind_estudiante=? AND rind_periodo=? AND institucion=? AND year=?";
            $parametros = [$carga, $codEstudiante, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
            $consultaRecuperacionIndicador = BindSQL::prepararSQL($sql, $parametros);
            $recuperacionIndicador = mysqli_fetch_array($consultaRecuperacionIndicador, MYSQLI_BOTH);
            
            $notaDefIndicador = round($recuperacionIndicador[0],1);

            $sql = "UPDATE ".BD_ACADEMICA.".academico_boletin SET bol_nota_anterior=bol_nota, bol_nota=?, bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now(), bol_nota_indicadores=?, bol_tipo=3, bol_observaciones='Actualizada desde el indicador.' WHERE bol_carga=? AND bol_periodo=? AND bol_estudiante=? AND institucion=? AND year=?";
            $parametros = [$notaDefIndicador, $notaDefIndicador, $carga, $periodo, $codEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
            
            $mensajeNot = 'La recuperación del indicador de este periodo se ha guardado correctamente para el estudiante <b>'.Estudiantes::NombreCompletoDelEstudiante($datosEstudiante).'</b>. La nota definitiva de la asignatura ahora es <b>'.round($recuperacionIndicador[0],1)."</b>.";
            $heading = 'Cambios guardados';
            $tipo = 'success';
        }

        $datosMensaje=["heading"=>$heading,"estado"=>$tipo,"mensaje"=>$mensajeNot];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar los aspectos academicos de los estudiantes
     * 
     * @param mysqli    $conexion 
     * @param string       $codEstudiante 
     * @param string       $carga
     * @param int       $periodo
     * @param double    $aspectoAcademico
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarAspectosAcademicos($conexion, $codEstudiante, $carga, $periodo, $aspectoAcademico)
    {
        global $config;
        if(trim($aspectoAcademico)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite un aspecto correcto."];
            return $datosMensaje;
        }

        $consultaNumD=mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$codEstudiante."' AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        $numD = mysqli_num_rows($consultaNumD);
        $datosEstudiante =Estudiantes::obtenerDatosEstudiante($codEstudiante);
	
        if($numD==0){
            $idInsercion=Utilidades::generateCode("DN");
            mysqli_query($conexion, "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_aspecto_academico, dn_periodo, institucion, year)VALUES('" .$idInsercion . "', '".$codEstudiante."','".$carga."','".$aspectoAcademico."', '".$periodo."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
        }else{
            mysqli_query($conexion, "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_aspecto_academico='".$aspectoAcademico."', dn_fecha_aspecto=now() WHERE dn_cod_estudiante='".$codEstudiante."'  AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        }

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>'El aspecto academico se ha guardado correctamente para el estudiante <b>'.Estudiantes::NombreCompletoDelEstudiante($datosEstudiante).'</b>'];

        return $datosMensaje;
    }

    /**
     * Este metodo sirve para registrar los aspectos convivencial de los estudiantes
     * 
     * @param mysqli    $conexion 
     * @param string       $codEstudiante 
     * @param string       $carga
     * @param int       $periodo
     * @param double    $aspectoConvivencial
     * 
     * @return array // se retorna mensaje de confirmación
    **/
    public static function ajaxGuardarAspectosConvivencional($conexion, $codEstudiante, $carga, $periodo, $aspectoConvivencial)
    {
        global $config;
        if(trim($aspectoConvivencial)==""){
            $datosMensaje=["heading"=>"Nota vacia","estado"=>"warning","mensaje"=>"Digite un aspecto correcto."];
            return $datosMensaje;
        }

        $consultaNumD=mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$codEstudiante."' AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        $numD = mysqli_num_rows($consultaNumD);
        $datosEstudiante =Estudiantes::obtenerDatosEstudiante($codEstudiante);
	
        if($numD==0){
            $idInsercion=Utilidades::generateCode("DN");
            mysqli_query($conexion, "INSERT INTO ".BD_DISCIPLINA.".disiplina_nota(dn_id, dn_cod_estudiante, dn_id_carga, dn_aspecto_convivencial, dn_periodo, institucion, year)VALUES('" .$idInsercion . "', '".$codEstudiante."','".$carga."','".$aspectoConvivencial."', '".$periodo."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
        }else{
            mysqli_query($conexion, "UPDATE ".BD_DISCIPLINA.".disiplina_nota SET dn_aspecto_convivencial='".$aspectoConvivencial."', dn_fecha_aspecto=now() WHERE dn_cod_estudiante='".$codEstudiante."'  AND dn_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        }

        $datosMensaje=["heading"=>"Cambios guardados","estado"=>"success","mensaje"=>'El aspecto convivencial se ha guardado correctamente para el estudiante <b>'.Estudiantes::NombreCompletoDelEstudiante($datosEstudiante).'</b>'];

        return $datosMensaje;
    }
}