<?php

class TimesheetSnapshot extends CFormModel
{	
	public $id_user, $user, $unit , $file, $from, $to,$tm;
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

			array('id_user, user, unit , file, from, to', 'safe'),

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
