<div class="create"><div class="row"><div><?php echo CHtml::activelabelEx($model,"title"); ?></div><div class="inputBg_create">	<?php echo CHtml::activeTextField($model,"title", array('style'=>'width:640px;','readonly' =>true)); ?>
	</div></div><div class="row" id="invoicesflag" style="padding-left:30px;">								
            	<div class="row <?php echo ($model->invoicesflag == '1')?'checked' : ''?>"  onclick="CheckOrUncheckInput(this,<?php echo $model->id;?>);">		
					<span style="padding-bottom:55px !important;"><?php echo CHtml::activeLabelEx($model, 'invoicesflag');?></span>
					<span style="padding-left:55px;"><?php  echo CHtml::CheckBox($model->invoicesflag,($model->invoicesflag== '1')?'checked' : '' ); ?> </span>
				</div>	<?php echo  CCustomHtml::error($model,'invoicesflag'); ?></div>
				<div class="row" id="incluetimesheet" style="padding-left:30px;">								
            	<div class="row <?php echo ($model->incluetimesheet == '1')?'checked' : ''?>"  onclick="CheckOrUncheckInputTimesheet(this,<?php echo $model->id;?>);">		
					<span style="padding-bottom:55px !important;"><?php echo CHtml::activeLabelEx($model, 'incluetimesheet');?></span>
					<span style="padding-left:55px;"><?php  echo CHtml::CheckBox($model->incluetimesheet,($model->incluetimesheet== '1')?'checked' : '' ); ?> </span>
				</div>	<?php echo  CCustomHtml::error($model,'incluetimesheet'); ?></div>
				<div class="row" id="includechecklist" style="padding-left:30px;">								
            	<div class="row <?php echo ($model->includechecklist == '1')?'checked' : ''?>"  onclick="CheckOrUncheckInputChecklist(this,<?php echo $model->id;?>);">		
					<span style="padding-bottom:55px !important;"><?php echo CHtml::activeLabelEx($model, 'includechecklist');?></span>
					<span style="padding-left:55px;"><?php  echo CHtml::CheckBox($model->includechecklist,($model->includechecklist== '1')?'checked' : '' ); ?> </span>
				</div>	<?php echo  CCustomHtml::error($model,'includechecklist'); ?></div>
				<br clear="all"><br clear="all">
<?php if(GroupPermissions::checkPermissions('projects-projects_general')){	$this->renderPartial('_report_highlights',array('model'=>$model));
			$this->renderPartial('_report_milestones',array('model'=>$model));		$this->renderPartial('_report_risks',array('model'=>$model));
			$this->renderPartial('_report_healthindicators', array('model'=>$modelhealth,'id_project'=>$model->id));	} ?></div>
<script>
function CheckOrUncheckInput(obj, id){
		var checkBoxDiv = $(obj);	var input = checkBoxDiv.find('input[type="checkbox"]');	var k=0;
		if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');	input.prop('checked', false);	}
		else {	checkBoxDiv.addClass('checked');	input.prop('checked', true);	var k=1;	}		
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('Projects/invoicesFlag');?>", 
		  	dataType: "json", 	data: {'id':id,'val':k},
			success: function(data) { 		}	});	}

function CheckOrUncheckInputTimesheet(obj, id){
		var checkBoxDiv = $(obj);	var input = checkBoxDiv.find('input[type="checkbox"]');	var k=0;
		if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');	input.prop('checked', false);	}
		else {	checkBoxDiv.addClass('checked');	input.prop('checked', true);	var k=1;	}		
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('Projects/timesheetFlag');?>", 
		  	dataType: "json", 	data: {'id':id,'val':k},
			success: function(data) { 		}	});	}
function CheckOrUncheckInputChecklist(obj, id){
		var checkBoxDiv = $(obj);	var input = checkBoxDiv.find('input[type="checkbox"]');	var k=0;
		if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');	input.prop('checked', false);	}
		else {	checkBoxDiv.addClass('checked');	input.prop('checked', true);	var k=1;	}		
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('Projects/checklistFlag');?>", 
		  	dataType: "json", 	data: {'id':id,'val':k},
			success: function(data) { 		}	});	}

function submitForm(element) {
		var data = $("#sr-healthindicators-form").serialize();
		$.ajax({type: "POST",	data: data,	  	dataType: "json",
		  	url : $("#sr-healthindicators-form").attr("action"),
		  	success: function(data) {
				  	if (data.status == 'sent') {  closeTab(configJs.current.url); } }		});	}
</script>