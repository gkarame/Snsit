<?php
class EaPaymentTerms extends CActiveRecord{
	public $customErrors = array();
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}
	public function tableName(){
		return 'ea_payment_terms';
	}
	public function rules()	{
		return array(
			array('id_ea, payment_term, milestone', 'required'),
			array('id_ea, milestone', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('payment_term', 'numerical', 'integerOnly'=>true, 'min'=>1, 'max'=>100),
			array('id, id_ea, payment_term, amount, milestone', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'ea' => array(self::BELONGS_TO, 'Eas', 'id_ea'),
			'eMilestone' => array(self::BELONGS_TO, 'Codelkups', 'milestone'),
		);
	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'id_ea' => 'Id Ea',
			'payment_term' => 'Payment Term',
			'amount' => 'Amount',
			'milestone' => 'Milestone',
		);
	}
	public function getMilestoneGrid(){	
		return '<div class="first_it panel_container">'
					.'<div class="term_clip clip">'.$this->eMilestone->codelkup.'</div>'
					.'<u class="red">+</u>'
					.'<div class="panel">'
						.'<div class="phead"></div>'
						.'<div class="pcontent"><div class="cover">'.$this->eMilestone->codelkup.'</div></div>'
						.'<div class="pftr"></div>'
					.'</div>'
				.'</div>';
	}	
	protected function beforeValidate() {
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }
        return $r;
    }    
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }    
	public function search(){
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);	$criteria->compare('id_ea',$this->id_ea);
		$criteria->compare('payment_term',$this->payment_term);	$criteria->compare('amount',$this->amount);
		$criteria->compare('milestone',$this->milestone);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',            
		        'attributes'=>array(
		            'eMilestone.codelkup'=>array(
		                'asc'=>'eMilestone.codelkup',
		                'desc'=>'eMilestone.codelkup DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}
	public static function getAllAutocomplete(){
		$result =  Codelkups::getCodelkupsDropDownOriginals('ea_milestone');
		$milestone = array();	$j = 0;
		foreach ($result as $i=>$res){
			
				$milestone[$j]['label'] = $res;
				$milestone[$j]['id'] = $i;
				$j++;
			
		}
		return $milestone;
	}
	public static function getAllAutocomplete2()
	{
		$result =  Codelkups::getCodelkupsDropDown('ea_milestone');
		$milestone = array();	$j = 0;
		foreach ($result as $i=>$res)	{
			$milestone[$j]['label'] = $res;	$milestone[$j]['id'] = $i;	$j++;
		}
		$milestone = self::removeElementWithValue($milestone, "id", 649);
		return $milestone;
	}		
	public static function  removeElementWithValue($array, $key, $value){
     foreach($array as $subKey => $subArray){
          if($subArray[$key] < $value){
               unset($array[$subKey]);
          }
     }
     return $array;
	}
}?>