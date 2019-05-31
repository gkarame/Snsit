<div class="bcontenu projects">
	<div class="stat_years">
		<span class="status status_proj 100" id="alltypes" onClick="changeProjType(<?php echo "100";?>)"><i>All /</i></span>
 		<span class="status status_proj <?php echo Projects::TYPE_SW;?>" onClick="changeProjType(<?php echo Projects::TYPE_SW;?>)"><i>SW / </i></span>
 		<span class="status status_proj <?php echo Projects::TYPE_CONSULTING;?>" onClick="changeProjType(<?php echo Projects::TYPE_CONSULTING;?>)"><i>Consultancy  </i></span>
 	</div>
 	<div id="widget_projectbudget">
 		<div class="boardrow color333">
<div class="width122 inline-block">
		 		<span class="width122"><b>Customers</b></span>
		 	</div>
		 	<div class="width122 inline-block ">
		 		<span class="width122"><b>Project</b></span>
		 	</div> 		 	
		 	<div class="width80 inline-block">
		 		<span class="width80"><b>Budget</b></span>
		 	</div>
		 	<div class="width89 inline-block">
		 		<span class="width89"><b>Actual MDs</b></span>
		 	</div>
		 	<div class="widthoffset inline-block">
		 		<span class="widthoffset"><b>Total InclOffset</b></span>
		 	</div>
		 	 <div class="width81 inline-block">
		 		<span class="width81 orderb"><b>Offset</b></span>
		 	</div>
			<div class="widthoffset inline-block">
		 		<span class="widthoffset"> <b>Offset Requests</b></span>
		 	</div>
		 	<div class="width122 inline-block nobackground ">
		 		<span class="width122 "><b>Potential OverRun</b></span>
		 	</div>
		</div>	
		<?php $projects = WidgetProjectRunOut::getProjects(); foreach ($projects as $project) {
			$id = $project['id']; $cur=$project['currency'];
			$rate = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$cur'")->queryScalar();?>
		<div class="boardrow odd-even default" >
		 	<div class="width122 inline-block">
		 		<span   href = <?php echo Yii::app()->createUrl("customers/view", array("id" => $project['id']));?>>	<span  class="width122"><?php echo substr(Customers::getNameById($project['customer_id']),0,19);?> </span> </span>
		 	</div>
			<div class="width122 inline-block" onmouseenter="showToolTipM(this);" onmouseleave="hideToolTipM(this);">
				<div class="first_it panel_container">
				<a  class = "show_link" href = <?php echo Yii::app()->createUrl("projects/view", array("id" => $id));?>>	<span class="item_clip clip"><?php echo substr(Projects::getNameById($id),0 ,14) ?></span></a>
						 <u class="red">+</u>
						 <div class="panelM" style = "left:80px">
							 <div style="  background-image: url('images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
							 <div style="  background-image: url('images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
							 	<div class="coverM" style=" background-image: url('images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u>Project Name:</u></b> ".Projects::getNameById($id); echo "<br/>" ;  ?> </div>
							 </div>
							 <div  style="background-image: url('images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
						 </div>
					 </div>
			 	</div>
		 	<div class="width80 inline-block">
		 		<span  class="width80"><?php echo Utils::formatNumber($project['budget'],2); ?></span>
		 	</div>
		 	<div class="width89 inline-block ">
		 		<span  class="width89"><?php echo Utils::formatNumber($project['actuals'],2); ?></span>
		 	</div>
			<div class="widthoffset inline-block ">
			 		<span  class="widthoffset"><?php echo Utils::formatNumber($project['includingoffset'],2) ;?></span>
			 	</div>
			  	<div class="width81 inline-block ">
			 		<span  class="width81"><?php echo Utils::formatNumber($project['offset'],2) ;?></span>
			 	</div>
			 	<div   class="width122 inline-block" onmouseenter="showoffsetToolTipM(this);" onmouseleave="hideToolTipoffset(this);">
				<div class="first_it panel_container">
			<span  class="widthoffset"><?php echo Utils::formatNumber($project['requests'],2) ; ?> <u class="red">+</u></span>
						 <div class="paneloffset" style = "left:80px">
							 <div style="  background-image: url('images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
							 <div style="  background-image: url('images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
							 	<div class="coveroffset" style=" background-image: url('images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u>Reasons:</u></b> ".$project['reasons']; echo "<br/>" ;  ?> </div>
							 </div> <div  style="background-image: url('images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
						 </div>
					 </div>
			 	</div>
			 	<div class="width122 inline-block ">
		 		<span  class="width122 <?php if ($project['potential']<0){echo "red" ;} ?>"><?php echo Utils::formatNumber($project['potential'],2); ?></span>
		 	</div>
		</div><?php }?>
	</div>
</div>
<?php $id = $this->getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Dialog box 1',
        'autoOpen'=>false,
		'modal'=>true,
	    'width'=>1006,
	    'height'=>810,
		'resizable'=>false,
	)));?>
<div class="bcontenu projects z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetProjectRunOut::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div>
	<div class="stat_years">
		<span class="status status_proj 100" id="alltypes" onClick="changeProjType(<?php echo "100";?>)"><i>All /</i></span>
 		<span class="status status_proj <?php echo Projects::TYPE_SW;?>" onClick="changeProjType(<?php echo Projects::TYPE_SW;?>)"><i>SW / </i></span>
 		<span class="status status_proj <?php echo Projects::TYPE_CONSULTING;?>" onClick="changeProjType(<?php echo Projects::TYPE_CONSULTING;?>)"><i>Consultancy  </i></span>
 	</div> 	<?php $projects = WidgetProjectRunOut::getProjects(); ?>
 	<div id="widget_projectbudget1" class="bigsize">
 	 	<div class="boardrow color333">
 			<div class="width122 inline-block">	<span class="width122"><b>Customers</b></span></div>
		 	<div class="width122 inline-block "><span class="width122"><b>Project</b></span></div>
		 	<div class="width80 inline-block"><span class="width80"><b>Budget</b></span></div>
		 	<div class="width89 inline-block"><span class="width89"><b>Actual MDs</b></span></div>
		 	<div class="widthoffset inline-block"><span class="widthoffset"><b>Total InclOffset</b></span></div>
		 	<div class="width81 inline-block"><span class="width81 orderb"><b>Offset</b></span></div>		 	 
		 	<div class="widthoffset inline-block"><span class="widthoffset"> <b>Offset Requests</b></span></div>
		 <div class="width122 inline-block nobackground "><span class="width122"><b>Potential OverRun</b></span></div>
		</div><?php $projects = WidgetProjectRunOut::getProjects(); foreach ($projects as $project) {
			$id = $project['id']; $cur=$project['currency']; $rate = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$cur'")->queryScalar(); ?>
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
							 </div>
							 <div  style="background-image: url('../images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
						 </div>
					 </div>
			 	</div>
		<div class="width80 inline-block"><span  class="width80"><?php echo Utils::formatNumber($project['budget'],2); ?></span></div>
		<div class="width89 inline-block "><span  class="width89"><?php echo Utils::formatNumber($project['actuals'],2); ?></span></div>
		<div class="widthoffset inline-block "><span  class="widthoffset"><?php echo Utils::formatNumber($project['includingoffset'],2) ;?></span></div>
		<div class="width81 inline-block "><span  class="width81"><?php echo Utils::formatNumber($project['offset'],2) ; ?></span></div>
		<div   class="width122 inline-block" onmouseenter="showoffsetToolTipM(this);" onmouseleave="hideToolTipoffset(this);">
				<div class="first_it panel_container">
			<span  class="widthoffset"><?php echo Utils::formatNumber($project['requests'],2) ; ?> <u class="red">+</u></span>
			<div class="paneloffset" style = "left:80px">
							 <div style="  background-image: url('images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
							 <div style="  background-image: url('images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
							 	<div class="coveroffset" style=" background-image: url('images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u>Reasons:</u></b> ".$project['reasons']; echo "<br/>" ;  ?> </div>
							 </div><div  style="background-image: url('images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
						 </div>
					 </div>
			 	</div>
			 	<div class="width122 inline-block ">
		 		<span  class="width122 <?php if ($project['potential']<0){echo "red" ;} ?>"><?php echo Utils::formatNumber($project['potential'],2); ?></span>
		 	</div></div><?php }?>
	</div>
</div><?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
$(document).ready(function(){$('.summary').hide();var id_type=100;$('#alltypes').addClass("colorRed")});function changeProjType(type){$.ajax({type:"POST",data:{'type1':type},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ProjectRunOut');?>",dataType:"json",success:function(data){if(data){$('.sear').addClass('hidden');$('.status_proj').removeClass("colorRed");$('#widget_projectbudget').html(data.html);$('#widget_projectbudget1').html(data.html);$('.status_proj.'+type).addClass("colorRed");id_type=type}}})}
function changeOrder(order){$.ajax({type:"POST",data:{'order':order},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ProjectRunOut');?>",dataType:"json",success:function(data){if(data){$('#widget_projectbudget').html(data.html);$('#widget_projectbudget1').html(data.html);id_order=order}}})}
function changeOrder2(order){var orderclass=order.split(' ');$('.up.'+orderclass[0]+'.'+orderclass[1]).addClass('hidden')}
function show(element){$('.run_project').removeClass("colorRed");if($('.sear').hasClass('hidden')){$('.sear').removeClass('hidden');$(element).addClass('colorRed')}
else{$('.sear').addClass('hidden')}}
</script>	