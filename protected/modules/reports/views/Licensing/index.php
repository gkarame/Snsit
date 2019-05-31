<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div class="gridApproval">
	<?php $this->renderPartial('html_report', array('Licensing' => $Licensing, 'message' => $message ));?>
</div>

<script> var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; </script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>

