<?php
/* @var $this TravelController */
/* @var $model Travel */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('travel-grid', {
		data: $(this).serialize()
	});
	return false;
});
"); 
?>

<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php 
	$buttons = array();
	$tmp = '';
	/*if (GroupPermissions::checkPermissions('travel-list', 'write'))
	{*/
		$tmp = '{update} {delete}';
		$buttons = array(
			'update' => array(
					'label' => Yii::t('translations', 'Edit'),
					'imageUrl' => null,
			),
			'delete' => array(
					'label' => Yii::t('translations', 'Delete'),
					'imageUrl' => null,
			),
		);
	/*}*/
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'travel-grid',
		'dataProvider'=>$model->search(),
		'summaryText' => '',
		'selectableRows'=>1,
		'selectionChanged'=>'function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "'.$this->createUrl('view').'/"+idg;}',
		'pager'=> Utils::getPagerArray(),
	    'template'=>'{items}{pager}',
		'columns'=>array(
	
			array(
	            'name' => 'id_user',
				'header' => 'Resource',
	            'value' => 'Users::getUsername($data->idUser->id)'
	        ),
						
			array(
				'name'=>'origincountry.codelkup',
				'header'=> Yii::t('translations', 'Origin Country'),
				'value'	=> 'isset($data->origincountry->codelkup) ? $data->origincountry->codelkup : ""',
			),
			array(
				'name'=>'notes',
				'value'=>'$data->notes'
			),
			
			
		),
)); ?>
