<?php

class fbrsList extends CFormModel
{	
	public $description, $billable, $complexity,$task, $project,$product,$customer, $version, $project_manager, $phase, $module, $keywords, $existsfbr, $parent_fbr,$notes,$file,$delta;
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

			array('file,description,task, billable, customer,complexity, project, project_manager, phase, module, keywords, existsfbr, parent_fbr,notes, product, version', 'safe'),

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'project_manager' => 'PM',
			'description' =>'FBR',
			'keywords' => 'Keywords',
			'complexity' =>'Complexity',
			'module' => 'Module',
			'notes' =>'Notes',
			'parent_fbr' => 'Parent FBR',
			'phase' => 'Phase',
			'existsfbr' => 'Exists?'
		);
	}

	public static function getAllAutocompleteUsers($active = false)
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

		public static function getCodelkupsDropDownOriginals($label){
		$criteria = new CDbCriteria(array(
            'with'=>array(
                'codelist'=>array(
	                'alias'=>'cl',
	                'together'=>true
               ),
            ),
            'select' => 'cl.id, cl.codelkup',
            'condition' =>'custom=0 and cl.codelist LIKE :value',
			'params' => array(':value' => '%'.$label.'%'), 
            'order'=>'codelkup ASC',
        ));
        return CHtml::listData(Codelkups::model()->findAll($criteria), 'id','codelkup');
	}
}
