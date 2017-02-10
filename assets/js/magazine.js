var PDFLightViewerApp;

(function($) {
	
	$(document).ready(function() {
		
		PDFLightViewerApp = {
			self: null,
			
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
          
        // preload images
          self.preloadImages(magazine);
          
        // per page download (need it before init)
          $(document).on('pdf-light-viewer.turned', function(event, data) {
            if ($('.js-pdf-light-viewer-magazine', data.instance).turn('display') == 'single') {
              var pages = [data.page];
              var neighborhood_page = null;
            }
            else {
              var pages = [data.page,data.neighborhood_page];
              var neighborhood_page = data.neighborhood_page;
            }
            
            var href = $('.page.p'+data.page+' .js-pdf-light-viewer-lazy-loading', data.instance).data('original');
            switch (magazine.data('download-page-format')) {
              case 'pdf':
                href = href.substring(0, href.length-3);
                href += 'pdf';
                href = href.replace('/page-', '-pdfs/page-');
                break;
            }
            $('.js-pdf-light-viewer-download-page', data.instance).attr('href', href);
            
            if (neighborhood_page) {
              href = $('.page.p'+data.neighborhood_page+' .js-pdf-light-viewer-lazy-loading', data.instance).data('original');
              switch (magazine.data('download-page-format')) {
                case 'pdf':
                  href = href.substring(0, href.length-3);
                  href += 'pdf';
                  href = href.replace('/page-', '-pdfs/page-');
                  break;
              }
              $('.js-pdf-light-viewer-download-neighborhood-page', data.instance)
                .attr('href', href)
                .show();
            }
            else {
              $('.js-pdf-light-viewer-download-neighborhood-page', data.instance).hide();
            }
          });
				
				var flipbook = magazine.turn({
					display: (magazine.data('page-layout') == 'single' ? 'single' : 'double'),
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
							
              magazine.data('current-page', page);
					
							// Update the current URI
							if (PdfLightViewer.settings.enable_hash_nav) {
                Hash.go('page/' + page).update();
              }
		
							$('.js-pdf-light-viewer-magazine-thumbnails .page-'+currentPage, instance).parent().removeClass('current');
		
							$('.js-pdf-light-viewer-magazine-thumbnails .page-'+page, instance).parent().addClass('current');
		
						},
						turned: function(event, page) {
							
							if (magazine.data('current-page')) {
								page = magazine.data('current-page');
							}
							else {
								magazine.data('current-page', page);
							}
							
							if (
								typeof(page) == "undefined"
								|| page == "undefined"
								|| (
                  PdfLightViewer.settings.enable_hash_nav
									&& window.location.hash
									&& window.location.hash != '#page/'+parseInt(page)
								)
							) {
								return;
							}
							
							var
								is_first_page = (magazine.data('current-page') == 1),
								is_last_page = (magazine.data('pages-count') == magazine.data('current-page')),
								is_left_page = ((magazine.data('current-page') % 2) == 0),
								is_right_page = ((magazine.data('current-page') % 2) != 0),
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
              
              if (parseInt(neighborhood_page) > magazine.data('pages-count')) {
                neighborhood_page = null;
              }
              
              var preloadPages = [
                page,
                page - 1
              ];
              
              if (neighborhood_page) {
                preloadPages.push(neighborhood_page);
                preloadPages.push(neighborhood_page + 1);
              }
              else {
                preloadPages.push(page + 1);
                preloadPages.push(page + 2);
              }
              
              for (var preloadPageKey in preloadPages) {
                var preloadPage = preloadPages[preloadPageKey];
                
                if (typeof preloadPage != 'number') {
                  continue;
                }
                
                if (
                  typeof(loaded_pdf_pages[preloadPage]) == 'undefined'
                  && $('.js-pdf-light-viewer-lazy-loading-'+preloadPage, instance).size()
                ) {
                  $('.js-pdf-light-viewer-lazy-loading-'+preloadPage, instance).lazyload({
                    effect : 'fadeIn',
                    skip_invisible: true
                  })
                  .trigger('lazyload')
                  .trigger('scroll')
                  .trigger('appear'); // to fix cases when image is not loading
                  
                  loaded_pdf_pages[preloadPage] = preloadPage;
                }
              }
              
							self.zoom.initSingle(instance, magazine, page);
              
              $(document).trigger('pdf-light-viewer.turned', {
                instance: instance,
                magazine: magazine,
                page: page,
                neighborhood_page: neighborhood_page
              });
              
              if ($('.js-pdf-light-viewer-current-page-indicator', instance).size()) {
                if (magazine.turn('display') == 'single' || !neighborhood_page) {
                  $('.js-pdf-light-viewer-current-page-indicator', instance).text(page + ' / ' + magazine.data('pages-count'));
                }
                else {
                  if (page < neighborhood_page) {
                    $('.js-pdf-light-viewer-current-page-indicator', instance).text(page + ' - '  + neighborhood_page + ' / ' + magazine.data('pages-count'));
                  }
                  else {
                    $('.js-pdf-light-viewer-current-page-indicator', instance).text(neighborhood_page + ' - '  + page+ ' / ' + magazine.data('pages-count'));
                  }
                }
              }
              
              instance.data('current-page', page);
              instance.data('current-neighborhood-page', neighborhood_page);
							
							self.zoom.initSingle(instance, magazine, neighborhood_page);
						}
					}
				});
				
				// Events for thumbnails
					$('.js-pdf-light-viewer-magazine-thumbnails a.js-page-thumbnail', instance).on('click', function(event) {
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
						$('.js-pdf-light-viewer-fullscreen', instance).click(function(e) {
							e.preventDefault();
							if (screenfull.isFullscreen) {
								screenfull.exit();
							}
							else {
                screenfull.request(instance.get(0));
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
          
        // toolbar arrows
          $('.js-pdf-light-viewer-previous-page', instance).click(function(e) {
            e.preventDefault();
            magazine.turn('previous');
          });
          
          $('.js-pdf-light-viewer-next-page', instance).click(function(e) {
            e.preventDefault();
            magazine.turn('next');
          });
          
          $('.js-pdf-light-viewer-goto-page', instance).on('click', function(e) {
            e.preventDefault();
            var page = parseInt($('.js-pdf-light-viewer-goto-page-input', instance).val());
            if (page) {
              magazine.turn('page', page);
            }
          });
          
          $('.js-pdf-light-viewer-goto-page-input', instance).on('keypress', function(e) {
            if (e.keyCode == 13) {
              var page = parseInt($(this).val());
              if (page) {
                magazine.turn('page', page);
              }
            }
          });
          
        // zoom toggler
          $('.js-pdf-light-viewer-toggle-zoom', instance).on('click', function(e) {
            e.preventDefault();
            
            if (instance.data('enable-zoom')) {
              instance.data('enable-zoom', 0);
              self.zoom.disable(instance);
            }
            else {
              instance.data('enable-zoom', 1);
              self.zoom.enable(instance);
            }
          });
          
        // per page download
          if ($('.js-pdf-light-viewer-download-options', instance).size()) {
            $('.js-pdf-light-viewer-download-options', instance).each(function() {
              var self = $(this),
                  instance = self.parents('.js-pdf-light-viewer');
              self.qtip({
                style: { classes: 'qtip-light pdf-light-viewer-tips' },
                content: {
                  text: $('.js-pdf-light-viewer-download-options-contaner', instance)
                },
                show: 'click',
                hide: {
                  event: 'click'
                },
                position: {
                  my: 'top center',
                  container: instance
                }
              }).on('click', function(e) {
                e.preventDefault();
              });
            });
          }
					
				// window resize
					window.addEventListener('resize', function (e) {
						self.resize(viewport, magazine, ratio_single, ratio_double);
					});
					self.resize(viewport, magazine, ratio_single, ratio_double);
			},
			
      triggerRecalculateAllSizes: function() {
        
        $(window).trigger('resize');
        
        $('.js-pdf-light-viewer').each(function() {
					var instance = $(this);
					
          var
            self = PDFLightViewerApp.self,
            viewport = $('.js-pdf-light-viewer-magazine-viewport', instance),
            magazine = $('.js-pdf-light-viewer-magazine', instance),
            ratio_single = magazine.data('width') / magazine.data('height'),
            ratio_double = (magazine.data('width')*2) / magazine.data('height');
            
          self.resize(viewport, magazine, ratio_single, ratio_double);
          
          instance
            .trigger('lazyload')
            .trigger('scroll')
            .trigger('appear');
				});
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
					height: Math.round(width / ratio)
				};
        
        if (
          magazine.data('max-book-height')
          && size.height > magazine.data('max-book-height')
        ) {
          size = {
            width: magazine.data('max-book-height') * ratio,
            height: magazine.data('max-book-height')
          };
        }
        
        if (magazine.data('limit-fullscreen-book-height')) {
          var fullScreenHeight = $('.js-pdf-light-viewer.pdf-light-viewer-fullscreen .js-pdf-light-viewer-magazine-viewport').height();
          if (fullScreenHeight && size.height > fullScreenHeight) {
            size = {
              width: fullScreenHeight * ratio,
              height: fullScreenHeight
            };
          }
        }
        
				return size;
			},
			
			resize: function(viewport, magazine, ratio_single, ratio_double) {
				
        var page_layout = magazine.data('page-layout');
        
				setTimeout(function() {
					var
					    ratio,
					    size,
					    self = PDFLightViewerApp.self;
				
          if (page_layout == 'single') {
            magazine.turn('display', 'single');
            ratio = ratio_single;
          }
          else if (page_layout == 'double')  {
            magazine.turn('display', 'double');
            ratio = ratio_double;
          }
          
          // adaptive
          else {
            if (screenfull.isFullscreen) {
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
          }
          
					size = self.getViewportSize(viewport.width(), viewport.height(), ratio, magazine);
					
					magazine.turn('size', size.width, size.height);
					
					var
						is_first_page = (magazine.data('current-page') == 1),
						is_last_page = (magazine.data('pages-count') == magazine.data('current-page')),
						is_left_page = ((magazine.data('current-page') % 2) == 0),
						is_right_page = ((magazine.data('current-page') % 2) != 0);
					
					if (is_first_page || (is_last_page && is_left_page)) { 
					
					}
				}, 0);
			},
			
			zoom: {
				initSingle: function(instance, magazine, page) {
					
					if (instance.data('enable-zoom') && instance.data('enable-zoom') == true) {
						var page = $('.page.p'+page, magazine);
						page.zoom({
							url: $('img', page).attr('data-original'),
              magnify: instance.data('zoom-magnify') || 1
						});
					}
				},
        
        enable: function(instance) {
          var
            magazine = $('.js-pdf-light-viewer-magazine', instance);
            
          $('.page', magazine).each(function() {
            var page = $(this);
            page.zoom({
              url: $('img', page).attr('data-original'),
              magnify: instance.data('zoom-magnify') || 1
            });
          });
        },
        
        disable: function(instance) {
          var
            magazine = $('.js-pdf-light-viewer-magazine', instance);
            
          $('.page', magazine).each(function() {
            var page = $(this);
            page.trigger('zoom.destroy');
          });
          
        }
			},
      
      preloadImages: function(magazine) {
        $('img', magazine).each(function() {
          var img = $(this);
          $('<img />').attr('src', img.data('original'));
        });
      }
		};
		PDFLightViewerApp.init();
		
		// hash and keyboard controls only when we have one PDF on the page
		if ($('.js-pdf-light-viewer').size() == 1 && PdfLightViewer.settings.enable_hash_nav) {

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
					if ($('.js-pdf-light-viewer-magazine').turn('is')) {
            $('.js-pdf-light-viewer-magazine').turn('page', 1);
					}
				}
			});
		
		}
	});
	
})(jQuery);
