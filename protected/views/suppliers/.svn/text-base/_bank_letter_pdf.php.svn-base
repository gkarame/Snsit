<style>
	div, span {
		font-family: Helvetica;
		font-size: 13px;
	}
	div.italic{
		
		font-size:15px;
		margin-bottom:50px;
		font-weight:normal;
	}
	div.italic p{
		text-decoration:underline;
	}
	div.body{
		display:block;
		margin: 15px auto;
		width:850px;
		font-family:Arial,​Helvetica,​serif;
	}
	.info h3{
		font-family:​serif !important;
	}
</style>

<div class = "body">
	<div class="italic">
		<br/><br/><br/><br/><br/><br/><br/>
		<p>Beirut: <?php echo date('d/m/Y',strtotime($model->date));?><p>
	</div>
	<br/>
	<div class="info">
		<h3>Messrs. Bank to Beirut - Bauchrieh Branch</h3>
		<h3>Attn. Mr. Asaad Asmar</h3>
		<span>Fax: 01/871415</span>
		<p>Subject: <?php echo $model->description;?></p>
		<br/><br/>
		<p>Kindly transfer from SNS US-dollar current account number 1140164905400 the amount of <b><?php echo Codelkups::getCodelkup($model->idSupplier->currencyId);?> <?php echo Utils::formatNumber($model->amount)?> <span>(<?php echo Utils::convert_number_to_words($model->amount)?>)</b> to the following Bank Account. Transfer to be conducted today.</span></p>
	</div>
	<div style="margin-bottom:25px;">
		<div style="margin-left:150px; width:99px; display:block;float:left">
			<span>Banc:  </span>
		</div>
		<div style="margin-left:50px;display:block;float:left;width:300px">
			<span ><?php echo $model->idSupplier->bank_name;?><br></span>
		</div>
		<div style="margin-left:150px; width:99px; display:block;float:left">
			<span>Swift Code:</span>
		</div>
		<div style="margin-left:50px;display:block;float:left;width:300px">
			<span> <?php echo $model->idSupplier->swift;?><br></span>
		</div>
		<div style="margin-left:150px; width:99px; display:block;float:left">
			<span>IBAN #: </span>
		</div>
		<div style="margin-left:50px;display:block;float:left;width:300px">
			<span> <?php echo $model->idSupplier->iban?><br></span>
		</div>
		<div style="margin-left:150px; width:99px; display:block;float:left;margin-bottom:50px">
			<span>Account Name:</span>
		</div>
		<div style="margin-left:50px;display:block;float:left;width:300px;margin-bottom:50px">
			<span><?php echo $model->idSupplier->account_name?></span>
			
		</div>
	</div>
	<div>
		<span>Thanking you in advance for your swift action.</span>
		<br><br><br><br><br>
		Best regards,
		<br/>
		Mario Ghosn
		<br/>
		General Manager
	</div>
	
	<br /><br /><br />
</div>
