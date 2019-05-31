<div class="user-view mytabs hidden"><?php $this->widget('CCustomJuiTabs', array(  
	   'options'=>array( 'collapsible'=>false, 	'active' =>  'js:configJs.current.activeTab',   ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',	));
	if ($days==0){echo " " ;}else {	echo $days;}
	$dayss=0;
	if($days=='Less than 1 day'){ $dayss =0.5;}
	if($days=='Less than 3 days'){ $dayss = 2;}
	if($days=='Less than 5 days'){ $dayss = 4;}
	if($days=='Less than 10 days'){ $dayss = 9;} 
	if($days=='More than 10 Days'){ $dayss = 11;} 	
	 $issues = SupportDesk::getNewIssues($dayss);?>	
	<div class="projectAlerts paddigl0"><div class="row title">
		<div  style="width:130px;" class="item user inline-block normal uppercase">Customer</div>
		<div style="width:70px;"  class="item inline-block normal uppercase ">Date</div>
		<div style="width:90px;"  class="item inline-block normal uppercase ">Issue status</div>
		<div style="width:120px;" class="item inline-block normal uppercase ">Assigned To</div>
		<div style="width:380px;" class="item inline-block normal uppercase">Description</div>
	</div>	<?php if(sizeof($issues)>0){	foreach($issues as $row){	?>
			<div class="row nobackground">
				<a  href="<?php echo Yii::app()->getBaseUrl(true);?>/supportDesk/update/<?php echo $row['id']; ?>" ><div style="width:130px;" class="item user inline-block normal">
					<?php echo Customers::getNameById($row['id_customer']); echo " - ";  echo $row['id'];  ?>
				</div></a>	<div style="width:70px;" class="item inline-block normal">
					<?php echo Utils::formatDate($row['date'],'Y-m-d H:i:s','d/m/Y');?>
				</div><div  style="width:90px;"  class="item inline-block normal">
					<?php if ($row['status']=='0'){echo "New";}else{echo"In Progress";}?>
				</div><div  style="width:120px;" class="item inline-block normal">
					<?php echo Users::getUsername($row['assigned_to']);?>
				</div><div  style="width:380px;"  class="item inline-block width315">
					<?php echo $row['description'];?></div>	</div>	<?php }	} ?>	</div></div>