<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />	
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" />	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<?php	Yii::app()->clientScript->scriptMap=array(
		    'jquery.js'=>Yii::app()->request->baseUrl.'/scripts/jquery-1.10.2.min.js',   'jquery-ui-1.10.4.min.js'=>Yii::app()->request->baseUrl.'/scripts/jquery-ui-1.10.4.min.js',    'jquery-ui.min.js' => false,'jquery-ui.css' => false);?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/mCustomScrollbar.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css?v=<?php echo time();?>" />
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery-ui-1.9.2.css" />	
	<?php Yii::app()->clientScript->registerScriptFile('jquery.js', CClientScript::POS_HEAD);?>
	<?php Yii::app()->clientScript->registerScriptFile('jquery-ui-1.10.4.min.js', CClientScript::POS_HEAD);?>	
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/cycleAll.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.mCustomScrollbar.concat.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.smooth-scroll.min.js"></script>	
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/jquery.timePicker.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/main.js"></script>
</head>
<body><div class="wrapper page"><div class="headerWrapper"><div class="header">
<span class="logo"><?php echo CHtml::link('<img src="'. Yii::app()->request->baseUrl . '/images/logo.png"  alt="SNS" />', Yii::app()->homeUrl, array('class'=> "sns2"));?></span>
<?php if(Yii::app()->user->isAdmin) {?>
				<div class="mainMenu">
					<?php	$this->widget('MainMenu',array('activeCssClass'=>'active','activateParents'=>true,'lastItemCssClass'=>'last','items'=>MainMenu::getItems(),)); ?></div>	
				<?php }else{?><div class="mainMenu">
					<?php $this->widget('MainMenu',array('activeCssClass'=>'active','activateParents'=>true,'lastItemCssClass'=>'last','items'=>MainMenu::getCustomerItems(),)); ?></div><?php } ?>
				<div class="user">
					<span class="username">Welcome  <?php echo Yii::app()->user->name;?></span>	<?php if(Yii::app()->user->isAdmin) $this->renderPartial('//site/_alerts_list');?>
					<?php echo CHtml::link('', array('/site/logout'), array('class'=>'logout'));?></div></div></div>		
		<div class="container"><div class="nav_menu"><div class="nav_buttons"><div class="prev"></div><div class="next"></div></div>
		<div class="links">
					<?php $this->widget('zii.widgets.CMenu',array('activeCssClass' => 'selected','firstItemCssClass' => 'first','items' =>	$this->action_menu,'itemTemplate' => '{menu}<span class="tabClose"></span>',));?>
					<div class="line"></div></div></div><div class="article"><?php echo $content; ?><br clear="all" />	</div>			
			<span class="pageBottom"></span>	
		<div class="footer">
			<!-- <?php echo Yii::t('translations', 'Copyright');?> &copy; <?php echo date('Y'); ?><br/>
				<?php echo Yii::t('translations', 'All Rights Reserved.');?><br/>	-->
		</div>	</div> <div class="popup_list" style="display:none"></div>
	<div style="display:none" id="confirm_dialog" class="confirm dialog"><h1 class="confirm_title"></h1><div class="confirm_content"></div></div>
	<div style="display:none" id="confirm_dialog_not" class="confirm dialog"><h1 class="confirm_title"></h1><div class="confirm_content"></div></div>
	<div class="popupwidget graph hidden"  id="graph-customer-profile" > <div id='cust_name' style="color: black;"> </div><div class="closepopupwidget" onclick="parentNode.classList.add('hidden');">> </div></div>
	<div class="popupwidgetMaint graph hidden"  id="graph-services" style="height:280px !important;"> <div id='cust_name' style="color: black;"> </div><div class="closepopupwidget" onclick="parentNode.classList.add('hidden');">> </div></div>
	<div class="popupwidgetsrs graph hidden"  id="srs-services" > <div id='cust_name' style="color: black;"> </div><div class="closepopupwidgetsrs" onclick="parentNode.classList.add('hidden');">> </div></div>
	<div class="hidden" id="popupLicenses"> 
			<div class='titre red-bold'>Customers Audited Licences</div> 
			<div class='closeLicences' onclick="parentNode.classList.add('hidden');"> </div>
			<div class='invoicescontainer'><b>Customer </b><b style="margin-left:160px !important;">Licenses</b><br/>
				<?php	$Customers = Customers::getAllSelectVersionCheck();		$limit="";
				echo CHtml::dropDownlist('customers',"",$Customers, array('prompt'=>'Select a customer','class' => 'quota-input status ','style'=>'width:200px !important;height:30px !important;')).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.CHtml::textField('license',$limit,array("value"=>"","style"=>"padding-left:15px !important;height:30px !important;width:40px;text-align:center",'id'=>'license','class' => 'quota-input',"onClick"=>"this.select()")); ?>
			</div> <div class='submitinvoices'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-top:55px;' ,'onclick' => 'updatelicencesCust();return false;','id'=>'createbut')); ?>
		
		</div></div></body>
</html>
<script type="text/javascript">

$("#inv_status").blur(function(){  document.getElementById('inv_status').style.visibility="hidden";});
function updatelicencesCust(){	var customer = $("#customers").val();	var licensesnb = $("#license").val();	
	$.ajax({ type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('Customers/updatelicencesCust');?>",dataType: "json", data: {'customer':customer,'licenses':licensesnb},
		  	success: function(data){
			  	if (data) { if (data.status == 'success') { $('#popupLicenses').addClass('hidden');				  		
					  	}else{ $('#popupLicenses').addClass('hidden');
					  		var action_but = {
										"Ok": {
											click: function() 
											{
												$(this).dialog('close');
											},
											class: 'ok_button'	
										} 
								};							
								tes=1;								 
								custom_alert('ERROR MESSAGE', data.msg, action_but);		
					  	} } } }); }
</script>