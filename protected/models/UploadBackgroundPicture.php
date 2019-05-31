<?php 
class UploadBackgroundPicture extends CFormModel{
    public $upload_file;    public $url_link;    public $title;    public $short_description;
    public function rules() {
        return array(
        	array('upload_file', 'required'),
       		array('upload_file', 'file','types'=>'jpg,jpeg,png','maxSize'=>10*1024*1024),
        );
    }
    public function attributeLabels(){
        return array(
            'upload_file'=>Yii::t('translations','Upload File'),
        	'url_link'=>Yii::t('translations','Link URL'),
        );
    }
}?>