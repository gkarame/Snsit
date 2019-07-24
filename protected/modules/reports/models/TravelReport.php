<?php


class TravelReport extends CFormModel
{
    public $id_user,$branch,$year,$format;

    public function rules()
    {
        return array(
            array('branch,id_user,year,format', 'safe'),

        );
    }

    public function attributeLabels()
    {
        return array(
            'id_user' => 'User',
            'branch' => 'Branch',
            'year' => 'Year',
            'format' => 'Format'
        );
    }


}