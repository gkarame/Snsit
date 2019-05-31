<?php
Yii::app()->clientScript->registerScript('search', "$('.search-button').click(function(){	$('.search-form').toggle();	return false;});
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('maintenance-grid', {		data: $(this).serialize()	});	return false;});");?>

<div class="search-form" style="overflow: inherit;">
<?php $this->renderPartial('_search',array(	'model'=>$model,)); ?>
<?php $searchArray = Utils::getSearchSession();?>
<!-- <div class="header_title" id="export">	<a>Export to Excel</a>
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'ea-header-form',	'enableAjaxValidation'=>false,)); ?>
</div>-->
</div><?php
	$tmpl = '{delete}';
	$butoane = array('delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,),);		
$this->widget('zii.widgets.grid.CGridView', array('id'=>'maintenance-grid','dataProvider'=>$model->search(),'summaryText' => '',
	'selectableRows'=>1,'selectionChanged'=>'function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "'.$this->createUrl('view').'/"+idg;}',
	'pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}',
	'columns'=>array(
		array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice'),'selectableRows'=>2,),
		array('name' => 'customer','value'=>'$data->customer0->name','headerHtmlOptions' => array('class' => 'column100'),),
		array('name' => 'Description','value'=>'$data->short_description','headerHtmlOptions' => array('class' => 'column100'),	),
		array('name' => 'Service','value'=>'empty($data->supportService)? " " : $data->supportService->codelkup','headerHtmlOptions' => array('class' => 'column100'),),
		array('name' => 'Product','value'=>'Maintenance::getProductDesc($data->product)','headerHtmlOptions' => array('class' => 'column100'),),
		array('name' => 'owner','value'=>'$data->owner0->codelkup',	'headerHtmlOptions' => array('class' => 'column50'),),
		array('name' => 'freq','value'=>'$data->frequency0->codelkup','headerHtmlOptions' => array('class' => 'column50'),),
		array('name' => 'Amount','value'=>'Utils::formatNumber($data->getTotalGrossAmountUsd())','headerHtmlOptions' => array('class' => 'column50'),),
		array('name' => 'Net','value'=>'Utils::formatNumber($data->getTotalNetAmountUsd())','headerHtmlOptions' => array('class' => 'column50'),),
		array('name' => 'Start date','value'=>'empty($data->starting_date)? " " : date(\'d/m/Y\', strtotime($data->starting_date))',
			'headerHtmlOptions' => array('class' => 'column100'),),		
		array('name' => 'escalation_factor','value'=>'$data->escalation_factor','headerHtmlOptions' => array('class' => 'column65'),),
		array('name' => 'status','value'=>'$data->status',),	
		array('class' => 'CCustomButtonColumn','template'=>$tmpl,'htmlOptions'=>array('class' => 'button-column','style'=>'text-align:left;'),'buttons'=>$butoane,),),)); ?>
<div id="popupinv" > <div class='titre red-bold'>Input Invoice Dates</div> 	<div class='closetandm2'> </div><div class='maintdatescontainer'>					
	<div class="row startDateRow">	<?php  echo "From Period"; ?>	<div class="dateInput">
			<?php   $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'starting_date','cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'start_date'),));?>
			<span class="calendar calfrom"></span>	<?php echo CCustomHtml::error($model,'starting_date');  ?>	</div></div>	
<div class="row endDateRow">
		<?php echo "To Period"; ?>	<div class="dateInput">
			<?php  $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'starting_date','cssFile' => false,
		        'options'=>array('dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'end_date'),));	?>
			<span class="calendar calfrom"></span>	<?php echo  CCustomHtml::error($model,'starting_date'); ?>
		</div>	</div></div> 
	<div class='submitandm'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-top:-5px !important;margin-left:135px;' ,'onclick' => 'createInvmaint();return false;','id'=>'createbut')); ?>
				<span class="cancel" style="cursor: pointer;font-size:13px;margin-left:-15px !important;" onclick="$('#popupinv').fadeOut();">CANCEL</span></div>
	</div><div id="popupmaintenance" > <div class='titre red-bold'>Input Dates</div> <div class='closetandm2'> </div>	<div class='maintratecontainer'>					
	<div class="row startDateRow">	<?php  echo "From Date"; ?>	<div class="dateInput">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'starting_date','cssFile' => false,
		        'options'=>array('dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'start_date'),   ));?>
			<span class="calendar calfrom"></span>	<?php echo CCustomHtml::error($model,'starting_date');  ?>	</div></div>
<div class="row endDateRow"><?php echo "To Date"; ?> <div class="dateInput">	<?php   $this->widget('zii.widgets.jui.CJuiDatePicker',array(
		        'model'=>$model,'attribute'=>'starting_date','cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),
		    	'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'end_date'),)); ?>
			<span class="calendar calfrom"></span>	<?php echo  CCustomHtml::error($model,'starting_date'); ?>	</div></div>
<?php $this->endWidget(); ?></div> 
		<div class='submitandm'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:135px;' ,'onclick' => 'exportexcel2();return false;','id'=>'createbut')); ?>
		</div>	</div>
<script type="text/javascript">
$(document).ready(function(){	$('#popupmaintenance').hide();	$('#popupinv').hide();});
 $(".closetandm2").click(function() { $('.action_list').hide();	$('#popupmaintenance').stop().hide();	$('#popupinv').stop().hide();	});
function createContract(){
		$.ajax({ type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/create');?>",dataType: "json",
		  	success: function(data) { if (data) { if (data.status == 'success') { $('.action_list').hide(); } } } }); }
function exportexcel(){	$.ajax({ type: "POST",data: $('#search_maintenance').serialize()  + '&ajax=maintenance-form',					
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/getExcel');?>", dataType: "json",
	  	success: function(data) {
			  	if(data.success == 'success'){
			  		window.location = "<?php echo Yii::app()->createAbsoluteUrl("site/download", array('file'=>Utils::getFileExcel(true)));?>"; $('.action_list').hide();
			  	} }	});	}
function openinvpopupfilter(){
$.ajax({ type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/inputInv');?>",dataType: "json",  	data: $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) { 		if (data) { if(data.status=='success'){ $('#popupinv').stop().show(); $("#end_date").val(data.dateend); $("#start_date").val(''); }else{
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
		  			} }}}); }
function openpopupfilter(){ $('#popupmaintenance').stop().show();
$.ajax({	type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/inputDate');?>",dataType: "json",
		  	success: function(data) {
		  		if (data) {
		  			if(data.status=='success'){ $('.closetandm').attr("id_project",data.id_project); $('.closetandm').attr("month",data.month);
		  				$('.closetandm').attr("year",data.year); $('.maintratecontainer').html(data.rate_table);
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
		  			}			  		
		  		}}}); }
function createInvmaint(){
		var start= ($("#start_date").val()).toString();	var end= ($("#end_date").val()).toString();	var dt1   = parseInt(start.substring(0,2));
		var mon1  = parseInt(start.substring(3,5));	var yr1   = parseInt(start.substring(6,10)); var date1 = new Date(yr1, mon1-1, dt1);
		var dt1   = parseInt(end.substring(0,2));	var mon1  = parseInt(end.substring(3,5));	var yr1   = parseInt(end.substring(6,10));
		var date2 = new Date(yr1, mon1-1, dt1);		
		if (date1>date2){
			var action_buttons = {
			    "Ok": {
			       	class: 'ok_button',
			       	click: function() 
			        {
			            $( this ).dialog( "close" );
			        }
			    }
			}
			custom_alert('ERROR MESSAGE', "Invalid Start Date", action_buttons);
		}else{		
			$.ajax({type: "POST",	data: $('.checkbox_grid_invoice input').serialize()  + '&ajax=maintenance-form&start='+start+'&end='+end,	
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/createInvMaint');?>", dataType: "json",  	success: function(data) {
				  	if(data.status == 'success'){ $('#popupinv').fadeOut(); }else {
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
				  	} } });	} }
function exportexcel2(){
		var start= ($("#start_date").val()).toString();	var end= ($("#end_date").val()).toString();	var dt1   = parseInt(start.substring(0,2));
		var mon1  = parseInt(start.substring(3,5));	var yr1   = parseInt(start.substring(6,10)); var date1 = new Date(yr1, mon1-1, dt1);
		var dt1   = parseInt(end.substring(0,2));	var mon1  = parseInt(end.substring(3,5));	var yr1   = parseInt(end.substring(6,10));
		var date2 = new Date(yr1, mon1-1, dt1);		
		if (date1>date2){
		var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', "Invalid Start Date", action_buttons);
		}else{
		$.ajax({type: "POST",data: $('#search_maintenance').serialize()  + '&ajax=maintenance-form&start='+start+'&end='+end,	
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/getExcel2');?>", 	dataType: "json",
	  	success: function(data) {  	if(data.success == 'success'){
			  		window.location = "<?php echo Yii::app()->createAbsoluteUrl("site/download", array('file'=>Utils::getFileExcel(true)));?>";
			  			$('.action_list').hide(); $('#popupmaintenance').stop().hide(); } }	});	} }
</script>