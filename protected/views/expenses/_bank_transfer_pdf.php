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
		<p>Beirut: <?php echo date('F d,Y')?><p>
	</div>
	<br/>
	<div class="info">
		<h3>Messrs. Bank to Beirut - Bauchrieh Branch</h3>
		<h3>Attn. Mr. Asaad Asmar</h3>
		<span>Fax: 01/871415</span>
		<p>Subject:Transfer to <?php echo $model->user->firstname.' '.$model->user->lastname?> Account Number <?php echo $model->user->userHrDetails->bank_account?></p>
		<br/><br/>
		<p>Kindly transfer from SNS US-dollar current account number 1140164905400 the amount of <b>$ <?php echo Utils::formatNumber($model->payable_amount)?> <span>(<?php echo Utils::convert_number_to_words(bcdiv($model->payable_amount,1,3))?>)</span></b></p>
	</div>
	<div style="margin-bottom:25px;">
		<span>Account Number:  <?php echo $model->user->userHrDetails->bank_account?><br>
		Beneficiary: <?php echo $model->user->firstname.' '.$model->user->lastname?><br>
		IBAN #:  <?php echo $model->user->userHrDetails->iban?>
		
		</span>
		
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
