var j = jQuery.noConflict();
  
j(function() {
	j(".btn_cart").css({"margin-left": (j('.shoppingcart_box').width()-100)/2+"px", "display": "block"})
	
	j(document).ready(function()
	{  j('#left_category > li').bind('mouseover', jsddm_open)
	   j('#left_category > li').bind('mouseout',  jsddm_timer)
	});
	document.onclick = jsddm_close;
});

var timeout    = 500;
var closetimer = 0;
var ddmenuitem = 0;
var j = jQuery.noConflict();
function jsddm_open()
{  jsddm_canceltimer();
   jsddm_close();
   ddmenuitem = j(this).find('ul').css('visibility', 'visible');}

function jsddm_close()
{  if(ddmenuitem) ddmenuitem.css('visibility', 'hidden');}

function jsddm_timer()
{  closetimer = window.setTimeout(jsddm_close, timeout);}

function jsddm_canceltimer()
{  if(closetimer)
   {  window.clearTimeout(closetimer);
	  closetimer = null;}}