<?php
require_once("Servicios.php");
class MatriculaServicios extends Servicios
{
    public static function listar($parametrosArray=null)
    {
      $sqlInicial="SELECT * FROM academico_matriculas";
      if($parametrosArray && count($parametrosArray)>0){
        $parametrosValidos=array('mat_grado','mat_grupo','mat_tipo_matricula','mat_acudiente');
        $sqlInicial=Servicios::concatenarWhereAnd($sqlInicial,$parametrosValidos,$parametrosArray);
      };
      $sqlFinal ="";
      $sql=$sqlInicial.$sqlFinal;
      return Servicios::SelectSql($sql);         
    }
    
    public static function consultar($idDato = 1)
    {
        return Servicios::getSql("SELECT * FROM academico_matriculas WHERE mat_id=" . $idDato);
    }

    public static function listarEstudianteNombre($nombre='')
    {
      $sqlInicial= "SELECT mat_id,mat_primer_apellido,mat_segundo_apellido,mat_nombres,mat_nombre2 FROM academico_matriculas
      WHERE CONCAT(mat_primer_apellido,' ',mat_segundo_apellido,' ',mat_nombres,' ',mat_nombre2) LIKE '%".$nombre."%'"; 	  
      $sqlFinal ="AND mat_eliminado IN (0) ORDER BY mat_grado, mat_grupo, mat_primer_apellido, mat_segundo_apellido, mat_nombres";
      $sql=$sqlInicial.$sqlFinal;
      return Servicios::SelectSql($sql,'LIMIT 5');         
    }

    public static function apellidos($matricula){
        return strtoupper($matricula['mat_primer_apellido']." ".$matricula['mat_segundo_apellido']);
    }
    
    public static function nombres($matricula){
        return strtoupper($matricula['mat_nombres']." ".$matricula['mat_nombre2']);
    }

    public static function nombreCompleto($matricula){
        return MatriculaServicios::apellidos($matricula)." ".MatriculaServicios::nombres($matricula);
    }

    public static function guardar($Post,$idAcudiente,$idEstudianteU,$transacion=false){
      $result = Servicios::InsertSql(
        "INSERT INTO academico_matriculas(
          mat_matricula, mat_fecha, mat_tipo_documento, 
          mat_documento, mat_religion, mat_email, 
          mat_direccion, mat_barrio, mat_telefono, 
          mat_celular, mat_estrato, mat_genero, 
          mat_fecha_nacimiento, mat_primer_apellido, mat_segundo_apellido, 
          mat_nombres, mat_grado, mat_grupo, 
          mat_tipo, mat_lugar_nacimiento, mat_lugar_expedicion, 
          mat_acudiente, mat_estado_matricula, mat_id_usuario, 
          mat_folio, mat_codigo_tesoreria, mat_valor_matricula, 
          mat_inclusion, mat_extranjero, mat_tipo_sangre, 
          mat_eps, mat_celular2, mat_ciudad_residencia, 
          mat_nombre2)
          VALUES(
          ".$Post["result_numMat"].", now(), ".$Post["tipoD"].",
          ".$Post["nDoc"].", ".$Post["religion"].", '".strtolower($Post["email"])."',
          '".$Post["direccion"]."', '".$Post["barrio"]."', '".$Post["telefono"]."',
          '".$Post["celular"]."', ".$Post["estrato"].", ".$Post["genero"].", 
          '".$Post["fNac"]."', '".$Post["apellido1"]."', '".$Post["apellido2"]."', 
          '".$Post["nombres"]."', '".$Post["grado"]."', '".$Post["grupo"]."',
          '".$Post["tipoEst"]."', '".$Post["procedencia"]."', '".$Post["lugarD"]."',
          ".$idAcudiente.", '".$Post["matestM"]."', '".$idEstudianteU."', 
          '".$Post["folio"]."', '".$Post["codTesoreria"]."', '".$Post["va_matricula"]."', 
          '".$Post["inclusion"]."', '".$Post["extran"]."', '".$Post["tipoSangre"]."', 
          '".$Post["eps"]."', '".$Post["celular2"]."', '".$Post["ciudadR"]."', 
          '".$Post["nombre2"]."'
          )",
          $transacion
      );
      return $result;

    }

}

