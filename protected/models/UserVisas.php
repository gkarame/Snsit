<?php
class UserVisas extends CActiveRecord{
	public $diff;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'user_visas';
	}
	public function rules(){
		return array(
			array('id_user, type, expiry_date, visa_type', 'required'),
			array('id_user,country', 'numerical',  'integerOnly'=>true),
			array('type, duration_of_stay', 'length', 'max'=>255),
			array('expiry_date', 'type', 'type' => 'date', 'message' => '{attribute} is not a valid date!', 'dateFormat' => 'dd/MM/yyyy'),
			array('visa_type', 'length', 'max'=>10),
			array('notes', 'safe'),
			array('id, id_user, type, expiry_date, visa_type, duration_of_stay, notes', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'user' => array(self::BELONGS_TO, 'Users', 'id_user'),
		);
	}
	public function beforeSave(){
		if (parent::beforeSave()){
			$this->expiry_date = DateTime::createFromFormat('d/m/Y', $this->expiry_date)->format('Y-m-d H:i:s');
			return true;
		}
		return false;
	}
	public function attributeLabels(){
		return array(
			'id' => Yii::t('translations', 'ID'),
			'id_user' => Yii::t('translations', 'Id User'),
			'type' => Yii::t('translations', 'Type'),
			'expiry_date' => Yii::t('translations', 'Expiry Date'),
			'visa_type' => Yii::t('translations', 'Visa Type'),
			'duration_of_stay' => Yii::t('translations', 'Duration Of Stay'),
			'notes' => Yii::t('translations', 'Notes'),
			'exday' => Yii::t('translations', 'exday')
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_user',$this->id_user);
		$criteria->compare('type',$this->type,true);		$criteria->compare('expiry_date',$this->expiry_date,true);
		$criteria->compare('visa_type',$this->visa_type,true);	$criteria->compare('duration_of_stay',$this->duration_of_stay,true);
		$criteria->compare('notes',$this->notes,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function getNotesGrid(){
		if ($this->notes != null)
			return '<div class="first_it panel_container panel_container_expenses" style="color:#990000 !important; text-align:right" onmouseenter="showToolTipExpenses(this);" onmouseleave="hideToolTipExpenses(this);">'.'Notes'
						.'<div class="panel_expenses">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->notes.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
		return false;
	}	
	public static function getVisasExpires(){
		$criteria=new CDbCriteria;		$criteria->with = array('user'); 
		$criteria->select = "t.*, ( t.expiry_date ) AS exday,((t.expiry_date)-(curdate())) AS diff";
		$criteria->Addcondition('(curdate() + 2) <= (t.expiry_date) AND (curdate()) +13 >= (t.expiry_date) AND t.type="visa" and t.id_user in (SELECT id FROM `users` where active=1)');
		$criteria->together = true;
		return new CActiveDataProvider('UserVisas', array(
				'criteria' => $criteria,
				'pagination'=>array(
						'pageSize' => Utils::getPageSize(),
				),
				'sort'=>array(
						'defaultOrder'=>'exday ASC',
	
				),
		));
	}
	public static function getVisasExpired(){
		$criteria=new CDbCriteria;	$criteria->with = array('user'); 
		$criteria->select = "t.*, ( t.expiry_date ) AS exday";
		$criteria->Addcondition('curdate() >= (t.expiry_date) AND t.type="visa" and t.id_user in (SELECT id FROM `users` where active=1)');
		$criteria->together = true;
		return new CActiveDataProvider('UserVisas', array(
				'criteria' => $criteria,
				'pagination'=>array(
						'pageSize' => Utils::getPageSize(),
				),
				'sort'=>array(
						'defaultOrder'=>'exday ASC',
	
				),
		));
	}
	public static function getPassportExpires(){
		$criteria=new CDbCriteria;	$criteria->with = array('user');
		$criteria->select = "t.*, ( t.expiry_date ) AS exday,((t.expiry_date)-(curdate())) AS diff";
		$criteria->Addcondition('(curdate() + 2) <= (t.expiry_date) AND (curdate()) +13 >= (t.expiry_date) AND t.type = "passport"  and t.id_user in (SELECT id FROM `users` where active=1)');
		$criteria->together = true;
		return new CActiveDataProvider('UserVisas', array(
				'criteria' => $criteria,
				'pagination'=>array(
						'pageSize' => Utils::getPageSize(),
				),
				'sort'=>array(
						'defaultOrder'=>'exday ASC',
	
				),
		));
	}
	public static function getCountry($country,$id_visa){
		$all_country = Codelkups::getCodelkupsDropDown('country');
		if(GroupPermissions::checkPermissions('financial-invoices','write')) {
			return CHtml::dropDownlist('assigned_to', $country, $all_country, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeCountry('."value".','. $id_visa.','."2".')',
		    	'style'=>'width:95px;border:none;',
		    	'prompt'=>''
		    ));
	    }else{
	    	return CHtml::dropDownlist('assigned_to', $country, $all_country, array(
		       	'disabled'=>true,
		    	'style'=>'width:95px;border:none;',
		    	'prompt'=>''
		    ));
	    }
	}
} ?>