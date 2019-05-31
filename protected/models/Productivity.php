<?php
class Productivity extends CActiveRecord{
	public $contact_name;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'productivity';
	}
	public function rules(){
		return array(
			array('id_projects,id_task', 'required'),
			array('reason', 'length', 'max'=>500),
			array('id_project,id_task ,id_phase ,id_user', 'numerical', 'integerOnly'=>true),
			array('id_project,id_task ,id_phase ,id_user', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'cType'=> array(self::BELONGS_TO, 'Codelkups', 'type'),				
			'PrimaryContact' => array(self::BELONGS_TO, 'Users', 'primary_contact'),
			'SecondaryContact' => array(self::BELONGS_TO, 'Users', 'secondary_contact'),
		);
	}
	public function search(){
		$criteria = new CDbCriteria;	$criteria->together = true;	$criteria->compare('t.id_user', $this->id_user);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
          
		));
	}	
	public function searchSunday(){
		$criteria = new CDbCriteria;		$criteria->with = array('PrimaryContact', 'SecondaryContact','cType');
		$criteria->together = true;		$criteria->compare('t.primary_contact', $this->primary_contact);
		$criteria->compare('t.secondary_contact', $this->secondary_contact);		$criteria->compare('t.type','403');		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
          
		));
	}	
	public static function getAllActiveUsers(){
		$result =  Yii::app()->db->createCommand('SELECT id, firstname, lastname FROM users WHERE active = 1')->queryAll();		
		$users = array();
		foreach ($result as $i => $res)	{	$users[$res['id']] = $res['firstname'].' '.$res['lastname'];	}
		return $users;
	}
	public static function getColumnsForGrid(){		
		$columns= array(
					array(
						'name' => 'id',
						'header' => 'ID',
						'value' => '$data->id',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column70'), 
						'headerHtmlOptions' => array('class' => 'column70'),
					),					
					array(
						'name' => 'from_date',
						'header' => 'From Date',
						'value' => '$data->from_date',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column200'), 
						'headerHtmlOptions' => array('class' => 'column200'),
					),					
					array(
						'name' => 'to_date',
						'header' => 'To Date',
						'value' => '$data->to_date',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column200'), 
						'headerHtmlOptions' => array('class' => 'column200'),
					),
					array(
						'name' => 'PrimaryContact.firstname'.'PrimaryContact.lastname',
						'header' => 'Primary Contact',
						'value' => 'isset($data->PrimaryContact) ? $data->PrimaryContact->firstname." ".$data->PrimaryContact->lastname : ""',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column200'), 
						'headerHtmlOptions' => array('class' => 'column200'),
					),					
				array(
						'name' => 'SecondaryContact.firstname'.'SecondaryContact.lastname',
						'header' => 'Secondary Contact',
						'value' => 'isset($data->SecondaryContact) ? $data->SecondaryContact->firstname." ".$data->SecondaryContact->lastname : ""',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column200'), 
						'headerHtmlOptions' => array('class' => 'column200'),
					),
					
					
				);		
		$columns[] = array(
				'class'=>'CCustomButtonColumn',
				'template'=>'{update} {delete}',
				'htmlOptions'=>array('class' => 'button-column'),
				'afterDelete'=>'function(link,success,data){ 
										if (success) {
											var response = jQuery.parseJSON(data); 
											// update amounts
						  					$.each(response.amounts, function(i, item) {
						  		    			$("#"+i).html(item);
						  					});
						  					$.fn.yiiGridView.update("terms-grid");
						  				}}',
				'buttons'=>array
	            (
	            	'update' => array(
						'label' => Yii::t('translations', 'Edit'), 
						'imageUrl' => null,
						'url' => 'Yii::app()->createUrl("eas/manageItem", array("id"=>$data->id))',
	            		'options' => array(
	            			'onclick' => 'showItemForm(this);return false;'
	            		),
					),
					'delete' => array(
						'label' => Yii::t('translations', 'Delete'),
						'imageUrl' => null,
						'url' => 'Yii::app()->createUrl("eas/deleteItem", array("id"=>$data->id))',  
	                	'options' => array(
	                		'class' => 'delete',
						),
					),
	            ),
			); 
		return $columns;
	}
	public function getPrimaryContactGrid(){
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->primary_contact.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->primary_contact.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}
	public function getSecondaryContactGrid(){
		return '<div class="first_it panel_container">'
						.'<div class="item_clip clip">'.$this->secondary_contact.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->secondary_contact.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
	}
	public function getFromDateGrid(){
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
	public function getToDateGrid(){
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
}?>