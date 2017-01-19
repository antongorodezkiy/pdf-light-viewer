(function($) {
	$(document).ready(function(){
		
		PdfLightViewer = typeof PdfLightViewer != 'undefined' ? PdfLightViewer : {};
		PdfLightViewer.app = {
			success: function(content) {
				var html = '<div class="updated"><p><i class="icons slicon-check"></i> '+content+'</p></div>';
				$('#wpbody .wrap').prepend(html);
			},
			error: function(content) {
				var html = '<div class="error"><p><i class="icons slicon-close"></i> '+content+'</p></div>';
				$('#wpbody .wrap').prepend(html);
			}
		};

		// import ping
		if (typeof PdfLightViewer != "undefined" && PdfLightViewer.flags.ping_import) {

			PdfLightViewer.ping_import = function() {
				$.post(PdfLightViewer.url.ajaxurl, {"action": "pdf-light-viewer_ping_import"}, function(data) {
					
					try {
						if (typeof(data) == 'string') {
							var json = $.parseJSON(data);
						}
						else {
							var json = data;
						}
						
						
						$(".js-pdf-light-viewer-current-status").html(json.status);
						$(".js-pdf-light-viewer-current-progress").text(json.progress);
						
						if (json.status && json.status != 'failed' && json.status != 'error') {
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
		
		if ( $('.js-pdf-light-viewer-hide-notification').size() ) {
			$('.js-pdf-light-viewer-hide-notification').on('click', function(e) {
				var a = $(this);
				if (a.attr('href') == '#') {
					e.preventDefault();
				}
				
				if (a.parents('.updated').size()) {
          a.parents('.updated').slideUp();
        }
        else {
          a.parents('.error').slideUp();
        }
				
				$.post(PdfLightViewer.url.ajaxurl, {
					'action': 'pdf-light-viewer_notification_viewed',
					'notification': a.data('notification')
				});
			});
		}
    
    if ( $('.js-pdf-light-viewer-cancel-import').size() ) {
			$('.js-pdf-light-viewer-cancel-import').on('click', function(e) {
        e.preventDefault();
        
				var a = $(this);
				
				$.post(PdfLightViewer.url.ajaxurl, {
					'action': 'pdf-light-viewer_cancel_import'
				}, function(data) {
          try {
						if (typeof(data) == 'string') {
							var json = $.parseJSON(data);
						}
						else {
							var json = data;
						}
            
            PdfLightViewer.app.error(json.error);
          }
          catch(error){
						$(".js-pdf-light-viewer-current-status").parents(".updated").slideUp(300);
						PdfLightViewer.app.error(PdfLightViewer.__['Import process failed due to the error:']+' '+error);
					}
        });
			});
		}
    
    if ($('.js-tip').size()) {
      $('.js-tip').qtip({
        style: { classes: 'qtip-tipsy' }
      });
    }
    
	});
})(jQuery);
