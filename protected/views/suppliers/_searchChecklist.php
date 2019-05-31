<div class="wide search" id="search-checklists"><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get',)); ?>
		
		<div class="row width_common"><div class="selectBg_search">	<?php echo $form->label($model,'Status'); ?>
				<span class="spliter"></span><div class="select_container ">	<?php echo CHtml::activeDropDownList($model,"status",SuppliersPrint::getStatusList(), array('prompt'=>'', 'style'=>'width:107px;')); ?>	</div>	</div></div>
							
		<div class="btncheck" ><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
			</div><div class="horizontalLine search-margin"></div>	<?php $this->endWidget(); ?></div>