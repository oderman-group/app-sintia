<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
class SubRoles {

     /**
     * Esta función  crea un registro en la tabla sub_roles
     *
     * @param string $nombre
     * @param array $paginas
     *
     * @return String // se retorna el id del registro ingresado    */

    public static function crear(String $nombre = "",array $paginas = []){
        global $conexion, $baseDatosServicios,$config;
        $idRegistro = -1;        
        try {
            $sqlUpdate="INSERT INTO ".$baseDatosServicios.".sub_roles(
                subr_nombre, 
                subr_institucion, 
                subr_year)
            VALUES(
                '".$nombre."',
                '".$config['conf_id_institucion']."', 
                '".$config['conf_agno']."'
                )";
            mysqli_query($conexion,$sqlUpdate);
            $idRegistro = mysqli_insert_id($conexion);  
            self::crearRolesPaginas( $idRegistro,$paginas);
                
            
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }
        
        return $idRegistro;
    }
  /**
     * Esta función  crea un registro en la tabla sub_roles_paginas
     *
     * @param string $idSubRol
     * @param array $paginas
     *
     * @return String // se retorna el id del registro ingresado    */
    public static function crearRolesPaginas($idSubRol,array $paginas= []){
        global $conexion, $baseDatosServicios;
        $idRegistro = -1;        
        try {
            foreach ($paginas as $page ) {
                $paginaSubrol=self::validarPaginasSubRol($idSubRol,$page,$conexion,$baseDatosServicios);
                if($paginaSubrol){
                    $sqlinsert="INSERT INTO ".$baseDatosServicios.".sub_roles_paginas(
                        spp_id_rol, 
                        spp_id_pagina
                    )
                    VALUES(
                        '".$idSubRol."',
                        '".$page."'                        
                    )";
                    mysqli_query($conexion,$sqlinsert);
                    $idRegistro = mysqli_insert_id($conexion);
                    self::guardarPaginasHijasSubRol($idSubRol,$page);
                    self::guardarPaginasDependecias($idSubRol,$page,$conexion,$baseDatosServicios);
                }
            }                  
            
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }
        
        return $idRegistro;
    }
     /**
     * Esta función  crea un registro en la tabla sub_roles_usuarios
     *
     * @param string $idUsuario
     * @param array $subRoles
     *
     * @return String // se retorna el id del registro ingresado    */
    public static function crearRolesUsuario($idUsuario,array $subRoles= []){
        global $conexion, $baseDatosServicios,$config;
        $idRegistro = -1;        
        try {
            foreach ($subRoles as $subrol ) {
                $sqlinsert="INSERT INTO ".$baseDatosServicios.".sub_roles_usuarios(
                    spu_id_sub_rol, 
                    spu_id_usuario,
                    spu_institucion,
                    spu_year
                 )
                VALUES(
                    '".$subrol."',
                    '".$idUsuario."',                    
                    '".$config['conf_id_institucion']."', 
                    '".$config['conf_agno']."'                        
                )";
                mysqli_query($conexion,$sqlinsert);
                $idRegistro = mysqli_insert_id($conexion);                   
            }                  
            
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }
        
        return $idRegistro;
    }
          /**
     * Esta función actauliza  la tabla sub_roles_usuarios
     * importante que la estructura del array pueda venir los valores:
     * ($datos["nombre"],$datos["id"],$datos["paginas"])
     * @param array $datos
     *
     * @return void 
     */
    public static function actualizar(array $datos = []){
        global $conexion, $baseDatosServicios;
        
        $setNombre=empty($datos["nombre"])?"":$datos["nombre"];            

        $sqlUpdate="
        UPDATE ".$baseDatosServicios.".sub_roles
        SET subr_nombre='".$setNombre."'
        WHERE subr_id='".$datos["id"]."'";  
        mysqli_query($conexion,$sqlUpdate);

        if(!empty($datos["paginas"])){
            try{
                $consultaPaginaSubRoles = mysqli_query($conexion, "SELECT spp_id_pagina FROM ".$baseDatosServicios.".sub_roles_paginas 
                INNER JOIN ".$baseDatosServicios.".paginas_publicidad ON pagp_id=spp_id_pagina AND (pagp_pagina_padre='' OR pagp_pagina_padre IS NULL)
                WHERE spp_id_rol = '".$datos["id"]."'");
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
            $subRolesPaginas = mysqli_fetch_all($consultaPaginaSubRoles, MYSQLI_ASSOC);
            $valoresPaginas = array_column($subRolesPaginas, 'spp_id_pagina');

            $resultadoAgregar= array_diff($datos["paginas"],$valoresPaginas);
            if(!empty($resultadoAgregar)){
                try{
                    self::crearRolesPaginas($datos["id"],$resultadoAgregar);
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }
            }

            $resultadoEliminar= array_diff($valoresPaginas,$datos["paginas"]);
            if(!empty($resultadoEliminar)){
                try{
                    self::eliminarRolesPaginas($datos["id"],$resultadoEliminar);
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }
            }
        }else{
            $sqlDelete="DELETE FROM ".$baseDatosServicios.".sub_roles_paginas
            WHERE spp_id_rol='".$datos["id"]."'";
            try{
                mysqli_query($conexion,$sqlDelete);
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
        }

        if(!empty($datos["usuarios"])){
            try{
                $consultaUsuariosSubRoles = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".sub_roles_usuarios WHERE spu_id_sub_rol = '".$datos["id"]."'");
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
            $subRolesUsuarios = mysqli_fetch_all($consultaUsuariosSubRoles, MYSQLI_ASSOC);
            $valoresUsuarios = array_column($subRolesUsuarios, 'spu_id_usuario');

            $resultadoAgregarUsuario= array_diff($datos["usuarios"],$valoresUsuarios);
            if(!empty($resultadoAgregarUsuario)){
                try{
                    self::crearRolesUsuarioMasivos($resultadoAgregarUsuario,$datos["id"]);
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }
            }

            $resultadoEliminarUsuario= array_diff($valoresUsuarios,$datos["usuarios"]);
            if(!empty($resultadoEliminarUsuario)){
                try{
                    self::eliminarRolesUsuarioMasivos($datos["id"],$resultadoEliminarUsuario);
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }
            }
        }else{
            $sqlDelete="DELETE FROM ".$baseDatosServicios.".sub_roles_usuarios
            WHERE spu_id_sub_rol='".$datos["id"]."'";
            try{
                mysqli_query($conexion,$sqlDelete);
            } catch (Exception $e) {
                include("../compartido/error-catch-to-report.php");
            }
        }
    }

     /**
     * Esta función  crea o elimina un registro si es necesario en la tabla sub_roles_usuarios
     *
     * @param string $idUsuario
     * @param array $subRoles
     *
     * @return void // */
     public static function actualizarRolesUsuario($idUsuario,array $subRoles= []){

        $subRolesActualales= self::listarRolesUsuarios($idUsuario);
        $subRolesCrear= [];
        $subRolesElimnar= [];
        $cantAgregar = 0;
        $cantEliminar = 0; 
        $subRolesArray= [];
        foreach ($subRoles as $subRol ) {
            $subRolesArray[$subRol]=$subRol;
        } 
        foreach ($subRolesActualales as $subrolBD ) {
            if(!array_key_exists($subrolBD["spu_id_sub_rol"], $subRolesArray)){
                $subRolesElimnar[$subrolBD["spu_id_sub_rol"]]= $subrolBD["spu_id_sub_rol"];
                $cantEliminar ++;
            }
        }
        foreach ($subRolesArray as $subrol ) {                
            if(!array_key_exists($subrol, $subRolesActualales)){
               $subRolesCrear[$subrol]= $subrol;
               $cantAgregar ++;
            }
        }
        if($cantEliminar>=1){
            self::eliminarSubrolesUsuarios($idUsuario, $subRolesElimnar);
        }
        if($cantAgregar>=1){
            self::crearRolesUsuario($idUsuario, $subRolesCrear);
        }
    }

     /**
     * Esta función  consulta  la tabla sub_roles por la identificaicon unica
     * @param String $id
     *
     * @return array // retorna el dato encontrado  */
    public static function consultar(String $id ='-1' ) {
        global $conexion, $baseDatosServicios;
        $resultado=[];        
       
        $sqlExecute="SELECT * FROM ".$baseDatosServicios.".sub_roles
        LEFT JOIN ".$baseDatosServicios .".instituciones ON ins_id = subr_institucion
        WHERE subr_id = '".$id."'";
        try {
           $resultadoConsuta = mysqli_query($conexion,$sqlExecute);
           while($fila=$resultadoConsuta->fetch_assoc()){
               $fila["paginas"]=self::listarPaginasRoles($fila["subr_id"]); 
               $fila["usuarios"]=self::listarUsuariosRoles($fila["subr_id"]); 
               $resultado=$fila; 
           } 
           return $resultado;
        } catch (Exception $e) {            
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        
    }
     /**
     * Esta función  Lista  la tabla sub_roles
     * @param array $parametrosBusqueda
     *
     *   */   
    public static function listar(array $parametrosBusqueda = []) {
        global $conexion,$config, $baseDatosServicios;
        $resultado = [];
        $institucion=empty($parametrosBusqueda["institucion"])?$config['conf_id_institucion']:$parametrosBusqueda['institucion'];
        $andYear=empty($parametrosBusqueda["year"])?" ":"AND subr_year='".$parametrosBusqueda["year"]."'";
        
        $sqlExecute="SELECT * FROM ".$baseDatosServicios.".sub_roles
        LEFT JOIN ".$baseDatosServicios .".instituciones ON ins_id = subr_institucion
        WHERE subr_institucion =".$institucion
        .$andYear;
        try {
            $resultado= mysqli_query($conexion,$sqlExecute);
           
            return $resultado;
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }
        return $resultado;
    }
     /**
     * Esta función  Lista  la tabla paginas_publicidad por el tipo de usuario
     * por defecto se buscará por el tipo de usuario=5 y las ordenara teniendo encuenta el sub rol selecionado
     * @param String $tipoUsuario
     * @param String $subRol
     * */
    public static function listarPaginas($subRol='-1', $tipoUsuario = '5', $soloActivas='0', $idModulo=''){
        global $conexion, $baseDatosServicios, $config;
        $resultado = [];
        $sqlJoin   = $soloActivas == '1' ? "INNER" : "LEFT";
        $filtroMod   = !empty($idModulo) ? "AND pagp_modulo = '{$idModulo}'" : "";
       
        $sqlExecute="SELECT pp.*, m.*, srp.* FROM ".BD_ADMIN.".paginas_publicidad pp
        INNER JOIN ".BD_ADMIN .".modulos m ON mod_id=pagp_modulo
        INNER JOIN ".BD_ADMIN .".instituciones_modulos ON ipmod_modulo=mod_id AND ipmod_institucion={$config['conf_id_institucion']} 
        ".$sqlJoin." JOIN ".BD_ADMIN .".sub_roles_paginas srp ON spp_id_pagina=pagp_id AND spp_id_rol='".$subRol."'
        WHERE pagp_tipo_usuario = '".$tipoUsuario."' {$filtroMod} AND pagp_asignable_subroles = '".SI."' AND (pagp_pagina_padre='' OR pagp_pagina_padre IS NULL) 
        UNION
        SELECT pp.*, m.*, srp.* FROM ".BD_ADMIN.".paginas_publicidad pp
        INNER JOIN ".BD_ADMIN .".modulos m ON mod_id=pagp_modulo
        INNER JOIN ".BD_ADMIN .".instituciones_paquetes_extras ON paqext_id_paquete=mod_id AND paqext_institucion={$config['conf_id_institucion']} AND paqext_tipo='".MODULOS."' 
        ".$sqlJoin." JOIN ".BD_ADMIN .".sub_roles_paginas srp ON spp_id_pagina=pagp_id AND spp_id_rol='".$subRol."'
        WHERE pagp_tipo_usuario = '".$tipoUsuario."' {$filtroMod} AND pagp_asignable_subroles = '".SI."' AND (pagp_pagina_padre='' OR pagp_pagina_padre IS NULL) 
        ORDER BY spp_id_pagina DESC";
        try {
            $resultado = mysqli_query($conexion,$sqlExecute);
            
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }
        return $resultado;
    }
    /**
     * Esta función  Lista  la tabla sub_roles_paginas teniendo en cuenta el rol
     * por defecto se buscará por el tipo de usuario=1
     * @param String $idRol
     *   */
    public static function listarPaginasRoles($idRol = '1'){
        global $conexion, $baseDatosServicios;
        $arraysDatos = [];
        
        $sqlExecute="SELECT * FROM ".$baseDatosServicios.".sub_roles_paginas
        LEFT JOIN ".$baseDatosServicios .".sub_roles ON subr_id=spp_id_rol  
        LEFT JOIN ".$baseDatosServicios .".paginas_publicidad ON pagp_id=spp_id_pagina
        WHERE spp_id_rol = '".$idRol."' AND (pagp_pagina_padre='' OR pagp_pagina_padre IS NULL) " ;
        try {
            $resultadoConsulta = mysqli_query($conexion,$sqlExecute);
            while($fila=$resultadoConsulta->fetch_assoc()){
                $arraysDatos[$fila["pagp_id"]]=$fila;
            } 
            return $arraysDatos;
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }
        
    }
 /**
     * Esta función  Lista  la tabla sub_roles_usuarios teniendo en cuenta el id del usuario
     * por defecto se buscará por el  usuario=1
     * @param String $idUsuario
     *  */
    public static function listarRolesUsuarios($idUsuario = '1'){
        global $conexion,$config, $baseDatosServicios;
        $arraysDatos = [];
        $sqlExecute="SELECT * FROM ".$baseDatosServicios.".sub_roles_usuarios
        WHERE spu_id_usuario = '".$idUsuario."'
        AND spu_institucion =".$config['conf_id_institucion'];
        try {
            $resultadoConsulta = mysqli_query($conexion,$sqlExecute);
            while($fila=$resultadoConsulta->fetch_assoc()){
                $arraysDatos[$fila["spu_id_sub_rol"]]=$fila;
            } 
            return $arraysDatos;
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }
        
    }
    /**
     * Esta función  Elimina los  registros en la tabla sub_roles_usuarios
     *
     * @param String $idUsuario
     * @param array $subRoles
     *
     * @return void //   */

     public static function eliminarSubrolesUsuarios(String $idUsuario = "",array $subRoles = []){
        global $conexion, $baseDatosServicios,$config;               
        try {
            $INsubroles="";
            if(!empty($subRoles)){
                $INsubroles=" AND spu_id_sub_rol IN (".implode(",",$subRoles).")";
            }
            $sql="DELETE FROM ".$baseDatosServicios.".sub_roles_usuarios
            WHERE spu_id_usuario='".$idUsuario.
            "' AND spu_institucion =".$config['conf_id_institucion'].
            $INsubroles;
            mysqli_query($conexion,$sql);              
            
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }
    }

    /**
     * Esta función  Lista sirve para listar los usuarios que tienen un Subrol en especifico
     * por defecto se buscará por el  usuario=1
     * @param String $subrol
     *  */
    public static function listarUsuariosRoles($subrol){
        global $conexion, $baseDatosServicios;
        $arraysDatos = [];
        
        $sqlExecute="SELECT * FROM ".$baseDatosServicios.".sub_roles_usuarios
        WHERE spu_id_sub_rol = '".$subrol."'";
        try {
            $resultadoConsulta = mysqli_query($conexion,$sqlExecute);
            while($fila=$resultadoConsulta->fetch_assoc()){
                $arraysDatos[$fila["spu_id_usuario"]]=$fila;
            } 
            return $arraysDatos;              
            
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }
    }

    /* * Este metodo sirve para guardar las paginas hijas
     * 
     * @param int       $idSubRol
     * @param string    $idPagina
     * 
     * @return void
    **/
    public static function guardarPaginasHijasSubRol($idSubRol,$idPagina){
        global $conexion, $baseDatosServicios;

        try{
            $consultaPaginasHijas=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad WHERE pagp_pagina_padre='".$idPagina."'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $numPaginasHijas=mysqli_num_rows($consultaPaginasHijas);
        if ($numPaginasHijas>0) {
            $datosPaginasHijas = mysqli_fetch_all($consultaPaginasHijas, MYSQLI_ASSOC);
            $arrayPaginasHijas = array_column($datosPaginasHijas, 'pagp_id');

            $sqlinsert="INSERT INTO ".$baseDatosServicios.".sub_roles_paginas(
                spp_id_rol, 
                spp_id_pagina
            )
            VALUES";
            foreach ($arrayPaginasHijas as $page ) {
                $paginaSubrol=self::validarPaginasSubRol($idSubRol,$page,$conexion,$baseDatosServicios);
                if($paginaSubrol){
                    $sqlinsert.="(
                        '".$idSubRol."',
                        '".$page."'                        
                    ),";    
                }
            }
            $sqlCompleta=explode('VALUES',$sqlinsert);
            if(!empty($sqlCompleta[1])){
                $sqlinsert = substr($sqlinsert, 0, -1);
                try{
                    mysqli_query($conexion,$sqlinsert);
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }
            }
        }
    }

    /**
     * Esta función  elimina los registro en la tabla sub_roles_paginas
     *
     * @param int $idSubRol
     * @param array $paginas
     *
     * @return void
    **/
    public static function eliminarRolesPaginas($idSubRol,array $paginas= []){
        global $conexion, $baseDatosServicios;
        try {
            foreach ($paginas as $page ) {
                self::eliminarPaginasHijasSubRol($idSubRol,$page);
                self::eliminarPaginasDependencia($idSubRol,$page,$conexion,$baseDatosServicios);
                try{
                    mysqli_query($conexion,"DELETE FROM ".$baseDatosServicios.".sub_roles_paginas
                    WHERE spp_id_rol='".$idSubRol."' AND spp_id_pagina='".$page."'");
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }
            }    
        }catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
     * Este metodo sirve para eliminar las paginas hijas
     * 
     * @param int       $idSubRol
     * @param string    $idPagina
     * 
     * @return void
    **/
    public static function eliminarPaginasHijasSubRol($idSubRol,$idPagina){
        global $conexion, $baseDatosServicios;

        try{
            $consultaPaginasHijas=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad WHERE pagp_pagina_padre='".$idPagina."'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $numPaginasHijas=mysqli_num_rows($consultaPaginasHijas);
        if ($numPaginasHijas>0) {
            $datosPaginasHijas = mysqli_fetch_all($consultaPaginasHijas, MYSQLI_ASSOC);
            $arrayPaginasHijas = array_column($datosPaginasHijas, 'pagp_id');

            foreach ($arrayPaginasHijas as $page ) {
                try{
                    mysqli_query($conexion,"DELETE FROM ".$baseDatosServicios.".sub_roles_paginas
                    WHERE spp_id_rol='".$idSubRol."' AND spp_id_pagina='".$page."'");
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }          
            }
        }
    }
    /**
    * Esta función asigna los usuarios al rol de forma masiva
    *
    * @param array $idUsuario
    * @param int $subRoles
    *
    * @return int // se retorna el id del registro ingresado    */
    public static function crearRolesUsuarioMasivos(array $usuarios=[],$subRol){
        global $conexion, $baseDatosServicios,$config;

        $sqlinsert="INSERT INTO ".$baseDatosServicios.".sub_roles_usuarios(spu_id_sub_rol, spu_id_usuario, spu_institucion, spu_year) VALUES";

        foreach ($usuarios as $idUsuario ) {
            $sqlinsert.="('".$subRol."', '".$idUsuario."', '".$config['conf_id_institucion']."', '".$config['conf_agno']."'),"; 
        }
        $sqlinsert = substr($sqlinsert, 0, -1);

        try {
                mysqli_query($conexion,$sqlinsert);
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            exit();
        }
        $idRegistro = mysqli_insert_id($conexion);
        return $idRegistro;
    }

    /**
     * Esta función  elimina los registro en la tabla sub_roles_usuarios
     *
     * @param int $idSubRol
     * @param array $usuarios
     *
     * @return void
    **/
    public static function eliminarRolesUsuarioMasivos($idSubRol,array $usuarios= []){
        global $conexion, $baseDatosServicios;
        try {
            foreach ($usuarios as $idUsuario ) {
                try{
                    mysqli_query($conexion,"DELETE FROM ".$baseDatosServicios.".sub_roles_usuarios
                    WHERE spu_id_sub_rol='".$idSubRol."' AND spu_id_usuario='".$idUsuario."'");
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }
            }    
        }catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    /**
    * Esta función nos valida si ya existe una relación entre el rol y el usuarios
    *
    * @param int $idUsuario
    * @param int $subRoles
    *
    */
    public static function validarExistenciaUsuarioRol($idUsuario,$subRol){
        global $conexion, $baseDatosServicios,$config;

        try {
                $existencia=mysqli_query($conexion, "SELECT spu_id FROM ".$baseDatosServicios.".sub_roles_usuarios WHERE spu_id_sub_rol='".$subRol."' AND spu_id_usuario='".$idUsuario."'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            exit();
        }

        $numExistencias = mysqli_num_rows($existencia);
        if($numExistencias>0){
            return true;
        }
        return false;
    }

    /** 
     * Este metodo sirve para guardar las paginas dependencias
     * 
     * @param int       $idSubRol
     * @param string    $idPagina
     * @param mysqli    $conexion
     * @param string    $baseDatosServicios
     * 
     * @return void
    **/
    public static function guardarPaginasDependecias($idSubRol,$idPagina,$conexion,$baseDatosServicios){

        $datosPaginasDependencias=self::paginasDependencia($idPagina,$conexion,$baseDatosServicios);
        $paginasDependencias=!empty($datosPaginasDependencias)?explode(',',$datosPaginasDependencias['pagp_paginas_dependencia']):"";
        if ($paginasDependencias!='') {
            $sqlinsert="INSERT INTO ".$baseDatosServicios.".sub_roles_paginas(
                spp_id_rol, 
                spp_id_pagina
            )
            VALUES";
            foreach ($paginasDependencias as $page ) {
                $paginaSubrol=self::validarPaginasSubRol($idSubRol,$page,$conexion,$baseDatosServicios);
                if($paginaSubrol){
                    $sqlinsert.="(
                        '".$idSubRol."',
                        '".$page."'                        
                    ),";   
                }       
            }
            $sqlCompleta=explode('VALUES',$sqlinsert);
            if(!empty($sqlCompleta[1])){
                $sqlinsert = substr($sqlinsert, 0, -1);
                try{
                    mysqli_query($conexion,$sqlinsert);
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }
            }
        }
    }

    /** 
     * Este metodo valida si ya existe la pagina para ese rol
     * 
     * @param int       $idSubRol
     * @param string    $idPagina
     * @param mysqli    $conexion
     * @param string    $baseDatosServicios
     * 
     * @return bool
    **/
    public static function validarPaginasSubRol($idSubRol,$idPagina,$conexion,$baseDatosServicios){

        try{
            $consultaPaginasHijas=mysqli_query($conexion, "SELECT spp_id FROM ".$baseDatosServicios.".sub_roles_paginas WHERE spp_id_rol='".$idSubRol."' AND spp_id_pagina='".$idPagina."'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $numPaginasDependencias = mysqli_num_rows($consultaPaginasHijas);
        if ($numPaginasDependencias>0) {
            return false;
        }
        return true;
    }

    /**
     * Este metodo me trae las paginas de dependencia
     * 
     * @param string    $idPagina
     * @param mysqli    $conexion
     * @param string    $baseDatosServicios
     * 
     * @return array
    **/
    public static function paginasDependencia($idPagina,$conexion,$baseDatosServicios){
        $resultado = [];

        try{
            $consultaPaginasHijas=mysqli_query($conexion, "SELECT pagp_paginas_dependencia FROM ".$baseDatosServicios.".paginas_publicidad WHERE pagp_id='".$idPagina."'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        $datosPaginasDependencias = mysqli_fetch_array($consultaPaginasHijas, MYSQLI_BOTH);
        if (!empty($datosPaginasDependencias['pagp_paginas_dependencia'])) {
            $resultado = $datosPaginasDependencias;
        }
        return $resultado;
    }

    /**
     * Este metodo sirve para eliminar las paginas dependencia
     * 
     * @param int       $idSubRol
     * @param string    $idPagina
     * @param mysqli    $conexion
     * @param string    $baseDatosServicios
     * 
     * @return void
    **/
    public static function eliminarPaginasDependencia($idSubRol,$idPagina,$conexion,$baseDatosServicios){

        $datosPaginasDependencias=self::paginasDependencia($idPagina,$conexion,$baseDatosServicios);
        $paginasDependencias=!empty($datosPaginasDependencias)?explode(',',$datosPaginasDependencias['pagp_paginas_dependencia']):"";
        if ($paginasDependencias!='') {
            foreach ($paginasDependencias as $page ) {
                self::eliminarPaginasHijasSubRol($idSubRol,$page);
                try{
                    mysqli_query($conexion,"DELETE FROM ".$baseDatosServicios.".sub_roles_paginas
                    WHERE spp_id_rol='".$idSubRol."' AND spp_id_pagina='".$page."'");
                } catch (Exception $e) {
                    include("../compartido/error-catch-to-report.php");
                }          
            }
        }
    }
}