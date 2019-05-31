<?php Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('ir-grid', { data: $(this).serialize()	});	return false; }); "); ?>
<div class="search-form" style="overflow: inherit;">
<?php $this->renderPartial('_search',array('model'=>$model,)); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'ir-grid','dataProvider'=>$model->search(),'summaryText' => '','emptyText' => 'No Results Found.',
	'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
	'columns'=>array(
		array('header'=>Yii::t('translations', 'IR #'),'value'=>'$data->renderRequestNumber()','name' => 'ir_number',
			'htmlOptions' => array('class' => 'column50'),'headerHtmlOptions' => array('class' => 'column50'),),
        array('name' => 'eCustomer.name','header' => Yii::t('translations', 'Customer'),'value' => '$data->eCustomer->name',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
		array('name' => 'project','header' => Yii::t('translations', 'Project'),'value' => '($data->maintenance == 0) ? Projects::getNameById($data->project) : Maintenance::getMaintenanceDescription(substr($data->project, 0, -1))',
			'htmlOptions' => array('class' => 'column160'),'headerHtmlOptions' => array('class' => 'column160'),),		
		 array('header'=>Yii::t('translations', 'Created By'),'value'=>'$data->eRequested_by->fullname','name' => 'eRequested_by.fullname',
			'htmlOptions' => array('class' => 'column110'),'headerHtmlOptions' => array('class' => 'column110'),),
		array('header'=>Yii::t('translations', 'Servers Ready On'),'name' => 'expected_starting_date','value' => 'date(\'d/m/Y\',strtotime($data->expected_starting_date))',		
			'htmlOptions' => array('class' => 'column150'),'headerHtmlOptions' => array('class' => 'column150'),),
		array('header'=>Yii::t('translations', 'Delivery Date'),'name' => 'deadline_date','value' => 'date(\'d/m/Y\',strtotime($data->deadline_date))',		
			'htmlOptions' => array('class' => 'column110'),'headerHtmlOptions' => array('class' => 'column110'),),
		array('name' => 'status','value'=>'$data->getStatusLabel($data->status)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
		array('name' => 'assing_to','header' => 'Assigned To','htmlOptions' => array('class' => 'column50'),'type'=>'raw',
            'value' => '(!GroupPermissions::checkPermissions(\'ir-assign-installationrequests\',\'write\'))?  Users::getUsername($data->assigned_to) : InstallationRequests::getAssignToUsers($data->id,$data->assigned_to,$data->status)', ),
		), )); ?>
<script>
function getExcel() {	
			$('.action_list').hide(); window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('installationrequests/getExcel');?>/?"); }
function changeInput(value,id_ir){
	$.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('installationrequests/assigned');?>", dataType: "json", data: {'value':value,'id_ir':id_ir,},
	  	success: function(data) { if (data) { if (data.status == 'success') { console.log('da'); } } }	});
}
</script>