<?php $this->breadcrumbs=array(	'Requests',);
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/select2.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/scripts/select2.js'); ?>
<?php echo $this->message;?>
<div class="create"><?php $form=$this->beginWidget('CActiveForm', array('id'=>'ea-header-form','enableAjaxValidation'=>false,)); ?>
	<div class="row marginb20">	<?php echo $form->labelEx($model,'type'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model,'type', Requests::requstsType(), array('prompt' => Yii::t('translations', 'Choose type'),'id'=>'typeDropDown')); ?>
		</div><?php echo $form->error($model,'type'); ?></div>
	<div class="row startDateRow"><?php echo $form->label($model,'startDate'); ?><div class="dateInput">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'startDate', 
		    	'cssFile' => false,'options'=>array('minDate'  => 'Yii::app()->Date->now(false)','dateFormat'=>'dd/mm/yy'),
		    	'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'start_date'),));	?>
				<span class="calendar calfrom"></span>	<?php echo $form->error($model,'startDate'); ?>	</div></div>		
	<div class="row endDateRow"><?php echo $form->label($model,'endDate'); ?><div class="dateInput">
			<?php  $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model, 'attribute'=>'endDate', 'cssFile' => false,'options'=>array('minDate'  => 'Yii::app()->Date->now(false)','dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'end_date'),));?>
			<span class="calendar calfrom"></span>	<?php echo $form->error($model,'endDate'); ?>	</div>	</div>	<br/><br/>
	<div style="display: block;float:left;font-family:Arial" class="margint10">
		<p style="margin-bottom:10px;">This Leave Request will be forwarded for approval to your Line Manager, for more visibility use this drop down to include other senior managers with whom you are currently working on projects</p>
	</div><div class="row margin0">	<?php echo $form->labelEx($model,'cc'); ?>	<div class="selectBg_create">
			<?php echo $form->dropDownList($model,'cc', Users::getAllSelect(), array('multiple'=>'true','style="width:300px;"')); ?>
		</div>	<?php echo $form->error($model,'cc'); ?></div><div class="horizontalLine"></div><div class="row buttons">
		<?php echo CHtml::submitButton('Save', array('class'=>'submit','id'=>'submitrequest')); ?></div><br clear="all" /><?php $this->endWidget(); ?></div>
<script type="text/javascript">
	$(document).ready(function() {	$("#Requests_cc").select2();	});
	$("#start_date").change(function(){	var d = new Date();    var year_now = (d.getFullYear()).toString();    var type = $("#typeDropDown option:selected").val();
   	if(type=="91"){ 
	var start= ($("#start_date").val()).toString();	var start_fix_dd= start.substr(0,2);	var start_fix_mm= start.substr(3,2);
	var start_fix_yy= start.substr(6);	var start = start_fix_mm+"/"+start_fix_dd+"/"+start_fix_yy;	var date1= new Date(start);
	console.log(date1);	var end= ($("#end_date").val()).toString();	var end_fix_dd= end.substr(0,2);	var end_fix_mm= end.substr(3,2);	var end_fix_yy= end.substr(6);
	var end = end_fix_mm+"/"+end_fix_dd+"/"+end_fix_yy;	var date2= new Date(end);	console.log(date2);
	var timeDiff = Math.abs(date2.getTime() - date1.getTime());	var diffDays = Math.ceil(timeDiff / (1000*3600*24))+1; 	console.log(diffDays);
	var month_start = start_fix_mm;	var year = parseInt(start.substr(6));	var weekend_count= isWeekend(date1,date2);	var diffDays= diffDays-weekend_count;
	console.log("Days off without weekend:"+diffDays);
	if(month_start =="01"){		year=year-1;		console.log("We are in Jan so year is: "+year);	}
	if(year != year_now){
		var allowed_days=parseInt(<?php		echo Timesheets::getAllowedVacationDays(Yii::app()->user->id);	?>);
		var allowed= allowed_days - diffDays;
		if(allowed <0){
			document.getElementById("submitrequest").style.visibility = "hidden";
			var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit!', action_but);	
		}else{
			document.getElementById("submitrequest").style.visibility="visible";
		} }else{
	var url = "<?php echo Yii::app()->createAbsoluteUrl('requests/VacationDaysHRRequest');?>";
	$.ajax({type: "POST",data: {selected:year, start:start},	url: url, dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.valid == '0') {			
				document.getElementById("submitrequest").style.visibility = "hidden"; 
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit!', action_but);	
				}else{
			  		var allowed = parseInt(data.daysleft) - diffDays;
			  		if(allowed <0){
			  			document.getElementById("submitrequest").style.visibility = "hidden";
			  			var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
					custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit!', action_but);	
			  		}else{	
			  			document.getElementById("submitrequest").style.visibility="visible";
			  		}  	}  	}	} }); }	} });
	$("#end_date").change(function(){
	var d = new Date();   var year_now = (d.getFullYear()).toString();   var type = $("#typeDropDown option:selected").val();
   	if(type=="91"){ var start= ($("#start_date").val()).toString();	var start_fix_dd= start.substr(0,2);	var start_fix_mm= start.substr(3,2);
	var start_fix_yy= start.substr(6);	var start = start_fix_mm+"/"+start_fix_dd+"/"+start_fix_yy;	var date1= new Date(start);
	console.log(date1);	var end= ($("#end_date").val()).toString();	var end_fix_dd= end.substr(0,2);	var end_fix_mm= end.substr(3,2);
	var end_fix_yy= end.substr(6);	var end = end_fix_mm+"/"+end_fix_dd+"/"+end_fix_yy;	var date2= new Date(end);	console.log(date2);
	var timeDiff = Math.abs(date2.getTime() - date1.getTime());	var diffDays = Math.ceil(timeDiff / (1000*3600*24))+1; 	console.log(diffDays);
	var month_start = start_fix_mm;	var year = parseInt(start.substr(6));	var weekend_count= isWeekend(date1,date2);	var diffDays= diffDays-weekend_count;
	console.log("Days off without weekend:"+diffDays);
	if(month_start =="01"){		year=year-1;		console.log("We are in Jan so year is: "+year);	}
	if(year != year_now){	var allowed_days=parseInt(<?php		echo Timesheets::getAllowedVacationDays(Yii::app()->user->id);	?>);
		var allowed= allowed_days - diffDays;
		if(allowed <0){
			document.getElementById("submitrequest").style.visibility = "hidden";
			var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit!', action_but);	
		}else{
			document.getElementById("submitrequest").style.visibility="visible";
		}
	}else{
	var url = "<?php echo Yii::app()->createAbsoluteUrl('requests/VacationDaysHRRequest');?>";
	$.ajax({type: "POST",	data: {selected:year, start:start}, 	url: url, dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		console.log("IM HERE");
		  		if (data.valid == '0') {			
				document.getElementById("submitrequest").style.visibility = "hidden";
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'No Vacation Days Left!', action_but);	
				}else{
			  		var allowed = parseInt(data.daysleft) - diffDays;
			  		if(allowed <0){
			  			document.getElementById("submitrequest").style.visibility = "hidden";
			  			var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
					custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit!', action_but);	
			  		}else{	
			  			document.getElementById("submitrequest").style.visibility="visible";
			  		}  	}  	}	} }); }	} });
function isWeekend(date1, date2) {
   	var user_branch = "<?php echo Requests::getUserBranch(); ?>";  	console.log("User Branch:"+user_branch);   var d1 = new Date(date1),
     d2 = new Date(date2), 
        isWeekend = false;
        var count=0;
    while (d1 <= d2) {
        var day = d1.getDay();
        if(user_branch=="UAE"){   	isWeekend = (day == 6) || (day==5);
        }else{    	isWeekend = (day == 6) || (day == 0);       } 
        if (isWeekend) { 	count++; }	
        d1.setDate(d1.getDate() + 1);   }
    console.log("Weekend count: "+count);    return count; }
</script>