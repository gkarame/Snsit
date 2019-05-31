<?php
class CustomerSatisfaction extends CActiveRecord{
	public $contact_name;
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}
	public function tableName()	{
		return 'customer_satisfaction';
	}
	public function rules()	{
		return array(
			array('task,notes', 'required'),
			array('id_project, id_resc ', 'numerical', 'integerOnly'=>true),
			array('score ', 'numerical', 'integerOnly'=>false),
			array('expected_delivery_date', 'type', 'type' => 'date', 'message' => '{attribute} is not a valid date!', 'dateFormat' => 'dd/MM/yyyy'),
			array('id_project,id_task ', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'cType'=> array(self::BELONGS_TO, 'Codelkups', 'type'),				
			'PrimaryContact' => array(self::BELONGS_TO, 'Users', 'primary_contact'),
			'SecondaryContact' => array(self::BELONGS_TO, 'Users', 'secondary_contact'),
		);
	}	
	public function search(){
		$criteria = new CDbCriteria;
		$criteria->together = true;		
		$criteria->compare('t.id_user', $this->id_user);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),          
		));
	}	
	public function searchSunday(){
		$criteria = new CDbCriteria;
		$criteria->with = array('PrimaryContact', 'SecondaryContact','cType');
		$criteria->together = true;
		$criteria->compare('t.primary_contact', $this->primary_contact);
		$criteria->compare('t.secondary_contact', $this->secondary_contact);
		$criteria->compare('t.type','403');		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
          
		));
	}	
	public static function getCountSRCustomerResource($id_user, $id_customer,$year){
		$result =  Yii::app()->db->createCommand("select  count(distinct sd.id ) from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate<>0 and sd.status='5' and sdc.id_user='".$id_user."' and sdc.`status`='3' and sd.id_customer='".$id_customer."' and YEAR(sd.date)='".$year."' ")->queryScalar();		
		return  $result;
	}
	public static function getTotalSRClosedCustomerSatis($id_user, $id_customer,$year){
		$result =  Yii::app()->db->createCommand("select  count(distinct sd.id ) from support_desk sd , support_desk_comments sdc where sd.id=sdc.id_support_desk and sd.rate>2 and sd.status='5' and sdc.id_user='".$id_user."' and sdc.`status`='3' and sd.id_customer='".$id_customer."' and YEAR(sd.date)='".$year."' ")->queryScalar();		
		return  $result;
	}	
	public static function getTotalSurveyProjectType($id_project, $surv_type){
		$result =  Yii::app()->db->createCommand("
select count(rate) as count, sr.id_project , sent_to as last , sent_to_first_name as first, ss.surveys_submitted_date as response_date , sr.surv_type ,sr.rate   from surveys_results sr , surveys_status ss where sr.rate<>0 and sr.rate>2 and sr.id_project= ss.id_project and ss.surveys_submitted='1' and sr.surv_type=ss.surv_type and sr.id_project='".$id_project."' and sr.surv_type='".$surv_type."'  group by sr.rate ,sr.id_project , sent_to , sent_to_first_name , ss.surveys_submitted_date, sr.surv_type  order by sr.id_project asc , sr.rate desc
")->queryAll();
$total=0;
foreach ($result as $key => $value) {
			$total+=$value['count'];
		}		
		return  $total;
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
				'buttons'=>array(
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
	public function getPrimaryContactGrid()	{
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
	public function getFromDateGrid()	{
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
	public function getToDateGrid()	{
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