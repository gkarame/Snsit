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
		<?php foreach ($projects as $project) {
			
			$id = $project['id'];


		 $cur=$project['currency'];
		$rate = Yii::app()->db->createCommand("SELECT rate FROM currency_rate WHERE currency='$cur'")->queryScalar();
			?>

		<div class="boardrow odd-even default" >


		 	<div class="width122 inline-block">
		 		<span   href = <?php echo Yii::app()->createUrl("customers/view", array("id" => $project['id']));?>>	<span  class="width122"><?php echo substr(Customers::getNameById($project['customer_id']),0,19);?> </span> </span>
		 	</div>

			
			 <div   class="width122 inline-block" onmouseenter="showToolTipM(this);" onmouseleave="hideToolTipM(this);">
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
			 	<!--<div class="widthoffset inline-block ">

			 		<span  class="widthoffset"><?php // echo Utils::formatNumber($project['requests'],2) ; ?></span>
			 	</div>-->


			 <div   class="width122 inline-block" onmouseenter="showoffsetToolTipM(this);" onmouseleave="hideToolTipoffset(this);">
				<div class="first_it panel_container">
			<span  class="widthoffset"><?php echo Utils::formatNumber($project['requests'],2) ; ?> <u class="red">+</u></span>
					 
						 <div class="paneloffset" style = "left:80px">
							 <div style="  background-image: url('images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
							 <div style="  background-image: url('images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
							 	<div class="coveroffset" style=" background-image: url('images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php echo "<b><u>Reasons:</u></b> ".$project['reasons']; echo "<br/>" ;  ?> </div>
							 </div>
							 <div  style="background-image: url('images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
						 </div>
					 </div>
			 	</div>

			 	<div class="width122 inline-block ">
		 		<span  class="width122 <?php if ($project['potential']<0){echo "red" ;} ?>"><?php echo Utils::formatNumber($project['potential'],2); ?></span>
		 	</div>

			<!-- 	<div class="widthoffset inline-block " onmouseenter="showoffsetToolTipM(this);">
				<span  class="widthoffset"><?php echo Utils::formatNumber($project['requests'],2) ; ?></span>
						 <u class="red">+</u>
						 <div class="paneloffset" style = "left:900px">
							 <div style="  background-image: url('images/hprofil.png');background-repeat: no-repeat;height: 17px;position: relative;width: 187px;"></div>
							 <div style="  background-image: url('images/cprofil.png');background-repeat: repeat-y;color: #555555;font-family: 'Arial';//'Conv_segoeui';font-size: 12px;line-height: 23px;padding-left: 12px;padding-right: 10px;position: relative;width: 163px;">
							 	<div class="coveroffset" style=" background-image: url('images/bgprogil.png'); background-repeat: repeat-x;padding-left: 5px;padding-right: 9px;padding-top: 4px;"><?php print_r($project['reasons']);?> </div>
							 </div>
							 <div  style="background-image: url('images/fprofil.png');background-repeat: no-repeat;color: #333333;font-size: 13px;height: 18px;width: 179px;"></div>			
						 </div>
					 </div>
			 	</div>-->
			 
		</div>
		<?php }?>
