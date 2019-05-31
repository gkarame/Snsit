<div class="perftabs expenses_edit" >
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'productivity-form','enableAjaxValidation'=>false,)); ?>
	<div style="height:1px; width:100%; background-color:#ddd; margin-top:4px; margin-left:-5px;padding-right:10px; "></div>
  <?php if(GroupPermissions::checkPermissions('performance-productivity','write')){ ?>
<div class="add" style="margin-top:27px;"></div>  <?php } else { ?> <div class="hiddenadd " style="margin-top:27px;"></div>  <?php } ?>
<div class="hiddenadd hidden" style="margin-top:27px;"></div> <div class="taskAdd" style="margin-top:27px;"><div class="inner"><div class="itemsDiv">
<div class="item second"><div class="label">Project*</div><div class="field">	<?php echo $form->dropDownList($productivity, 'id_project', Performance::getAllProjectsAssignedtoUserProd($userid) , array('onchange'=>'refreshTaskList("P")','id'=>'prod_proj','prompt'=>'Choose Project')); ?></div>
</div><div class="item first"><div class="label">Phase/Task*</div><div class="field">	<?php echo $form->dropDownList($productivity, 'id_task', array() , array('id'=>'prod_task','prompt'=>'Choose Phase/Task')); ?></div>
</div><div class="item third"><div class="label">Expected MDs*</div><div class="field"><input type="text" id="prod_est"/></div></div>
<div class="options"><div class="save" onclick="SaveProductivity();">SAVE</div><div class="cancel">CANCEL</div></div></div><div id="perr" class="red" style="margin-top:10px;"></div>
</div></div><div id="productivity" class="tableCont" ><div class="table"   id="currentyear"  ><div class="perfrow titleRow"><div class="item first" style="width:130px !important;">Project</div>
<div class="item second" style="width:130px !important;">Phase/Task</div><div class="item third" style="margin-left:-30px;">Expected MDs</div>
<div class="item third" style="margin-left:-10px;">Actual MDs</div><div class="item third">OverRun %</div><div class="item third">Logged Date</div>
<div class="item third">Reason</div><?php if(GroupPermissions::checkPermissions('performance-productivity','write')) { ?><div class="item ">Delete</div> <?php } ?>
<div   id="ajxLoader"><p><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif"></p></div></div> 
<div class="datacontainer"> <div   id="newlyprodadded"></div><?php if(count($prod_list)>0){ foreach ($prod_list as $key => $value){ ?>
<div class="perfrow " id="P<?php echo $value['id'] ;?>">	 <div class="qualperfhorizontalLine" ></div> 
<div class="item first prodproj"  id="prodproj_<?php echo $value['id']; ?>" project="<?php echo $value['id']; ?>" ><?php if(strlen(Projects::getNameById2($value['id_project'], $value['maintenancec'], $value['internal_project']  ))<15){echo Projects::getNameById2($value['id_project'],$value['maintenancec'], $value['internal_project']) ; }else{ echo Performance::getPopupGrid(Projects::getNameById2($value['id_project'], $value['maintenancec'], $value['internal_project'])) ; } ?></div>
<div class="item second prodtask" style="width:100px !important;" id="prodtask_<?php echo $value['id']; ?>" project="<?php echo $value['id']; ?>" ><?php   
 if($value['internal_project']>0)
 {
 	$task_phase= InternalTasks::getNameById($value['id_task']);
 }
 else if($value['id_phase']=='0')
 {
 	$task_phase= ProjectsTasks::getTaskDescByid2($value['id_task'], $value['maintenancec']);
 }else{
 	$task_phase= ProjectsPhases::getPhaseDescByPhaseId($value['id_phase']);
 }
 if(strlen($task_phase)<15){ echo $task_phase; }else{ echo Performance::getPopupTaskGridProd($task_phase);} ?></div>
<div class="item third" style="margin-left:-20px;"><?php echo $value['expected_mds']; ?></div>
<div class="item third"><?php 
if($value['internal_project']>0)
{
	$actuals= Utils::formatNumber(InternalTasks::getTimeSpentperTask($value['id_task'] , $value['id_user']) );
}else if($value['id_phase']=='0')
{
	$actuals=(Utils::formatNumber(ProjectsTasks::getTimeSpentperTask($value['id_task'] , $value['id_user']) ));
}else{
	$actuals= (Utils::formatNumber( ProjectsPhases::getTimeSpentperPhase($value['id_phase'], $value['id_user']) ));
}
 echo $actuals; ?></div>
<div class="item third"><?php echo   $saverage =Utils::formatNumber((($value['expected_mds']-$actuals)*100 )/$value['expected_mds'] ); ?></div>
<div class="item third"><?php echo Utils::formatDate($value['adddate']); ?></div>
<div class="item third" > <?php if($actuals>$value['expected_mds']) {echo $form->textField($productivity,'reason',array('autocomplete'=>'off' ,'id'=>'prod_reason', "style"=>"width:90px !important;",'maxlength'=>500 ,'onkeyup' => 'updateReason("'.$value['id'].'", $(this).val())', 'value' => (($value['reason'])?$value['reason']:'')));}else{ ?> <div style="margin-left:50px !important;">&emsp;  </div><?php } ?></div>
<?php if(GroupPermissions::checkPermissions('performance-productivity','write')){ ?>
<div class="item " style="padding-right:25px; cursor:pointer" onclick="deleteButton('P','<?php echo $value['id']; ?>')"><img src="../../images/DeletePhase.png" height="15" width="15"></div>
<?php } ?></div><?php  }   } ?> </div></div><div id="newprodtable" class="hidden"></div> </div>	<?php $this->endWidget(); ?></div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.paging.min.js"></script>
<script>
 $("#pagination").paging(1337, { format: '[< ncnnn >]', onSelect: function(page) { },
                onFormat: function(type) {
                    switch (type) {
                        case 'block': // n and c
                            return '<a href="">' + this.value + '</a>';
                        case 'next': // >
                            return '<a href="">&gt;</a>';
                        case 'prev': // <
                            return '<a href="">&lt;</a>';
                        case 'first': // [
                            return '<a href="">first</a>';
                        case 'last': // ]
                            return '<a href="">last</a>';
                    } } });
</script>