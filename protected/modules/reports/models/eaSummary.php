<?php

class eaSummary extends CFormModel
{	
	public $name , $id,$file;
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.

			array('name ,id ,file', 'safe'),

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Customer',
			'eanumber' => 'EA #',
			'eadate' => '# of Licenses',
			'licnumber' => 'EA Approve Date'
	
		);
	}
}
