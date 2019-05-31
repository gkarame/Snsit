<div class="tache new_item" id='services_form' style="height:70px;"><fieldset class="items_fieldset"><?php $id = 'new';?>
<div class="textBox inline-block itms amt right-free"><div class="input_text_desc padding_smaller"><label class="required">Services<span class="required">*</span></label></div>
<div class="input_text"><div class="hdselect"><?php echo CHtml::activeDropDownList($model, "id_service",  MaintenanceServices::getSupportServices(), array('prompt' => Yii::t('translations', 'Choose Service'), 'class'=>'input_text_value' ,'style'=>'width:200px;')); ?>
</div></div></div>
<div style="right:75px; top:35px;" class="save" onclick="updateService(this, '<?php echo $model->id ;?>');return false;"><u><b>SAVE</b></u></div>
<div style="color:#333; top:35px;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid');panelClip('.item_clip');
panelClip('.term_clip');<?php }?>"><u><b>CANCEL</b></u></div></fieldset></div>