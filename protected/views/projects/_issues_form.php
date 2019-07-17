<div  id="support" class="tache-issue new" style="width:900px !important;" >
	<div id="support" class="bg_issue" ></div>
	<?php $form=$this->beginWidget('CActiveForm', array(	'id'=>'issue-form',		'enableAjaxValidation'=>false,	)); ?>		
		<div class="item inline-block notes normal left11"   >
			<?php echo $form->labelEx($model,'description'); ?>
			<div class="dataRow"  style="width:250px;"><?php echo $form->textField($model,'description'); ?></div>
			<?php echo $form->error($model,'description'); ?>	</div>
		<div class="item inline-block one normal " style="margin-left: -170px;width: 100px !important;">
			<?php echo $form->labelEx($model,'priority'); ?>
			<div class="selectRow" style="width:100px;">
			<?php echo $form->dropDownList($model, 'priority', ProjectsIssues::getPriorities()); ?> 
			</div>	<?php echo $form->error($model,'priority'); ?>	</div>
<div class="item inline-block one normal " style="margin-left:20px;">
			<?php echo $form->labelEx($model,'status'); ?>
			<div class="selectRow" style="width:100px;">
			<?php echo $form->dropDownList($model, 'status', ProjectsIssues::getStatusCustomList($model->status), array('class'=>'input_text_value', 'onchange' => 'changeStatus(this);')); ?> 
			</div>	<?php echo $form->error($model,'status'); ?></div>

<div class="item inline-block one normal " style="    margin-left: -165px;">
			<?php echo $form->labelEx($model,'module'); ?>
			<div class="selectRow" style="width:250px;">
			<?php echo $form->dropDownList($model, 'module', Codelkups::getCodelkupsDropDownOriginals('modules'), array('class'=>'input_text_value','prompt'=>'Choose module', )); ?> 
			</div>	<?php echo $form->error($model,'module'); ?>	</div>

<div class="item inline-block one normal left11" >
			<?php echo $form->labelEx($model,'type'); ?>
			<div class="selectRow" style="width:250px;">
			<?php echo $form->dropDownList($model, 'type', ProjectsIssues::getTypeList(), array('class'=>'input_text_value','prompt'=>'', 'onchange' => 'changeType(this);')); ?> 
			</div>	<?php echo $form->error($model,'type'); ?>	</div>
		<div class="item inline-block one normal hidden" id="fbr" style="margin-left:-17px;" >
			<?php echo $form->labelEx($model,'fbr'); ?>
			<div class="selectRow" style="width:250px;">
			<?php echo $form->dropDownList($model, 'fbr', ProjectsIssues::getFbrsList(($model->id_project )? $model->id_project : $id_project   ), array('class'=>'input_text_value','prompt'=>'')); ?> 
			</div>	<?php echo $form->error($model,'fbr'); ?>	</div>
		<div class="item inline-block one normal " id="fix" style="margin-left:-17px;" >
			<?php echo $form->labelEx($model,'fix'); ?>
			<div class="dataRow"  style="width:250px;"><?php echo $form->textField($model,'fix'); ?></div>
			<?php echo $form->error($model,'fix'); ?></div>
<!--
    /*
    * Author: Mike
    * Date: 15.07.19
    * Add ETD (mandatory) on issue level
    */
-->
        <div class="item inline-block one normal " id="fix" style="margin-left:-17px;margin-right: 0;" >
            <?php echo $form->labelEx($model,'etd_date'); ?>
            <div class="dataRow"  style="width:250px;height: 25px;"><?php echo $form->dateField($model,'etd_date'); ?></div>
            <?php echo $form->error($model,'etd_date'); ?></div>

		<div class="item inline-block one normal " id="notes" style="margin-left:-17px;" >
			<?php echo $form->labelEx($model,'notes'); ?>
			<div class="dataRow"  style="width:250px;"><?php echo $form->textField($model,'notes'); ?></div>
			<?php echo $form->error($model,'notes'); ?>	</div>	<br />
		<div class="textBox one inline-block left11" style="margin-top:28px;width:100px;">
			<?php if(!$model->isNewRecord ){
				Yii::import("xupload.models.XUploadForm");
				$this->widget( 'xupload.XUpload', array('url' => Yii::app( )->createUrl("projects/upload"),
		            'model' => new XUploadForm(),'htmlOptions' => array('id'=>'issue-form'),
					'formView' => 'small_form','attribute' => 'file','autoUpload' => true,'multiple' => false,
					'options' => array('maxFileSize' => 15728640,'submit' => "js:function (e, data) {						
						var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."projects".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_project}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."issues".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_issue}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
						var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."projects".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_project}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."issues".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_issue}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."'; 
						var model_id = '".$model->id."'; data.formData = {path : path, publicPath: publicPath, modelId : model_id}; return true;  }",			
						'completed' => 'js: function(e, data) {	$("#specialid").remove();	}',	),
		        ));?><div class="textBox" style="width:300px; font-size:12px; color:#8B0000; "> Please combine files in one attachment (.zip) </div> <?php } else { ?> <div class="textBox" style="width:300px; font-size:12px; color:#8B0000; "> After saving edit the issue to attach file. </div> <?php } ?>	</div>
		<div class="files inline-block margint15 marginl18" data-toggle="modal-gallery" data-target="#modal-gallery">
			<div class="files" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php if (($filepath = $model->getFile(true, true)) != null) {	$path_parts = pathinfo($filepath);	?>
				<div class="box template-download fade" id="tr0">
					<div class="title">	<a class="capitalize" href="<?php echo $this->createUrl('site/download', array('file' => $filepath));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>	</div>				       	
			       	<div class="size">    	<span><?php echo Utils::getReadableFileSize(filesize($filepath));?></span>      </div>	
					<div class="delete">
						<button class="btn btn-danger delete"
							 data-url="<?php echo $this->createUrl( "projects/DeleteUploadIssueFile", array(
	                          	"id_project" =>$model->id_project, "model_id" => $model->id_issue, "file" => $path_parts['basename'],));?>" 
							data-type="POST">
						</button>
					</div>
				</div>	<?php } ?>	</div>	</div> 
		<?php if ($model->isNewRecord) { ?>
			<div style="right:125px;top:180px;" class="save" onclick="createIssueItemwithNew(this, <?php echo "1"; ?>, '<?php echo Yii::app()->createAbsoluteUrl('projects/createIssue/?id_project='.$id_project.'');?>'); return false;"><u><b>SAVE & NEW</b></u></div>
			<div style="right:78px;top:180px;" class="save" onclick="createIssueItem(this, <?php echo "1"; ?>, '<?php echo Yii::app()->createAbsoluteUrl('projects/createIssue/?id_project='.$id_project.'');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } else { ?>
			<div style="right:125px;top:180px;" class="save" onclick="updateIssuewithNew(this, <?php echo $model->id; ?>, '<?php echo Yii::app()->createAbsoluteUrl('projects/manageIssueItem');?>'); return false;"><u><b>SAVE & NEW</b></u></div>
			<div style="right:78px;top:180px;" class="save" onclick="updateIssue(this, <?php echo $model->id; ?>, '<?php echo Yii::app()->createAbsoluteUrl('projects/manageIssueItem');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } ?>

		<div style="color:#333;top:180px;" class="save" onclick="$(this).parents('.tache-issue.new').siblings('.tache-issue').show();$(this).parents('.tache-issue.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('issues-grid');<?php }?>"><u><b>CANCEL</b></u></div>
	<?php $this->endWidget(); ?> </div>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var rateUrl = '<?php echo Yii::app()->createAbsoluteUrl('expenses/GetUSDRate');?>';
$(function() {	changeType('#ProjectsIssues_type');	changeStatus('#ProjectsIssues_status');	validatenotes('#ProjectsIssues_type', '#ProjectsIssues_status');	}); 
function validatenotes(type, status) {
	$typee =  $(type);	$statuss =  $(status);
	if ((($statuss.val() == '1' || $statuss.val() == '2' ) && $typee.val() == 'Base Bug') || $typee.val() == 'FBR'){	$('#notes').addClass('fright');	}		}
	function changeType(element) {
		$this =  $(element);	$status =  $('#ProjectsIssues_status'); 
		switch ($this.val()) {
			case 'FBR':				
				$('#fbr').removeClass('hidden'); $('#fix').addClass('hidden');	$('#notes').addClass('fright');							
				break;	
			case 'Base Bug':	
				$('#fbr').addClass('hidden');
				if($status.val() == '1' || $status.val() == '2'){ $('#fix').removeClass('hidden'); $('#notes').addClass('fright');	 }
				break;			
			default:
				$('#fbr').addClass('hidden'); $('#fix').addClass('hidden');	$('#notes').removeClass('fright');	
				break;				
	}	}
	function changeStatus(element) {
		$this =  $(element);	$type =  $('#ProjectsIssues_type'); 
		if (($this.val() == '1' || $this.val() == '2' ) && $type.val() == 'Base Bug') {
			$('#fix').removeClass('hidden'); $('#notes').addClass('fright');		}else{ $('#fix').addClass('hidden'); 	 }	}
</script>
