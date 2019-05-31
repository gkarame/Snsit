<?php
/* @var $this MaintenanceController */
/* @var $dataProvider CActiveDataProvider */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('internal-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<div class="search-form" style="overflow: inherit;">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
<?php $searchArray = Utils::getSearchSession();?>
</div>
<?php	
	$tmpl = '{delete}';
		$butoane = array('delete' => array(
            		'label' => Yii::t('translations', 'Delete'),
            		'imageUrl' => null,),);

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'internal-grid',
	'dataProvider'=>$model->search(),
	'summaryText' => '',
	'selectableRows'=>1,
	'selectionChanged'=>'function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "'.$this->createUrl('view').'/"+idg;}',
	'pager'=> Utils::getPagerArray(),
    'template'=>'{items}{pager}',
	'columns'=>array(
		/*array(    
			'class'=>'CCheckBoxColumn',        
			'id'=>'checkinvoice',
			'htmlOptions' => array('class' => 'item checkbox_grid_invoice'),
			'selectableRows'=>2,
		),*/
		array(
		    'name' => 'name',
			'value'=>'$data->name',
			'headerHtmlOptions' => array('class' => 'column200'),
		),
		array(
		    'name' => 'project_manager',
			'value'=>'Users::getNameById($data->project_manager)',
			'headerHtmlOptions' => array('class' => 'column150'),
		),
		array(
		    'name' => 'business_manager',
			'value'=>'Users::getNameById($data->business_manager)',
			'headerHtmlOptions' => array('class' => 'column150'),
		),
		
		array(
		    'name' => 'status',
			'value'=>'Internal::getStatusLabel($data->status)',
			'headerHtmlOptions' => array('class' => 'column120'),
		),
		
		array(
		    'header' => 'TOTAL MDS',
			'value'=>'$data->estimated_effort',
			//'headerHtmlOptions' => array('class' => 'column100'),
		),
		array(
		    'name' => 'actual mds',
			'value'=>'Utils::formatNumber(Internal::getTimeSpentPerProject($data->id))',
			//'headerHtmlOptions' => array('class' => 'column100'),
		),
		array(
		    'name' => 'eta',
			'value'=>'empty($data->eta)? " " : date(\'d/m/Y\', strtotime($data->eta))',
			//'headerHtmlOptions' => array('class' => 'column75'),
		),	
		array(
		    'name' => 'Open Tasks',
			'value'=>'InternalTasks::getOpenTasks($data->id)',
			//'headerHtmlOptions' => array('class' => 'column75'),
		),	
		/*array(
			'class' => 'CCustomButtonColumn',
			'template'=>$tmpl,
			'htmlOptions'=>array('class' => 'button-column','style'=>'text-align:left;'),
            'buttons'=>$butoane,
		),*/
	),
)); ?>

<script type="text/javascript">

</script>