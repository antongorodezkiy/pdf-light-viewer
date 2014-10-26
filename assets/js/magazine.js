
var PLV_Magazine = {
	
	// Zoom in / Zoom out
	zoomTo: function(event) {
	
		setTimeout(function() {

			if ($('.js-pdf-light-viewer-magazine-viewport').zoom('value')==1) {
				$('.js-pdf-light-viewer-magazine-viewport').zoom('zoomIn', event);
			} else {
				$('.js-pdf-light-viewer-magazine-viewport').zoom('zoomOut');
			}
			
		}, 1);

	},
	
	// Set the width and height for the viewport
	resizeViewport: function() {
	
		var width = $(window).width(),
			height = $(window).height(),
			options = $('.js-pdf-light-viewer-magazine').turn('options');
	
		$('.js-pdf-light-viewer-magazine').removeClass('animated');
	
		$('.js-pdf-light-viewer-magazine-viewport').css({
			width: width,
			height: height
		}).
		zoom('resize');
	
		if ($('.js-pdf-light-viewer-magazine').turn('zoom')==1) {
			var bound = PLV_Magazine.calculateBound({
				width: options.width,
				height: options.height,
				boundWidth: Math.min(options.width, width),
				boundHeight: Math.min(options.height, height)
			});
	
			if (bound.width%2!==0)
				bound.width-=1;
				
			if (bound.width!=$('.js-pdf-light-viewer-magazine').width() || bound.height!=$('.js-pdf-light-viewer-magazine').height()) {
				$('.js-pdf-light-viewer-magazine').turn('size', bound.width, bound.height);
	
				if ($('.js-pdf-light-viewer-magazine').turn('page')==1)
					$('.js-pdf-light-viewer-magazine').turn('peel', 'br');
	
				$('.next-button').css({height: bound.height, backgroundPosition: '-38px '+(bound.height/2-32/2)+'px'});
				$('.previous-button').css({height: bound.height, backgroundPosition: '-4px '+(bound.height/2-32/2)+'px'});
			}
	
			$('.js-pdf-light-viewer-magazine').css({top: -bound.height/2, left: -bound.width/2});
		}
	
		var magazineOffset = $('.js-pdf-light-viewer-magazine').offset(),
			boundH = height - magazineOffset.top - $('.js-pdf-light-viewer-magazine').height(),
			marginTop = (boundH - $('.js-pdf-light-viewer-magazine-thumbnails > div').height()) / 2;
	
		if (marginTop < 0) {
			$('.js-pdf-light-viewer-magazine-thumbnails').css({height:1});
		} else {
			$('.js-pdf-light-viewer-magazine-thumbnails').css({height: boundH});
			$('.js-pdf-light-viewer-magazine-thumbnails > div').css({marginTop: marginTop});
		}
	
		if (magazineOffset.top<$('.made').height())
			$('.made').hide();
		else
			$('.made').show();
	
		$('.js-pdf-light-viewer-magazine').addClass('animated');
	},
	
	
	// Width of the flipbook when zoomed in
	largeMagazineWidth: function() {
		return 2214;
	},
	
	// Calculate the width and height of a square within another square
	calculateBound: function(d) {
		var bound = {width: d.width, height: d.height};
		if (bound.width>d.boundWidth || bound.height>d.boundHeight) {
			var rel = bound.width/bound.height;
			if (d.boundWidth/rel>d.boundHeight && d.boundHeight*rel<=d.boundWidth) {
				bound.width = Math.round(d.boundHeight*rel);
				bound.height = d.boundHeight;
			}
			else {
				bound.width = d.boundWidth;
				bound.height = Math.round(d.boundWidth/rel);
			}
		}
		return bound;
	}
	
};


(function($) {
	
	$(window).bind('keydown', function(e){
		
		if (e.keyCode==37) {
			$('.js-pdf-light-viewer-magazine').turn('previous');
		}
		else if (e.keyCode==39) {
			$('.js-pdf-light-viewer-magazine').turn('next');
		}
	});
	
	$(document).ready(function(){
		
		var loaded_pdf_pages = [];
		
		// magazine
			if ($('.js-pdf-light-viewer-magazine').size()) {
				var flipbook = $('.js-pdf-light-viewer-magazine').turn({
					display: 'double',
					width: 922, // Magazine width
					height: 600, // Magazine height
					duration: 1000, // Duration in millisecond
					acceleration: !(navigator.userAgent.indexOf('Chrome')!=-1), // Hardware acceleration
					gradients: true,
					elevation:50,
					autoCenter: true,
					when: {
						turning: function(event, page, view) {
							
							var book = $(this),
							currentPage = book.turn('page'),
							pages = book.turn('pages');
							
					
							// Update the current URI
							Hash.go('page/' + page).update();
		
							$('.js-pdf-light-viewer-magazine-thumbnails .page-'+currentPage).parent().removeClass('current');
		
							$('.js-pdf-light-viewer-magazine-thumbnails .page-'+page).parent().addClass('current');
		
						},
						turned: function(e, page) {
							
							$(this).turn('center');
							if (page == 1) { 
								$(this).turn('peel', 'br');
							}
							
							if (typeof(page) == "undefined" || page == "undefined") {
								return;
							}
							
							if (typeof(loaded_pdf_pages[page]) == "undefined") {
								$(".js-pdf-light-viewer-lazy-loading").lazyload({
									effect : "fadeIn"
								});
								loaded_pdf_pages[page] = page;
							}
						}
					}
				});
				
				$(".js-pdf-light-viewer-lazy-loading").lazyload({
					effect : "fadeIn"
				});
			
			
		
				// URIs - Format #/page/1 
				Hash.on('^page\/([0-9]*)$', {
					yep: function(path, parts) {
						var page = parts[1];
			
						if (page!==undefined) {
							if ($('.js-pdf-light-viewer-magazine').turn('is'))
								$('.js-pdf-light-viewer-magazine').turn('page', page);
						}
					},
					nop: function(path) {
						if ($('.js-pdf-light-viewer-magazine').turn('is'))
							$('.js-pdf-light-viewer-magazine').turn('page', 1);
					}
				});
			
				// Events for thumbnails
				$('.js-pdf-light-viewer-magazine-thumbnails').click(function(event) {
					var page;
					var css_class = $(event.target).attr('class');
					if (event.target && (page=/page-([0-9]+)/.exec(css_class)) ) {
						$('.js-pdf-light-viewer-magazine').turn('page', page[1]);
					}
				});
			
				$('.js-pdf-light-viewer-magazine-thumbnails li').
					bind($.mouseEvents.over, function() {
						$(this).addClass('thumb-hover');
					}).bind($.mouseEvents.out, function() {
						$(this).removeClass('thumb-hover');
					});
				
				// thumbnails slider
					$('.js-pdf-light-viewer-magazine-thumbnails ul').bxSlider({
						slideWidth: 154,
						minSlides: 2,
						maxSlides: 4,
						slideMargin: 10,
						moveSlides: 2,
						infiniteLoop: false
					});
					
				// pages zoomer
					$(".js-pdf-light-viewer-fullscreen").click(function(e){
						e.preventDefault();
						if ($(document).fullScreen()) {
							$(".js-pdf-light-viewer").removeClass("pdf-light-viewer-fullscreen");
							$(".js-pdf-light-viewer").fullScreen(false);
						}
						else {
							$(".js-pdf-light-viewer").addClass("pdf-light-viewer-fullscreen");
							$(".js-pdf-light-viewer").fullScreen(true);
						}
					});
					
				
					
		}
	});
	
})(jQuery);
