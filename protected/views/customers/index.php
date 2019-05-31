<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('customers-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="search-form">
<?php $this->renderPartial('_search',array('model'=>$model,)); ?>
</div>
<?php $buttons = array(); $tmp = '';
	if (GroupPermissions::checkPermissions('customers-list', 'write')){
		$tmp = '{update} {delete}';
		$buttons = array(
			'update' => array(
					'label' => Yii::t('translations', 'Edit'),
					'imageUrl' => null,
					'visible' => '$data->id != 0 ',	
			),
			'delete' => array(
					'label' => Yii::t('translations', 'Delete'),
					'imageUrl' => null,
					'visible' => '$data->id != 0 ',	
			),
		);
	}
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'customers-grid',		'dataProvider'=>$model->search(),		'summaryText' => '',
		'selectableRows'=>1, 'selectionChanged'=>'function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "'.$this->createUrl('view').'/"+idg;}',
		'pager'=> Utils::getPagerArray(),	'template'=>'{items}{pager}',
		'columns'=>array(
			'name',
			array(
				'name' => 'cCountry.codelkup',
				'header' => 'Country',
				'value' => 'isset($data->cCountry->codelkup) ? $data->cCountry->codelkup : ""'
			),
			array(
	            'name' => 'status',
	            'value'=>'$data->isActiveAsString()',
	        ),
			'primary_contact_name',
	        'main_phone',
	        'website',
			array(
				'class'=>'CCustomButtonColumn',
				'template'=>$tmp,
				'htmlOptions'=>array('class' => 'button-column'),
	            'buttons'=>$buttons,
			),
	),
)); ?>
<script> function checkStatus(){} </script>