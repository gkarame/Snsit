<?php

class vacationAudit extends CFormModel
{	
	public $name,$id_user,$file,$delta;
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

			array('name,id_user,file', 'safe'),

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_user' => 'Resource'
			

	
		);
	}

	public static function getAllAutocomplete($active = false)
	{
		$query = "SELECT id, firstname, lastname FROM users";
		if ($active)
		{
			$query .= " WHERE active = 1 order by firstname,lastname";
		}
		$result =  Yii::app()->db->createCommand($query)->queryAll();
		$users = array();
		foreach ($result as $i => $res)
		{
			$users[$i]['label'] = $res['firstname'].'  '.$res['lastname'];
			$users[$i]['id'] = $res['id'];
		}
		return $users;
	}
}
