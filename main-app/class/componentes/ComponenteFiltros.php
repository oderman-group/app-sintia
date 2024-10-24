<?php

class ComponenteFiltro
{
        /* VALORES COMPONETE BARRA SUPERIOR */
    public const COMPB_OPCIONES_TEXTO             ='opciones_texto';
    public const COMPB_OPCIONES_URL               ='opciones_url';
    public const COMPB_OPCIONES_PERMISO           ='opciones_permiso';
    public const COMPB_OPCIONES_PAGINAS           ='opciones_paginas';
    public const COMPB_OPCIONES_PAGINAS_TEXTO     ='opciones_paginas_texto';
    public const COMPB_OPCIONES_PAGINAS_URL       ='opciones_paginas_url';
    public const COMPB_OPCIONES_PAGINAS_ACTION    ='opciones_paginas_action';
    public const COMPB_OPCIONES_PAGINAS_PERMISO   ='opciones_paginas_permiso';
    public const COMPB_OPCIONES_PAGINAS_TARGET    ='opciones_paginas_target';
    public const COMPB_OPCIONES_PAGINAS_DIVIDER   ='opciones_paginas_divider';
    public const COMPB_FILTRO_GET                 ='filtro_get';
    public const COMPB_FILTRO_TEXTO               ='filtro_texto';
    public const COMPB_FILTRO_SELECT              ='filtro_select';
    public const COMPB_FILTRO_LISTA               ='filtro_lista';
    public const COMPB_FILTRO_TIPO                ='filtro_tipo';
    public const COMPB_FILTRO_TIPO_CHECK          ='filtro_tipo_check';
    public const COMPB_FILTRO_TIPO_HTML           ='filtro_tipo_html';
    public const COMPB_FILTRO_TIPO_HTML_CONTENIDO ='filtro_tipo_html_contenido';
    public const COMPB_FILTRO_LISTA_ID            ='filtro_lista_id';
    public const COMPB_FILTRO_LISTA_TEXTO         ='filtro_lista_texto';
    public const COMPB_FILTRO_LISTA_STYLE         ='filtro_lista_style';
    public const COMPB_FILTRO_LISTA_URL           ='filtro_lista_url';
    /**
     * @var string El ID del componente.
     */
    private $id;
    /**
     * @var array  un array donde se insertaran los datos pra realizar los filtros de la consulta.
     */
    private $filtros;
    /**
     * @var array  un array donde se insertaran las opciones las culaes necesariamente tendran que utilizar las etiquetas 
     * [texto,permiso,pagina] y dentro de paginas debe ir un array con las siguiente estrucutra ['texto','url'].
     */
    private $opciones;
    /**
     * @var int la url donde sera ejecutado el fetch .
     */
    private $urlFilter;
    /**
     * @var int la url donde sera estara el resultado en html .
     */
    private $urlHtml;
    private $urlBase;
    private $buscador;

    /**
     * @var int metodo por el cual se enviara el resultado obtenido del fetch. este debe estar en la direccion:
     * /app-sintia/main-app/class/componentes/filter/
     */
    private $metodo;
    /**
     * @var string Son los filtros pasados por $_GET.
     */
    private $filtrosGet;
    public function __construct($id, $urlFilter, $urlHtml = '', $filtros = array(), $opciones = array(), $metodo = '' ,$buscador=true)
    {
        $protocolo = '';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $protocolo = 'https://';
        } else {
            $protocolo = 'http://';
        }
        if (!empty($opciones)) {
            foreach ($opciones as $indice => $opcion) {
                if (!array_key_exists(self::COMPB_OPCIONES_TEXTO, $opcion)) {
                    echo "En la opcion (" . $indice . ") del componente (" . $id . ")  no está la etiqueta texto en el array.";
                    exit();
                }
                if (!array_key_exists(self::COMPB_OPCIONES_PERMISO, $opcion)) {
                    echo "En la opcion (" . $indice . ") del componente (" . $id . ")  no está la etiqueta permiso en el array";
                    exit();
                }
                if (array_key_exists(self::COMPB_OPCIONES_PAGINAS, $opcion)) {
                    if (is_array($opcion[self::COMPB_OPCIONES_PAGINAS])) {
                        foreach ($opcion[self::COMPB_OPCIONES_PAGINAS] as $indice2 => $page) {
                            if (!array_key_exists(self::COMPB_OPCIONES_PAGINAS_TEXTO, $page)) {
                                echo "En la opcion (" . $indice . ") del componente (" . $id . ")  en la pagina({$indice2}) no está la etiqueta texto en el array";
                                exit();
                            }
                            if (!array_key_exists(self::COMPB_OPCIONES_PAGINAS_URL, $page)) {
                                echo "En la opcion (" . $indice . ") del componente (" . $id . ")  en la pagina({$indice2}) no está la etiqueta url en el array";
                                exit();
                            }
                        }
                    } else {
                        echo "En la opcion (" . $indice . ") del componente (" . $id . ")  paginas debe ser un  array.";
                        exit();
                    }
                }
            }
        }

        $this->opciones = $opciones;
        $this->id = $id;
        $this->urlFilter = 'filter/' . $urlFilter;
        $this->urlHtml =  'result/' . $urlHtml;
        $this->urlBase = $protocolo . $_SERVER['HTTP_HOST'] . '/app-sintia/main-app/class/componentes/barra-builder.php';
        $this->metodo = $metodo;
        $this->filtros = $filtros;
        $this->buscador = $buscador;
        $queryString = $_SERVER['QUERY_STRING']; // Parsear la cadena de consulta y almacenar los parámetros en un array
        parse_str($queryString, $parametros); // Convertir el array a JSON
        $filtrosGet = json_encode($parametros);
        $this->filtrosGet = $filtrosGet;
    }

    /**
     * Construye un array a partir de los resultados de una consulta SQL.
     *
     * @param mysqli_result $resultSql El resultado de la consulta SQL.
     * @return array El array construido a partir de los resultados de la consulta.
     */
    public function  builderArray($resultSql)
    {
        $index = 0;
        $arraysDatos = array();
        while ($fila = $resultSql->fetch_assoc()) {
            $arraysDatos[$index] = $fila;
            $index++;
        }
        $lista = $arraysDatos;
        $data["data"] = $lista;
        return $data;
    }

    public function generarComponente()
    {
        global $frases, $datosUsuarioActual, $Plataforma;
        // Generar HTML del componente
        $html = "
        <nav class='navbar navbar-expand-lg navbar-dark' style='background-color: #41c4c4;'>
            <ul class='navbar-nav mr-auto'>";
        if (!empty($this->opciones)) {
            foreach ($this->opciones as $opcion) {
                if ($opcion) {

                    if (!empty($opcion[self::COMPB_OPCIONES_PAGINAS])) {
                        $html .= "
                        <li class='nav-item dropdown'>
                        <a class='nav-link dropdown-toggle' href='javascript:void(0);' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' style='color:{$Plataforma->colorUno};'>
                            {$opcion[self::COMPB_OPCIONES_TEXTO]}
                            <span class='fa fa-angle-down'></span>
                        </a>";
                        $html .= "<div class='dropdown-menu' aria-labelledby='navbarDropdown'>";
                        foreach ($opcion[self::COMPB_OPCIONES_PAGINAS] as $pagina) {
                            $dataModal = !empty($pagina[self::COMPB_OPCIONES_PAGINAS_TARGET]) ? "data-toggle='modal'  data-target='{$pagina[self::COMPB_OPCIONES_PAGINAS_TARGET]}' " : "";
                            $action = !empty($pagina[self::COMPB_OPCIONES_PAGINAS_ACTION]) ? "onClick='{$pagina[self::COMPB_OPCIONES_PAGINAS_ACTION]}' " : "";
                            $permiso = !empty($pagina[self::COMPB_OPCIONES_PAGINAS_PERMISO]) ? $pagina[self::COMPB_OPCIONES_PAGINAS_PERMISO] : true;
                            if ($permiso) {
                                $html .= "<a class='dropdown-item' href='{$pagina[self::COMPB_OPCIONES_PAGINAS_URL]}' {$dataModal} {$action} >{$pagina[self::COMPB_OPCIONES_PAGINAS_TEXTO]}</a>";
                            }
                            $divider = !empty($pagina["divider"]) ? true : false;
                            if ($divider) {
                                $html .= "<div class='dropdown-divider'></div>  ";
                            }
                        }
                        $html .= "</div>";
                    } else {
                        $html .= "
                        <li class='nav-item'>
                        <a class='nav-link' href='{$opcion[self::COMPB_OPCIONES_URL]}' style='color:{$Plataforma->colorUno};'>
                        {$opcion[self::COMPB_OPCIONES_TEXTO]}
                        </a>";
                    }
                    $html .= "
                    </li>
                    ";
                }
            }
        }
        if (!empty($this->filtros) &&  !empty($this->opciones)) {
            $html .= " <li class='nav-item'> <a class='nav-link' href='javascript:void(0);' style='color:#FFF;'>|</a></li>";
        }
        if (!empty($this->filtros)) {

            foreach ($this->filtros as $filtro) {
                $html .= " <li class='nav-item dropdown'>
                <a class='nav-link dropdown-toggle' href='javascript:void(0);' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' style='color:#FFF;'>
                {$filtro[self::COMPB_FILTRO_TEXTO]}  ";
                // if(!empty($filtro[self::COMPB_FILTRO_SELECT])){
                //     $html .= ": {$filtro[self::COMPB_FILTRO_LISTA][$filtro[self::COMPB_FILTRO_SELECT]][self::COMPB_FILTRO_LISTA_TEXTO]} ";
                // }
                $decode = NULL;
                if (!empty($filtro[self::COMPB_FILTRO_GET])) {  //$filtro[self::COMPB_FILTRO_GET] debe ser el mismo nombre del filtro que viene desde la Url
                    if (!empty($_GET[$filtro[self::COMPB_FILTRO_GET]])) {
                        $decode = base64_decode($_GET[$filtro[self::COMPB_FILTRO_GET]]);
                        $seleccionado = array_filter($filtro[self::COMPB_FILTRO_LISTA], 
                                                    function($select) use($decode) { 
                                                        if(!empty($select[self::COMPB_FILTRO_LISTA_ID])){
                                                            return $select[self::COMPB_FILTRO_LISTA_ID] == $decode;
                                                        };                                                 
                                                    }
                                                );
                        $primerElemento = reset($seleccionado);
                        $html .= ": {$primerElemento[self::COMPB_FILTRO_LISTA_TEXTO]} ";
                    }
                }

                $parametros = "";
                if (isset($_GET) ) {
                    foreach ($_GET as $key => $value) {
                        if (!empty($filtro[self::COMPB_FILTRO_GET]) && $key != $filtro[self::COMPB_FILTRO_GET]) {
                            $parametros .= "&{$key}={$value}";
                        }    
                    }
                }

                $html .= "
					<span class='fa fa-angle-down'></span>
				</a>
                ";
                if (!empty($filtro[self::COMPB_FILTRO_LISTA])) {
                    $html .= "<div class='dropdown-menu' aria-labelledby='navbarDropdown'>";
                    $html .= "<form class='px-2 py-2' id='filtroForm'>";
                    foreach ($filtro[self::COMPB_FILTRO_LISTA] as $item) {
                        if(!empty($filtro[self::COMPB_FILTRO_TIPO]) && $filtro[self::COMPB_FILTRO_TIPO] == self::COMPB_FILTRO_TIPO_CHECK){
                            $html .= "<div class='form-check'>";
                            $html .= "<input class='form-check-input {$filtro[self::COMPB_FILTRO_GET]}-checkbox' type='checkbox' name='{$filtro[self::COMPB_FILTRO_GET]}[]' value='{$item[self::COMPB_FILTRO_LISTA_ID]}' id='{$filtro[self::COMPB_FILTRO_GET]}{$item[self::COMPB_FILTRO_LISTA_ID]}'>";
                            $html .= "<label class='form-check-label' for='{$filtro[self::COMPB_FILTRO_GET]}{$item[self::COMPB_FILTRO_LISTA_ID]}'>{$item[self::COMPB_FILTRO_LISTA_TEXTO]}</label>";
                            $html .= "</div>";
                        }else{
                            $style = !empty($item[self::COMPB_FILTRO_LISTA_STYLE]) ? "style='{$item[self::COMPB_FILTRO_LISTA_STYLE]}'" : "";
                            $styleSelect = !empty($item[self::COMPB_FILTRO_LISTA_ID]) && $item[self::COMPB_FILTRO_LISTA_ID] == $decode ? "style='color: orange;'" : "";
                            $html .= "<a class='dropdown-item' href='{$item[self::COMPB_FILTRO_LISTA_URL]}{$parametros}'  {$style} {$styleSelect}>{$item[self::COMPB_FILTRO_LISTA_TEXTO]}</a>";
                        }
                    }
                    $html .= "</form>";
                    $html .= "</div>";
                }
                if (!empty($filtro[self::COMPB_FILTRO_TIPO]) && $filtro[self::COMPB_FILTRO_TIPO] == self::COMPB_FILTRO_TIPO_HTML) {
                    $html .= "<div class='dropdown-menu' aria-labelledby='navbarDropdown'>";
                    $html .= $filtro[self::COMPB_FILTRO_TIPO_HTML_CONTENIDO];
                    $html .= "</div>";
                }

                $html .= "<script>
                                var {$filtro[self::COMPB_FILTRO_GET]}Seleccionados = [];
                            </script>";

                if(!empty($filtro[self::COMPB_FILTRO_TIPO]) && $filtro[self::COMPB_FILTRO_TIPO] == self::COMPB_FILTRO_TIPO_CHECK){
                    $html .= "
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                // Obtener todos los checkboxes del filtro
                                var {$filtro[self::COMPB_FILTRO_GET]}Checkboxes = document.querySelectorAll('.{$filtro[self::COMPB_FILTRO_GET]}-checkbox');

                                // Escuchar el evento de cambio en los checkboxes
                                {$filtro[self::COMPB_FILTRO_GET]}Checkboxes.forEach(function (checkbox) {
                                    checkbox.addEventListener('change', function () {
                                        // Obtener los valores seleccionados
                                            {$filtro[self::COMPB_FILTRO_GET]}Seleccionados = Array.from({$filtro[self::COMPB_FILTRO_GET]}Checkboxes)
                                            .filter(function (checkbox) {
                                                return checkbox.checked;
                                            })
                                            .map(function (checkbox) {
                                                return checkbox.value;
                                            });

                                        // Realizar la acción de filtrado con los checkboxes seleccionados
                                        filtrarPor{$filtro[self::COMPB_FILTRO_GET]}({$filtro[self::COMPB_FILTRO_GET]}Seleccionados);
                                    });
                                });

                                function filtrarPor{$filtro[self::COMPB_FILTRO_GET]}({$filtro[self::COMPB_FILTRO_GET]}Seleccionados) {
                                    console.log('Checkboxes seleccionados:', {$filtro[self::COMPB_FILTRO_GET]}Seleccionados);
                                    {$this->id}_buscar();
                                }
                            });
                        </script>
                    ";
                }

                $html .= "</li>";
            }
        }
        if (!empty($this->filtros) &&  !empty($this->opciones)) {
            $html .= "<li class='nav-item'> <a class='nav-link' href='javascript:void(0);' style='color:{$Plataforma->colorUno}'>|</a></li>
  
            <li class='nav-item'> <a class='nav-link' href='{$_SERVER['PHP_SELF']}' style='color:{$Plataforma->colorUno}'>Quitar filtros</a></li>";
        }

        $html .= "   
            </ul>
            <style>
                    
                    #input_{$this->id}::placeholder {
                        text-align: left;
                    }

                    #input_{$this->id} {
                        text-align: left;
                    }
            </style> ";
        
        $display=$this->buscador?'block':'none';
        $html .= " 
            <div class='form-inline my-2 my-lg-0' style='display:$display;'>
                <input id='input_{$this->id}' class='form-control mr-sm-2' style='width: 250px' type='search' 
                data-toggle='tooltip' 
                   data-placement='right' 
                   title='{$frases[431][$datosUsuarioActual['uss_idioma']]}'
                placeholder='{$frases[430][$datosUsuarioActual['uss_idioma']]}' aria-label='Search' name='busqueda' >
                <button id='btn_{$this->id}' onclick='{$this->id}_buscar(true)' class='btn deepPink-bgcolor my-2 my-sm-0' type='buttom'>{$frases[8][$datosUsuarioActual['uss_idioma']]}</button>
            </div>";
            
        $html .= "    
        </nav>
        <script type='text/javascript'>
        function cargarJquery() {
          $(document).ready(function() {			
              $('[data-toggle=".'popover'."]').popover();
              $('[data-toggle=".'tooltip'."]').tooltip();
           
          });
        };
        input_{$this->id}.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                {$this->id}_buscar()
             }
          });
        input_{$this->id}.addEventListener('input', function(event) {
           if ( input_{$this->id}.value.trim() === '') {
             {$this->id}_buscar()
           }
        });
        function {$this->id}_buscar(buscar){
            document.getElementById('gifCarga').style.display = 'block';
            var valor = document.getElementById('input_{$this->id}').value;
            var filtro2 = {'{$filtro[self::COMPB_FILTRO_GET]}Seleccionados' : {$filtro[self::COMPB_FILTRO_GET]}Seleccionados};
            var urlbase = '{$this->urlBase}'; 
            if(valor.length > 2){
                var data = {
                    'valor': (valor),
                    'filtro2': (filtro2),
                    'url': '{$this->urlFilter}'
                };
                data.filtros =JSON.parse('{$this->filtrosGet}');
                {$this->id}_ejecutarFetch(urlbase,data);
            }else if(valor.length == 0){
                var data = {
                    'valor': (valor),
                    'filtro2': (filtro2),
                    'url': '{$this->urlFilter}'
                };
                data.filtros =JSON.parse('{$this->filtrosGet}');
                {$this->id}_ejecutarFetch(urlbase,data);
            }else if(buscar){
                var data = {
                    'valor': (valor),
                    'filtro2': (filtro2),
                    'url': '{$this->urlFilter}'
                };
                data.filtros =JSON.parse('{$this->filtrosGet}');
                {$this->id}_ejecutarFetch(urlbase,data);

            }
            
           
        }
        function {$this->id}_ejecutarFetch(url,data){
            fetch(url, {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(data), // data can be `string` or {object}!
                headers: {
                    'Content-Type': 'application/json'
                },
            })
            .then((res) => res.json())
            .catch((error) => console.error('Error:', error))
            .then(
                function(response) {
                    {$this->id}_responseHtml(response);
                });
        }
        function {$this->id}_responseHtml(dato){
            var tbody = document.getElementById('{$this->id}_result');
           
            tbody.innerHTML = ''; 
            var data = {
                'data': (dato),
                'url': '{$this->urlHtml}'
            };
            data.filtros =JSON.parse('{$this->filtrosGet}');
            var url = '{$this->urlBase}'; 
            fetch(url, {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(data), // data can be `string` or {object}!
                headers: {
                    'Content-Type': 'text/html'
                },
            })
            .then((res) => res.text()).catch((error) => console.error('Error:', error))
            .catch((error) => console.error('Error:', error))
            .then(
                function(response) { 
                        if ($.fn.DataTable.isDataTable('#example1')) {
                            $('#example1').DataTable().clear().destroy(); // Destruir y limpiar
                        }                     
                       tbody.innerHTML = response;
                       cargarJquery();
                        $('#example1').DataTable();
                       document.getElementById('gifCarga').style.display = 'none';";

        if (!empty($this->metodo)) {
            $html .= "{$this->metodo}(response)";
        }
        $html .= "});
        }

        </script>
        ";

        // Retornar el HTML generado
        echo $html;
    }
}
