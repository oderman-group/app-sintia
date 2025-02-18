<?php
require_once(ROOT_PATH."/main-app/class/App/Mensajes_Informativos/Mensajes_Informativos.php");
if(isset($_GET['error']) || isset($_GET['success'])){
    /* MENSAJES DE ERROR O INFORMATIVOS */
    if(isset($_GET['error'])){
        switch($_GET['error']){
            case 1:
                $tipo = 'danger';
                $mensaje = 'El usuario no fue encontrado para esta institución en este año. Por favor verifique.';
            break;

            case 2:
                $tipo = 'danger';
                $mensaje = 'La contraseña ingresada es incorrecta. Por favor verifique.';
            break;

            case 3:
                $tipo = 'primary';
                $mensaje = 'Ha superado el número máximo de intentos fallidos.<br>
                            Restablezca su contraseña dando click abajo en "<b>¿Has olvidado tu contraseña?</b>"<br> o comuníquese con la administración de su institución para que le generen una nueva.';
            break;

            case 4:
                $tipo = 'primary';
                $mensaje = 'No se encontró una sesión de usuario activa. Ingrese al sistema nuevamente.';
            break;

            case 5:
                $tipo = 'danger';
                $mensaje = 'La clave no cumple con todos los requerimientos:<br>
                            - Debe tener mínimo 8 caracteres.<br>
                            - Solo se admiten caracteres de la a-z, A-Z, números(0-9) y los siguientes simbolos(. y $).';
            break;

            case Mensajes_Informativos::USUARIO_BLOQUEADO:
                $tipo = 'danger';
                $mensaje = '
                    Su usuario se encuentra bloqueado por el siguiente motivo:<br>
                    <b>' . $motivo . '.</b><br>';
                    if (!empty($telefono)) {
                        $mensaje .= '
                            Contacte con su institución:<br>
                            <b>Telefono:</b> ' . $telefono . '.<br>
                        ';
                    }
                $mensaje .= 'Envíe una solicitud de desbloqueo dando click <b><a class="forgot-password" id="forgot-password" href="solicitud-desbloqueo.php?inst=' . $_GET['inst'] . '&idU=' . $_GET['idU'] . '">AQUÍ</a></b>';
            break;

            case 7:
                $tipo = 'danger';
                $mensaje = 'Verifica que hayas seleccionado la institución y el año correctamente.';
            break;

            case 8:
                $tipo = 'danger';
                $mensaje = '
                No se pudo establecer una conexión. Revise su red.<br>
                <a href="index.php" class="btn btn-primary">Intente nuevamente</a>
                ';
            break;

            case 9:
                $tipo = 'danger';
                $mensaje = 'Su institución se encuentra desactivada. Por favor comúniquese con la administración de plataforma SINTIA.';
            break;

            case 10:
                $tipo = 'secondary';
                $mensaje = 'Error genérico: '.$_GET['genericError'];
            break;

            case 11:
                $tipo = 'info';
                $mensaje = 'No se encontraron registros para mostrar en la pagina donde intentaste acceder.';
            break;


            case 'ER_DT_1':
                $tipo = 'danger';
                $mensaje = 'Este usuario(<b>' . $_GET["usuario"] . '</b>) ya existe para otra persona. Cambie el nombre de usuario por favor.';
            break;

            case 'ER_DT_2':
                $tipo = 'danger';
                $mensaje = 'No tienes permiso para editar a este usuario: <b>' . base64_decode($_GET["usuario"]) . '</b>';
            break;

            case 'ER_DT_3':
                $tipo = 'success';
                $mensaje = 'El registro se eliminó exitosamente.';
            break;

            case 'ER_DT_4':
                $tipo = 'warning';
                $mensaje = 'Debe llenar todos los campos.';
            break;

            case 'ER_DT_5':
                $tipo = 'warning';
                $mensaje = 'Este estudiante ya se ecuentra creado.';
            break;

            case 'ER_DT_6':
                $tipo = 'warning';
                $mensaje = 'El acudiente no existe, por tanto debe llenar todos los campos para registrarlo.';
            break;

            case 'ER_DT_7':
                $tipo = 'danger';
                $mensaje = base64_decode($_GET['msj']);
            break;

            case 'ER_DT_8':
                $tipo = 'danger';
                $mensaje = 'El archivo enviado es invalido. Por favor vuelva a intentarlo.';
            break;

            case 'ER_DT_9':
                $tipo = 'danger';
                $mensaje = 'El estudiante o curso seleccionado no existe.';
            break;

            case 'ER_DT_10':
                $tipo = 'danger';
                $mensaje = 'Lo sentimos este curso no tiene grado siguiente, por favor asígnele uno desde la opción de editar en cursos.';
            break;

            case 'ER_DT_11':
                $tipo = 'danger';
                $mensaje = 'Este documento(<b>' . base64_decode($_GET["documento"]) . '</b>) ya existe para otra persona. Cambie el numero de documento por favor.';
            break;

            case 'ER_DT_12':
                $tipo = 'danger';
                $mensaje = 'La clave actual no es correcta. Por favor verifique.';
            break;

            case 'ER_DT_13':
                $tipo = 'danger';
                $mensaje = 'La clave nueva no coincide. Por favor verifique.';
            break;

            case 'ER_DT_14':
                $tipo = 'danger';
                $mensaje = 'Estos datos ya se encuentran registrados y asociados a la pagina <b>' . base64_decode($_GET["nombrePagina"]) . '</b>.<br>
                ¿Desea mostrar toda la información de la pagina?<br>
                <a href="dev-paginas-editar.php?idP=' . base64_decode($_GET["id"]) . '" id="addRow" class="btn deepPink-bgcolor">
                    Sí, deseo mostrar la información
                </a>';
            break;
            case 'ER_DT_15':
                $tipo = 'danger';
                $mensaje = base64_decode($_GET["msj"]);
            break;

            case 'ER_DT_16':
                $tipo = 'warning';
                $mensaje = 'No se encontró ninguna coincidencia o usted no tiene permisos para ver este registro.';
            break;

            case 'ER_DT_17':
                $tipo = 'danger';
                $mensaje = "Este archivo pesa <b>{$_GET['pesoMB']}MB</b>. 
                Lo ideal es que pese menos de {$config['conf_max_peso_archivos']}MB. 
                Intente comprimirlo o busque reducir su peso.";
            break;

            case 'ER_DT_18':
                $tipo = 'danger';
                $mensaje = 'Lo sentimos, todavía no se ha activado el año <b>'.base64_decode($_GET['yearPasar']).'</b> para su institución, por favor, ponte en contacto con la administración de la plataforma SINTIA.';
            break;

            case 'ER_DT_19':
                $tipo = 'danger';
                $mensaje = 'Este estudiante ya existe en el año <b>'.base64_decode($_GET['yearPasar']).'</b>.';
            break;

            case 'ER_DT_20':
                $tipo = 'danger';
                $mensaje = 'Ya existe una carga con esta misma información, verifica la información suministrada y vuelve a intentarlo.';
            break;

            case 'ER_DT_21':
                $tipo = 'warning';
                $mensaje = 'La encuesta escogida ya llego al limite y no puede ser respondida, porfavor escoja otra.';
            break;

            default:
                $tipo = 'secondary';
                $mensaje = 'Error desconocido: '.$_GET['error'];
            break;
        }
    }    
    
    /* MENSAJES DE EXITO */
    else if(isset($_GET['success'])){       
        switch($_GET['success']){
            case 'SC_DT_1':
                $parametrosObligatorios =["id"];
                Utilidades::validarParametros($_GET,$parametrosObligatorios);
                $tipo = 'success';
                $mensaje = 'El registro fue creado correctamente con el ID único: <b>' . base64_decode($_GET["id"]) . '</b>';
            break;

            case 'SC_DT_2':
                $parametrosObligatorios =["id"];
                Utilidades::validarParametros($_GET,$parametrosObligatorios);
                $tipo = 'success';
                $mensaje = 'El registro fue actualizado correctamente para el ID único: <b>' . base64_decode($_GET["id"]) . '</b>';
            break;

            case 'SC_DT_3': 
                $parametrosObligatorios =["id"];
                Utilidades::validarParametros($_GET,$parametrosObligatorios);
                $tipo = 'success';
                $mensaje = 'El registro fue eliminado correctamente para el ID único: <b>' . base64_decode($_GET["id"]) . '</b>';
            break;

            case 'SC_DT_4':
                $tipo = 'primary';
                $mensaje = base64_decode($_GET["summary"]);
            break;

            case Mensajes_Informativos::CONTRASENA_GENERADA:
                $tipo = 'success';
                $mensaje = 'Una nueva contraseña fue generada y enviada a tu correo electrónico: <b>' . base64_decode($_GET["email"]) . '</b>';
            break;

            case 'SC_DT_6':
                $tipo = 'success';
                $mensaje = '
                Fueron creadas <b>' . base64_decode($_GET["creadas"]) . '</b> cargas académicas nuevas.
                ';
                if(base64_decode($_GET["noCreadas"]) > 0) {
                    $mensaje .= '
                    <br>No se pudieron crear <b>' . base64_decode($_GET["noCreadas"]) . '</b> cargas académicas porque ya existía ese registro en el sistema. Por favor verifique.
                    ';
                }
            break;

            case 'SC_DT_7':
                $tipo = 'success';
                $mensaje = 'Del curso <b>'.base64_decode($_GET["curso"]).'</b> se promovieron <b>'.base64_decode($_GET["numEstudiantesPromocionados"]).'</b> estudiantes al curso <b>'.base64_decode($_GET["siguiente"]).'</b> correctamente.';
            break;

            case 'SC_DT_8':
                $tipo = 'success';
                $mensaje = 'La contraseña se genero correctamente para los usuarios escogidos.</b>';
            break;

            case 'SC_DT_9':
                $tipo = 'success';
                $mensaje = '
                Todos los estudiantes que NO estaban en estado <b>Matriculado</b> fueron removidos de la plataforma. TOTAL: <b>'.base64_decode($_GET['numRegistros']).'</b><br>
                Ahora la plataforma está más limpia y puedes trabajar con los estudiantes que necesitas para este año.</b>
                ';
            break;

            case 'SC_DT_10':
                $tipo = 'success';
                $mensaje = '
                El proceso de creación de una compañía nueva concluyó exitosamente.
                ';
            break;

            case 'SC_DT_11':
                $tipo = 'success';
                $mensaje = '
                La contraseña se cambió correctamente.
                ';
            break;

            case 'SC_DT_12':
                $tipo = 'success';
                $mensaje = '
                Los folios fueron generados correctamente.
                ';
            break;

            case base64_encode('SC_DT_13'):
                $tipo = 'success';
                $mensaje = 'Las cargas fueron transferidas exitosamente.';
            break;

            case 'SC_DT_14':
                $tipo = 'success';
                $mensaje = 'El estudiante fue movido al año <b>'.base64_decode($_GET['yearPasar']).'</b> exitosamente.';
            break;

            case 'SC_GN_1':
                $tipo = 'success';
                $mensaje = 'La evaluación fue creada correctamente. El siguiente paso es crear las preguntas o utilizar algunas existentes del banco de datos. <b>Empieza ahora!</b>';
            break;

            case 'SC_GN_2':
                $tipo = 'success';
                $mensaje = 'La información fue importada correctamente';
            break;

            case 'SC_GN_3':
                $tipo = 'success';
                $mensaje = 'La tematica fue registrada correctamente';
            break;

            case 'SC_GN_4':
                $tipo = 'success';
                $mensaje = 'El plan de clase fue registrado correctamente';
            break;
            
            case 'SC_GN_5':
                $tipo = 'success';
                base64_decode($_GET["estado"])==1 ? $mensaje = 'La respuesta cambio de estado a correcta' : $mensaje = 'La respuesta cambio de estado a incorrecta';
            break;

            case 'SC_GN_6':
                $tipo = 'success';
                $mensaje = 'Se te envío un correo con el nuevo usuarios de acceso.';
            break;

            case Mensajes_Informativos::SOLICITUD_DESBLOQUEO:
                $tipo = 'success';
                $mensaje = 'Hemos enviado su solicitud de desbloqueo, pronto un directivo se pondrá en contacto con usted.';
            break;

            default:
                $tipo = 'secondary';
                $mensaje = 'Error desconocido: '.$_GET['error'];
            break;
        }
    }
?>
    
    <div class="alert alert-block alert-<?=$tipo;?> animate__animated animate__flash animate__delay-1s animate__repeat-2">
        <p><?=$mensaje;?></p>
    </div>

<?php    
}
?>