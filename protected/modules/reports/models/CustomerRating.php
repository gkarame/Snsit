<?php

class CustomerRating extends CFormModel
{	
	public $name , $sd_no,  $rate , $rate_comment , $file, $assigned_to, $month, $year;
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

			array('name , sd_no , rate , rate_comment, file, assigned_to, month, year', 'safe'),

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				
			'user' => 'User',
			'from' => 'From',
			'to' => 'To'
			
		);
	}
}
