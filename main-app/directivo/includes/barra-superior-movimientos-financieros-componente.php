<?php
    $filtro = '';
    $busqueda='';
    $usuario = '';
    if (!empty($_GET['usuario'])) {
        $usuario = base64_decode($_GET['usuario']);
        $filtro .= " AND fcu_usuario='".$usuario."'";
    }
    $tipo = '';
    if (!empty($_GET['tipo'])) {
        $tipo = base64_decode($_GET['tipo']);
        $filtro .= " AND fcu_tipo='".$tipo."'";
    }
    $estadoFil = '';
    if (!empty($_GET['estadoFil'])) {
        $estadoFil = base64_decode($_GET['estadoFil']);
        $filtro .= " AND fcu_status='".$estadoFil."'";
    }
    $estadoM = '';
    if (!empty($_GET['estadoM'])) {
        $estadoM = base64_decode($_GET['estadoM']);
        $filtro .= " AND mat_estado_matricula='".$estadoM."'";
    }
    $fecha = '';
    if (!empty($_GET['fecha'])) {
        $fecha = base64_decode($_GET['fecha']);
        $filtro .= " AND fcu_fecha='".$fecha."'";
    }
    $desde='';
    $hasta='';
    if (!empty($_GET["fFecha"]) || (!empty($_GET["desde"]) || !empty($_GET["hasta"]))) {
        $desde=$_GET["desde"];
        $hasta=$_GET["hasta"];
        $filtro .= " AND (fcu_fecha BETWEEN '" . $_GET["desde"] . "' AND '" . $_GET["hasta"] . "' OR fcu_fecha LIKE '%" . $_GET["hasta"] . "%')";
    }
    
    // Filtro para ocultar anuladas por defecto
    $mostrarAnuladas = !empty($_GET['mostrarAnuladas']) && $_GET['mostrarAnuladas'] == '1';
    if (!$mostrarAnuladas) {
        $filtro .= " AND fcu_anulado=0";
    }

    $estiloResaltadoFV = '';
    if (isset($_GET['tipo']) && $_GET['tipo'] == base64_encode(1)) $estiloResaltadoFV = 'style="color: '.$Plataforma->colorUno.';"';
    $estiloResaltadoFC = '';
    if (isset($_GET['tipo']) && $_GET['tipo'] == base64_encode(2)) $estiloResaltadoFC = 'style="color: '.$Plataforma->colorUno.';"';

    $estiloResaltadoCobrado = '';
    if (isset($_GET['estadoFil']) && $_GET['estadoFil'] == base64_encode(COBRADA)) $estiloResaltadoCobrado = 'style="color: '.$Plataforma->colorUno.';"';
    $estiloResaltadoPorCobrar = '';
    if (isset($_GET['estadoFil']) && $_GET['estadoFil'] == base64_encode(POR_COBRAR)) $estiloResaltadoPorCobrar = 'style="color: '.$Plataforma->colorUno.';"';
    
    // Preservar mostrarAnuladas en las URLs de filtros
    $paramMostrarAnuladas = $mostrarAnuladas ? "&mostrarAnuladas=1" : "";
    
    require_once(ROOT_PATH."/main-app/class/componentes/ComponenteFiltros.php");
$opciones[0] = [
    ComponenteFiltro::COMPB_OPCIONES_TEXTO   => 'Menú movimiento financiero',
    ComponenteFiltro::COMPB_OPCIONES_URL     => 'movimientos-importar.php',
    ComponenteFiltro::COMPB_OPCIONES_PERMISO => Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0105']),
    ComponenteFiltro::COMPB_OPCIONES_PAGINAS => $paginas = [
        [
            ComponenteFiltro::COMPB_OPCIONES_PAGINAS_TEXTO => 'Importar saldos',
            ComponenteFiltro::COMPB_OPCIONES_PAGINAS_URL   => 'movimientos-importar.php'
        ]
    ]
];
$filtroTipo = [
    [
        ComponenteFiltro::COMPB_FILTRO_LISTA_ID    => 1,
        ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => 'Fact. Venta',
        ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF'] . "?estadoFil=" . base64_encode($estadoFil) . "&usuario=" . base64_encode($usuario) . "&desde=" . $desde . "&hasta=" . $hasta . "&tipo=" . base64_encode(1) . "&estadoM=" . base64_encode($estadoM) . "&fecha=" . base64_encode($fecha) . $paramMostrarAnuladas
    ],
    [
        ComponenteFiltro::COMPB_FILTRO_LISTA_ID    => 2,
        ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => 'Fact. Compra',
        ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF'] . "?estadoFil=" . base64_encode($estadoFil) . "&usuario=" . base64_encode($usuario) . "&desde=" . $desde . "&hasta=" . $hasta . "&tipo=" . base64_encode(2) . "&estadoM=" . base64_encode($estadoM) . "&fecha=" . base64_encode($fecha) . $paramMostrarAnuladas
    ],
    [
        ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => 'Ver Todos',
        ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF']. "?estadoFil=" . base64_encode($estadoFil) ."=&usuario=" . base64_encode($usuario) . "&desde=" . $desde . "&hasta=" . $hasta . "&tipo=&estadoM=" . base64_encode($estadoM) . "&fecha=" . base64_encode($fecha) . $paramMostrarAnuladas,
        ComponenteFiltro::COMPB_FILTRO_LISTA_STYLE => 'font-weight: bold; text-align: center;'
    ]

];
$filtroEstado = [
    [
        ComponenteFiltro::COMPB_FILTRO_LISTA_ID    => POR_COBRAR,
        ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => 'Por Cobrar',
        ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF'] . "?estadoFil=" . base64_encode(POR_COBRAR) . "&usuario=" . base64_encode($usuario) . "&desde=" . $desde . "&hasta=" . $hasta . "&tipo=" . base64_encode($tipo) . "&estadoM=" . base64_encode($estadoM) . "&fecha=" . base64_encode($fecha) . $paramMostrarAnuladas
    ],
    [
        ComponenteFiltro::COMPB_FILTRO_LISTA_ID    => COBRADA,
        ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => 'Cobradas',
        ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF'] . "?estadoFil=" . base64_encode(COBRADA) . "&usuario=" . base64_encode($usuario) . "&desde=" . $desde . "&hasta=" . $hasta . "&tipo=" . base64_encode($tipo) . "&estadoM=" . base64_encode($estadoM) . "&fecha=" . base64_encode($fecha) . $paramMostrarAnuladas
    ],
    [
        ComponenteFiltro::COMPB_FILTRO_LISTA_TEXTO => 'Ver Todos',
        ComponenteFiltro::COMPB_FILTRO_LISTA_URL   => $_SERVER['PHP_SELF']. "?estadoFil=&usuario=" . base64_encode($usuario) . "&desde=" . $desde . "&hasta=" . $hasta  . "&tipo=" . base64_encode($tipo) .  "&estadoM=" . base64_encode($estadoM) . "&fecha=" . base64_encode($fecha) . $paramMostrarAnuladas,
        ComponenteFiltro::COMPB_FILTRO_LISTA_STYLE => 'font-weight: bold; text-align: center;'
    ]

];
$html = "
<form class='dropdown-item' method='get' action='".$_SERVER['PHP_SELF']."'>
                        <input type='hidden' name='tipo' value='".base64_encode($tipo)."' />
                        <input type='hidden' name='busqueda' value='".$busqueda."' />
                        <input type='hidden' name='usuario' value='".base64_encode($usuario)."' />
                        <input type='hidden' name='estadoM' value='".base64_encode($estadoM)."' />
                        <input type='hidden' name='fecha' value='".base64_encode($fecha)."' />
                        <input type='hidden' name='estadoFil' value='".base64_encode($estadoFil)."' />
                        <input type='hidden' name='mostrarAnuladas' value='".($mostrarAnuladas ? '1' : '')."' />
                        <label>Fecha Desde:</label>
                        <input type='date' class='form-control' placeholder='desde' name='desde' value='".$desde."' />

                        <label>Hasta</label>
                        <input type='date' class='form-control' placeholder='hasta' name='hasta' value='".$hasta."' />

                        <input type='submit' class='btn deepPink-bgcolor' name='fFecha' value='Filtrar' style='margin: 5px;'>
                    </form>
                    <a class='dropdown-item' href='".$_SERVER['PHP_SELF']."' style='font-weight: bold; text-align: center;'>VER TODO</a>
";
$filtros[0] = [
    ComponenteFiltro::COMPB_FILTRO_GET    => 'tipo',
    ComponenteFiltro::COMPB_FILTRO_TEXTO  => 'Filtrar por tipo',
    ComponenteFiltro::COMPB_FILTRO_LISTA  => $filtroTipo,
];
$filtros[1] = [
    ComponenteFiltro::COMPB_FILTRO_GET    => 'estadoFil',
    ComponenteFiltro::COMPB_FILTRO_TEXTO  => 'Filtrar por estado',
    ComponenteFiltro::COMPB_FILTRO_LISTA  => $filtroEstado,
];

$filtros[2] = [
    ComponenteFiltro::COMPB_FILTRO_GET    => '',
    ComponenteFiltro::COMPB_FILTRO_TEXTO  => 'Filtrar por Fecha',
    ComponenteFiltro::COMPB_FILTRO_TIPO   => ComponenteFiltro::COMPB_FILTRO_TIPO_HTML,
    ComponenteFiltro::COMPB_FILTRO_TIPO_HTML_CONTENIDO=> $html ,
];


$barraSuperior = new ComponenteFiltro('movimientos', 'filter-movimientos.php','movimientos-tbody.php', $filtros, $opciones,'mostrarResultado', false);
$barraSuperior->generarComponente();

// Botón para mostrar/ocultar facturas anuladas
$mostrarAnuladas = !empty($_GET['mostrarAnuladas']) && $_GET['mostrarAnuladas'] == '1';
$urlMostrarAnuladas = $_SERVER['PHP_SELF'] . "?";
$params = $_GET;
$params['mostrarAnuladas'] = '1';
$urlMostrarAnuladas .= http_build_query($params);

$urlOcultarAnuladas = $_SERVER['PHP_SELF'] . "?";
$params = $_GET;
unset($params['mostrarAnuladas']);
$urlOcultarAnuladas .= http_build_query($params);

$colorPrimario = isset($Plataforma->colorUno) ? $Plataforma->colorUno : '#667eea';
$colorSecundario = isset($Plataforma->colorDos) ? $Plataforma->colorDos : '#764ba2';
$urlReporteMorosos = 'movimientos-reporte-morosos.php';
$queryActual = $_GET;
if (!empty($queryActual)) {
    $urlReporteMorosos .= '?' . http_build_query($queryActual);
}
?>
<div class="movimientos-actions-bar" style="margin: 12px 0; display: flex; justify-content: flex-end; align-items: center; gap: 10px; flex-wrap: wrap;">
	<?php if ($mostrarAnuladas) { ?>
		<a href="<?= $urlOcultarAnuladas ?>" class="btn btn-warning btn-sm">
			<i class="fa fa-eye-slash"></i> Ocultar Facturas Anuladas
		</a>
	<?php } else { ?>
		<a href="<?= $urlMostrarAnuladas ?>" class="btn btn-info btn-sm">
			<i class="fa fa-eye"></i> Mostrar Facturas Anuladas
		</a>
	<?php } ?>

	<?php if (Modulos::validarPermisoEdicion()) { ?>
	<button class="btn btn-warning btn-sm" onclick="bloquearUsuariosPendientes()" title="Bloquear usuarios con saldo pendiente">
		<i class="fa fa-user-slash"></i> Bloquear usuarios con saldo pendiente
	</button>
	<?php } ?>

	<a href="<?= $urlReporteMorosos ?>" target="_blank" class="btn btn-success btn-sm" title="Imprimir informe de morosos">
		<i class="fa fa-print"></i> Informe morosos
	</a>

	<?php if (Modulos::validarPermisoEdicion()) { ?>
	<button class="btn btn-primary btn-sm" onclick="recordarSaldoSeleccionados()" title="Recordar saldo pendiente por correo">
		<i class="fa fa-envelope"></i> Recordar saldo
	</button>
	<?php } ?>

	<?php if (Modulos::validarPermisoEdicion() &&  Modulos::validarSubRol(['DT0106'])) { ?>
	<button class="btn btn-sm" onclick="abrirModalAgregarMovimiento()" title="Agregar nueva transacción" style="background: linear-gradient(135deg, <?= $colorPrimario ?> 0%, <?= $colorSecundario ?> 100%); color: #fff; border: none; border-radius: 20px; padding: 8px 18px; font-weight: 600;">
		<i class="fa fa-plus"></i> Agregar transacción
	</button>
	<?php } ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).off('click', '.filtro-async');
    $(document).on('click', '.filtro-async', function(e) {
        e.preventDefault();
        var destino = $(this).data('url') || '';
        var extras = $(this).data('parametros') || '';
        window.location.href = destino + extras;
    });
});
</script>