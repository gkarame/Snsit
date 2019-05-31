<?php if(GroupPermissions::checkPermissions('projects-tasks','write')){ ?>
<div id="popuptandm" style="width:500px"> <div class='titre red-bold'>Request Offset*</div> <div class='closetandm'> </div>		 
			<div class='tandratecontainer'></div> 		
			<div class='submitandm'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:135px;' ,'onclick' => 'submitOffsets();','id'=>'createbut')); ?>
				<img src="<?php echo Yii::app()->getBaseUrl().'/images/loader.gif';?>" id="img"  style="display:none;padding-top:5px;width:20px;height:20px;"/ ></div></div>
<div class="wrapper_action" id="action_tabs_right">
	<div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
	<div class="action_list actionPanel">  	<div class="headli"></div>	<div class="contentli">		
		<div class="cover">	<div class="li noborder" onclick="checkedAllAll()">SELECT ALL</div>	</div>
			<div class="cover">	<div class="li noborder" onclick="UncheckAll()">DESELECT ALL</div>	</div>
			<div class="cover">	<div class="li noborder" onclick="showPhaseForm(this, true);">NEW PHASE</div>	</div>
			<div class="cover">	<div class="li noborder" onclick="getUsers();">ASSIGN RESOURCES</div>	</div>
			<div class="cover">	<div class="li noborder delete" onclick="getAssignedUsers();">UNASSIGN RESOURCES</div>		</div>
			<div class="cover">	<div class="li noborder delete" onclick="delete_tasks();">DELETE TASKS</div></div>
			<div class="cover">	<div class="li noborder delete" onclick="setOffsetReason();">REQUEST OFFSET</div>	</div>			
		</div>	<div class="ftrli"></div>   </div>   <div id="users-list" style="display:none;"></div>  </div>	<?php }?>
<div class="phases newP"> <?php $phases = Projects::getAllProjectPhases($model->id);foreach ($phases as $ph) {	$this->renderPartial('_body_phase_form', array('model' => $ph)); } ?></div>	
<div cid="projects_items_content"  class="grid border-grid generalTasksTickets bordertop0"><div class="total_amounts totalrow thead" id="foot"> <div>
				<?php $this->renderPartial('_footer_content', array('model' => $model));?></div></div></div>
<script type="text/javascript">		
$(document).ready(function(){	$('#popuptandm').hide();});
 $(".closetandm").click(function() { 	var	id_project=$('.closetandm').attr('id_project'); 
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/deleteOffsets');?>",
	  	dataType: "json",  	data: {'id_project':id_project},
	  	success: function(data) {  	if (data) {  	if (data.status == 'success') { 		$('#popuptandm').hide();  	}  	} 		}	}); });
function checkedAllAll () {	$('[id^="chech_"]').attr('checked',true);	$('[id^="checktask_"]').attr('checked',true); }
function UncheckAll () { $('[id^="chech_"]').attr('checked',false);	$('[id^="checktask_"]').attr('checked',false); }
function checkedAll (id_phase,variable) {
	$("#"+id_phase+"-grid tbody").children().each(function(i) {
		if ($('#chech_'+id_phase).is(':checked'))
			$("#"+id_phase+"-grid input#checktask_"+i).attr('checked',true);
		else
			$("#"+id_phase+"-grid input#checktask_"+i).attr('checked',false);	
});   }
function changeInput(value,id_task,id_phase,field){
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/changeTask');?>",  	dataType: "json",
	  	data: {'id_project':<?php echo $model->id;?>,'id_task':id_task,'value':value,'field':field,'id_phase':id_phase},
	  	success: function(data) {
		  	if (data) {  	if (data.status == 'success') {		$('#man_day_rate-'+id_phase).html(data.totalDays);	$.fn.yiiGridView.update(id_phase+'-grid'); } } }	}); }
function changeInputPhases(value,id_project_phase,field){	
	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/changePhase');?>",
	  	dataType: "json", 	data: {'id_project':<?php echo $model->id;?>,'value':value,'field':field,'id_project_phase':id_project_phase},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {				 
					document.getElementById('total-man-days-budgeted').innerHTML=data.estimatedMDs;	var tot_est=data.total_estimated;
					document.getElementById(id_project_phase+'-total-estimate').innerHTML=tot_est; document.getElementById('total-total-estimate').innerHTML=data.inculdingOffsetMDS;
		  			$(element).parents('.thead').html( data.form );
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
		  			custom_alert('ERROR MESSAGE',data.error, action_buttons);			  
			  	}  	} 		}	}); }
function changeOffset(value,id_project_phase,field){
	if(field==2 && (value=='' || value==' ') ) {
		document.getElementById(id_project_phase+"Off").value="";	changeOffset("",id_project_phase,3);	}
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/changeOffset');?>",
	  	dataType: "json",  	data: {'id_project':<?php echo $model->id;?>,'value':value,'field':field,'id_project_phase':id_project_phase},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {  		if(field==1){setOffsetReason(id_project_phase);}				
			  	}else{	var action_buttons = {
					        "Ok": {
								click: function() 
						        {
						            $( this ).dialog( "close" );
						        },
						        class : 'ok_button'
					        }
						}
					document.getElementById(id_project_phase+"Off").value="";
					custom_alert('ERROR MESSAGE',data.error, action_buttons);  
			  	}  	} }	}); }
function deletePhase(id_phase) {
	var action_buttons = {
        "YES": {
        	click: function() 
	        {
        		$( this ).dialog( "close" );
	        	$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('projects/deletePhase');?>/"+id_phase,dataType: "json",
	    		  	data: {'id_project':<?php echo $model->id;?>,'id_phase':id_phase},
	    		  	success: function(data) {
	    			  	if (data) { if (data.status == 'success') { $('#phase_'+id_phase).remove(); } else if(data.status=='fail'){

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


	    			  	}} } });
	        },
	        class : 'yes_button'
        },
		"NO":{
			click: function() 
	        {
	            $( this ).dialog( "close" );
	        },
	        class : 'no_button'
		}
	}
	custom_alert('DELETE MESSAGE', 'Are you sure you want to delete this phase?', action_buttons);
}
function changeType(element) 
	{
		$this =  $(element);
		if($this.val() == '2')
		{
			$('#descr').removeClass('hidden');
			$('#fbrnb').addClass('hidden');
			$('#titlestr').addClass('hidden');
			$('#complex').addClass('hidden');
			$('#modulestr').addClass('hidden');
			$('#keyw').addClass('hidden');
			$('#pfbr').addClass('hidden');
			$('#fexists').addClass('hidden');
			$('#section').removeClass('extraf');
			$('#bill').addClass('paddigr20');
		}else if($this.val() == '3'){
			$('#descr').addClass('hidden');
			$('#fbrnb').removeClass('hidden');
			$('#titlestr').removeClass('hidden');
			$('#complex').removeClass('hidden');
			$('#modulestr').removeClass('hidden');
			$('#keyw').removeClass('hidden');
			$('#section').removeClass('extraf');
			$('#fexists').addClass('hidden');
			$('#bill').removeClass('paddigr20');
		}else{
			$('#descr').addClass('hidden');
			$('#fbrnb').removeClass('hidden');
			$('#titlestr').removeClass('hidden');
			$('#complex').removeClass('hidden');
			$('#modulestr').removeClass('hidden');
			$('#keyw').removeClass('hidden');
			$('#section').removeClass('extraf');
			changeCategory('#ProjectsTasks_existsfbr');
			$('#fexists').removeClass('hidden');
			$('#bill').removeClass('paddigr20');
		}
	}




function changeCategory(element) {
		$this =  $(element);
		//alert($this.val());
		if($this.val() == '2')
		{
			$('#pfbr').removeClass('hidden');
			//$('#notes').removeClass('paddingl21');
			$('#section').addClass('extraf');
		}else{
			$('#pfbr').addClass('hidden');
		//	$('#notes').addClass('paddingl21');
			$('#section').removeClass('extraf');
		}	

	}
function showItemForm2(element, newItem, id_phase) {
	var url;
	if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('projects/manageTasks');?>"; } else {	url = $(element).attr('href');	}
	$.ajax({type: "POST", 	url: url,  	dataType: "json",  	data: {'id_project':<?php echo $model->id;?>,'id_phase':id_phase},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
				  	if (newItem) { $('.task_'+id_phase).hide(); $('.task_'+id_phase).after(data.form);
				  	} else { $(element).parents('tr').addClass('noback').html('<td colspan="8" class="noback">' + data.form + '</td>'); } 
				  changeType('#ProjectsTasks_type');

				} } }	}); }
function updateTasks(element,id_phase) {
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/updateTasks');?>",	  	dataType: "json",
	  	data: 'id_phase='+id_phase+'&'+$('.task input').serialize()+'&'+$('.task select').serialize()+'&save='+'da',
	  	success: function(data) {
		  	if (data) {  	if (data.status == 'success') { 		$.fn.yiiGridView.update(id_phase+'-grid'); }  	} }	}); }
function getUsers() {
	if (!$('#users-list').is(':visible')) {
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/GetUnssignedUsers');?>", 
		  	dataType: "json",  	data: $('.task').find('input[name!=change]').serialize()+'& id_project= <?php echo $model->id ?>',
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
		$.ajax({type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('projects/assignUsers', array('id'=>$model->id));?>", 
		  	dataType: "json",data:  $('#unassigned-users-form').serialize()+'&'+$('.task').find('input[name!=change]').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") {
		  			$.each( data.id_phases, function( key, value ) { $.fn.yiiGridView.update(value+'-grid'); });		  			
		  			$.each( data.allphases, function( key, value ) {		  				
		  				 $('#input_'+value).removeClass('ram'); $('#'+value).addClass('estez'); $('#'+value).prop('disabled', true); 	});
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
function setOffsetReason(element){
if ($('.task input').serialize()) {
		$.ajax({
	 		type: "POST",
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/SetOffsetReason');?>", 
		  	dataType: "json",
		  	data: 'id_project= <?php echo $model->id ?> &'+$('.task').find('input[name!=change]').serialize(),
		  	success: function(data) {
		  		if (data) {
		  			if(data.status=='success'){
		  				$('.closetandm').attr("id_project",data.id_project); $('.tandratecontainer').html(data.rate_table); $('#popuptandm').stop().show();
		  			}else{
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
		  			}	}}});		
		}else{
				var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', "Please select at least one phase.", action_buttons);
  		}  	}
function updateOffset(id, event){	
	var offset =event.target.value ; var arr=id.split('_');	var phase=arr[1];	
 	var	id_project=$('.closetandm').attr('id_project'); 
 	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/valueOffset');?>",  	dataType: "json",  	data: { 'id_project': id_project, 'offset':offset, 'phase':phase},
		  	success: function(data) {	  	if (data) {	  	} 		}	}); }
function updateReason(id, event){	
	var reason =event.target.value ; var arr=id.split('_');	var phase=arr[1]; 	var	id_project=$('.closetandm').attr('id_project'); 
 	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/reasonOffset');?>",
		  	dataType: "json", 	data: { 'id_project': id_project, 'reason':reason, 'phase':phase},
		  	success: function(data) {  	if (data) {	  	} 		}	}); }
function updateOffsetSign(id, event){	
	var sign =event.target.value ; var arr=id.split('_');	var phase=arr[1]; 	var	id_project=$('.closetandm').attr('id_project'); 
 	$.ajax({	type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/signOffset');?>",
		  	dataType: "json", 	data: { 'id_project': id_project, 'sign':sign, 'phase':phase},
		  	success: function(data) {  	if (data) {	  	} 		}	}); } 
 function submitOffsets(){
 	var	id_project=$('.closetandm').attr('id_project'); $('#img').show();
 	$.ajax({	type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/closeOffsets');?>",
		  	dataType: "json",  	data: { 'id_project': id_project},
		  	success: function(data) {
			  	if (data) {
					  if (data.status == 'success') { 		$('#img').hide(); 		$('#popuptandm').hide();  
						$.each( data.id_phases, function( key, value ) {
			  			var tot_est=0;
			  			$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('projects/getestimated');?>",dataType: "json", data: { 'phase': value},
							  	success: function(data) { if (data) { tot_est= data.estimatedMDs; document.getElementById(value+'-total-estimate').innerHTML=tot_est; } } }); });
			  			 $.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('projects/updateFooter');?>",dataType: "json",data: {'id_project': id_project},
							  	success: function(data) {
								  	if (data) { document.getElementById('total-total-estimate').innerHTML=data.inculdingOffsetMDS; document.getElementById('total-off').innerHTML=data.offsetMDS;		
									document.getElementById('total-offrequests').innerHTML=data.requestsoffs; } } }); 
					}else{			  				
				  	$('#img').hide();
				  		var buttons = {
							        
							        "Ok": {
							        	class: 'no_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
							        }
							}
				  		custom_alert('ERROR MESSAGE', data.message, buttons); } } }	}); }
function setreason(element) {
	 var reason=document.getElementById("offset-text").value; var id_phase=element;	  var offset=document.getElementById(id_phase+'Off').value;	   var offsetsign=document.getElementById(id_phase+'OffSign').value;
	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/setreason', array('id'=>$model->id));?>", 
		  	dataType: "json", 	data: 'reason='+reason+'& id_phase='+id_phase+'& offset='+offset+'& offsetsign='+offsetsign ,
		  		beforeSend: function() {
	  				$("#ajxLoader").fadeIn(); 
      				  },
        		complete: function() {
        			$("#ajxLoader").fadeOut();
       				 },
		  		success: function(data) {
		  		if (data.status == "success") {		  			
		  			$('#offset-reas').fadeOut(100);	document.getElementById(id_phase+'Off').value=" ";
		  			document.getElementById(id_phase+'OffSign').value=" ";	var tot_est=data.total_estimated;
		  			document.getElementById(id_phase+'-total-estimate').innerHTML=tot_est;	document.getElementById('total-total-estimate').innerHTML=data.inculdingOffsetMDS;
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
		  		} 		}	}); }
function cancelreason(element){
	 var id_phase=element;		var originalvalue = document.getElementById(id_phase+'Off').getAttribute('value');		 document.getElementById(id_phase+'Off').value= document.getElementById(id_phase+'Off').getAttribute('value');
 	$('#offset-reas').fadeOut(100); }
function getAssignedUsers() {
	if (!$('#users-list').is(':visible')) {
		$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('projects/GetAssignedUsers');?>",dataType: "json",data: $('.task').find('input[name!=change]').serialize(),
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
	$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('projects/unassignUsers', array('id'=>$model->id));?>", 
	  	dataType: "json",data:  $('#assigned-users-form').serialize()+'&'+$('.task').find('input[name!=change]').serialize(),
	  	success: function(data) {
	  		if (data.status == "success") {
	  			$.each( data.id_phases, function( key, value ) { $.fn.yiiGridView.update(value+'-grid'); });
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
function showPhaseForm(element, newPhase,id_phase) {
	var url;
	if (newPhase) {	url = "<?php echo Yii::app()->createAbsoluteUrl('projects/managePhase');?>";
	} else { url = "<?php echo Yii::app()->createAbsoluteUrl('projects/managePhase');?>/"+id_phase;	}
	$.ajax({type: "POST",  	url: url,  	dataType: "json",  	data: {'id_project':<?php echo $model->id;?>},
	  	success: function(data) {		  	
		  	if (data) {
			  	if (data.status == 'success') {
				  	if (newPhase) {	$('.phases').before(data.form);	$('.action_list').fadeOut(100);
				  	} else {	$(element).parents('.thead').addClass('noback').html('<div class="noback">' + data.form + '</div>'); 	}  	}  	}	}	});}
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
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/deleteTask');?>", 	dataType: "json",  	data: $('.task input').serialize(),
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
			  		$('.action_list').hide();	$.fn.yiiGridView.update('grid');
			  		$.each( data.id_phase, function( key, value ) {
			  			$.fn.yiiGridView.update(value+'-grid');
			  			$.each( data.totalDays, function( key1, value1 ) {
							if(key == key1)
			  					$('#man_day_rate-'+value).html(value1);
				  			}); 		});				  	
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
				}  	} } }); }
		function checkStatus() {};
</script>