<?php  Yii::app()->clientScript->registerScript('getTasks', "$('.search-form form').submit(function(){	$.fn.yiiGridView.update('phase-grid', {	data: $(this).serialize()	});	return false; });"); ?>
<div class="search-form" style="overflow: inherit;">	
<?php $status = InternalTasks::getStatusContor($model->id);?>
<div class="wide search" id="search-checklists">
<div id="incidents" >	<div class="divp em child" style="    padding-left: 45px;">
<?php  foreach ($status as $key_status => $stat) { ?>	<div onclick="triggerSDSearch('<?php echo $key_status;?>');" class="phase inline-block height85px normal st_<?php echo $key_status; ?>">
					<span class="text" style="margin-top:-1px !important;"><?php echo InternalTasks::getStatus($key_status);?></span>
					<span class="numberp" ><?php echo $stat;?> <span style="color:#989898;"><?php echo "/ ";echo InternalTasks::getTotalTasks($model->id); ?></span></span>	</div>			<?php } ?>	</div></div>

	<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),	'method'=>'get',)); ?>

	<div class="btn"  style="margin-top: 0%;">
	<?php $taskm= new InternalTasks(); ?>
		<div class="row width_common"><div class="selectBg_search"><?php echo $form->label($model,'Task'); ?>
		<span class="spliter"></span><div class="select_container ">
			<?php echo CHtml::activeTextField($taskm,"description",array('prompt' => '')); ?>	</div>	
		</div>	</div>	
		<div class="row width_common"><div class="selectBg_search"><?php echo $form->label($model,'Status'); ?>
		<span class="spliter"></span><div class="select_container " onblur="hidedropdown();" onclick="javascript:showdropdown();">
			<?php echo CHtml::activeDropDownList($taskm,"status",InternalTasks::getStatusList(), array('id'=>'inv_status','prompt'=>'','multiple' => 'multiple','style'=>'z-index: 100; position: absolute;width:105px; height: 100px; overflow: hidden; background-color: white; visibility: visible;')); ?>	</div>	
		</div>	</div>	
		<div class="row width_common"><div class="selectBg_search"><?php echo $form->label($model,'priority'); ?>
		<span class="spliter"></span><div class="select_container ">
			<?php echo CHtml::activeDropDownList($taskm,"priority",InternalTasks::getPriorityList(), array('prompt'=>'')); ?>	</div>	
		</div>	</div>	

		<div style="margin-left: 80%;"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?></div>
			
	</div>
	<div class="horizontalLine search-margin"></div>
<?php $this->endWidget(); ?></div>
</div>
<div id="popetd" > <div class='titre red-bold'></div> 	<div class='closetandm2' style="top: 3px;"> </div>
	<div class='height20'>					
		<div class="row startDateRow">	<?php  echo "ETD Date"; ?>	<div class="dateInput">
			<?php   $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'close_date','cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'start_date','value'=>' '),));?>
			<span class="calendar calfrom"></span>	<?php echo CCustomHtml::error($model,'close_date');  ?>	</div>
		</div>	
	</div> 
	<div class='submitandm'>
		<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-top:-5px !important;margin-left:135px;' ,'onclick' => 'assignetd();return false;','id'=>'createbut')); ?>
		<span class="cancel" style="cursor: pointer;font-size:13px;margin-left:-15px !important;" onclick="$('#popetd').fadeOut();document.getElementById('start_date').value='';">CANCEL</span></div>
</div>

<div class="wrapper_action" id="action_tabs_right" >
	<div onclick="chooseActions();" style="margin-top: -6%;" class="action triggerAction"><u><b>ACTION</b></u></div>
	<div class="action_list actionPanel" style="top: 150px !important;">  	<div class="headli"></div>	<div class="contentli">	
			<div class="cover">	<div class="li noborder" onclick="getetd();">ASSIGN ETD</div>	</div>	
			<div class="cover">	<div class="li noborder" onclick="getUsers();">ASSIGN RESOURCES</div>	</div>
			<div class="cover">	<div class="li noborder delete" onclick="getAssignedUsers();">UNASSIGN RESOURCES</div>		</div>
			<div class="cover">	<div class="li noborder delete" onclick="delete_tasks();">DELETE TASKS</div></div>
			<div class="cover">	<div class="li noborder delete" onclick="getExcel();">EXPORT TO EXCEL</div>		</div>
		</div>	<div class="ftrli"></div>   </div>   <div id="users-list" style="display:none;top: 150px !important;"></div>  </div>	
<div class="phases newP" >
<div cid="projects_items_content" style="    margin-top: 10px;" class="grid border-grid generalTasksTickets bordertop0"  data-id="<?php echo $model->id;?>">

<form class="task" method="post">
<?php	$buttons = array();	$tmp = ''; $tmp = '{update}'; 
				$buttons = array(
			           	'update' => array('label' => Yii::t('translations', ''),'imageUrl' => "../../images/EditPhase.png" ,
							'url' => 'Yii::app()->createUrl("Internal/manageTasks", array("id"=>$data->id))',
							'htmlOptions' => array('style' => 'margin-top:4px;'),'options' => array('onclick' => 'showItemForm2(this);return false;','class' => 'taskbutton'),),
						/*'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => "../../images/DeletePhase.png" ,
							'url' => 'Yii::app()->createUrl("Internal/deleteTask", array("id"=>$data->id))','htmlOptions' => array('style' => 'item checkbox_grid_task'),
			                'options' => array('class' => 'delete',),),*/); 
			$this->widget('zii.widgets.grid.CGridView', array('id'=>'phase-grid','dataProvider'=>$model->getTasks(),'summaryText' => '',
						'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
			'columns'=>array(
				array('class'=>'CCheckBoxColumn','id'=>'checktask','htmlOptions' => array('class' => 'item checkbox_grid_task'),'selectableRows'=>2,),
		        array('name' => 'description','header'=>'Tasks','value' => '$data->renderNumber()','htmlOptions' => array('class' => 'item paddigl0 width154'),),
		        //array('name' => 'users','type'=>'raw','value' => 'InternalTasks::getUsersGrid(InternalTasks::getAllUsersTask($data->id))','htmlOptions' => array('class' => 'internal'),),				
		        array('name' => 'priority','value'=>'InternalTasks::getPriority($data->priority)','htmlOptions' => array('class' => 'width70'),),   
		        array('name' => 'status','value'=>'InternalTasks::getStatus($data->status)','htmlOptions' => array('class' => 'width70'),), 
		        array('name' => 'eta','value'=>'$data->eta','htmlOptions' => array('class' => 'width90'),), 
		        array('name' => 'estimated_effort','value'=>'$data->estimated_effort','htmlOptions' => array('class' => 'width40'),
				'headerHtmlOptions' => array('class' => 'noneb'),), 
		        array('name' => 'actual','value'=>'InternalTasks::getTimeSpent($data->id)','htmlOptions' => array('class' => 'width40'),), 
		       // array('name' => 'close_date','value'=>'($data->close_date == \'0000-00-00 00:00:00\' || $data->close_date == null) ? "": date(\'d/m/Y\', strtotime($data->close_date))','htmlOptions' => array('class' => 'width90'),), 
		        array('name' => 'Assignee','value'=>'InternalTasks::getAllUsersTask($data->id)','htmlOptions' => array('class' => 'width100'),), 
		        array('class'=>'CCustomButtonColumn','template'=>$tmp,'buttons'=>$buttons,),),));  ?>
		        <div class="tache new_item task_<?php echo $model->id;?>">
<div onclick="showItemForm2(this, true,1);" class="newtask">	<u><b>+ <?php echo Yii::t('translations', 'NEW TASK');?></b></u></div></div>
</form></div>
</div>	

<script type="text/javascript">		

(function($){       $(window).load(function(){  hidedropdown();  $('#popetd').hide(); });   })(jQuery);
function showdropdown(){		document.getElementById('inv_status').style.visibility="visible";	}
function hidedropdown(){		document.getElementById('inv_status').style.visibility="hidden";	}
function UncheckAll () { $('[id^="chech_"]').attr('checked',false);	$('[id^="checktask_"]').attr('checked',false); }
function checkedAll (id_phase,variable) {
	id_phase=1;
	$("#phase-grid tbody").children().each(function(i) {
		if ($('#chech_'+id_phase).is(':checked'))
			$("#phase-grid input#checktask_"+i).attr('checked',true);
		else
			$("#phase-grid input#checktask_"+i).attr('checked',false);	
});   }
function triggerSDSearch(status) {	status = parseInt(status);	$('#inv_status').val(status); hidedropdown();	$('.search-btn').trigger('click'); }
function assignetd(){
		var start= ($("#start_date").val()).toString();	
		
		$.ajax({type: "POST",	data: $('.task').find('input[name!=change]').serialize() + '&start='+start,	
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('internal/UpdateEtd');?>", dataType: "json",  	success: function(data) {
				  	if(data.status == 'success'){ 
				  		$('#popetd').fadeOut();
		  				$.fn.yiiGridView.update('phase-grid');
		  				document.getElementById('start_date').value='';
		  			}else {
				  		var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', data.message, action_buttons);
				  	} } });	
}
function getExcel() {	
	$('.action_list').hide();		
window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('projects/GetExcelInternal', array('id' => "$model->id"));?>/?");	}

function showItemForm2(element, newItem, id_phase) {
	id_phase=1;
	var url;
	proj=<?php echo $model->id;?>;
	if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('internal/manageTasks');?>"; } else {	url = $(element).attr('href');	}
	$.ajax({type: "POST", 	url: url,  	dataType: "json",  	data: {'id_internal':<?php echo $model->id;?>,'id_phase':1},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
				  	if (newItem) { $('.task_'+proj).hide(); $('.task_'+proj).after(data.form);
				  	} else { $(element).parents('tr').addClass('noback').html('<td colspan="9" class="noback">' + data.form + '</td>'); } } } }	}); }
function updateTasks(element,id_phase) {
	id_phase=1;
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('internal/updateTasks');?>",	  	dataType: "json",
	  	data: 'id_phase='+id_phase+'&'+$('.task input').serialize()+'&'+$('.task select').serialize()+'&save='+'da',
	  	success: function(data) {
		  	if (data) {  	if (data.status == 'success') { 		$.fn.yiiGridView.update('phase-grid'); }  	} }	}); }
function getetd()
{
	if ($('.task input').serialize()) {
		$('#popetd').stop().show();
	} else {
		var action_buttons = {
	        "Ok": {
				click: function() 
		        {
		            $( this ).dialog( "close" );
		        },
		        class : 'ok_button'
	        }
		}
		custom_alert('ERROR MESSAGE', "You have to select at least one task!", action_buttons);
	}
}
$(".closetandm2").click(function() {	$('#popetd').stop().hide();	});
function getUsers() {
	if (!$('#users-list').is(':visible')) {
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('internal/GetUnssignedUsers');?>", 
		  	dataType: "json",  	data: $('.task').find('input[name!=change]').serialize()+'& id_internal= <?php echo $model->id ?>',
		  	success: function(data) {
		  		if (data.status == "success") { 		$('#users-list').html(data.div); 		$('.action_list').hide(); 		$('#users-list').show(); 		$('#users-list').find('.scroll_div').mCustomScrollbar();
			  	} else {
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
					$('.action_list').hide();
			  	} }	});	} else { $('#users-list').fadeOut(100);	} }
function assignUsers() {
	if ($('#unassigned-users-form').serialize() != '') {
		$.ajax({type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('internal/assignUsers', array('id'=>$model->id));?>", 
		  	dataType: "json",data:  $('#unassigned-users-form').serialize()+'&'+$('.task').find('input[name!=change]').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") {
		  			$.fn.yiiGridView.update('phase-grid');
		  			$('#users-list').fadeOut(100);	UncheckAll(); } }	});
	} else {
		var action_buttons = {
		        "Ok": {
					click: function() 
			        {
			            $( this ).dialog( "close" );
			        },
			        class : 'ok_button'
		        }
			}
			custom_alert('ERROR MESSAGE', 'You have to select at least one user in order to save!', action_buttons);
	} }
function getAssignedUsers() {
	if (!$('#users-list').is(':visible')) {
		$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('internal/GetAssignedUsers');?>",dataType: "json",data: $('.task').find('input[name!=change]').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") {
			  		$('#users-list').html(data.div); $('.action_list').hide(); $('#users-list').show(); $('#users-list').find('.scroll_div').mCustomScrollbar();			  		
		  		}else{
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
						$('.action_list').fadeOut(100);
			  	}	}	});
	} else {	$('#users-list').fadeOut(100); } }
function unassignUsers() {
	$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('internal/unassignUsers', array('id'=>$model->id));?>", 
	  	dataType: "json",data:  $('#assigned-users-form').serialize()+'&'+$('.task').find('input[name!=change]').serialize(),
	  	success: function(data) {
	  		if (data.status == "success") {
	  			$.fn.yiiGridView.update('phase-grid'); 
	  			$('#users-list').fadeOut(100);		UncheckAll();
	  		}else{
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
					$('.action_list').fadeOut(100);
			} }	});	}
function delete_tasks() {
	if ($('.task input').serialize()) {
		buttons = {
		        "YES": {
		        	class: 'yes_button',
		        	click: function() 
			        {
			            $( this ).dialog( "close" );
			            deleteTasks();
			        }
		        },
		        "NO": {
		        	class: 'no_button',
		        	click: function() 
			        {
			            $( this ).dialog( "close" );
			        }
		        }
		}
		custom_alert("DELETE MESSAGE", "Are you sure you want to delete these tasks?", buttons);
	} else {
		var action_buttons = {
	        "Ok": {
				click: function() 
		        {
		            $( this ).dialog( "close" );
		        },
		        class : 'ok_button'
	        }
		}
		custom_alert('ERROR MESSAGE', "You have to select at least one task!", action_buttons);
	}
}
function deleteTasks() {
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('internal/deleteTask');?>", 	dataType: "json",  	data: $('.task input').serialize(),
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
			  		$('.action_list').hide();	$.fn.yiiGridView.update('phase-grid');
			  		 	$('#man_day_rate').html(data.totalDays);		  	
			  	} else {
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
						$('.action_list').hide();
						$.fn.yiiGridView.update('phase-grid');
				}  	} } }); }
</script>