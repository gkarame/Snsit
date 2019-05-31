<style>
	div, span, table {
		font:11pt Calibri;
		color:#000;
	}
	table {
		border-collapse:collapse;
		width:100%;
	}
	.first td {
		padding-bottom:5px;
		padding-left:7px;
		padding-right:0;
		padding-top:0;
	}
	.second {
		min-height:300px;
		border:none;
		margin-top:60px;
	}
	.second td, .second th {
		padding:10px 5px;
	}
	.h2 {
		font:bold 12pt Calibri;
	}
	.h3_bold {
		font:bold 11pt Calibri;
	}
	.h3 {
		font:11pt Calibri;
	}
</style>


<?php 	$types = $project['type'];
		$start_date = new DateTime('0000-00-00');
		$end_datee = strtotime('2050-10-10');
		?>
							<?php foreach ($types as $key_type =>$type) {?>
								<?php foreach ($type as $val){?>
								<?php if($start_date > $val['startDate'])
									$start_date = $val['startDate'];?>
								<?php if($end_datee < $val['endDate'])
									$end_datee = $val['endDate'];
							}	}
							?>
<table class="first">
	<tr>
		<td class="h3" style="padding-top:20px;padding-left:20px;border-top:1.5px solid #567885;"><b>Customer:</b> <?php echo Customers::getNameById($key);?></td>
		<td class="h3" style="padding-top:20px;border-top:1.5px solid #567885;"><b>From Date:</b> <?php echo date('d/m/Y',strtotime($start_date)); ?></td>
	</tr>
	<tr>
		<td class="h3" style="padding-bottom:20px;padding-left:20px;border-bottom:1.5px solid #567885;"><b>Project:</b> <?php echo Projects::getNameById($key_project);?></td>
		<td class="h3" style="padding-bottom:20px;border-bottom:1.5px solid #567885;"><b>To Date:</b> <?php echo date('d/m/Y',strtotime($end_datee)); ?></td>
	</tr>
</table>
<table class="second">
	<tr>
		<th class="h2" style="text-align:left;width:8%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Item</th>
		<th class="h2" style="text-align:left;width:37%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Expense Type</th>
		<th class="h2" style="text-align:left;width:25%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885;padding-left:20px">Amount USD (Billable)</th>
		<th class="h2" style="text-align:left;width:30%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;;padding-left:20px">Amount USD (Not Billable)</th>
	</tr>
	<?php 
		$index = 0;
		$total_bill = 0;
		$total_not_bill = 0;
		$total_amount = 0;
		$chart = array();
	?>
	<?php $types = $project['type'];?>
	<?php foreach ($types as $key_type =>$type) {
		$bill = 0;
		$not_bill = 0;
		foreach ($type as $val){
			if($val['billable'] == "Yes")
				$bill += $val['amount'];
			else 
				$not_bill += $val['amount']; 
				
			$total_amount += $val['amount'];
		}
	?>
		<tr>
			<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo ++$index;?></td>
			<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo Codelkups::getCodelkup($key_type);?></td>
			<td class="h3" style="text-align:left;border-right:1px solid #567885;padding-left:20px"><?php echo Utils::formatNumber($bill);?></td>
			<td class="h3" style="text-align:left;padding-left:20px"><?php echo Utils::formatNumber($not_bill);?></td>
		</tr>
		<?php 
			$total_bill += $bill;
			$total_not_bill += $not_bill;
			$chart[$key_type]=$bill;
		?>
		
	<?php }?>
	<script type="text/javascript">
$(document).ready(function() {
			
					      var myChart = new FusionCharts( "../../FusionCharts/Pie3D.swf", 
					                   "myChartId2"+<?php echo $key_project?>, "400", "300", "0" );
					      myChart.setJSONData( {
					    	    "chart":{
					    	    	"exportEnabled":'1',
					    	    	"exportAtClient":'1',
					    	    	"exportHandler":'fcBatchExporter',
					    	        "caption":"<?php echo Customers::getNameById($key)."/".Projects::getNameById($key_project);?>"   
					    	     },   
					    	     "data":[
									<?php foreach ($chart as $type_exp=>$value){?>
										{"label":"<?php echo Codelkups::getCodelkup($type_exp);?>",
						    	        	"value":"<?php echo $value;?>" 
							    	    },
						    	        <?php }?>
					    	       ] 
					    	    }
					    	 );
			
						      myChart.render("graph-"+<?php echo $key_project?>);
						      
						}); 
</script>
	<tr>
		<td style="border-top:1px solid #567885"></td>
		<td class="h2" style="padding:10px 5px;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
			Total Amount: <?php echo Utils::formatNumber($total_amount);?>
		</td>
		<td class="h2" style="padding:10px 5px;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;border-right:1px solid #B20533;padding-left:20px">
			<?php echo Utils::formatNumber($total_bill);?>
		</td>
		<td class="h2" style="font-weight:normal;text-align:left;border-top:1px solid #567885;border-bottom:1px solid #B20533;padding-left:20px">
			<?php echo Utils::formatNumber($total_not_bill);?>
		</td>
	</tr>
</table>
<div class="graph" id="graph-<?php echo $key_project?>" ></div>

