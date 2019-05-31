<div class="wide search" id="search-projects"><?php $form=$this->beginWidget('CActiveForm', array(	'action'=>Yii::app()->createUrl($this->route),	'method'=>'get',)); ?>		
		<div class="row width_project_name width229">
			<div class="inputBg_txt">	<?php echo $form->label($model,'Project'); ?>	<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'name',
						'source'=>Projects::getActiveProjectsAutocomplete(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold',),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "width100"),));	?>	</div>	</div>		
		<div class="row width_common">	<div class="inputBg_txt"> <?php echo $form->label($model,'Customer'); ?><span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,'attribute' => 'customer_id','source'=>Projects::getCustomersAutocomplete(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold',),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "width94"),	));	?>	</div>	</div>			
		<div class="row width_common">	<div class="inputBg_txt">	<?php echo $form->label($model,'Status'); ?>	<span class="spliter"></span>
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'status','source'=>Projects::getStatusLabelDrop(),'options'=>array('minLength'	=>'0','showAnim'	=>'fold'),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "width94",'value' =>'Active'),
				));	?>	</div>	</div>		
		<div class="row width_prj_man">	<div class="inputBg_txt"><?php echo $form->label($model,'Project Manager'); ?>	<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,	'attribute' => 'project_manager','source'=>Projects::getUsersAutocompleteDS(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold',),'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "width96"),));?></div></div>
			<div class="row width_common margint10 width229" ><div class="inputBg_txt" ><?php echo $form->label($model,'tm' , array('style'=>'width:100px! important;')); ?>
				<span class="spliter"></span><?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'tm','source'=>Projects::getTm(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold'),'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "width100",'value' =>''),
				));	?>	</div>	</div>	
		<div class="row width_common margint10"><div class="inputBg_txt"><?php echo $form->label($model,'id_type'); ?>	<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'id_type','source'=>Projects::getAllCategoriesProjects(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold'),'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",	'class'		=> "width94"	),	));	?>	</div>	</div>	

		<div class="row margint10 width_common">
			<div class="selectBg_search">
				<?php echo $form->label($model, 'template'); ?>
				<span class="spliter"></span>
				<div class="select_container "><?php echo $form->dropDownList($model, 'template', Eas::getTemplateList(), array('prompt'=>'','style'=>'width:110% !important;')); ?></div>
			</div>
		</div>

		<div class="btn">	<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>	</div>	<div class="horizontalLine search-margin"></div>	
	<?php $this->endWidget(); ?></div>