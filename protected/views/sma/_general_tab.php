<div class="mytabs ea_edit">
	<?php $form=$this->beginWidget('CActiveForm', array('id'=>'sma-form',
		'enableAjaxValidation'=>false,	'htmlOptions' => array(	'class' => 'ajax_submit',		'enctype' => 'multipart/form-data',	),	)); ?>
	<div id="sma_header" class="edit_header"><?php if(GroupPermissions::checkPermissions('general-sma','write') || ($model->status !=3 && $model->status >0 && $model->assigned_to == Yii::app()->user->id)) {?>
		<div class="header_title">	<span class="red_title"><?php echo Yii::t('translations', 'SMA Info');?></span>
			 <a class="header_button" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('sma/update', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Edit');?></a>	
		</div>	<?php }?>	<div class="header_content tache">			 
				<?php $this->renderPartial('_header_content', array('model' => $model));?>
		</div>	<div class="hidden edit_header_content tache new" <?php if(!GroupPermissions::checkPermissions('general-sma','write')) {?>style="height:150px !important;" <?php }?>></div>
		<br clear="all" /></div><div id="fileuploads" class=" <?php if ($model->status!=1 && $model->status!=3){echo "hidden";}?>">
		<?php	Yii::import("xupload.models.XUploadForm");
			$this->widget( 'xupload.XUpload', array(
	        	'url' => Yii::app( )->createUrl("sma/upload"),
	            'model' => new XUploadForm(),
	            'htmlOptions' => array('id'=>'sma-form'),
				'formView' => 'small_form',
	            'attribute' => 'file',
				'autoUpload' => true,
	            'multiple' => false,
				'options' => array(
					'maxFileSize' => 15728640, 
					'submit' => "js:function (e, data) {
						var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."smas".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
						var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."smas".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
        				//var path = '".Yii::app( )->getBasePath( )."/../uploads/customers/{$model->id_customer}/smas/{$model->id}/"."';
						//var publicPath = '".Yii::app( )->getBaseUrl( )."/uploads/customers/{$model->id_customer}/smas/{$model->id}/"."';
        				var model_id = '".$model->id."';
	                    data.formData = {path : path, publicPath: publicPath, modelId : model_id};
	                    return true;
	                }",		'completed' => 'js: function(e, data) {
						console.log(data);
						$(".files div.box:not(:last)").remove();
					}',
				),       ));	?>	<div class="attachments_pic"></div>
		<div class="files" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php 
					if (($filepath = $model->getFile(true, true)) != null) {
						$path_parts = pathinfo($filepath);

					
				?>
				<div class="box template-download fade" id="tr0">
					<div class="title">
						<a href="<?php echo $this->createUrl('site/download', array('file' => $filepath));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
					</div>				       	
			       	<div class="size">
			        	<span><?php echo Utils::getReadableFileSize(filesize($filepath));?></span>
			        </div>	
					<div class="delete">
						<button class="btn btn-danger delete"
							 data-url="<?php echo $this->createUrl( "sma/deleteUpload", array(
	                          	"id_customer" =>$model->id_customer,
								"model_id" => $model->id,					 			
	                          	"file" => $path_parts['basename'],
	                            ));?>" 
							data-type="POST">
						</button>
					</div></div>	<?php } ?>	</div>	</div>	<?php $this->endWidget(); ?></div>
<script type="text/javascript">
function modeNotEditable() {	}
	function modeEditable() {	}
function submitForm(element) {
		var data = $("#sma-form").serialize() + '&ajax=eas-form';
		$.ajax({type: "POST",data: data,	  	dataType: "json",  	url : $("#ea-form").attr("action"),
		  	success: function(data) {
		  		element.disabled = false;
			  	if (data && data.status) {
			  		$('.errorMessage').html('');
				  	console.log(data);
				  	if (data.status == "saved") {				  		
					  	if (data.error) {  		showErrors(data.error);  	} else {  	closeTab(configJs.current.url); 	}	
				  	}  	}	} });	}
function showHeader(element){
		var url = $(element).attr('href');
		$.ajax({type: "POST", 	url: url,  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {	$('.edit_header_content').html(data.html);	$('.edit_header_content').removeClass('hidden');  $('.header_content').addClass('hidden');
				  	}  	} 	}	});	}
function updateHeader(element) {
		$('#img').removeClass('hidden');
		$.ajax({type: "POST",	data: $('#header_fieldset').serialize()  + '&ajax=sma-form',	url: "<?php echo Yii::app()->createAbsoluteUrl('sma/update', array('id' => $model->id));?>", 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		element.disabled = false;
				  	if (data.status == 'saved' && data.html) {
				  		$('.header_content').html(data.html); 		$('.header_content').removeClass('hidden');
				  		$('#img').addClass('hidden'); 		$('.edit_header_content').addClass('hidden');
				  		if( data.statusNotNew){	 $("#fileuploads").removeClass("hidden"); }else{ $("#fileuploads").addClass("hidden"); }
		} else { 		if (data.status == 'success' && data.html) {	$('.edit_header_content').html(data.html);		}  	}
				  	if (data.can_modify == false) {  modeNotEditable();  	} else {	modeEditable();	  	}
				  	showErrors(data.error);  	}  		}	}); 	}
</script>