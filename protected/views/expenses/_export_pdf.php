<style>
	div, span {
		font-family: Helvetica;
		font-size: 13px;
	}
	table {
		border-collapse:collapse;
		width:100%;
		font-family: Helvetica;
		font-size: 13px;
		color:#000000;
	}
	.second {
		min-height:100px;
		border:none;
	}
	
	.second td{
		padding:0px 10px 24px 3px;
		text-align:left;
		font-family:Calibri;
	}
	.second .th td{
		padding:10px 10px 12px 3px;
		text-align:left;
		font-family:Calibri;
	}
	.first td {
		padding:5px;
	}
	.first tr td:first-child{
		text-align:right !important;
	}
	.second tr td h2 {
		color:black;
		font:bold 11px Calibri;
	}
	table.second tr td h3{
		font:14px Calibri !important;
		color:#000 !important;
	}
</style>

<table class="first">
	<tr>
		<td style="width:59%;vertical-align:top;" rowspan="8"><img style="width:225px;margin-top:-15px" src="<?php echo Yii::app()->getBaseUrl().'/images/logo_pdf.png';?>" /></td>
		<td style="border-top:1.5px solid #567885" colspan="2"></td>
	</tr>
	<tr>	
		<td style="font-weight:bold;text-align:right">User Name:</td>
		<td><?php echo $model->user->firstname.' '.$model->user->lastname;?></td>
	</tr>
	
	<tr>
		<td style="font-weight:bold;text-align:right">Sheet Id:</td>
		<td><?php echo $model->no;?></td>
	</tr>
	<tr>
		<td style="font-weight:bold;text-align:right">Customer:</td>
		<td><?php echo $model->customer->name;?></td>
	</tr>
	<tr>
		<td style="font-weight:bold;text-align:right">Project Description:</td>
		<td><?php if($model->training!=1){
		echo $model->project->name;}else{
			echo Yii::app()->db->createCommand("SELECT name FROM `trainings` where id=".$model->project_id." ")->queryScalar();	
		}?></td>
	</tr>
	<tr>
		<td style="font-weight:bold;text-align:right">From Date:</td>
		<td><?php echo date('d/m/Y', strtotime($model->startDate));?></td>
	</tr>
	<tr>
		<td style="font-weight:bold;text-align:right">To Date:</td>
		<td><?php echo date('d/m/Y', strtotime($model->endDate));?></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr> 
	<tr>
		<td style="width:50%;vertical-align:top;" rowspan="8"></td>
		<td style="border-top:1.5px solid #567885" colspan="2"></td>
	</tr>
</table>
<br />
<h1 style="color:#B20533;font-weight:bold;font-size:22px;font-family: Calibri">SNS Expense Sheet</h1>
<table class="second">
	<tr class="th">
		<td style="width:6%;border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Item#</h2></td>
		<td style="width:20%;border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Expense Type</h2></td>
		<td style="width:36%;border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Notes</h2></td>
		<td style="border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Amount</h2></td>
		<td style="border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Currency</h2></td>
		<td style="border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Rate</h2></td>
		<td style="border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Amount USD</h2></td>
		<td style="border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Date</h2></td>
		<td style="border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Billable</h2></td>
		<td style="border-top:1px solid #B20533;border-bottom:1px solid #B20533;"><h2 style="font:bold 16px Calibri;">Payable</h2></td>
	</tr>
	<?php 
		$sum = 0;
		$i = 1;
		foreach ($model->expensesDetails as $k => $item) {
		?>
	<tr>
		<td style="border-right:1px solid #476976;"><h3 style="font:15px Calibri;"><?php echo $i++;?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $item->type0->codelkup;?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $item->notes;?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($item->original_amount);?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $item->currency1->codelkup; ?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $item->currencyRate->rate; ?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($item->amount);?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo date('d/m/Y',strtotime($item->date));?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $item->billable;?></h3></td>
		<td style=""><h3 style="font:15px Calibri;"><?php echo $item->payable;?></h3></td>
	</tr>
	<?php } ?>
	
	<?php $transportation= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type in ('42','44') ")->queryScalar();
			$phone= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type in ('46','1075') ")->queryScalar();
			$meals= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type in ('43') ")->queryScalar();
			$misc= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type not in ('42','44','43','46','1075') ")->queryScalar();
		?> 	
	<tr class="th">
	
		<td  colspan="2" style="border-top:1px solid #476976;"></td>
		<td style="text-align:right;font-weight:bold;border-top:1px solid #476976;border-bottom:1px solid #B20533;border-right:1px solid #B20533">
			<h2  style="font:bold 16px Calibri;text-align:right">TRANSPORTATION (USD)</h2>
		</td>
		<td style="text-align:left;border-top:1px solid #476976;border-bottom:1px solid #8D0719;">
			<h2  style="font:bold 16px Calibri;"><?php echo Utils::formatNumber($transportation);?></h2>
		</td>
		<td   style="border-top:1px solid #476976;"></td> 
		
		<td colspan="3" style="text-align:right;font-weight:bold;border-top:1px solid #476976;border-bottom:1px solid #B20533;border-right:1px solid #B20533">
			<h2  style="font:bold 16px Calibri;text-align:right">Total	Amount	(USD)</h2>
		</td>
		<td colspan="2" style="text-align:left;border-top:1px solid #476976;border-bottom:1px solid #8D0719;">
			<h2  style="font:bold 16px Calibri;"><?php echo Utils::formatNumber($model->total_amount);?></h2>
		</td>
		

	</tr>
	<tr class="th">
		<td colspan="2"><h2 style="font:bold 16px Calibri;">PM Signature</h2></td>
		<td  style="text-align:right;font-weight:bold;border-bottom:1px solid #B20533;border-right:1px solid #B20533">
			<h2  style="font:bold 16px Calibri;text-align:right">PHONE & INTERNET (USD)</h2>
		</td>
		<td   style="text-align:left;border-top:1px solid #B20533;border-bottom:1px solid #B20533;">
			<h2  style="font:bold 16px Calibri;"><?php echo Utils::formatNumber($phone);?></h2>
		</td>
		<td ><h2  style="font:bold 16px Calibri;"></h2></td>								
		<td colspan="3" style="text-align:right;font-weight:bold;border-bottom:1px solid #B20533;border-right:1px solid #B20533">
			<h2  style="font:bold 16px Calibri;text-align:right">Total	Amount	Billable	(USD)</h2>
		</td>
		<td  colspan="2" style="text-align:left;border-top:1px solid #B20533;border-bottom:1px solid #B20533;">
			<h2  style="font:bold 16px Calibri;"><?php echo Utils::formatNumber($model->billable_amount);?></h2>
		</td>
	</tr> 
	<tr class="th">
		<td colspan="2"><h2 style="font:bold 16px Calibri;"></h2></td>
		<td style="text-align:right;font-weight:bold;border-bottom:1px solid #B20533;border-right:1px solid #B20533">
			<h2  style="font:bold 16px Calibri;text-align:right">MEALS (USD)</h2>
		</td>
		<td  style="text-align:left;border-top:1px solid #B20533;border-bottom:1px solid #B20533;">
			<h2  style="font:bold 16px Calibri;"><?php echo Utils::formatNumber($meals);?></h2>
		</td>
		<td ><h2  style="font:bold 16px Calibri;"></h2></td>		
		<td colspan="3" style="text-align:right;font-weight:bold;border-bottom:1px solid #B20533;border-right:1px solid #B20533">
			<h2  style="font:bold 16px Calibri;text-align:right">Total	Amount	Payable	(USD)</h2>
		</td>
		<td  colspan="2" style="text-align:left;border-top:1px solid #B20533;border-bottom:1px solid #B20533;">
			<h2  style="font:bold 16px Calibri;"><?php echo Utils::formatNumber($model->payable_amount);?></h2>
		</td>
	</tr>
	<tr class="th">
		<td colspan="2"><h2 style="font:bold 16px Calibri;">HR Signature</h2></td>
		<td style="text-align:right;font-weight:bold;border-bottom:1px solid #B20533;border-right:1px solid #B20533">
			<h2  style="font:bold 16px Calibri;text-align:right">MISC (USD)</h2>
		</td>
		<td   style="text-align:left;border-top:1px solid #B20533;border-bottom:1px solid #B20533;">
			<h2  style="font:bold 16px Calibri;"><?php echo Utils::formatNumber($misc);?></h2>
		</td>
		<td ><h2  style="font:bold 16px Calibri;"></h2></td>		
		<td colspan="3" style="text-align:right;font-weight:bold;">
			<h2  style="font:bold 16px Calibri;text-align:right"></h2>
		</td>
		<td  colspan="2" style="text-align:left;">
			<h2  style="font:bold 16px Calibri;"></h2>
		</td>
	</tr>
</table>
<br /><br /><br />
