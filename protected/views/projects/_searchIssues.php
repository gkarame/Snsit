<?php $status = ProjectsIssues::getStatusContor($model->id);?><div class="wide search" id="search-checklists"><div id="incidents" >	<div class="divp em child" style="    padding-left: 80px;">
<?php  foreach ($status as $key_status => $stat) { ?>	<div onclick="triggerSDSearch('<?php echo $key_status;?>');" style="width: 124px;" class="phase inline-block height85px normal st_<?php echo $key_status; ?>">
					<span class="text" style="margin-top:-1px !important;"><?php echo ProjectsIssues::getStatus($key_status);?></span>
					<span class="numberp" ><?php echo $stat;?> <span style="color:#989898;"><?php echo "/ ";echo ProjectsIssues::getTotalIssues($model->id); ?></span></span>	</div>			<?php } ?>	


						<div onclick="triggerSDSearch('All');" class="phase inline-block height85px normal st_12 " style="cursor: pointer;">
					<span class="text" style="margin-top:-1px !important;"><?php echo "ALL";?></span>
					<span class="numberp" ><?php echo ProjectsIssues::getTotalIssues($model->id);;?></span>	</div>	


					</div></div>
	<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),	'method'=>'get',)); ?>
	<div class="horizontalLine search-margin" style="margin-top:-10px;margin-bottom:10px;"></div>


		<div style="font-style: italic;float:left; padding-top:10px;padding-right:5px; " > Priority <a style="cursor: pointer;" class="sr_p sr_p_all"  onclick="triggerSDPriority('All');">	 <?php echo Yii::t('translations', 'All');?> </a>
			</div>	
		<div style="font-style: italic;float:left; padding-top:10px;padding-right:5px;cursor: pointer; " onclick="triggerSDPriority('2');">	<a class="sr_p sr_p_2" > <?php echo Yii::t('translations', '/ High');?>  </a>
			</div>	
			<div style="font-style: italic;float:left; padding-top:10px;padding-right:5px;cursor: pointer; " onclick="triggerSDPriority('1');" > <a class="sr_p sr_p_1" ><?php echo Yii::t('translations', '/ Medium');?>  </a>
			</div>	
			<div style="font-style: italic;float:left; padding-top:10px;padding-right:5px;cursor: pointer; " onclick="triggerSDPriority('0');" >	<a class="sr_p sr_p_0" > <?php echo Yii::t('translations', '/ Low');?>  </a>
			</div>	

		<div class="row width_common hidden"  ><div class="selectBg_search"><?php echo $form->label($model,'Priority'); ?>
				<span class="spliter"></span><div class="select_container " onblur="hidedropdown();" onclick="javascript:showdropdown();">
				<?php echo CHtml::activeDropDownList($model,"complexmodule",ProjectsIssues::getPriorities(), array('prompt'=>'','id'=>'inv_priority','prompt'=>'','style'=>'width:105px;')); ?>	</div>	</div>	</div>	
		

		<div class="row width_common hidden "  ><div class="selectBg_search"><?php echo $form->label($model,'Status'); ?>
				<span class="spliter"></span><div class="select_container " onblur="hidedropdown();" onclick="javascript:showdropdown();">
				<?php echo CHtml::activeDropDownList($model,"complexnotes",ProjectsIssues::getStatusList(), array('prompt'=>'','id'=>'inv_status','prompt'=>'','style'=>'width:105px;')); ?>	</div>	</div>	</div>	
		<div class="btncheck hidden"  ><div style="  margin-left: -120%;"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?></div>
			</div><div class="horizontalLine search-margin"></div>

	<?php $form=$this->endWidget(); ?>  
</div>
<div class="wrapper_action" id="action_tabs_right" >
	<div onclick="chooseActions();"  class="action triggerAction" style="margin-top: 145%;"><u><b>ACTION</b></u></div>
	<div class="action_list actionPanel" style="top: 150px !important;">  	<div class="headli"></div>	<div class="contentli">	
			<div class="cover">	<div class="li noborder" onclick="getUsersIssues();">ASSIGN RESOURCES</div>	</div>
			<div class="cover">	<div class="li noborder delete" onclick="getAssignedUsersIssues();">UNASSIGN RESOURCES</div>		</div>
			<div class="cover">	<div class="li noborder" onclick="getetd();"><a class="special_edit_header" href="<?php echo $this->createUrl('projects/getExcelIssues', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'EXPORT TO EXCEL');?></a></div>	</div>	
			
		</div>	<div class="ftrli"></div>   </div> 

		<div id="users-list-issues" style="display:none;top: 150px !important;"> </div>	 </div>	

		