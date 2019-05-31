<form id="send-survey-form" method="post" >	<div class="sendsurvey" style="width:300px;"><div class="headli"></div><div class="contentli">
	<fieldset class="shareby_fieldset">	<div class="create">	<div class="title">SEND SURVEY</div>	<div class="row clear" style="text-align:left;" >	<span style="font-family: Arial,Helvetica,sans-serif; font-size:14px;">Email Address:</span> <br /> <br />
				<div class="inputBg_create"><input id="to" class="auto_email ui-autocomplete-input" name="ShareByForm[to]" type="text" autocomplete="off">	</div></div>			
			<div class="row clear"  style="text-align:left;"><span style="font-family: Arial,Helvetica,sans-serif; font-size:14px;">First Name:</span><br /> <br />
				<div class="inputBg_create"><input id="fname" name="ShareByForm[fname]" type="text" autocomplete="off">	</div></div>
			<div class="row clear"  style="text-align:left;"><span style="font-family: Arial,Helvetica,sans-serif; font-size:14px;">Last Name:</span><br /> <br />
				<div class="inputBg_create"><input id="name" name="ShareByForm[name]" type="text" autocomplete="off"></div></div>			
			<div class="row buttons clear">	<a href="#" class="save customSaveBtn ua" onclick="sendSurveyEmail(this);return false;"><?php echo Yii::t('translation', 'SEND');?></a>
			<div class="loader"></div><a href="javascript:void(0);" class="customCancelBtn ua" onclick="$('#offset-reas').fadeOut(100);$('#offset-reas').html('');"><?php echo Yii::t('translation', 'CANCEL');?></a>
			</div></div></fieldset>	</div><div class="ftrli"></div></div></form>

