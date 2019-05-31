<div class="bcontenu">
	<div class="stat_years">
		<span class="status status_proj 10" id="top10" onClick="changeTop(10)"><i>Top 10 /</i></span>
 		<span class="status status_proj 20" onClick="changeTop(20)"><i>Top 20 / </i></span>
 		<span class="status status_proj 30" onClick="changeTop(30)"><i>Top 30 </i></span>
 	</div>
 	<div id="widget_oldestInvoices">
 		<div class="boardrow color333">
			<div class="width165 inline-block">
		 		<span class="width165"><b>Customer</b></span>
		 	</div>
		 	<div class="width165 inline-block ">
		 		<span class="width165"><b>Final Inv#</b></span>
		 	</div>
		 	<div class="width165 inline-block">
		 		<span class="width165"><b>Amount $</b></span>
		 	</div>
		 	<div class="width165 inline-block">
		 		<span class="width165"><b>Age</b></span>
		 	</div>
		 	<div class="width165 inline-block">
		 		<span class="width165"><b>Resource</b></span>
		 	</div>
		</div>	
		<?php $customers = WidgetOldestInvoices::getCustomers(); foreach ($customers as $customer) { ?>
		<div class="boardrow odd-even default" >	
		 	<div class="width165 inline-block">
		 		<span  class="width165"><?php echo $customer['customer_name']; ?></span>
		 	</div>
		 		<div class="width165 inline-block">
		 		<span  class="width165"><?php echo $customer['Invoice_num']; ?></span>
		 	</div>
			<div class="width165 inline-block">
		 		<span  class="width165"><?php echo Utils::formatNumber($customer['amount']); ?></span>
		 	</div>
				<div class="width165 inline-block">
		 		<span  class="width165"><?php echo $customer['Age']; ?></span>
		 	</div>
		 	<div class="width165 inline-block">
		 		<span  class="width165"><?php echo $customer['Resource']; ?></span>
		 	</div>
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
	    'width'=>1000,
	    'height'=>810,
		'resizable'=>false,
	)));?>
<div class="bcontenu projects z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetOldestInvoices::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
	<div class="stat_years">
		<span class="status status_proj 10" id="top10" onClick="changeTop(10)"><i>Top 10 /</i></span>
 		<span class="status status_proj 20" onClick="changeTop(20)"><i>Top 20 / </i></span>
 		<span class="status status_proj 30" onClick="changeTop(30)"><i>Top 30 </i></span>
 	</div>
 	<div id="widget_oldestInvoices1" class="bigsize">
 	 	<div class="boardrow color333">
 		   <div class="width190 inline-block">
		 		<span class="width190"><b>Customer</b></span>
		 	</div>
		 	<div class="width190 inline-block ">
		 		<span class="width190"><b>Final Inv#</b></span>
		 	</div>
		 	<div class="width190 inline-block">
		 		<span class="width190"><b>Amount $</b></span>
		 	</div>
		 	<div class="width190 inline-block">
		 		<span class="width190"><b>Age</b></span>
		 	</div>
		 	<div class="width190 inline-block">
		 		<span class="width190"><b>Resource</b></span>
		 	</div>
		</div>	
		<?php $customers = WidgetOldestInvoices::getCustomers();  foreach ($customers as $customer) { ?>
		<div class="boardrow odd-even default" >
		 <div class="boardrow odd-even default" >	
		 	<div class="width190 inline-block">
		 		<span  class="width190"><?php echo $customer['customer_name']; ?></span>
		 	</div>
		 		<div class="width190 inline-block">
		 		<span  class="width190"><?php echo $customer['Invoice_num']; ?></span>
		 	</div>
				<div class="width190 inline-block">
		 		<span  class="width190"><?php echo Utils::formatNumber($customer['amount']); ?></span>
		 	</div>
				<div class="width190 inline-block">
		 		<span  class="width190"><?php echo $customer['Age']; ?></span>
		 	</div>
		 	<div class="width190 inline-block">
		 		<span  class="width190"><?php echo $customer['Resource']; ?></span>
		 	</div>
		</div>	</div>	<?php }?>
	</div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
$(document).ready(function(){$('#top10').addClass("colorRed")});function changeTop(top){$.ajax({type:"POST",data:{'topcust':top},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/OldestInvoices');?>",dataType:"json",success:function(data){if(data){$('.status_proj').removeClass("colorRed");$('#widget_oldestInvoices').html(data.html);$('#widget_oldestInvoices1').html(data.html);$('.status_proj.'+top).addClass("colorRed");id_type=top}}})}
</script>	