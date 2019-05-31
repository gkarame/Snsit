<div class="wide search" id="search-users">
	<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get',	)); ?>	
		<div class="row"><div class="inputBg_txt"><?php echo $form->label($model,'firstname'); ?><span class="spliter"></span>
				<?php echo $form->textField($model,'firstname',array('size'=>50,'maxlength'=>50,'class'=>"width111")); ?></div></div>
		<div class="row"><div class="inputBg_txt"><?php echo $form->label($model,'lastname'); ?>
				<span class="spliter"></span><?php echo $form->textField($model,'lastname',array('size'=>50,'maxlength'=>50,'class'=>"width111")); ?></div></div>
		<div class="row"><div class="selectBg_search"><?php echo $form->label($model,'Status'); ?><span class="spliter"></span>
				<div class="select_container width111"><?php echo $form->dropDownList($model,'active', Users::getStatusList(), array('prompt'=>'','class'=>"width119")); ?>
				</div></div></div>	
		<div class="btn receivables-grid"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>		
		<div class="wrapper_action" id="action_tabs_right"><div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
		<div class="action_list actionPanel"><div class="headli"></div><div class="contentli"><div class="cover"><div class="li noborder" onclick="getExcel();">Export to Excel</div></div>
		</div><div class="ftrli"></div></div><div id="users-list" style="display:none;"></div></div></div><div class="horizontalLine search-margin"></div>
	<?php $this->endWidget(); ?></div>