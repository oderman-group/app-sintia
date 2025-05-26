<?php
$datosMatricula = [
	'tipoD'     => '',
	'documento'      => '',
	'religion'       => '',
	'email'    => NULL,
	'direcion'      => '',
	'barrio' => '',
	'telefono'      => '',
	'celular'       => '',
	'estrato'    => '',
	'genero'      => '',
	'nacimiento' => '',
	'apellido1'      => '',
	'apellido2'       => '',
	'nombre'    => '',
	'grado'      => '',
	'grupo' => '',
	'tipoE'      => 128,
	'lugarEx'       => '',
	'lugarNac'    => '',
	'matricula'      => '',
	'folio' => '',
	'tesoreria'      => '',
	'vaMatricula'       => '0',
	'inclusion'    => 0,
	'extran'      => 0,
	'tipoSangre' => '',
	'eps'      => '',
	'celular2'       => '',
	'ciudadR'    => '',
	'nombre2'      => '',
	'documentoA' => '',
	'nombreA'      => '',
	'ocupacionA'       => '',
	'generoA'    => '',
	'expedicionA'      => '',
	'tipoDocA' => '',
	'apellido1A'      => '',
	'apellido2A'       => '',
	'nombre2A'    => '',
	'matestM'    => 4
];
if(isset($_GET['tipoD'])){
	$datosMatricula['tipoD'] = base64_decode($_GET['tipoD']);
}
if(isset($_GET['documento'])){
	$datosMatricula['documento'] = base64_decode($_GET['documento']);
}
if(isset($_GET['religion'])){
	$datosMatricula['religion'] = base64_decode($_GET['religion']);
}
if(isset($_GET['email'])){
	$datosMatricula['email'] = base64_decode($_GET['email']);
}
if(isset($_GET['direcion'])){
	$datosMatricula['direcion'] = base64_decode($_GET['direcion']);
}
if(isset($_GET['barrio'])){
	$datosMatricula['barrio'] = base64_decode($_GET['barrio']);
}
if(isset($_GET['telefono'])){
	$datosMatricula['telefono'] = base64_decode($_GET['telefono']);
}
if(isset($_GET['celular'])){
	$datosMatricula['celular'] = base64_decode($_GET['celular']);
}
if(isset($_GET['estrato'])){
	$datosMatricula['estrato'] = base64_decode($_GET['estrato']);
}
if(isset($_GET['genero'])){
	$datosMatricula['genero'] = base64_decode($_GET['genero']);
}
if(isset($_GET['nacimiento'])){
	$datosMatricula['nacimiento'] = base64_decode($_GET['nacimiento']);
}
if(isset($_GET['apellido1'])){
	$datosMatricula['apellido1'] = base64_decode($_GET['apellido1']);
}
if(isset($_GET['apellido2'])){
	$datosMatricula['apellido2'] = base64_decode($_GET['apellido2']);
}
if(isset($_GET['nombre'])){
	$datosMatricula['nombre'] = base64_decode($_GET['nombre']);
}
if(isset($_GET['grado'])){
	$datosMatricula['grado'] = base64_decode($_GET['grado']);
}
if(isset($_GET['grupo'])){
	$datosMatricula['grupo'] = base64_decode($_GET['grupo']);
}
if(isset($_GET['tipoE'])){
	$datosMatricula['tipoE'] = base64_decode($_GET['tipoE']);
}
if(isset($_GET['lugarEx'])){
	$datosMatricula['lugarEx'] = base64_decode($_GET['lugarEx']);
}
if(isset($_GET['lugarNac'])){
	$datosMatricula['lugarNac'] = base64_decode($_GET['lugarNac']);
}
if(isset($_GET['matricula'])){
	$datosMatricula['matricula'] = base64_decode($_GET['matricula']);
}
if(isset($_GET['folio'])){
	$datosMatricula['folio'] = base64_decode($_GET['folio']);
}
if(isset($_GET['tesoreria'])){
	$datosMatricula['tesoreria'] = base64_decode($_GET['tesoreria']);
}
if(isset($_GET['vaMatricula'])){
	$datosMatricula['vaMatricula'] = base64_decode($_GET['vaMatricula']);
}
if(isset($_GET['inclusion'])){
	$datosMatricula['inclusion'] = base64_decode($_GET['inclusion']);
}
if(isset($_GET['extran'])){
	$datosMatricula['extran'] = base64_decode($_GET['extran']);
}
if(isset($_GET['tipoSangre'])){
	$datosMatricula['tipoSangre'] = base64_decode($_GET['tipoSangre']);
}
if(isset($_GET['eps'])){
	$datosMatricula['eps'] = base64_decode($_GET['eps']);
}
if(isset($_GET['celular2'])){
	$datosMatricula['celular2'] = base64_decode($_GET['celular2']);
}
if(isset($_GET['ciudadR'])){
	$datosMatricula['ciudadR'] = base64_decode($_GET['ciudadR']);
}
if(isset($_GET['nombre2'])){
	$datosMatricula['nombre2'] = base64_decode($_GET['nombre2']);
}
if(isset($_GET['documentoA'])){
	$datosMatricula['documentoA'] = base64_decode($_GET['documentoA']);
}
if(isset($_GET['nombreA'])){
	$datosMatricula['nombreA'] = base64_decode($_GET['nombreA']);
}
if(isset($_GET['ocupacionA'])){
	$datosMatricula['ocupacionA'] = base64_decode($_GET['ocupacionA']);
}
if(isset($_GET['generoA'])){
	$datosMatricula['generoA'] = base64_decode($_GET['generoA']);
}
if(isset($_GET['expedicionA'])){
	$datosMatricula['expedicionA'] = base64_decode($_GET['expedicionA']);
}
if(isset($_GET['tipoDocA'])){
	$datosMatricula['tipoDocA'] = base64_decode($_GET['tipoDocA']);
}
if(isset($_GET['apellido1A'])){
	$datosMatricula['apellido1A'] = base64_decode($_GET['apellido1A']);
}
if(isset($_GET['apellido2A'])){
	$datosMatricula['apellido2A'] = base64_decode($_GET['apellido2A']);
}
if(isset($_GET['nombre2A'])){
	$datosMatricula['nombre2A'] = base64_decode($_GET['nombre2A']);
}
if(isset($_GET['matestM'])){
	$datosMatricula['matestM'] = base64_decode($_GET['matestM']);
}