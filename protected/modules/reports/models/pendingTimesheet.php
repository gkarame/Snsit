<?php

class TimesheetSummary extends CFormModel
{	
	public $customer_id, $id_customer, $id_project, $user, $id_user, $file, $from, $to;
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

			array('user, id_project, customer_id, id_customer, id_user, file, from, to', 'safe'),

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_project' => 'Project',
			'customer_id' => 'Customer',
			'user' => 'User',
			'from' => 'From',
			'to' => 'To'
		);
	}
}
