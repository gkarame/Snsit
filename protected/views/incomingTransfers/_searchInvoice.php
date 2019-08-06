<div class="wide search" id="search-checklists"><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get',)); ?>
		
		<div class="row width_common"><div class="selectBg_search">	<?php echo $form->label($model,'searched_inv'); ?>
				<span class="spliter"></span><div class="select_container ">	<?php echo $form->textField($model,'searched_inv',array('class'=>'width141')); ?>		</div>	</div></div>
							
		<div class="btncheck" ><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
			</div><div class="horizontalLine search-margin"></div>	<?php $this->endWidget(); ?></div>