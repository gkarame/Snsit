<?php  Yii::app()->clientScript->registerScript('search', "$('.search-button').click(function(){	$('.search-form').toggle();	return false; }); 
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('users-grid', {	data: $(this).serialize()	});	return false; });"); ?>
<div class="search-form"><?php $this->renderPartial('_search',array('model'=>$model,)); ?></div><?php 	$butoane = array('update' => array('label' => Yii::t('translations', 'Edit'),'imageUrl' => null,));
	$tmpl = '{update}';
	if(GroupPermissions::checkPermissions('users-list','write')){
		$tmpl = '{update} {activate}{deactivate}';
		$butoane = array(
            	'update' => array(
            		'label' => Yii::t('translations', 'Edit'),
            		'imageUrl' => null,		
            	),
                'activate' => array(
                    'label' => Yii::t('translations', 'Activate'),
                    'url' => 'Yii::app()->createUrl("users/changeStatus", array("id"=>$data->id))',  
                	'visible' => '$data->active == 0'  ,
                	'options' => array(
						'ajax' => array(
							'type' => 'get', 
							'url'=>'js:$(this).attr("href")', 
							'success' => 'js:function(data) { $.fn.yiiGridView.update("users-grid");}'
						)
					)              
                ),
                'deactivate' => array(
                    'label' => Yii::t('translations', 'Deactivate'),
                    'url' => 'Yii::app()->createUrl("users/changeStatus", array("id"=>$data->id))',  
                	'visible' => '$data->active == 1',
                	'options' => array(
						'ajax' => array(
							'type' => 'get', 
							'url'=>'js:$(this).attr("href")', 
							'success' => 'js:function(data) { $.fn.yiiGridView.update("users-grid");}'
						)
					)   ),   );	}
	$this->widget('zii.widgets.grid.CGridView', array('id'=>'users-grid','dataProvider'=>$model->search(),'summaryText' => '','selectableRows'=>1,
	'selectionChanged'=>'function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "'.$this->createUrl('update').'/"+idg;}','pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}',
	'columns'=>array('firstname','lastname', array('name' => 'userPersonalDetails.job_title','value' => 'isset($data->userPersonalDetails->job_title) ? $data->userPersonalDetails->job_title : ""'),
		array(	'name' => 'userPersonalDetails.unit',	'value' => 'isset($data->userPersonalDetails->unit) ? Codelkups::getCodelkup($data->userPersonalDetails->unit) : ""'),
		array(	'name' => 'userPersonalDetails.email','value' => 'isset($data->userPersonalDetails->email) ? $data->userPersonalDetails->email : ""'	),
		array(	'name' => 'userPersonalDetails.mobile',	'value' => 'isset($data->userPersonalDetails->mobile) ? $data->userPersonalDetails->mobile : ""'),
		array( 'name' => 'status',  'value'=>'$data->isActiveAsString()','htmlOptions' => array('class' => 'width62'),	'headerHtmlOptions' => array('class' => 'width62'),     ),
		array(	'class' => 'CCustomButtonColumn',	'template'=>$tmpl,	'htmlOptions'=>array('class' => 'button-column','style'=>'text-align:left'),  'buttons'=>$butoane,),	),)); ?>
