/**
 * LavaLamp - A menu plugin for jQuery with cool hover effects.
 * @requires jQuery v1.1.3.1 or above
 *
 * http://gmarwaha.com/blog/?p=7
 *
 * Copyright (c) 2007 Ganeshji Marwaha (gmarwaha.com)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Version: 0.1.0
 */

/**
 * Creates a menu with an unordered list of menu-items. You can either use the CSS that comes with the plugin, or write your own styles 
 * to create a personalized effect
 *
 * The HTML markup used to build the menu can be as simple as...
 *
 *       <ul class="lavaLamp">
 *           <li><a href="#">Home</a></li>
 *           <li><a href="#">Plant a tree</a></li>
 *           <li><a href="#">Travel</a></li>
 *           <li><a href="#">Ride an elephant</a></li>
 *       </ul>
 *
 * Once you have included the style sheet that comes with the plugin, you will have to include 
 * a reference to jquery library, easing plugin(optional) and the LavaLamp(this) plugin.
 *
 * Use the following snippet to initialize the menu.
 *   $(function() { $(".lavaLamp").lavaLamp({ fx: "backout", speed: 700}) });
 *
 * Thats it. Now you should have a working lavalamp menu. 
 *
 * @param an options object - You can specify all the options shown below as an options object param.
 *
 * @option fx - default is "linear"
 * @example
 * $(".lavaLamp").lavaLamp({ fx: "backout" });
 * @desc Creates a menu with "backout" easing effect. You need to include the easing plugin for this to work.
 *
 * @option speed - default is 500 ms
 * @example
 * $(".lavaLamp").lavaLamp({ speed: 500 });
 * @desc Creates a menu with an animation speed of 500 ms.
 *
 * @option click - no defaults
 * @example
 * $(".lavaLamp").lavaLamp({ click: function(event, menuItem) { return false; } });
 * @desc You can supply a callback to be executed when the menu item is clicked. 
 * The event object and the menu-item that was clicked will be passed in as arguments.
 */
 var j = jQuery.noConflict();
(function(j) {
j.fn.lavaLamp = function(o) {
	o = j.extend({ fx: "linear", speed: 500, click: function(){}, currIndex: 0 }, o || {});
	return this.each(function() {
		var me = j(this), noop = function(){},
			$back = j('<li class="lavalamp_back"></li>').appendTo(me),
			$li = j("li", this), curr = j("li.lavalamp_current", this)[0] || j($li[o.currIndex]).addClass("lavalamp_current")[0];

		$li.not(".lavalamp_back").hover(function() {
			move(this);
		}, noop);

		j(this).hover(noop, function() {
			move(curr);
		});

		$li.click(function(e) {
			setCurr(this);
			return o.click.apply(this, [e, this]);
		});
		
		setCurr(curr);

		function setCurr(el) {
			$back.css({ "left": el.offsetLeft+"px", "width": el.offsetWidth-9+"px"});
			curr = el;
		};

		function move(el) {
			$back.each(function() {
				j.dequeue(this, "fx"); }
			).animate({
				width: el.offsetWidth-9,
				left: el.offsetLeft
			}, o.speed, o.fx);
		};

	});
};
})(jQuery);
