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
	table.first tr td.h3{
		font:14px Calibri !important;
	}
	table.first{
		position:absolute;
		top:0px;
		left:0px;
	}
</style>
<table class="first">
	<tr>
		<td style="vertical-align:top;"><img style="width:225px;margin-top:-10px" src="<?php echo Yii::app()->getBaseUrl().'/images/logo_pdf.png';?>" /></td>
	</tr>
	
	<tr>
		<td class="h3" style="padding:5px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;width:35%;"><b> DATE:</b> <?php echo (isset($model->invoice_date_month) && isset($model->invoice_date_year) ) ? date('d.m.Y',strtotime($model->invoice_date_year.'-'.$model->invoice_date_month.'-30')) : date('d.m.Y')?></td>
		<td class="h3" style="padding:5px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;font-weight:bold;float:right;width:15%;"></td>
		<td class="h3" style="padding:5px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;float:left;width:50%;"><b>Please pay before:</b> <?php echo date('d.m.Y', strtotime($model->invoice_date_year.'-'.$model->invoice_date_month.'-30 +1 month' ))?></td>
	</tr>
	
	<tr>
		<td class="h3"  style="padding:5px;border-bottom:1.5px solid #B20533;font-weight:bold;width:35%;color:#B20533">BILL TO </td>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:15%" > </td>
		<td class="h3" style="padding:5px;border-bottom:1.5px solid #B20533;font-weight:bold;float:right;width:50%;color:#B20533">KINDLY REMIT TO</td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td class="h3" rowspan="3" style="padding:5px;"><?php echo Customers::getNamebyId($model->id_customer); ?> </td>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%"></td>
		<td class="h3" style="padding:5px;">Supply Network Solutions (S.N.S) (Offshore) SAL</td>
	</tr>

	<tr>
		<td class="h3" rowspan="3" style="padding:5px;"><?php echo $model->idEa->billto_address!=null?$model->idEa->billto_address:$model->customer->bill_to_address;?> </td>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%"></td>
		<td class="h3" style="padding:5px;">Bank Name: Bank Of Beirut - Bauchrieh Branch </td>
	</tr>
	<tr>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%">TEL# <?php echo $model->customer->main_phone;?></td>
		<td></td>
		<td class="h3" style="padding:5px;">IBAN#: LB77 0075 0000 0001 1401 6490 5400</td>
	</tr>
	<tr>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%">Attn: <?php echo $model->idEa->billto_contact_person!=null?$model->idEa->billto_contact_person:$model->customer->bill_to_contact_person;?> </td>
		<td></td>
		<td class="h3" style="padding:5px;">Swift: BABELBBE</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td class="h3" style="padding:5px;">Beirut, Sed El Bauchrieh, Electricity Street</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td class="h3" style="padding:5px;">Hachem Center,1st Floor</td>
	</tr>
	
</table>

<br />
<h1 style="color:#B20533;font-weight:bold;font-size:22px;font-family: Calibri">SNS Expense Sheet</h1>
<table class="second">
	<tr class="th">
		<td style="width:6%;border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Item#</h2></td>
		<td style="width:54%;border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Description</h2></td>
		<td style="border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Currency</h2></td>
		<td style="border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Amount</h2></td>
	</tr>
	<tr>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $model->invoice_title; ?> <?php echo (isset($model->id_ea))? "EA#".$model->id_ea:""?><?php ($model->type=='T&M')? echo  " - Billable Hours: ".TandM::gethoursbillableperea($model->id_ea, $model->invoice_date_month, $model->invoice_date_year);?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $model->currency?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($model->net_amount);?></h3></td>
	</tr>
	
	<tr>
		<td style="text-align:center;text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo str_pad(++$i, 3, "0", STR_PAD_LEFT);?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;">V.A.T 0%</h3></td>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $item->currency1->codelkup; ?></h3></td>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber(0);?></h3></td>
				
	</tr>
	<?php if($i<7){?>
		<?php for($j = $i;$j<=7;$j++){?>
			<tr>
				<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"> </h3></td>
				<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"> </h3></td>
				<td style="text-align:center;border-right:1px solid #476976;text-align:center;"> <h3 style="font:15px Calibri;"> </h3></td>
				<td style="text-align:center;border-right:1px solid #476976;text-align:center;"> <h3 style="font:15px Calibri;"> </h3></td>
			</tr> 
		<?php }?>
	<?php } ?>
	<tr class="th">
		<td colspan="2" style="border-top:1px solid #476976;font:14px Calibri;"></td>
		<td colspan="1" style="text-align:right;font-weight:bold;border:1px solid #141414;">
			<h2  style="font:bold 16px Calibri;text-align:center"><?php echo $model->iCurrency->codelkup;?></h2>
		</td>
		<td colspan="1" style="text-align:left;border:1px solid #141414;">
			<h2  style="font:bold 16px Calibri;text-align:center"><?php echo Utils::formatNumber($model->net_amount);?></h2>
		</td>
	</tr>
	<tr>
		<td colspan="4"></td>
	</tr>
	<tr>
		<td colspan="4"></td>
	</tr>
	<tr>
		<td colspan="4" style="font-size:15px;;font-family:Calibri">Notes:</td>
	</tr>
	<tr>
		<td colspan="4" class="h3" style="padding:2px;font-size:15px;;font-family:Calibri">The amount (fee) of the contract is net (excluding) of any and all taxes or government surcharges.</td>
	
	</tr>
	<tr>
		<td colspan="4" class="h3" style="padding:2px;font-size:15px;;font-family:Calibri">In case you need any further information regarding this invoice, please do not hesitate to contact</td>
	</tr>
	<tr>
		<td colspan="4" class="h3" style="padding:0px 2px;font-size:15px;;font-family:Calibri">Mrs. Micheline Daaboul by phone, fax or e-mail to: micheline.daaboul@sns-emea.com</td>
	</tr>
</table>
