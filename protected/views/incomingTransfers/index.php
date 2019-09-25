<?php Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('it-grid', { data: $(this).serialize()	});	return false; }); "); ?>
<div class="search-form" style="overflow: inherit;">
<?php $this->renderPartial('_search',array('model'=>$model,)); ?>
</div>
	<div id="recipients-list" style="display:none;top: 0%;left: 35%;    position: absolute;"></div>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'it-grid','dataProvider'=>$model->search(),'summaryText' => '','emptyText' => 'No Results Found.',
	'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
	'columns'=>array(
		array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice'),'selectableRows'=>2,),
		array('header'=>Yii::t('translations', 'TR #'),'value'=>'$data->renderRequestNumber()','name' => 'it_no',
			'htmlOptions' => array('class' => 'column50'),'headerHtmlOptions' => array('class' => 'column50'),),
        array('name' => 'id_customer','header' => Yii::t('translations', 'Customer'),'value' => 'IncomingTransfers::getCustomers($data->id)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),	

array('name' => 'partner','header' => Yii::t('translations', 'Partner'),'value' => 'Codelkups::getCodelkup($data->partner)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),	
array('name' => 'adddate','header' => 'date','value' => 'date("d/m/Y", strtotime($data->adddate))',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column110'),),		
array('name' => 'received_amount','header' => 'amount','value' => 'Utils::formatNumber($data->received_amount)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column110'),),	
array('name' => 'currency','header' => 'currency','value' => 'Codelkups::getCodelkup($data->currency)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),	
array('name' => 'offsetting','header' => 'offsetting','value' => 'IncomingTransfers::getOffsettingLabel($data->offsetting)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
array('name' => 'status','header' => 'status','value' => 'IncomingTransfers::getStatusLabel($data->status)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
		 array('name'=>'id_user','header'=>'Created By','value'=>'Users::getNameById($data->id_user)',
			'htmlOptions' => array('class' => 'column110'),'headerHtmlOptions' => array('class' => 'column110'),),), )); ?>
<script>

function getRecipients() {
	if (!$('#recipients-list').is(':visible')) {
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/GetUnssignedInvoices');?>", 
		  	dataType: "json",  	data:  $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") { 		
		  			$('#recipients-list').html(data.div); 		$('.action_list').hide(); 
		  			$('#recipients-list').show(); 		$('#recipients-list').find('.scroll_div').mCustomScrollbar();
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
			  	} }	});	
	} else { 
		$('#recipients-list').fadeOut(100);	
	}
}
function assignrecipients() {
	if ($('#unassigned-recipients-form').serialize() != '') {
		$.ajax({
			type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/assignInvoices');?>", 
		  	dataType: "json",data:  $('#unassigned-recipients-form').serialize()+'&'+$('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") {		
		  			$('#recipients-list').fadeOut(100);	UncheckAll(); 
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
			  	}  }	});
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
			custom_alert('ERROR MESSAGE', 'You have to select at least one invoice to save!', action_buttons);
	} 
}

function UncheckAll () { $('[id^="checkinvoice"]').attr('checked',false);	 }

function getExcel() {	
			$('.action_list').hide(); window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/getExcel');?>/?"); }

</script>