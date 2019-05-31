<?php $css = "";
	if (SiteController::getBackgroundPicture() != null){	$css = "style=\"background: #d7d7d7 url('".SiteController::getBackgroundPicture()."') no-repeat center 20px\""; }
	$link = Yii::app()->db->createCommand('SELECT * FROM url_log ORDER BY id desc limit 1')->queryRow();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />	
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico" />	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" />
	<?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery-ui-1.10.2.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/cycleAll.js"></script>
</head><body <?php echo $css;?>>
	<div class="wrapper" >	<div class="container">	
			<?php echo $content; ?>		
			<div class="clear"></div>
		</div>
		<div class="bottom_link">
			<a href = "<?php echo $link['link'];?>">
				<div align="center" style="width: 1500px;text-align:center;margin:0 auto;">	<span class="title_login"><?php echo $link['title'];?> </span>		</div>
			</a></div></div>
<script type="text/javascript">
	$(document).ready(function() {
		$('input').keydown(function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
		    if(key == 13) {        e.preventDefault();    	$(this).closest('form:not(.ajax_submit)').submit();	    }	});	});
	</script>
</body>
</html>
