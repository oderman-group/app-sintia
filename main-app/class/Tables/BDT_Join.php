<?php
trait  BDT_Join {
    private static $foringKey = [];
    private static $typeJoin ;
    

    public  static function foreignKey($typeJoin,$foringKey=[]) {   
        self::$typeJoin = $typeJoin;    
        self::$foringKey = $foringKey;
    }
    public static  function getForeignKey() {       
        return self::$foringKey ;
    }

    public static  function getTypeJoin() {       
        return self::$typeJoin ;
    }

}