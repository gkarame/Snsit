<?php	$this->breadcrumbs=array('Support Desks'=>array('index'),'Manage',);
$this->menu=array(	array('label'=>'List SupportDesk', 'url'=>array('index')),	array('label'=>'Create SupportDesk', 'url'=>array('create')),);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){	$('.search-form').toggle();	return false; });
$('.search-form form').submit(function(){	$('#support-desk-grid').yiiGridView('update', {	data: $(this).serialize()	});	return false; }); "); ?>
<h1>Manage Support Desks</h1>
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none"><?php $this->renderPartial('_search',array(	'model'=>$model,)); ?></div>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'support-desk-grid',	'dataProvider'=>$model->search(),	'filter'=>$model,	'columns'=>array('id','sd_no','id_customer','severity','status','date',	array('class'=>'CButtonColumn',),),)); ?>
