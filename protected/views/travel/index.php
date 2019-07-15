<?php   Yii::app()->clientScript->registerScript('search', "$('.search-button').click(function(){	$('.search-form').toggle();	return false; });
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('travel-grid', {	data: $(this).serialize()	});	return false;  }); "); ?>
<div class="search-form"><?php $this->renderPartial('_search',array(	'model'=>$model,)); ?></div>
<?php 	$buttons = array();	$tmp = '';	if (GroupPermissions::checkPermissions('travel-list', 'write')){
		$tmp = '{update} {delete}';
		$buttons = array(
			'update' => array('label' => Yii::t('translations', 'Edit'),	'imageUrl' => null,
			),'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,	),		);	}
	$this->widget('zii.widgets.grid.CGridView', array(	'id'=>'travel-grid',	'dataProvider'=>$model->search(),	'summaryText' => '',	'selectableRows'=>1,
		'pager'=> Utils::getPagerArray(),    'template'=>'{items}{pager}',	'columns'=>array(
			array('header'=>Yii::t('translations', 'Expense#'),'value'=>'$data->renderTravelExpenseNumber()','name' => 'id',),
			array('name'=>'idCustomer.name','header'=> Yii::t('translations', 'Customer'),'value'	=> 'isset($data->idCustomer->name) ? $data->idCustomer->name : ""','htmlOptions' => array('href' =>  'echo Yii::app()->createUrl("view", array("id"=>$data->id))')),
			array('name'=>'idProject.name','header'=> Yii::t('translations', 'Project'),'value'	=> '($data->training!=1) ? Projects::getNamebyId($data->id_project) : Trainings::getName($data->id_project)'),
			array('name' => 'id_user','header' => 'Resource','value' => 'Users::getUsername($data->idUser->id)'),
			array('name'=>'expenseType.codelkup','header'=> Yii::t('translations', 'Expense Type'),'value'	=> 'isset($data->expenseType->codelkup) ? $data->expenseType->codelkup : ""',),
			array('name'=>'amount','value'=>'Utils::formatNumber($data->amount)." USD"'),	array('name'=>'billable','value'=>'ucfirst($data->billable)'),
			array(		'name' => 'status',		'value'=>'$data->getStatusLabel($data->status)',	),
			array(		'class'=>'CCustomButtonColumn',		'template'=>$tmp,	'htmlOptions'=>array('class' => 'button-column'),	'buttons'=>$buttons,	),	),)); ?>
<script type="text/javascript">
function changeInput(value,id_request){
	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('travel/update');?>",  	dataType: "json",
	  	data: {'status':value,'id':id_request},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
			  		console.log('da');
			  	}  	} 		}	});  }
</script>
