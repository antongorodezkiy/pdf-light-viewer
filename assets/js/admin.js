(function($) {
	
	$(document).ready(function(){

		// import ping
			if (typeof PdfLightViewer != "undefined" && PdfLightViewer.flags.ping_import) {

				PdfLightViewer.ping_import = function() {
					$.post(PdfLightViewer.url.ajaxurl, {"action": "pdf-light-viewer_ping_import"}, function(data) {
						try {
							var json = $.parseJSON(data);
							
							$(".js-pdf-light-viewer-current-status").text(json.status);
							$(".js-pdf-light-viewer-current-progress").text(json.progress);
							
							if (json.progress) {
								if (json.progress >= 100) {
									$(".js-pdf-light-viewer-current-status").parents(".updated").slideUp(300);
								}
								else {
									PdfLightViewer.ping_import();
								}
							}
						} catch(error){}
					});
				};
				PdfLightViewer.ping_import();
					
			}
	});
	
})(jQuery);
