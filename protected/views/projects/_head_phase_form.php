<div class="column1 item red special">	<input id="chech_<?php echo $model->id;?>" type="checkbox" name="chk1_1" onclick="checkedAll('<?php echo $model->id;?>')" class="checkbox_firstitem phase_check">
	<label for="chk1_1" class="uppercase label_check">		<b><?php echo $model->description;?></b>	</label></div>
<div class="itemlabel" style="margin-left:0px;">	<b><?php echo "Budgeted:";?></b></div><div id='<?php echo $model->id;?>_inital'> 
<?php	$budgetedMDs=Projects::getBudgetedMD($model->id_project);	$estimatedMDs=Projects::getEstimatedMD($model->id_project);			
if($model->assigned!="1" || $budgetedMDs!=$estimatedMDs){ if(GroupPermissions::checkPermissions('projects-tasks','write')){ ?>	
<div class="column30 item ram" style="text-align:center;top:-3px;" id="input_<?php echo $model->id; ?>">	
<?php echo CHtml::activeTextField($model, "man_days_budgeted", array('id'=>$model->id ,'class'=>'mdb','style'=> 'width:25px;top:-10px; text-align:center; font-color:#fff',"onchange"=>"changeInputPhases(value,$model->id,1)"));
?></div>	 <?php } } else{ ?> <div class="column30 item" style="top:-3px;" id="input_<?php echo $model->id; ?>">	
<?php echo CHtml::activeTextField($model, "man_days_budgeted", array('id'=>$model->id ,'disabled'=>'true','class'=>'mdb estez','style'=> 'width:45px; top:-10px;text-align:center; font-color:#fff',"onchange"=>"changeInputPhases(value,$model->id,1)"));
?></div><?php } ?>
</div><div class="itemlabel" style="margin-left:13px;">	<b><?php echo "Total (with offset):";?></b></div>
<div class="column30 item" style="text-align:center;" id="total"><span id= "<?php echo $model->id;?>-total-estimate" class="value" ><?php echo $model->total_estimated; ?></span>	<div id="offset-reas" style="display:none;"></div></div>  
<div class="itemlabel" style="margin-left:13px;">	<b><?php echo  "Actuals:";?></b></div>
<div class="column30 item" style="text-align:center;">	<span id="man_day_rate" class="value"><?php echo Utils::formatNumber(Projects::getActualDaysOf($model->id),2); ?></span></div>
<div class="column6 item" style="margin-left:30px;" >	<b><?php echo "Complexity"?></b></div>
<div class="column6 item" style="margin-left:45px;" >	<b><?php echo "Billable"?></b></div>
<?php if(GroupPermissions::checkPermissions('projects-tasks','write')){?>
	<div class="column7 item editingstyle red update_column" onclick="showPhaseForm(this,false,<?php echo $model->id; ?>);return false;"><img src="../../images/EditPhase.png" height="15" width="15"></div>
	<div class="column7 item editingstyle red noimg delete_column" onclick="deletePhase(<?php echo $model->id; ?>);return false;"><img src="../../images/DeletePhase.png" height="15" width="15"></div> <?php }?>
<script type="text/javascript"></script>