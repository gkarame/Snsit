<div class="mytabs irinfo_edit">
	<div id="irinfo_header" class="edit_header">
	<div class="header_title">	
	<span class="red_title"><?php echo Yii::t('translations', 'IR Info HEADER');?></span>
	 <a class="tabs_extra  extra_edit2" id="extra_edit_info" onclick="showInfoHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('installationrequests/updateinfoheader', array('id' => $infomodel->id)).'"  title="'.Yii::t('translations', 'Edit IR Information')?>">Edit IR Information</a>
	</div>
		<div class="header_content tache">
			<?php $this->renderPartial('_header_content_info', array('model' => $infomodel));?>
		</div>
		<div class="hidden edit_header_content tache new">
		</div>
		<br clear="all" />
	</div>
</div><br clear="all" />
<script>
function showInfoHeader(element){
		var url = $(element).attr('href');
		$.ajax({ type: "POST", url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') { $('.edit_header_content').html(data.html); $('.edit_header_content').removeClass('hidden');
						$('.header_content').addClass('hidden'); } } } });
}
function updateHeader(element) {
		$.ajax({ type: "POST", data: $('#header_info_fieldset').serialize(),					
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('installationrequests/updateinfoheader', array('id' => $infomodel->id));?>", 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved' && data.html) {
				  		$('.header_content').html(data.html); $('.header_content').removeClass('hidden'); $('.edit_header_content').addClass('hidden');
				  	} else { if (data.status == 'success' && data.html) { $('.edit_header_content').html(data.html); } }
			  	} } });
}
</script>