/***
	KenBurning Slider 0.20 by Simbirsk
***/
(function ($) {
	jQuery.fn.kenBurning = function (options) {
		var defaults = {
			zoom : 1.2,
			time : 6000
		},
			settings = $.extend(defaults, options),

			zoomMax = (100 * settings.zoom) + "%",
			zoomStepIn = (((settings.zoom - 1) * 20) + 100) + "%",
			zoomStepOut = (((settings.zoom - 1) * 80) + 100) + "%",
			
			timeStep1 = settings.time * 0.2,
			timeStep2 = settings.time * 0.8,

			$container = $(this),
			animation = "in";

		$(function () {
			$container.addClass('kenburning-container');
			$.fn.kenBurning.doIt();
		    setInterval("$.fn.kenBurning.doIt()", settings.time);
		});

		$.fn.kenBurning.doIt = function () {
		    var $active = $container.find('img.active');

		    if ($active.length === 0) {$active = $container.find('img:last'); }

		    // use this to pull the images in the order they appear in the markup
		    var $next =  $active.next().length ? $active.next()
		        : $container.find('img:first');

		    $active.addClass('last-active').removeClass('active');

			if (animation === "in") {
			    $next.css({
					left	:	"0",
					right	:	"auto",
					opacity	:	0.0,
					width	:	"100%"
			    })
			        .addClass('active')
			        .animate({opacity: 1.0, width: zoomStepIn}, 0, "linear") // En vrai : timeStep1 au lieu de 0
			        .animate({width: zoomMax},settings.time, "linear", function () { // En vrai : timeStep2 au lieu de settings.time
			            $active.removeClass('last-active');
			        });
			    animation = "out";
		    } else {
				$next.css({
					left : "auto",
					right : "0",
					opacity : 0.0,
					width : zoomMax
				})
				    .addClass('active')
				    .animate({opacity: 1.0, width: zoomStepOut}, 0, "linear")
				    .animate({width: "100%"}, settings.time, "linear", function () {
				        $active.removeClass('active last-active');
				    });
				animation = "in";
		    }
		};
	};
})(jQuery);