
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
	
	$(document).ready(function() {
		
		PDFLightViewerApp = {
			self: null,
			
			init: function() {
				self = this;
				PDFLightViewerApp.self = self;
				
				if (!$('.js-pdf-light-viewer').size()) {
					return;
				}
				
				$('.js-pdf-light-viewer').each(function() {
					var instance = $(this);
					self.magazine(instance);
				});
			},
			
			magazine: function(instance) {
				var viewport = $('.js-pdf-light-viewer-magazine-viewport', instance);
				var magazine = $('.js-pdf-light-viewer-magazine', instance);
				var ratio_single = magazine.data('width') / magazine.data('height');
				var ratio_double = (magazine.data('width')*2) / magazine.data('height');
				var loaded_pdf_pages = [];
				var flipbook = magazine.turn({
					display: 'double',
					width: (magazine.data('width') * 2), // Magazine width
					height: magazine.data('height'), // Magazine height
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
		
							$('.js-pdf-light-viewer-magazine-thumbnails .page-'+currentPage, instance).parent().removeClass('current');
		
							$('.js-pdf-light-viewer-magazine-thumbnails .page-'+page, instance).parent().addClass('current');
		
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
								$(".js-pdf-light-viewer-lazy-loading", instance).lazyload({
									effect : "fadeIn",
									skip_invisible: true
								})
								.trigger("lazyload")
								.trigger('scroll')
								.trigger('appear'); // to fix cases when image is not loading
								
								loaded_pdf_pages[page] = page;
							}
						}
					}
				});
				
				// lazyload
					$(".js-pdf-light-viewer-lazy-loading", instance).lazyload({
						effect : "fadeIn",
						skip_invisible: true
					})
					.trigger("lazyload")
					.trigger('scroll')
					.trigger('appear');
				
				// Events for thumbnails
					$('.js-pdf-light-viewer-magazine-thumbnails', instance).click(function(event) {
						var page;
						var css_class = $(event.target).attr('class');
						if (event.target && (page=/page-([0-9]+)/.exec(css_class)) ) {
							magazine.turn('page', page[1]);
						}
					});
				
					$('.js-pdf-light-viewer-magazine-thumbnails .slide', instance)
						.bind($.mouseEvents.over, function() {
							$(this).addClass('thumb-hover');
						})
						.bind($.mouseEvents.out, function() {
							$(this).removeClass('thumb-hover');
						});
				
				// thumbnails slider
					$('.js-pdf-light-viewer-magazine-thumbnails ul', instance).bxSlider({
						slideWidth: 154,
						minSlides: 1,
						maxSlides: 6,
						slideMargin: 10,
						moveSlides: 2,
						infiniteLoop: false
					});
					
				// pages fulscreen
					if ($(document).fullScreen() != null) {
						$(".js-pdf-light-viewer-fullscreen", instance).click(function(e){
							e.preventDefault();
							if ($(document).fullScreen()) {
								instance.removeClass("pdf-light-viewer-fullscreen");
								instance.fullScreen(false);
								self.zoom.init(instance, magazine);
							}
							else {
								instance.addClass("pdf-light-viewer-fullscreen");
								instance.fullScreen(true);
								self.zoom.destroy(instance, magazine);
							}
						});
						
						$(document).bind("fullscreenchange", function() {
							self.resize(viewport, magazine, ratio_single, ratio_double);
						});
					}
					else {
						// if not supported
						$(".js-pdf-light-viewer-fullscreen", instance).remove();
					}
					
				// pages zoomer
					self.zoom.init(instance, magazine);
					
				// window resize
					window.addEventListener('resize', function (e) {
						self.resize(viewport, magazine, ratio_single, ratio_double);
					});
					self.resize(viewport, magazine, ratio_single, ratio_double);
			},
			
			getViewportSize: function(width, height, ratio) {
				if ($(document).fullScreen()) {
					var size = {
						width: Math.round(height * ratio),
						height: height,
					};
				}
				else {
					var size = {
						width: width,
						height: Math.round(width / ratio),
					};
				}
				
				return size;
			},
			
			resize: function(viewport, magazine, ratio_single, ratio_double) {
				
				var ratio;
				
				if ($(document).fullScreen()) {
					magazine.turn('display', 'double');
					ratio = ratio_double;
				}
				else {
					if (viewport.width() >= 800) {
						magazine.turn('display', 'double');
						ratio = ratio_double;
					}
					else {
						magazine.turn('display', 'single');
						ratio = ratio_single;
					}
				}
				size = self.getViewportSize(viewport.width(), viewport.height(), ratio);
				
				magazine.turn('size', size.width, size.height);
			},
			
			zoom: {
				init: function(instance, magazine) {
					if (instance.data('enable-zoom') && instance.data('enable-zoom') == true) {
						$('.page', magazine).each(function() {
							var page = $(this);
							page.zoom({
								url: $('img', page).attr('data-original')
							});
						});
					}
				},
			
				destroy: function(instance, magazine) {
					if (instance.data('enable-zoom') && instance.data('enable-zoom') == true) {
						$('.page', magazine).each(function() {
							var page = $(this);
							page.trigger('zoom.destroy');
						});
					}
				}
			}
		};
		PDFLightViewerApp.init();
		
		// hash and keyboard controls only when we have one PDF on the page
		if ($('.js-pdf-light-viewer').size() == 1) {

			$(window).bind('keydown', function(e){
		
				if (e.keyCode == 37) {
					$('.js-pdf-light-viewer-magazine').turn('previous');
				}
				else if (e.keyCode == 39) {
					$('.js-pdf-light-viewer-magazine').turn('next');
				}
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
		
		}
	});
	
})(jQuery);
