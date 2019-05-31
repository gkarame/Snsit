<div class="tache new" style="width:99%;height:171px;">
	<div class="bg"></div>
	<fieldset class="new_connection">
		<?php $id = $model->isNewRecord ? 'new' : $model->id;?>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]name"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]name", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]name", array('id'=>"Connections_".$id."_name_em_")); ?>
		</div>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]type"); ?></div>
			<div class="input_text">
				<div class="hdselect">
					<?php echo CHtml::activeDropDownList($model, "[$id]type", Codelkups::getCodelkupsDropDown('connection_type'), array('prompt'=>Yii::t('translations', 'Select connection type'))); ?>
				</div>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]type", array('id'=>"Connections_".$id."_type_em_")); ?>
		</div>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]server_name"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]server_name", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]server_name", array('id'=>"Connections_".$id."_server_name_em_")); ?>
		</div>
		<div class="textBox one inline-block first">
			<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"[$id]password"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]password", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]password", array('id'=>"Connections_".$id."_password_em_")); ?>
		</div>
		<div class="textBox one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"Notes"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]notes", array('class'=>'input_text_value')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]notes", array('id'=>"Connections_".$id."_notes_em_")); ?>
		</div>
		<div class="textBox one inline-block" style="margin-top:28px;width:100px;">
			<?php if(!$model->isNewRecord ){
				Yii::import("xupload.models.XUploadForm");
				$this->widget( 'xupload.XUpload', array(
		        	'url' => Yii::app( )->createUrl("customers/upload"),
		            'model' => new XUploadForm(),
					'htmlOptions' => array('id'=>'customers-form'),
					'formView' => 'small_form',
		            'attribute' => 'file',
					'autoUpload' => true,
		            'multiple' => false,
					'options' => array(
						'maxFileSize' => 15728640,	
						'submit' => "js:function (e, data) {						
						var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."connections".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
						var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."connections".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."'; 
						//var path = '".Yii::app( )->getBasePath( )."/../uploads/customers/$id_customer/connections/"."';
						//var publicPath = '".Yii::app( )->getBaseUrl( )."/uploads/customers/$id_customer/connections/"."';
	        				var model_id = '".$model->id."';
		                    data.formData = {path : path, publicPath: publicPath, modelId : model_id};
		                    return true;
		                }",			
						'completed' => 'js: function(e, data) {
							$("#specialid").remove();
						}',
					),
		        )); } else { ?> <div class="textBox" style="width:300px; font-size:14px; color:#8B0000; "> After saving edit the connection to attach file. </div> <?php }
			?>	
		</div>
		<div class="files inline-block margint15" data-toggle="modal-gallery" data-target="#modal-gallery">
			<div class="files" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php if (($filepath = $model->getFile(true, true)) != null) {
						$path_parts = pathinfo($filepath);
				?>
				<div class="box template-download fade" id="tr0">
					<div class="title">
						<a class="capitalize" href="<?php echo $this->createUrl('site/download', array('file' => $filepath));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
					</div>				       	
			       	<div class="size">
			        	<span><?php echo Utils::getReadableFileSize(filesize($filepath));?></span>
			        </div>	
					<div class="delete">
						<button class="btn btn-danger delete"
							 data-url="<?php echo $this->createUrl( "customers/deleteUploadConnFile", array(
	                          	"id_customer" =>$model->id_customer,
								"model_id" => $model->id,					 			
	                          	"file" => $path_parts['basename'],
	                            ));?>" 
							data-type="POST">
						</button>
					</div>
				</div>	<?php } ?>
			</div>
		</div>
		<?php if ($model->id) { ?>
			<input type="hidden" name="Connections[<?php echo $id;?>][id]" value="<?php echo $model->id;?>" />
		<?php } ?>
		<div style="right:65px;" class="save" onclick="<?php echo ($update) ? 'saveConnection(this, \''.$id.'\');' : 'js:submitForm();return false;'?>"><u><b>SAVE</b></u></div>
		<div style="color:#333;right:8px;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('connections-grid');<?php }; unset(Yii::app()->session['customers_conn']);?>"><u><b>CANCEL</b></u></div>
	</fieldset>
</div>