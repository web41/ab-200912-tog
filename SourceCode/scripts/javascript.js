var j = jQuery.noConflict();
j(function() {
	j('#'+main_menu).ptMenu();
	j("#calendar_box").calendar({
		minYear: 1900,
		maxYear: 2100,
		firstDayOfWeek: 0,
		parentElement: '#calendar_box',
		dateFormat: '%d.%m.%Y',
		selectHandler: function(){},
		closeHandler: function(){}
	});
	
	j(".table_paging").css({"margin-left": (j('.main_box').width()-j('.table_paging').width())/2+"px", "display": "block"})
});