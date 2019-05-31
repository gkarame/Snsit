<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'milestones-grid','dataProvider'=>$model->milestones,'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
	'columns'=>array(
		array('name' => 'idMilestone.description','header' => 'Milestone','value' => '$data->idMilestone->description'),
		array('name' => 'Status','header' => 'Status','type'  => 'raw','value' => 'Milestones::getStatus($data->id,$data->status)'),
		array('name'=>'estimated_date_of_completion','type'=>'raw','value'=>'(GroupPermissions::checkPermissions("projects-milestones","write"))? CHtml::textField("change_".$data->id,($data->estimated_date_of_completion != null) ? date("d/m/Y",strtotime($data->estimated_date_of_completion)):"",array("style"=>"width:70px;text-align:left;border:none;background:#F0F0F0;color:#555555;font-family:Arial;font-size:12px","onClick"=>"changeDate($data->id)","onchange"=>"changeInput(value,$data->id,1)")):CHtml::textField("change_".$data->id,($data->estimated_date_of_completion != null) ? date("d/m/Y",strtotime($data->estimated_date_of_completion)):"",array("style"=>"width:70px;text-align:left;border:none;background:#F0F0F0;color:#555555;font-family:Arial;font-size:12px"))',
            'htmlOptions'=>array('class'=>'column5 item','style'=>'padding-left:5px'),),
		array('name' => 'last_updated','value' => 'date("d/m/Y", strtotime($data->last_updated))',),
		array('name' => 'applicable',	'type'  => 'raw',	'value' => '$data->applicable',	),	),)); ?>