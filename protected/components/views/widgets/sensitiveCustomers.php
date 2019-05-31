<div class="bcontenu revenues " >
 	<div id="widget_Resources">
 		<div class="boardrow color333">
		 	<div class="width96 inline-block ">	<span class="width96"><b>Customer</b></span></div>
		 	<div class="width96 inline-block"><span class="width96"><b>Pending Amount($)</b></span></div>
		 	<div class="width96 inline-block"><span class="width96"><b>Average Age</b></span></div>
		 	<div class="width96 inline-block"><span class="width96"><b>Max Age</b></span></div>
		</div>	<?php $resources = WidgetSensitiveCustomers::getResources(); $resources = array_slice($resources, 0, 4, true);
		foreach ($resources as $resource) {	?>
		<div class="boardrow odd-even default" style="height:30px !important;" >
		 	<div class="width96 inline-block " ><span  class="width96"><?php echo $resource['customer']; ?></span></div>
			<div class="width96 inline-block "><span  class="width96"><?php echo Utils::formatNumber($resource['AmountInDollars']);?></span></div>
			<div class="width96 inline-block "><span  class="width96"><?php echo Utils::formatNumber($resource['avgage'],2);?></span></div>
			<div class="width96 inline-block "><span  class="width96"><?php echo  Utils::formatNumber($resource['maxage']); ?></span></div>
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
			<div class="title"><?php echo  WidgetSensitiveCustomers::getName();?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<?php $resources = WidgetSensitiveCustomers::getResources(); ?>
 	<div id="widget_projects1" class="bigsize">
 		<div class="boardrow color333">
 		<div class="width200 inline-block"><span class="width200"><b>Customer</b></span></div>
		<div class="width200 inline-block"><span class="width200"><b>Pending Amount($)</b></span></div>
		<div class="width200 inline-block"><span class="width200"><b>Average Age</b></span></div>
		<div class="width200 inline-block"><span class="width200"><b>Max Age</b></span></div>		 
		</div>	
		<?php $resources = WidgetSensitiveCustomers::getResources();  foreach ($resources as $resource) {	?>
		<div class="boardrow odd-even default" >
		  	<div class="width200 inline-block "><span  class="width200"><?php echo $resource['customer']; ?></span></div>
			<div class="width200 inline-block "><span  class="width200"><?php echo Utils::formatNumber($resource['AmountInDollars']);?></span></div>
			<div class="width200 inline-block "><span  class="width200"><?php echo Utils::formatNumber($resource['avgage'],2);?></span></div>
			<div class="width200 inline-block "><span  class="width200"><?php echo Utils::formatNumber($resource['maxage']); ?></span></div>
		</div>	<?php }?>
	</div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
