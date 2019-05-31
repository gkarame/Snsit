<div class="bcontenu revenues " >
 	<div id="widget_Resources">
 		<div class="boardrow color333">
		 	<div class="width90 inline-block ">
		 		<span class="width90"><b>Customer</b></span>
		 	</div>
		 	<div class="width100 inline-block">
		 		<span class="width100"><b>Contract</b></span>
		 	</div>
		 	<div class="width90 inline-block">
		 		<span class="width90"><b>Support Service</b></span>
		 	</div>
		 	<div class="width90 inline-block">
		 		<span class="width90" ><b>Services</b></span>
		 	</div>
		</div>	
		<?php $contracts = WidgetmaintenanceCont::getContracts(); $i=0; 
		foreach ($contracts as $resource) {	if ($i > 3) break;	?>
		<div class="boardrow odd-even default" >
			<div class="width90 inline-block ">
		 		<span  class="width90"><?php echo $resource['name']; ?></span>
		 	</div>
		 	<div class="width100 inline-block ">
			 		<span  class="width100"><?php echo $resource['descr'];?></span>
			 	</div>
			<div class="width90 inline-block ">
			 		<span  class="width90"><?php echo $resource['serv'];?></span>
			 	</div>
			  	<div class="width90 inline-block ">
			 		<span  class="width90" style="cursor:pointer;text-decoration: underline;color:<?php echo Maintenance::GetCrossingContracts($resource['contract_id']);?>;" onClick="showServiceSum(<?php echo $resource['contract_id'];?>);"><?php echo  "View details"; ?></span>
			 	</div>
			</div><?php $i++;}?>
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
	))); ?>
<div class="bcontenu projects z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetmaintenanceCont::getName();?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div> <?php $contracts = WidgetmaintenanceCont::getContracts(); ?>
 	<div id="widget_maint1" class="bigsize">
 		<div class="boardrow color333">
 		 
		 	<div class="width235 inline-block ">
		 		<span class="width235"><b>Customer</b></span>
		 	</div>
		 	<div class="width235 inline-block">
		 		<span class="width235"><b>Contract</b></span>
		 	</div>
		 	<div class="width235 inline-block">
		 		<span class="width235"><b>Support Service</b></span>
		 	</div>
		 	<div class="width235 inline-block">
		 		<span class="width235" ><b>Services</b></span>
		 	</div>
		</div>	
		<?php $contracts = WidgetmaintenanceCont::getContracts(); foreach ($contracts as $resource) { ?>
		<div class="boardrow odd-even default" >
		 	<div class="width235 inline-block ">
		 		<span  class="width235"><?php echo $resource['name']; ?></span>
		 	</div>
		 	<div class="width235 inline-block ">
			 		<span  class="width235"><?php echo $resource['descr'];?></span>
			 	</div>
			<div class="width235 inline-block ">
			 		<span  class="width235"><?php echo $resource['serv'];?></span>
			 	</div>
			  	<div class="width235 inline-block ">
			 		<span  class="width235" style="cursor:pointer;text-decoration: underline;color:<?php echo Maintenance::GetCrossingContracts($resource['contract_id']);?>;" onClick="showServiceSum(<?php echo $resource['contract_id'];?>);"><?php echo "View details"; ?></span>
			 	</div>
		</div> <?php }?>
	</div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
$(document).ready(function(){$('.closepopupwidget').click(function(){$('.popupwidget').addClass('hidden')})});function showServiceSum(id,cust)
{$.ajax({type:"POST",data:{'id':id},url:"<?php echo Yii::app()->createAbsoluteUrl('Maintenance/getServicesGrid');?>",dataType:"json",success:function(data){if(data){$('.popupwidgetMaint').addClass('z-index');$('.popupwidgetMaint').removeClass('hidden');$('.closepopupwidget').removeClass('hidden');$('#graph-services').html(data.servs)}}})}
</script>