<div class="files exppage" data-toggle="modal-gallery" data-target="#modal-gallery">
	<div class="attachments_pic exppage margint20" ></div>
	<?php $files = $model->getFiles($model->id);
		foreach ($files as $file) { $path_parts = pathinfo($file['path']); ?>
		<div class="box template-download fade" style="padding:9px;">
			<div class="title">
				<a href="<?php echo $this->createUrl('site/download', array('file' => $file['path']));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
			</div>				       	
	       	<div class="size">
	        	<span><?php echo Utils::getReadableFileSize(filesize($file['path']));?></span>
	        </div>
			<div class="delete">
				<button class="btn btn-danger delete"
					 data-customer="<?php echo $model->customer?>"
                     data-filename = "<?php echo $path_parts['basename']?>" 
                     data-id = "<?php echo $model->id?>" 
					data-type="GET">
				</button>
			</div>
		</div>	<?php } ?>	
</div>
<script type="text/javascript">
$(document).ready(function() {
	$("button.delete").click(function(){
	    $.ajax({ type: "GET", data: {'customer':$(this).attr('data-customer'),'filename':$(this).attr('data-filename'),'id':$(this).attr('data-id')},				
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('installationrequests/deleteUpload');?>", dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success' && data.html) { $('#attachament-change').html(data.html); }
				  	showErrors(data.error); } } });	}); });
</script>