<div class="wide search" id="search-maintenance" style="overflow:inherit;"><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get','id'=>'search_maintenance')); ?>		
<div class="row width203"><div class="inputBg_txt"><label><?php echo Yii::t('translations', 'Customer');?></label><span class="spliter"></span>
<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer','source'=>Maintenance::getCustomersAutocomplete(),	'options'=>array('minLength'=>'0','showAnim'=>'fold',),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",),));?></div>
</div><div class="row width203"><div class="selectBg_search"><?php echo $form->label($model,'owner',array('class'=>'width71')); ?>
<span class="spliter"></span><?php echo CHtml::activeDropDownList($model, 'owner', Codelkups::getCodelkupsDropDown('partner'), array('prompt'=>Yii::t('translations', 'Select Owner'),'class'=>'width111 paddingtb10 smaller_margin')); ?>
</div></div>
<div class="row width_common"><div class="inputBg_txt"><?php echo $form->label($model,'Product'); ?>
<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'product','source'=>Maintenance::getProductsAutocomplete(),
'options'=>array('minLength'=>'0','showAnim'	=>'fold',),'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'style'=> "width:97px"),));?>
</div></div>
<div class="row width203"><div class="selectBg_search"><?php echo $form->label($model, 'status',array('class'=>'width71')); ?>
<span class="spliter"></span><?php echo $form->dropDownList($model,'status', Maintenance::getStatusList(),array('class'=>'width111 paddingtb10 smaller_margin')); ?>
</div></div>
<div class="row width203 margint10" ><div class="selectBg_search"><?php echo $form->label($model,'support_service2',array('class'=>'width71')); ?>
<span class="spliter"></span><?php echo CHtml::activeDropDownList($model, 'support_service', Codelkups::getCodelkupsDropDown('support_service'), array('prompt'=>Yii::t('translations', 'Select Service'),'class'=>'width111 paddingtb10 smaller_margin')); ?>
</div></div>
<div class="row width203 margint10" ><div class="selectBg_search"><?php echo $form->label($model,'Month',array('class'=>'width71')); ?>
<span class="spliter"></span><?php echo CHtml::activeDropDownList($model, 'starting_date', Invoices::getMonths(), array('prompt'=>Yii::t('translations', 'Select Month'),'class'=>'width111 paddingtb10 smaller_margin')); ?>
</div></div>
<div class="row width_common margint10"><div class="inputBg_txt"><label><?php echo Yii::t('translations', 'End Cust');?></label><span class="spliter"></span>
<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'end_customer','source'=>Maintenance::getCustomersAutocomplete(),	'options'=>array('minLength'=>'0','showAnim'=>'fold',),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",),));?></div>
</div>

<div class="row width203 margint10"><div class="selectBg_search"><?php echo $form->label($model, 'frequency',array('class'=>'width71')); ?>
<span class="spliter"></span><?php echo $form->dropDownList($model,'frequency', Codelkups::getCodelkupsDropDown('frequency'),array('class'=>'width111 paddingtb10 smaller_margin', 'prompt'=>Yii::t('translations', 'Select Freq'),)); ?>
</div></div>
<div class="btn"><div class="action-maintenance"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
</div><div class="wrapper_action" id="action_tabs_right"><div onclick="chooseActions()" class="action triggerAction"><u><b>ACTION</b></u><div class="action_list actionPanel " style="margin-top:-100px; margin-right:-30px;" >
<div class="headli"></div><div class="contentli" >
						<?php if(GroupPermissions::checkPermissions('financial-maintenance','write')){ ?>
						<div class="cover"><div class="li noborder" ><a class="special_edit_header" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('maintenance/create');?>"><?php echo Yii::t('translations', 'NEW CONTRACT');?></a></div></div>
						<div class="cover"><div class="li noborder special_edit_header" onclick="exportexcel();">EXPORT TO EXCEL</div></div>
						<div class="cover"><div class="li noborder special_edit_header" onclick="openpopupfilter();">P&L REPORT</div></div>
						<div class="cover"><div class="li noborder special_edit_header" onclick="openinvpopupfilter();">NEW INVOICE</div></div><?php } ?></div>
					<div class="ftrli"></div></div><div id="users-list" style="display:none;"></div></div></div></div>		
<div class="horizontalLine search-margin"></div><?php $this->endWidget(); ?></div>