<div class="boardrow color333">
<div class="width122 inline-block">
		 		<span class="width122"><b>Customers</b></span>
		 	</div>

		 	<div class="width122 inline-block ">
		 		<span class="width122"><b>Project</b></span>
		 	</div>
		 	
		 	
		 		<div class="width89 inline-block">
		 		<span class="width89"><b>Budg.MDs</b></span>
		 	</div>
		 	<div class="width89 inline-block">
		 		<span class="width89"><b>Actual MDs</b></span>
		 	</div>
		 	<div class="width81 inline-block">
		 		<span class="width81"><b>Rem.MDs</b></span>
		 	</div>
		 	 <div class="width81 inline-block">
		 		<span class="width81 orderb"><b>Budg.Rate</b></span>
		 	</div>	 	
		 	<div class="width10 inline-block nobackground"><span class="up ActualRate ASC" onClick="changeOrder('ActualRate ASC')"></span><span class="down ActualRate DESC" onClick="changeOrder('ActualRate DESC')"></span></div>
		 	 <div class="width75 inline-block">
		 		<span class="width75"> <b> Actual Rate</b></span>
		 	</div>

		 	 <div class="width89 inline-block">		 	
		 		<span class="width89" style="float:left; "><b>Profit</b></span>
		 	</div>
	 
		 	<div class="width10 inline-block nobackground"><span class="up Overrun ASC" onClick="changeOrder('Overrun ASC')"></span><span class="down Overrun DESC" onClick="changeOrder('Overrun DESC')"></span></div>
		 	
		 	 <div class="width75 inline-block nobackground ">
		 		<span class="width75"><b>OverRun %</b></span>
		 	</div>
		 
</div>	
<?php foreach ($projects as $project){?>
<?php $id =$project['projectid']; 

	$cur=$project['currency'];
	$rate = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$cur'")->queryScalar();

?>
	<div class="boardrow odd-even default" >
	<div class="width122 inline-block">
	 			<a  class = "show_link" href = <?php echo Yii::app()->createUrl("customers/view", array("id" => $project['customer_id']));?>> <span  class="width122"><?php echo substr(Customers::getNameById($project['customer_id']),0,18);?> </span></a>
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
			 	</div>	<div class="width89 inline-block">
		 		<span  class="width89"><?php echo Utils::formatNumber($project['TotalMDs'],2); ?></span>
		 	</div>
		 	<div class="width89 inline-block ">
		 		<span  class="width89"><?php echo Utils::formatNumber($project['ActualMDs'],2); ?></span>
		 	</div>
			<div class="width81 inline-block ">
			 		<span  class="width81"><?php echo Utils::formatNumber($project['RemaingMDs'],2) ;?></span>
			 	</div>
			 	  	<div class="width81 inline-block ">

			 		<span  class="width81"><?php echo Utils::formatNumber($project['BudgetedRate']/$rate,1) ;  echo " $";  ?></span>
			 	</div>
			 	<div class="width89 inline-block ">

			 		<span  class="width89"><?php echo Utils::formatNumber($project['ActualRate']/$rate,1) ; echo " $";  ?></span>
			 	</div>
			 	<div class="width89 inline-block ">
			 		<span  class="width89 "><?php echo Utils::formatNumber((($project['BudgetedRate']/$rate)*$project['RemaingMDs']),1) ; echo " $"; ?></span>
			 	</div>
			 	<div class="width81 inline-block nobackground">
			 		<span  class="width81 <?php if ($project['Overrun']<0){echo "red" ;} ?>"><?php echo Utils::formatNumber($project['Overrun'],2) ;?></span>
			 	</div>
	
	</div>
<?php } ?>