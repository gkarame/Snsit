<?php


class CountryPerdiem extends CActiveRecord
{
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    public function tableName(){
        return 'country_perdiem';
    }
    public function rules(){
        return array(
            array('id_country, per_diem', 'required'),
            array('id_country', 'numerical', 'integerOnly'=>true),
            array('id_country, per_diem', 'safe', 'on'=>'search'),
        );
    }
    public function relations(){
        return array(
            'countryData' => array(self::BELONGS_TO, 'Country', 'id_country'),
        );
    }
    public static function getAllRecords(){
        return self::model()->with('countryData')->findAll();
    }
}