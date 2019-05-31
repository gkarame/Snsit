<?php $form=$this->beginWidget('CActiveForm', array('id'=>'supportdesk-form-details','enableAjaxValidation'=>false,
			'htmlOptions' => array(	'class' => 'ajax_submit','enctype' => 'multipart/form-data','action' => Yii::app()->createUrl("supportDesk/postComment", array("id"=>$model->id))	),	)); ?>
		<div class="files exppage" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php	Yii::import("xupload.models.XUploadForm");
					$this->widget( 'xupload.XUpload', array('url' => Yii::app( )->createUrl("supportDesk/upload_comm"), 'model' => new XUploadForm(),
			            'htmlOptions' => array('id'=>'supportdesk-form-details'),'formView' => 'small_form_exp',
			            'attribute' => 'file',	'autoUpload' => true,  'multiple' => true,
						'options' => array(
							'maxFileSize' => 15728640, 
							'submit' => "js:function (e, data) {
								var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."supportdesk".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."comments".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
								var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."supportdesk".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."comments".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
								//var path = '".Yii::app( )->getBasePath( )."/../uploads/customers/{$model->id_customer}/supportdesk/{$model->id}/comments/"."';
								//var publicPath = '".Yii::app( )->getBaseUrl( )."/uploads/customers/{$model->id_customer}/supportdesk/{$model->id}/comments/"."';
		        				var model_id = '".$model->id."';
			                    data.formData = {path : path, publicPath: publicPath, modelId : model_id};
			                    return true;
			                }",	),       ));	?>
 			<div class="exppage"></div>	<div class="saveDiv1 nobackground" style="display:block;">	<?php if (Yii::app()->user->isAdmin) { ?>
	           		<a href="javascript:void(0);" class="followUpButton" style="margin-top: 1px;margin-left:10px;margin-right:10px;"></a>  	<?php } ?>
	           <?php if ( (Yii::app()->user->isAdmin) || (!Yii::app()->user->isAdmin && $model->status<>'3')) { ?>	<input type="button" value="" onClick = "postComm()" id="post_button" class="post_button" >	<?php } ?>	   
	         <?php  $issue_date=strtotime($model->date); $now_date=strtotime(date("Y-m-d H:i:s"));
					$escalationtimediff=$now_date-$issue_date;
					if(!(Yii::app()->user->isAdmin) && ($model->status<>'3' && $model->status<>'5' && $escalationtimediff>604800 && $model->severity=='Low' && $model->escalate=='No')|| (!(Yii::app()->user->isAdmin) && $model->status<>'3' && $model->status<>'5' && $escalationtimediff>259200 && $model->severity=='Medium' && $model->escalate=='No') || (!(Yii::app()->user->isAdmin) && $model->status<>'3' && $model->status<>'5' && $escalationtimediff>21600 && $model->severity=='High' && $model->system_down=='No' && $model->escalate=='No') ||  (!(Yii::app()->user->isAdmin) && $model->status<>'3' && $model->status<>'5' && $escalationtimediff>5400 && $model->severity=='High' && $model->system_down=='Yes' && $model->escalate=='No') ){    $sr=$model->id; ?> 
					<div id='escalate' class="submit nobackground width144" style="display:block"><?php echo CHtml::Button(Yii::t('translations','Escalate'), array('class'=>'escalate','onClick'=>'Specifyescalate();')); ?></div>
			<?php 	} ?>   	<div id="ajxLoader"><p><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif"></p></div>
	           	<span id="rejected_message_not"></span> <div class="sep1">	<br/><br/><br/><br/><br/>  </div>
	           	 <?php if (Yii::app()->user->isAdmin) { ?> <div class="support_desk_label"> Root Cause* </div>
	           	  <input class="root_cause" id="root_cause" name="root_cause"/>
	           	  	<div class="sep2"> <br/> <div class="horizontalLine margintb20"></div></div>
	           	   		  <div class="support_desk_label"> Issue Solution* </div> 	<?php } ?>	           	
	            <textarea id="rejected_message" name="rejected_message"></textarea>
	            <div class="sep3"> 	    </div> 
	        </div>	</div>	<?php if (Yii::app()->user->isAdmin) { ?> 
			<div class="horizontalLine margintb20"></div>	<?php if ($model->status<>'3' && $model->status<>'5') { ?>
	        <div class="buttons user_btn">    	<?php if (SupportDesk::doneLicensing($model->id)){ ?>
				<div class="submit nobackground"><?php echo CHtml::Button(Yii::t('translations','Close'), array('id'=>'closeissue','class'=>'close_red','id'=>'close_red','onClick'=>'showFullResolution();')); ?></div>
				<?php }  ?>	<div class="submit nobackground"><?php echo CHtml::Button(Yii::t('translations','Pending Info'), array('id'=>'pending_info','class'=>'pending_info','onClick'=>'changeStatusIncident("pending_info");')); ?></div>
			</div>	<?php } } else { ?>	<?php if ($model->status == SupportDesk::STATUS_CLOSED) { ?>
				<div class="horizontalLine margintb20"></div>	<div class="buttons customer_btn">
					<div class="submit nobackground width144" style="display:block"><?php echo CHtml::Button(Yii::t('translations','Confirm Close'), array('class'=>'confirm_close','onClick'=>'confirmCloseTicket("confirm_close");')); ?></div>
					<div class="submit nobackground" style="display:block"><?php echo CHtml::Button(Yii::t('translations','Reopened'), array('class'=>'reopen','onClick'=>'SpecifyReason();')); ?></div>
				</div>	<?php }  } ?>		<?php $this->endWidget(); ?>