<?php

class TimesheetSummary extends CFormModel
{	
	public $customer_id, $unit ,$id_customer, $id_project, $user, $id_user, $file, $from, $to,$tm,$id_phase;
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

			array('user, unit, id_project, customer_id, id_phase, id_customer, id_user, file, from, to,billable ,tm', 'safe'),

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
			'to' => 'To',
			'billable' => 'B',
			'tm'=> 'T&M',
			'id_phase' => 'Phase'
		);
	}
}
