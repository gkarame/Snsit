<div class="bcontenu projects">
	<div class="stat_years">
		<span class="status status_project 100" id="allcat" onClick="changeType(<?php echo "100";?>)"><i>All /</i></span>
 		<span class="status status_project <?php echo Projects::TYPE_SW;?>" onClick="changeType(<?php echo Projects::TYPE_SW;?>)"><i>SW / </i></span>
 		<span class="status status_project <?php echo Projects::TYPE_CONSULTING;?>" onClick="changeType(<?php echo Projects::TYPE_CONSULTING;?>)"><i>Consultancy  </i></span>
 		<span class="status overrun_project <?php echo 2;?>" style="float:center;padding-left:160px;" onClick="getOverrun(2)"><i>All/</i></span>
		<span class="status overrun_project <?php echo 0;?>" style="float:center;" onClick="getOverrun(0)"><i>Positive OverRun/</i></span>
 		<span class="status overrun_project <?php echo 1;?>" style="float:center;"   onClick="getOverrun(1)"><i>Negative OverRun </i></span>	 			
 		<span class="status statuses_project <?php echo Projects::STATUS_INACTIVE;?>" style="float:right;padding-right:20px;" onClick="changeStatus(<?php echo Projects::STATUS_INACTIVE;?>)"><i>Inactive </i></span>
 		<span class="status statuses_project <?php echo Projects::STATUS_CLOSED;?>" style="float:right;"   onClick="changeStatus(<?php echo Projects::STATUS_CLOSED;?>)"><i>Closed /</i></span>	 			
 		<span class="status statuses_project <?php echo Projects::STATUS_ACTIVE;?>" style="float:right;" onClick="changeStatus(<?php echo Projects::STATUS_ACTIVE;?>)"><i>Active /</i></span>
 		<span class="status statuses_project 100" id ="allsts" style="float:right" onClick="changeStatus(<?php echo 100;?>)"><i>All /</i></span>
 		</div>
 	<div id="widget_projects">
 		<div class="boardrow color333">
		<div class="width122 inline-block"><span class="width122"><b>Customers</b></span></div>
		<div class="width122 inline-block "><span class="width122"><b>Project</b></span></div>
		<div class="width89 inline-block"><span class="width89"><b>Budg.MDs</b></span></div>
		<div class="width89 inline-block"><span class="width89"><b>Actual MDs</b></span></div>
		<div class="width81 inline-block"><span class="width81"><b>Rem.MDs</b></span></div>
		<div class="width81 inline-block"><span class="width81 orderb"><b>Budg.Rate</b></span></div>
		<div class="width10 inline-block nobackground"><span class="up ActualRate ASC" onClick="changeOrder('ActualRate ASC');"></span><span class="down ActualRate DESC" onClick="changeOrder('ActualRate DESC')"></span></div>
		<div class="width75 inline-block"><span class="width75"> <b> Actual Rate</b></span></div>
		<div class="width89 inline-block">	<span class="width89" style="float:left; "><b>Profit</b></span></div>
	    <div class="width10 inline-block nobackground"><span class="up Overrun ASC" onClick="changeOrderOver('Overrun ASC')"></span><span class="down Overrun DESC" onClick="changeOrderOver('Overrun DESC')"></span></div>
		<div class="width75 inline-block nobackground "><span class="width75"><b>OverRun %</b></span></div>
		</div>	
		<?php $projects = WidgetProjects::getProjects(); foreach ($projects as $project) {
			$id = $project['projectid']; $cur=$project['currency']; $rate = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$cur'")->queryScalar();?>
		<div class="boardrow odd-even default" >
		 	<div class="width122 inline-block">
		 		<a  class = "show_link" href = <?php echo Yii::app()->createUrl("customers/view", array("id" => $project['customer_id']));?>>	<span  class="width122"><?php echo substr(Customers::getNameById($project['customer_id']),0,19);?> </span> </a>
		 	</div>
			 	<div   class="width122 inline-block" onmouseenter="showToolTipM(this);" onmouseleave="hideToolTipM(this);">
					<div class="first_it panel_container">
				<a  class = "show_link" href = <?php echo Yii::app()->createUrl("projects/view", array("id" => $id));?>>	<span class="item_clip clip"><?php echo substr(Projects::getNameById($id),0 ,14) ?></span></a>
						 <u class="red">+</u>
						 <div class="panelM" style = "left:80px">
							 <div style="  background-image: url('images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
							 <div style="  background-image: url('images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
							 	<div class="coverM" style=" background-image: url('images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u>Project Name:</u></b> ".Projects::getNameById($id); echo "<br/>" ; if(Projects::getCurrentMilestone($id)<>'' && Projects::getCurrentMilestone($id)<>' ' && Projects::getCurrentMilestone($id)<>0) {echo "<b><u>Current Milestone:</b></u> ".Milestones::getMilestoneDescription(Projects::getCurrentMilestone($id));} ?> </div>
							 </div>
							 <div  style="background-image: url('images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
						 </div>
					 </div>
			 	</div>
		 	<div class="width89 inline-block"><span  class="width89"><?php echo Utils::formatNumber($project['TotalMDs'],2); ?></span>	</div>
		 	<div class="width89 inline-block "><span  class="width89"><?php echo Utils::formatNumber($project['ActualMDs'],2); ?></span></div>
			<div class="width81 inline-block "><span  class="width81"><?php echo Utils::formatNumber($project['RemaingMDs'],2) ;?></span></div>
			<div class="width81 inline-block "><span  class="width81"><?php echo Utils::formatNumber($project['BudgetedRate']/$rate,1) ; echo " $";   ?></span>
			</div>
			<div class="width89 inline-block "><span  class="width89"><?php echo Utils::formatNumber($project['ActualRate']/$rate,1) ; echo " $";  ?></span></div>
			<div class="width89 inline-block "><span  class="width89 "><?php echo Utils::formatNumber((($project['BudgetedRate']/$rate)*$project['RemaingMDs']),1) ; echo " $"; ?></span>
			 	</div>
			<div class="width81 inline-block nobackground">
			 		<span  class="width81 <?php if ($project['Overrun']<0){echo "red" ;} ?>"><?php echo Utils::formatNumber($project['Overrun'],2) ;?></span>
			 	</div>
		</div>	<?php }?>
	</div>
</div>
<?php $id = $this->getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
    'options'=>array(
        'title'=>'Dialog box 1',
        'autoOpen'=>false,
		'modal'=>true,
	    'width'=>1006,
	    'height'=>810,
		'resizable'=>false,	)));?>
<div class="bcontenu projects z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetProjects::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div>
	<div class="stat_years">
		<span class="status status_project 100" id="allcat"  onClick="changeType(<?php echo "100";?>)"> <i>All /</i></span>
 		<span class="status status_project <?php echo Projects::TYPE_SW;?>" onClick="changeType(<?php echo Projects::TYPE_SW;?>)"><i>SW / </i></span>
 		<span class="status status_project <?php echo Projects::TYPE_CONSULTING;?>" onClick="changeType(<?php echo Projects::TYPE_CONSULTING;?>)"><i>Consultancy  </i></span>
		<span class="status overrun_project <?php echo 2;?>" style="float:center;padding-left:230px;" onClick="getOverrun(2)"><i>All/</i></span>
 	 	<span class="status overrun_project <?php echo 0;?>" style="float:center;" onClick="getOverrun(0)"><i>Positive OverRun/</i></span>
 		<span class="status overrun_project <?php echo 1;?>" style="float:center;"   onClick="getOverrun(1)"><i>Negative OverRun </i></span>	 			
 		<span class="status statuses_project <?php echo Projects::STATUS_INACTIVE;?>" style="float:right;padding-right:20px;" onClick="changeStatus(<?php echo Projects::STATUS_INACTIVE;?>)"><i>Inactive </i></span>
 		<span class="status statuses_project <?php echo Projects::STATUS_CLOSED;?>" style="float:right;"   onClick="changeStatus(<?php echo Projects::STATUS_CLOSED;?>)"><i>Closed /</i></span>	 			
 		<span class="status statuses_project <?php echo Projects::STATUS_ACTIVE;?>" style="float:right;" onClick="changeStatus(<?php echo Projects::STATUS_ACTIVE;?>)"><i>Active /</i></span>
 		<span class="status statuses_project 100" id ="allsts" style="float:right" onClick="changeStatus(<?php echo 100;?>)"><i>All /</i></span>
 	</div> 	<?php $projects = WidgetProjects::getProjects(); ?>
 	<div id="widget_projects1" class="bigsize">
 	 	<div class="boardrow color333"><div class="width122 inline-block"> <span class="width122"><b>Customers</b></span></div>
		<div class="width122 inline-block "><span class="width122"><b>Project</b></span></div>
		<div class="width89 inline-block"><span class="width89"><b>Budg.MDs</b></span></div>
		<div class="width89 inline-block"><span class="width89"><b>Actual MDs</b></span></div>
		<div class="width81 inline-block"><span class="width81"><b>Rem.MDs</b></span></div>
		<div class="width81 inline-block"><span class="width81 orderb"><b>Budg.Rate</b></span></div>
		<div class="width10 inline-block nobackground"><span class="up ActualRate ASC" onClick="changeOrder('ActualRate ASC')"></span><span class="down ActualRate DESC" onClick="changeOrder('ActualRate DESC')"></span></div>
		<div class="width75 inline-block"><span class="width75"> <b> Actual Rate</b></span></div>
		<div class="width89 inline-block">	<span class="width89" style="float:left; "><b>Profit</b></span></div>
		<div class="width10 inline-block nobackground"><span class="up Overrun ASC" onClick="changeOrderOver('Overrun ASC')"></span><span class="down Overrun DESC" onClick="changeOrderOver('Overrun DESC')"></span></div>
		<div class="width75 inline-block nobackground "><span class="width75"><b>OverRun %</b></span></div>
	</div>	
		<?php $projects = WidgetProjects::getProjects();  foreach ($projects as $project) {
			$id = $project['projectid']; $cur=$project['currency']; $rate = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$cur'")->queryScalar();?>
		<div class="boardrow odd-even default" >
			<div class="width122 inline-block">
		 		<a  class = "show_link" href = <?php echo Yii::app()->createUrl("customers/view", array("id" => $project['customer_id']));?>>	<span  class="width122"><?php echo substr(Customers::getNameById($project['customer_id']),0,19);?> </span> </a>
		 	</div>
			 	<div   class="width122 inline-block" onmouseenter="showToolTipM(this);" onmouseleave="hideToolTipM(this);">
					<div class="first_it panel_container">
				<a  class = "show_link" href = <?php echo Yii::app()->createUrl("projects/view", array("id" => $id));?>>	<span class="item_clip clip"><?php echo substr(Projects::getNameById($id),0 ,14) ?></span></a>
						 <u class="red">+</u>
						 <div class="panelM" style = "left:80px">
							 <div style="  background-image: url('../images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
							 <div style="  background-image: url('../images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
							 	<div class="coverM" style=" background-image: url('../images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u>Project Name:</u></b> ".Projects::getNameById($id); echo "<br/>" ; if(Projects::getCurrentMilestone($id)<>'' && Projects::getCurrentMilestone($id)<>' ' && Projects::getCurrentMilestone($id)<>0) {echo "<b><u>Current Milestone:</b></u> ".Milestones::getMilestoneDescription(Projects::getCurrentMilestone($id));} ?> </div>
							 </div>	<div  style="background-image: url('../images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
						 </div>
					 </div>
			 	</div>
		<div class="width89 inline-block"><span  class="width89"><?php echo Utils::formatNumber($project['TotalMDs'],2); ?></span></div>
		<div class="width89 inline-block "><span  class="width89"><?php echo Utils::formatNumber($project['ActualMDs'],2); ?></span></div>
		<div class="width81 inline-block "><span  class="width81"><?php echo Utils::formatNumber($project['RemaingMDs'],2) ;?></span></div>
		<div class="width81 inline-block "><span  class="width81"><?php echo Utils::formatNumber($project['BudgetedRate']/$rate,1) ;  echo " $";  ?></span>
		</div>
		<div class="width89 inline-block "><span  class="width89"><?php echo Utils::formatNumber($project['ActualRate']/$rate,1) ;  echo " $";  ?></span>
		</div>
		<div class="width89 inline-block "><span  class="width89 "><?php echo Utils::formatNumber((($project['BudgetedRate']/$rate)*$project['RemaingMDs']),1) ; echo " $"; ?></span>
		</div>
		<div class="width81 inline-block nobackground"><span  class="width81 <?php if ($project['Overrun']<0){echo "red" ;} ?>"><?php echo Utils::formatNumber($project['Overrun'],2) ;?></span>
		</div></div><?php }?>
	</div>
</div><?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
$(document).ready(function(){$('.summary').hide();var id_type=100;var status=100;$('.statuses_project.'+'1').addClass("colorRed");$('#allcat').addClass("colorRed");$('.overrun_project.'+'2').addClass("colorRed");getOverrun(1)});function changeType(type){$.ajax({type:"POST",data:{'type':type},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ProjectSort');?>",dataType:"json",success:function(data){if(data){$('.sear').addClass('hidden');$('.status_project').removeClass("colorRed");$('#widget_projects').html(data.html);$('#widget_projects1').html(data.html);$('.status_project.'+type).addClass("colorRed");id_type=type}}})}
function changeOrder(order){$.ajax({type:"POST",data:{'order':order},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ProjectSort');?>",dataType:"json",success:function(data){if(data){$('#widget_projects').html(data.html);$('#widget_projects1').html(data.html);id_order=order}}})}
function changeOrderOver(orderover){$.ajax({type:"POST",data:{'orderover':orderover},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ProjectSort');?>",dataType:"json",success:function(data){if(data){$('#widget_projects').html(data.html);$('#widget_projects1').html(data.html);id_order=order}}})}
function changeOrder2(order){var orderclass=order.split(' ');$('.up.'+orderclass[0]+'.'+orderclass[1]).addClass('hidden')}
function getOverrun(state)
{$.ajax({type:"POST",data:{'state':state},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ProjectSort');?>",dataType:"json",success:function(data){if(data){$('.sear').addClass('hidden');$('.overrun_project').removeClass("colorRed");$('#widget_projects').html(data.html);$('#widget_projects1').html(data.html);$('.overrun_project.'+state).addClass("colorRed");id_state=state}}})}
function changeStatus(status){$.ajax({type:"POST",data:{'status':status},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ProjectSort');?>",dataType:"json",success:function(data){if(data){$('.sear').addClass('hidden');$('.statuses_project').removeClass("colorRed");$('#widget_projects').html(data.html);$('#widget_projects1').html(data.html);$('.statuses_project.'+status).addClass("colorRed");id_status=status}}})}
function search_customer(id,customer_name){$('.new').hide();$.ajax({type:"POST",data:{'id':id,'type':id_type},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/GetProjects');?>",dataType:"json",success:function(data){if(data){$('.default').hide();$.each(data,function(id,message){$('#widget_projects').append('<div class = "boardrow odd-even new default"> <div class="width158 inline-block"> <span class="width158">'+message.name+'</span></div> <div class="width158 inline-block"><span class="width158">'+customer_name+'</span></div>  <div class="width280 inline-block"> <span class="width280">'+message.milestone+'</span></div>  <div class="width122 inline-block"><span class="width122">'+message.totalMDs+' </span></div>  <div class="width122 inline-block nobackground"><span class="width122">'+message.actualMDs+' </span></div>     </div></div>');$('#widget_projects1').append('<div class = "boardrow odd-even new default"> <div class="width158 inline-block"> <span class="width158">'+message.name+'</span></div> <div class="width158 inline-block"><span class="width158">'+customer_name+'</span></div>  <div class="width280 inline-block"> <span class="width280">'+message.milestone+'</span></div>  <div class="width122 inline-block"><span class="width122">'+message.totalMDs+' </span></div>  <div class="width122 inline-block nobackground"><span class="width122">'+message.actualMDs+' </span></div>     </div></div>')})}}})}
function show(element){$('.status_project').removeClass("colorRed");if($('.sear').hasClass('hidden')){$('.sear').removeClass('hidden');$(element).addClass('colorRed')}
else{$('.sear').addClass('hidden')}}
</script>	