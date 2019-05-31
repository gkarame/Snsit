<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('eas-grid', {	data: $(this).serialize() }); return false;}); "); ?>
<div class="search-form">
<?php $this->renderPartial('_search',array('model'=>$model,)); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'eas-grid','dataProvider'=>$model->search(),'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
	'columns'=>array(
		array(            
            'header'=>Yii::t('translations', 'EA #'), 'value'=>'$data->renderEANumber()', 'name' => 'ea_number', 'htmlOptions' => array('class' => 'column50'),  'headerHtmlOptions' => array('class' => 'column50'),),
		array( 'header'=>Yii::t('translations', 'Customer'), 'value'=>'$data->customer->name', 'name' => 'customer.name', 'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),),
		array( 'header'=>Yii::t('translations', 'Project'), 'name' => 'project.name', 'value' => 'isset($data->project->name) ? $data->project->name : $data->project_n',		 'htmlOptions' => array('class' => 'column90'), 'headerHtmlOptions' => array('class' => 'column90'),),
		array( 'name' => 'status', 'value'=>'$data->getStatusLabel($data->status)', 'htmlOptions' => array('class' => 'column65'), 'headerHtmlOptions' => array('class' => 'column65'),),
		array( 'name' => 'eCategory.codelkup', 'header' => Yii::t('translations', 'Type'), 'value' => 'isset($data->eCategory->codelkup) ? $data->eCategory->codelkup : ""', 'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),),
		array( 'name' => 'amount', 'value' => 'Utils::formatNumber($data->getNetAmount(true))." USD"', 'htmlOptions' => array('class' => 'column90'), 'headerHtmlOptions' => array('class' => 'column90'),),
		array( 'name' => 'eAuthor.fullname', 'header' => Yii::t('translations', 'Author'), 'value' => 'isset($data->eAuthor->fullname) ? $data->eAuthor->fullname : ""', 'htmlOptions' => array('class' => 'column90'), 'headerHtmlOptions' => array('class' => 'column90'),),
        array( 'name' => 'created', 'value' => 'date("d/m/Y", strtotime($data->created))', 'htmlOptions' => array('class' => 'column90'), 'headerHtmlOptions' => array('class' => 'column90'),),
		array( 'class'=>'CCustomButtonColumn', 'template'=>'{download} {share} {delete}', 'htmlOptions'=>array('class' => 'button-column'),
            'buttons'=>array
            ( 	'delete' => array( 'label' => Yii::t('translations', 'Delete'), 'imageUrl' => null, 'url' => 'Yii::app()->createUrl("eas/delete", array("id"=>$data->id))', 'visible' => '$data->status != Eas::STATUS_INVOICED && $data->status != Eas::STATUS_APPROVED  				&& $data->status != Eas::STATUS_PART_INVOICED && $data->status != Eas::STATUS_FULLY_INVOICED',   	), 	'download' => array( 		'label' => Yii::t('translations', 'Print'),  		'imageUrl' => null, 		'url' => 'Yii::app()->createUrl("eas/print", array("id"=>$data->id))', 	), 	'share' => array( 'label' => Yii::t('translations', 'Share'), 'url' => 'Yii::app()->createUrl("site/shareby", array("id"=>$data->id))', 'options' => array( 		'onclick' => 'shareBySubmit(this, "eas");return false;', 		'class'=>'shareby_button', ), 	),
            ),),
	),
)); ?>
