<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('trip_number')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode('#'.$model->id_book); ?></div>
	<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations', 'Resource')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->idUser->firstname.' '.$model->idUser->lastname); ?></div>
</div>

<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('origin_country')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->origincountry->codelkup); ?></div>	
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
	<div class="general_col4"><?php echo  CHtml::encode($model->notes); ?></div>
	
</div>
<div class="view_row">


</div>


		<div class="btn">
			
			
			<?php 
			if(GroupPermissions::checkPermissions('travel-list','write'))
			{
			?>
			<?php echo CHtml::link(Yii::t('translation', 'Add Trip'), array('createdetail?id_book=1'), array('class'=>'add-travel add-btn')); ?>
			<?php 
			}
			?>
		</div>
		<div class="horizontalLine search-margin"></div>	

	<div id="ea_items">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'TRIP DETAIL');?></span>
		</div>
		<div id="ea_items_content" class="border-grid grid">
			 <?php
				$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'items-grid',
				'dataProvider'=>$model->searchDetails($model->id_book),
				'summaryText' => '',
				'pager'=> Utils::getPagerArray(),
			    'template'=>'{items}{pager}',
				'afterAjaxUpdate' => 'js:function() {panelClip(".item_clip");panelClip(".term_clip");}',
				'columns' => TravelBooking::getColumnsForGrid($model->id_book , false),));
			?>
		</div>
	</div>
			
<script type="text/javascript">
	$(document).ready(function() {
		if ($(".scroll1").length > 0) {
			if (!$(".scroll1").find(".mCustomScrollBox").length > 0) {
				$(".scroll1").mCustomScrollbar();
			}
		}
		panelClip('.item_clip');
		panelClip('.term_clip');
		addPercent('.discountinput');
	});

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
			}
		});
	}
	function modeNotEditable() {
		$.fn.yiiGridView.update('terms-grid');
		$.fn.yiiGridView.update('items-grid');
		$('.tache.new_item').hide();
		$('.tache.new_term').hide();
		$('.total_amounts .apply').hide();
		$('#Eas_discount').prop('disabled',true);
		$('.note_item').find('.input').addClass('checkboxDisabled');
		$('.note_item').find('input').prop('disabled',true);
		initializeNotes();
	}
	function modeEditable() {
		$.fn.yiiGridView.update('terms-grid');
		$.fn.yiiGridView.update('items-grid');
		$('.tache.new_item').show();
		$('.tache.new_term').show();
		$('.total_amounts .apply').show();
		$('#Eas_discount').prop('disabled',false);
		$('.note_item').find('.input').removeClass('checkboxDisabled');
		$('.note_item').find('input').prop('disabled', false);
	}

	function initializeNotes()
	{
		$('.chk').each(function() {
			var checkBoxDiv = $(this).find('div.input');
			var input = checkBoxDiv.siblings('input');
			if (input.is(':checked')) {
				checkBoxDiv.addClass('checked');
			} else {
				checkBoxDiv.removeClass('checked');
			}
		});
	}
	
	function showItemForm(element, newItem) {
		var url;
		if (newItem) {
			url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageItem');?>";
		} else {
			url = $(element).attr('href');
		}
		$.ajax({
	 		type: "POST",
		  	url: url,
		  	dataType: "json",
		  	data: {'id_ea':<?php echo $model->id;?>},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) {
					  		$('.new_item').hide();
					  		$('.new_item').after(data.form);
					  	} else {
							$(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>');
					  	}
				  	}
			  	}
	  		}
		});
	}
	</script>