<?php $this->beginContent('//layouts/login_screen'); ?>
<?php $css = "";
	if (SiteController::getBackgroundPicture() != null)	{		$css = "style=\"background:url('".SiteController::getBackgroundPicture()."') no-repeat center 53px\"";	}?>
<div id="content_login">	<?php echo $content; ?>	<br clear="all" /></div>
<?php $this->endContent(); ?>