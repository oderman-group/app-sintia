<?php
require_once(ROOT_PATH."/main-app/class/Cronograma.php");
$consulta = Cronograma::traerDatosCronograma($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$contReg=1; 
$eventos="";
while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	$resultado["mes"]--;
	$eventos .= '
		{
			title: "'.mysqli_real_escape_string($conexion,$resultado["cro_tema"]).'",
			start: new Date('.$resultado["agno"].', '.$resultado["mes"].', '.$resultado["dia"].', 6, 0),
			backgroundColor: "'.$resultado["cro_color"].'",
			url: "cronograma-editar.php?idR='.base64_encode($resultado["cro_id"]).'"
		},
	'; 
}
$eventos = substr($eventos,0,-1);
?>

<script type="application/javascript">
		var AppCalendar = function() {
		return {
			init: function() {
				this.initCalendar()
			},
			initCalendar: function() {
				if (jQuery().fullCalendar) {
					var e = new Date,
						t = e.getDate(),
						a = e.getMonth(),
						n = e.getFullYear(),
						r = {};
					$("#calendar").removeClass("mobile"), r = {
						left: "prev,next,today",
						center: "title",
						right: "month,agendaWeek,agendaDay"
					};
					var l = function(e) {
							var t = {
								title: $.trim(e.text())
							};
							e.data("eventObject", t), e.draggable({
								zIndex: 999,
								revert: !0,
								revertDuration: 0
							})
						},
						o = function(e) {
							e = 0 === e.length ? "Untitled Event" : e;
						   /* var t = $('<div class="external-event label label-event">' + e + "</div>");*/
							var t = $('<div class="external-event label label-event-' +e+'">' + e + "</div>");
							jQuery("#event_box").append(t), l(t)
						};
					$("#external-events div.external-event").each(function() {
						l($(this))
					}), $("#event_add").unbind("click").click(function() {
						var e = $("#event_title").val();
						o(e)
					}), $("#event_box").html(""), o("Navidad"), o("Cumpleaños"), o("Reunión"), o("Competencia"), o("Cena"), o("Fiesta"), $("#calendar").fullCalendar("destroy"), $("#calendar").fullCalendar({
						header: r,
						defaultView: "month",
						slotMinutes: 15,
						editable: !0,
						droppable: !0,
						drop: function(e, t) {
							var a = $(this).data("eventObject"),
								n = $.extend({}, a);
							n.start = e, n.allDay = t, n.className = $(this).attr("data-class"), $("#calendar").fullCalendar("renderEvent", n, !0), $("#drop-remove").is(":checked") && $(this).remove()
						},

						/***** events ********/
						events: [
						 <?=$eventos;?>
						]
					})
				}
			}
		}
	}();
	jQuery(document).ready(function() {
		'use strict';
		AppCalendar.init()
	});
	</script>