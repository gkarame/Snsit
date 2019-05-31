<?php
class GroupPermissions extends CActiveRecord{
	public function tableName(){
		return 'permissions';
	}
	public function rules(){
		return array(
			array('group_id, page, read, write', 'required'),
			array('read, write', 'numerical', 'integerOnly'=>true),
			array('group_id', 'length', 'max'=>11),
			array('page', 'length', 'max'=>255),
			array('group_id, page, read, write', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'group_id' => 'Group',
			'page' => 'Page',
			'read' => 'Read',
			'write' => 'Write',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('group_id',$this->group_id,true);
		$criteria->compare('page',$this->page,true);	$criteria->compare('read',$this->read);
		$criteria->compare('write',$this->write);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}
	public static function checknotCSonly()	{
		$id_user = Yii::app()->user->id;
		$result = Yii::app()->db->createCommand("SELECT id_group FROM user_groups where id_user=".$id_user." and id_group !=35")->queryAll();		
		if (!empty($result))		{
			if(sizeof($result)==1){ if ($result[0]['id_group']==9){return false;}else{return true;}}else{return true;}
		}else{ return true;}		
	}	
	public static function checkPermissions($page = '',$type = 'read', $id_user = 0, $multi = NULL){
		if($id_user == 0){
			$id_user = Yii::app()->user->id;
		}		
		if (empty($id_user)){
			Yii::app()->request->redirect(Yii::app()->getBaseUrl(true).'/site/login');
		}		
		$result = Yii::app()->db->createCommand("SELECT permissions.*  FROM permissions LEFT JOIN user_groups ON (permissions.group_id = user_groups.id_group) WHERE user_groups.id_user = $id_user AND permissions.page = '$page' AND (permissions.read LIKE '%1%' OR permissions.write = 1)")->queryAll();
		foreach ($result as $row){
			switch (strlen($row[$type]))
			{
				case 3:
					if ($multi !== NULL){
						if($row[$type][$multi] == 1 || $row[$type][0] == 1 || $row[$type][2] == 1){
							return true;
						}
					}
					elseif($row[$type][0] == 1){
						return true;
					}
					break;				
				default:
					if($row[$type] == 1){
						return true;
					}
					break;
			}
		}
		return false;
	}
}?>
