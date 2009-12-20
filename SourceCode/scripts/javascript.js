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

function toogleCheckBox(theElement,state) {
	var theForm = theElement.form, z = 0;

	for(z=0; z<theForm.length;z++){
		if(theForm[z].type == 'checkbox'){
			theForm[z].checked = state;
		}
	}
}