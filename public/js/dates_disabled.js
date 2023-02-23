let select=$('#dates_disabled');

$(function(){
    if (!select.length)
        return;

    select.datepicker({
        weekStart: 1,
        language: "uk",
        daysOfWeekDisabled: "0,6",
        todayHighlight: true,
        maxViewMode: 0,
        multidate: true,
        updateViewDate: false,
        disableTouchKeyboard: true
    });

    select.datepicker('setDates', close_dates);

    $('#dates_disabled_save').click(function(){
    	let dates=$('#dates_disabled').datepicker('getDates');

    	$.ajax({
    		url: '/admin/dates_disabled/save',
    		type: 'POST',
    		data: {dates: dates},
    		dataType: 'json',
    		success: function(data){
                alert(data.success ? 'Дати збережено' : 'Сталася помилка!\n\nДати не збережено');
    		}
    	});
    });
});