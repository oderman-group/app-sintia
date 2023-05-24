<?php
require_once("Servicios.php");
class UsuarioServicios extends Servicios
{
    public static function listar($parametrosArray=null)
    {
      $sqlInicial="SELECT * FROM usuarios";
      if($parametrosArray && count($parametrosArray)>0){
        $parametrosValidos=array('uss_tipo','uss_nombre');
        $sqlInicial=Servicios::concatenarWhereAnd($sqlInicial,$parametrosValidos,$parametrosArray);
      };
      $sqlFinal =" ORDER BY gra_vocal";
      $sql=$sqlInicial.$sqlFinal;
      return Servicios::SelectSql($sql);         
    }
    
    public static function consultar($idDato = 1)
    {
        return Servicios::getSql("SELECT * FROM usuarios WHERE uss_id=" . $idDato);
    }

    public static function listarUsuariosEstudiante($parametrosArray=null)
    {
      $sqlInicial="SELECT * FROM usuarios_por_estudiantes";
      if($parametrosArray && count($parametrosArray)>0){
        $parametrosValidos=array('upe_id_usuario');
        $sqlInicial=Servicios::concatenarWhereAnd($sqlInicial,$parametrosValidos,$parametrosArray);
      };
      $sqlFinal ="";
      $sql=$sqlInicial.$sqlFinal;
      return Servicios::SelectSql($sql);         
    }


    public static function editar($Post)
    {
       Servicios::UpdateSql(
            ""
        );
    }

    public static function guardarAcudiente($Post,$transacion=false)
    {
        $result = Servicios::InsertSql(
          "INSERT INTO usuarios(
            uss_usuario, 
            uss_clave, 
            uss_tipo, 
            uss_nombre, 
            uss_estado, 
            uss_ocupacion, 
            uss_email, 
            uss_fecha_nacimiento, 
            uss_permiso1, 
            uss_genero, 
            uss_celular, 
            uss_foto,
            uss_idioma,
            uss_tipo_documento, 
            uss_lugar_expedicion, 
            uss_direccion, 
            uss_apellido1, 
            uss_apellido2, 
            uss_nombre2,
            uss_documento, 
            uss_tema_sidebar,
            uss_tema_header,
            uss_tema_logo
            )VALUES(
            '".$Post["documentoA"]."',
            '12345678',
            3,
            '".$Post["nombresA"]."',
            0,
            '".$Post["ocupacionA"]."',
            '".$Post["email"]."',
            '".$Post["fechaNA"]."',
            0,
            '".$Post["generoA"]."',
            '".$Post["celular"]."', 
            'default.png',
            1,
            '".$Post["tipoDAcudiente"]."',
            '".$Post["lugarDa"]."', 
            '".$Post["direccion"]."', 
            '".$Post["apellido1A"]."', 
            '".$Post["apellido2A"]."', 
            '".$Post["nombre2A"]."',
            '".$Post["documentoA"]."',
            'cyan-sidebar-color',
            'header-indigo',
            'logo-indigo'
            )",
            $transacion
        );
        return $result;
    }

    public static function guardarEstudiante($Post,$transacion=false)
    {
        $result = Servicios::InsertSql(
          "INSERT INTO usuarios(
            uss_usuario, 
            uss_clave, 
            uss_tipo, 
            uss_nombre, 
            uss_estado, 
            uss_email, 
            uss_fecha_nacimiento, 
            uss_permiso1, 
            uss_genero, 
            uss_celular, 
            uss_foto, 
            uss_idioma, 
            uss_tipo_documento, 
            uss_lugar_expedicion, 
            uss_direccion, 
            uss_apellido1, 
            uss_apellido2, 
            uss_nombre2,
            uss_documento, 
            uss_tema_sidebar,
            uss_tema_header,
            uss_tema_logo
            )VALUES(
            '".	$Post["nDoc"]."',
            '12345678',
            4,
            '".$Post["nombres"]."',
            0,
            '".$Post["email"]."',
            '".$Post["fNac"]."',
            0,
            '".$Post["genero"]."',
            '".$Post["celular"]."', 
            'default.png', 
            1, 
            '".$Post["tipoD"]."',
            '".$Post["lugarD"]."', 
            '".$Post["direccion"]."', 
            '".$Post["apellido1"]."', 
            '".$Post["apellido2"]."', 
            '".$Post["nombre2"]."',
            '".$Post["nDoc"]."',
            'cyan-sidebar-color',
            'header-indigo',
            'logo-indigo'
            )",
            $transacion
        );
        return $result;
    }

    public static function guardarUsuarioEstudiante($idAcudiente,$idEstudiante,$transacion=false)
    {
        $result = Servicios::InsertSql(
          "INSERT INTO usuarios_por_estudiantes(upe_id_usuario, upe_id_estudiante)VALUES('".$idAcudiente."', '".$idEstudiante."')",
            $transacion
        );
        return $result;
    }

    public static function apellidos($usuario){
        return strtoupper($usuario['uss_apellido1']." ".$usuario['uss_apellido2']);
    }
    
    public static function nombres($usuario){
        return strtoupper($usuario['uss_nombre']." ".$usuario['uss_nombre2']);
    }

    public static function nombreCompleto($usuario){
        return UsuarioServicios::apellidos($usuario)." ".UsuarioServicios::nombres($usuario);
    }

}
