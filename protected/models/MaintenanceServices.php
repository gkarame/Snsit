<?php
class MaintenanceServices extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'maintenance_services';
	}
	public function rules(){
		return array(
			array('id, id_contract, id_service', 'required'),
			array('access, id_contract, id_service', 'numerical', 'integerOnly'=>true),
			array('limit, availability', 'safe','max'=>20),
			array('id, id_contract, id_service,limit,access,availability', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(		
			'idContract' => array(self::BELONGS_TO, 'Maintenance', 'id_contract'),		
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_contract' => 'Id Contract',
			'id_service' => 'service',
			'limit' => 'limit',
			'availability' => 'availability',
			'access'=>'access',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_contract',$this->id_contract);
		$criteria->compare('id_service',$this->id_service);	$criteria->compare('limit',$this->limit);	$criteria->compare('access',$this->access);
		$criteria->compare('availability',$this->availability);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
public static function findMaintenanceServicesbyContract($id_contract){
		$criteria=new CDbCriteria;	$criteria->compare('id_contract',$id_contract);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getDescriptionName($id_service){		
		$result = Yii::app()->db->createCommand("SELECT service FROM support_services WHERE id='$id_service'")->queryScalar();
		return $result;
	}
	public static function getDescription($id_service, $limit){		
		$result = Yii::app()->db->createCommand("SELECT service FROM support_services WHERE id='$id_service'")->queryScalar();
		if($id_service==4 || $id_service==15){
			$result=$result.' ('.$limit.' installations)';
		}
		return $result;
	}
	public static function getTaskDescription($id_task){		
		$service = Yii::app()->db->createCommand("SELECT id_service FROM maintenance_services WHERE id='$id_task'")->queryScalar();
		$result=self::getDescriptionName($service);	return $result;
	}
	public static function getMaintenanceDescriptionByTask($id_task){		
		$contract = Yii::app()->db->createCommand("SELECT id_contract FROM maintenance_services WHERE id='$id_task'")->queryScalar();
		$result=Maintenance::getMaintenanceDescription($contract);	return $result;
	}
	public static function getLimit1($id, $id_contract , $id_service, $field_type, $limit){	
		$actuals = self::getActual($id, $id_service, $field_type);		
		$e= $limit - $actuals;
		return (float)$e;				
	}
	public static function getLimit($id, $id_service, $field_type, $limit){ 
		$access_val = array(
	        'Yes' => 'Yes',
	        'No' => 'No',
	    );
		switch ($field_type){
			case 1:				
				$actuals = self::getActual($id, $id_service, $field_type);					
				return CHtml::textField("change",$limit,array("style"=>"width:35px;text-align:center",'value' =>"$limit",'class' => 'quota-input',"onClick"=>"this.select()","onkeyup"=>"changeInput(value,'".$id."')"));
				break;
			case 2:
				if($limit=='1'){$limits='No';}else{$limits='Yes';}	
				return CHtml::dropDownlist('assigned_to', $limits, $access_val, array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInput2('."value".','.$id.')',
		    	'style'=>'border:none;',		    	
		    	));
				break;
			case 3:				
				return 'Unlimited';
				break;
				}
	}
	public static function getRenovationDate($id_task){		
		$contractdetail = Yii::app()->db->createCommand("SELECT starting_date, frequency FROM `maintenance` where id_maintenance= (SELECT id_contract FROM maintenance_services WHERE id='$id_task' limit 1)")->queryRow();
		
		if(!empty($contractdetail['starting_date']) && !empty($contractdetail['frequency']))
		{
			$f="";
			/*switch ($contractdetail['frequency']) {
				case '80':
					//monthly	
					$f= "+1 months";	
					$minusf= "-1 months";		
					break;
				case '81':
					//quarterly
					$f= "+3 months";
					$minusf= "-3 months";	
					break;
				case '82':
					//biyearly
					$f= "+6 months";
					$minusf= "-6 months";	
					break;
				case '83':
					//yearly
					$f= "+12 months";
					$minusf= "-12 months";	
					break;
				default:
					$f="";
					break;
			}*/
			$f= "+12 months";
			$minusf= "-12 months";	
			if(!empty(trim($f)))
			{
				$date= $contractdetail['starting_date'];
				$today = date("Y-m-d"); 
				while($date<$today)
				{
					$date = date('Y-m-d', strtotime($f, strtotime($date)));
				}
				$date = date('Y-m-d', strtotime($minusf, strtotime($date)));
				return $date;
			}else{
				return '';
			}
			
		}else{
			return '';
		}
	}
	public static function getNextRenovationDate($id_task){		
		$contractdetail = Yii::app()->db->createCommand("SELECT starting_date, frequency FROM `maintenance` where id_maintenance= (SELECT id_contract FROM maintenance_services WHERE id='$id_task' limit 1)")->queryRow();
		
		if(!empty($contractdetail['starting_date']) && !empty($contractdetail['frequency']))
		{
			$f="";
		/*	switch ($contractdetail['frequency']) {
				case '80':
					//monthly	
					$f= "+1 months";	
					$minusf= "-1 months";		
					break;
				case '81':
					//quarterly
					$f= "+3 months";
					$minusf= "-3 months";	
					break;
				case '82':
					//biyearly
					$f= "+6 months";
					$minusf= "-6 months";	
					break;
				case '83':
					//yearly
					$f= "+12 months";
					$minusf= "-12 months";	
					break;
				default:
					$f="";
					break;
			}*/
			$f= "+12 months";
			$minusf= "-12 months";	
			if(!empty(trim($f)))
			{
				$date= $contractdetail['starting_date'];
				$today = date("Y-m-d"); 
				while($date<$today)
				{
					$date = date('Y-m-d', strtotime($f, strtotime($date)));
				}
				return $date;
			}else{
				return '';
			}
			
		}else{
			return '';
		}
	}
	public static function getNextRenovationDatePerContract($id){		
		$contractdetail = Yii::app()->db->createCommand("SELECT starting_date, frequency FROM `maintenance` where id_maintenance=".$id." ")->queryRow();
		
		if(!empty($contractdetail['starting_date']) && !empty($contractdetail['frequency']))
		{
			$f="";
		/*	switch ($contractdetail['frequency']) {
				case '80':
					//monthly	
					$f= "+1 months";	
					$minusf= "-1 months";		
					break;
				case '81':
					//quarterly
					$f= "+3 months";
					$minusf= "-3 months";	
					break;
				case '82':
					//biyearly
					$f= "+6 months";
					$minusf= "-6 months";	
					break;
				case '83':
					//yearly
					$f= "+12 months";
					$minusf= "-12 months";	
					break;
				default:
					$f="";
					break;
			}*/
			$f= "+12 months";
			$minusf= "-12 months";	
			if(!empty(trim($f)))
			{
				$date= $contractdetail['starting_date'];
				$today = date("Y-m-d"); 
				while($date<$today)
				{
					$date = date('Y-m-d', strtotime($f, strtotime($date)));
				}
				return $date;
			}else{
				return '';
			}
			
		}else{
			return '';
		}
	}
	public static function getActual($id, $id_service, $field_type){ 
		$limitdate= MaintenanceServices::getRenovationDate($id);
		$where='';
		
		if ($field_type == '1' && $id_service !='6' && $id_service !='9' && $id_service !='18' && $id_service !='24' && $id_service !='23' && $id_service !='22'){     
			if(!empty($limitdate))
			{
				$where= " and date>='".$limitdate."' ";
			} 		
      		if ($id_service == 4 || $id_service == 15){			

			/*	$getVal = Yii::app()->db->createCommand("SELECT SUM(amount) as tot, TIMESTAMPDIFF(DAY,MIN(date),MAX(date)) as diff  FROM user_time WHERE `default`=2 and id_task=".$id." ".$where." ")->queryRow();
				if ($getVal['diff']>30){
					$margin = self::getmargin($id, $limitdate);
					if ($margin == 0){
						$result= round($getVal['tot']/5);
					}else{
						$result= $margin;
					}
				}else{
					$result= round($getVal['tot']/5);
				}*/
				$contract = Yii::app()->db->createCommand("SELECT id_maintenance,customer FROM `maintenance` where id_maintenance= (SELECT id_contract FROM maintenance_services WHERE id='".$id."' limit 1)")->queryRow();
				if(!empty($contract))
				{
					
					$result = Yii::app()->db->createCommand("SELECT count(1) FROM installation_requests where customer= ".$contract["customer"]." and deadline_date>='".$limitdate."' and status= 3 and project like '".$contract["id_maintenance"]."%m' ")->queryScalar();
				}else{
					return 0;
				}
			}else{
	      		$result = Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time WHERE `default`=2 and id_task='".$id."'  ".$where." ")->queryScalar();
	      	}
			if(empty($result)){	return 0;}else{	return $result; }
		}
		if ($field_type == '1' && $id_service =='9'){
			if(!empty($limitdate))
			{
				$where= " where year(start_date)>= year('".$limitdate."') ";
			}
			$result = Yii::app()->db->createCommand("SELECT count(1) FROM trainings_free_candidates
			where id_customer= (select customer from maintenance where id_maintenance=(select id_contract from maintenance_services where id=".$id.")) and id_training in ( select idtrainings from trainings_new_module ".$where.")")->queryScalar();
			return $result;
	    }
	}
	public static function getmargin($id,$limitdate){
		if(!empty($limitdate))
		{
			$getValues= Yii::app()->db->createCommand("SELECT `date`  FROM user_time WHERE `default`=2 and id_task=".$id."  and date>='".$limitdate."' order by date ASC")->queryAll();
		}else{
			$getValues= Yii::app()->db->createCommand("SELECT `date`  FROM user_time WHERE `default`=2 and id_task=".$id." order by date ASC")->queryAll();
		}
		$lastTime = null;    $diff='';    $count=1; $str='';
	    foreach ($getValues as $getValue) {
	        if ($lastTime == null) {
	            $lastTime = $getValue['date'];
	            continue;
	        }
	        $timeVal = $getValue['date']; 	$diff=(strtotime($timeVal) - strtotime($lastTime)) / (60 * 60 * 24);
			$str.= $timeVal.' - - '.$diff.'<br/> ';
		   	if ($diff>30){ $count++;  }
			$lastTime = $getValue['date'];
	    }
		//print_r($str);exit;
	    return $count;
	}
	public static function getActualExcel($id_maint, $id_service, $field_type){ 
		
		$where='';
		
		$id= Yii::app()->db->createCommand("SELECT id  FROM maintenance_services WHERE id_contract=".$id_maint." and id_service=".$id_service." ")->queryScalar();
		$limitdate= MaintenanceServices::getRenovationDate($id);
		if (($field_type == '1' && $id_service !='6' && $id_service !='9' && $id_service !='18'  && $id_service !='24' && $id_service !='23' && $id_service !='22') || $id_service==7 || $id_service == 19){
			if(!empty($limitdate))
			{
				$where= " and date>='".$limitdate."' ";
			} 
			if ($id_service == 4 || $id_service == 15){				
			/*	$getVal = Yii::app()->db->createCommand("SELECT SUM(amount) as tot, TIMESTAMPDIFF(DAY,MIN(date),MAX(date)) as diff  FROM user_time WHERE `default`=2 and id_task=".$id." ".$where." ")->queryRow();
				if ($getVal['diff']>30){
					$margin = self::getmargin($id,$limitdate);
					if ($margin == 0){
						$result= round($getVal['tot']/5);
					}else{
						$result= $margin;
					}
				}else{
					$result= round($getVal['tot']/5);
				}*/

				$contract = Yii::app()->db->createCommand("SELECT id_maintenance,customer FROM `maintenance` where id_maintenance=".$id_maint)->queryRow();
				if(!empty($contract))
				{					
					$result = Yii::app()->db->createCommand("SELECT count(1) FROM installation_requests where customer= ".$contract["customer"]." and deadline_date>='".$limitdate."' and status= 3 and project like '".$contract["id_maintenance"]."%m' ")->queryScalar();
				}else{
					return 0;
				}

			}else{
				$result = Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time WHERE `default`=2 and id_task='".$id."'  ".$where." ")->queryScalar();
			}      		
      		if(empty($result)){	return 0; }else{	return $result;	}
		}
		if ($field_type == '1' && $id_service =='9'){
		if(!empty($limitdate))
			{
				$where= " where year(start_date)>= year('".$limitdate."') ";
			}
			$result = Yii::app()->db->createCommand("SELECT count(1) FROM trainings_free_candidates
			where id_customer= (select customer from maintenance where id_maintenance=(select id_contract from maintenance_services where id=".$id.")) and id_training in ( select idtrainings from trainings_new_module ".$where.")")->queryScalar();
			return $result;
	    }
	}
	public static function getSupportServices(){			
		$result = Yii::app()->db->createCommand("SELECT id , service FROM support_services WHERE type='501' ")->queryAll();
        return CHtml::listData($result, 'id','service');
	}
	public static function getNameById($id){			
		$result = Yii::app()->db->createCommand("SELECT service FROM support_services WHERE id=".$id." ")->queryScalar();
        return $result;
	}
	public static function getSupportServicesPerMaint($id){			
		$result = Yii::app()->db->createCommand("SELECT id_service, `limit` as quota, field_type FROM maintenance_services WHERE id_contract=".$id." and (access='Yes' or id_service=9)")->queryAll();
        return $result;
	}
	public static function getfieldtypeByService($id_service){			
		return  $result = Yii::app()->db->createCommand("SELECT field_type FROM support_services WHERE id='".$id_service."' ")->queryScalar();
	}
	public static function getaccesstypeByService($id_service){			
		return  $result = Yii::app()->db->createCommand("SELECT access FROM support_services WHERE id='".$id_service."' ")->queryScalar(); 
	}
	public static function getvaluetypeByService($id_service){			
		return  $result = Yii::app()->db->createCommand("SELECT default_value FROM support_services WHERE id='".$id_service."' ")->queryScalar();   
	}
}	
?>