	<div class="mytabs maintenance_edit">
	<div id="recipients-list" style="display:none;top: 0%;left: 35%;    position: absolute;"></div>
		<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'internal-form',
		'enableAjaxValidation'=>false,
		'htmlOptions' => array(
			'class' => 'ajax_submit',
			'enctype' => 'multipart/form-data',
		),
		)); ?>
		<div id="internal_header" class="edit_header">
			<div class="header_title">	
				<span class="red_title"><?php echo Yii::t('translations', 'PROJECT HEADER');?></span>
				<?php if ($model->status != 1) { ?>
					<a class="header_button" id="header_button" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('internal/updateHeader', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Edit Header');?></a>
					<a class="header_button" id="header_button" onclick="getRecipients(this);return false;" href=""><?php echo Yii::t('translations', 'Add Recipients');?></a>
					
				<?php }?>
			</div>
			<div class="header_content tache">
				<?php $this->renderPartial('_header_content', array('model' => $model));?>
			</div>
			<div class="hidden edit_header_content tache new" style="width:98%;height:165px;"></div>
			<br clear="all" />
		</div>		
		<div class="horizontalLine smaller_margin"></div>
	</div>
	<?php $this->endWidget(); ?>
<br clear="all" />
<script type="text/javascript">

function getRecipients() {
	if (!$('#recipients-list').is(':visible')) {
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('internal/GetUnssignedRecipients');?>", 
		  	dataType: "json",  	data: $('.task').find('input[name!=change]').serialize()+'& id_internal= <?php echo $model->id ?>',
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
		$.ajax({type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('internal/AssignRecipients', array('id'=>$model->id));?>", 
		  	dataType: "json",data:  $('#unassigned-recipients-form').serialize()+'&'+$('.task').find('input[name!=change]').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") {		  				
		  			document.getElementById('allrecipients').innerHTML=data.names;	
		  			$('#recipients-list').fadeOut(100);	UncheckAll(); } }	});
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
			custom_alert('ERROR MESSAGE', 'You have to select at least one user in order to save!', action_buttons);
	} 
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
			  		$('.edit_header_content').html(data.html);
					$('.edit_header_content').removeClass('hidden');
					$('.header_content').addClass('hidden');
			  	}
		  	}
  		}
	});
}
function updateHeader(element) {
	$.ajax({
 		type: "POST",
 		data: $('#header_fieldset').serialize()  + '&ajax=internal-form',					
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('internal/updateHeader', array('id' => $model->id));?>", 
	  	dataType: "json",
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'saved' && data.html) {
			  		if(data.closed == 1)
			  		{
			  			document.getElementById("header_button").style.display = "none";
			  		}
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