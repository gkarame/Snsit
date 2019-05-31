<div class="bcontenu revenues " >
 	<div id="widget_Resources">
 		<div class="boardrow color333">
		 	<div class="width89 inline-block "><span class="width89"><b>First Name</b></span></div>
		 	<div class="width89 inline-block"><span class="width89"><b>Last Name</b></span></div>
		 	<div class="width89 inline-block"><span class="width89"><b>Total Invoices</b></span></div>
		 	<div class="width89 inline-block"><span class="width89"><b>Amount $</b></span></div>
	</div>	
		<?php $resources = WidgetResourceProductivity::getResources(); foreach ($resources as $resource) { ?>
		<div class="boardrow odd-even default" >
		 	<!-- <div class="width89 inline-block">	<span  class="width89"><?php echo $resource['id']; ?></span></div> -->
		 	<div class="width89 inline-block "><span  class="width89"><?php echo $resource['FirstName']; ?></span></div>
			<div class="width89 inline-block ">	<span  class="width89"><?php echo $resource['LastName'];?></span></div>
			<div class="width89 inline-block "><span  class="width89"><?php echo $resource['total_invoices'] ?></span></div>
			<div class="width89 inline-block "><span  class="width89"><?php echo Utils::formatNumber($resource['AmountInDollars']) ?></span></div>
		</div>	<?php }?>
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
		'resizable'=>false,	)));?>
<div class="bcontenu projects z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetResourceProductivity::getName();?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<?php $resources = WidgetResourceProductivity::getResources(); ?>
 	<div id="widget_projects1" class="bigsize">
 		<div class="boardrow color333">
		 	<div class="width89 inline-block "><span class="width89"><b>First Name</b></span></div>
		 	<div class="width89 inline-block"><span class="width89"><b>Last Name</b></span></div>
		 	<div class="width89 inline-block"><span class="width89"><b>Total Invoices</b></span></div>
		 	<div class="width89 inline-block"><span class="width89"><b>Amount $</b></span></div>		 
		</div>	
		<?php $resources = WidgetResourceProductivity::getResources(); foreach ($resources as $resource) { ?>
		<div class="boardrow odd-even default" >
		 	<!-- <div class="width89 inline-block">	<span  class="width89"><?php echo $resource['id']; ?></span></div> -->
		 	<div class="width89 inline-block "><span  class="width89"><?php echo $resource['FirstName']; ?></span></div>
			<div class="width89 inline-block "><span  class="width89"><?php echo $resource['LastName'];?></span></div>
			<div class="width89 inline-block "><span  class="width89"><?php echo $resource['total_invoices'] ?></span></div>
			<div class="width89 inline-block "><span  class="width89"><?php echo Utils::formatNumber($resource['AmountInDollars'])?></span></div>
		</div>	<?php }?>
	</div>
</div><?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
