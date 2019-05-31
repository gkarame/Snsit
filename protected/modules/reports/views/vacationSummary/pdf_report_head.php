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
<table class="first">
	<tr>
		<td style="width:50%;vertical-align:top;" rowspan="8"><img style="width:225px;margin-top:-10px" src="<?php echo Yii::app()->getBaseUrl().'/images/logo_pdf.png';?>" /></td>
		<td class="h3_bold" style="font-size:14pt;padding:7px;border-bottom:1.5px solid #567885;color:#B20533;">NUMBER #</td>
		<td class="h3" style="padding:7px;border-bottom:1.5px solid #567885;"><?php //echo $model->customer->name.'-'.date('Ymd').'-'.$model->ea_number;?></td>
	</tr>
	<tr>
		<td class="h3_bold" style="padding-top:20px">Resource:</td>
		<td class="h3" style="padding-top:20px"><?php echo $_POST['UserPersonalDetails']['id_user'];?></td>
	</tr>
	<tr>
		<td class="h3_bold">Year:</td>
		<td class="h3"><?php echo $_POST['UserPersonalDetails']['years'];?></td>
	</tr>
	<tr>
		<td class="h3_bold" style="padding-bottom:20px;">Branch:</td>
		<td class="h3" style="padding-bottom:20px;"><?php echo$_POST['UserPersonalDetails']['branch']?></td>
	</tr>
</table>

