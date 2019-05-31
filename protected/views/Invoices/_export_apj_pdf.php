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
	table.first tr td.h3,table.firstt tr td.h3{
		font:16px Calibri !important;
	}
	table.first{
		position:absolute;
		top:0px;
		left:0px;
	}
	tabel.firstt tr td.right{
		text-align:right;
	}
</style>
<h2 style="margin-left:35%">TAX INVOICE</h2>
<table class="firstt">
	
	<tr>
		<td colspan="2"><img style="width:200px;margin-top:-10px" src="<?php echo Yii::app()->getBaseUrl().'/images/snsapj_logo.png';?>" /></td>
		<td></td>
		<td style = "color:#696e81;font-size:18px;text-align:right;"><b>INVOICE # APJ - <?php echo $model->snsapj_partner_inv;?></b></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	
	<tr>
		<td style="vertical-align:top;width:50%;" rowspan="8" ></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td class="h3"  style="text-align:right"><b>GST Reg No: 201323119E</b></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	
</table>	
<table class="first">
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr >
		<td class="h3" style="padding:5px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;width:30%;"><b>Date:</b>  <?php echo (isset($model->invoice_date_month) && isset($model->invoice_date_year) ) ? date('t.m.Y',strtotime($model->invoice_date_year.'-'.$model->invoice_date_month.'-01')) : date('d.m.Y')?></td>
		<td class="h3" style="padding:5px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;font-weight:bold;float:right;width:10%;"></td>
		<td class="h3" style="padding:5px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;text-align:right;width:60%;margin-left:20px"><b>Please pay before:</b><?php echo date('t.m.Y', strtotime($model->invoice_date_year.'-'.$model->invoice_date_month.'-01 +1 month' ))?></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td class="h3"  style="padding:5px;border-bottom:1.5px solid #B20533;font-weight:bold;width:30%;color:#B20533">BILL TO </td>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:10%" > </td>
		<td class="h3" style="padding:5px;border-bottom:1.5px solid #B20533;font-weight:bold;float:right;width:60%;color:#B20533">KINDLY REMIT TO</td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td class="h3"  style="padding:5px;">S.N.S.INT SAL (Offshore)</td>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%"></td>
		<td class="h3" style="padding:5px;">SNS APJ PTE. LTD.</td>
	</tr>

	<tr>
		<td class="h3" style="padding:5px;">Beirut, Badaro, Sami El Solh St., Amine Khayat</td>
		<!--<td class="h3"  style="padding:5px;">Bank Audi - Audi Saradar Group</td>-->
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%"></td>
		<td class="h3" style="padding:5px;">Standard Chartered Bank (Singapore) Limited</td>
	</tr>
	
	<tr>
		<td class="h3" style="padding:5px;"></td>
		<!--<td class="h3"  style="padding:5px;">Zouk Branch - Beirut - Lebanon</td>-->
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%"></td>
		<td class="h3" style="padding:5px;">16Raffles Quay,#33-03 Hong Leong Build, Singapore048581</td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"></td>
		<!--<td class="h3" style="padding:5px;">Swift no: AUDBLBBX</td>-->
		<td></td>
		<td class="h3" style="padding:5px;">Swift no: SCBLSG22</td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"></td>
		<!--<td class="h3" style="padding:5px;">Account Number: 901966/461/002/012/02</td>-->
		<td></td>
		<td class="h3" style="padding:5px;"><?php if ($model->currency == 9){echo 'Account# :0106899805'; }else if ($model->currency == 169){echo 'Account#: 0104185864'; }else if ($model->currency == 195){echo 'Account#: 0100232663'; }else{ echo 'Account# :0106899805'; } ?> </td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"></td>
		<td></td>
		<td class="h3" style="padding:5px;">6 Battery Road, #03-0, Singapore 049909</td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"></td>
		<td></td>
		<td class="h3" style="padding:5px;">Bank Code: 7144 </td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"></td>
		<td></td>
		<td class="h3" style="padding:5px;">Bank Branch: 01 </td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"></td>
		<td></td>
		<td class="h3" style="padding:5px;"></td>
	</tr>
</table>

<br />
<table class="second">
	<tr class="th">
		<td style="text-align:center;width:6%;border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Item#</h2></td>
		<td style="text-align:center;width:54%;border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Description</h2></td>
		<td style="text-align:center;border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Currency</h2></td>
		<td style="text-align:center;border-top:1px solid #B20533;border-bottom:1px solid #B20533;border-right:1px solid #476976"><h2 style="font:bold 16px Calibri;">Amount</h2></td>
	</tr>
	<?php $i='000';
		  $sum = 0;
	?>
	<?php foreach ($models as $rez){?>
		<?php if($rez->id_expenses == null)
		{?>
			<tr>
				<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo str_pad(++$i, 3, "0", STR_PAD_LEFT);?></h3></td>
				<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $rez->invoice_title; ?> <?php echo (isset($rez->id_ea))? "EA#".$rez->id_ea:""?><?php echo($model->type=='T&M')?   " - Billable Hours: ".TandM::gethoursbillableperea($model->id_ea, $model->invoice_date_month, $model->invoice_date_year):"";?></h3></td>
				<td style="text-align:center;border-right:1px solid #476976;text-align:center;"><h3 style="font:15px Calibri;"><?php echo Codelkups::getCodelkup($rez->currency)?></h3></td>
				<td style="text-align:center;border-right:1px solid #476976;text-align:center;"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($rez->gross_amount);?></h3></td>
			</tr>
		
		<?php $sum += $rez->gross_amount; ?>
		
		<?php }else{?>
				
					<tr>
						<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo str_pad(++$i, 3, "0", STR_PAD_LEFT);?></h3></td>
						<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $rez->idExpense->user->firstname." ".$rez->idExpense->user->lastname." - "."Travel and Living Expenses - ".' '."Expenses sheet # ".$rez->idExpense->no." - ".date('d/m/Y', strtotime($rez->idExpense->startDate)).' - '.date('d/m/Y', strtotime($rez->idExpense->endDate));?></h3></td>
						<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Codelkups::getCodelkup($rez->currency); ?></h3></td>
						<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($rez->gross_amount);?></h3></td>
						
					</tr>
				<?php $sum += $rez->gross_amount?>
			
		<?php }?>
	<?php }?>
	<?php if (!empty($ids_news)){?>
			<?php foreach ($ids_news as $_id){?>
				<?php $val = Invoices::model()->findByPk((int)$_id);?>
					<tr>
						<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo str_pad(++$i, 3, "0", STR_PAD_LEFT);?></h3></td>
						<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $val->uUser->firstname." ".$val->uUser->lastname." - "."Airfare/Visa and Travel Insurance Expenses ";?></h3></td>
						<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $val->iCurrency->codelkup; ?></h3></td>
						<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($val->gross_amount);?></h3></td>
						
					</tr>
				<?php $sum += $val->gross_amount?>
			<?php }?>
		
	<?php }?>
	<?php ?>
	<tr>
	<?php $gst=0;
		if( Customers::getCountryById($model->id_customer) == 127)
		{
		  $gst=($sum*7)/100; ?>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo str_pad(++$i, 3, "0", STR_PAD_LEFT);?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;">GST @ 7%<br/>(Services Inside Singapore)</h3></td>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $model->iCurrency->codelkup; ?></h3></td>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($gst);?></h3></td>
		<?php } else{?>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo str_pad(++$i, 3, "0", STR_PAD_LEFT);?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;">GST @ 0%<br/>(Services Outside Singapore)</h3></td>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $model->iCurrency->codelkup; ?></h3></td>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($gst);?></h3></td>
		<?php }?>
	</tr>
	<?php if($i<6){?>
		<?php for($j = $i;$j<=6;$j++){?>
			<tr>
				<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"> </h3></td>
				<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"> </h3></td>
				<td style="text-align:center;border-right:1px solid #476976;text-align:center;"> <h3 style="font:15px Calibri;"> </h3></td>
				<td style="text-align:center;border-right:1px solid #476976;text-align:center;"> <h3 style="font:15px Calibri;"> </h3></td>
			</tr> 
		<?php }?>
	<?php } ?>
	<tr class="th">
		<td colspan="2" style="border-top:1px solid #476976;font:14px Calibri;"><b><?php echo Utils::convert_number_to_words(($sum+$gst),$model->iCurrency->codelkup);?></b></td>
		<td colspan="1" style="text-align:center;font-weight:bold;border:1px solid #141414;">
			<h2 style="font:bold 16px Calibri;text-align:center"><?php echo $model->iCurrency->codelkup;?></h2>
		</td>
		<td colspan="1" style="text-align:center;border:1px solid #141414;">
			<h2  style="font:bold 16px Calibri;"><?php echo Utils::formatNumber(($sum+$gst));?></h2>
		</td>
	</tr>
	
	
	<tr>
		<td colspan="4" style="color:#B20533;font-size:15px;;font-family:Calibri">Notes:</td>
	</tr>
	<tr>
		<td colspan="4" class="h3" style="padding:2px;font-size:15px;;font-family:Calibri">The amount (fee) of the contract is net (excluding) of any and all taxes or government surcharges.</td>
	</tr>
	<tr>
		<td colspan="4" class="h3" style="padding:1px;font-size:15px;;font-family:Calibri">Please ensure all Bank Charges are paid by your company, so that the amount we receive reflects the invoice amounts.</td>
	</tr>
	<tr>
		<td colspan="4" class="h3" style="padding:0px 1px;font-size:15px;;font-family:Calibri">In case you need any further information regarding this invoice, please do not hesitate to contact</td>
	</tr>
	<tr>
		<td colspan="4" class="h3" style="padding:0px 1px;font-size:15px;;font-family:Calibri">Mrs. Micheline Daaboul at: micheline.daaboul@sns-emea.com</td>
	</tr>
</table>
<div style="padding-top:20px;"> </div>
