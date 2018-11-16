/**
 * @author molle
 */
(function($) {
	$.extend({
		timedRedirect: new function() {
			var config;
			var timed = 0;
			var self = this;
			var redirected = false;
			
			this.defaults = {
				timeout: 3, // in seconds
				target: "#timer",
				url: "http://www.linux.de"
			}
			
			function tick() {
				$(config.target).animate({
					opacity: 1
				}, 500, "linear");
				
				$(config.target).animate({
					opacity: 0.25
				}, 500, "linear", tick);
				
				$(config.target).html(timed);
				
				if (timed == 0 && redirected == false) {
					window.location.href = config.url;
					redirected = true;
				}
				
				timed = timed > 0 ? timed - 1 : timed ;
			};
			
			this.construct = function(settings) {
				config = $.extend(self.config, $.timedRedirect.defaults, settings);
				timed = config.timeout;
				redirected = false;
				tick();
			}
			
		}
		
	});
	// extend plugin scope
	$.extend({
		redirect: $.timedRedirect.construct
	});
})(jQuery);
