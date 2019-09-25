<div class="mytabs support_edit">	<div id="support_header" class="edit_header">	<div class="header_title">	<span class="red_title"><?php echo Yii::t('translations', 'INCIDENT HEADER');?></span>
		

			<div class="btn" onclick="js:hidedropdown();" style="margin-top: -20px;">
			<div class="wrapper_action" id="action_tabs_right"><div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel"><div class="headli"></div><div class="contentli">
						<div class="cover"><div class="li noborder"><a  href="<?php echo $this->createUrl('supportDesk/getExcel', array('id' => $model->id));?>">Export to Excel</a></div></div>
						<?php if(Yii::app()->user->isAdmin){?> 
						<?php if(SupportRequest::getRSR($model->id) == null && ((Users::checkCSManagers(Yii::app()->user->id)) > 0 /* || (Users::checkLead(Yii::app()->user->id)) > 0*/) ){?> 
						<div class="cover" id="rsr"><div class="li noborder">	<a  href="javascript:CreateRSR();">Create as RSR</a></div></div>
						<?php if(SupportRequest::getRSR($model->id) == null && Users::checkCSManagers(Yii::app()->user->id) > 0 ){?>
						<div class="cover"  id="rsr2" ><div class="li noborder">	<a href="javascript:SpecifyRSR();">Link to RSR</a></div></div>
						<?php } } ?>
						<div class="cover"><div class="li noborder"><a href="<?php echo $this->createUrl('deployments/create',array('id' => $model->id));?>">Create Deployment</a> </div></div>
					<?php } ?>
				</div><div class="ftrli"></div></div><div id="users-list" style="display:none;"></div></div></div>



		</div>	<div id="rate_ticket" style="display:none;"></div>	<div class="header_content tache">	<?php $this->renderPartial('_header_content', array('model' => $model));?>
		</div>	<div class="hidden edit_header_content tache new" style="width:97%"></div>	<br clear="all" />	</div>	<div class="supportdesk_attache" id="attachament-change">
		<?php $this->renderPartial('_attachements',array(	'model'=>$model,	)); ?>	<br clear="all"/>	</div>	<div class="supportdesk_comm_table">
		<?php $this->renderPartial('_posts_table',array('model'=>$model,	)); ?></div><?php if($model->status != SupportDesk::STATUS_CONFIRME_CLOSED){?>	<div class="supportdesk_comm">
			<?php $this->renderPartial('_comment',array('model'=>$model,	)); ?>	</div>	<?php  }?></div>

			<div id="popuprsr"> <div class='titre red-bold'>Specify RSR</div> 
			<div class='closereason' style='    margin-left: 250px !important; '> </div><div class='reasoncontainer'>	
			<?php echo CHtml::activeDropDownList($model,"escalation_reason",SupportRequest::getOpenRsrs($model->id_customer), array('prompt'=>'','style'=>'margin-top:3px; width: 95%;','id'=>'rsrid')); 	?>
			</div> 	<div class='submitreason'>	<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:170px; margin-top:10px;' ,'onclick' => 'linkrsr();return false;','id'=>'createbut')); ?>
	</div></div>


			<div id="popuprsrcreate"> <div class='titre red-bold'>Create RSR</div> 
			<div class='closereason' style='    margin-left: 250px !important; '> </div><div class='reasoncontainer'>	
			<?php echo 'Title' ;	?><textarea id="rsrdesc" style="width:95%;height:15px;resize:none;margin-bottom: 8px;margin-top:2px;" name="rsrdesc"></textarea>
			<?php echo 'Description' ;	?><textarea id="rsrcomm" style="width:95%;height:60px;resize:none;margin-bottom: 8px;margin-top:2px;" name="rsrcomm"></textarea>
			<?php echo 'Category' ; echo CHtml::activeDropDownList($model,"escalation_reason",SupportRequest::getCategoryList(), array('prompt'=>'','style'=>'margin-top:3px; width: 97%;','id'=>'rsrcat')); 	?>

			</div> 	<div class='submitreason'>	<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:170px; margin-top:10px;' ,'onclick' => 'createactualrsr();return false;','id'=>'createbut')); ?>
	</div></div>


			<div id="popupreaason"> <div class='titre red-bold'>Specify Reason</div> 
			<div class='closereason'> </div><div class='reasoncontainer'>	<textarea id="reopen_message" style="width:270px;height:120px;resize:none;" name="reopen_message"></textarea>
			</div> 	<div class='submitreason'>	<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px; margin-top:10px;' ,'onclick' => 'SetReason();return false;','id'=>'createbut')); ?>
	</div></div>

	<div id="popupavoid"> <div class='titre red-bold'>How can we Avoid this issue?</div> <div class='closereason'> </div><div class='reasoncontainer'>
				<textarea id="solution_message" style="width:270px;height:120px;resize:none;" name="solution_message"></textarea></div> 
					<div class='submitreason'>	<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px; margin-top:10px;' ,'onclick' => 'Setavoid();return false;','id'=>'createbut')); ?>	
					</div></div>

<div id="popupescalate"> <div class='titre red-bold'>Please specify the escalation reason:</div> <div class='closereason'> </div><div class='escalatecontainer'>
				<textarea id="escalate_message" style="width:270px;height:120px;resize:none;" name="escalate_message"></textarea></div> 	<div class='submitreason'>	<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px; margin-top:10px;' ,'onclick' => 'SetEscalation();return false;','id'=>'createbut')); ?>	</div></div>				
<script type="text/javascript">
$(document).ready(function() {
	$(".sep1").hide();	$(".sep2").hide();	$(".sep3").hide();	$(".support_desk_label").hide();	$(".root_cause").hide();	$( "span.attachFile" ).addClass(" support_file");
	$('#popupreaason').hide(); $('#popuprsr').hide(); $('#popuprsrcreate').hide();		$('#popupavoid').hide(); $('#popupescalate').hide();
	var id="<?php echo $model->id;?>";	var url = "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/VacationLicenseAudit');?>";
	$.ajax({type: "POST",data: {id:id},url: url, 	dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.valid == '0') {
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Please perfom a License Audit for this customer! Notice you will not be able to close any issue related to this customer before doing so!', action_but);	
			  	}
			  	if (data.valid == '2') {
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				//custom_alert('ERROR MESSAGE', 'Please update customer connection tab before closing this SR. It is either empty or not updated since 3 months.', action_but);	
				custom_alert('ERROR MESSAGE', 'Please update customer connection tab before closing this SR, it is empty.', action_but);	
			  	}  	}		} });
	$.ajax({type: "POST",	data: {id:id},	url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/updateCheckedUsers');?>", dataType: "json",
	  	success: function(data) { 	if (data) {	  	}	}	});	});
	 $(".closereason").click(function() {		$('#popupreaason').hide(); $('#popuprsr').hide();	$('#popuprsrcreate').hide();		$('#popupavoid').hide(); 	$('#popupescalate').hide(); 	});	 
$('.followUpButton').click(function() {
	var action_buttons = {
        "Ok": {
        	class: 'ok_button',
        	click: function() {
	            $( this ).dialog( "close" );
	        }
        }
		};
	send = "supportdesk-grid_c0[]=<?php echo $model->id;?>&update=1";
	if (send.length == 0) {
		custom_alert('ERROR MESSAGE', 'Please select at least one SR', action_buttons);
	} else {
		custom_alert('', 'Are you sure you want to send follow-ups?', {
	        "Ok": {
	        	class: 'ok_button',
	        	click: function() {
	        		var dialog = $(this);
	        		$.ajax({
						type: "POST",
						url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/sendFollowUp');?>",
						dataType: "json",
						data: send,
						success: function(data) {
							if (typeof data != 'undefined' && typeof data.status != 'undefined' && data.status == 'success') {
								dialog.dialog( "close" );
								if (typeof data.num_of_followups != 'undefined') {
									$('.num_of_followups').html(data.num_of_followups); 
								}
								if (typeof data.last_followup_date != 'undefined') {
									$('.last_followup_date').html(data.last_followup_date);
								}
							}
						}			
					});
		        }
	        }, 
			"No": {
				class: 'no_button',
				click: function() {$(this).dialog("close");}
			}
  		});
	}
});
function ChangeReason(reason,sr){
	$.ajax({type: "POST",data: {'reason':reason,'sr':sr},	url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/checkReassign');?>",
	  	dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
			  		buttons = {
			  			        "CONFIRM": {
			  			        	class: 'yes_button',
			  			        	click: function() 
			  				        {
			  				            $( this ).dialog( "close" );
			  				          	changeInput(data.user, sr, 2);
			  				          	
			  				          		$("#assigned_to_txt").html(data.uname);
			  				          	$("#assigned_to").val(data.user);			  				          		
			  				        }
			  			        },
			  			        "CANCEL": {
			  			        	class: 'no_button',
			  			        	click: function() 
			  				        {
			  				            $( this ).dialog( "close" );
			  				          	/*$(".sep1").hide();
										$(".sep2").hide();
										$(".sep3").hide();

										$(".support_desk_label").hide();
										$(".root_cause").hide();
										
										$('#popupreaason').hide(); $('#popuprsr').hide();	$('#popuprsrcreate').hide();		
										$('#popupavoid').hide();  $('#popupescalate').hide(); */
			  				        }
			  			        }
			  			}
			  			custom_alert("ALERT MESSAGE", data.message, buttons);
			  
    		//	 $('#post_button').removeClass('hidden');
    		//	 $('#pending_info').removeClass('hidden');
    		//	 $('#close_red').removeClass('hidden');
		  	}else if (data.status == 'done')
		  	{
		  		/*if(data.hideout == 'true')
		  		{
		  			 $('#post_button').addClass('hidden');		  		 
			  		 $('#pending_info').addClass('hidden');		  		 
			  		 $('#close_red').addClass('hidden');
		  		}*/

			  				          		$("#assigned_to_txt").html(data.uname);
				 $("#assigned_to").val(data.user);	
		  		 
		  		var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ALERT MESSAGE', data.message, action_buttons);
				  	}
		  	}
  		}
	});
}
	
function postComm(){
	$.ajax({type: "POST",data: $('#supportdesk-form-details').serialize()  + '&ajax=supportdesk-form',	url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/postComment', array('id' => $model->id));?>", 
	  	dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'saved' && data.html) {
			  		$('.support_edit').html(data.html);  		$('.rejected_message').html(''); 		in_progress = 'In Progress'; 		reopen = 'Reopened';
			  			$(".sep1").hide();		$(".sep2").hide();		$(".sep3").hide();		$(".support_desk_label").hide();		$(".root_cause").hide();						
						$('#popupreaason').hide(); $('#popuprsr').hide();	$('#popuprsrcreate').hide();		$('#popupavoid').hide(); $('#popupescalate').hide(); 
			  	} else {
			  		if (data.status == 'no_message') {
			  			$('span#rejected_message_not').html("No message");
			  		}else if(data.status == 'pending'){
			  			buttons = {
			  			        "YES": {
			  			        	class: 'yes_button',
			  			        	click: function() 
			  				        {
			  				            $( this ).dialog( "close" );
			  				          	changeStatus("yes");
			  				          		$(".sep1").hide();
											$(".sep2").hide();
											$(".sep3").hide();

											$(".support_desk_label").hide();
											$(".root_cause").hide();
											
											$('#popupreaason').hide(); $('#popuprsr').hide();	$('#popuprsrcreate').hide();	
											$('#popupavoid').hide(); $('#popupescalate').hide(); 
			  				        }
			  			        },
			  			        "NO": {
			  			        	class: 'no_button',
			  			        	click: function() 
			  				        {
			  				            $( this ).dialog( "close" );
			  				          changeStatus("no");
			  				          	$(".sep1").hide();
										$(".sep2").hide();
										$(".sep3").hide();

										$(".support_desk_label").hide();
										$(".root_cause").hide();
										
										$('#popupreaason').hide(); $('#popuprsr').hide();	$('#popuprsrcreate').hide();		
										$('#popupavoid').hide();  $('#popupescalate').hide(); 
			  				        }
			  			        }
			  			}
			  			custom_alert("DELETE MESSAGE", " Have you supplied the information required for this Support Request?", buttons);
				  	}
			  	}
			  	showErrors(data.error);
		  	}
  		}
	});
}
function changeInput(value,id_support_dask,type){
    if (type == 3 && (value == 409 || value == 413 || value == 402 || value == 405 || value == 404 || value == 406)){
        changeInput('Yes',<?php echo $model->id; ?>, 7);
        $('#deployment').attr('disabled','disabled')
        $('#deployment option').removeAttr('selected');
        $('#deployment option[value="Yes"]').attr('selected','selected');
    }
	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/assigned');?>",  	dataType: "json",
	  	data: {'value':value,'id_support_desk':id_support_dask,'type':type},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {  		if(type == 3) {  ChangeReason(value,data.sr); }  	}	  	} 		}	});}
function SpecifyReason(){	$('#popupreaason').stop().show(); }
function SpecifyRSR(){ 	 $('#popuprsr').stop().show(); }
function CreateRSR(){ 	 $('#popuprsrcreate').stop().show(); }
function linkrsr(){
	var rsrid= $("#rsrid").val();
	if(typeof  rsrid == 'undefined'  || rsrid.trim() == ''  )				
	{
						var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', 'RSR# must be specified!', action_buttons);
			}else {

				var id="<?php echo $model->sd_no;?>";	var url = "<?php echo Yii::app()->createAbsoluteUrl('supportRequest/linkFromSr');?>";
				$.ajax({type: "POST",data: {id:id, rsr: rsrid},url: url, 	dataType: "json",
				  	success: function(data) {
					  	if (data) {
					  		if (data.valid == '0') {
							var action_but = {
												"Ok": {
													click: function() 
													{
														$(this).dialog('close');
													},
													class: 'ok_button'	
												} 
										};
							custom_alert('ERROR MESSAGE', 'RSR was not linked successfully', action_but);	
						  	}else{
						  		
						  		var link = document.getElementById('rsr');
						  		link.style.display = 'none';
						  		var link2 = document.getElementById('rsr2');
						  		link2.style.display = 'none';
						  		var action_but = {
												"Ok": {
													click: function() 
													{
														$(this).dialog('close');
													},
													class: 'ok_button'	
												} 
										};
										document.getElementById('rsrnum').innerHTML=data.num;	
										$('#rsrtitle').removeClass('hidden');
										$('#rsrnum').removeClass('hidden');
										$('#popuprsr').hide();  $('#popuprsrcreate').hide();	
							custom_alert('MESSAGE', 'RSR is successfully linked!', action_but);	
						  	
						  	}
				}		} });

			}		
}
function createactualrsr(){	
	var id="<?php echo $model->id;?>";	var url = "<?php echo Yii::app()->createAbsoluteUrl('supportRequest/createFromSr');?>";
	var descr=  $("#rsrdesc").val();
	var cat=  $("#rsrcat").val();
	var comm= $("#rsrcomm").val();
if(typeof  descr == 'undefined'  || descr.trim() == '' || typeof  cat == 'undefined'  || cat.trim() == '' || typeof  comm == 'undefined'  || comm.trim() == ''  )			
	{
		var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', 'Title, Description and Category must be specified!', action_buttons);

	}else{
	$.ajax({type: "POST",data: {id:id, description:descr, category:cat, comm:comm },url: url, 	dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.valid == '0') {
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'RSR was not created successfully', action_but);	
			  	}else{
			  		
			  		var link = document.getElementById('rsr');
			  		link.style.display = 'none';
			  		if(data.approved != 8){
			  			var link2 = document.getElementById('rsr2');
			  			link2.style.display = 'none';
			  		}
			  		
			  		var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
							document.getElementById('rsrnum').innerHTML=data.num;	
							$('#rsrtitle').removeClass('hidden');
							$('#rsrnum').removeClass('hidden');
							$('#popuprsr').hide();  $('#popuprsrcreate').hide();	
				custom_alert('MESSAGE', 'RSR is successfully created!', action_but);	
			  	
			  	}
		}		} });
	}
}
function Setavoid(){
	if( (!(document.getElementById('solution_message').value ==="") && document.getElementById('solution_message').value.trim().length !=0 ) ||  document.getElementById('SupportDesk_reason').value == 407 ||  document.getElementById('SupportDesk_reason').value == 413 ){
	changeStatusIncident("close"); $('#popupavoid').hide();     }else{	alert("Please specify a way to avoid this issue");	} }

function SetEscalation(){
	if(document.getElementById('escalate_message').value ==="" || document.getElementById('escalate_message').value.trim().length ==0 || ((document.getElementById('escalate_message').value.replace(/^\s+|\s+$/gm,'')).length <4 && (document.getElementById('escalate_message').value.replace(/^\s+|\s+$/gm,'')).toLowerCase().match("^n") && (document.getElementById('escalate_message').value.replace(/^\s+|\s+$/gm,'')).toLowerCase().match("a$"))){
		alert("Please specify a valid escalation reason");
	}else	{
	var esc = document.getElementById('escalate_message').value;
	//takes sr
	escalateIncident(esc); $('#popupescalate').hide();     
	} 
}

function SetReason(){
	if(!(document.getElementById('reopen_message').value ==="") && document.getElementById('reopen_message').value.trim().length !=0){
	changeStatusIncident("reopened");	$('#popuprsr').hide(); $('#popuprsrcreate').hide();	 $('#popupreaason').hide();    }else{	alert("Please specify a reason for re-opening the issue");	} }
function changeStatus(value){
	if(value == "yes")
		url = "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/postComment', array('id' => $model->id,'changeStatus'=>'yes'));?>";
	else
		url = "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/postComment', array('id' => $model->id,'changeStatus'=>'no'));?>";
		
	$.ajax({type: "POST",data: $('#supportdesk-form-details').serialize()  + '&ajax=supportdesk-form',	url: url, 	dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'saved' && data.html) {
			  		$('.support_edit').html(data.html);
			  		$('.rejected_message').html('');			  		
			  		in_progress = '<?php echo SupportDesk::getStatusLabel(SupportDesk::STATUS_IN_PROGRESS)?>';
			  		reopen = '<?php echo SupportDesk::getStatusLabel(SupportDesk::STATUS_REOPENED)?>';
			  		$('#status').html(data.current_status);
			  	} else {
			  		if (data.status == 'no_message') { 			$('span#rejected_message_not').html("No message");	}
				  	showErrors(data.error);
			  	}
	  		}
	  	}
	});
	$(".sep1").hide();	$(".sep2").hide();	$(".sep3").hide();	$(".support_desk_label").hide();	$(".root_cause").hide(); $('#popuprsr').hide(); $('#popuprsrcreate').hide();		$('#popupreaason').hide();	$('#popupavoid').hide(); $('#popupescalate').hide();  }
function escalateIncident(element){
	var sr= <?php echo $model->id;?>;
	$.ajax({	type: "POST",	data: {'sr':sr , 'reason':element},url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/escalateSR');?>", 	dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {
			    $('.escalate').addClass('hidden');$('#escalatec').addClass('checked');
			    

			  	}		}	});	}
function confirmCloseTicket(val){
	$.ajax({type: "POST",	data: $('#supportdesk-form-details').serialize()+ '&ajax=supportdesk-form&status='+val,url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/RateTicket', array('id' => $model->id));?>", dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
	  		 	if (data) { $('#rate_ticket').html(data.div);	$('#rate_ticket').show(); } }	}); }
function rateTicket(val){
	var selectedVal = "";	var selected = $("input[type='radio'][name='rating']:checked");
	if (selected.length > 0) {    	selectedVal = selected.val();	}
	var rate_reason=$('.ratingcomment').val();
		$.ajax({type: "POST",	data: {'rate_reason':rate_reason , 'rate':selectedVal},url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/RateTicket', array('id' => $model->id));?>", dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
	  		 	if (data.status=="success") { 
	  		 				changeStatusIncident("confirm_close");
	  		 				$('#rate_ticket').hide();  		 				
	  		 				}else{
		  			$('#remark-comment').removeClass('hidden');
	  		 				}			
		  	}
	});
	}

function hidedropdown(){		document.getElementById('btn').style.visibility="hidden";	}
	function closerateTicket() {	$('#rate_ticket').hide(); }
function Specifysol(){	$('#popupavoid').stop().show(); }
function Specifyescalate(){	$('#popupescalate').stop().show(); }
function showFullResolution(){
	 if($(".root_cause").is(":visible")){		Specifysol();
	 }else{
		$(".sep1").slideDown( "slow");	
		$(".support_desk_label").slideDown( "slow");
		$(".sep2").slideDown( "slow");
		$(".root_cause").slideDown( "slow");
		$(".sep3").slideDown( "slow");
		$("html, body").animate({ scrollTop: $(document).height() }, "slow");
		} }
function changeStatusIncident(val){
	var msg = document.getElementById('reopen_message').value;	var msg2 = document.getElementById('solution_message').value;
	$.ajax({type: "POST",	data: $('#supportdesk-form-details').serialize()+ '&ajax=supportdesk-form&status='+val+'&reopenmsg='+msg+'&solmsg='+msg2,					
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/postComment', array('id' => $model->id));?>", 
	  	dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success' && data.html) {
			  		$('.support_edit').html(data.html);
			  		in_progress = <?php echo SupportDesk::STATUS_IN_PROGRESS?>;
			  		reopen = <?php echo SupportDesk::STATUS_REOPENED?>;
			  		close = <?php echo SupportDesk::STATUS_CLOSED?>;
			  		confirme_close = <?php echo SupportDesk::STATUS_CONFIRME_CLOSED?>;			  		
				  	if(data.current_status == confirme_close){
				  		$('.supportdesk_comm').css('display','none');
					}
				  	$('#status').html(data.current_status);
				  	$(".sep1").hide();	$(".sep2").hide();		$(".sep3").hide();		$(".support_desk_label").hide();		$(".root_cause").hide(); $('#popuprsr').hide(); $('#popuprsrcreate').hide();		$('#popupreaason').hide();	$('#popupavoid').hide(); $('#popupescalate').hide(); 
			  	}else if(data.status == 'not_complet'){
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
			  		}else if(data.status == 'no_message') {
			  			$('span#rejected_message_not').html("No message");
						}
				}
			  	showErrors(data.error);
		  	}	});  }
</script>