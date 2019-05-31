<?php
class Groups extends CActiveRecord{	
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'groups';
	}
	public function rules(){
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>255),
			array('description', 'safe'),
			array('id, name, description', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'members' => array(self::HAS_MANY, 'UserGroups', 'id_group'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Group',
			'description' => 'Description',
		);
	}
	public function getMembersProvider(){
		$criteria=new CDbCriteria;	
		$criteria->with = 'user';	
		$criteria->compare('id_group',$this->id);
		return new CActiveDataProvider('UserGroups', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort' => array(
            	'defaultOrder'=>'user.username ASC',
            ),
		));
	}	
	public static function getExpensePermissions(){
		$user = Yii::app()->user->id;
		$permissions = Yii::app()->db->createCommand("SELECT `read`, `write`, group_id from permissions p LEFT JOIN user_groups ug ON ug.id_group=p.group_id WHERE ug.id_user={$user} AND p.page='expenses-expenses_approval'")->queryAll();
		$response['write'] = false;
		$response['read_submitted'] = false;
		$response['read_approved'] = false;
		$response['read_paid'] = false;
		foreach ($permissions as $perm){	
			if ($perm['write'] == 1)
				$response['write'] = true;
			if(isset($perm['read'][0])){
			if ($perm['read'][0] == 1)
				$response['read_submitted'] = true;
			}else{$response['read_submitted'] = false;}
			if(isset($perm['read'][1]))
			if ($perm['read'][1] == 1){
				$response['read_approved'] = true;
			}else{$response['read_approved'] = false;}
			if(isset($perm['read'][2]))
			if ($perm['read'][2] == 1){
				$response['read_paid'] = true;
			}else{$response['read_paid'] = false;}
		}
		return $response;
	}	
	public static function getExpenseApprovalStatuses($permissions){
		$statuses = array();
		if ($permissions['read_submitted'] == true)
			$statuses["Submitted"] = "Submitted";
		if ($permissions['read_approved'] == true){
			$statuses["Approved"] = "Approved";
		}
		if ($permissions['read_paid'] == true)
			$statuses["Paid"] = "Paid";
		$statuses["Invoiced"] = "Invoiced";
		$statuses["Transferred"] = "Transferred";
		return $statuses;
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);		$criteria->compare('name',$this->name,true);	$criteria->compare('description', $this->description, true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort' => array(
            	'defaultOrder'=>'name ASC',
            ),
		));
	}
	public static function getGroupDescription($id_group){
		return Yii::app("SELECT DISTINCT description FROM ")->db->createCommand()->queryAll();
	}
}?>