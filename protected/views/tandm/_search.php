<div class="wide search" id="search-invoices" style="overflow:inherit;"><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),	'method'=>'get',)); ?>
	
<div class="row project"><div class="inputBg_txt">	<?php echo $form->label($model, 'customer',array('style'=>'width:110px')); ?>	<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'notes',	'source'=>Invoices::getCustomersAutocomplete(),
'options'=>array('minLength'=>'0','showAnim'=>'fold',),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width106",),));?></div></div>

	<div class="row project " >	<div class="inputBg_txt">	<?php echo $form->label($model,'Project'); ?>	<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(	'model' => $model,	'attribute' => 'id_project','source'=>TandM::getAllTandMProjects(),
						'options'=>array('minLength'=>'0','showAnim'=>'fold',),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'=> "width136",),)); ?>
			</div>	</div>	<div class="row dateRow ">	<div class="inputBg_txt">	<?php echo $form->label($model,'Month',array('style'=>'width:110px')); ?>	<span class="spliter"></span>
			<div class="select_container" style="width:106px">	<?php echo $form->dropDownList($model,'tandm_month',Invoices::getMonths(),array('prompt'=>'')); ?>	</div>	</div>	</div>
	<div class="row dateRow margint10" >	<div class="inputBg_txt">	<?php echo $form->label($model,'Year',array('style'=>'width:110px')); ?><span class="spliter"></span>
			<div class="select_container" style="width:106px">	<?php echo $form->dropDownList($model,'tandm_year',Invoices::getYears(),array('prompt'=>'')); ?>	</div>	</div>	</div> 
	<div class="row author margint10">	<div class="inputBg_txt">	<?php echo $form->label($model,'pm'); ?>	<span class="spliter"></span>
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'project_manager','source'=>TandM::getUsersAutocomplete(),
						'options'=>array('minLength'=>'0','showAnim'=>'fold',),	'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",	'class'	  => "width136",),	));		?>
			</div>	</div>	<div class="row margint10  " style="margin-left:10px;">	<div class="inputBg_txt">	<?php echo $form->label($model,'status',array('style'=>'width:110px')); ?>
			<span class="spliter"></span><div class="select_container" style="width:106px">	<?php echo $form->dropDownList($model,'status',TandM::getStatusList(),array('prompt'=>' ')); ?>
			</div>	</div>	</div>	<div class="row ea_number  margint10" >	<div class="inputBg_txt" >	<?php echo $form->label($model,'ea_#',array('style' => 'width:110px;')); ?>
				<span class="spliter"></span>	<?php echo $form->textField($model,'ea_number',array('class' => 'width106')); ?>	</div>	</div>
	<div class="btn" >	<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>	<div class="wrapper_action" id="action_tabs_right">
					<div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>	<div class="action_list actionPanel">
			    	<div class="headli"></div>	<div class="contentli">	<?php if(GroupPermissions::checkPermissions('general-tandm','write')){	?>
						<div class="cover">	<div class="li noborder" onclick="inputrate();">INPUT RATE</div></div>
							<?php if(TandM::validateUserPerm()){	?>
						<div class="cover">	<div class="li noborder" onclick="generateInvoice();">GENERATE INVOICE</div></div>
							<?php } ?>
						<div class="cover"> <div class="li noborder"><a class="li noborder" id="mylink" href=""  style="margin-left:-12px;text-decoration:none!important;" onclick="printtimesheets();">PRINT TIMESHEET</a></div>
						<?php if(TandM::validateUserPerm()){	?>
						</div><div class="cover">	<div class="li noborder" onclick="deletetm();">DELETE</div>		</div>
							<?php } ?>
								<?php } ?>	</div>	
			    </div>   <div id="users-list" style="display:none;"></div>	 </div>	</div><div class="horizontalLine search-margin"></div><?php $this->endWidget(); ?></div>
