<?php  Yii::app()->clientScript->registerScript('getIssues', "$('.search-form-issue form').submit(function(){	$.fn.yiiGridView.update('issues-grid', {	data: $(this).serialize()	});	return false; });"); ?>
<div class="search-form-issue">
<?php $this->renderPartial('_searchIssues',array(	'model'=>$model,)); ?></div>
<div class="mytabs expenses_edit">	<div id="expenses_items">	<div class="theme" style="padding-top:0px; background:none"><b><?php echo Yii::t('translations', ' ');?></b></div>
		<?php  $this->widget('zii.widgets.grid.CGridView', array('id'=>'issues-grid','dataProvider'=>$model->getIssues(),'summaryText' => '',
						'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}','columns'=>array(		
						array('class'=>'CCheckBoxColumn','id'=>'checkissue','htmlOptions' => array('class' => 'item checkbox_grid_issue'),'selectableRows'=>2,),					
					array('name' => '#','value' => '$data->renderNumber()','htmlOptions' => array('class' => 'width30'),'headerHtmlOptions' => array('class' => 'width30'),),
					array('name' => 'description','value' => '$data->description','visible' => true, 'htmlOptions' => array('class' => 'column300'), 'headerHtmlOptions' => array('class' => 'column300'),),
					array('name' => 'priority','header' => 'Priority','value' => '$data->priority==0 ? "Low" :($data->priority==1? "Medium": "High")','visible' => true, 'htmlOptions' => array('class' => 'width80'), 'headerHtmlOptions' => array('class' => 'width80'),),
					array('name' => 'status','header' => 'Status','value' => 'ProjectsIssues::getStatus($data->status)','visible' => true, 'htmlOptions' => array('class' => 'width80'), 'headerHtmlOptions' => array('class' => 'width80'),),
					array('name' => 'type','value' => '$data->type','htmlOptions' => array('class' => 'column70'), 'headerHtmlOptions' => array('class' => 'column70'),),
					array('name' => 'assigned to','type'=>'raw','value' => 'ProjectsIssues::getUsersGrid(ProjectsIssues::getAssignedto($data->id))','htmlOptions' => array('class' => '','style' => 'width:100px  !important;'), 'headerHtmlOptions' => array('class' => 'column100'),),	
					array('name' => 'logged_date','header' => 'Log Date', 'value' => 'date("d/m/Y", strtotime($data->logged_date))','visible' => true, 'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),),
					array('header'=>'','value'=>'$data->renderAttachment()','name' => 'attachment','sortable' => false,'headerHtmlOptions' => array('class' => 'attach-issue'),),
					array('class'=>'CCustomButtonColumn','template'=>' {update}','htmlOptions'=>array('class' => 'button-column'),'buttons'=>array(						  
						            	'update' => array('label' => Yii::t('translations', 'Edit'),'imageUrl' => null,'url' => 'Yii::app()->createUrl("projects/manageIssueItem", array("id"=>$data->id))','options' => array('onclick' => 'showIssueItemForm(this, false);return false;'),),),),),)); ?>
			<div class="tache-issue new_issue">	<div onclick="showIssueItemForm(this, true);" class="newrisk">	<u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u>	</div>	</div>
        <?php $form=$this->beginWidget('CActiveForm', array('id'=>'issue-form',	'enableAjaxValidation'=>false,
			'htmlOptions' => array(	'class' => 'ajax_submit','enctype' => 'multipart/form-data','action' => Yii::app()->createUrl("projects/update", array("id"=>$model->id))	),	)); ?>
		<?php $this->endWidget(); ?></div></div>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';	var createItemRiskUrl = '<?php echo Yii::app()->createUrl("projects/createRiskItem/?id_project=$id_project"); ?>';
	var createIssueUrl= '<?php echo Yii::app()->createUrl("projects/createIssue/?id_project=$id_project"); ?>';	var updateItemRiskUrl = '<?php echo Yii::app()->createUrl("projects/manageRiskItem"); ?>';

function triggerSDSearch(status) {	
	if(status == 'All'){
		$('#inv_status').val('');
	}else{
		status = parseInt(status);	$('#inv_status').val(status); 
	}
	hidedropdown();	$('.search-btn').trigger('click'); }

function triggerSDPriority(priority) {	
	$('.sr_p').removeClass("colorRed");
	if(priority == 'All')
	{
		$('#inv_priority').val('');
		$('.sr_p_all').addClass("colorRed");
	}else{
		priority = parseInt(priority);	$('#inv_priority').val(priority);
		$('.sr_p_'+priority).addClass("colorRed");

	}
 	hidedropdown();	
	$('.search-btn').trigger('click'); 
}

function updateIssuewithNew(element, id, url){
	$.ajax({type: "POST",	data: $(element).parents('#issue-form').serialize() +'&ajax=issue-form',	url: url+'?id='+id, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-issue.new').remove(); $('.new_issue').show();		$.fn.yiiGridView.update('issues-grid'); 				  				
				  		  showIssueItemForm(1, true);	
				  		$.each(data.amounts, function(i, item) {	$('#'+i).html(item);	});		 

				  	} else { if (data.status == 'success') { $(element).parents('.tache-issue.new').replaceWith(data.form); } }
				  	showErrors(data.errors);  	showErrors(data.alert);	  	} 		}	}); }
function updateIssue(element, id, url){
	$.ajax({type: "POST",	data: $(element).parents('#issue-form').serialize() +'&ajax=issue-form',	url: url+'?id='+id, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-issue.new').remove(); $('.new_issue').show();					  				
				  		$.fn.yiiGridView.update('issues-grid');
				  		$.each(data.amounts, function(i, item) {	$('#'+i).html(item);	});				  		
				  	} else { if (data.status == 'success') { $(element).parents('.tache-issue.new').replaceWith(data.form); } }
				  	showErrors(data.errors);  	showErrors(data.alert);	  	} 		}	}); }
function showIssueItemForm(element, newItem) {
	var url;
	if (newItem) {	url = createIssueUrl;	} else {	url = $(element).attr('href');	}
	$.ajax({	type: "POST",  	url: url, 	dataType: "json",  	data: {'expenses_id':modelId},
	  	success: function(data) {
		  	if (data) {	  	if (data.status == 'success') {
					if (newItem) { $('.new_issue').hide();	  	$('.new_issue').after(data.form);
					  } else {	$(element).parents('tr').addClass('noback').html('<td colspan="10" class="noback">' + data.form + '</td>'); } } } }	}); }
function createIssueItem(element, expensId, url){
	$.ajax({ type: "POST",	data: $(element).parents('#issue-form').serialize() + '&expenses_id='+expensId+'&ajax=issue-form',	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-issue.new').remove();  	$('.new_issue').show();	$.fn.yiiGridView.update('issues-grid'); 
				  		$.each(data.amounts, function(i, item) {   $('#'+i).html(item); 		});	
				  	} else {
				  		if (data.status == 'success') {	$(element).parents('.tache-issue.new').replaceWith(data.form);	}  	}
				  	showErrors(data.errors); showErrors(data.alert);  	} 		}		}); }
function createIssueIetmwithNew(element, expensId, url){
	$.ajax({ type: "POST",	data: $(element).parents('#issue-form').serialize() + '&expenses_id='+expensId+'&ajax=issue-form',	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-issue.new').remove();  	$('.new_issue').show(); $.fn.yiiGridView.update('issues-grid'); showIssueItemForm(1, true);	
				  		$.each(data.amounts, function(i, item) {   $('#'+i).html(item); 		});	
				  		
				  	} else {
				  		if (data.status == 'success') {	$(element).parents('.tache-issue.new').replaceWith(data.form);	}  	}
				  	showErrors(data.errors); showErrors(data.alert);  	} 		}		}); }

	function getUsersIssues() {
	if (!$('#users-list-issues').is(':visible')) {
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/GetUnssignedUsersIssues');?>", 
		  	dataType: "json",  	data:  $('.checkbox_grid_issue input').serialize()+'& id_project= <?php echo $model->id ?>',
		  	success: function(data) {
		  		if (data.status == "success") { 		
		  			$('#users-list-issues').html(data.div); 		
		  			$('.action_list').hide(); 	
		  				$('#users-list-issues').show(); 	
		  					$('#users-list-issues').find('.scroll_div').mCustomScrollbar();
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
			  	} }	});	} else { $('#users-list-issues').fadeOut(100);	} }

function UncheckAll () { $('[id^="chech_"]').attr('checked',false);	$('[id^="checkissue_"]').attr('checked',false); }

function assignUsersIssues() {
	if ($('#unassigned-users-form-issue').serialize() != '') {
		$.ajax({type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('projects/assignUsersIssues', array('id'=>$model->id));?>", 
		  	dataType: "json",data:  $('#unassigned-users-form-issue').serialize()+'&'+$('.checkbox_grid_issue').find('input[name!=change]').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") {
		  		//	$.each( data.id_phases, function( key, value ) { $.fn.yiiGridView.update(value+'-grid'); });		  			
		  		//	$.each( data.allphases, function( key, value ) {		  				
		  		//		 $('#input_'+value).removeClass('ram'); $('#'+value).addClass('estez'); $('#'+value).prop('disabled', true); 	});
		  			$('#users-list-issues').fadeOut(100);	
		  			UncheckAll(); 
					$.fn.yiiGridView.update('issues-grid'); 
		  		} }	});
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
function getAssignedUsersIssues() {
	if (!$('#users-list-issues').is(':visible')) {
		$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('projects/GetAssignedUsersIssues');?>",dataType: "json",data: $('.checkbox_grid_issue').find('input[name!=change]').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") {
			  		$('#users-list-issues').html(data.div); $('.action_list').hide(); $('#users-list-issues').show(); $('#users-list-issues').find('.scroll_div').mCustomScrollbar();			  		
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
	} else {	$('#users-list-issues').fadeOut(100); } }

function unassignUsersIssues() {
	$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('projects/unassignUsersIssues', array('id'=>$model->id));?>", 
	  	dataType: "json",data:  $('#assigned-users-issue-form').serialize()+'&'+$('.checkbox_grid_issue').find('input[name!=change]').serialize(),
	  	success: function(data) {
	  		if (data.status == "success") {
	  			//$.each( data.id_phases, function( key, value ) { $.fn.yiiGridView.update(value+'-grid'); });
	  			$('#users-list-issues').fadeOut(100);		UncheckAll();
				$.fn.yiiGridView.update('issues-grid'); 
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
</script>