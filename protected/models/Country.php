<?php


class Country extends CActiveRecord
{
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    public function tableName(){
        return 'apps_countries';
    }
    public function rules(){
        return array(
            array('country_code, country_name', 'required'),
            array('country_code, country_name', 'safe', 'on'=>'search'),
        );
    }
    public static function getAllCounters(){
        return self::model()->findAll();
    }
    public static function getCountersDropDownOriginals(){
        return CHtml::listData(self::model()->findAll(), 'id','country_name');
    }
}