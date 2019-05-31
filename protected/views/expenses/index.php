<?php Yii::app()->clientScript->registerScript('search', "$('.search-button').click(function(){	$('.search-form').toggle();	return false; });
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('expenses-grid', {	data: $(this).serialize() });	return false; }); "); ?>
<div class="search-form">
<?php $this->renderPartial('_search',array(	'model'=>$model, )); ?>
</div>
<?php   $this->widget('zii.widgets.grid.CGridView', array('id'=>'expenses-grid','dataProvider'=>$model->search(),'summaryText' => '',
	'pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}',
	'columns'=>array(
        array( 'header'=>Yii::t('translations', 'SHEET #'), 'value'=>'$data->renderExpensesNumber()',
			'name' => 'expenses_number', 'htmlOptions' => array('class' => 'column50'),  'headerHtmlOptions' => array('class' => 'column50'), ),
		array( 'header' => Yii::t('translations', 'PROJECT NAME'), 'value' =>'($data->training)? Trainings::getName($data->project_id) : Projects::getNameById($data->project_id)', 'name' => 'project.name' ),
		array( 'header'=>Yii::t('translations', 'START DATE'), 'name' => 'startDate', 'value'=>'date("d/m/Y",strtotime($data->startDate))', ),
		array( 'header'=>Yii::t('translations', 'END DATE'), 'name' => 'endDate', 'value'=>'date("d/m/Y",strtotime($data->endDate))', ),
		array( 'header'=>Yii::t('translations', 'STATUS'), 'name' => 'status', 'value'=>'$data->status', ),
		array( 'header'=>Yii::t('translations', 'AMOUNT'), 'name' => 'total_amount', 'value'=>'Utils::formatNumber($data->total_amount)', ),
		array( 'header'=>Yii::t('translations', 'AMOUNT BILLABLE'), 'name' => 'billable_amount', 'value'=>'Utils::formatNumber($data->billable_amount)', ),
		array( 'header'=>Yii::t('translations', 'AMOUNT PAYABLE'), 'name' => 'payable_amount', 'value'=>'Utils::formatNumber($data->payable_amount)', ),
		array( 'class'=>'CCustomButtonColumn',
			'template'=>'{print} {submit} {update} {delete}',
			'htmlOptions'=>array('class' => 'button-column'), 'buttons'=>array (
				'print' => array(
					'label' => Yii::t('translations', 'Print'), 'imageUrl' => null, 'url' => 'Yii::app()->createUrl("expenses/print", array("id"=>$data->id))',
				), 	
				'update' => array('label' => Yii::t('translations', 'Edit'), 'imageUrl' => null,
					'url' => 'Yii::app()->createUrl("expenses/update", array("id"=>$data->id))',
					'visible' => '$data->status == Expenses::STATUS_REJECTED || $data->status == Expenses::STATUS_NEW', ),
				'delete' => array(	'label' => Yii::t('translations', 'Delete'), 'imageUrl' => null,
					'url' => 'Yii::app()->createUrl("expenses/delete", array("id"=>$data->id))',
					'visible' => '$data->status == Expenses::STATUS_REJECTED || $data->status == Expenses::STATUS_NEW', ),
				'submit' => array(
					'label' => Yii::t('translations', 'Submit'), 'imageUrl' => null,
					'url' => 'Yii::app()->createUrl("expenses/UpdateHeaderList", array("id"=>$data->id))',
					'visible' => '($data->status == Expenses::STATUS_REJECTED || $data->status == Expenses::STATUS_NEW) && (Timesheets::getPendingTimesheetsCount2($data->startDate, $data->endDate)==0)',
					'options' => array( 'onclick' => '$this->redirect(array("expenses/index"));' ),	), ),
		),	), ));  ?>		
<script> var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; </script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>
<?php Yii::app()->clientScript->registerScript('gridclick', "$('#expenses-grid table tbody tr td:first-child').click(function()
{
        location.href = '".$this->createUrl('/expenses/view')."/'+parseInt($(this).text());
});"); ?>