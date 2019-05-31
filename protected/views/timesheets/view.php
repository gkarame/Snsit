<?php
/* @var $this TimesheetsController */
/* @var $model Timesheets */
?>
<div class="main_timesheet hidden">
	<?php if(GroupPermissions::checkPermissions('general-timesheets','write'))
	{?>	 <input id="srhidden" class="hidden" > 
		<div class="edit_header" id="list_timesheet">
			<!-- <div class="header_title">	
					<span class="red_title"><?php echo Yii::t('translations', 'ADD TASKS');?></span>
				</div>
			 -->
			<div class="wide search-timesheet search" id="search-expenses">
		
			<?php $form=$this->beginWidget('CActiveForm', array(
				'action'=>Yii::app()->createUrl($this->route),
				'method'=>'get',
			)); ?>
			
				<div class="row  " >
					<div class="inputBg_txt " style="width:419px;">
						<?php echo $form->label($model,'Tasks'); ?>
						<span class="spliter"></span>
						<?php 
						$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
								'name' => 'customer_id',		
								'source'=>Customers::getAllTStasks(),
								// additional javascript options for the autocomplete plugin
								'options'=>array(
									'minLength'	=>'0',
									'showAnim'	=>'fold',
									
								),
								'htmlOptions'	=>array(
									'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
									'onblur'	=> "blurfunction()",
									'id'=> "customers_timesheet1",
									'class'=>'width320'
								),
						));
						?>
					</div>
				</div>
			
				<a href="javascript:void(0)" class="addTasks" style="margin-right:375px;"></a>
				<div class="horizontalLine search-margin"></div>
			<?php $this->endWidget(); ?>
			
			
			<div class="actionPanel showTasksForm" style="">
				<form id="add_tasks" method="post" style="float:right">
					<div class="listofactions unassigned unassigned_bigger">										
							<div class="title"   >ADD TASKS</div>
							<div class="scroll_div width320" id="unicat">
								<ul class="cover width320 " >
									<li class="userAssign">
										<input type="checkbox" id="" value="" name="checked[]" />
										<label for="">
											<span class="input"></span>
										</label>
									</li> 
								</ul>
							</div>
							<ul class="act">
								<li class="customBtn" style=" list-style-type: none;">
									<a href="javascript:void(0);" class="save customSaveBtn ua"><?php echo Yii::t('translation', 'Save');?></a>
									
									<a href="javascript:void(0);" class="customCancelBtn ua" style="color:#222"><?php echo Yii::t('translation', 'CANCEL');?></a>
								</li>
							</ul>	
					
					</div><!-- end listofactions -->		
				</form>
			</div>
			
			<div class="actionPanel1 showTasksForm">
				<form id="add_tasks1" method="post" style="float:right">
					<div class="listofactions unassigned">
						<div class="headli"></div>
							<div class="contentli scroll_div width230" id="unicat">
								<div class="title">ADD SUPPORT TASKS</div>
								<ul class="cover width220 " >
									<li class="userAssign">
										<input type="checkbox" id="" value="" name="checked[]" />
										<label for="">
											<span class="input"></span>
										</label>
								      <input type="checkbox" id="checked[]" value="" name="checked[]2" />
									</li> 
								</ul>
							</div>
							<ul class="act">
								<li class="customBtn">
									<a href="javascript:void(0);" class="save customSaveBtn ua"><?php echo Yii::t('translation', 'Save');?></a>
									
									<a href="javascript:void(0);" class="customCancelBtn ua" style="color:#222"><?php echo Yii::t('translation', 'Cancel');?></a>
								</li>
							</ul>	
						<div class="ftrli"></div>
					</div><!-- end listofactions -->		
				</form>
			</div>
			
			</div><!-- search-form -->
			<br clear="all" />
		</div>
	<?php }?>
	<div id="timesheet_items_content"  class="grid border-grid bordertop0">
		<?php 
			$this->renderPartial('_timesheet_table',array(
					'model'=>$model,
					'data'=>$data,
					'default_tasks'=>$default_tasks,
					'maintenance_tasks'=>$maintenance_tasks,
					'internal_projects'=>$internal_projects
			));

		
		?>
	</div>
	<!--  POPUP EDIT -->
	<div class="editPopUp <?php echo (!in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? '' : 'hide')?>" style="display:<?php echo (!in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? '' : 'none')?>,color:red">
			<div class="itemActions button-column <?php echo (!in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? '' : 'hide')?>" >
				<a href="javascript:void(0)" class="copyLast" style="font-style:normal">Copy last</a>
				<a href="javascript:void(0)" class="clearForm" style="font-style:normal">Clear</a>
			</div>
		<form action="post" id="saveWorkedItem">
			<input type="hidden" name="id_task" class="inputIdTask" />
			<input type="hidden" name="date" class="inputDate" />
			<input type="hidden" name="default" class="inputDefault" />
			<div class="ticket">
				<label>SR #</label>
				<input type="text" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="inputticket" class="inputticket" value="" />
			</div>
			<br>
			<div class="rsr">
				<label>RSR #</label>
				<input type="text" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="inputrsr" class="inputrsr" value="" />
			</div>
			<br>
			<div class="customer" style="margin-top:-10px !important;"> 
				<label>Customer</label><br>
				<select  multiple style="width:227px;height:50px; margin-bottom:-20px !important;"  id="inputcustomer">
				<?php  $customers=Customers::getAllAutocompleteActive(); ?>
  					<?php foreach ($customers as $key=>$value) { ?>
  					<option value="<?php echo $value['id']; ?>"><?php echo $value['label'];?></option> 
  				<?php	}?>
  					
				</select>	
			</div>
			<br>
			<div class="inst">
				<label>IR #</label>
				<input type="text" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="inputinst" class="inputinst" value="" />
			</div>
			<br>
			<div class="radio" style="    margin-top: -40px;">
				<label>Documented?</label>
				<input type="radio" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="inputradio" class="inputradio" id="yes" value="yes" />Yes
				<input type="radio" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="inputradio" class="inputradio" id="no" value="no" /> No
			</div>
			<br>
			<div class="fbr">
				<label>FBR</label>
				<input type="text" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="inputfbr" id="inputfbr" class="inputfbr" value="" />
			</div>
			<br>
			<div class="radio2" style=" ">
				<label>Deployment?</label>
				<input type="radio" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="inputradio2" onclick="handleClick();" class="inputradio2" id="yes" value="yes" />Yes
				<input type="radio" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="inputradio2" onclick="handleClick();" class="inputradio2" id="no" value="no" /> No
			</div>
			<br>
			<div class="dep">
				<label>DEP#</label>
				<input type="text" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="inputdep" class="inputdep" value="" />
			</div>
			<br>
			<div>
				<label for="hours">Hours</label><br />
				<input type="text" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="hours" class="inputHours" />
			</div>
			<br>
			<div class="comments">
				<label for="comment">Comment</label><br />
				<textarea name="comment" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> class="inputComment"></textarea>
			</div>
			<div class="lieuCont">
				<label>In Lieu Of</label>
				<input type="text" readonly="readonly" name="inputLieuOf" class="inputLieuOf" />
			</div>
			<div class="reason">
				<label>Reason</label>
				<input type="text" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="reason" class="inputreason" value="" />
			</div>

			<div class="itemSaveAction button-column <?php echo (!in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? '' : 'hide')?>">
				<button>Save</button>
			</div>
			
		</form>
	</div>
	<!--  END POPUP EDIT -->
	<?php if(GroupPermissions::checkPermissions('general-timesheets','write'))
	{?>	
		<div class="row buttons">
			<a href="javascript:void(0);" id="save" class="recallBtn recallTimesheet <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED)) ? '' : 'hide')?>">Recall</a>
			<a href="javascript:void(0);" id="submit" class="submit submitTimesheet <?php echo (in_array($model->status, array(Timesheets::STATUS_NEW, Timesheets::STATUS_REJECTED)) ? '' : 'hide')?>">Submit</a>
			<!-- <a href="javascript:void(0);" id="reject" class="reject1 rejectTimesheet <?php //echo (!in_array($model->status, array(Timesheets::STATUS_IN_APPROVAL, Timesheets::STATUS_APPROVED)) ? 'hide' : '')?>">Reject</a> -->
		</div>
	<?php }?>
<!-- End Main Div -->
</div>
<script> var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClientUser');?>'; 
		 var Branch = '<?php echo Users::getLoggedUserBranch()?>';
		 var Status = '<?php echo Timesheets::getStatusFunc($model->id)?>';
		 var Approved = '<?php echo Timesheets::STATUS_APPROVED?>';
		 var Submit ='<?php echo Timesheets::STATUS_SUBMITTED?>';
		 var InApproval ='<?php echo Timesheets::STATUS_IN_APPROVAL?>';
</script>

<script type="text/javascript">
	function handleClick() {
		    if(document.querySelector('.inputradio2').checked == true )
		    {
		    	$('.dep').show();
		    }else{
		    	$('.dep').hide();
		    }
		}
	var hours, comment, id_task, date, isDefault, lieuOf, ok,mes,stat, ticket,rsr, inst, dep, radiob, radiob2, fbr;

	$(document).ready(function(){
		
		$('body').on('click', function(e){
			
			var $container = $('.editPopUp');
			if (!$container.is(e.target)  && $container.has(e.target).length === 0 && !$(e.target).hasClass('ui-widget-overlay') && $('.ui-dialog').has(e.target).length === 0 && !$(e.target).hasClass('ui-datepicker') && $('#ui-datepicker-div').has(e.target).length === 0 && !$(e.target).hasClass('ui-datepicker-prev') && !$(e.target).hasClass('ui-datepicker-next'))
			{
				if(!$(e.target).is('input'))
				{
					$('.addBackComment input').focus();
				}
				
				if($container.is(':visible'))
				{
					var stat ,tes;
					$.ajax({
					url: configJs.urls.baseUrl + '/timesheets/ValidateProject',
					type: 'post',
					dataType: 'json',
					data: {id_task: $('.inputIdTask').val()},
					success: function(res)
					{ 	stat= res.stat;
						if (stat!=1 && stat!='' &&  $('.inputHours').val() != '') {						
								$('.inputHours').val('');
								$('.inputComment').val('');
								var action_but = {
										"Ok": {
											click: function() 
											{
												$(this).dialog('close');
											},
											class: 'ok_button'	
										} 
								};
							
								 tes=1;
								 
								custom_alert('ERROR MESSAGE', 'Current Project is closed', action_but);		
						}else {
							
								saveItem();	
						}
					} 
					});
				}
			}
		});

		// on press escape close the edit input
		$(document).keyup(function(e) {
			if (e.keyCode == 27 && $('.editPopUp').is(':visible')) 
			{
				$('.editPopUp').hide();
				$('.addBackComment').removeClass('addBackComment');
			}
		});

		// on press escape close the edit input
		$(document).keyup(function(e) {
			if (e.keyCode == 1324 && $('.editPopUp').is(':visible')) 
			{
				$('.editPopUp').hide();
				$('.addBackComment').removeClass('addBackComment');
			}
		});

		// on click the input show it
		<?php if(GroupPermissions::checkPermissions('general-timesheets','write'))
		{?>	
			$(document).on('click', '.task_thead .item input', function(){
				$('.lieuCont').hide();
				$('.ticket').hide();
				$('.rsr').hide();
				$('.inst').hide();
				$('.dep').hide();
				$('.fbr').hide();
				$('.radio').hide();
				$('.radio2').hide();
				$('.customer').hide();
				$('.reason').hide();
				$('.task_thead .item input').blur();
				$('.addBackComment').removeClass('addBackComment');
				$(this).parents('.item').addClass('addBackComment');
					$('.editPopUp').css('height', '400px');
				// making inputs as default style
				$('.inputHours').css('border-color', '#ccc');
				$('.inputHours').attr('placeholder', '');
				$('.inputComment').css('border-color', '#ccc');
				$('.inputComment').attr('placeholder', '');
				$('.inputLieuOf').css('border-color', '#ccc');
				$('.inputLieuOf').attr('placeholder', '');
				$('.inputticket').css('border-color', '#ccc');
				$('.inputrsr').css('border-color', '#ccc');
				$('.inputinst').css('border-color', '#ccc');
				$('.inputinst').attr('placeholder', '');
				$('.inputdep').css('border-color', '#ccc');
				$('.inputdep').attr('placeholder', '');
				$('.inputfbr').css('border-color', '#ccc');
				$('.inputfbr').attr('placeholder', '');
				$('.inputradio').css('border-color', '#ccc');
				$('.inputradio2').css('border-color', '#ccc');
				$('.inputticket').attr('placeholder', '');
				$('.inputrsr').attr('placeholder', '');
				$('.inputreason').css('border-color', '#ccc');
				$('.inputreason').attr('placeholder', '');
				$('#inputcustomer option:selected').css('border-color', '#ccc');
				$('#inputcustomer option:selected').attr('placeholder', '');
				
	



				// parent to see if the input is a day off
				var id = $(this).parents('.task_thead').attr('data-id');
				isDefault = $(this).parents('.task_thead').attr('data-default');
				var date = $(this).attr('data-date');
				var weekend = $(this).attr('data-weekend');

				if(id == '<?php echo Timesheets::ITEM_DAYOFF; ?>' && isDefault == 1)
						{
							
							$('.lieuCont').show();
				}
			

					$.ajax({
						url: configJs.urls.baseUrl+'/timesheets/validatePublicHoliday',
						type: 'post',
						dataType: 'json',
						data: {phdate: date},
						success: function(data)
						{ 
							if(data){
							
								if((data.status=='failure' || weekend ==='weekendtask') && id!=='11'  && id!=='12'  && id!=='13'  && id!=='14'  && id!=='15' && data.support==0 ){									
									$('.reason').show();
																				}								

							}
						} 
						});

				

			
							
						$.ajax({
						url: configJs.urls.baseUrl+'/timesheets/getParentDefaulttasks',
						type: 'post',
						dataType: 'json',
						data: {id_task: id},
						success: function(data)
						{ 
							if(data){
							var parent=data.id_parent;


							if( parent=='27' && isDefault==1)
							{
							
								$('.ticket').show();
								
									

							}
							if( parent=='1324' && isDefault==1)
							{
							
								$('.rsr').show();
								
									

							}

							if(parent=='526'){
									$('.customer').show();
							}
							
							if(parent=='3.1')
							{
									$('.inst').show();
							}
							
							

							}
						} 
						});
					if(isDefault == 0)
					{
						$.ajax({
						url: configJs.urls.baseUrl+'/timesheets/getprojectdevtasks',
						type: 'post',
						dataType: 'json',
						data: {id_task: id, date: date},
						success: function(data){ 
							if(data){
							if( data.count>0){							
									$('.radio').show();
									if(data.fbr == 1)										
									{
										$('.fbr').show();
										if(data.dfbr!= null)
										{
											document.getElementById('inputfbr').value= data.dfbr;
										}else{
											document.getElementById('inputfbr').value= '';
										} 
									}
									if(data.rad != '' && data.rad!= null)
									{
										if(data.rad == 'yes')
										{
											$("#yes").prop("checked", true);
										}else{
											$("#no").prop("checked", true);
										}
									}else
									{
										$('.inputradio').prop('checked', false);
									}

								}	
							}
						}	});

						$.ajax({
						url: configJs.urls.baseUrl+'/timesheets/getprojectdevtasksonly',
						type: 'post',
						dataType: 'json',
						data: {id_task: id, date: date},
						success: function(data){ 
							if(data){
							if( data.count>0){							
									$('.radio2').show();
									$('.inputradio2').prop('checked', false);
								}	
							}
						}	});
					}

			
	
				// open the pop-up
				if(Status != Approved && Status != Submit && Status != InApproval){
					$('.editPopUp').show();
					var worked = ($(this).val() == '0.00' ? '' : $(this).val()); 
					$('.inputHours').val(worked).focus();
					$('.inputComment').val($(this).attr('data-comment'));
					$('.inputIdTask').val($(this).parents('.task_thead').attr('data-id'));


					$('.inputDate').val($(this).attr('data-date'));
					$('.inputticket').val($(this).attr('data-ticket'));
					$('.inputrsr').val($(this).attr('data-rsr'));
					
					$('.inputinst').val($(this).attr('data-inst'));
					$('.inputdep').val($(this).attr('data-dep'));
					$('#inputcustomer option:selected').val($(this).attr('data-customer'));
					$('.inputreason').val($(this).attr('data-reason'));
					$('.inputDefault').val($(this).parents('.task_thead').attr('data-default'));
		
					defaultDate = new Date($(this).attr('data-lieu-of'));
					datepickerDate = new Date();
					if(defaultDate != 'Invalid Date')
					{
						defaultDate = ('0' + defaultDate.getDate()).slice(-2) + '/' + ('0' + (defaultDate.getMonth()+1)).slice(-2) + '/' + defaultDate.getFullYear();
						$('.inputLieuOf').val(defaultDate);
						datepickerDate = $(this).attr('data-lieu');
					}
					else
					{
						$('.inputLieuOf').val('');
					}
		
					<?php if(Users::getLoggedUserBranch() == 'UAE') { ?>
						$('.inputLieuOf').datepicker({ dateFormat: 'dd/mm/yy', setDate: datepickerDate});
					<?php } else { ?>
						$('.inputLieuOf').datepicker({ dateFormat: 'dd/mm/yy', setDate: datepickerDate});
					<?php }?>
		
					hours = $('.inputHours').val();
					comment = $('.inputComment').val();
					id_task = $('.inputIdTask').val();
					date = $('.inputDate').val();
					isDefault = $('.inputDefault').val();
					lieuOf = $('.inputLieuOf').val();
					ticket=$('.inputticket').val();
					rsr= $('.inputrsr').val();
					inst=$('.inputinst').val();
					dep=$('.inputdep').val();
					fbr=$('.inputfbr').val();
					radiob=$('.inputradio').val();
					customer=$('#inputcustomer option:selected').val();
					reason=$('.inputreason').val();

				 }
			});
		<?php }?>
		//round the hours
		$('.inputHours').change(function() {
					hourss = parseFloat($('.inputHours').val());
					hourss = Math.round(hourss * 4) / 4;
					//newHours = number_format(hours, 2, '.', '');
					$('.inputHours').val(hourss);
					});
		
		
		// on submitting the form save item 
		$(document).on('submit', '#saveWorkedItem', function(evt){
			
			evt.preventDefault(); evt.stopPropagation();
			
			var stat ,tes;

				if($('.editPopUp .dep').is(':visible')){ 
					if($('.dep .inputdep').val() === '')
					{
						$('.inputdep').css('border-color', 'red');
						$('.inputdep').val('');
						$('.inputdep').attr('placeholder', 'The DEP# is mandatory!');
						error = 1;
					}
					else
					{ 

						$.ajax({
						url: configJs.urls.baseUrl + '/timesheets/ValidateProject',
						type: 'post',
						dataType: 'json',
						data: {id_task: $('.inputIdTask').val()},
						success: function(res)
						{ 	stat= res.stat;
								if (stat!=1 && stat!='' && $('.inputHours').val() != '' && $('.inputDefault').val()==0) {						
										$('.inputHours').val('');
									$('.inputComment').val('');
									var action_but = {
											"Ok": {
												click: function() 
												{
													$(this).dialog('close');
												},
												class: 'ok_button'	
											} 
									};
								
									 tes=1;
									 
									custom_alert('ERROR MESSAGE', 'Current Project is closed', action_but);		
							}		} 
						});

					$.ajax({
						url: configJs.urls.baseUrl + '/timesheets/validatedepNumber',
						type: 'post',
						async : false,						
						dataType: 'json',
						data: {dep: $('.dep .inputdep').val()},
						success: function(data)
							{
									if(data.status=='success3'){
										
										$('.inputdep').css('border-color', '#ccc');
										$('.inputdep').attr('placeholder', '');
											saveItem();
		
										return false;

									}else{		

										$('.inputdep').css('border-color', 'red');
										$('.inputdep').val('');
										$('.inputdep').attr('placeholder', 'The DEP# input is invalid!');
										p=1;
									
										error=1;
										saveUT(error , j , p );	
										
									}
								
								
							}
						});



					}
				}else if($('.editPopUp .inst').is(':visible')){ 
					if($('.inst .inputinst').val() === '')
					{
						$('.inputinst').css('border-color', 'red');
						$('.inputinst').val('');
						$('.inputinst').attr('placeholder', 'The IR # input is mandatory!');
						error = 1;
					}
					else
					{ 

						//houda validation on IR number
					$.ajax({
						url: configJs.urls.baseUrl + '/timesheets/validateIRNumber',
						type: 'post',
						dataType: 'json',
						data: {inst: $('.inst .inputinst').val()},
						success: function(data)
							{
									if(data.status=='success2'){
										
										$('.inputinst').css('border-color', '#ccc');
										$('.inputinst').attr('placeholder', '');
											saveItem();
		
										return false;

									}else if (data.status=='unvalid'){		

										$('.inputinst').css('border-color', 'red');
										$('.inputinst').val('');
										$('.inputinst').attr('placeholder', 'The IR # input is invalid!');
										p=0;
									
										error=1;
										saveUT(error , j , p );	
										
									}else if(data.status=='project'){		

										var action_but = {
											"Ok": {
												click: function() 
												{
													$(this).dialog('close');
												},
												class: 'ok_button'	
											} 
										};
									
										 
										custom_alert('ERROR MESSAGE', 'Please fill the time under the installation project!', action_but);												
										
									}else if (data.status=='failure'){		

										$('.inputinst').css('border-color', 'red');
										$('.inputinst').val('');
										$('.inputinst').attr('placeholder', 'Error!');
										p=0;
									
										error=1;
										saveUT(error , j , p );	
										
									}
								
								
							}
						});

					}
				}else
				{
					if(isDefault == 3){
						$.ajax({
						url: configJs.urls.baseUrl + '/timesheets/ValidateInternalProject',
						type: 'post',
						dataType: 'json',
						data: {id_task: $('.inputIdTask').val()},
						success: function(res)
						{ 	stat= res.stat;
								if (stat!=0 && stat!='' && $('.inputHours').val() != '') {						
										$('.inputHours').val('');
									$('.inputComment').val('');
									var action_but = {
											"Ok": {
												click: function() 
												{
													$(this).dialog('close');
												},
												class: 'ok_button'	
											} 
									};
								
									 tes=1;
									 
									custom_alert('ERROR MESSAGE', 'Current Project is closed', action_but);		
							}else {
									saveItem();				
									return false;
									
								}
							} 
						});
					}else{
						$.ajax({
						url: configJs.urls.baseUrl + '/timesheets/ValidateProject',
						type: 'post',
						dataType: 'json',
						data: {id_task: $('.inputIdTask').val()},
						success: function(res)
						{ 	stat= res.stat;
								if (stat!=1 && stat!='' && $('.inputHours').val() != '' && $('.inputDefault').val()==0) {						
										$('.inputHours').val('');
									$('.inputComment').val('');
									var action_but = {
											"Ok": {
												click: function() 
												{
													$(this).dialog('close');
												},
												class: 'ok_button'	
											} 
									};
								
									 tes=1;
									 
									custom_alert('ERROR MESSAGE', 'Current Project is closed', action_but);		
							}else {
								//alert('here2');
									saveItem();
				
										return false;
									
								}
							} 
						});
					}	
			 	}
		
		});
		
		// on click the clear button clear form
		$(document).on('click', '.clearForm', function(){
			$('.inputHours').val('');
			$('.inputComment').val('');
			$('.inputLieuOf').val('');
			$('.inputticket').val('');
			$('.inputrsr').val('');
			$('.inputinst').val('');
			$('.inputdep').val('');
			$('.inputfbr').val('');
			$('#inputcustomer option:selected').val('');
			$('.inputreason').val('');
		});

		// on click the copy last
		$(document).on('click', '.copyLast', function(){
			$.ajax({
				url: configJs.urls.baseUrl + '/timesheets/copyLast',
				type: 'post',
				dataType: 'json',
				data: {id_task: $('.inputIdTask').val(), date: $('.inputDate').val(), isDefault: $('.inputDefault').val()},
				success: function(res){
					if(typeof res.error == 'undefined')
					{
						$('.inputHours').val(res.amount);
						$('.inputComment').val(res.comment);
						$('.inputticket').val(res.srs);
						newLiueOf = new Date(res.lieu_of);
						if(newLiueOf != 'Invalid Date')
						{
							newLiueOf = ('0' + newLiueOf.getDate()).slice(-2) + '/' + ('0' + (newLiueOf.getMonth()+1)).slice(-2) + '/' + newLiueOf.getFullYear();
							$('.inputLieuOf').val(newLiueOf);
						}
						else
						{
							$('.inputLieuOf').val('');
						}
					}	
					else
					{
						var action_buttons = {
						        "Ok": {
		  				        	click: function() 
		  					        {
		  					            $(this).dialog('close');
		  					        },
									class: 'ok_button'	
						        }
				  		};

						custom_alert('ERROR MESSAGE', 'There is no data in the previous day!', action_buttons);
					}
				}
			});
		});

		// on click submit button
		$(document).on('click', '.submitTimesheet', function(){
			currentDate = $('.total_hours div.column80').attr('data-date');
			val1 = $('div.total_hours  [data-date="'+currentDate+'"]').html();
			
			
			$.ajax({
				url: configJs.urls.baseUrl + '/timesheets/getValidation',
				type: 'post',
				dataType: 'json',
				data: {id_timesheet: <?php echo $model->id; ?>, date: currentDate},
				success: function(res){
					ok = 1;
					mes = " ";
					//if dubai allow friday and saturday
					/*$.each(res.date, function(index, date){
						if(parseInt($('div.total_hours  [data-date="'+date+'"]').html()) < 8){
							if(Branch == 'UAE') { 
								if(index != "5" && index != "6"){
									mes +=date+" nu ai 8 ore";
									ok = 0;
								}
							}else{								
								if(index != "1" && index != "6"){
									mes +=date+" nu ai 8 ore";
									ok = 0;
								}
							}
						}
					});*/
					//window.location.href = configJs.urls.baseUrl + '/timesheets/';
					totalHours = $('#timesheet_items_content .total_hours').find('.side-borders-last').html();
					/*	if(ok == 0)
					{
						var action_buttons = {
						        "Ok": {
		  				        	click: function()  
		  					        {
		  					            $(this).dialog('close');
		  					        },
									class: 'ok_button'	
						        }
				  		};
	
						custom_alert('ERROR MESSAGE', "You have entered less than 8 hours for a specific day", action_buttons);
					}
					
					else if(totalHours < 40)
					{
						var action_buttons = {
						        "Ok": {
		  				        	click: function()  
		  					        {
		  					            $(this).dialog('close');
		  					        },
									class: 'ok_button'	
						        }
				  		};

						custom_alert('ERROR MESSAGE', 'You must have at least 40 hours worked on a timesheet!', action_buttons);
					}
					else
					{*/
						$.ajax({
							url: configJs.urls.baseUrl + '/timesheets/changeTimesheetStatus',
							type: 'post',
							dataType: 'json',
							data: {id_timesheet: <?php echo $model->id; ?>, status: '<?php echo Timesheets::STATUS_SUBMITTED; ?>'},
							success: function(res) {
								window.location.href = configJs.urls.baseUrl + '/timesheets/';
							}
						});
					//}
				}
			});
			
			
		
		});

		// on click the recall button
		$(document).on('click', '.recallTimesheet', function() {
			$this = $(this);
			$.ajax({
				url: configJs.urls.baseUrl + '/timesheets/changeTimesheetStatus',
				type: 'post',
				dataType: 'json',
				data: {id_timesheet: <?php echo $model->id; ?>, status: '<?php echo Timesheets::STATUS_NEW;?>'},
				success: function(res) {
					$this.hide();
					$('.submitTimesheet').removeClass('hide');
					$('.editPopUp .itemActions').removeClass('hide');
					$('.editPopUp .itemSaveAction').removeClass('hide');
					$('.editPopUp input[type=text]').prop('readonly', '');
					$('.editPopUp textarea').prop('readonly', '');
					Status = res.status;
				}
			});
		});

		$(document).on('click', '.rejectTimesheet', function(){
			$.ajax({
				url: configJs.urls.baseUrl + '/timesheets/changeTimesheetStatus',
				type: 'post',
				dataType: 'json',
				data: {id_timesheet: <?php echo $model->id; ?>, status: '<?php echo Timesheets::STATUS_REJECTED; ?>'},
				success: function(res){
					window.location.href = configJs.urls.baseUrl + '/timesheets/';
				}
			});
		});
		
		// on click plus minus sign collapse or narrow
		$(document).on('click', '.plus-minus', function(){
			if($(this).attr('data-collapsed') == 1)
			{
				$(this).attr('data-collapsed', '0').css('background-position', '0px -1px');
				$(this).parents('.project_thead').nextUntil('.project_thead').hide();
			}
			else
			{
				$(this).attr('data-collapsed', '1').css('background-position', '0px -22px');
				$(this).parents('.project_thead').nextUntil('.project_thead').show();
			}
		});

		collapseOrNot();
		selectPhasesTasks();

		// on click delete icon
		$(document).on('click', '.deleteFromTimesheet', function(){
			$this = $(this);
			
			// if the type of delete item is project or is a task
			if($(this).parents('.thead').hasClass('project_thead'))
			{
				if ($(this).parents('.thead').hasClass('default')) {
					type = 'default_project';
				} else {
					type = 'project';
				}
			}
			else
			{
				if ($(this).parents('.thead').hasClass('default')) {
					type = 'default_task';
				} else {
					type = 'task';
				}
			}
			buttons = {
			        "YES": {
			        	class: 'yes_button',
			        	click: function() 
				        {
				            $( this ).dialog( "close" );
				            deleteItem($this, type);
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
			custom_alert("DELETE MESSAGE", "Are you sure you want to delete this " + type + "?", buttons);
		});
		

		// on click the add tasks button 
		$(document).on('click','.addDefaultTasks', function(e){

			$.ajax({
				url: configJs.urls.baseUrl + '/timesheets/getDefaultTasks',
				type: 'post',
				dataType: 'json',
				data: {},
				success: function(tasks)
				{	
					$('#unicat').mCustomScrollbar("destroy");
					var tasksFormInputs = '';
					var phaseArr = new Array();
					$.each(tasks, function(index, task){
						
						tasksFormInputs += ''
							+ '<li class="userAssign uTask">'
								+ '<input type="checkbox" data-id-parent="' + task.id_phase + '" id="' + task.id + '" value="' + task.id + '" name="tasks[]" />'
								+ '<label for="' + task.id + '">'
								+ task.name
								+ '</label>'
							+ '</li>'
					+ '';
					});
					if(tasksFormInputs == '')
					{
						var action_buttons = {
						        "Ok": {
		  				        	click: function() 
		  					        {
		  					            $(this).dialog('close');
		  					        },
									class: 'ok_button'	
						        }
				  		};

						custom_alert('ERROR MESSAGE', 'There are no default tasks!', action_buttons);
					}
					else
					{
						
						$('.actionPanel1 ul.cover').html(tasksFormInputs);
						$('.actionPanel1').show();
						//$('#unicat').mCustomScrollbar();
						$('#unicat').mCustomScrollbar();
					}	
							
				}
				
			});
			
			e.stopPropagation();
		});
			

		// on click the add tasks button 
		$(document).on('click','.addTasks', function(e){
			//alert($("#customers_timesheet1"));
			var project_id = $("#customers_timesheet1").val();
			//console.log(project_id);
			if(project_id == '')
			{
				var action_buttons = {
				        "Ok": {
  				        	click: function() 
  					        {
  					            $(this).dialog('close');
  					        },
							class: 'ok_button'	
				        }
		  		};

				custom_alert('ERROR MESSAGE', 'You must select a project in order to add tasks!', action_buttons);
			}
			else
			{	
				$.ajax({
					url: configJs.urls.baseUrl + '/timesheets/getTasks',
					type: 'post',
					dataType: 'json',
					data: {id_project: $("#customers_timesheet1").val()},
					success: function(tasks)
					{	
						$('#unicat').mCustomScrollbar("destroy");
						var tasksFormInputs = '';
						var phaseArr = new Array();
						$.each(tasks, function(index, task){
							if(phaseArr.indexOf(task.id_phase) == -1)
							{
								phaseArr.push(task.id_phase);
								tasksFormInputs += ''
									+ '<li class="userAssign uProject">'
										+ '<input type="checkbox" data-id-project="' + task.id_phase + '" id="phase_' + task.id_phase + '" value="phase_' + task.id_phase + '" name="phases[]" />'
										+ '<label for="phase_' + task.id_phase + '" class="phaseInput">'
										+ truncate(task.phase_name, 33)
										+ '</label>'
									+ '</li>'
								+ '';
							}
							tasksFormInputs += ''
								+ '<li class="userAssign uTask">'
									+ '<input type="checkbox" data-id-parent="' + task.id_phase + '" id="' + task.id + '" value="' + task.id + '" name="tasks[]" />'
									+ '<label for="' + task.id + '">'
									+ truncate(task.name, 45)
									+ '</label>'
								+ '</li>'
						+ '';
						});
						if(tasksFormInputs == '')
						{
							var action_buttons = {
							        "Ok": {
			  				        	click: function() 
			  					        {
			  					            $(this).dialog('close');
			  					        },
										class: 'ok_button'	
							        }
					  		};

							custom_alert('ERROR MESSAGE', 'There are no tasks in this project!', action_buttons);
						}
						else
						{
							
							$('.actionPanel ul.cover').html(tasksFormInputs);
							$('.actionPanel').show();
							//$('#unicat').mCustomScrollbar();
							$('#unicat').mCustomScrollbar();
						}	
								
					}
					
				});
			}
			
			e.stopPropagation();
		});

		// on click cancel from add tasks form
		$(document).on('click','.actionPanel .customCancelBtn', function(){
			$('.actionPanel').fadeOut(100);
		});

		// on click the save button in add tasks form
		$(document).on('click','.actionPanel .customSaveBtn', function(){
			$.ajax({
				url: configJs.urls.baseUrl + '/timesheets/addTasks',
				type: 'post',
				dataType: 'json',
				data: $('#add_tasks').serialize() + '&id_timesheet=<?php echo $model->id;?>',
				success: function(res)
				{
					$('.actionPanel').fadeOut(100);
					refreshTimesheet();
					selectPhasesTasks();
				}
			});
		});

		// on click cancel from add tasks form
		$(document).on('click','.actionPanel1 .customCancelBtn', function(){
			$('.actionPanel1').fadeOut(100);
		});

		// on click the save button in add tasks form
		$(document).on('click','.actionPanel1 .customSaveBtn', function(){
			$.ajax({
				url: configJs.urls.baseUrl + '/timesheets/addDefaultTasks',
				type: 'post',
				dataType: 'json',
				data: $('#add_tasks1').serialize() + '&id_timesheet=<?php echo $model->id;?>',
				success: function(res)
				{
					$('.actionPanel1').fadeOut(100);
					refreshTimesheet();
					selectPhasesTasks();
				}
			});
		});
	});

	function selectPhasesTasks()
	{
		$(document).on('change', '.uProject input', function(){
			if($(this).is(':checked'))
			{
				$('.uTask input[data-id-parent="' + $(this).attr('data-id-project') + '"]').prop('checked', true);
			}
			else
			{
				$('.uTask input[data-id-parent="' + $(this).attr('data-id-project') + '"]').prop('checked', false);
			}
		});	

		$(document).on('change', '.uTask input', function(){
			if($(this).is(':checked'))
			{
				var allActive = 1;
				$.each($('.uTask input[data-id-parent="' + $(this).attr('data-id-parent') + '"]'), function(){
					console.log($(this));
					if(!$(this).is(':checked'))
					{
						allActive = 0;
					}
				});

				console.log(allActive);
				if(allActive == 1)
				{
					$('.uProject input[data-id-project="' + $(this).attr('data-id-parent') + '"]').prop('checked', true);	
				}
			}
			else
			{
				$('.uProject input[data-id-project="' + $(this).attr('data-id-parent') + '"]').prop('checked', false);
			}
		});
	}

	function collapseOrNot()
	{
		// foreach project collapse or expand if it has hours worked or not 
		$('.plus-minus').each(function(index){
			value = $.trim($(this).parents('.project_thead').find('.side-borders-last').html());
			if(value == '0.00')
			{
				$(this).attr('data-collapsed', '0').css('background-position', '0px -1px');
				$(this).parents('.project_thead').nextUntil('.project_thead').hide();
			}
			else
			{
				$(this).attr('data-collapsed', '1').css('background-position', '0px -22px');
				$(this).parents('.project_thead').nextUntil('.project_thead').show();
			}
		});
	}
		
	function refreshTimesheet()
	{
		$.ajax({
			url: configJs.urls.baseUrl + '/timesheets/view/<?php echo $model->id?>',
			type: 'post',
			data: {refreshTimesheet: 1},
			success: function(res)
			{
				$('#timesheet_items_content').html(res);
				collapseOrNot();
			}
		});
	}

	function truncate(string, maxlen){
	   if (string.length > maxlen)
	      return string.substring(0, maxlen)+'...';
	   else
	      return string;
	};

	function saveItem()
	{
		// making inputs as default style
		$('.inputHours').css('border-color', '#ccc');
		$('.inputHours').attr('placeholder', '');
		$('.inputComment').css('border-color', '#ccc');
		$('.inputComment').attr('placeholder', '');
		$('.inputLieuOf').css('border-color', '#ccc');
		$('.inputLieuOf').attr('placeholder', '');
		j = 1;p = 1;	
	

			// if the user clicked on a item and didn't modified anything, just exit without save on ajax
		if(hours == $('.inputHours').val() && comment == $('.inputComment').val() && id_task == $('.inputIdTask').val() &&  date == $('.inputDate').val() &&  isDefault == $('.inputDefault').val() && lieuOf == $('.inputLieuOf').val() && ticket==$('.inputticket').val()  && rsr==$('.inputrsr').val() && inst==$('.inputinst').val() && dep==$('.inputdep').val() && fbr==$('.inputfbr').val() && customer==$('#inputcustomer option:selected').val() && reason==$('.inputreason').val())
		{	
			$('.editPopUp').hide();
			$('.addBackComment').removeClass('addBackComment');
		}
		else
		{	

			// hours, comment and lieuOf validations 
			oldHours = (hours == '' ? '0.00' : hours); 
			newHours = parseFloat($('.inputHours').val());
			/*$hours = parseFloat($('.inputHours').val());
			$hours = round($hours * 4) / 4;
			newHours = number_format($hours, 2, '.', '');*/
			newComment = $.trim($('.inputComment').val());
			
			currentDate = $('.addBackComment input').attr('data-date'); 
			oldTotalHoursByDate = $('#timesheet_items_content .total_hours').find('.item[data-date="' + currentDate + '"]').html();
			newTotalHoursByDate = parseFloat(oldTotalHoursByDate) - parseFloat(oldHours) + parseFloat(newHours);
			parentId = parseInt($('.addBackComment').parents('.task_thead').attr('data-id'));
			parentDefault = parseInt($('.addBackComment').parents('.task_thead').attr('data-default'));
			error = 0;
			
		
			  
			if (isNaN(newHours))
			{
				$('.inputHours').css('border-color', 'red');
				$('.inputHours').val('');
				$('.inputHours').attr('placeholder', 'The hours input is mandatory!');
				error = 1;
				j = 0;
			}
			console.log('1');
			if (newComment.length == 0 ) 
			{console.log('2');
			
				if( !$('.ticket').is(':visible') && !$('.rsr').is(':visible') ){
				$('.inputComment').css('border-color', 'red');
				$('.inputComment').val('');
				$('.inputComment').attr('placeholder', 'The commentary input is mandatory!');
				error = 1;
					}
				p = 0;
			}

			if (newHours > 24 || newTotalHoursByDate > 24)
			{	
				error = 1;
				var action_buttons = {
				        "Ok": {
  				        	click: function() 
  					        {
  					            $(this).dialog('close');
  					        },
							class: 'ok_button'	
				        }
		  		};

				custom_alert('ERROR MESSAGE', 'Cannot enter more than 24 hours on a single day', action_buttons);
			}

			if($('.editPopUp .inputradio').is(':visible') && newComment.length != 0 && !isNaN(newHours)){ 
				if($('input[name=inputradio]:checked').length < 1)
				{  
					error=1;
					var action_but = {
						"Ok": {
												click: function() 
												{
													$(this).dialog('close');
												},
												class: 'ok_button'	
											} 
									};
																 
									custom_alert('ERROR MESSAGE', 'Please specify if the change(s) are documented', action_but);		
						}
			}
			if($('.editPopUp .inputradio2').is(':visible') && newComment.length != 0 && !isNaN(newHours)){ 
				if($('input[name=inputradio2]:checked').length < 1)
				{  
					error=1;
					var action_but = {
						"Ok": {
												click: function() 
												{
													$(this).dialog('close');
												},
												class: 'ok_button'	
											} 
									};
																 
									custom_alert('ERROR MESSAGE', 'Please specify if a deployment was performed', action_but);		
						}
			}


			// if the task is VACATION
			if(parentId == <?php echo Timesheets::ITEM_VACATION; ?> && parentDefault == 1)
			{
				if(newHours != 4 && newHours != 8)
				{	
					error = 1;
					var action_buttons = {
					        "Ok": {
	  				        	click: function() 
	  					        {
	  					            $(this).dialog('close');
	  					        },
								class: 'ok_button'	
					        }
			  		};
					if(p != 0 && j != 0)
						custom_alert('ERROR MESSAGE', 'You can add only 4 or 8 hours on a vacation task!', action_buttons);
				}
			}

			// if the task is DAYOFF 
			if(parentId == <?php echo Timesheets::ITEM_DAYOFF; ?> && parentDefault == 1)
			{
				if(newHours < 4)
				{	
					error = 1;
					var action_buttons = {
					        "Ok": {
	  				        	click: function() 
	  					        {
	  					            $(this).dialog('close');
	  					        },
								class: 'ok_button'	
					        }
			  		};
					if(p != 0 && j != 0)
						custom_alert('ERROR MESSAGE', 'You can add only more than 4 hours on a day off task!', action_buttons);
				}


				$.ajax({
						url: configJs.urls.baseUrl + '/timesheets/validateDayOff',
						type: 'post',
						dataType: 'json',
						async : false,
						data: {'date': $('.inputLieuOf').val() , 'time': newHours, 'entrydate':$('.inputDate').val()},
						success: function(data)
							{
									if(data.status=='success'){
										
									}else{		
											error = 1;
											var action_buttons = {
											        "Ok": {
							  				        	click: function() 
							  					        {
							  					            $(this).dialog('close');
							  					        },
														class: 'ok_button'	
											        }
									  		};
											if(p != 0 && j != 0)
												custom_alert('ERROR MESSAGE', data.message, action_buttons);	
										
									}
								
								
							}
						});


				
				if($('.editPopUp .lieuCont').is(':visible'))
				{
					if($('.lieuCont .inputLieuOf').val() === '')
					{
						$('.inputLieuOf').css('border-color', 'red');
						$('.inputLieuOf').val('');
						$('.inputLieuOf').attr('placeholder', 'The in lieu of input is mandatory!');
						error = 1;
					}
				}
				else
				{
					$('.inputLieuOf').css('border-color', '#ccc');
					$('.inputLieuOf').attr('placeholder', '');
				}
			}

			if($('.editPopUp .ticket').is(':visible')){ 
					if($('.ticket .inputticket').val() === '')
					{
						$('.inputticket').css('border-color', 'red');
						$('.inputticket').val('');
						$('.inputticket').attr('placeholder', 'The SR # input is mandatory!');
						error = 1;
					}else
					{ 

						//ramy validation on sr number
					$.ajax({
						url: configJs.urls.baseUrl + '/timesheets/validateSRNumber',
						type: 'post',
						dataType: 'json',
						async : false,
						data: {ticket: $('.ticket .inputticket').val() , task: parentId},
						success: function(data)
							{
									if(data.status=='success'){
										
										$('.inputticket').css('border-color', '#ccc');
										$('.inputticket').attr('placeholder', '');
										 saveUT(error , j , p );	

									}else{		

									
											$('.inputticket').css('border-color', 'red');
											$('.inputticket').val('');
											$('.inputticket').attr('placeholder', 'The SR # input is invalid!');
											p=0;
											error=1;
										
									}
								
								
							}
						});

					}
				}

				if($('.editPopUp .rsr').is(':visible')){ 
					if($('.rsr .inputrsr').val() === '')
					{
						$('.inputrsr').css('border-color', 'red');
						$('.inputrsr').val('');
						$('.inputrsr').attr('placeholder', 'The RSR # input is mandatory!');
						error = 1;
					}else
					{ 

						//ramy validation on sr number
					$.ajax({
						url: configJs.urls.baseUrl + '/timesheets/validateRSRNumber',
						type: 'post',
						dataType: 'json',
						async : false,
						data: {rsr: $('.rsr .inputrsr').val() , task: parentId},
						success: function(data)
							{
									if(data.status=='success'){
										
										$('.inputrsr').css('border-color', '#ccc');
										$('.inputrsr').attr('placeholder', '');
										 saveUT(error , j , p );	

									}else{		

									
											$('.inputrsr').css('border-color', 'red');
											$('.inputrsr').val('');
											$('.inputrsr').attr('placeholder', 'The RSR # input is invalid!');
											p=0;
											error=1;
										
									}
								
								
							}
						});

					}
				}

				


				 if($('.editPopUp .reason').is(':visible')){
						
					if($('.reason .inputreason').val().trim() === ''){
						$('.inputreason').css('border-color', 'red');
						$('.inputreason').val('');
						$('.inputreason').attr('placeholder', 'The reason input is mandatory!');
						error = 1;

					
					}
					
									
				}
			
				if(!$('.ticket').is(':visible') || ($('.inputticket').val()==="" ||$('.inputticket').val()===" "||$('.inputticket').val()===0) || !$('.rsr').is(':visible') || ($('.inputrsr').val()==="" ||$('.inputrsr').val()===" "||$('.inputrsr').val()===0) || !$('.inst').is(':visible') || ($('.inputinst').val()==="" ||$('.inputinst').val()===" "||$('.inputinst').val()===0) || ($('.inputdep').val()==="" ||$('.inputdep').val()===" "||$('.inputdep').val()===0)){
				
					saveUT(error , j , p );		
							}
			
				
					

		}
	}

	
	function saveUT(error , j , p  ){

	/*	 console.log(comm); */
//	 alert(error);exit;
	var currentdate = new Date(); 
var datetime = "Last Sync: " + currentdate.getDate() + "/"
                + (currentdate.getMonth()+1)  + "/" 
                + currentdate.getFullYear() + " @ "  
                + currentdate.getHours() + ":"  
                + currentdate.getMinutes() + ":" 
                  + currentdate.getSeconds() + ":" 
                + currentdate.getMilliseconds();

		console.log(datetime); console.log(' error :'+error); console.log('j :'+j);  console.log('p :'+p); 
			
			if(error == 0 || (j == 0 && p == 0) )
			{  
				if($('.radio').is(':visible') && error!=1){
				var valradio=document.querySelector('input[name="inputradio"]:checked').value;
				}else{
					var valradio='';
				}
				$.ajax({
					url: configJs.urls.baseUrl + '/timesheets/saveItem',
					type: 'post',
					dataType: 'json',
					data: {id_task: $('.inputIdTask').val(), date: $('.inputDate').val(), hours: $('.inputHours').val(), comment: $('.inputComment').val(), isDefault: $('.inputDefault').val(), id_timesheet: <?php echo $model->id; ?>, lieu_of: $('.inputLieuOf').val() , ticket: $('.inputticket').val() , inst: $('.inputinst').val() ,  customer: $('#inputcustomer').val() , reason: $('.inputreason').val(), radio:valradio, fbr: $('.inputfbr').val(), rsr: $('.inputrsr').val() ,},
					success: function(res)
					{	
						currentDate = $('.addBackComment input').attr('data-date'); 
						// gettinge all the old data from the table 
						oldHours = (hours == '' ? '0.00' : hours); 
						oldTaskTotalPerWeek = $('.addBackComment').parent('.task_thead').find('.side-borders-last').find('span').html();
						oldProjectTimeByDate = $('.addBackComment').parent('.task_thead').prevAll('.project_thead:first').find('.item[data-date="' + currentDate + '"]').html(); 
						oldProjectTotal = $('.addBackComment').parent('.task_thead').prevAll('.project_thead:first').find('.side-borders-last').html();
						oldTotalHoursByDate = $('#timesheet_items_content .total_hours').find('.item[data-date="' + currentDate + '"]').html();
						oldTotalBillableByDate = $('#timesheet_items_content .total_billable').find('.item[data-date="' + currentDate + '"]').html();
						oldBillabilityByDate = $('#timesheet_items_content .billability').find('.item[data-date="' + currentDate + '"]').html();
						oldTotalHours = $('#timesheet_items_content .total_hours').find('.side-borders-last').html();
						oldTotalBillable = $('#timesheet_items_content .total_billable').find('.side-borders-last').html();
						oldTotalBillability = $('#timesheet_items_content .billability').find('.side-borders-last').html();

						// modifying the old data from the table
						newTaskTotalPerWeek = numberFormat(parseFloat(oldTaskTotalPerWeek) - parseFloat(oldHours) + parseFloat(res.hours));
						newProjectTimeByDate = numberFormat(parseFloat(oldProjectTimeByDate) - parseFloat(oldHours) + parseFloat(res.hours));
						newProjectTotal = numberFormat(parseFloat(oldProjectTotal) - parseFloat(oldHours) + parseFloat(res.hours));
						newTotalHoursByDate = numberFormat(parseFloat(oldTotalHoursByDate) - parseFloat(oldHours) + parseFloat(res.hours));
						
						if($('.addBackComment').parents('.task_thead').attr('data-billable') == 'Yes') 
						{
							newTotalBillableByDate = numberFormat(parseFloat(oldTotalBillableByDate) - parseFloat(oldHours) + parseFloat(res.hours));
						}
						else
						{
							newTotalBillableByDate = oldTotalBillableByDate;
						}
						if(newTotalHoursByDate != 0)
							newBillabilityByDate = Math.round(numberFormat(parseFloat(newTotalBillableByDate) / parseFloat(newTotalHoursByDate)) * 100) + '%';
						else
							newBillabilityByDate = "0%";	
						newTotalHours = numberFormat(parseFloat(oldTotalHours) - parseFloat(oldHours) + parseFloat(res.hours));

						if($('.addBackComment').parents('.task_thead').attr('data-billable') == 'Yes') 
						{
							newTotalBillable = numberFormat(parseFloat(oldTotalBillable) - parseFloat(oldHours) + parseFloat(res.hours));
						}
						else
						{
							newTotalBillable = oldTotalBillable;
						}
						if(newTotalHours != 0)
							newTotalBillability = Math.round(parseFloat(newTotalBillable) / parseFloat(newTotalHours) * 100) + '%';
						else{
							newTotalBillability = "0%";
							}
						// introducing the new info into the table 
						$('.addBackComment').parent('.task_thead').find('.side-borders-last').find('span').html(newTaskTotalPerWeek);
						$('.addBackComment').parent('.task_thead').prevAll('.project_thead:first').find('.item[data-date="' + currentDate + '"]').html(newProjectTimeByDate);
						$('.addBackComment').parent('.task_thead').prevAll('.project_thead:first').find('.side-borders-last').html(newProjectTotal);
						$('#timesheet_items_content .total_hours').find('.item[data-date="' + currentDate + '"]').html(newTotalHoursByDate);
						$('#timesheet_items_content .total_billable').find('.item[data-date="' + currentDate + '"]').html(newTotalBillableByDate);
						$('#timesheet_items_content .billability').find('.item[data-date="' + currentDate + '"]').html(newBillabilityByDate);
						$('#timesheet_items_content .total_hours').find('.side-borders-last').html(newTotalHours);
						$('#timesheet_items_content .total_billable').find('.side-borders-last').html(newTotalBillable);
						/*if(newTotalBillability = 'NAN%')
							newTotalBillability = "0%";
						*/$('#timesheet_items_content .billability').find('.side-borders-last').html(newTotalBillability);
						// End introducing the new info into the table

						$('.addBackComment input').attr('data-lieu-of', res.lieu_of);
						$('.addBackComment input').attr('data-ticket', res.ticket);
						$('.addBackComment input').attr('data-inst', res.inst);
						$('.addBackComment input').attr('data-rsr', res.rsr);
						
						$('.addBackComment input').attr('data-customer', res.customer);
						hours = res.hours;
						comment = res.comment;

						$('.addBackComment input').val(hours);
						$('.addBackComment input').attr('data-comment', comment);					
						$('.inputHours').val(hours);
						$('.inputComment').val(comment);
						$('.editPopUp').hide();
						$('.addBackComment').removeClass('addBackComment');
					}
				});
			} 
	}
 
	function numberFormat(number)
	{
		return parseFloat(Math.round(number * 100) / 100).toFixed(2);
	}

	function deleteItem($element, type)
	{
		// if the type of delete item is project or is a task 
		if(type == 'project' || type == 'default_project')
		{
			$element.parents('.project_thead').nextUntil('.project_thead').hide();
			$element.parents('.project_thead').addClass('hide');
		}
		else
		{
			$element.parents('.task_thead').addClass('hide').css('display', 'none');

			// if it's no tasks on the current project hide the project also
			siblings = $element.parents('.task_thead').prevAll('.project_thead:first').nextUntil('.project_thead');
			count = 0;
			siblings.each(function(index){
				if(!$(this).hasClass('hide'))
				{
					count++;
				}
			});

			if(count == 0)
			{
				$element.parents('.task_thead').prevAll('.project_thead:first').addClass('hide').css('display', 'none');
			}
		}

		$.ajax({
			url: configJs.urls.baseUrl + '/timesheets/deleteItemFromTimesheet/',
			type: 'post',
			dataType: 'json',
			data: {id_timesheet: <?php echo $model->id?>, id: $this.attr('data-id'), type: type },
			success: function(data)
			{	
				$.each(data.total,function(id,value){
					$('#timesheet_items_content .total_hours').find('.item[data-date="' + id + '"]').html(value);
				});
				$.each(data.total_bill,function(id,value){
					$('#timesheet_items_content .total_billable').find('.item[data-date="' + id + '"]').html(value);
				});
				$('#timesheet_items_content .total_hours').find('.side-borders-last').html(numberFormat(data.total_time));
				$('#timesheet_items_content .total_billable').find('.side-borders-last').html(numberFormat(data.total_bill_last));
				if (data.status == 'task' || data.status == 'default_task') {
					$.each(data.total_project,function(id,value){
						$('#timesheet_items_content .project_thead').find('.item[data-date="' + id + '"]').html(value);
					});
					$('#timesheet_items_content .project_thead').find('.side-borders-last').html(numberFormat(data.total_per_project));
				}
			}
		});
	}

	function refreshProjectListsTimesheet(id)
	{
		console.log(id);
		$('#Timesheet_project_id').removeAttr('disabled');
			$.ajax({
	 			type: "GET",
	 			data: {id : id},					
	 			url: getProjectsByClientUrl, 
	 			dataType: "json",
	 			success: function(data) {
				  	if (data) {
				  		var arr = [];

				  		for (var key in data) {
				  		    if (data.hasOwnProperty(key)) {
				  		        arr.push({'id': key, 'label': data[key]});
				  		    }
				  		}
				  		
				  		 var sorted = arr.sort(function (a, b) {
			    				if (a.label > b.label) {
			      					return 1;
			      				}
			    				if (a.label < b.label) {
			     					 return -1;
			     				}

			    				return 0;
						 });
					  	
				  		var selectOptions = '<option value=""></option>';
				  		var index = 1;
				  		$.each(sorted,function(index, val){
				  			selectOptions += '<option value="' + val.id+'">'+val.label+'</option>';
				  		});
					    $('#Timesheets_project_id').html(selectOptions);

				  	}
		  		}
			});

	}

	function blurfunction(){
			console.log("Im here:");
			var name = document.getElementById('customers_timesheet1').value;

			$('#Timesheet_project_id').removeAttr('disabled');
			$.ajax({
	 			type: "GET",
	 			data: {id : name},					
	 			url: getProjectsByClientUrl, 
	 			dataType: "json",
	 			success: function(data) {
				  	if (data) {
				  		var arr = [];

				  		for (var key in data) {
				  		    if (data.hasOwnProperty(key)) {
				  		        arr.push({'id': key, 'label': data[key]});
				  		    }
				  		}
				  		
				  		 var sorted = arr.sort(function (a, b) {
			    				if (a.label > b.label) {
			      					return 1;
			      				}
			    				if (a.label < b.label) {
			     					 return -1;
			     				}

			    				return 0;
						 });
					  	
				  		var selectOptions = '<option value=""></option>';
				  		var index = 1;
				  		$.each(sorted,function(index, val){
				  			selectOptions += '<option value="' + val.id+'">'+val.label+'</option>';
				  		});
					    $('#Timesheets_project_id').html(selectOptions);

				  	}
		  		}
			});
	};
	function openDropDown(othis) {
		closeDropDown(".dropdownSelect");
		$('#Timesheet_project_id').mCustomScrollbar("destroy");
		var parent = $(othis).parents('.dropdownSelect');
		if (parent.find('.options').css('height') == "0px") {
			parent.addClass('opn');
			parent.find('.options').css('width', parent.parents('.row').width());
			parent.find('.options').stop(false).animate({'height':150 + "px",'width':157+'px' }, '700', 'easeInQuart', function () {
				if (parent.find(".scrollOptions").length > 0) {
					if (!parent.find(".scrollOptions").find(".mCustomScrollBox").length > 0) {
						parent.find(".scrollOptions").mCustomScrollbar({});
					}
				}
			});
		}
		else {
			closeDropDown(".dropdownSelect");
		}
		closeDropDown("dropdownWrap");
	}
	function closeDropDown(element) {
		$(element).removeClass('opn');
		$(element).find('.options').stop(false).animate({ 'height': 0 + 'px' }, '700', 'easeInQuart', function () { });
	}
	function j(){
		return "dsfsd";
	}
</script>