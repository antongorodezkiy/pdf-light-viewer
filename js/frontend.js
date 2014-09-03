(function($) {
	
	$(window).bind('keydown', function(e){
		
		if (e.keyCode==37) {
			$('.js-pdf-light-viewer-preview').turn('previous');
		}
		else if (e.keyCode==39) {
			$('.js-pdf-light-viewer-preview').turn('next');
		}
	});
	
	$(document).ready(function(){
		
		var loaded_pdf_pages = [];
		
		// magazine
			if ($('.js-pdf-light-viewer-magazine').size()) {
				$('.js-pdf-light-viewer-magazine').turn({
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
							
							console.log(currentPage);
					
							// Update the current URI
							Hash.go('page/' + page).update();
		
							$('.thumbnails .page-'+currentPage).parent().removeClass('current');
		
							$('.thumbnails .page-'+page).parent().addClass('current');
		
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
			}
			
		
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
			$('.thumbnails').click(function(event) {
				var page;
				if (event.target && (page=/page-([0-9]+)/.exec($(event.target).attr('class'))) ) {
					$('.js-pdf-light-viewer-magazine').turn('page', page[1]);
				}
			});
		
			$('.thumbnails li').
				bind($.mouseEvents.over, function() {
					$(this).addClass('thumb-hover');
				}).bind($.mouseEvents.out, function() {
					$(this).removeClass('thumb-hover');
				});
	
			var slider = $('.thumbnails ul').bxSlider({
				slideWidth: 154,
				minSlides: 2,
				maxSlides: 8,
				slideMargin: 10,
				moveSlides: 2,
				infiniteLoop: false
			});
	});
	
})(jQuery);
