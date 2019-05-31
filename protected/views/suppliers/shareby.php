<div class="headli"></div><div class="contentli">	<?php $form = $this->beginWidget('CActiveForm', array('id'=>'check-form',	)); ?>		
	<fieldset class="shareby_fieldset">	<div class="create"><div class="title">PRINT LETTER</div>	<div class="row"><?php echo $form->labelEx($model,'supplier'); ?>
				<div class="inputBg_create"><?php echo CHtml::textField("supplier_name",$name,array("readonly"=>true)); ?>	</div>	<?php echo CHtml::error($model,"id_supplier"); ?>	</div>		
			<div class="row">	<?php echo $form->labelEx($model,'Amount in USD'); ?>	<div class="inputBg_create">	<?php echo $form->textField($model,"amount"); ?>	</div>
				<?php echo CHtml::error($model,"amount"); ?>	</div>	<div class="row">	<?php echo $form->labelEx($model,'description'); ?>
				<div class="textareaBg_create">	<?php echo $form->textArea($model,'description'); ?>	</div>	<?php echo CHtml::error($model,"description"); ?>
			</div>	<div class="row"><?php echo $form->labelEx($model,'date'); ?>
				<?php if($model->date != null){ $model->date = date("d/m/Y",strtotime($model->date ));}
						else{  $model->date = date("t/m/Y",strtotime("now")); }	?>
				<div class="inputBg_create dataRow"><?php echo $form->textField($model,'date',array('autocomplete'=>'off')); ?><span class="calendar calfrom"></span></div>
				<?php echo $form->error($model,'date'); ?>	</div>		<div class="row buttons">
				<a href="<?php echo Yii::app()->createUrl("suppliers/createLetter");?>" class="save customSaveBtn ua" onclick="shareBySubmit(this, 'e');return false;"><?php echo Yii::t('translation', 'PRINT');?></a>
				<div class="loader"></div>	<a href="javascript:void(0);" class="customCancelBtn ua" onclick="$('.popup_shareby').fadeOut(100);$('.popup_shareby').html('');"><?php echo Yii::t('translation', 'CANCEL');?></a>
			</div>	</div></fieldset>	<?php $this->endWidget(); ?></div><div class="ftrli"></div>
<script>
$(function() {
	 	$("#SuppliersPrint_date").datepicker({ dateFormat: 'dd/mm/yy' });	  
	 	 $("#SuppliersPrint_date").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#SuppliersPrint_date").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#SuppliersPrint_date").offset().left));
		 }); });	
function shareBySubmit(element, model) {
	if (submitted == false) {
		submitted = true;
		var dialog = $('.popup_list'), send = 'model='+model;
		if (!$(element).hasClass('shareby_button')) {
			send += '&'+ $(element).parents('.shareby_fieldset').serialize();
			$('.loader').show();
		}else {	dialog.removeClass('popup_shareby').hide().html('');	}		
		var action_buttons = {
		        "Ok": {
			        	class: 'ok_button',
			        	click: function() 
				        {
				            $( this ).dialog( "close" );
				        }
		        }
  		}		
		$.ajax({type: "POST", 	url: $(element).attr('href'),  	dataType: "json", 	data: send,
		  	success: function(data) {
		  		if (data) {
		  			submitted = false;
		  			if (dialog.find('.loader').length) {	$('.loader').hide();	}
			  		if (data.status == "failure") {
			  			dialog.html(data.form);
			  			dialog.addClass('popup_shareby');
			  			dialog.show();
			  		} else {
			  			if (data.status == "success") {
							var token = new Date().getTime();
							window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('suppliers/printLetter');?>'+'?id='+data.id);
							dialog.hide();		
			  			} else {	custom_alert('ERROR MESSAGE', 'There was an error. Please try again!', action_buttons);	}	} 	}	},
			error: function() {
				submitted = false;
				if (dialog.find('.loader').length) {
	  				$('.loader').hide();
	  			}
				custom_alert('ERROR MESSAGE', 'There was an error. Please try again!', action_buttons);
			}	});	} }
</script>