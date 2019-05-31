<?php $this->breadcrumbs=array(	'Requests',);
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/select2.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/scripts/select2.js');?>
<div class="create"><?php $form=$this->beginWidget('CActiveForm', array('id'=>'ea-header-form','enableAjaxValidation'=>false,)); ?>
	<div class="row marginb20">	<?php echo $form->labelEx($model,'type'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model,'type', Requests::requstsType(), array('prompt' => Yii::t('translations', 'Choose type'),'id'=>'typeDropDown', 'onchange' => 'changeCategory(this);')); ?>
		</div>	<?php echo $form->error($model,'type'); ?>	</div>	
	<div class="row startDateRow">	<?php echo $form->label($model,'startDate'); ?>	<div class="dateInput">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model, 'attribute'=>'startDate','cssFile' => false,'options'=>array(
		    		'minDate'  => 'Yii::app()->Date->now(false)','dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'start_date'),   ));	?>
			<span class="calendar calfrom"></span><?php echo CCustomHtml::error($model,'startDate');  ?></div>	</div>		
	<div class="row endDateRow"><?php echo $form->label($model,'endDate'); ?>	<div class="dateInput">	<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
'model'=>$model,'attribute'=>'endDate', 	 	'cssFile' => false,'options'=>array('minDate'  => 'Yii::app()->Date->now(false)','dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'end_date'),));?>
			<span class="calendar calfrom"></span>	<?php echo  CCustomHtml::error($model,'endDate'); ?>	</div></div>
	<div class="row inLieuOf1 hidden">	<?php echo $form->label($model,'inLieuOf1'); ?>		<div class="dateInput">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
		        'model'=>$model,'attribute'=>'inLieuOf1', 'cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),
		    	'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'inLieuOf1' ),   ));	?>
			<span class="calendar calfrom"></span>	<?php echo $form->error($model,'inLieuOf1'); ?>	</div>	</div>	
		<div class="row hidden" id="halfday" >									
            	<div class="row <?php echo ($model->halfday == '1')?'checked' : ''?>"  onclick="CheckOrUncheckInput(this);">		
					<?php echo CHtml::activeLabelEx($model, 'half day');?>	<?php  echo CHtml::CheckBox('Requests[halfday]',($model->halfday == '1')?'checked' : '' ); ?> 
				</div>	<?php echo  CCustomHtml::error($model,'halfday'); ?></div>
		<div class="row red hidden plus" onclick="addhalf()"  style="cursor:pointer; font-size:12px; padding-top:25px; padding-right:25px;">Another Half Day ?</div>
	<div class="row inLieuOf2 hidden">	<?php echo $form->label($model,'inLieuOf2'); ?>	<div class="dateInput">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model, 'attribute'=>'inLieuOf2','cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'inLieuOf2'),));	?>
			<span class="calendar calfrom"></span>	<?php echo $form->error($model,'inLieuOf2'); ?>	</div>	</div>	
	<div class="width300 row row_textarea_ea " id="description"><div id='labeldescription'> <?php echo $form->labelEx($model, 'description'); ?></div>
		<div id='labelprojects' class='hidden'> <?php echo $form->labelEx($model, 'Project(s)'); ?></div>	<div class="inputBg_create">
		<?php echo $form->textField($model, 'description',array('autocomplete'=>'off' )); ?></div>	<?php  echo CCustomHtml::error($model,'description'); ?></div>
		<div class="row red hidden minus" onclick="removehalf()" style="cursor:pointer; font-size:12px; padding-top:25px; padding-right:25px;"> One Half Day Only</div>
	<br/><br/><div style="display: block;float:left;font-family:Arial" class="margint10">
		<p style="margin-bottom:10px;">This Leave Request will be forwarded for approval to your Line Manager, for more visibility use this drop down to include other senior managers with whom you are currently working on projects</p>
	</div><div class="row margin0">	<?php echo $form->labelEx($model,'cc'); ?><div class="selectBg_create">
			<?php echo $form->dropDownList($model,'cc', Users::getAllSelect(), array('multiple'=>'true','style="width:300px;"')); ?></div>	
		<?php echo $form->error($model,'cc'); ?></div>	<div class="horizontalLine"></div>	<div class="row buttons">
		<?php echo CHtml::submitButton('Save', array('class'=>'submit','id'=>'submitrequest','onclick'=>'disablebutton();')); ?>
	</div>	<br clear="all" /><?php $this->endWidget(); ?></div>
<script type="text/javascript">
	$(document).ready(function() {		$("#Requests_cc").select2();	});
function disablebutton() {
var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('LOADING', 'Submitting ...'); }
function addhalf() { $('.plus').addClass('hidden');	$('.inLieuOf2').removeClass('hidden'); $('.minus').removeClass('hidden'); }
function removehalf() {	$('.plus').removeClass('hidden');	$('.inLieuOf2').addClass('hidden');	$('.minus').addClass('hidden'); }
function changeInLieuOf(element) { var dat; $this =  $(element); var date = $this.val(); console.log(date); var url = "<?php echo Yii::app()->createAbsoluteUrl('requests/DayoffRequest');?>";
	$.ajax({type: "POST",data: {dat: date},url: url, dataType: "json",
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
				custom_alert('INVALID IN LIEU OF DATE', date+' has 0 working hours submitted', action_but);	
				} else { document.getElementById("submitrequest").style.visibility = "visible"; } } }	}); };
	function changeCategory(element) {
		$this =  $(element);		
		if($this.val()=='90') {
			$('.inLieuOf1').removeClass('hidden');	$('#description').removeClass('hidden');	$('#halfday').removeClass('hidden');	$('#labelprojects').addClass('hidden');
			$('#labeldescription').removeClass('hidden');	}else{
				if($this.val()=='533' || $this.val()=='534') {	$('#description').removeClass('hidden');	$('#labeldescription').addClass('hidden');		$('#labelprojects').removeClass('hidden');	} 
					if($this.val()=='91') {
						$('#description').addClass('hidden');	$('.inLieuOf1').addClass('hidden');		$('#labeldescription').addClass('hidden');
						$('#labelprojects').removeClass('hidden');	$('#halfday').removeClass('hidden');
					}else{	$('#description').addClass('hidden');	$('.inLieuOf1').addClass('hidden');		$('#halfday').addClass('hidden'); $('#labeldescription').addClass('hidden');
					$('#labelprojects').removeClass('hidden');	}	} }
	$("#start_date").change(function(){
	var d = new Date();   var year_now = (d.getFullYear()).toString();    var type = $("#typeDropDown option:selected").val();
  	if(type=="91"){
	var start= ($("#start_date").val()).toString();	var start_fix_dd= start.substr(0,2);	var start_fix_mm= start.substr(3,2);	var start_fix_yy= start.substr(6);
	var start = start_fix_mm+"/"+start_fix_dd+"/"+start_fix_yy; 	var date1= new Date(start);	console.log(date1);
	var end= ($("#end_date").val()).toString();	var end_fix_dd= end.substr(0,2);	var end_fix_mm= end.substr(3,2);	var end_fix_yy= end.substr(6);
	var end = end_fix_mm+"/"+end_fix_dd+"/"+end_fix_yy;	var date2= new Date(end);	console.log(date2);	var timeDiff = Math.abs(date2.getTime() - date1.getTime());
	var diffDays = Math.ceil(timeDiff / (1000*3600*24))+1; 	console.log(diffDays);	var month_start = start_fix_mm;	var year = parseInt(start.substr(6));
	var weekend_count= isWeekend(date1,date2);	var diffDays= diffDays-weekend_count;	console.log("Days off without weekend:"+diffDays);
	if(month_start =="01" && year_now!= year){	year=year-1;		console.log("We are in Jan so year is: "+year);	}
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
				custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit! You have requested '+diffDays, action_but);	
			  		
		}else{	document.getElementById("submitrequest").style.visibility="visible";	}	
	}else{
	var url = "<?php echo Yii::app()->createAbsoluteUrl('requests/VacationDaysHRRequest');?>";
	$.ajax({type: "POST",	data: {selected:year, start:start},url: url, dataType: "json",
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
				custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit! You have requested '+diffDays+' and the remaining balance is '+data.daysleft, action_but);	
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
					custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit! You have requested '+diffDays+' and the remaining balance is '+data.daysleft, action_but);	
			  		}else{ document.getElementById("submitrequest").style.visibility="visible"; } } } }	}); }	} });
	$("#end_date").change(function(){
	var d = new Date();    var year_now = (d.getFullYear()).toString();    var type = $("#typeDropDown option:selected").val();
   	if(type=="91"){
	var start= ($("#start_date").val()).toString();	var start_fix_dd= start.substr(0,2);	var start_fix_mm= start.substr(3,2);	var start_fix_yy= start.substr(6);
	var start = start_fix_mm+"/"+start_fix_dd+"/"+start_fix_yy;	var date1= new Date(start);	console.log(date1);
	var end= ($("#end_date").val()).toString();	var end_fix_dd= end.substr(0,2);	var end_fix_mm= end.substr(3,2);	var end_fix_yy= end.substr(6);
	var end = end_fix_mm+"/"+end_fix_dd+"/"+end_fix_yy;	var date2= new Date(end);	console.log(date2);
	var timeDiff = Math.abs(date2.getTime() - date1.getTime());	var diffDays = Math.ceil(timeDiff / (1000*3600*24))+1; 	console.log(diffDays);
	var month_start = start_fix_mm;	var year = parseInt(start.substr(6));	var weekend_count= isWeekend(date1,date2);
	var diffDays= diffDays-weekend_count;	console.log("Days off without weekend:"+diffDays);
	if(month_start =="01" && year_now!= year){		year=year-1;	console.log("We are in Jan so year is: "+year);	}
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
				custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit! You have requested '+diffDays, action_but);	
		}else{	document.getElementById("submitrequest").style.visibility="visible"; }
	}else{
	var url = "<?php echo Yii::app()->createAbsoluteUrl('requests/VacationDaysHRRequest');?>";
	$.ajax({type: "POST",data: {selected:year, start:start},	url: url, dataType: "json",
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
				//custom_alert('ERROR MESSAGE', 'No Vacation Days Left!', action_but);	
				custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit! You have requested '+diffDays+' and the remaining balance is 0', action_but);	
			  		
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
					custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit! You have requested '+diffDays+' and the remaining balance is '+data.daysleft, action_but);	
			  		
					//custom_alert('ERROR MESSAGE', 'Choosing these dates will exceed your Vacation Days Limit!', action_but);	
			  		}else{ document.getElementById("submitrequest").style.visibility="visible";		}  	}  	}	} }); }	} });
function isWeekend(date1, date2) {
   	var user_branch = "<?php echo Requests::getUserBranch(); ?>";   	console.log("User Branch:"+user_branch);
    var d1 = new Date(date1),
     d2 = new Date(date2), 
        isWeekend = false;
        var count=0;
    while (d1 <= d2) {
        var day = d1.getDay();
        if(user_branch=="UAE"){     	isWeekend = (day == 6) || (day==5);       }
        else{     	isWeekend = (day == 6) || (day == 0);        } 
        if (isWeekend) { 	count++; }	
        d1.setDate(d1.getDate() + 1);
    }
    console.log("Weekend count: "+count);
    return count;
}
function CheckOrUncheckInput(obj){
		var checkBoxDiv = $(obj);
		var input = checkBoxDiv.find('input[type="checkbox"]');		
		if (checkBoxDiv.hasClass('checked')) {
			checkBoxDiv.removeClass('checked');
			input.prop('checked', false);
			$('.plus').addClass('hidden');
					if($('.inLieuOf2').hasClass('hidden')){
					}else {	$('.inLieuOf2').addClass('hidden');		$('.minus').addClass('hidden');	}	}
		else {
			checkBoxDiv.addClass('checked');	input.prop('checked', true);	$('.plus').removeClass('hidden');	}	}
</script>