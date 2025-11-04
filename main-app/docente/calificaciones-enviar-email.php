<?php
if($datosUnicosInstitucion['ins_notificaciones_acudientes']==1){
    if($datosRelacionados["mat_notificacion1"]==1){

        // Migrado a PDO - Consulta preparada
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "INSERT INTO ".$baseDatosServicios.".correos(
            corr_institucion, corr_carga, corr_actividad, corr_nota, corr_tipo, 
            corr_fecha_registro, corr_estado, corr_usuario, corr_estudiante
        ) VALUES (?, ?, ?, ?, 1, now(), 0, ?, ?)";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(2, $datosRelacionados["car_id"], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST["codNota"], PDO::PARAM_STR);
        $stmt->bindParam(4, $_POST["nota"], PDO::PARAM_STR);
        $stmt->bindParam(5, $datosRelacionados["uss_id"], PDO::PARAM_STR);
        $stmt->bindParam(6, $_POST["codEst"], PDO::PARAM_STR);
        $stmt->execute();
        

        //INICIO ENVÍO DE MENSAJE
        $tituloMsj = "¡REGISTRO DE NOTA PARA <b>".strtoupper($datosRelacionados["mat_nombres"])."</b>!";
        $bgTitulo = "#4086f4";
        $contenidoMsj = '
            <p>
                Hola <b>'.strtoupper($datosRelacionados["uss_nombre"]).'</b>, te informamos que fue registrada una nueva nota para el estudiante <b>'.strtoupper($datosRelacionados["mat_nombres"]).'</b>!<br>
                Estos son los datos relacionados:<br>
                <b>ESTUDIANTE:</b> '.strtoupper($datosRelacionados["mat_primer_apellido"]." ".$datosRelacionados["mat_segundo_apellido"]." ".$datosRelacionados["mat_nombres"]).'<br>
                <b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
                <b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
                <b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
                <b>PERIODO:</b> '.$datosRelacionados["act_periodo"].'<br>
                <b>ACTIVIDAD:</b> '.strtoupper($datosRelacionados["act_descripcion"]).' ('.$datosRelacionados["act_valor"].'%)<br>
                <b>NOTA:</b> '.$_POST["nota"].'<br>
            </p>';

        if($datosRelacionados["mat_notificacion1"]==1){
            $contenidoMsj .= '
                <p>
                    <h3 style="color:navy; text-align: center;"><b>ACUDIENTE PREMIUM SINTIA</b></h3>
                    Usted está recibiendo esta notificación porque hace parte del grupo de los <b>ACUDIENTES PREMIUM SINTIA</b>.<br>
                    Gracias por haber adquirido el servicio de notificaciones por correo.
                </p>
            ';	
        }
        else{	
            $contenidoMsj .= '
                <p>
                    <h3 style="color:navy; text-align: center;"><b>MUY IMPORTANTE</b></h3>
                    Este servicio de <b>notifiaciones por correo</b> lo hemos otorgado gratuitamente durante el mes de <b>SEPTIEMBRE DE 2019</b> para que usted vea sus beneficios.<br>
                    Si desea adquirir este servicio de forma permanente durante todo el resto de este año 2019 y todo el año 2020, aproveche el <b>65% DE DESCUENTO</b> que hay ahora, y adquieralo por la módica suma de <b>$21.000</b>.<br>
                    Recuerde que es por todo el resto de este año y todo el año siguiente.<br>
                    <b>A PARTIR DE MAÑANA YA VALDRÁ $60.000.</b>
                    <h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ÚLTIMO DÍA, AÚN ESTÁS A TIEMPO</b></h1>
                </p>


                <h2 style="color:#eb4132; text-align: center;"><b>AHORRA $39.000</b></h2>
                <p style="text-align: center;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1001&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank"><img src="https://plataformasintia.com/files-general/email/ultimosdias.jpg"></a></p>



                <p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1000&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>¡ADQUIRIR SERVICIO AHORA!</b></a></p>

                <p>
                Ó para su <b>mayor facilidad</b> puede hacer una transferencia, sin costo adicional, a nuestra cuenta:<br>
                <img src="https://plataformasintia.com/files-general/iconos/bacolombia.png" width="40" align="middle"> Ahorros Bancolombia Número: <b>431-565882-54</b>.<br>
                <img src="https://plataformasintia.com/files-general/iconos/colpatria.png" width="40" align="middle"> Ahorros Colpatria Número: <b>789-20112-53</b>.<br>
                Si desea puede escribirnos al <b>WhatsApp: 313 752 5894</b> para brindarle mayor información.
                </p>

                <p>Para activar su servicio de inmediato, recuerde enviar el soporte de pago, o el pantallazo(si hace su pago en línea), al correo electrónico <b>pagos@plataformasintia.com</b>. o al <b>WhatsApp: 313 752 5894</b></p>


                <p>
                    <h3 style="color:navy; text-align: center;"><b>¿QUÉ NOTIFICACIONES RECIBIRÁS?</b></h3>
                    1. Registro de notas.<br>
                    2. Modificación de notas.<br>
                    3. Registro de recuperaciones.<br>
                    4. Registro de nivelaciones de fin de año.<br>
                    5. Reportes disciplinarios<br>
                    6. Cobros realizados por la insitución.<br>
                    7. CUANDO EL DOCENTE TERMINA PERIODO, CÓMO LE QUEDÓ LA DEFINITIVA.<br>
                    8. Entre otras notificaciones importanes.
                </p>

                <h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ÚLTIMO DÍA, AÚN ESTÁS A TIEMPO</b></h1>
                <h2 style="color:#eb4132; text-align: center;"><b>AHORRA $39.000</b></h2>
                <p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1002&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>¡ADQUIRIR SERVICIO AHORA!</b></a></p>

                <hr>
                <p>
                    <h3 style="text-align: center;"><b>PREGUNTAS FRECUENTES</b></h3>
                    <b>1. ¿Por qué tiene un costo este servicio?</b><br>
                    <b>R/.</b> Este servicio lo presta una entidad externa a la Institución y el envío de email masivo, como lo es en este caso, tiene un costo adicional para poder cubrir el servidor que se encarga de realizar este envío de correos.<br><br>

                    <b>2. ¿Si retiro a mi(s) acudido(s) de la Institución debo seguir pagando este valor?</b><br>
                    <b>R/.</b> Definitivamente NO. Usted solo paga mientras lo desee y mientras le sea útil este servicio.<br><br>

                    <b>3. ¿Si tengo algún problema con este servicio a quién debo contactar?</b><br>
                    <b>R/.</b> Se puede contactar directamente con nosotros al correo <b>soporte@plataformasintia.com</b> o al número de <b>WhatsApp: 313 752 5894.</b><br><br>

                    <b>4. ¿Si no quiero el servicio de notificación por correo no podré acceder, yo o mis acudidos, a la plataforma?</b><br>
                    <b>R/.</b> Usted como acudiente y sus acudidos siempre tendrán acceso a la plataforma por el hecho de estar matriculados en la Institución. El servicio de notificaciones por correo electrónico es diferente.<br><br>

                    <b>5. ¿El pago se puede hacer en la Institución?</b><br>
                    <b>R/.</b> Por ser un servicio directo con los proveedores de la plataforma educativa, el pago del servicio sólo se acepta a través de los siguientes métodos y entidades: pago electrónico (PSE) con tarjeta débito o crédito, GANA, EFECTY, BALOTO, Trasnferencia directa a nuestra cuenta Bancolombia o Colpatria.<br><br>

                    <b>6. ¿El valor del servicio cubre todos los acudidos que tenga o es por cada uno?</b><br>
                    <b>R/.</b> El valor del servicio es por cada uno de los acudidos de los cuales usted quiera recibir las notificaciones al correo electrónico.<br><br>
                </p>

                <h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ÚLTIMO DÍA, AÚN ESTÁS A TIEMPO</b></h1>
                <p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1003&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>¡ADQUIRIR SERVICIO AHORA!</b></a></p>

                <hr>
                <p style="font-size:8px;">
                    <h6 style="text-align: center;"><b>TÉRMINOS Y CONDICIONES</b></h6>
                    <b>1.</b> Para recibir las notificaciones relacionadas con las notas debe estar paz y salvo con la Institución.<br>
                    <b>2.</b> El valor del servicio es por cada uno de los acudidos de los cuales usted quiera recibir la notificación electrónica.<br>
                </p>

                <h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ÚLTIMO DÍA, AÚN ESTÁS A TIEMPO</b></h1>
                <p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1004&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>¡ADQUIRIR SERVICIO AHORA!</b></a></p>

            ';
        }

    }
}