<?php $this->pageTitle=Yii::app()->name . ' - '. Yii::t('translations', 'Login'); ?>
<div id="login-content-div" class="reset">	<div align="center" class="head_login">	<h1 class="animated fadeInDown"></h1><h2 class="animated fadeInDown"></h2><h3 class="passChange animated fadeInDown"><?php echo Yii::t('translations', 'Change Your Password');?></h3>
	</div><div class="form changePswBg marginl0" align="center" style="height:254px;margin-top:3px;">
	<?php $form=$this->beginWidget('CActiveForm', array('id'=>'change-pass-form','enableClientValidation'=>true,'clientOptions'=>array(	'validateOnSubmit'=>true,	),	'htmlOptions' => array(	'autocomplete' => 'off'	),	)); ?>	
		<div class="row inputBg" align="center">
			<span class="user_icon"><img  src="<?php echo Yii::app()->request->baseUrl; ?>/images/username-icon-first.png"></span>
			<?php echo $form->textField($model,'username', array('style'=> 'display:none', 'value' => 'false')); ?>
			<?php echo $form->textField($model,'username', array('placeholder'=> $model->getAttributeLabel('username'), 'autocomplete'=>'off')); ?>
			<?php echo $form->error($model,'username',array('style'=>'padding-top: 31px')); ?>
		</div>	<div class="row inputBg" align="center">
			<span class="user_icon" style="margin-top:4px;"><img width="20px" src="<?php echo Yii::app()->request->baseUrl; ?>/images/password-icon-first.png"></span>
			<?php echo $form->textField($model,'old_password', array('style'=> 'display:none', 'value' => 'false')); ?>
			<?php echo $form->passwordField($model,'old_password', array('placeholder'=> $model->getAttributeLabel('old_password'), 'autocomplete'=>'off')); ?>
			<?php echo $form->error($model, 'old_password',array('style'=>'padding-top: 31px')); ?>
		</div>	<div class="row inputBg" align="center">
			<span class="user_icon" style="margin-top:4px;"><img width="20px" src="<?php echo Yii::app()->request->baseUrl; ?>/images/password-icon-first.png"></span>
			<?php echo $form->textField($model,'password', array('style'=> 'display:none', 'value' => 'false')); ?>
			<?php echo $form->passwordField($model,'password', array('placeholder'=> $model->getAttributeLabel('password'), 'autocomplete'=>'off')); ?>
			<?php echo $form->error($model, 'password',array('style'=>'padding-top: 31px')); ?>
		</div>	<div class="row inputBg" align="center" style="margin-bottom: 30px">
			<span class="user_icon" style="margin-top:4px;"><img width="20px" src="<?php echo Yii::app()->request->baseUrl; ?>/images/password-icon-first.png"></span>
			<?php echo $form->textField($model,'repeat_password', array('style'=> 'display:none', 'value' => 'false')); ?>
			<?php echo $form->passwordField($model,'repeat_password', array('placeholder'=> $model->getAttributeLabel('repeat_password'), 'autocomplete'=>'off')); ?>
			<?php echo $form->error($model, 'repeat_password',array('style'=>'padding-top: 31px')); ?>
		</div>	<?php echo CHtml::submitButton(Yii::t('translations', 'Save'), array('name'=>'submit', 'style'=>'display:none')); ?>
		<div class="loginDiv changePass">	<div class="save" onclick="$('#change-pass-form').submit();return false;">Save</div>
			<a href="<?php echo $this->createAbsoluteUrl('site/login')?>" class="cancel">Cancel</a>		</div>	<?php $this->endWidget(); ?>	</div></div>