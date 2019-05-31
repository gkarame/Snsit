<div class="mytabs maintenance_edit"><?php $form=$this->beginWidget('CActiveForm', array('id'=>'invoice-form','enableAjaxValidation'=>false,'htmlOptions' => array(	'class' => 'ajax_submit',	'enctype' => 'multipart/form-data',	),	)); ?>
	<?php if(GroupPermissions::checkPermissions('financial-invoices','write'))	{	?>	<div id="invoice_header" class="edit_header">
		<div class="header_title">	<span class="red_title"><?php echo Yii::t('translations', 'INVOICE HEADER');?></span>
			<div class="btn" style="margin-top:-25px">	<div class="wrapper_action" id="action_tabs_right">
						<div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
					<div class="action_list actionPanel">  	<div class="headli"></div>	<div class="contentli">
							<?php if ( $model->status == "To Print" || $model->status == "New" || $model->status=="Printed"){?>
								<div class="cover"><div class="li noborder"><a class="special_edit_header" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('invoices/updateHeader', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'EDIT HEADER');?></a></div>
								</div><?php }?><?php if($model->status == "New" || $model->status == "Cancelled"){?>
							<div class="cover toprint"><div class="li noborder toprint" onclick="changeStatus(<?php echo $model->id?>);"><?php echo Yii::t('translations', 'TO PRINT');?></div>
							</div><?php }else if( $model->status == "To Print" || $model->status == "Printed"){?>
							<div class="cover"><div class="li noborder printone" ><a class="special_edit_header" href="<?php echo $this->createUrl('invoices/printOne', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'PRINT');?></a></div>
							</div><?php }?><div class="cover"><div class="li noborder"><a class="special_edit_header" href="<?php echo $this->createUrl("site/shareby", array('id' => $model->id));?>" title="Share" onclick="shareBySubmit(this, 'invoicesshare');return false;"><?php echo Yii::t('translations', 'SHARE');?></a></div>
							</div><?php if($model->status == "New" || $model->status == "Cancelled"){?><div class="cover">
									<div class="li noborder delete" onclick="alertDelete(this);"><?php echo Yii::t('translations', 'DELETE');?></div>
								</div><?php }?></div><div class="ftrli"></div></div><div id="users-list" style="display:none;"></div>
				 </div>	</div>	</div>		<div class="header_content tache">	<?php $this->renderPartial('_header_content', array('model' => $model));?>
		</div><div class="hidden edit_header_content tache new" style="width:98%;height:170px;"></div>	<br clear="all" />	</div>	<?php $this->endWidget(); ?></div><br clear="all" />
<script type="text/javascript">
function changeStatus(id){
	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/changeStatus');?>/"+id, 	dataType: "json",
	  	success: function(data) {
		  	if (data) {
			  	if (data.status== 'success') {
			  		$('.action_list').hide();
			  		$(".status_invoice").html(data.status_invoice);
			  		$(".toprint").html('<div class="li noborder printone" ><a class="special_edit_header" href="<?php echo $this->createUrl('invoices/printOne', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'PRINT');?></a></div>');
					$('.delete').addClass('hidden');
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
		  			custom_alert('ERROR MESSAGE', "Please select an Invoice Date", action_buttons);
		  		}	}  	}	});}
function showHeader(element){
	var url = $(element).attr('href');
	$.ajax({type: "POST",  	url: url,	dataType: "json",
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
					$('.edit_header_content').html(data.html);
					$('.edit_header_content').removeClass('hidden');
					$('.header_content').addClass('hidden');
					$('.action_list').hide();
			  	}  	}  		}	}); }
function updateHeader(element) {
	$.ajax({
 		type: "POST",
 		data: $('#header_fieldset').serialize()  + '&ajax=maintenance-form',					
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/updateHeader', array('id' => $model->id));?>", 
	  	dataType: "json",
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'saved' && data.html) {			  		
			  		$('.header_content').html(data.html);
			  		$('.header_content').removeClass('hidden');
			  		$('.edit_header_content').addClass('hidden');
			  	} else {
			  		if (data.status == 'success' && data.html) {
			  			$('.action_list').hide();
			  			$('.edit_header_content').removeClass('hidden');
			  			$('.edit_header_content').html(data.html);
			  			$('.header_content').addClass('hidden');
			  		}
			  	}
			  	showErrors(data.error);
		  	} 		}	}); }
function alertDelete(){
	$('.action_list').hide();
	buttons = {
	        "YES": {
	        	class: 'yes_button',
	        	click: function() 
		        {
		            $( this ).dialog( "close" );
		            
		            deleteInvoice();
		        }
	        },
	        "NO": {
	        	class: 'no_button',
	        	click: function() 
		        {
		            $( this ).dialog( "close" );
		        }
	        }
	}
	custom_alert("DELETE MESSAGE", "Are you sure you want to delete these invoice?", buttons);
}
function deleteInvoice(){
	$.ajax({
 		type: "POST",
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/delete', array('id'=>$model->id));?>",
	  	dataType: "json",
	  	data: {},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
			  		closeTab(configJs.current.url);				  	
			  	} else {	}		  	}  		}	}); }
function checkStatus(){}
</script>