<?php
class UserHrDetails extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'user_hr_details';
	}
	public function rules(){
			return array(
			array('id_user', 'required'),
			array('id_user, mof, ssnf,bank_dolphin, aux_dolphin', 'numerical', 'integerOnly'=>true),
			array('evaluation_batch,iban,bank_account', 'length', 'max'=>255),
			array('contract_signed, hr_manual_signed', 'length', 'max'=>1),
			array('employment_date, evaluation_date, contract_expiry_date', 'type', 'type' => 'date', 'message' => '{attribute} is not a valid date!', 'dateFormat' => 'dd/MM/yyyy'),
			array('id, id_user, employment_date, evaluation_date, evaluation_batch, contract_signed, contract_expiry_date, hr_manual_signed, mof, ssnf', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
			'rbank' => array(self::BELONGS_TO, 'Codelkups', 'bank_code'),
			'raux' => array(self::BELONGS_TO, 'Codelkups', 'aux_code'),
		);
	}
	public function beforeSave(){
		if (parent::beforeSave()){
			if (!empty($this->employment_date)){				
				$this->employment_date = DateTime::createFromFormat('d/m/Y', $this->employment_date)->format('Y-m-d H:i:s');
			}else {
				$this->employment_date = null;
			}
			if (!empty($this->evaluation_date)){
				$this->evaluation_date = DateTime::createFromFormat('d/m/Y', $this->evaluation_date)->format('Y-m-d H:i:s');
			}else {
				$this->evaluation_date = null;
			}
			if (!empty($this->contract_expiry_date)){
				$this->contract_expiry_date = DateTime::createFromFormat('d/m/Y', $this->contract_expiry_date)->format('Y-m-d H:i:s');
			}else {
				$this->contract_expiry_date = null;
			}
			return true;
		}
		return false;
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_user' => 'Id User',
			'employment_date' => Yii::t('translations', 'Employment Date'),
			'evaluation_date' => Yii::t('translations', 'Evaluation Date'),
			'evaluation_batch' => Yii::t('translations', 'Evaluation Batch'),
			'contract_signed' => Yii::t('translations', 'Contract Signed'),
			'contract_expiry_date' => Yii::t('translations', 'Contract Expiry Date'),
			'hr_manual_signed' => Yii::t('translations', 'Hr Manual Signed'),
			'mof' => Yii::t('translations', 'MOF'),
			'ssnf' => Yii::t('translations', 'SSNF'),
			'bank_dolphin' => Yii::t('translations', 'Credit Card Bank'),
			'aux_dolphin' => Yii::t('translations', 'Auxiliary'),
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_user',$this->id_user);
		$criteria->compare('employment_date',$this->employment_date,true);	$criteria->compare('evaluation_date',$this->evaluation_date,true);
		$criteria->compare('evaluation_batch',$this->evaluation_batch,true);	$criteria->compare('contract_signed',$this->contract_signed,true);
		$criteria->compare('contract_expiry_date',$this->contract_expiry_date,true);	$criteria->compare('hr_manual_signed',$this->hr_manual_signed,true);
		$criteria->compare('mof',$this->mof);	$criteria->compare('ssnf',$this->ssnf);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}?>