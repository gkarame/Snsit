<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.ajax({
 			type: 'GET',
 			data: $(this).serialize(),		
 			url: $(this).attr('action'), 
 			dataType: 'json',
 			success: function(data) {
			  	if (data && data.status == 'success') {
			  		$('.gridApproval').html(data.html);
			  	}
	  		}
		});
	return false;
});
");
?>
<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->


<div class="gridApproval">
	<?php $this->renderPartial('_approval_grid', array('projects' => $projects,'default_tasks'=>$default_tasks));?>
</div>
<?php 
if(GroupPermissions::checkPermissions('timesheets-timesheets_approval','write'))
{
?>
	<div class="buttons-section">	
		<?php 
			echo CHtml::button(Yii::t('translations','Approve'), array('class'=>'approve', 'onclick' => 'approve()'));
			echo CHtml::button(Yii::t('translations','Reject'), array('class'=>'reject', 'onclick' => 'reject()','style' => 'top:1.5px'));
		?>
		<div class="loader" style="position:absolute;margin-left:226px;margin-top: -27px;"></div>
 <div style="width:100px; height:20px;">   </div>


<form class="reply-form">

<span style="color:#B22222; font-size:16px; font-weight:bold; font-family:Calibri;"> Why did you reject my timesheet ? *</span> <br/><br />

    <textarea id="reason" style="width:900px; height:100px;vertical-allign:top;"></textarea> 
    <br/><br />

    <a onclick="sendrejectemail();" style="cursor:pointer;font-weight:bold; font-family:Calibri;font-size:16px;"> Send Email </a>
</form>
<br />

	</div>
<?php } ?>


 <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>


<style type="text/css">.reply-form{ display:none;  }</style>
<script type="text/javascript">
$('a').click( function() {  } );


 function clicktimesheetgrid(timesheet,user){ 	

 			$.ajax({
			 		type: "POST",					
				  	url: "<?php echo $this->createAbsoluteUrl('timesheets/getTimeSheetGrid');?>", 
				  	dataType: "json",
				  	data: { 'timesheet':timesheet , 'user':user },
				  	success: function(data) {
				  		if (data) {

				  		$('.timehseetcontainer').html(data.timesheetgrid);
				  		$('.timesheettitre').html('Timesheet # '+timesheet);
				  			$('#popuptimesheet').stop().show();

 					$(".timehseetcontainer").mCustomScrollbar({
              			advanced:{ updateOnContentResize: true}});

				  		}
			  		},
			  	
				});				
		
 }

	$('.reply-form').hide();
	var submitted = false, rejected = false;

	$(document).ready(function(){


			$('#popuptimesheet').stop().hide();

			$(".closetimesheet").click(function() {    
			         
			          $('#popuptimesheet').stop().hide();
			    }
			);



		$('.project_head').click(function() {
			var body = $(this).siblings('.project_body');
			if (body.hasClass('hidden')) {
				body.removeClass('hidden');
			} else {
				body.addClass('hidden');
			}
		});
		$('.user_head').click(function() {
			var body = $(this).siblings('.user_body');
			if (body.hasClass('hidden')) {
				body.removeClass('hidden');
			} else {
				body.addClass('hidden');
			}
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
		
	});
	function checkedAll (id_proj) {
			if ($('#chech_'+id_proj).is(':checked')){
				$(".project_"+id_proj+" .task_thead .checkbox_div input.tasks_chk").attr('checked',true);
			}else{
				$(".project_"+id_proj+" .task_thead .checkbox_div input.tasks_chk").attr('checked',false);
			}
	}
	function approve() {
		if (!$('input:checkbox').is(':checked'))
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
			custom_alert('ERROR MESSAGE', 'You have to select at least one task!', action_buttons);
		}
		else
		{
			if (submitted == false) {
				submitted = true;
				$('.loader').show();
				$.ajax({
			 		type: "POST",					
				  	url: "<?php echo $this->createAbsoluteUrl('timesheets/approveTasks');?>", 
				  	dataType: "json",
				  	data: $('#approval-form').serialize(),
				  	success: function(data) {
				  		if (data) {
				  			submitted = false;
				  			if ($('.buttons-section').find('.loader').length) {
				  				$('.loader').hide();
				  			}
				  		
				  			if (data.status == "success") {

				  				$('input:checked').each(function(i, checkbox) {
					  				$(this).parents('.task_thead').remove();
				  			    });
				  				$.each(data.approved, function(i, item) {
									$(".timesheet_"+item).remove();
					  			});
				  				refresh();	  											
				  			} else {
				  				var action_buttons = {
								        "Ok": {
				  				        	class: 'ok_button',
				  				        	click: function() 
				  					        {
				  					            $( this ).dialog( "close" );
				  					        }
								        }
						  		}
				  				custom_alert('ERROR MESSAGE', data.error, action_buttons);
				  			}
				  		}
			  		},
			  		error : function(data) {
			  			submitted = false;
			  			if ($('.buttons-section').find('.loader').length) {
			  				$('.loader').hide();
			  			}
			  		}
				});
			}
		}
	}
	function refresh() {
		$(".timesheet").each(function() {
			if ($(this).children().length == 0) {
				$(this).remove();	
			}	
		});
		$(".user_body").each(function() {
			if ($(this).children().length == 0) {
				$(this).parents('.user').remove();	
			}	
		});
		$(".project_body").each(function() {
			if ($(this).children().length == 0) {
				$(this).parents('.project').remove();	
			}	
		});
	}

	
	function reject() {
		
		if (!$('input:checkbox').is(':checked'))
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
			custom_alert('ERROR MESSAGE', 'You have to select at least one task!', action_buttons);
		}
		else
		{ 
			if (rejected == false) {32
			rejected = true;
			$('.loader').show();
			$.ajax({
		 		type: "POST",					
			  	url: "<?php echo $this->createAbsoluteUrl('timesheets/RejectTasksValid');?>", 
			  	dataType: "json",
			  	data: $('#approval-form').serialize(),
			  	success: function(data) {
			  		if (data) {
			  			rejected = false;
			  			if ($('.buttons-section').find('.loader').length) {
			  				$('.loader').hide();
			  			}
			  			if (data.status == "success") {
							
							$('.reply-form').show();
				  			refresh();

			  			} else {
			  				var action_buttons = {
							        "Ok": {
			  				        	class: 'ok_button',
			  				        	click: function() 
			  					        {
			  					            $( this ).dialog( "close" );
			  					        }
							        }
					  		}
					  		$('.reply-form').hide();
			  				custom_alert('ERROR MESSAGE', 'You cannot reject more than 1 timesheet at a time.', action_buttons);
			  			}
			  		}
		  		},
		  		error : function(data) {
		  			rejected = false;
		  			if ($('.buttons-section').find('.loader').length) {
		  				$('.loader').hide();
		  			}
		  		}
			});
		}
		}
		
	}


function sendrejectemail() {

		var reason=document.getElementById("reason").value;


		if(reason==' ' || reason=='' || reason==null) {
				var action_buttons = {
							        "Ok": {
			  				        	class: 'ok_button',
			  				        	click: function() 
			  					        {
			  					            $( this ).dialog( "close" );
			  					        }
							        }
					  		}
		
			  custom_alert('ERROR MESSAGE', 'Please specify the reason.', action_buttons);
			

		}else {

		var task = $("input[type=checkbox]:checked").parents('.task_thead');
		var id_timesheet = task.attr("data-id_timesheet");

		$.ajax({
		 		type: "POST",					
			  	url: "<?php echo $this->createAbsoluteUrl('timesheets/sendrejectemails'); ?>", 
			  	dataType: "json",
			  	data: {reason:reason, tasks:id_timesheet},
			  	success: function(data) {
			  		
			  			if (data.status == "success") {

							rejectTasks();
							

			  			}
			  		
		  		},

		  	
		  		
			});

		
	}

}

	function rejectTasks() {
		

		if (rejected == false) {
			rejected = true;
			$('.loader').show();
			$.ajax({
		 		type: "POST",					
			  	url: "<?php echo $this->createAbsoluteUrl('timesheets/rejectTasks');?>", 
			  	dataType: "json",
			  	data: $('#approval-form').serialize(),
			  	success: function(data) {
			  		if (data) {
			  			rejected = false;
			  			if ($('.buttons-section').find('.loader').length) {
			  				$('.loader').hide();
			  			}
			  			if (data.status == "success") {

				  			$('input:checked').each(function(i, checkbox) {
				  				var task = $(this).parents('.task_thead');
				  				var id_timesheet = task.attr("data-id_timesheet");
				  				$(".timesheet_"+id_timesheet).remove();
			  			    });
				  			refresh();
			  			} else {
			  				var action_buttons = {
							        "Ok": {
			  				        	class: 'ok_button',
			  				        	click: function() 
			  					        {
			  					            $( this ).dialog( "close" );
			  					        }
							        }
					  		}
					  		$('.reply-form').hide();
			  				custom_alert('ERROR MESSAGE', 'You can not reject more than 1 timesheet at a time.', action_buttons);
			  			}
			  		}
		  		},
		  		error : function(data) {
		  			rejected = false;
		  			if ($('.buttons-section').find('.loader').length) {
		  				$('.loader').hide();
		  			}
		  		}
			});
		}
	}
		

</script>