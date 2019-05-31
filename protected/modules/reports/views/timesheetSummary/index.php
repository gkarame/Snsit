<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div class="gridApproval">
	<?php $this->renderPartial('html_report', array('timesheetSummary' => $timesheetSummary, 'message' => $message ));?>
</div>

<script> var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/GetProjectsByClientTimesheetReport');?>';
var getPhasesByProjectUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/GetPhasesByProjectTimesheetReport');?>'; </script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>

