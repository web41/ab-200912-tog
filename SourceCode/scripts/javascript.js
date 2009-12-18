var j = jQuery.noConflict();
j(function() {
	j('#mainmenu').ptMenu();
	j("#calendar_box").calendar({
		minYear: 1900,
		maxYear: 2100,
		firstDayOfWeek: 0,
		parentElement: '#calendar_box',
		dateFormat: '%d.%m.%Y',
		selectHandler: function(){},
		closeHandler: function(){}
	});
});