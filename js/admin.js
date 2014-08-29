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
			$('.js-pdf-light-viewer-preview').turn({
				display: 'double',
				acceleration: true,
				gradients: !$.isTouch,
				elevation:50,
				autoCenter: true,
				when: {
					turned: function(e, page) {
						/*console.log('Current view: ', $(this).turn('view'));*/
					}
				}
			}).bind("turned", function(event, page, view) {
				
				if (typeof(page) == "undefined" || page == "undefined") {
					return;
				}
				
				if (typeof(loaded_pdf_pages[page]) == "undefined") {
					$(".js-pdf-light-viewer-lazy-loading").lazyload({
						effect : "fadeIn"
					});
					loaded_pdf_pages[page] = page;
				}
				console.log(loaded_pdf_pages);
			});

		
		$(".js-pdf-light-viewer-lazy-loading").lazyload({
			effect : "fadeIn"
		});
		
	});
	
})(jQuery);
