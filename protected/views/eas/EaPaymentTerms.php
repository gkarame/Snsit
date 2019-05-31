<?php

/**
 * This is the model class for table "ea_payment_terms".
 *
 * The followings are the available columns in table 'ea_payment_terms':
 * @property integer $id
 * @property integer $id_ea
 * @property double $payment_term
 * @property double $amount
 * @property integer $milestone
 *
 * The followings are the available model relations:
 * @property Eas $idEa
 * @property Codelkups $milestone0
 */
class EaPaymentTerms extends CActiveRecord
{
	public $customErrors = array();
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EaPaymentTerms the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ea_payment_terms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_ea, payment_term, milestone', 'required'),
			array('id_ea, milestone', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('payment_term', 'numerical', 'integerOnly'=>true, 'min'=>1, 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, id_ea, payment_term, amount, milestone', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'ea' => array(self::BELONGS_TO, 'Eas', 'id_ea'),
			'eMilestone' => array(self::BELONGS_TO, 'Codelkups', 'milestone'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_ea' => 'Id Ea',
			'payment_term' => 'Payment Term',
			'amount' => 'Amount',
			'milestone' => 'Milestone',
		);
	}

	public function getMilestoneGrid()
	{	
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
	
	protected function beforeValidate() 
	{
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }
        return $r;
    }
    
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
    
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('id_ea',$this->id_ea);
		$criteria->compare('payment_term',$this->payment_term);
		$criteria->compare('amount',$this->amount);
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
	public static function getAllAutocomplete()
	{
		$result =  Codelkups::getCodelkupsDropDown('ea_milestone');
		$milestone = array();
		$j = 0;
		foreach ($result as $i=>$res)
		{
			$milestone[$j]['label'] = $res;
			$milestone[$j]['id'] = $i;
			$j++;
		}
		return $milestone;
	}

	public static function getAllAutocomplete2()
	{
		$result =  Codelkups::getCodelkupsDropDown('ea_milestone');
		$milestone = array();
		$j = 0;
		foreach ($result as $i=>$res)
		{
			$milestone[$j]['label'] = $res;
			$milestone[$j]['id'] = $i;
			$j++;
		}
		//print_r($milestone);exit;
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

}