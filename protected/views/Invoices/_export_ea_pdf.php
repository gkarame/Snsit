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
	<?php  	$model_ea = Eas::model()->findByPk((int)$model->idEa->id);
	$str= "";
	
	
			 $ext = pathinfo($model_ea->getFile(false,true), PATHINFO_EXTENSION);
			 $ext= strtolower($ext);
			 if($ext == 'jpg'){
			 	imagepng(imagecreatefromjpeg($model_ea->getFile(true,true)), "output".$model->idEa->id.".png");
				$str= Yii::app()->getBaseUrl().'/output'.$model->idEa->id.'.png';
			 }else if($ext == 'jpeg'){
			 	imagepng(imagecreatefromjpeg($model_ea->getFile(true,true)), "output".$model->idEa->id.".png");
				$str= Yii::app()->getBaseUrl().'/output'.$model->idEa->id.'.png';
			 }elseif($ext == 'png'){
				$str= $model_ea->getFile(false,true);
			 }else if($ext == 'gif'){
			 	imagepng(imagecreatefromgif($model_ea->getFile(true,true)), "output.png");
				$str= Yii::app()->getBaseUrl().'/output'.$model->idEa->id.'.png';
			 }
			 

			 
	 ?>
		<td style="vertical-align:top;"><img   style="margin-top:-10px;" src="<?php echo $str; ?>"/></td>
	</tr> 
	
</table>
 
<div style="padding-top:20px;"> </div> 