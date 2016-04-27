(function($) {
	
	$(document).ready(function() {
		
		PDFLightViewerApp = {
			self: null,
			page: 0,
			pages_count: 0,
			
			init: function() {
				var self = this;
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
				var
          self = PDFLightViewerApp.self,
					viewport = $('.js-pdf-light-viewer-magazine-viewport', instance),
					magazine = $('.js-pdf-light-viewer-magazine', instance),
					ratio_single = magazine.data('width') / magazine.data('height'),
					ratio_double = (magazine.data('width')*2) / magazine.data('height'),
					loaded_pdf_pages = [];
					
				PDFLightViewerApp.pages_count = magazine.data('pages-count');
				
				var flipbook = magazine.turn({
					display: (magazine.data('force-one-page-layout') ? 'single' : 'double'),
					width: (magazine.data('width') * 2), // Magazine width
					height: magazine.data('height'), // Magazine height
					duration: 1000, // Duration in millisecond
					acceleration: !(navigator.userAgent.indexOf('Chrome')!=-1), // Hardware acceleration
					gradients: true,
					elevation: 50,
					autoCenter: false,
					when: {
						turning: function(event, page, view) {
							
							var book = $(this),
							currentPage = book.turn('page'),
							pages = book.turn('pages');
							
							PDFLightViewerApp.page = page;
					
							// Update the current URI
							Hash.go('page/' + page).update();
		
							$('.js-pdf-light-viewer-magazine-thumbnails .page-'+currentPage, instance).parent().removeClass('current');
		
							$('.js-pdf-light-viewer-magazine-thumbnails .page-'+page, instance).parent().addClass('current');
		
						},
						turned: function(event, page) {
							
							if (PDFLightViewerApp.page) {
								page = PDFLightViewerApp.page;
							}
							else {
								PDFLightViewerApp.page = page;
							}
							
							if (
								typeof(page) == "undefined"
								|| page == "undefined"
								|| (
									window.location.hash
									&& window.location.hash != '#page/'+parseInt(page)
								)
							) {
								return;
							}
							
							var
								is_first_page = (PDFLightViewerApp.page == 1),
								is_last_page = (PDFLightViewerApp.pages_count == PDFLightViewerApp.page),
								is_left_page = ((PDFLightViewerApp.page % 2) == 0),
								is_right_page = ((PDFLightViewerApp.page % 2) != 0),
								neighborhood_page = null;
							
							if (is_first_page || (is_last_page && is_left_page)) { 
								$(this).turn('peel', 'br');
								neighborhood_page = null;
							}
							else {
								magazine.css('margin-left', 'inherit');
								if (is_left_page) {
									neighborhood_page = page + 1;
								}
								else {
									neighborhood_page = page - 1;
								}
							}
						
							if (typeof(loaded_pdf_pages[page]) == 'undefined') {
								$('.js-pdf-light-viewer-lazy-loading-'+page, instance).lazyload({
									effect : 'fadeIn',
									skip_invisible: false
								})
								.trigger('lazyload')
								.trigger('scroll')
								.trigger('appear'); // to fix cases when image is not loading
								
								loaded_pdf_pages[page] = page;
							}
							self.zoom.initSingle(instance, magazine, page);
							
							if (
								neighborhood_page
								&& typeof(loaded_pdf_pages[neighborhood_page]) == 'undefined'
							) {
								$('.js-pdf-light-viewer-lazy-loading-'+neighborhood_page, instance).lazyload({
									effect : 'fadeIn',
									skip_invisible: false
								})
								.trigger('lazyload')
								.trigger('scroll')
								.trigger('appear'); // to fix cases when image is not loading
								
								loaded_pdf_pages[neighborhood_page] = neighborhood_page;
							}
							self.zoom.initSingle(instance, magazine, neighborhood_page);
						}
					}
				});
				
				// Events for thumbnails
					$('.js-pdf-light-viewer-magazine-thumbnails a', instance).on('click', function(event) {
						event.preventDefault();
						
						var page;
						var css_class = $(event.currentTarget).attr('class');
						if (event.currentTarget && (page=/page-([0-9]+)/.exec(css_class)) ) {
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
						infiniteLoop: false,
						prevText: '<i class="icons slicon-arrow-left-circle"></i>',
						nextText: '<i class="icons slicon-arrow-right-circle"></i>'
					});
					
				// pages fulscreen
					if (screenfull.enabled) {
						$('.js-pdf-light-viewer-fullscreen', instance).click(function(e){
							e.preventDefault();
							if (screenfull.isFullscreen) {
								screenfull.exit();
							}
							else {
                screenfull.request(instance.parent()[0]);
							}
						});
						
						$(document).bind(screenfull.raw.fullscreenchange, function() {
							if (screenfull.isFullscreen) {
								instance.addClass('pdf-light-viewer-fullscreen');
							}
							else {
								instance.removeClass('pdf-light-viewer-fullscreen');
							}
							
						});
					}
					else {
						// if not supported
						$('.js-pdf-light-viewer-fullscreen', instance).parent().remove();
					}
					
				// window resize
					window.addEventListener('resize', function (e) {
						self.resize(viewport, magazine, ratio_single, ratio_double);
					});
					self.resize(viewport, magazine, ratio_single, ratio_double);
			},
			
			getViewportSize: function(width, height, ratio, magazine) {
				
				width -= 20;
        
        if (
          magazine.data('max-book-width')
          && width > magazine.data('max-book-width')
        ) {
          width = magazine.data('max-book-width') - 20;
        }
				
				var size = {
					width: width,
					height: Math.round(width / ratio),
				};
			
				return size;
			},
			
			resize: function(viewport, magazine, ratio_single, ratio_double) {
				
        var force_single = magazine.data('force-one-page-layout');
        
				setTimeout(function() {
					var
					    ratio,
					    size,
					    self = PDFLightViewerApp.self;
				
					if (screenfull.isFullscreen && !force_single) {
						magazine.turn('display', 'double');
						ratio = ratio_double;
					}
					else {
						if (viewport.width() >= 800 && !force_single) {
							magazine.turn('display', 'double');
							ratio = ratio_double;
						}
						else {
							magazine.turn('display', 'single');
							ratio = ratio_single;
						}
					}
					size = self.getViewportSize(viewport.width(), viewport.height(), ratio, magazine);
					
					magazine.turn('size', size.width, size.height);
					
					var
						is_first_page = (PDFLightViewerApp.page == 1),
						is_last_page = (PDFLightViewerApp.pages_count == PDFLightViewerApp.page),
						is_left_page = ((PDFLightViewerApp.page % 2) == 0),
						is_right_page = ((PDFLightViewerApp.page % 2) != 0);
					
					if (is_first_page || (is_last_page && is_left_page)) { 
					
					}
				}, 0);
			},
			
			zoom: {
				initSingle: function(instance, magazine, page) {
					
					if (instance.data('enable-zoom') && instance.data('enable-zoom') == true) {
						var page = $('.page.p'+page, magazine);
						page.zoom({
							url: $('img', page).attr('data-original')
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
		
					if (page !== undefined) {
						if ($('.js-pdf-light-viewer-magazine').turn('is')) {
							$('.js-pdf-light-viewer-magazine').turn('page', page);
						}
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
