<?php $tmp = ''; $buttons = array();
	$this->widget('zii.widgets.grid.CGridView', array('id'=>'milestones-grid','dataProvider'=>$model->getMilestones(),'summaryText' => '',
	'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
	'columns'=>array(
		array('name'=>'','type'=>'raw','value'=>'(Milestones::getAlert($data->id))? "<img height=\"15\" width=\"15\" onmouseover = \"showAlert($data->id)\" src=\"'.Yii::app()->getBaseUrl(true).'/images/flagm.png'.'\">":""',
            'htmlOptions'=>array('class'=>'column5 item','style'=>'padding-left:5px;'),),
		array('name' => 'idMilestone.description','header' => 'Milestone','value' => 'Milestones::getMilestoneDescriptionTemplate($data->idMilestone->id,$data->id_project)','htmlOptions'=>array('class'=>'red_title')),
		array('name' => 'status','header' => 'Status','type'  => 'raw',
			'value' => '(GroupPermissions::checkPermissions("projects-milestones","write"))? Milestones::getStatus($data->id,$data->status,$data->id_project):$data->status'
		),
		array('name'=>'estimated_date_of_start','type'=>'raw',
            'value'=>'(GroupPermissions::checkPermissions("projects-milestones","write"))? CHtml::textField("change_".$data->id,($data->estimated_date_of_start != "0000-00-00") ? date("d/m/Y",strtotime($data->estimated_date_of_start)):"",array("style"=>"width:70px;text-align:left;border:none;background: url(../../images/calendar.png) no-repeat scroll 0 0 rgba(0, 0, 0, 0);padding-top: 3px;height:20px; padding-left: 30px;color:#555555;font-family:Arial;font-size:12px","onClick"=>"changeDate($data->id)","onchange"=>"changeInputMilestone(value,$data->id,$data->id_project,4)")):CHtml::textField("change_".$data->id,($data->estimated_date_of_start != "0000-00-00" ) ? date("d/m/Y",strtotime($data->estimated_date_of_start)):"",array("style"=>"width:70px;text-align:left;border:none;background: url(../../images/calendar.png) no-repeat scroll 0 0 rgba(0, 0, 0, 0); padding-left: 30px;padding-top: 3px;height:20px;color:#555555;font-family:Arial;font-size:12px"))',
            'htmlOptions'=>array('class'=>'column5 item','style'=>'padding-left:5px'),),
		array('name'=>'estimated_date_of_completion','type'=>'raw',
            'value'=>'(GroupPermissions::checkPermissions("projects-milestones","write"))? CHtml::textField("changeend_".$data->id,($data->estimated_date_of_completion != "0000-00-00") ? date("d/m/Y",strtotime($data->estimated_date_of_completion)):(($data->estimated_date_of_start != "0000-00-00") ? date("d/m/Y",strtotime($data->estimated_date_of_start)):""),array("style"=>"width:70px;text-align:left;border:none;background: url(../../images/calendar.png) no-repeat scroll 0 0 rgba(0, 0, 0, 0);padding-top: 3px;height:20px; padding-left: 30px;color:#555555;font-family:Arial;font-size:12px","onClick"=>"changeDate2($data->id)","onchange"=>"changeInputMilestone(value,$data->id,$data->id_project,2)")):CHtml::textField("change_".$data->id,($data->estimated_date_of_completion != "0000-00-00" ) ? date("d/m/Y",strtotime($data->estimated_date_of_completion)):"",array("style"=>"width:70px;text-align:left;border:none;background: url(../../images/calendar.png) no-repeat scroll 0 0 rgba(0, 0, 0, 0); padding-left: 30px;padding-top: 3px;height:20px;color:#555555;font-family:Arial;font-size:12px"))',
            'htmlOptions'=>array('class'=>'column5 item','style'=>'padding-left:5px'),),
		array('name' => 'last_updated','value' => 'date("d/m/Y", strtotime($data->last_updated))',	),
		array('name' => 'applicable','header' => 'Applicable','type'  => 'raw',
			'value' => '(GroupPermissions::checkPermissions("projects-milestones","write"))? Milestones::getApplicable($data->id,$data->applicable,$data->id_project):$data->applicable'),
		array('class'=>'CCustomButtonColumn',	'template'=>$tmp,'htmlOptions'=>array('class' => 'button-column'),'buttons'=>$buttons),	),)); ?>
<script type="text/javascript">
	function showMilestoneForm(element, newConn) {
		if (false) {	$(element).addClass('invalid');	} else {	$(element).removeClass('invalid');	}		
		if (!$(element).hasClass('invalid')) {	var url;	url = $(element).attr('href');
			$.ajax({ type: "POST", 	url: url, 	data: 'id_project='+<?php echo $model->id;?>, dataType: "json",
			  	success: function(data) {
				  	if (data) {
					  	if (data.status == 'success') { $(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>');
					  	} } } });	} else {	alert('The form is not valid!');	}	}	
	
	
function changeDate(id){	$("input#change_"+id).datepicker({ dateFormat: 'dd/mm/yy' }).datepicker( "show" );	 	$('#ui-datepicker-div').css('top',parseFloat($("input#change_"+id).offset().top) + 25.0);	 	$('#ui-datepicker-div').css('left',parseFloat($("input#change_"+id).offset().left));	}
function changeDate2(id){	$("input#changeend_"+id).datepicker({ dateFormat: 'dd/mm/yy' }).datepicker( "show" ); 	$('#ui-datepicker-div').css('top',parseFloat($("input#changeend_"+id).offset().top) + 25.0); 	$('#ui-datepicker-div').css('left',parseFloat($("input#changeend_"+id).offset().left));	}
function showAlert(id){	
	$.ajax({	type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/showMilestoneAlert');?>/"+id,  	dataType: "json",
	  	success: function(data) {
		  	if (data) {			  	
			  	var action_buttons = {
				        "Ok": {
					click: function() 
					{
					    $( this ).dialog( "close" );
									        },
					     class : 'ok_button'
					}
				}
				custom_alert('ERROR MESSAGE', data.message, action_buttons);
			} }	});	}
</script>