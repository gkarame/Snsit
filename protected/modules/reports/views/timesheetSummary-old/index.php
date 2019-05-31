<?php /*
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('expense-summary-grid', {
		data: $(this).serialize()
	});
	return false;
});
");*/
?>

<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div class="gridApproval">
	<?php $this->renderPartial('html_report', array('expenses' => $expenses,'mes'=>$message));?>
</div>

<script> var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; </script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>
<?php 
Yii::app()->clientScript->registerScript('gridclick', "$('#timesheet-grid table tbody tr td:first-child').click(function()
{
        location.href = '".$this->createUrl('/timesheet/view')."/'+parseInt($(this).text());
});");
?>

