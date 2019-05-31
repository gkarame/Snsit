<?php  Yii::app()->clientScript->registerScript('search', "	$('.search-form form').submit(function(){		$.fn.yiiGridView.update('supportdesk-grid', {	data: $(this).serialize()	});		return false;	});");?>
	<div id="sp_whole_page"><div class="search-form"><?php $this->renderPartial('_search',array('model' => $model,	'provider' => $provider	)); ?></div>
<?php $searchArray = isset($_GET['Maintenance']) ? $_GET['Maintenance'] : Utils::getSearchSession();?>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'supportdesk-grid','afterAjaxUpdate' => "function(id, data){ $('#incidents').html($('#incidents', $.parseHTML(data)));}",
	'dataProvider' => $provider,'summaryText' => '','selectableRows'=>1,'pager'=> Utils::getPagerArray(),  'template'=>'{items}{pager}',
	'columns'=>array(		array('class'=>'CCheckBoxColumn','htmlOptions' => array('class' => 'item checkbox_grid_sd'),'selectableRows'=>2,'disabled'=>'$data->status != SupportDesk::STATUS_PENDING_INFO && $data->status != SupportDesk::STATUS_CLOSED','visible' => Yii::app()->user->isAdmin),
		array('header'=>Yii::t('translations', 'SR #'),'value'=>'$data->renderSRNumber()','name' => 'sd_no','cssClassExpression' => 'SupportDesk::getCheckedUsers($data->sd_no, $data->status) > 0 ? "" : "boldw"','htmlOptions' => array('class' => 'column50'), 'headerHtmlOptions' => array('class' => 'column50'),),
        array('name' => 'product','header' => (Yii::app()->user->isAdmin != true) ? "PRODUCT": "CUSTOMER",  'value' => '(Yii::app()->user->isAdmin != true)? $data->product0->codelkup : $data->idCustomer->name',
        	'htmlOptions'=>array('class'=>'width152', 'id'=>'cust_prod'),  ),
		array('name' => 'severity','header'=>'Severity','value'=> 'SupportDesk::getSeverityLevel($data->severity)'	),
        array('name' => 'status','header' => 'Status','value' => 'SupportDesk::getStatusLabel($data->status)'),
        array('name' => 'date','header' => 'Logged','value' => 'date("d/m/Y", strtotime($data->date))'),     
		array('name' => 'short_description','header' => 'Short Description','value' => '$data->short_description','htmlOptions'=>array('class'=>'width195'),),
		array('name' => 'rsr','header' =>'rsr#','value' => 'SupportRequest::getRSR($data->id)','visible'=> (Yii::app()->user->isAdmin != true) ? false:true),
         array('name' => 'assing_to','header' => 'ASSIGNED TO','type'=>'raw','htmlOptions'=>array('class'=>'width100'),'value' => '((Users::checkCSManagers(Yii::app()->user->id)) > 0)?  SupportDesk::getSupportDeskUsers($data->id,$data->assigned_to) :  Users::getUsername($data->assigned_to) ',),),)); ?></div>
<script type="text/javascript">
function triggerSDSearch(status) {	status = parseInt(status);	$('#inv_status').val(status); changestat($('#inv_status').val());	$('.search-btn').trigger('click'); }

function changestat(element) {
	fdate = element.toString();
	fdate= fdate.replace('0','New');fdate= fdate.replace('1','In Progress');fdate= fdate.replace('2','Awaiting Customer');
	fdate= fdate.replace('3','Solution Proposed');fdate= fdate.replace('4','Reopened');fdate= fdate.replace('5','Closed');
	fdate= fdate.replace(' ','');
	fdate= fdate.substring(0, 18);	
	$('#status_str').html(fdate); }

function changeInput(value,id_support_dask,type){
	$.ajax({
 		type: "POST",
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/assigned');?>",
	  	dataType: "json",
	  	data: {'value':value,'id_support_desk':id_support_dask,'type':type},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {  		console.log('da');  	}  	}	} }); }
function showdropdown(){		document.getElementById('inv_status').style.visibility="visible";	}
function changeDate(id){
	$("input#change_"+id).datepicker({ dateFormat: 'dd/mm/yy' }).datepicker( "show" );
 	$('#ui-datepicker-div').css('top',parseFloat($("input#change_"+id).offset().top) + 25.0);
 	$('#ui-datepicker-div').css('left',parseFloat($("input#change_"+id).offset().left)); }
$(document).on('click','.toggle',function(){
    if ($(this).children().is('.icon-plus')) {
      $('#search_support').addClass('hide');
      $('#icon-hide-show').html('Show Search Bar');
      $(this).children().removeClass('icon-plus');
      $(this).children().addClass('icon-minus');
  }else{
     $(this).children().addClass('icon-plus');
     $(this).children().removeClass('icon-minus');
     $('#icon-hide-show').html('Hide Search Bar');
     $('#search_support').removeClass('hide');
  } });
$( "#export" ).click(function() {
	$.ajax({type: "POST",	data: $('#search_support').serialize()  + '&ajax=supportdesk-form',	url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/getExcelGrid');?>", 
	  	dataType: "json",
	  	success: function(data) {
		  	if(data.success == 'success')
		  		window.location = "<?php echo Yii::app()->createAbsoluteUrl("site/download", array('file'=>Utils::getFileExcel(true)));?>";
		  	
  		}	}); });
$('.followUpButton').click(function() {
	var action_buttons = {
        "Ok": {
        	class: 'ok_button',
        	click: function() {
	            $( this ).dialog( "close" );
	        }
        }
	};
	send = $('.checkbox_grid_sd input').serialize();
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
								console.log(data);
								$('input[type="checkbox"]').prop('checked', false);
								dialog.dialog( "close" );
							}
						}			
					});
					
		        }
	        },
		"No": {
				class: 'no_button',
				click: function() {$(this).dialog("close");}
			}
  		});	} });
</script>
