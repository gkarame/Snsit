<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" />
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	
	
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->
	
	<?php
		Yii::app()->clientScript->scriptMap=array(
		    'jquery.js'=>Yii::app()->request->baseUrl.'/scripts/jquery-1.10.2.min.js',
		    'jquery-ui-1.10.4.min.js'=>Yii::app()->request->baseUrl.'/scripts/jquery-ui-1.10.4.min.js',
		    'jquery-ui.min.js' => false,
		    'jquery-ui.css' => false
		);
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/mCustomScrollbar.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css?v=<?php echo time();?>" />
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui-1.9.2.css" /><!-- For date Picker--> 
	
	<?php Yii::app()->clientScript->registerScriptFile('jquery.js', CClientScript::POS_HEAD);?>
	<?php Yii::app()->clientScript->registerScriptFile('jquery-ui-1.10.4.min.js', CClientScript::POS_HEAD);?>
	
	
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/cycleAll.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.mCustomScrollbar.concat.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.smooth-scroll.min.js"></script>
	<!-- For date Picker-->
	
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.timePicker.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/main.js"></script>
</head>

<body>
	<div class="wrapper page">
		<div class="headerWrapper">
			<div class="header">
				<span class="logo">
					<?php echo CHtml::link('<img src="'. Yii::app()->request->baseUrl . '/images/logo.png"  alt="SNS" />', Yii::app()->homeUrl, array('class'=> "sns2"));?>
				</span>
				
			

				
				<div class="user">
					<span class="username">Welcome  <?php
					 $project=$_GET['p']; 
					 $p=Surveys::decrypt_url($project);
					 $checkprojectstatus = Projects::getProjectStatus($p);

					if($checkprojectstatus=="2"){				
						$surv_type='close';
					}else{				
						$surv_type='intermediate';
					}
		  			echo Projects::getSurveyContactByProject($p,$surv_type); 

		  			?></span>
			
					
				</div>
			</div><!-- end header -->
		</div><!-- end headerWrapper -->
		
		<div style="padding-top:0px; margin-top:0px;" class="container">			
			<!-- page menu -->
		
			<!-- page menu -->
			
			<div class="article">
				<?php echo $content; ?>
				<br clear="all" />
			</div>
			
			<span class="pageBottom"></span>
		</div><!-- end content -->
	
		<div class="footer">
			<!--
				<?php echo Yii::t('translations', 'Copyright');?> &copy; <?php echo date('Y'); ?><br/>
				<?php echo Yii::t('translations', 'All Rights Reserved.');?><br/>
			-->
		</div><!-- footer -->
		
	</div><!-- end wrapper -->
	<div class="popup_list" style="display:none"></div>
	<div style="display:none" id="confirm_dialog" class="confirm dialog"><h1 class="confirm_title"></h1><div class="confirm_content"></div></div>
	<div style="display:none" id="confirm_dialog_not" class="confirm dialog"><h1 class="confirm_title"></h1><div class="confirm_content"></div></div>
	
</body>
</html>
