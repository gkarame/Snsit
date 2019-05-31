<?php
class TravelBooking extends CActiveRecord{
	const STATUS_NEW = 0;	const STATUS_INVOICED = 1;	const STATUS_CLOSED = 2;	public $project_name;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'travel_booking';
	}
	public function rules(){
		return array(
			array('origin_country, destination_country, from_date, project_id,id_user', 'required'),
			array('id_user, project_id, id_book', 'numerical', 'integerOnly'=>true),		
			array('notes', 'length', 'max'=>255),
			array('id_user', 'safe', 'on'=>'search'),			
		);
	}
	public function relations(){
		return array(			
			'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),		
			'idProject' => array(self::BELONGS_TO, 'Projects', 'project_id'),	
			'origincountry' => array(self::BELONGS_TO, 'Codelkups', 'origin_country'),
			'destinationcountry' => array(self::BELONGS_TO, 'Codelkups', 'destination_country'),
				
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'origin_country' => 'Origin Country',
			'destination_country' => 'Destination Country',			
			'project_id' => 'Project',		
			'from_date' => 'From Date',
			'to_date' => 'To Date',
		);
	}
	public function search(){	
		$criteria=new CDbCriteria;	$criteria->with = array('idUser');	$criteria->together = true;
		$criteria->addCondition('idUser.firstname LIKE :tn');	$criteria->params[':tn']='%'.substr($this->id_user,0,strrpos($this->id_user," ")).'%';
		$criteria->group='t.id_book';		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
					'pageSize' => Utils::getPageSize(),
			),
			'sort'=>array(
				'defaultOrder' => 'idUser.id ASC',
				'attributes' => array(
					'idUser.firstname'=>array(
						'asc'=>'idUser.firstname',
						'desc'=>'idUser.firstname DESC',
					),
					'idUser.lastname'=>array(
						'asc'=>'idUser.lastname',
						'desc'=>'idUser.lastname DESC',
					),
					
					'origincountry.codelkup'=>array(
						'asc'=>'origincountry.codelkup',
						'desc'=>'origincountry.codelkup DESC',
					),								
					't.notes'=>array(
						'asc'=>'t.notes',
						'desc'=>'t.notes DESC',
					),		
				),
			),
		));
	}
	public static function getUsersAutocomplete(){
		$result =  Yii::app()->db->createCommand('SELECT DISTINCT users.id, users.firstname, users.lastname FROM users ')->queryAll();
		$users = array();
		foreach ($result as $i => $res){	$users[$i]['label'] = $res['firstname'].' '.$res['lastname'];	$users[$i]['id'] = $res['id'];	}
		return $users;
	}	
	public function searchDetails($id_book)	{
		$criteria=new CDbCriteria;	$criteria->compare('t.id_book',$id_book);	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
					'pageSize' => Utils::getPageSize(),
			),
			'sort'=>array(
				'defaultOrder' => 't.id_user ASC',
				'attributes' => array(
					't.id_user'=>array(
						'asc'=>'t.id_user',
						'desc'=>'t.id_user DESC',
					),
					't.destination_country'=>array(
						'asc'=>'t.destination_country',
						'desc'=>'t.destination_country DESC',
					),					
					't.project_id'=>array(
						'asc'=>'t.project_id',
						'desc'=>'t.project_id DESC',
					),								
						
				),
			),
		));
	}
	public static function getColumnsForGrid($id,$can_modify){			
		$columns= array(
					array(
							'name' => 'destination',
							'value' => '$data->getDestinationGrid()',
							'type'=>'raw',
							'htmlOptions' => array('class' => 'column300', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"), 
							'headerHtmlOptions' => array('class' => 'column300'),
					),array(
							'name' => 'Project',
							'value' => '$data->getProjectGrid()',
							'type'=>'raw',
							'htmlOptions' => array('class' => 'column300', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"), 
							'headerHtmlOptions' => array('class' => 'column300'),
					),array(
							'name' => 'From_date',
							'value' => '$data->getFromdateGrid()',
							'type'=>'raw',
							'htmlOptions' => array('class' => 'column300', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"), 
							'headerHtmlOptions' => array('class' => 'column300'),
					),array(
							'name' => 'to_date',
							'value' => '$data->getTodateGrid()',
							'type'=>'raw',
							'htmlOptions' => array('class' => 'column300', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"), 
							'headerHtmlOptions' => array('class' => 'column300'),
					),
				);		
		return $columns;
	}	
	public function getDestinationGrid(){	
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->destinationcountry->codelkup.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->destinationcountry->codelkup.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}	
	public function getProjectGrid(){	
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->idProject->name.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->idProject->name.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}
	public function getFromdateGrid(){	
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->from_date.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->from_date.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}
	public function getTodateGrid(){	
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->to_date.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->to_date.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}	
	public static function getAllCountriesSelect(){
		return CHtml::listData(Codelkups::model()->findAllBySql('SELECT codelkups.id, codelkups.codelkup from codelkups JOIN codelists ON codelists.id = codelkups.id_codelist AND codelists.id_category = 1'),
				'id', 'codelkup');
	}
}?>