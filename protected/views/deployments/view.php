	<div class="mytabs maintenance_edit">
		<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'deployments-form',
		'enableAjaxValidation'=>false,
		'htmlOptions' => array(
			'class' => 'ajax_submit',
			'enctype' => 'multipart/form-data',
		),
		)); ?>
		<div id="deployments_header" class="edit_header">
			<div class="header_title">	
				<span class="red_title"><?php echo Yii::t('translations', 'DEPLOYMENT HEADER');?></span>
				<a class="header_button" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('deployments/updateHeader', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Edit Header');?></a>
			</div>
			<div class="header_content tache">
				<?php $this->renderPartial('_header_content', array('model' => $model));?>
			</div>
			<div class="hidden edit_header_content tache new" style="width:98%;height:230px;"></div>
			<br clear="all" />
		</div>		
		<div class="horizontalLine smaller_margin"></div>
	</div>
	<?php $this->endWidget(); ?>
<br clear="all" />
<script type="text/javascript">
$(document).ready(function() {
	if ($(".scroll1").length > 0) {
		if (!$(".scroll1").find(".mCustomScrollBox").length > 0) {
			$(".scroll1").mCustomScrollbar();
		}
	}
	panelClip('.item_clip');
	panelClip('.term_clip');
	
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
function showHeader(element)
{
	var url = $(element).attr('href');
	$.ajax({
 		type: "POST",
	  	url: url,
	  	dataType: "json",
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
			  		//$("input#Maintenance_starting_date").datepicker({ dateFormat: 'dd/mm/yy' });
			  	
					$('.edit_header_content').html(data.html);
					$('.edit_header_content').removeClass('hidden');
					$('.header_content').addClass('hidden');
					//$("input#Maintenance_starting_date").val(data.starting_date);
				
					
					
			  	}
		  	}
  		}
	});
}
function updateHeader(element) {
	$.ajax({
 		type: "POST",
 		data: $('#header_fieldset').serialize()  + '&ajax=deployments-form',					
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('deployments/updateHeader', array('id' => $model->id));?>", 
	  	dataType: "json",
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'saved' && data.html) {
			  		$('.header_content').html(data.html);
			  		$('.header_content').removeClass('hidden');
			  		$('.edit_header_content').addClass('hidden');
			  		$.each( data.total, function( key, value ) {
	  					$('#'+key).html(value);	
			  		});
			  	} else {
			  		if (data.status == 'success' && data.html) {
			  			$('.edit_header_content').html(data.html);
			  			showErrors(data.error);
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
		  	}
  		}
	});
}
</script>