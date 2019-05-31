<style>
	div, span, table {
		font:10pt Calibri;
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
		margin-top:30px;
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
<table class="first">
	<tr>
		<td style="width:50%;vertical-align:top;" rowspan="8"><img style="width:225px;margin-top:-10px" src="<?php echo Yii::app()->getBaseUrl().'/images/logo_pdf.png';?>" /></td>
		<td class="h3_bold" style="font-size:14pt;padding:7px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;color:#B20533;">EA #</td>
		<td class="h3" style="padding:7px;border-top:1.5px solid #567885;border-bottom:1.5px solid #567885;"><?php echo $model->customer->name.'-'.date('Ymd').'-'.$model->ea_number;?></td>
	</tr>
	<tr>
		<td class="h3_bold" style="padding-top:20px; width:180px ">Date</td>
		<td class="h3" style="padding-top:20px"><?php echo Utils::formatDate(substr($model->created, 0, 10));?></td>
	</tr>
	<!-- <tr>
		<td class="h3_bold">Client Code</td>
		<td class="h3"><?php /*echo $model->customer->accounting_code; */?></td>
	</tr> -->
	
	<tr>
		<td class="h3_bold">Client LPO</td>
		<td class="h3"><?php echo $model->customer_lpo;?></td>
	</tr>

	<tr>
		<td class="h3_bold">Client Name</td>
		<td class="h3"><?php echo  $model->customer->name; ?></td>
	</tr>
	<tr>
		<td class="h3_bold" >Client Manager</td>
		<td class="h3" ><?php  if( isset($model->primary_contact_name) && ($model->primary_contact_name <>' ') && ($model->primary_contact_name <>'' ) ) {echo $model->primary_contact_name ; }else {echo $model->customer->primary_contact_name; }?></td>
	</tr>
	<tr>
		<td class="h3_bold">Bill To Contact Person</td>
		<td class="h3" ><?php  if(isset($model->billto_contact_person)&& ($model->billto_contact_person <>' ')&& ($model->billto_contact_person <>'')){ echo $model->billto_contact_person ;}else {echo $model->customer->bill_to_contact_person;}  ?></td>
	</tr>
	<tr>
		<td class="h3_bold" style="padding-bottom:20px;border-bottom:1.5px solid #567885;">Bill To Address</td>
		<td class="h3" style="padding-bottom:20px;border-bottom:1.5px solid #567885;"><?php if(isset($model->billto_address)&& ($model->billto_address <>' ')&& ($model->billto_address <>'')){ echo $model->billto_address ;}else { echo $model->customer->bill_to_address;}?></td>
	</tr>
</table>
<table class="second">
	<tr>
		<th class="h2" style="text-align:center;width:10%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885">Item</th>
		<th class="h2" style="text-align:center;width:56%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885">Description</th>
		
		<th class="h2" style="text-align:center;width:18%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885">Currency</th> 
		<th class="h2" style="text-align:center;width:16%;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;">	<?php if ($model->TM==1 ) { ?> Daily Rate <?php }else{ ?>Amount<?php }?></th>
	</tr>
	<?php 
		$items = $model->eItems;
		$last = count($items);
		$diff = 4 - $last;
		foreach ($items as $k => $item) {
	?>
	<tr>
		<td class="h3" style="text-align:center;border-right:1px solid #567885"><?php echo str_pad($k+1, 2, "0", STR_PAD_LEFT);?></td>
		<td class="h3" style="text-align:left;border-right:1px solid #567885"><?php echo $item->description;?></td>
				
		<td class="h3" style="text-align:center;border-right:1px solid #567885"><?php echo $model->eCurrency->codelkup;?></td>
		<td class="h3" style="text-align:center;"><?php 	 if ($model->TM==1) { echo $item->man_day_rate_n; }else{ echo ($model->category == 25 ) ? Utils::formatNumber($item->amount*$item->man_days) : Utils::formatNumber($item->amount); } ?></td>
	</tr>
	<?php 		
	}
		if (!empty($model->expense) && $model->expense != 'N/A' && $model->expense != 'Actuals') { 
	?>
	<tr>
		<td class="h3" style="text-align:center;border-right:1px solid #567885"><?php echo str_pad($last+1, 3, "0", STR_PAD_LEFT);?></td>
		<td class="h3" style="text-align:left;border-right:1px solid #567885"><?php echo Yii::t("translations", "Travel and Living Expenses");?></td>
		<td class="h3" style="text-align:center;border-right:1px solid #567885"><?php echo $model->eCurrency->codelkup;?></td>
		<td class="h3" style="text-align:center;"><?php echo Utils::formatNumber($model->expense);?></td>
	</tr>
	<?php }?>
	
	<tr>
		<td style="border-bottom:1px solid #567885;border-right:1px solid #567885">&nbsp;</td>
		<td style="border-bottom:1px solid #567885;border-right:1px solid #567885">&nbsp;</td>
		<td style="border-right:1px solid #567885">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<?php if ($model->discount > 0) {?>
	<tr>
		<td colspan="2"></td>
		<td class="h2" style="font-weight:normal;padding:10px 5px;text-align:center;border-top:1px solid #567885;border-bottom:1px solid #567885;border-right:1px solid #567885">
			Total:
		</td>
		<td class="h2" style="font-weight:normal;text-align:center;border-top:1px solid #567885;border-bottom:1px solid #567885;">
			<?php if(!empty($model->expense) && $model->expense != 'N/A' && $model->expense != 'Actuals') { $totexp=$model->expense; }else{$totexp=0;} echo Utils::formatNumber($model->getTotalAmount()+$totexp); echo ' '.$model->eCurrency->codelkup;?>
		</td>
	</tr>
	<tr>
		<td colspan="2"></td>
		<td class="h2" style="font-weight:normal;padding:10px 5px;text-align:center;border-right:1px solid #567885">
			Discount:
		</td>
		<td class="h2" style="font-weight:normal;text-align:center;">
			<?php echo round($model->discount,2). ' %';?>
		</td>
	</tr>
	<?php } if($model->TM!=1){ ?>
	<tr>
		<td colspan="2"></td>
		
		<td class="h2" style="padding:10px 5px;text-align:center;color:#B20533;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885">
			Net Amount :
		</td>
		<td class="h2" style="font-weight:normal;text-align:center;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;">
			<?php echo Utils::formatNumber($model->getNetAmountWithExp());  echo ' '.$model->eCurrency->codelkup;?> 
		</td>
	</tr>
 <?php	if( $model->category==25  || ($model->customization ==1 && $model->getTotalSandU()>0 ) ){  ?>
	<tr>
		<td colspan="2"></td>
		<td class="h2" style="padding:10px 5px;text-align:center;color:#B20533;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885">
		<?php if( $model->category==25){ ?>
			AMC Amount:
			<?php }else{ ?>
			Yearly Customization Support:
			<?php } ?>
		</td>
		<td class="h2" style="font-weight:normal;text-align:center;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;">
			<?php echo Utils::formatNumber($model->getTotalSandU()); echo ' '.$model->eCurrency->codelkup;?> 
		</td>
	</tr>

	<tr>
		<td colspan="2"></td>
		<td class="h2" style="padding:10px 5px;text-align:center;color:#B20533;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885">
			Total Amount:
		</td>
		<td class="h2" style="font-weight:normal;text-align:center;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;">
			<?php echo Utils::formatNumber($model->getNetAmountWithExp()+$model->getTotalSandU()); echo ' '.$model->eCurrency->codelkup;?> 
		</td>
	</tr>
	<?php } ?>
	<?php } else {  ?> 

	<?php	if(($model->customization ==1 && $model->getTotalSandU()>0 ) ){  ?>
	<tr>
		<td colspan="2"></td>
		<td class="h2" style="padding:10px 5px;text-align:center;color:#B20533;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;border-right:1px solid #567885">
		Yearly Customization Support:
		</td>
		<td class="h2" style="font-weight:normal;text-align:center;border-top:1.5px solid #B20533;border-bottom:1.5px solid #B20533;">
			<?php echo Utils::formatNumber($model->getTotalSandU()); echo ' '.$model->eCurrency->codelkup;?> 
		</td>
	</tr>

<?php } ?>
		<tr>
		<td colspan="2"></td>
		<td class="h2" style="padding:10px 5px;text-align:center;color:#B20533;border-top:1.5px solid #B20533;">
			
		</td>
		<td class="h2" style="font-weight:normal;text-align:center;border-top:1.5px solid #B20533;">
			
		</td>
	</tr>
	<?php } ?>
</table>
<div style="width:100%;margin-top:10px;position:relative;">
	<div style="width:70%;float:left;">
	<?php 
		$terms = $model->eTerms;		
		if (count($terms) > 0) {
		?>
			<div style="display:block;min-height:40px !important;">
				<div class="h2" style="clear:right;margin-bottom:5px;color:#B20533;">Payment Terms:</div>
				<?php if($model->TM==1){ ?> 
				<ol>
				<?php foreach ($model->eTerms as $term) { ?>
				<li><div class="h3" style="clear:right;padding:0px;padding-bottom:3px;"><?php if($term->milestone=='1067'){ echo $term->eMilestone->codelkup;}else{echo 'The invoice  will be paid '.$term->eMilestone->codelkup;}?></div></li>
				<?php  } ?>
				</ol>
				 <?php } else { ?>
				<ol>
				<?php foreach ($model->eTerms as $term) { 
					if($term->term_type=="sandu" && $model->getTotalSandU()>0 || empty($term->term_type) ){ ?>
				<li><div class="h3" style="clear:right;padding:0px;padding-bottom:3px;"><?php if($model->category=='25'){ if($term->term_type=="sandu"){ echo "S&U ";}elseif(($model->category == 28 && $model->customization ==1  && $model->getTotalSandU()>0) || ( $model->category ==27 && $model->getTotalSandU()>0 && (($model->template ==6 && Customers::getRegion($model->id_customer) != 59)))) {  echo "Customization Support  "; } else if (($model->category == 28 && $model->customization ==1  && $model->getTotalSandU()>0) || ( $model->category ==27 && $model->getTotalSandU()>0 && ($model->template ==2))) {  echo "Yearly Customization Support "; } else {echo "Licenses ";} ;}else{echo "An ";} ;echo "amount of ". Utils::formatNumber($term->amount) . ' ' . $model->eCurrency->codelkup . ' will be invoiced '.$term->eMilestone->codelkup;?></div></li>
				<?php } } ?>
				</ol>
				<?php } ?>
			</div>
		<?php } ?>
		<?php 
		$notes = $model->getNotes(true);
		if (count($notes) > 0) { ?>
			<div style="display:block;margin-bottom:20px;min-height:40px !important;">
				<div class="h2" style="clear:right;margin-bottom:5px; color:#B20533;">Notes:</div>
				<ol>
				<?php foreach ($notes as $note) { ?>
				<li><div class="h3" style="clear:right;padding:0px;padding-bottom:3px;"><?php echo $note; ?></div></li>
				<?php  }
				if(($model->customization ==1 && $model->getTotalSandU()>0 ))
				{	?>
					<li><div class="h3" style="clear:right;padding:0px;padding-bottom:3px;"><?php echo "The Customization Support % was added based on ".$model->support_percent."% of ".$model->support_amt." ".$model->eCurrency->codelkup." development amount"; ?></div></li>
		<?php	} ?>
				</ol>
			</div>
		<?php } ?>
		
		<br/>
		<span class="h2" style="color:#B20533;">
			Client Signature:
		</span>
	</div>
	<div style="width:20%;float:left;top:70px;position:absolute;right:0;">
		
	</div>
	<br clear="all" />
</div>