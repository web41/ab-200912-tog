var j = jQuery.noConflict();
j(function() {
	j('#slideshow').cycle({ 
		fx:     'fade', 
		speed:   800, 
		timeout: 8000,
		pager:  '.navigation' 
	});
});