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
		font:16px Calibri !important;
	}
	table.first{
		position:absolute;
		top:0px;
		left:0px;
	}
</style>
<table>
	<tr>
		<td style="width:50%;vertical-align:top;" colspan="2"><img style="width:225px;margin-top:-10px" src="<?php echo Yii::app()->getBaseUrl().'/images/logo_pdf.png';?>" /></td>
		<td></td>
		<td style = "color:#696e81;font-size:18px;text-align:right"><b>INVOICE # SNS - <?php echo $model->final_invoice_number;?></b></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td style="vertical-align:top;width:50%;" rowspan="8" ></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td class="h3"  style="text-align:right"><b>V.A.T No. 1320296-601</b></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td class="h3" style="text-align:right"><b>Registration No.1801519</b></td>
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
	
	<tr style="height: 35px;">
		<td class="h3" style="padding:5px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;width:35%;"><b> DATE:</b> <?php echo (isset($model->invoice_date_month) && isset($model->invoice_date_year) ) ? date('t.m.Y',strtotime($model->invoice_date_year.'-'.$model->invoice_date_month.'-01')) : date('d.m.Y')?></td>
		<td class="h3" style="padding:5px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;font-weight:bold;float:right;width:15%;"></td>
		<td class="h3" style="padding:5px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;text-align:right;width:50%;"><b>Please pay before:</b> <?php echo date('t.m.Y', strtotime($model->invoice_date_year.'-'.$model->invoice_date_month.'-01 +1 month' ))?></td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"></td>
		<td></td>
		<td class="h3" style="padding:5px;"></td>
	</tr>
	<tr>
		<td class="h3"  style="padding:5px;border-bottom:1.5px solid #B20533;font-weight:bold;width:35%;color:#B20533">BILL TO </td>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%" > </td>
		<td class="h3" style="padding:5px;border-bottom:1.5px solid #B20533;font-weight:bold;float:right;width:50%;color:#B20533">KINDLY REMIT TO</td>
	</tr>
	<tr>
	
		<td class="h3" style="padding:5px;"><?php echo (Customers::getCustomerRef($model->id_customer))!=null?Customers::getNamebyId(Customers::getCustomerRef($model->id_customer)):Customers::getNamebyId($model->id_customer); ?> </td>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%"></td>
		<td class="h3" style="padding:5px;">Supply Network Solutions (S.N.S) (Offshore) SAL</td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;width:29%"><?php echo $model->idEa->billto_address!=null?$model->idEa->billto_address:$model->customer->bill_to_address; ?></td>
		<td></td>
		<td class="h3" style="padding:5px;">Bank Name: Bank Of Beirut - Bauchrieh Branch</td>
	</tr>
	<tr>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%">TEL# <?php echo $model->customer->main_phone;?></td>
		<td></td>
		<td class="h3" style="padding:5px;"><?php if ($model->currency == 9){echo 'Account# :1140164905400 <br/> IBAN#: LB77 0075 0000 0001 1401 6490 5400'; }else if ($model->currency == 8){echo 'Account# :4040164905400 <br/> IBAN#: LB42 0075 0000 0004 0401 6490 5400'; }else if ($model->currency == 167){echo 'Account# :5040164905400 <br/> IBAN#: LB50 0075 0000 0005 0401 6490 5400'; }else if ($model->currency == 169){echo 'Account#: 2740164905400 <br/> IBAN#: LB51 0075 0000 0002 7401 6490 5400'; }else if ($model->currency == 174){echo 'Account#: 1240164905400 <br/> IBAN#: LB39 0075 0000 0001 2401 6490 5400'; }else{ echo 'Account# :1140164905400 <br/> IBAN#: LB77 0075 0000 0001 1401 6490 5400'; } ?> </td>

	</tr>
	<tr>
		<td style="padding:5px;border-bottom:1.5px solid #fff;width:29%"> Attn: <?php echo $model->idEa->billto_contact_person!=null?$model->idEa->billto_contact_person:$model->customer->bill_to_contact_person;?></td>
		<td></td>
		<td class="h3" style="padding:5px;">Swift: BABELBBE</td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"> <?php echo $model->idEa->customer_lpo!=null?"PO: ".$model->idEa->customer_lpo: (($model->type=='Maintenance' && !empty($model->po))?"PO: ".$model->po:""); ?></td>
		<td></td>
		<td class="h3" style="padding:5px;">Beirut, Sed El Bauchrieh, Electricity Street</td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"></td>
		<td></td>
		<td class="h3" style="padding:5px;">Hachem Center,1st Floor</td>
	</tr>
	<tr>
		<td class="h3" style="padding:5px;"> </td>
		<td></td>
		<td class="h3" style="padding:5px;">Tel: +961 1 884700</td>
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
	
	<?php foreach ($models as $rez)	
	//print_r($rez);exit;
	{?>
		<?php if($rez->id_expenses == null)
		{?>
			<tr style="text-align:center">
				<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo str_pad(++$i, 3, "0", STR_PAD_LEFT);?></h3></td>
				<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $rez->invoice_title; ?>  <?php echo (isset($rez->id_ea))? "EA#".$rez->id_ea:""?><?php echo($model->type=='T&M')?   " - Billable Hours: ".TandM::gethoursbillableperea($model->id_ea, $model->invoice_date_month, $model->invoice_date_year):"";?></h3></td>
				<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Codelkups::getCodelkup($rez->currency)?></h3></td>
				<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($rez->gross_amount);
						$sum += $rez->gross_amount;
				?>
					</h3></td>
			</tr>
			
		<?php }else{?>
				<?php
				//if($rez->eItems != null)
				//{ 
				?>
					<tr>
						<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo str_pad(++$i, 3, "0", STR_PAD_LEFT);?></h3></td>
						<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo $rez->idExpense->user->firstname." ".$rez->idExpense->user->lastname." - "."Travel and Living Expenses - ".' '."Expenses sheet # ".$rez->idExpense->no." - ".date('d/m/Y', strtotime($rez->idExpense->startDate)).' - '.date('d/m/Y', strtotime($rez->idExpense->endDate));?></h3></td>
						<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Codelkups::getCodelkup($rez->currency); ?></h3></td>
						<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Utils::formatNumber($rez->gross_amount);?></h3></td>
						
					</tr>
				<?php $sum += $rez->gross_amount?>
			<?php //}?>
		<?php }?>
	
	<?php }?>



	<?php //Travel Expenses
	 if (!empty($ids_news)){?>
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
	<tr>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo str_pad(++$i, 3, "0", STR_PAD_LEFT);?></h3></td>
		<td style="border-right:1px solid #476976"><h3 style="font:15px Calibri;">V.A.T 0%</h3></td>
		<td style="text-align:center;border-right:1px solid #476976"><h3 style="font:15px Calibri;"><?php echo Codelkups::getCodelkup($rez->currency); ?></h3></td>
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
		<td colspan="2" style="border-top:1px solid #476976;font:14px Calibri;"><b><?php echo Utils::convert_number_to_words($sum,$model->iCurrency->codelkup);?></b></td>
		<td colspan="1" style="text-align:center;font-weight:bold;border:1px solid #141414;">
			<h2  style="font:bold 16px Calibri;"><?php echo $model->iCurrency->codelkup;?></h2>
		</td>
		<td colspan="1" style="text-align:center;border:1px solid #141414;">
			<h2  style="font:bold 16px Calibri;"><?php echo Utils::formatNumber($sum);?></h2>
		</td>
	</tr>
	<tr>
		<td colspan="4" ></td>
	</tr>
	
	<tr>
		<td colspan="3" class="bigger" style="color:#B20533;font-size:15px;;font-family:Calibri">Notes:</td>
			<td style="float:right;"> <img style="width:25%;" src="<?php echo Yii::app()->getBaseUrl().'/images/Micha_Signature.png';?>"/></td>

	</tr>
	<tr>
		<td colspan="4" class="bigger" style="padding:2px;font-size:15px;font-family:Calibri">In case you need any further information regarding this invoice, please do not hesitate to contact</td>
	</tr>
	<tr>
		<td colspan="4" class="bigger" style="padding:0px 2px;font-size:15px;font-family:Calibri">Mrs. Micheline Daaboul at: micheline.daaboul@sns-emea.com</td>
	</tr>

</table>
<div style="padding-top:20px;"> </div>