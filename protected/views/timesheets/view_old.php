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
			
				<div class="row">
					<div class="inputBg_txt">
						<?php echo $form->label($model,'Customer'); ?>
						<span class="spliter"></span>
						<?php 
						$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
								'name' => 'customer_id',		
								'source'=>Timesheets::getAllTimeSheetTasks(),
								// additional javascript options for the autocomplete plugin
								'options'=>array(
									'minLength'	=>'0',
									'showAnim'	=>'fold',
									'select'	=>"js: function(event, ui){ refreshProjectListsTimesheet(ui.item.value); }",
								),
								'htmlOptions'	=>array(
									'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
									'onblur'	=> "blurfunction()",
									'id'=> "customers_timesheet1"
								),
						));
						?>
					</div>
				</div>
	<!--
				<div class="row">
					<div class="selectBg_search">
						<?php echo $form->label($model, 'Project'); ?>
						<span class="spliter"></span>
						<div class="select_container">
							<?php echo $form->dropDownList($model, 'project_id', array(), array('prompt' => Yii::t('translations', 'Choose project'))); ?>
						</div>
					</div>
					<?php echo $form->error($model,'project_id'); ?>
				</div>-->

			 <a href="javascript:void(0)" class="addTasks"></a>
			<!--	<a href="javascript:void(0)" class="addDefaultTasks"></a> -->
				
				<div class="horizontalLine search-margin"></div>
			<?php $this->endWidget(); ?>
			
			
			<div class="actionPanel showTasksForm">
				<form id="add_tasks" method="post" style="float:right">
					<div class="listofactions unassigned unassigned_bigger">
						<div class="headli"></div>
							<div class="contentli scroll_div width280" id="unicat">
								<div class="title">ADD TASKS</div>
								<ul class="cover width280 marginl-9" >
									<li class="userAssign">
										<input type="checkbox" id="" value="" name="checked[]" />
										<label for="">
											<span class="input"></span>
										</label>
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
					</div> 		
				</form>
			</div>
			<!--
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
					</div>		
				</form>
			</div> -->
			
			</div><!-- search-form -->
			<br clear="all" />
		</div>
	<?php }?>
	<div id="timesheet_items_content"  class="grid border-grid bordertop0">
		<?php 
			$this->renderPartial('_timesheet_table',array(
					'model'=>$model,
					'data'=>$data,
					'default_tasks'=>$default_tasks
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
			<div>
				<label for="hours">Hours</label><br />
				<input type="text" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> name="hours" class="inputHours" />
			</div>
			<br>
			<div>
				<label for="comment">Comment</label><br />
				<textarea name="comment" <?php echo (in_array($model->status, array(Timesheets::STATUS_SUBMITTED, Timesheets::STATUS_APPROVED)) ? 'readonly="readonly"' : '');?> class="inputComment"></textarea>
			</div>
			<div class="lieuCont">
				<label>In Lieu Of</label>
				<input type="text" readonly="readonly" name="inputLieuOf" class="inputLieuOf" />
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
	
	var hours, comment, id_task, date, isDefault, lieuOf, ok,mes,stat, ticket;

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

		// on click the input show it
		<?php if(GroupPermissions::checkPermissions('general-timesheets','write'))
		{?>	
			$(document).on('click', '.task_thead .item input', function(){
				$('.task_thead .item input').blur();
				$('.addBackComment').removeClass('addBackComment');
				$(this).parents('.item').addClass('addBackComment');
	
				// making inputs as default style
				$('.inputHours').css('border-color', '#ccc');
				$('.inputHours').attr('placeholder', '');
				$('.inputComment').css('border-color', '#ccc');
				$('.inputComment').attr('placeholder', '');
				$('.inputLieuOf').css('border-color', '#ccc');
				$('.inputLieuOf').attr('placeholder', '');
				$('.inputticket').css('border-color', '#ccc');
				$('.inputticket').attr('placeholder', '');
	
	
				// parent to see if the input is a day off
				id = $(this).parents('.task_thead').attr('data-id');
				isDefault = $(this).parents('.task_thead').attr('data-default');

				

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
						$('.editPopUp').css('height', '265px');
						$('.ticket').show();
					}
					else
					{
						$('.editPopUp').css('height', '225px');
						$('.ticket').hide();
					}

					}
				} 
				});
			
			 


				if(id == <?php echo Timesheets::ITEM_DAYOFF; ?> && isDefault == 1)
				{
					$('.editPopUp').css('height', '265px');
					$('.lieuCont').show();
				}
				else
				{
					$('.editPopUp').css('height', '225px');
					$('.lieuCont').hide();
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
		
					<?php if(Users::getLoggedUserBranch() == 'Dubai') { ?>
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
				$.ajax({
				url: configJs.urls.baseUrl + '/timesheets/ValidateProject',
				type: 'post',
				dataType: 'json',
				data: {id_task: $('.inputIdTask').val()},
				success: function(res)
				{ 	stat= res.stat;
						if (stat!=1 && stat!='' && $('.inputHours').val() != '') {						
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
			 
		
		});

		// on click the clear button clear form
		$(document).on('click', '.clearForm', function(){
			$('.inputHours').val('');
			$('.inputComment').val('');
			$('.inputLieuOf').val('');
			$('.inputticket').val('');
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
							if(Branch == 'Dubai') { 
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

			var project_id = $("#Timesheets_project_id").val();
			console.log(project_id);
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
					data: {id_project: $("#Timesheets_project_id").val()},
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
		if(hours == $('.inputHours').val() && comment == $('.inputComment').val() && id_task == $('.inputIdTask').val() &&  date == $('.inputDate').val() &&  isDefault == $('.inputDefault').val() && lieuOf == $('.inputLieuOf').val() && ticket==$('.inputticket').val())
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
			if (newComment.length == 0)
			{console.log('2');
				$('.inputComment').css('border-color', 'red');
				$('.inputComment').val('');
				$('.inputComment').attr('placeholder', 'The commentary input is mandatory!');
				error = 1;
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

			if($('.editPopUp .ticket').is(':visible'))
				{
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
						data: {ticket: $('.ticket .inputticket').val()},
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

											 error=1;
											 saveUT(error , j , p );					
													
									}
								
								
							}
						});

									
				
					
				}
				}
				
			

		}
	}

	
	function saveUT(error , j , p ){

			
			if(error == 0 || (j == 0 && p == 0) )
			{
				$.ajax({
					url: configJs.urls.baseUrl + '/timesheets/saveItem',
					type: 'post',
					dataType: 'json',
					data: {id_task: $('.inputIdTask').val(), date: $('.inputDate').val(), hours: $('.inputHours').val(), comment: $('.inputComment').val(), isDefault: $('.inputDefault').val(), id_timesheet: <?php echo $model->id; ?>, lieu_of: $('.inputLieuOf').val() , ticket: $('.inputticket').val()},
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