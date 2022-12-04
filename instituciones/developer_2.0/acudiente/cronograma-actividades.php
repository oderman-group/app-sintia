<?php include("session.php");?>
<?php include("../estudiante/verificar-usuario.php");?>
<?php $idPaginaInterna = 109;?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../estudiante/verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<!-- full calendar -->
    <link href="../../../config-general/assets/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->

<?php include("../compartido/body.php");?>
	
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <?php include("../compartido/cronograma-calendario-contenido.php");?>
            </div>
            <!-- end page content -->
             <?php include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
     <!-- start js include path -->
    <script src="../../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="../../../config-general/assets/plugins/jquery-ui/jquery-ui.min.js" ></script>
    <!-- bootstrap -->
    <script src="../../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <!-- calendar -->
    <script src="../../../config-general/assets/plugins/moment/moment.min.js" ></script>
    <script src="../../../config-general/assets/plugins/fullcalendar/fullcalendar.min.js" ></script>
    <!--<script src="../../../config-general/assets/js/pages/calendar/calendar.min.js" ></script>-->

	<?php include("../estudiante/calendario-js.php");?>
		
    <!-- Common js-->
	<script src="../../../config-general/assets/js/app.js" ></script>
    <script src="../../../config-general/assets/js/layout.js" ></script>
	<script src="../../../config-general/assets/js/theme-color.js" ></script>
	<!-- Material -->
	<script src="../../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
</body>

</html>