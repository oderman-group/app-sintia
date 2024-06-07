<?php
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/app-sintia');
include(ROOT_PATH."/sensitive.php");

define('EMAIL_SENDER', 'info@plataformasintia.com');
define('NAME_SENDER', 'Plataforma Sintia');

define('HEADER_EMAIL_BACKGROUND', '#6017dc');
define('GRADO_INDIVIDUAL', 'individual');
define('GRADO_GRUPAL', 'grupal');


define('JOBS_ESTADO_PENDIENTE', 'Pendiente');
define('JOBS_ESTADO_PROCESO', 'Proceso');
define('JOBS_ESTADO_FINALIZADO', 'Finalizado');
define('JOBS_ESTADO_ERROR', 'Error');

define('JOBS_TIPO_IMPORTAR_ESTUDIANTES_EXCEL', 'importar_estudiantes');
define('JOBS_TIPO_GENERAR_INFORMES', 'generar_informes');

define('JOBS_PRIORIDAD_ALTA', '1');
define('JOBS_PRIORIDAD_MEDIA', '2');
define('JOBS_PRIORIDAD_BAJA', '3');

define('MENU', 'menu');
define('MENU_PADRE', 'menu-padre');
define('SUB_MENU', 'sub-menu');

define('ESTADO_EMAIL_ENVIADO', 'enviado');
define('ESTADO_EMAIL_ERROR', 'error');


define('SOLICITUD_CANCELACION_PENDIENTE', 'Pendiente');
define('SOLICITUD_CANCELACION_APROBADO', 'Aprobado');
define('SOLICITUD_CANCELACION_CANCELADO', 'Cancelado');


define('CHAT_TIPO_MENSAJE', 1);
define('CHAT_TIPO_IMAGEN', 2);
define('CHAT_TIPO_DOCUMENTO', 3);
define('CHAT_TIPO_AUDIO', 4);


/* Nombres de carpetas  */
define('FILE_CURSOS', "cursos/");
define('FILE_PUBLICACIONES', "publicaciones/");
define('FILE_TAREAS', "tareas/");
define('FILE_VIDEO_CLASES', "video-clases/");
define('FILE_TAREAS_ENTREGADAS', "tareas-entregadas/");


/* TIPOS DE USUARIO DE LA PLATAFORMA */
define('TIPO_DEV', 1);
define('TIPO_DOCENTE', 2);
define('TIPO_ACUDIENTE', 3);
define('TIPO_ESTUDIANTE', 4);
define('TIPO_DIRECTIVO', 5);
define('TIPO_CLIENTE', 7);
define('TIPO_PROVEEDOR', 8);

/* CLIENTES CON CAMBIOS PARTICULARES*/
define('ICOLVEN', 1);
define('DEVELOPER', 22);
define('DEVELOPER_PROD', 30);
define('ELLEN_KEY', 16);

define('PORCENTAJE_MINIMO_GENERAR_INFORME', 99);

/* CONSTANTES ACADEMICAS */
define('CONFIG_MANUAL_INDICADOR', 1);
define('CONFIG_AUTOMATICO_INDICADOR', 0);
define('CONFIG_MANUAL_CALIFICACIONES', 1);
define('CONFIG_AUTOMATICO_CALIFICACIONES', 0);
define('PERMISO_EDICION_PERIODOS_DIFERENTES', true); //Diferentes al actual

/* FORMAS DE VER NOTAS */
define('CUALITATIVA', 'CUALITATIVA');
define('CUANTITATIVA', 'CUANTITATIVA');

define('CLAVE_SUGERIDA', 'sherman1298');

/* SINTIA PLATFORM MODULES */
define('MODULO_ACADEMICO', 1);
define('MODULO_FINANCIERO', 2);
define('MODULO_DISCIPLINARIO', 3);
define('MODULO_ADMINISTRATIVO', 4);
define('MODULO_COMUNICATIVO', 5);
define('MODULO_MERCADEO', 6);
define('MODULO_GENERAL', 7);
define('MODULO_ADMISIONES', 8);
define('MODULO_RESERVA_CUPO', 9);
define('MODULO_MEDIA_TECNICA', 10);

/* TIPO DE VALIDACION*/
define('IDENTIFICAION', 'Identificacion');
define('USUARIO', 'Usuario');
define('CORREO', 'Correo');

/* TIPO DE ACCIONES*/
define('ACCION_CREAR', 'Crear');
define('ACCION_MODIFICAR', 'Modificar');
define('ACCION_ELIMINAR', 'Eliminar');

/* TIPOS DE FACTURA o COTIZACIÓn */
define('TIPO_FACTURA', 'INVOICE');
define('TIPO_COTIZACION', 'QUOTE');
define('TIPO_RECURRING', 'INVOICE_RECURRING');

/* TIPO DE FACTURA*/
define('FACTURA_VENTA', 1);
define('FACTURA_COMPRA', 2);


/* TIPOS DE PREGUNTAS */
define('TEXT', 'TEXT');
define('MULTIPLE', 'MULTIPLE');
define('SINGLE', 'SINGLE');

/* TIPOS DE ESTADO DE MATRICULA MEDIATECNICA */
define('ESTADO_CURSO_ACTIVO', 'ACTIVO');
define('ESTADO_CURSO_INACTIVO', 'INACTIVO');
define('ESTADO_CURSO_PRE_INSCRITO', 'PRE INSCRITO');
define('ESTADO_CURSO_APROBADO', 'APROBADO');
define('ESTADO_CURSO_NO_APROBADO', 'NO APROBADO');

define('COBRADA', 'COBRADA');
define('POR_COBRAR', 'POR_COBRAR');

define('SI', 'SI');
define('NO', 'NO');

/* ESTADO PASARLEA DE PAGO */
define('TRANSACCION_ACEPTADA', 'Aceptada');
define('TRANSACCION_PENDIENTE', 'Pendiente');
define('TRANSACCION_FALLIDA', 'Fallida');
define('TRANSACCION_RECHAZADA', 'Rechazada');

define('INVOICE', 'INVOICE');
define('ACCOUNT', 'ACCOUNT');

/* Tipos de impuestos */
define('IVA', 'IVA');
define('ICO', 'ICO');
define('ICUI', 'ICUI');
define('OTRO', 'OTRO');

/* ESTADOS DE MATRICULA */
define('MATRICULADO', 1);
define('ASISTENTE', 2);
define('CANCELADO', 3);
define('NO_MATRICULADO', 4);
define('EN_INSCRIPCION', 5);

/* NIVELES EDUCATIVOS */
define('PREESCOLAR', 1);
define('BASICA_PRIMARIA', 2);
define('BASICA_SECUNDARIA', 3);
define('MEDIA', 4);

define('ACTIVO', 'ACTIVO');
define('INACTIVO', 'INACTIVO');

/* TIPOS DE ENCUESTA */
define('DOCENTE', 'DOCENTE');
define('ACUDIENTE', 'ACUDIENTE');
define('ESTUDIANTE', 'ESTUDIANTE');
define('DIRECTIVO', 'DIRECTIVO');
define('AREA', 'AREA');
define('MATERIA', 'MATERIA');
define('CURSO', 'CURSO');

/* METODOS PARA OPEN AI*/
define('TEXT_TO_IMAGE', 'TEXT_TO_IMAGE');
define('TEXT_TO_TEXT', 'TEXT_TO_TEXT');

define('PENDIENTE', 'PENDIENTE');
define('PROCESO', 'PROCESO');
define('FINALIZADO', 'FINALIZADO');

/* TIPOS DE COMPAÑIA */
define('SCHOOL', 'SCHOOL');
define('INSTITUTE', 'INSTITUTE');
define('UNIVERSITY', 'UNIVERSITY');
define('COMPANY', 'COMPANY');
define('KINDERGARTEN', 'KINDERGARTEN');
define('PEOPLE', 'PEOPLE');

/** TIPOS DE PAQUETES **/
define('PLANES', 'PLANES');
define('ESPACIO', 'ESPACIO');
define('USUARIOS', 'USUARIOS');
define('MODULOS', 'MODULOS');
define('PAQUETES', 'PAQUETES');

switch($_SERVER['HTTP_HOST']){
	case 'localhost':
        define('REDIRECT_ROUTE', 'http://localhost/app-sintia/main-app');
        define('ENVIROMENT', 'TEST');
        break;

	case 'developer.plataformasintia.com':
        define('REDIRECT_ROUTE', 'https://developer.plataformasintia.com/app-sintia/main-app');
        define('ENVIROMENT', 'TEST');
        break;

	case 'main.plataformasintia.com':
        define('REDIRECT_ROUTE', 'https://main.plataformasintia.com/app-sintia/main-app');
        define('ENVIROMENT', 'PROD');
        error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
        break;

        case 'copyprod.plataformasintia.com':
        define('REDIRECT_ROUTE', 'https://copyprod.plataformasintia.com/app-sintia/main-app');
        define('ENVIROMENT', 'PROD');
        error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
        break;

        default:
        define('REDIRECT_ROUTE', 'https://'.$_SERVER['HTTP_HOST'].'/app-sintia/main-app');
        define('ENVIROMENT', 'TEST');
        error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
        break;
}

switch (ENVIROMENT) {
        case 'LOCAL':
        include(ROOT_PATH."/conexion-datos.php");
        define('BD_PREFIX', 'odermangroup_');
        define('EPAYCO_TEST', 'true');
        break;

        case 'TEST':
        include(ROOT_PATH."/conexion-datos-developer.php");
        define('BD_PREFIX', 'mobiliar_');
        define('EPAYCO_TEST', 'true');
	break;

        case 'PROD':
        include(ROOT_PATH."/conexion-datos-production.php");
        define('BD_PREFIX', 'mobiliar_');
        define('EPAYCO_TEST', 'false');
        break;

        default:
        include(ROOT_PATH."/conexion-datos.php");
        define('BD_PREFIX', 'odermangroup_');
        break;
}