

<div class="mytabs support_edit">
	<div id="support_header" class="edit_header">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'ISSUE INFORMATION');?></span>			
		</div>
		<div class="header_content tache">
			<?php $this->renderPartial('_issue_content', array('model' => $model));?>
		</div>				
		<div class="hidden edit_header_content tache new" style="width:97%"></div>
		<br clear="all" />
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() { });	
</script>