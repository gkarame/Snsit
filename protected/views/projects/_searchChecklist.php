<div class="wide search" id="search-checklists"><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get',)); ?>
		<div class="row width_commons" style="width:210px;"><div class="inputBg_txt">	<?php echo $form->label($model,'Phase'); ?>	<span class="spliter"></span>			
			<div class="select_container " style="width:100px;"><?php echo CHtml::activeDropDownList($model,"name",Milestones::getAllMilestonesByCtegoryIdDDL(27, true), array('prompt'=>'','style'=>'width:115px;' )); ?></div>	</div></div>
		<div class="row width_common"><div class="selectBg_search">	<?php echo $form->label($model,'Status'); ?>
				<span class="spliter"></span><div class="select_container ">	<?php echo CHtml::activeDropDownList($model,"status",Checklist::getStatusList(), array('prompt'=>'', 'style'=>'width:107px;')); ?>	</div>	</div></div>
		<div class="row width_project_name width229"><div class="selectBg_search">	<?php echo $form->label($model,'responsibility', array('style'=>'width:104px;')); ?>
				<span class="spliter"></span><div class="select_container " style="width:96px;">	<?php echo CHtml::activeDropDownList($model,"surveystatus",Checklist::getresponsibilityList(), array('prompt'=>'', 'style'=>'width:108px;')); ?>	</div>	</div></div>
							
		<div class="btncheck" >
			<div style="font-style: italic;float:left; padding-top:10px;padding-right:5px;" >	<a class="special_edit_header" href="<?php echo $this->createUrl('projects/getExcel', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Export To Excel');?></a>
			</div>	
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
			</div><div class="horizontalLine search-margin"></div>	<?php $this->endWidget(); ?></div>