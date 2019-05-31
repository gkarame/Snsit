<?php 
class InstallationRequestsInfo extends CActiveRecord {
	public $customErrors = array();	CONST LANGUAGE_PACK_ARABIC = 0;	CONST LANGUAGE_PACK_Portuguese = 1;	CONST LANGUAGE_PACK_Dutch = 2;
	CONST LANGUAGE_PACK_English = 3;	CONST LANGUAGE_PACK_French = 4;	CONST LANGUAGE_PACK_German = 5;
	CONST LANGUAGE_PACK_Italian = 6;	CONST LANGUAGE_PACK_Japanese = 7;	CONST LANGUAGE_PACK_Polish = 8;
	CONST LANGUAGE_PACK_Russian = 9;	CONST LANGUAGE_PACK_Simplified_Chinese = 10;	CONST LANGUAGE_PACK_Thai = 11;
	CONST LANGUAGE_PACK_Spanish = 12;	CONST LANGUAGE_PACK_Swedish = 13;	CONST LANGUAGE_PACK_Traditional_Chinese = 14;
	CONST LICENSE_SNS = 1;	CONST LICENSE_INFOR = 2;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'installation_requests_info';
	}
	public function rules(){
	return array(
		array('app_url,app_server_hostname,app_username,app_password,db_server_hostname,db_name,
			db_username,db_password,db_local_bkup,license_type,infor_local_bkup', 'required'),
		array('license_type,id_irprd', 'numerical', 'integerOnly'=>true),
		array('id_irprd', 'exist', 'attributeName' => 'id', 'className' => 'InstallationrequestsProducts','allowEmpty'=>false),
		array('app_server_hostname,app_username,app_password,db_server_hostname,db_name,
			db_username,db_password,db_local_bkup,infor_local_bkup','length','max'=>45),
			array('app_url','length','max'=>255),
		array('customer,project, assigned_to, requested_by, status', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'eIRPD' => array(self::BELONGS_TO, 'InstallationrequestsProducts', 'id_irprd'),
			);
	}
	public function attributelabels(){
		return array(
			'app_url' => 'App Url',
			'app_server_hostname' => 'App-Server HostName',
			'app_username' => 'App Username',
			'app_password' => 'App Password',
			'db_server_hostname' => 'DB-Server HostName',
			'db_name' => 'DB Name',
			'db_username' => 'DB UserName',
			'db_password' => 'DB Password',
			'db_local_bkup' => 'DB Backup Path',
			'infor_local_bkup' => 'INFOR Backup Path *',
			'license_type' => 'License Type',		
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->select  = "t.*";		$criteria->with = array('eIRPD');		$criteria->together = true;
		$criteria->compare('t.', $this->app_url, true);		$criteria->compare('t.app_sapp_urlerver_hostname', $this->app_server_hostname, true);
		$criteria->compare('t.app_username', $this->app_username, true);		$criteria->compare('t.app_password', $this->app_password, true);
		$criteria->compare('t.db_server_hostname', $this->db_server_hostname, true);		$criteria->compare('t.db_name', $this->db_name, true);
		$criteria->compare('t.db_username', $this->db_username, true);		$criteria->compare('t.db_password', $this->db_password, true);
		$criteria->compare('t.db_local_bkup', $this->db_local_bkup, true);		$criteria->compare('t.infor_local_bkup', $this->infor_local_bkup, true);
		$criteria->compare('t.license_type', $this->license_type, true);
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id DESC',            
		         'attributes'=>array(
		         	'eAssigned_to.fullname'=>array(
		                'asc'=>'eAssigned_to.firstname',
		                'desc'=>'eAssigned_to.firstname DESC',
		            ),
		            'eIR.id'=>array(
		                'asc'=>'eIR.id',
		                'desc'=>'eIR.id DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}
	public static function getLicenseTypeList(){
		return array(self::LICENSE_SNS => 'SNS',
				self::LICENSE_INFOR => 'INFOR');
	}
	public static function getLicenseTypeLabel($id){
		$list = self::getLicenseTypeList();
		return $list[$id];
	}
}
?>