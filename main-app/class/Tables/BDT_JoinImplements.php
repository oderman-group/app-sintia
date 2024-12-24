<?php

interface BDT_JoinImplements {
    public static function getForeignKey();

    public static function foreignKey($tipoJoin,Array $foringKey=[]);
}