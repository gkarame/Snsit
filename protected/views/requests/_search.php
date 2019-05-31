<div class="wide search" id="search-requests"><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get',)); ?>
	<div class="row">	<div class="selectBg_search">	<?php echo $form->label($model,'user_id'); ?>	<span class="spliter"></span>
			<div class="select_container">	<?php echo $form->dropDownList($model, 'user_id', Users::getAllSelect(), array('prompt' => Yii::t('translations', 'Choose user'))); ?>
			</div>	</div> <?php echo $form->error($model,'user_id'); ?></div>	<div class="row">	<?php echo $form->label($model,'startDate'); ?>	<div class="dateInput">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'startDate', 'cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111'),    ));		?>
			<span class="calendar calfrom"></span><?php echo $form->error($model,'startDate'); ?>	</div></div>
	<div class="row"><?php echo $form->label($model,'endDate'); ?>	<div class="dateInput">
			<?php  $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model, 'attribute'=>'endDate', 'cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),'htmlOptions'=>array('class'=>'width111'),));
			?><span class="calendar calfrom"></span><?php echo $form->error($model,'startDate'); ?>	</div>	</div>	<div class="row margint10">		<div class="selectBg_search">
			<?php echo $form->labelEx($model,'Leave Type'); ?>	<span class="spliter"></span>	<div class="select_container">
				<?php echo $form->dropDownList($model, 'type', Requests::requstsType(), array('prompt' => Yii::t('translations', 'Choose leave type'))); ?>
			</div></div> <?php echo $form->error($model,'user_id'); ?></div>
	<div class="row margint10">	<div class="selectBg_search">	<?php echo $form->labelEx($model,'status'); ?>	<span class="spliter"></span>
			<div class="select_container">	<?php echo $form->dropDownList($model, 'status', Requests::getAllStatus(), array('prompt' => Yii::t('translations', 'Choose status'))); ?>
			</div>	</div>	 <?php echo $form->error($model,'user_id'); ?>	</div>
	<div class="btn"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>	</div><div class="horizontalLine search-margin"></div><?php $this->endWidget(); ?></div>
			