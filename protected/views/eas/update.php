<div class="mytabs ea_edit">
	<?php $form=$this->beginWidget('CActiveForm', array('id'=>'ea-form', 'enableAjaxValidation'=>false, 'htmlOptions' => array( 	'class' => 'ajax_submit', 	'enctype' => 'multipart/form-data', ),
	)); ?>
	<?php 	//$cur=$model->eCurrency->id;		
	 		//$rez = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$cur' order by date desc limit 1 ")->queryScalar(); 			
			$rate=$model->rate;//$rez; 
	if($model->category=='25'){ $sandu_flag = Yii::app()->db->createCommand("SELECT count(1) FROM ea_payment_terms WHERE id_ea=".$model->id." and term_type='sandu'")->queryScalar(); 	 	
	if ($sandu_flag ==0) { 	Yii::app()->db->createCommand("INSERT INTO ea_payment_terms (id_ea,payment_term,amount,milestone,term_type) VALUES (".$model->id.", '100', '0' , '72', 'sandu')")->execute();  	}
	} 
	if($model->customization ==1 ) { 
		$yearly_support_flag = Yii::app()->db->createCommand("SELECT count(1) FROM ea_payment_terms WHERE id_ea=".$model->id." and term_type='sandu'")->queryScalar(); 	 	
		if ($yearly_support_flag ==0) { 	

			if($model->customization ==1)
			{
				if($model->support_amt>0 && $model->support_percent>0)
				{
					$amt=$model->getTotalSandU();
					Yii::app()->db->createCommand("INSERT INTO ea_payment_terms (id_ea,payment_term,amount,milestone,term_type) VALUES (".$model->id.", '100', ".$amt." , '1450', 'sandu')")->execute(); 
				}else{
					Yii::app()->db->createCommand("INSERT INTO ea_payment_terms (id_ea,payment_term,amount,milestone,term_type) VALUES (".$model->id.", '100', '0' , '1450', 'sandu')")->execute(); 
				} 
			}else{
				Yii::app()->db->createCommand("INSERT INTO ea_payment_terms (id_ea,payment_term,amount,milestone,term_type) VALUES (".$model->id.", '100', '0' , '515', 'sandu')")->execute(); 

			}	}
	} 
	?>
	<div id="ea_header" class="edit_header">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'EA HEADER');?></span>
			<a class="shareby_button header_button" href="<?php echo $this->createUrl("site/shareby", array('id' => $model->id));?>" title="Share" onclick="shareBySubmit(this, 'eas');return false;">Share</a>
			<a class="header_button" href="<?php echo $this->createUrl('eas/print', array('id' => $model->id));?>">Print</a>
			<a class="header_button" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('eas/updateHeader', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Edit Header');?></a>	
		</div>
		<div class="header_content tache">
			<?php $this->renderPartial('_header_content', array('model' => $model));?>
		</div>
		<div class="hidden edit_header_content tache new"></div>
		<br clear="all" />
	</div>
	<div id="ea_items">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'EA ITEMS');?></span>
		</div>
		<div id="ea_items_content" class="border-grid grid">
			 <?php if ($model->TM !='1') { 
				$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'items-grid', 'dataProvider'=>$model->items,	'summaryText' => '',	'pager'=> Utils::getPagerArray(),
			    'template'=>'{items}{pager}',	'afterAjaxUpdate' => 'js:function() {panelClip(".item_clip");panelClip(".term_clip");}',
				'columns' => EasItems::getColumnsForGrid($model->category, $can_modify,$model->id_customer, $model->template, $model->customization),
			)); } else  {$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'items-grid',	'dataProvider'=>$model->items,	'summaryText' => '',
				'pager'=> Utils::getPagerArray(),    'template'=>'{items}{pager}',
				'afterAjaxUpdate' => 'js:function() {panelClip(".item_clip");panelClip(".term_clip");}',
				'columns' => EasItems::getColumnsForTMGrid($model->category, $can_modify,$model->id_customer, $model->template, $model->customization),
			)); }?>
			<div class="tache new_item" <?php echo (!$can_modify) ? 'style="display:none;"' : '';?>>
				<div onclick="showItemForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u></div>
			</div>
			<div class="total_amounts totalrow">
				<?php if ($model->TM !='1') { ?>
				<div class="column inline-block">
					<span class="title"><?php  echo Yii::t('translations', 'GROSS AMOUNT').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="total_amount" class="value"><?php echo Utils::formatNumber($model->getTotalAmount());?></span>
				</div>
				<?php if ($model->category != 24 && $model->category != 25 && $model->category != 454 && $model->category != 496 && $model->category != 623 ) { ?>
				<div class="column inline-block middleitem">
					<span class="title"><?php echo Yii::t('translations', 'TOTAL MD');?></span>
					<br /><br />			
					<span id="total_man_days" class="value"><?php echo Utils::formatNumber($model->getTotalManDays());?></span>
				</div>
				<div class="column inline-block middleitem">
					<span class="title"><?php echo Yii::t('translations', 'AVERAGE MD RATE');?></span>
					<br /><br />
					<span id="man_day_rate" class="value"><?php echo Utils::formatNumber($model->getManDayRate()); ?></span>
				</div>  <?php } } else {  if ($model->category != 24 && $model->category != 25 && $model->category != 454 && $model->category != 496 && $model->category != 623) { ?>
				<div class="column inline-block middleitem">
					<span class="title"><?php echo Yii::t('translations', 'AVERAGE MD RATE');?></span>
					<br /><br />
					<span id="man_day_rate" class="value"><?php echo Utils::formatNumber($model->getTMManDayRate()); ?></span>
				</div><?php }  }  ?>
				<div class="column inline-block last">
					<div class="label">DISCOUNT</div>
						<?php echo $form->textField($model,'discount', array('onblur' => 'addPercent(this);unLockupdate=true;submitDiscount(this);',
							'onclick' => "removePercent(this);return;",'class' => 'discountinput','disabled' => $can_modify ? '' : 'disabled', ));?> 
					</div>
			</div>
			<div class="net_amounts totalrow">
				<?php if ($model->TM !='1') { ?>
				<div class="column inline-block">
					<span class="red title"><?php echo "NET ";  echo Yii::t('translations', 'AMOUNT').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span class="">
						<?php echo $form->textField($model,'net_amount', array('value'=>$model->getNetAmount(),	'onchange' => 'unLockupdate=true; changeDiscount(this);addComma(this);',
							'onclick' => "removeComma(this);return;",'class' => 'netamount-input', 'style' => 'width:90%', 'id'=>'net_amount', 'disabled' => $can_modify ? '' : 'disabled',)); ?>
					</span>
				</div>
				<?php if ($model->category != 25 && $model->category != 454 && $model->category != 496 && $model->category != 24 && $model->category != 623) { ?>
				<div class="column  inline-block middleitem">
					<span class="red title"><?php if ($model->category != 25){echo Yii::t('translations', 'NET MD RATE').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="net_man_day_rate" class="value" ><?php echo Utils::formatNumber($model->getNetManDayRate());}?></span>
				</div>
				 <?php } if ($model->category == 25) {?><div class="column inline-block">
					<span class="red title"><?php echo Yii::t('translations', 'S&U AMOUNT').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="sandu_amount" class="value"><?php echo Utils::formatNumber($model->getTotalSandU());?></span>
				</div>
				<div class="column inline-block">
					<span class="red title"><?php echo Yii::t('translations', 'NET AMOUNT').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="net_sandu_amount" class="value"><?php echo Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU()); ?></span>
				</div>
				 <?php } if ( $model->customization ==1) {?><div class="column inline-block">
					<span class="red title"><?php echo Yii::t('translations', 'SUPPORT AMOUNT').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="sandu_amount" class="value"><?php echo Utils::formatNumber($model->getTotalSandU());?></span>
				</div>
				<div class="column inline-block">
					<span class="red title"><?php echo Yii::t('translations', 'NET AMOUNT').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="net_sandu_amount" class="value"><?php echo Utils::formatNumber($model->getNetAmount()+$model->getTotalSandU()); ?></span>
				</div>
				 <?php } if (!empty($model->expense) && $model->expense != 'N/A' && $model->expense != 'Actuals') {?>
					<div class="column  inline-block middleitem">
						<span class="red title"><?php  echo Yii::t('translations', 'GROSS AMOUNT (incl. expenses) ('.$model->eCurrency->codelkup.')');?></span>
						<br /><br />
						<span id="total_net_amount" class="value"><?php echo Utils::formatNumber($model->getNetAmountWithExp());?></span>
					</div>
					<div class="">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br/><br/><br/><br/>
						</div>
				<?php } }  if ($model->category != 25 && $model->category != 454 && $model->category != 496 && $model->category != 623) {				
				if  ($model->TM =='1') { ?>  
				<div class="column  inline-block middleitem">
					<span class="red title"><?php if ($model->category != 25){echo Yii::t('translations', 'NET MD RATE').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="net_man_day_rate" class="value value2"><?php echo Utils::formatNumber($model->getNetTMManDayRate());}?></span>
				</div> 

				<?php if ( $model->customization ==1) {?><div class="column inline-block">
					<span class="red title"><?php echo Yii::t('translations', 'SUPPORT AMOUNT').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="sandu_amount" class="value"><?php echo Utils::formatNumber($model->getTotalSandU());?></span>
				</div>


				<?php } }  
				} if($model->eCurrency->codelkup !='USD') { if ($model->TM !='1') { ?>
				<div class="column inline-block">
					<span class="red title"><?php echo Yii::t('translations', 'NET AMOUNT (USD)');?></span>
					<br /><br />
					<span id="net_amount" class="value" name="net_amount_usd"><?php echo Utils::formatNumber(($model->getNetAmount()+$model->getTotalSandU())*$rate);?></span>
				</div> 
				<div class="column  inline-block middleitem">
					<span class="red title"><?php if ($model->category != 25 && $model->category != 454 && $model->category != 496 && $model->category != 623){echo Yii::t('translations', 'NET MD RATE (USD)');?></span>
					<br /><br />
					<span id="net_man_day_rate" class="value" name="net_man_day_rate_usd"> <?php echo Utils::formatNumber($model->getNetManDayRate()*$rate);}?></span>
				</div>
					<?php if (!empty($model->expense) && $model->expense != 'N/A' && $model->expense != 'Actuals') {?>
					<div class="column  inline-block middleitem">
						<span class="red title"><?php  echo Yii::t('translations', 'GROSS AMOUNT (incl. expenses) (USD)');?></span>
						<br /><br />
						<span id="total_net_amount" class="value" name="total_net_amount_usd"><?php echo Utils::formatNumber($model->getNetAmountWithExp()*$rate);?></span>
					</div>	<?php } } } ?>			 	
			</div>
		</div>
	</div>
	<div id="terms_notes">
		<div id="ea_terms">
			<div class="header_title">
			<?php if($model->category=='25'){ ?>	
				<span class="red_title"><?php echo Yii::t('translations', 'LICENSE PAYMENT TERMS');?></span>
				<?php }else { ?>
				<span class="red_title"><?php echo Yii::t('translations', 'PAYMENT TERMS');?></span>
				<?php }?>
			</div>
			<div id="ea_terms_content" class="border-grid grid">
				<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'terms-grid','dataProvider'=>$model->getTerms(),
					'summaryText' => '','pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}',
					'afterAjaxUpdate' => 'js:function() {panelClip(".term_clip")}',
					'columns'=>array(
						array( 'name' => 'payment_term', 'header' => 'Payment %',
						),
						array( 'name' => 'amount', 'value' => 'Utils::formatNumber($data->amount)',
						),
						array( 'name' => 'eMilestone.codelkup', 'header' => 'Milestone', 'type' => 'raw', 'htmlOptions' => array('class' => 'column100', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"),  'headerHtmlOptions' => array('class' => 'column100'), 'value' => 'isset($data->eMilestone->codelkup) ? $data->getMilestoneGrid() : ""'
						),
						array( 'class'=>'CCustomButtonColumn', 'visible' => $can_modify, 'template'=>'{update} {delete}', 'htmlOptions'=>array('class' => 'button-column'), 'buttons'=>array
				            (
				            	'update' => array( 		'label' => Yii::t('translations', 'Edit'),  		'imageUrl' => null, 		'url' => 'Yii::app()->createUrl("eas/manageTerm", array("id"=>$data->id))',
				            		'options' => array(
				            			'onclick' => 'showTermForm(this);return false;'
				            		), 	), 	'delete' => array( 		'label' => Yii::t('translations', 'Delete'), 		'imageUrl' => null, 		'url' => 'Yii::app()->createUrl("eas/deleteTerm", array("id"=>$data->id))',  
				                	'options' => array(
				                		'class' => 'delete', 		), 	),
				            ),
						),
					),
				)); ?>
				<div class="tache new_term" <?php echo (!$can_modify) ? 'style="display:none;"' : '';?>>
					<div onclick="showTermForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW TERM');?></b></u></div>
				</div>

				<div id="additionaltagContainer" class="tache new_termappend hidden">
					<div id="additionaltag" class="newtask"><u><b></b></u></div>
				</div> 
			</div>

			<?php if($model->category=='25' ||  $model->customization ==1  ){ ?>
			<div   >
			&nbsp;
			</div>
			<div class="header_title" style="margin-top:20px !important;">	
				<span class="red_title"><?php if($model->category=='25'){ echo Yii::t('translations', 'S&U PAYMENT TERMS'); }else{ echo Yii::t('translations', 'SUPPORT PAYMENT TERMS'); } ?></span>
			</div>
			<div id="ea_terms_content" class="border-grid grid">
				<?php

				 $this->widget('zii.widgets.grid.CGridView', array(
					'id'=>'sanduterms-grid',
					'dataProvider'=>$model->getTermsSANDUProvider(),
					'summaryText' => '',
					'pager'=> Utils::getPagerArray(),
				    'template'=>'{items}{pager}',
					'afterAjaxUpdate' => 'js:function() {panelClip(".term_clip")}',
					'columns'=>array(
						array( 'name' => 'payment_term', 'header' => 'Payment %',
						),
						array( 'name' => 'amount', 'value' => 'Utils::formatNumber($data->amount)',
						),
						array( 'name' => 'eMilestone.codelkup', 'header' => 'Milestone', 'type' => 'raw', 'htmlOptions' => array('class' => 'column100', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"),  'headerHtmlOptions' => array('class' => 'column100'), 'value' => 'isset($data->eMilestone->codelkup) ? $data->getMilestoneGrid() : ""'
						),
						array( 'class'=>'CCustomButtonColumn', 'visible' => $can_modify, 'template'=>'{update} {delete}', 'htmlOptions'=>array('class' => 'button-column'), 'buttons'=>array
				            (
				            	'update' => array( 		'label' => Yii::t('translations', 'Edit'),  		'imageUrl' => null, 		'url' => 'Yii::app()->createUrl("eas/manageTermSandU", array("id"=>$data->id))',
				            		'options' => array(
				            			'onclick' => 'showTermForm2(this);return false;'
				            		), 	), 	'delete' => array( 		'label' => Yii::t('translations', 'Delete'), 		'imageUrl' => null, 		'url' => 'Yii::app()->createUrl("eas/deleteTerm", array("id"=>$data->id))',  
				                	'options' => array(
				                		'class' => 'delete', 		), 	),
				            ),
						),
					),
				)); ?>
				<div class="tache new_termSU" <?php echo (!$can_modify) ? 'style="display:none;"' : '';?>>
					<div onclick="showTermForm2(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW TERM');?></b></u></div>
				</div>

				<div id="additionaltagContainerSU" class="tache new_termappendSU hidden">
					<div id="additionaltagSU" class="newtask"><u><b></b></u></div>
				</div> 

			</div>
		<?php } ?>
			<br clear="all" />
		</div>
		<div id="ea_notes">
			<div class="header_title">	
				<span class="red_title"><?php echo Yii::t('translations', 'NOTES');?></span>
			</div>
			<fieldset id="notes" class="scroll1">
				<input type="hidden" name="submitted" value="1" />
				<?php 
					$notes = Codelkups::getCodelkupsDropDownUniqueEas($model->id);
					//print_r($notes);exit;
					if ($model->category!=24)
					{
						 unset($notes['678']);unset($notes[677]);unset($notes[676]);
					}
					//print_r($notes);exit;
					$ea_notes = $model->getNotes();
					foreach ($notes as $key => $note) {
				?>
                    <!--
                         /*
                         * Author: Mike
                         * Date: 18.06.19
                         * Add a sorting input
                         */
                    -->
					<div class="row note_item chk" onclick="CheckOrUncheckInput(this)" onmouseover="$(this).find('label').css('color','#990000');" onmouseout="$(this).find('label').css('color','#666');">
                        <input onblur="updateSortRangeNotes('<?php echo $key?>')" style="width: 13px;float: left;border: 1px solid #ccc;margin-right: 2px;border-radius: 4px;" type="text" id="note_<?php echo $key?>" value="<?php echo $note['sort_rang'];?>">
                        <div class="input <?php echo !$can_modify ? 'checkboxDisabled' : '';echo in_array($key, $ea_notes) ? ' checked' : '';?>"></div>
						<input type="checkbox" name="EasNotes[]" value="<?php echo $key;?>" 
						<?php echo in_array($key, $ea_notes) ? 'checked' : '';?> <?php  echo $key==228 ? 'disabled="disabled"' : '';?> <?php echo !$can_modify ? 'disabled="disabled"' : '';?>  />
						<label style="width: 88%;"><?php echo $note['note'];?></label>
					</div>
				<?php 					
					}
				?>
			</fieldset>
				<div class="tache new_note" <?php echo (!$can_modify) ? 'style="display:none;"' : '';?>>
					<div onclick="showNoteForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW NOTE');?></b></u></div>

				</div>
		</div>
	</div>
	<div class="horizontalLine smaller_margin"></div>
	<div id="fileuploads"><span style="float:left;"><b>Note:</b> Images only</span> 
		<?php
			Yii::import("xupload.models.XUploadForm");
			$this->widget( 'xupload.XUpload', array(
	        	'url' => Yii::app( )->createUrl("eas/upload"),
	            'model' => new XUploadForm(),
	            'htmlOptions' => array('id'=>'ea-form'),
				'formView' => 'small_form',
	            'attribute' => 'file',
				'autoUpload' => true,
	            'multiple' => false,
				'options' => array(
					'maxFileSize' => 15728640, 
					'submit' => "js:function (e, data) {
						var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."eas".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
						var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."eas".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
        				//var path = '".Yii::app( )->getBasePath( )."/../uploads/customers/{$model->id_customer}/eas/{$model->id}/"."';
						//var publicPath = '".Yii::app( )->getBaseUrl( )."/uploads/customers/{$model->id_customer}/eas/{$model->id}/"."';
        				var model_id = '".$model->id."';
	                    data.formData = {path : path, publicPath: publicPath, modelId : model_id};
	                    return true;
	                }",
					'completed' => 'js: function(e, data) {
						console.log(data);
						$(".files div.box:not(:last)").remove();
					}',
				),
	        ));
		?>
		<div class="attachments_pic"></div>
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
							 data-url="<?php echo $this->createUrl( "eas/deleteUpload", array( "id_customer" =>$model->id_customer,
								"model_id" => $model->id,	"file" => $path_parts['basename'], ));?>" 
							data-type="POST">
						</button>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>	
	</div>
	<div class="row buttons">
		<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save'), array('id'=>'btn_save','onclick' => 'js:submitForm(this);this.disabled = true;return false;','class'=>'marginl9')); ?></div>
	</div> <?php $this->endWidget(); ?>
</div>
<br clear="all" />
<script type="text/javascript">
	$(document).ready(function() {	
	if ($(".scroll1").length > 0) {	if (!$(".scroll1").find(".mCustomScrollBox").length > 0) {	$(".scroll1").mCustomScrollbar(); }		}
		panelClip('.item_clip'); panelClip('.term_clip'); addPercent('.discountinput');	addComma('.netamount-input'); });
	function panelClip(element) {
		var width = 0;
		if (element == '.item_clip'){ width = 300; }else{ width = 90; }
		$(element).each(function() {
			if ($(this).width() < width) { $(this).parent().find('u').hide(); console.log($(this).parent().find('u').attr('class')); }	});
	}
    /*
    * Author: Mike
    * Date: 18.06.19
    * Add a sorting input
    */
	function updateSortRangeNotes(id) {
        if ($(`#note_${id}`).val().trim().length > 0){
            $.get(`http://snsit.loc/eas/ChangeSortRangeNote/${id}?range=${$(`#note_${id}`).val()}`,function (data) {
                alert(data)
            });
        }
    }
	function modeNotEditable() {
		$.fn.yiiGridView.update('terms-grid');  $.fn.yiiGridView.update("sanduterms-grid"); 	$.fn.yiiGridView.update('items-grid');	$('.tache.new_item').hide();$('.tache.new_termSU').hide();
		$('.tache.new_term').hide(); $('.total_amounts .apply').hide();	$('#Eas_discount').prop('disabled',true); 	$('#net_amount').prop('disabled',true);
		$('.note_item').find('.input').addClass('checkboxDisabled'); $('.note_item').find('input').prop('disabled',true); initializeNotes();
	}
	function modeEditable() {
		$.fn.yiiGridView.update('terms-grid'); $.fn.yiiGridView.update("sanduterms-grid"); $.fn.yiiGridView.update('items-grid');	$('.tache.new_item').show();
		$('.tache.new_term').show(); $('.total_amounts .apply').show();	$('#Eas_discount').prop('disabled',false);
		$('.note_item').find('.input').removeClass('checkboxDisabled');	$('.note_item').find('input').prop('disabled', false);
	}
	function initializeNotes(){
		$('.chk').each(function() {
			var checkBoxDiv = $(this).find('div.input'); var input = checkBoxDiv.siblings('input');
			if (input.is(':checked')) {		checkBoxDiv.addClass('checked');
			} else { checkBoxDiv.removeClass('checked');	}
		});
	}	
	function changeManDayRate(element){
		var fieldset = $(element).parents('.items_fieldset'); var amount = parseFloat((fieldset.find('.amount').val()).replace(/[^0-9^.]/g, ''));
		var man_days = parseFloat(fieldset.find('.man_days').val());	var sandu= parseFloat(fieldset.find('.sandu').val());		
		var region = parseFloat(fieldset.find('#ea_region').val());
		if (man_days != NaN && man_days > 0 && amount != NaN){
			if (parseFloat(fieldset.find('#ea_category').val()) == 25 ) {
				fieldset.find('.sandu_total').text(formatNumber(parseFloat(amount * man_days*(sandu/100))));
				fieldset.find('.man_day_rate').text(formatNumber(parseFloat(amount * man_days)));
			}else if ((parseFloat(fieldset.find('#ea_category').val()) == 28 && parseFloat(fieldset.find('#ea_customization').val()) == 1)  || (parseFloat(fieldset.find('#ea_category').val()) == 27 && (parseFloat(fieldset.find('#ea_template').val()) == 2 || (parseFloat(fieldset.find('#ea_template').val()) == 6 && region !=59))) ){
				fieldset.find('.man_day_rate').text(formatNumber(parseFloat(amount/man_days)));
				fieldset.find('.sandu_total').text(formatNumber(parseFloat(amount *(sandu/100))));
			} 
			else 	{	fieldset.find('.man_day_rate').text(formatNumber(parseFloat(amount/man_days)));	}
		}else if (amount != NaN && ( (parseFloat(fieldset.find('#ea_category').val()) == 28 && parseFloat(fieldset.find('#ea_customization').val()) == 1) || (parseFloat(fieldset.find('#ea_category').val()) == 27 && (parseFloat(fieldset.find('#ea_template').val()) == 2 || (parseFloat(fieldset.find('#ea_template').val()) == 6 && region !=59)))) )
		{
			fieldset.find('.sandu_total').text(formatNumber(parseFloat(amount *(sandu/100))));
		}
		else{	fieldset.find('.man_day_rate').text('');}
	}	
	function showHeader(element){
		var url = $(element).attr('href');
		$.ajax({ type: "POST",	url: url,	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
						$('.edit_header_content').html(data.html);	$('.edit_header_content').removeClass('hidden');	$('.header_content').addClass('hidden'); 	}  	} 		}	});
	}
function updateTermSandU(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageTermSandU');?>";
		if (id != 'new') {	url += '/'+parseInt(id); }
		$.ajax({
	 		type: "POST",
	 		data: $(element).parents('.terms_fieldset').serialize() + '&EaPaymentTerms['+id+'][id_ea]=<?php echo $model->id;?>' + '&id_ea=<?php echo $model->id;?>',					
		  	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {	
					if (data.status == 'saved') {
					  	if (id == 'new') {	$(element).parents('.tache.new').remove();	$('.new_termSU').show(); }		 	
				  		$.fn.yiiGridView.update('sanduterms-grid');
				  	} else {
				  		if (data.status == 'success') {	$(element).parents('.tache.new').replaceWith(data.form);	}
				  	}	}	}	});
	}
	function updateTerm(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageTerm');?>";
		if (id != 'new') {	url += '/'+parseInt(id); }
		$.ajax({
	 		type: "POST",
	 		data: $(element).parents('.terms_fieldset').serialize() + '&EaPaymentTerms['+id+'][id_ea]=<?php echo $model->id;?>' + '&id_ea=<?php echo $model->id;?>',					
		  	url: url, 	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	if (id == 'new') {	$(element).parents('.tache.new').remove();	$('.new_term').show();	}		
				  		$.fn.yiiGridView.update('terms-grid');	 
				  	} else {
				  		if (data.status == 'success') {	$(element).parents('.tache.new').replaceWith(data.form);	}
				  	}	}	} });
	}
	function createNote(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageNote');?>";
		if (id != 'new') {	url += '/'+parseInt(id); }
		$.ajax({
	 		type: "POST",	data: $(element).parents('.note_fieldset').serialize(),	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	if (id == 'new') {	$(element).parents('.tache.new').remove();	$('.new_note').show();	}		
				  		$.fn.yiiGridView.update('terms-grid');	$.fn.yiiGridView.update('sanduterms-grid');
				  	} else {
				  		if (data.status == 'success') {	$(element).parents('.tache.new').replaceWith(data.noteform);
							$('.new_note').hide();	$('.notte').show();
				  		} }	}	}	});
	}
	var unLockupdate = false;
	function updateItem(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageItem');?>";
		if (id != 'new') {	url += '/'+parseInt(id); }
		$.ajax({
	 		type: "POST",
	 		data: $(element).parents('.items_fieldset').serialize() + '&EasItems['+id+'][id_ea]=<?php echo $model->id;?>' + '&id_ea=<?php echo $model->id;?>',					
		  	url: url, 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	if (id == 'new') {	$(element).parents('.tache.new').remove();	$('.new_item').show();	$.fn.yiiGridView.update('items-grid'); 	}	
				  		$.fn.yiiGridView.update('terms-grid');	$.fn.yiiGridView.update('sanduterms-grid');	$.fn.yiiGridView.update('items-grid');
				  		$.each(data.amounts, function(i, item) {
				  		   if(i=='net_amount'){	
				  		  	 	$('#'+i).val(item); }
				  		   	else if(i.indexOf("usd") !=-1)
				  		   	{
								$('[name='+i+']').html(item);
				  		   	}
				  		   	else{	
				  		   		$('#'+i).html(item); }	
				  		});
				  		unLockupdate = false;
				  	} else {
				  		if (data.status == 'success') {	$(element).parents('.tache.new').replaceWith(data.form);	}
				  	}  } } });
	}
	function submitForm(element) {
		var data = $("#ea-form").serialize() + '&ajax=eas-form';
		$.ajax({type: "POST",	data: data,	dataType: "json",	url : $("#ea-form").attr("action"),
		  	success: function(data) {
		  		element.disabled = false;
			  	if (data && data.status) {
			  		$('.errorMessage').html('');  	console.log(data);
				  	if (data.status == "saved") {				  		
					  	if (data.error) {	showErrors(data.error);	} else { closeTab(configJs.current.url); }	} 
			  	}	} });
	}
	function showItemForm(element, newItem){
		var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageItem');?>";
		} else {	url = $(element).attr('href'); }
		$.ajax({type: "POST", 	url: url,	dataType: "json", 	data: {'id_ea':<?php echo $model->id;?>},
		  	success: function(data) {
			  	if (data) {	if (data.status == 'success') {
					  	if (newItem) {	$('.new_item').hide();	$('.new_item').after(data.form);
					  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="7" class="noback">' + data.form + '</td>');
					  	} } }	} });
	}
	function showItemFormNew(newItem) {
		var str='<div id="additionaltag" class="newtask"><u><b></b></u></div>';
		var element = $(' ');	element.html(str);	$('#additionaltagContainer').removeClass("hidden");	var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageTermSecond');?>";
		} else { url = $(element).attr('href'); }
		$.ajax({type: "POST", 	url: url,	data: {'id_ea':<?php echo $model->id;?>}, 	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) {
					  		$('.new_termappend').hide();	$('.new_termappend').after(data.form);	$('#additionaltagContainer').addClass("hidden");
					  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="7" class="noback">' + data.form + '</td>');  }
				  	}	}	}	});
	}
	function showItemFormNewSU(newItem) {
		var str='<div id="additionaltagSU" class="newtask"><u><b></b></u></div>';	var element = $(' ');	element.html(str);
		$('#additionaltagContainerSU').removeClass("hidden");	var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageTermSandUSec');?>";
		} else { url = $(element).attr('href');	}
		$.ajax({type: "POST",	url: url,	data: {'id_ea':<?php echo $model->id;?>},	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) {
					  		$('.new_termappendSU').hide();	$('.new_termappendSU').after(data.form);	$('#additionaltagContainerSU').addClass("hidden");
					  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="7" class="noback">' + data.form + '</td>'); 	}
				  	}
			  	}	}	});
	}	
	function showTermForm2(element, newItem){		
		var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageTermSandU');?>";
		} else {	url = $(element).attr('href');	}
		$.ajax({type: "POST",	url: url,	data: {'id_ea':<?php echo $model->id;?>},	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) {	$('.new_termSU').hide();	$('.new_termSU').after(data.form);
					  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="7" class="noback">' + data.form + '</td>'); 	}
				  	}  } } });
	}
	function showTermForm(element, newItem){		 
		var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageTerm');?>";
		} else {	url = $(element).attr('href');	}
		$.ajax({type: "POST",url: url,	data: {'id_ea':<?php echo $model->id;?>},	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) {	$('.new_term').hide();	$('.new_term').after(data.form);
					  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="7" class="noback">' + data.form + '</td>'); 	}
				  	} } } });
	}
	function showNoteForm(element, newItem){
		var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageNote');?>";
		} else { url = $(element).attr('href'); }
		$.ajax({ type: "POST",	url: url,	data: {'id_ea':<?php echo $model->id;?>}, dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) {	$('.new_note').hide();	$('.new_note').after(data.noteform);
					  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="7" class="noback">' + data.noteform + '</td>'); 	}
				  	} } } });
	}
	function submitDiscount(element) {
		var val = parseFloat($('#Eas_discount').val());
		if (isNaN(val)) { val = 0; }
		$.ajax({ type: "POST", url: '<?php echo Yii::app()->createAbsoluteUrl('eas/saveDiscount', array('id' => $model->id));?>',
			data: { 'Eas[discount]' : val}, dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved') {
				  		$.fn.yiiGridView.update('terms-grid');	$.fn.yiiGridView.update('sanduterms-grid');
					  	if(unLockupdate){
				  		$.each(data.amounts, function(i, item) {
				  			if(i=='net_amount'){	
				  		  	 	$('#'+i).val(item); }
				  		   	else if(i.indexOf("usd") !=-1)
				  		   	{
								$('[name='+i+']').html(item);
				  		   	}
				  		   	else{	
				  		   		$('#'+i).html(item); }	 });				  			
				  		}	unLockupdate = false;
				  	} } } });
	}
	function changeDiscount(element){
		var val = parseFloat($(element).val());
		$.ajax({type: "POST",url: '<?php echo Yii::app()->createAbsoluteUrl('eas/saveNetAmount', array('id' => $model->id));?>',
			data: { 'val' : val},dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved') {
				  		$.fn.yiiGridView.update('terms-grid');	$.fn.yiiGridView.update('sanduterms-grid');
					  	if(unLockupdate){
				  		$.each(data.amounts, function(i, item) {
				  			if(i=='discountinput'){ $('.'+i).val(item+'%'); }else if(i.indexOf("usd") !=-1)
				  		   	{
								$('[name='+i+']').html(item);
				  		   	}
				  		   	else{	
				  		   		$('#'+i).html(item); }	 });				  		
				  		}
				  		unLockupdate =false;
				  	} } } });
	}
	function addComma(element) {
		var val = parseFloat($(element).val());
		if (!isNaN(val)) {	
			val= val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			$(element).val(val);
		}
	}
	function removeComma(element){
		var val = parseFloat($(element).val().replace(/,/g, '')); 
		if (isNaN(val)) {	$(element).val(0); } else { $(element).val(val); }
	}
	function addPercent(element) {
		var val = parseFloat($(element).val());
		if (isNaN(val)) {	$(element).val('0%');			
		} else {
			if ($(element).hasClass('discountinput') && val > 100) { val = 100; }
			$(element).val(val + '%');
		}
	}	
	function removePercent(element){
		var val = parseFloat($(element).val());
		if (isNaN(val) || val == 0) {	$(element).val(""); } else { $(element).val(val); }
	}
	function updateHeader(element){
		$.ajax({ type: "POST", data: $('#header_fieldset').serialize()  + '&ajax=eas-form',					
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('eas/updateHeader', array('id' => $model->id));?>", dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		element.disabled = false;
				  	if (data.status == 'saved' && data.html) {
				  		$('.header_content').html(data.html); $('.header_content').removeClass('hidden'); $('.edit_header_content').addClass('hidden');

				  		if(data.flag == 'true')
				  		{
				  			$.each(data.amounts, function(i, item) {
				  			if(i.indexOf("usd") !=-1)
				  		   	{
								$('[name='+i+']').html(item);
				  		   	}
				  		   	else{	
				  		   		$('#'+i).html(item); } });	
				  			$.fn.yiiGridView.update('sanduterms-grid');
				  		}

				  	} else {
				  		if (data.status == 'success' && data.html) {	$('.edit_header_content').html(data.html); }
				  	}
				  	if (data.can_modify == false) {	modeNotEditable();	} else {	modeEditable(); 	}
				  	showErrors(data.error);
			  	} } });
		submitDiscount(document.getElementsByClassName("discountinput"));
	}	
	function ApproveEA(element) {
		$.ajax({type: "POST",data: $('#header_fieldset').serialize()  + '&ajax=eas-form', url: "<?php echo Yii::app()->createAbsoluteUrl('eas/ApproveEA', array('id' => $model->id));?>", 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved' && data.html) {
				  		$('.header_content').html(data.html);	$('.header_content').removeClass('hidden');	$('.edit_header_content').addClass('hidden');
				  	} else {
				  		if (data.status == 'success' && data.html) {	$('.edit_header_content').html(data.html); }
				  	}
				  	if (data.can_modify == false) {	modeNotEditable(); } else {	modeEditable();  	}
				  	showErrors(data.error);
			  	} } });
	}
	function updateDuration(element) {
		$.ajax({type: "POST",data: {"training":element},	url: "<?php echo Yii::app()->createAbsoluteUrl('eas/updateDuration')?>", dataType: "json",
		  	success: function(data) {
			  	if (data) {	if (data.status == 'success') {	$('#training_duration').val(data.duration);	} 	}
	  		} });
	}
</script>