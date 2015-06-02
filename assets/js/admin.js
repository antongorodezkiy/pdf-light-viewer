(function($) {
	$(document).ready(function(){
		
		PdfLightViewer = typeof PdfLightViewer != 'undefined' ? PdfLightViewer : {};
		PdfLightViewer.app = {
			success: function(content) {
				var html = '<div class="updated"><p><i class="fa fa-check"></i> '+content+'</p></div>';
				$('#wpbody .wrap h2').after(html);
			},
			error: function(content) {
				var html = '<div class="error"><p><i class="fa fa-warning"></i> '+content+'</p></div>';
				$('#wpbody .wrap h2').after(html);
			}
		};

		// import ping
		if (typeof PdfLightViewer != "undefined" && PdfLightViewer.flags.ping_import) {

			PdfLightViewer.ping_import = function() {
				$.post(PdfLightViewer.url.ajaxurl, {"action": "pdf-light-viewer_ping_import"}, function(data) {
					try {
						var json = $.parseJSON(data);
						
						$(".js-pdf-light-viewer-current-status").html(json.status);
						$(".js-pdf-light-viewer-current-progress").text(json.progress);
						
						if (json.status != 'failed') {
							if (json.progress >= 100) {
								$(".js-pdf-light-viewer-current-status").parents(".updated").slideUp(300);
								PdfLightViewer.app.success(PdfLightViewer.__['Import process was successfully finished. Please check results on the PDF page.']);
							}
							else {
								PdfLightViewer.ping_import();
							}
						}
						else {
							$(".js-pdf-light-viewer-current-status").parents(".updated").slideUp(300);
							PdfLightViewer.app.error(PdfLightViewer.__['Import process failed due to the error:']+' '+json.error);
						}
						
					}
					catch(error){
						$(".js-pdf-light-viewer-current-status").parents(".updated").slideUp(300);
						PdfLightViewer.app.error(PdfLightViewer.__['Import process failed due to the error:']+' '+error);
					}
				})
				.fail(function() {
					$(".js-pdf-light-viewer-current-status").parents(".updated").slideUp(300);
					PdfLightViewer.app.error(PdfLightViewer.__['Import process failed due to the unknown error.']);
				});
			};
			PdfLightViewer.ping_import();
				
		}
	});
})(jQuery);
