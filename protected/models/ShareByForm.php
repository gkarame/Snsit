<?php
class ShareByForm extends CFormModel{
	public $to;	public $subject;	public $header = 'Dear ';	public $body;	public $footer = 'Best Regards,<br />';	public $soa = array();	
	public function __construct(){
		$this->footer .= Yii::app()->user->name.'<br />';	$email= str_replace(" ",".",Yii::app()->user->name);
		$this->footer.='<a href="mailto:'.$email.'">'.$email.'@sns-emea.com</a>';
	}
	public function rules(){
		return array(
			array('to', 'required'),
			array('subject, body, footer, header, soa', 'safe'),
		);
	}
	public function attributeLabels(){
		return array(
			'to' => Yii::t('translations', 'To'),
			'subject' => Yii::t('translations', 'Subject'),
			'body' => Yii::t('translations', 'Body'),
			'header' => Yii::t('translations', 'Header'),
			'footer' => Yii::t('translations', 'Footer'),
			'soa' => Yii::t('translations', 'Include SoA'),
		);
	}
} ?>