<?php $form=$this->beginWidget('CActiveForm', array('id'=>'supportrequest-details','enableAjaxValidation'=>false,
			'htmlOptions' => array(	'class' => 'ajax_submit','enctype' => 'multipart/form-data',
			'action' => Yii::app()->createUrl("supportRequest/postComment", array("id"=>$model->id))	),	)); ?>
		<div id="commentssection" class="files exppage <?php if ($model->status == supportRequest::STATUS_CLOSED || $model->status == supportRequest::STATUS_CANCELLED || $model->status == supportRequest::STATUS_NOTCONFIRM) {echo 'hidden'; }?>" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php	Yii::import("xupload.models.XUploadForm");
					$this->widget( 'xupload.XUpload', array('url' => Yii::app( )->createUrl("supportRequest/upload_comm"), 
						'model' => new XUploadForm(),
			            'htmlOptions' => array('id'=>'supportrequest-details'),'formView' => 'small_form_exp',
			            'attribute' => 'file',	'autoUpload' => true,  'multiple' => true,
						'options' => array(
							'maxFileSize' => 15728640, 
							'submit' => "js:function (e, data) {
								var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."supportrequest".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."comments".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
								var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."supportrequest".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."comments".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
								//var path = '".Yii::app( )->getBasePath( )."/../uploads/customers/{$model->id_customer}/supportrequest/{$model->id}/comments/"."';
								//var publicPath = '".Yii::app( )->getBaseUrl( )."/uploads/customers/{$model->id_customer}/supportrequest/{$model->id}/comments/"."';
		        				var model_id = '".$model->id."';
			                    data.formData = {path : path, publicPath: publicPath, modelId : model_id};
			                    return true;
			                }",	),       ));	?>
 			<div class="exppage"></div>
 			<div class="saveDiv1 nobackground" style="display:block;">	
 			<div class="submit nobackground margint20" ><input type="button" value="" onClick = "postComm()" class="post_button  " >	</div>
	         	   
				<div id="ajxLoader"  class="margint30"><p><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif"></p></div>
	           	<span id="rejected_message_not" class="margint30"></span>  
	           
	           	<div class="sep2"> <br/> <div class="horizontalLine margintb20"></div></div>
	           	<textarea id="rejected_message" name="rejected_message"></textarea>
	           
	        </div>	
		</div>
			<div class="horizontalLine margintb20"></div>	

	        <div class="buttons user_btn">    	

				

<?php $this->endWidget(); ?>