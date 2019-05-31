<div class="perftabs expenses_edit" >
	<div style="height:1px; width:100%; background-color:#ddd; margin-top:4px; margin-left:-5px;padding-right:10px; "></div>
<?php if(GroupPermissions::checkPermissions('performance-quality','write')) { ?> <div class="qadd" style="margin-top:27px;"></div> <?php }else{ ?>
<div class="hiddenadd " style="margin-top:27px;"></div>  <?php } ?><div class="hiddenadd hidden" style="margin-top:27px;"></div>
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'quality-form','enableAjaxValidation'=>false,)); ?><div class="qualAdd" style="margin-top:27px;">
<div class="inner"><div class="itemsDiv"><div class="item second"><div class="label">Project*</div>
<div class="field">	<?php echo $form->dropDownList($quality, 'id_project', Performance::getAllProjectsAssignedtoUserProd($userid) , array('onchange'=>'refreshTaskList("Q")','id'=>'qual_proj','prompt'=>'Choose Project')); ?></div>
</div><div class="item second"><div class="label">Phase/Task* </div><div class="field"><?php echo $form->dropDownList($quality, 'id_task', array() , array('id'=>'qual_task','prompt'=>'Choose Phase/Task')); ?> </div>
</div><div class="item third"><div class="label">Quality Controller* </div>
<div class="field">	<?php echo $form->dropDownList($quality, 'id_resc', Users::getAllSelect() , array('id'=>'qual_resc','prompt'=>'Choose QC')); ?></div>
</div><div class="item second"  style="padding-top:20px;"><div class="label">Notes </div><div class="field"><?php echo $form->textField($quality,'notes',array('autocomplete'=>'off' ,'id'=>'qual_notes' )); ?></div>
</div><div class="item first" style="padding-top:20px;"><div class="label">Expected Delivery Date* </div>
<div class="field">	<?php echo $form->textField($quality,'expected_delivery_date',array('autocomplete'=>'off' , 'id'=>'expec_date')); ?><span class="calendar calfrom"></span></div>
</div><div class="options"  style="padding-top:20px;"><div class="save" onclick="SaveQuality()">SAVE</div><div class="qcancel">CANCEL</div></div></div>
<div id="qerr" class="red" style="margin-top:10px;"></div></div></div><div id="quality"  class="tableCont"><div class="table" id="currentqualyear"><div class="perfrow titleRow">
<div class="item first">Project</div><div class="item second">Phase/Task</div><div class="item fourth">&nbsp;&nbsp;QC</div><div class="item fifth">&nbsp;&nbsp;Exp. Delivery Date</div>
<div class="item notes" >Notes</div><div class="item six" style="padding-left:30px;">Passed</div><?php if(GroupPermissions::checkPermissions('performance-quality','write'))
        { ?><div class="item " style="padding-left:10px;" >Delete</div><?php } ?></div> <div   id="qajxLoader"><p><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif"></p></div>
<div class="datacontainer"><div id="newlyqualadded"></div><?php  if(count($qual_list)>0){	foreach ($qual_list as $key => $value) { ?>
	<div class="perfrow "  id="Q<?php echo $value['id'] ; ?>">	 <div class="qualperfhorizontalLine" ></div> 
<div class="item first prodproj"  id="prodproj_<?php echo $value['id']; ?>" project="<?php echo $value['id']; ?>" ><?php 
if($value['internal_project']>0){
	$pname= Internal::getNameById($value['id_project']);
}else{
	$pname=Projects::getNameById($value['id_project']);
}
if(strlen($pname)<15){echo $pname ; }else{ echo Performance::getPopupGrid($pname) ; } ?></div>
<div class="item second prodtask"  id="prodtask_<?php echo $value['id']; ?>" project="<?php echo $value['id']; ?>"><?php   
if($value['internal_project']>0){
	$task_phase=  InternalTasks::getNameById($value['id_task']);
}else if($value['id_phase']=='0'){
	$task_phase=ProjectsTasks::getTaskDescByid($value['id_task']);
	} else{
		$task_phase=ProjectsPhases::getPhaseDescByPhaseId($value['id_phase']);
	}
	 if(strlen($task_phase)<26){ echo $task_phase; }else{ echo Performance::getPopupTaskGrid($task_phase);} ?></div>
<div class="item fourth" id="id_resc_<?php echo $value['id']; ?>"><?php if(GroupPermissions::checkPermissions('performance-quality','write')) {
	echo $form->dropDownList($quality, 'id_resc', Users::getAllSelect() , array('style'=>'width:95px;' ,'id'=>'qual_resc_'.$value['id'],'prompt' => (isset($value['id_resc']))? Users::getNameById($value['id_resc']):'Choose Qc' ,'onchange'=>'assignQA('.$value['id'].')'));
 }else{	if(isset($value['id_resc'])){ echo Users::getNameById($value['id_resc']);}else{ echo ""; } } ?></div>
 <div class="item fifth " style="<?php if (empty(trim($value['notes']))){ echo "padding-right:98px;"; } ?> "><?php   if(GroupPermissions::checkPermissions('performance-quality','write')){ ?>	
 <div class="item inline-block time normal" style="cursor:pointer; margin-left:0px;">
<div class=""><?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $quality,'attribute' => "expected_delivery_date", 
			    	'cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy','showAnim' => 'fadeIn','onSelect' => 'js:function( selectedDate ) {
   						assignDate(selectedDate, "'.$value['id'].'"); }' ), 'htmlOptions' => array( 'style'=>'width:100%','class' => 'datefield', 'id'=> $value['id'],'value'=> Quality::getDateFormat($value['edd']), ),   ));	?>
				<span class="calendar calfrom"></span>	</div>	</div><?php } else { 	if(isset($value['edd'])){ echo Users::getNameById($value['edd']);}else{ echo ""; } } ?></div>  
<div class="item notes" ><?php echo Performance::getnotes($value['notes']) ; ?></div>
<?php if(GroupPermissions::checkPermissions('performance-quality','write')){ ?><div class="item six" style="padding-left:10px;" id="score_<?php echo $value['id']; ?>"><?php echo $value['score']=='0'?'Yes':($value['score']=='1'?'No':$form->dropDownList($quality, 'score', array('Yes','No') , array('id'=>'qual_score_'.$value['id'],'prompt'=>$value['score']=='0'?'Yes':($value['score']=='1'?'No':''),'onchange'=>'editScore('.$value['id'].')')))  ;    ?></div> <?php } else { ?> <div class="item six" id="score_<?php echo $value['id']; ?>"><?php echo $value['score']=='0'?'Yes':($value['score']=='1'?'No':'') ;?></div> <?php } ?>
<?php if(GroupPermissions::checkPermissions('performance-quality','write')){ ?><div class="item " style="margin-left:40px; cursor:pointer"  onclick="deleteButton('Q','<?php echo $value['id']; ?>')" ><img src="../../images/DeletePhase.png" height="15" width="15"></div> <?php } ?>
</div><?php  } }?> <br/><br/><br/></div></div><div id="newqualtable" class="hidden"></div></div><?php $this->endWidget(); ?></div>
<script >
function dateset(id){  alert(id);}
</script>