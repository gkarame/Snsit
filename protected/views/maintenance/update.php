<div class="mytabs maintenance_edit">
		<?php $form=$this->beginWidget('CActiveForm', array('id'=>'maintenance-form','enableAjaxValidation'=>false,	'htmlOptions' => array(	'class' => 'ajax_submit',	'enctype' => 'multipart/form-data',	),	)); ?>
		<div id="popupinvitems" > <div class='titre red-bold'>Input Invoice Dates</div> <div class='closetandm2'> </div>
		<div class='maintdatescontainer'>	<div class="row startDateRow">	<?php  echo "From Period"; ?>	<div class="dateInput">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'starting_date','cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),
		    	'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'start_date'),));?>
			<span class="calendar calfrom"></span><?php echo CCustomHtml::error($model,'starting_date');  ?></div></div>	
<div class="row endDateRow"><?php echo "To Period"; ?>	<div class="dateInput">
			<?php   $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'starting_date','cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),
		    	'htmlOptions'=>array('class'=>'width111','autocomplete'=>'off','id'=>'end_date'),));?>
			<span class="calendar calfrom"></span>	<?php echo  CCustomHtml::error($model,'starting_date'); ?>	</div></div></div> 
			<div class='submitandm'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-top:-5px !important;margin-left:135px;' ,'onclick' => 'createInvmaintItems();return false;','id'=>'createbut')); ?>
			<span class="cancel" style="cursor: pointer;font-size:13px;margin-left:-15px !important;" onclick="$('#popupinvitems').fadeOut();">CANCEL</span></div>
	</div>	<div id="maintenance_header" class="edit_header"><div class="header_title">	<span class="red_title"><?php echo Yii::t('translations', 'SERVICE HEADER');?></span>
				<a class="header_button" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('maintenance/updateHeader', array('id' => $model->id_maintenance));?>"><?php echo Yii::t('translations', 'Edit Header');?></a>
			</div><div class="header_content tache"><?php $this->renderPartial('_header_content', array('model' => $model));?></div>
			<div class="hidden edit_header_content tache new" style="width:98%;height:480px;"></div><br clear="all" /></div>
		<div id="maintenance_items"><div class="header_title"><span class="red_title"><?php echo Yii::t('translations', 'ADDITIONAL SERVICES CONTRACTS');?></span>
				<div class="wrapper_action" id="action_tabs_right" style="margin-top:-30px;"><div onclick="chooseActions();" class="action triggerAction" style="margin-right:20px;"><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel" style="margin-top:-25px;"><div class="headli"></div><div class="contentli">
					<?php if(GroupPermissions::checkPermissions('financial-maintenance','write')){ ?>
						<div class="cover"><div class="li noborder" onclick="openinvpopupfilter();">NEW INVOICE</div></div>
						<div class="cover"><div class="li noborder" onclick="deactivate();">DEACTIVATE ITEM(S)</div></div><?php } ?></div><div class="ftrli"></div>
					</div></div></div>	<div id="maintenance_items_content" class="border-grid grid">
				<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid','dataProvider'=>$model->items,'summaryText' => '',
					'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}','afterAjaxUpdate' => 'js:function() {panelClip(".item_clip");panelClip(".term_clip");}',
					'columns'=>array(
							array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice'),'selectableRows'=>2,),
							array('header' => 'contract description','header'=> Yii::t('translations', 'CONTRACT DESCRIPTION'),
								'value' => '$data->contract_description','type'=>'raw','htmlOptions'=>array('class'=>'paddingl16')),
							array('header' => 'amount','header'=> Yii::t('translations', 'GROSS AMOUNT'),'value' => 'Utils::formatNumber($data->amount)','type'=>'raw'),
							array('header' => 'original_currency','header'=> Yii::t('translations', 'CURRENCY'),'value' => '$data->currency0->codelkup','type'=>'raw'),
							array('header' => 'amount','header'=> Yii::t('translations', 'GROSS AMOUNT USD'),'value' => 'Utils::formatNumber($data->amount_usd)','type'=>'raw'),
							array('header' => 'amount','header'=> Yii::t('translations', 'NET AMOUNT USD'),'value' => 'Utils::formatNumber($data->amount_usd*($data->sns_share/100))','type'=>'raw'),
							array('header' => 'sns_share','header'=> Yii::t('translations', 'SNS SHARE %'),'value' => '$data->sns_share','type'=>'raw'),
							array('class'=>'CCustomButtonColumn','template'=>' {update} {delete}','htmlOptions'=>array('class' => 'button-column'),
									'afterDelete'=>'function(link,success,data){ if (success) {
																var response = jQuery.parseJSON(data); 
											  					$.each( response.total, function( key, value ) { $("#"+key).html(value); });
											  					$.fn.yiiGridView.update("terms-grid"); }}',
									'buttons'=>array(
						            	'update' => array('label' => Yii::t('translations', 'Edit'),'imageUrl' => null,
										'url' => 'Yii::app()->createUrl("maintenance/manageItem", array("id"=>$data->id))',
						            	'options' => array('onclick' => 'showItemForm(this, false);return false;'),),
										'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,'url' => 'Yii::app()->createUrl("maintenance/deleteItem", array("id"=>$data->id))',  
						                	'options' => array('class' => 'delete'),),),),),)); ?>
				<div class="tache new_item" ><div onclick="showItemForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u></div>	</div>
				<div class="total_amounts totalrow"><div class="column inline-block width161">
						<span class="title"><?php echo Yii::t('translations', 'TOTAL PERIODIC AMOUNT USD');?></span><br /><br />
						<span id="total_periodic_amount" class="value"><?php echo Utils::formatNumber($model->getTotalPeriodicAmountUsd());?></span>
					</div><div class="column inline-block middleitem">
						<span class="title"><?php echo Yii::t('translations', 'TOTAL GROSS AMOUNT USD');?></span>	<br /><br />			
						<span id="total_gross_amount" class="value"><?php echo Utils::formatNumber($model->getTotalGrossAmountUsd());?></span>
					</div>	<div class="column inline-block middleitem"><span class="title"><?php echo Yii::t('translations', 'TOTAL NET AMOUNT <br/> USD');?></span>
						<br /><br /><span id="total_net_amount " class="value paddingt10"><?php echo Utils::formatNumber($model->getTotalNetAmountUsd()); ?></span>
					</div>	<div class="column inline-block width161 last">
						<span class="title"><?php echo Yii::t('translations', 'TOTAL PARTNER AMOUNT USD');?></span>
						<br /><br /><span id="total_partent_amount" class="value"><?php echo Utils::formatNumber($model->getTotalPartnerAmountUsd()); ?></span>
					</div>	</div>	</div>	</div>
		<div id="maintenance_invoices">	<div class="header_title">	<span class="red_title"><?php echo Yii::t('translations', 'SERVICE INVOICES');?></span>
			</div>	<div id="maintenance_items_content" class="border-grid grid">
				<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid-invoices','dataProvider'=>$model->getMaintenanceInvoices(),
					'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}','afterAjaxUpdate' => 'js:function() {panelClip(".item_clip");panelClip(".term_clip");}',
					'columns'=>array(
							array('header' => 'contract description','header'=> Yii::t('translations', 'INVOICE #'),'value' => '$data->idInvoice->renderInvoiceNumber()','type'=>'raw','htmlOptions'=>array('class'=>'paddingl16')),
							array('header' => 'amount','header'=> Yii::t('translations', 'FROM PERIOD'),'value' => 'date("d/m/Y",strtotime($data->from_period))','type'=>'raw'),
							array('header' => 'original_currency','header'=> Yii::t('translations', 'TO PERIOD'),'value' => 'date("d/m/Y",strtotime($data->to_period))','type'=>'raw'),
							array('header' => 'amount','header'=> Yii::t('translations', 'AMOUNT'),'value' => 'Utils::formatNumber($data->amount)','type'=>'raw'),
							array('header' => 'sns_share','header'=> Yii::t('translations', 'CURRENCY'),'value' => '$data->idInvoice->iCurrency->codelkup','type'=>'raw'),
							array('header' => 'amount','header'=> Yii::t('translations', 'AMOUNT USD'),'value' => 'Utils::formatNumber($data->getAmountUsd())','type'=>'raw'),
							array('header' => 'sns_share','header'=> Yii::t('translations', 'STATUS'),'value' => '$data->idInvoice->status','type'=>'raw'),
							array('header' => 'sns_share','header'=> Yii::t('translations', 'CREATION DATE'),'value' => '(!empty($data->idInvoice->ADDDATEINV)) ? date("d/m/Y",strtotime($data->idInvoice->ADDDATEINV)) : ""','type'=>'raw')
							,array('header'=> Yii::t('translations', 'ESC%'),'value' => 'Utils::formatNumber($data->escalation_factor)','type'=>'raw'),),)); ?>
			</div>	</div>	<div class="horizontalLine smaller_margin"></div><div id="fileuploads" class= "maintenace_upload">
		<?php	Yii::import("xupload.models.XUploadForm");
			$this->widget( 'xupload.XUpload', array('url' => Yii::app( )->createUrl("maintenance/upload"),'model' => new XUploadForm(),
	            'htmlOptions' => array('id'=>'maintenance-form'),'formView' => 'small_form','attribute' => 'file','autoUpload' => true,'multiple' => false,
				'options' => array( 'maxFileSize' => 15728640, 
					'submit' => "js:function (e, data) {
						var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."maintenance".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_maintenance}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
						var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."maintenance".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_maintenance}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
        				var model_id = '".$model->id_maintenance."'; data.formData = {path : path, publicPath: publicPath, modelId : model_id};
	                    return true; }", 'completed' => 'js: function(e, data) { console.log(data); $(".files div.box:not(:last)").remove(); }',), )); ?>
			<div class="attachments_pic"></div>	<div class="files margint18" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php if (($filepath = $model->getFile(true, true)) != null) {	$path_parts = pathinfo($filepath);	?>
				<div class="box template-download fade " id="tr0">	<div class="title">
						<a href="<?php echo $this->createUrl('site/download', array('file' => $filepath));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
					</div><div class="size"><span><?php echo Utils::getReadableFileSize(filesize($filepath));?></span></div><div class="delete">
						<button class="btn btn-danger delete"
							 data-url="<?php echo $this->createUrl( "maintenance/deleteUpload", array(
	                          	"id_maintenance" =>$model->id_maintenance,"model_id" => $model->id_maintenance,					 			
	                          	"file" => $path_parts['basename'],
	                            ));?>" 
							data-type="POST">
						</button></div></div><?php } ?></div></div>	</div>	<?php $this->endWidget(); ?><br clear="all" />
<script type="text/javascript">
$(document).ready(function() {
	if ($(".scroll1").length > 0) {	if (!$(".scroll1").find(".mCustomScrollBox").length > 0) {	$(".scroll1").mCustomScrollbar();	}	}
	panelClip('.item_clip'); panelClip('.term_clip');	$("#Maintenance_starting_date").datepicker({ dateFormat: 'dd/mm/yy' }); });
$(document).ready(function(){	$('#popupinvitems').hide();});
 $(".closetandm2").click(function() {	$('.action_list').hide();	$('#popupinvitems').stop().hide();	});
function createInvmaintItems(){	var start= ($("#start_date").val()).toString();	var end= ($("#end_date").val()).toString();
		var dt1   = parseInt(start.substring(0,2));	var mon1  = parseInt(start.substring(3,5));	var yr1   = parseInt(start.substring(6,10));
		var date1 = new Date(yr1, mon1-1, dt1);	var dt1   = parseInt(end.substring(0,2));	var mon1  = parseInt(end.substring(3,5));
		var yr1   = parseInt(end.substring(6,10));	var date2 = new Date(yr1, mon1-1, dt1);		
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
			$.ajax({type: "POST",data: $('.checkbox_grid_invoice input').serialize()  + '&ajax=maintenance-form&start='+start+'&end='+end,	
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/createInvMaintItem');?>",dataType: "json",
		  	success: function(data) {
				  	if(data.status == 'success'){ $('#popupinvitems').fadeOut();
				  	
				  	 $.fn.yiiGridView.update('items-grid-invoices');
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
				  	} } });	} }
function deactivate()
{
	$.ajax({	type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/deactivateitems');?>", 	dataType: "json",data: $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
		  		if (data) {
		  			if(data.status=='success'){ 
		  				var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ALERT MESSAGE', "Item(s) were deactivated successfully", action_buttons);
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
		  			} }}}); 
}
function openinvpopupfilter(){
$.ajax({	type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/inputInvItems');?>", 	dataType: "json",data: $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
		  		if (data) {
		  			if(data.status=='success'){ $('#popupinvitems').stop().show(); $("#end_date").val(data.dateend); $("#start_date").val('');
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
		  			} }}}); }
function panelClip(element) {
	var width = 0;
	if (element == '.item_clip')
		width = 300;
	else
		width = 90;
		
	$(element).each(function() {
		if ($(this).width() < width) {
			$(this).parent().find('u').hide();
			console.log($(this).parent().find('u').attr('class'));
		}	}); }
function showHeader(element){
	var url = $(element).attr('href');
	$.ajax({type: "POST",url: url,dataType: "json",
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
			  		$("input#Maintenance_starting_date").datepicker({ dateFormat: 'dd/mm/yy' }); $('.edit_header_content').html(data.html);
					$('.edit_header_content').removeClass('hidden'); $('.header_content').addClass('hidden'); $("input#Maintenance_starting_date").val(data.starting_date); }  	}	}	}); }
function updateHeader(element) {
	$.ajax({ type: "POST",data: $('#header_fieldset').serialize()  + '&ajax=maintenance-form',					
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('maintenance/updateHeader', array('id' => $model->id_maintenance));?>", 
	  	dataType: "json",
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'saved' && data.html) {
			  		$('.header_content').html(data.html); $('.header_content').removeClass('hidden'); $('.edit_header_content').addClass('hidden');
			  		$.each( data.total, function( key, value ) { $('#'+key).html(value); });
			  	} else {
			  		if (data.status == 'success' && data.html) { $('.edit_header_content').html(data.html);
			  		}else if (data.status == 'fail') {
			  			var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', data.errormsg, action_but);	
			  		}

			  	}
			  	showErrors(data.error);  	}	}}); } 
function showItemForm(element, newItem) {
	var url;
	if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('maintenance/manageItem');?>";
	} else {	url = $(element).attr('href');	}
	$.ajax({	type: "POST",  	url: url,  	dataType: "json",  	data: {'id_maintenance':<?php echo $model->id_maintenance;?>,'currency':<?php echo $model->currency;?>},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
				  	if (newItem) { $('.new_item').hide(); $('.new_item').after(data.form);
				  	} else { $(element).parents('tr').addClass('noback').html('<td colspan="9" class="noback">' + data.form + '</td>'); } }
				  	
			} }	}); }
function updateItem(element, id) {
	var url = "<?php echo Yii::app()->createAbsoluteUrl('maintenance/manageItem');?>";
	if (id != 'new') {	url += '/'+parseInt(id); }
	$.ajax({ type: "POST",data: $(element).parents('.items_fieldset').serialize() + '&id_maintenance=<?php echo $model->id_maintenance;?>' + '&id_contract=<?php echo $model->id_maintenance;?>',					
	  	url: url,dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.status == 'saved') {
				  	if (id == 'new') { $(element).parents('.tache.new').remove(); $('.new_item').show(); }	
			  		$.fn.yiiGridView.update('terms-grid');		$.fn.yiiGridView.update('items-grid');  $.fn.yiiGridView.update('items-grid-invoices');
	  				$.each( data.total, function( key, value ) { $('#'+key).html(value); });
			  	}else if(data.status == 'errorinv'){
				  		var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
						custom_alert('ERROR MESSAGE', data.errormsg, action_but);
				  	}  else { if (data.status == 'success') { $(element).parents('.tache.new').replaceWith(data.form); } } } }	}); }
</script>