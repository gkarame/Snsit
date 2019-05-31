<?php $this->pageTitle=Yii::app()->name . ' - '. Yii::t('translations', 'Login');?><div id="login-content-div">	<div align="center" class="head_login">	<a href="<?php echo Yii::app()->request->baseUrl; ?>">	<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/newLogo1.png" class="loginLogo"> 	</a></div><div class="form loginFormBg marginl0" align="center">
	<?php $form=$this->beginWidget('CActiveForm', array('id'=>'login-form',	'enableClientValidation'=>true,	'clientOptions'=>array(	'validateOnSubmit'=>true,	),	'htmlOptions' => array(		'autocomplete' => 'off'	),)); ?>
		<div class="row fields inputBg" align="center">	<span class="user_icon"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/username-icon-first.png"></span>
			<?php echo $form->textField($model,'username', array('style'=> "display:none", "value" => "false")); ?> 
			<?php echo $form->textField($model,'username', array('placeholder'=> $model->getAttributeLabel('username'),)); ?>
			<?php echo $form->error($model,'username',array()); ?>	</div>
		<div class="row fields inputBg" align="center">	<span class="user_icon" style="margin-top:4px"><img width="20px" src="<?php echo Yii::app()->request->baseUrl; ?>/images/password-icon-first.png"></span>
			<?php echo $form->passwordField($model,'password', array('style'=> "display:none", "value" => "false")); ?>
			<?php echo $form->passwordField($model,'password', array('placeholder'=> $model->getAttributeLabel('password'))); ?>
			<?php echo $form->error($model, 'password',array('style'=>'padding-top: 31px')); ?>	</div>	
		<div class="row fields">	<?php echo CHtml::link(Yii::t('translations', 'Change your password'), array('site/changePassword'), array('id'=>'change-pass'));?>	</div>
		<div class="row fields" style="padding-top:10px">	<?php echo CHtml::link(Yii::t('translations', 'Forgot your password'), array('site/ForgetPassword'), array('id'=>'change-pass'));?>	</div>
		<div class="row input" onclick="CheckOrUncheckInput(this)">	<input type="checkbox" name="LoginForm[rememberMe]" value="remember">	<label><?php echo Yii::t('translations', 'Remember Me')?></label>		</div>				
		<div class="row buttons loginDiv" align="center">	<?php echo CHtml::submitButton(Yii::t('translations', 'Save'), array('name'=>'submit', 'style'=>'display:none')); ?>
			<a href="javascript:void(0);" class="button" onclick="$('#login-form').submit();return false">Login</a>	</div>	<?php $this->endWidget(); ?></div></div>
<script type="text/javascript">
function CheckOrUncheckInput(obj){
	var checkBoxDiv = $(obj);	var input = checkBoxDiv.find('input[type="checkbox"]');	
	if (checkBoxDiv.hasClass('checked')) {	checkBoxDiv.removeClass('checked');		input.prop('checked', false); }else {	checkBoxDiv.addClass('checked');	input.prop('checked', true);	} }
</script>