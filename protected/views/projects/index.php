<?php Yii::app()->clientScript->registerScript('search', "$('.search-form form').submit(function(){ $.fn.yiiGridView.update('projects-grid', {	data: $(this).serialize() });	return false; }); "); ?>
<div class="search-form"> <?php $this->renderPartial('_search',array('model'=>$model,)); ?></div>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'projects-grid','dataProvider'=>$model->search(),'summaryText' => '','selectableRows'=>1,
	'selectionChanged'=>'function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "'.$this->createUrl('view').'/"+idg;}',
	'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}','columns'=>array(
		array('name' => 'project.name','header' => 'Project Name','value' => '$data->name','htmlOptions' => array('style' => '    text-transform: none;')),
		array('name' => 'eas.description','header' => 'Type','value' => '($data->type->codelkup == \'Change Request\')? \'CR\': $data->type->codelkup ' , 'htmlOptions' => array('class' => 'column90')),
        array('name' => 'customer.name','header' => 'Customer', 'htmlOptions' => array('class' => 'column110'),'value' => 'isset($data->customer->name) ? $data->customer->name : ""' ),
        array('name' => 'users.name','header' => 'PM', 'htmlOptions' => array('class' => 'column120'),'value' => 'isset($data->projectManager->username) ? $data->projectManager->firstname." ".$data->projectManager->lastname : ""'),
        array('name' => 'curent.phase','header' => 'Current Milestone','value' => '$data->getCurrentMilestone($data->id)'),
        array('name' => 'status','header' => 'Status','value' => 'Projects::getStatusLabel($data->status)'), 
        array('name' => 'ea #','header' => 'ea #','value' => ' Projects::getEAid($data->id)'),),)); ?>
