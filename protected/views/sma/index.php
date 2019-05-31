<?php Yii::app()->clientScript->registerScript('search', "	$('.search-form form').submit(function(){ $.fn.yiiGridView.update('sma-grid', { data: $(this).serialize()		});		return false;	});"); ?>
<div id="sp_whole_page"><div class="search-form"><?php  $this->renderPartial('_search',array(	'model' => $model, 	'provider' => $provider )); 	?></div>
<?php $searchArray = isset($_GET['Sma']) ? $_GET['Sma'] : Utils::getSearchSession();?><?php 
	$this->widget('zii.widgets.grid.CGridView', array('id'=>'sma-grid','afterAjaxUpdate' => "function(id, data){ $('#incidents').html($('#incidents', $.parseHTML(data)));}",
	'dataProvider' => $provider,'summaryText' => '','selectableRows'=>1,'pager'=> Utils::getPagerArray(),  'template'=>'{items}{pager}',
	'columns'=>array(		
		array('header'=>Yii::t('translations', 'SSN#'),'value'=>'$data->renderSRNumber()',	'name' => 'id_no','htmlOptions' => array('class' => 'column70'), 'headerHtmlOptions' => array('class' => 'column70'),   ),
        array('name' => 'Customer',	'header' => 'Customer','value' => '$data->idCustomer->name','htmlOptions'=>array('class'=>'width211'), ),
        array('header' => 'SMA Instance','value'=> '$data->instance','htmlOptions'=>array('class'=>'width152'),),
        array('name' => 'status','header' => 'Status','htmlOptions'=>array('class'=>'paddingl16 width119'),'headerHtmlOptions' => array('class' => 'paddingl16'),'value' => 'Sma::getStatusLabel($data->status)'),
		array('name' => 'month','header'=>'month/year','htmlOptions'=>array('class'=>'paddingl16 width119'),'value'=> 'Sma::getMonthName($data->sma_month).$data->sma_year'	),
		array( 'name' => 'assigned_to',	'header' => 'ASSIGNED TO',    	'type'=>'raw',       	'htmlOptions'=>array('class'=>'paddingl16'),
         	'headerHtmlOptions' => array('class' => 'paddingl16'), 'value' => '(GroupPermissions::checkPermissions(\'general-sma\',\'write\') && $data->status!= 3)?  Sma::getSmaUsers($data->id,$data->assigned_to) :  Users::getUsername($data->assigned_to)',
        ),),)); ?></div>
<script type="text/javascript">
function triggerSDSearch(status) {	status = parseInt(status);	$('#Sma_status').val(status);	$('.search-btn').trigger('click'); }
function changeAssigned(value,id){
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('Sma/assigned');?>",  	dataType: "json",  	data: {'value':value,'id':id},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {  console.log('da');	  }  } }	}); }
function changeDate(id){	$("input#change_"+id).datepicker({ dateFormat: 'dd/mm/yy' }).datepicker( "show" ); 	$('#ui-datepicker-div').css('top',parseFloat($("input#change_"+id).offset().top) + 25.0);
 	$('#ui-datepicker-div').css('left',parseFloat($("input#change_"+id).offset().left)); }
$(document).on('click','.toggle',function(){
    if ($(this).children().is('.icon-plus')) {
      $('#search_support').addClass('hide');   $('#icon-hide-show').html('Show Search Bar');   $(this).children().removeClass('icon-plus');
      $(this).children().addClass('icon-minus');
  }else{
     $(this).children().addClass('icon-plus');     $(this).children().removeClass('icon-minus');     $('#icon-hide-show').html('Hide Search Bar');     $('#search_support').removeClass('hide');
  } });
$( "#export" ).click(function() {
	$.ajax({type: "POST",data: $('#search_support').serialize()  + '&ajax=sma-form', 	url: "<?php echo Yii::app()->createAbsoluteUrl('Sma/getExcelGrid');?>", 
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
						url: "<?php echo Yii::app()->createAbsoluteUrl('Sma/sendFollowUp');?>",
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
