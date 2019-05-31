<div class="wide search" id="search-suppliers"><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),	'method'=>'get',)); ?>
		<div class="row " >	<div class="inputBg_txt" >	<?php echo $form->label($model,'check'); ?>	<span class="spliter"></span><?php echo $form->textField($model,'check',array('class' => 'width111')); ?></div>
		</div>	
		<div class="row width203">	<div class="inputBg_txt">	<label><?php echo Yii::t('translations', 'Supplier');?></label>	<span class="spliter"></span>
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'name','source'=>Suppliers::getNamesAutocomplete(),
						'options'=>array('minLength'=>'0','showAnim'=>'fold',),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",
						),	));	?>	</div>	</div>
		<div class="row " >	<div class="selectBg_search">	<?php echo $form->label($model, 'id_type'); ?>
				<span class="spliter"></span>	<div class="select_container width111">		<?php echo $form->dropDownList($model, 'id_type', Codelkups::getCodelkupsDropDown('supplier_type'), array('prompt'=>'Select Type')); ?>
				</div>	</div>	</div>
		<div class="row margin_right0 width203">	<div class="inputBg_txt">	<label><?php echo Yii::t('translations', 'Contact');?></label>	<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'main_contact','source'=>Suppliers::getContactsAutocomplete(),
						'options'=>array('minLength'=>'0','showAnim'=>'fold',),	'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",	),	));	?>
			</div>	</div>
		<div class="row margint10" >	<div class="selectBg_search">	<?php echo $form->label($model, 'countryId'); ?>
				<span class="spliter"></span>	<div class="select_container width111">		<?php echo $form->dropDownList($model, 'countryId', Codelkups::getCodelkupsDropDown('country'), array('prompt'=>'Select Country')); ?>
				</div>	</div>	</div>
		<div class="btn"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>	</div>
		<div class="horizontalLine search-margin"></div>	<?php $this->endWidget(); ?></div>
