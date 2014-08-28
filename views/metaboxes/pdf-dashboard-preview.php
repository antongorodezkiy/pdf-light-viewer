<div id="magazine">
	<?php foreach($pages as $page) { ?>
		<div style="background-image:url(<?php echo $pdf_upload_dir_url.'/'.$page;?>);"></div>
	<?php } ?>
</div>

<style type="text/css">
#magazine{
	width:1152px;
	height:752px;
}
#magazine .turn-page{
	background-color:#ccc;
	background-size:100% 100%;
}
</style>

<script type="text/javascript">

(function($){
	$(window).ready(function() {
		$('#magazine').turn({
							display: 'double',
							acceleration: true,
							gradients: !$.isTouch,
							elevation:50,
							when: {
								turned: function(e, page) {
									/*console.log('Current view: ', $(this).turn('view'));*/
								}
							}
						});
	});
	
	
	$(window).bind('keydown', function(e){
		
		if (e.keyCode==37)
			$('#magazine').turn('previous');
		else if (e.keyCode==39)
			$('#magazine').turn('next');
			
	});
})(jQuery);

</script>
