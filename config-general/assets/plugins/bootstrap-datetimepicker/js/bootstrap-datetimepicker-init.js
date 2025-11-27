$('.form_datetime').datetimepicker({
		    weekStart: 1,
		    todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			startView: 2,
			forceParse: 0,
		    showMeridian: 1,
		    // Usar iconos de Font Awesome en lugar de glyphicons
		    icons: {
		        time: 'fa fa-clock-o',
		        date: 'fa fa-calendar',
		        up: 'fa fa-chevron-up',
		        down: 'fa fa-chevron-down',
		        previous: 'fa fa-chevron-left',
		        next: 'fa fa-chevron-right',
		        today: 'fa fa-screenshot',
		        clear: 'fa fa-trash',
		        close: 'fa fa-remove'
		    }
		});
		$('.form_date').datetimepicker({
		    weekStart: 1,
		    todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			startView: 2,
			minView: 2,
			forceParse: 0,
		    // Usar iconos de Font Awesome en lugar de glyphicons
		    icons: {
		        time: 'fa fa-clock-o',
		        date: 'fa fa-calendar',
		        up: 'fa fa-chevron-up',
		        down: 'fa fa-chevron-down',
		        previous: 'fa fa-chevron-left',
		        next: 'fa fa-chevron-right',
		        today: 'fa fa-screenshot',
		        clear: 'fa fa-trash',
		        close: 'fa fa-remove'
		    }
		});
		$('.form_time').datetimepicker({
		    weekStart: 1,
		    todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			startView: 1,
			minView: 0,
			maxView: 1,
			forceParse: 0,
		    // Usar iconos de Font Awesome en lugar de glyphicons
		    icons: {
		        time: 'fa fa-clock-o',
		        date: 'fa fa-calendar',
		        up: 'fa fa-chevron-up',
		        down: 'fa fa-chevron-down',
		        previous: 'fa fa-chevron-left',
		        next: 'fa fa-chevron-right',
		        today: 'fa fa-screenshot',
		        clear: 'fa fa-trash',
		        close: 'fa fa-remove'
		    }
		});
		$(function () {
            $('#datetimepicker1').datetimepicker();
        });
		 $(function() {
			    // Bootstrap DateTimePicker v3
			    $('#datetimepicker4').datetimepicker({
			      pickTime: false
			    });
			    // Bootstrap DateTimePicker v4
			    $('#datetimepicker3').datetimepicker({
			      format: 'DD/MM/YYYY'
			    });
			  });
		