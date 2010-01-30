var j = jQuery.noConflict();
j(function() {
	j('#slideshow').cycle({ 
		fx:     'fade', 
		speed:   500, 
		timeout: 5000,
		pager:  '.navigation'
	});
});