<?php
class EasItems extends CActiveRecord{
	public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'eas_items';
	}
	public function rules()	{
		return array(
			array('id_ea, description', 'required'),
			array('id_ea', 'numerical', 'integerOnly'=>true),
			array('amount, sandu','numerical'),
			array('man_day_rate_n','numerical'),
			array('man_days', 'numerical', 'min'=>0.01),
			array('offshore', 'length', 'max'=>3),
			array('man_days', 'requiredbycategory'),
			array('settings_codelist', 'in', 'range'=>array('product', 'consultancy', 'training_course','service_support','labels','Recruitment'), 'allowEmpty' => true),
			array('settings_codelkup', 'numerical', 'integerOnly'=>true, 'allowEmpty' => true),
			array('id, id_ea, offshore, description, amount, man_days, settings_codelist, settings_codelkup', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'settingsCodelkup' => array(self::BELONGS_TO, 'Codelkups', 'settings_codelkup'),
			'ea' => array(self::BELONGS_TO, 'Eas', 'id_ea'),
			'settingsCodelist' => array(self::BELONGS_TO, 'Codelists', array('settings_codelist'=>'codelist')),
			//'TM'=> array(self::BELONGS_TO, 'Eas', 'TM'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_ea' => 'Ea',
			'description' => 'Description',
			'amount' => 'Amount',
			'man_days' => 'Man Days',
			'man_day_rate' => 'Man Day Rate',
			'settings_codelist' => 'Settings Codelist',
			'settings_codelkup' => 'Settings Codelkup',
		
		);
	}
	public static function getColumnsForGrid($id_category, $can_modify, $customer, $template, $customization){
	$category = (int)$id_category;
		$columns= array(
					array(
							'name' => 'description',
							'value' => '$data->getDescriptionGrid()',
							'type'=>'raw',
							'htmlOptions' => array('class' => 'column300','onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"), 
							'headerHtmlOptions' => array('class' => 'column300'),
					),array(
						'name' => 'settingsCodelkup.codelkup',
						'header' => Eas::getEaItemsCodelist($category),
						'value' => 'isset($data->settingsCodelkup) ? $data->settingsCodelkup->codelkup : ""',
						'visible' => Eas::getEaItemsCodelist($category) ? true : false, 
						'htmlOptions' => array('class' => 'column120'), 
						'headerHtmlOptions' => array('class' => 'column120'),
					),
				);
		$diff_columns = array();
		switch ($category){
			case 25:
				$diff_columns = array(
					array(
						'name' => 'man_days',
						'header' => Eas::getLabelByCategoryField($category, 'man_days'),
						'htmlOptions' => array('class' => 'column100'), 	
						'headerHtmlOptions' => array('class' => 'column100'),
					),
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column120'), 
						'headerHtmlOptions' => array('class' => 'column120'),
					),
					array(
						'name' => 'sandu',
						'header' => Eas::getLabelByCategoryField($category, 'sandu'),
						'value' => 'Utils::formatNumber($data->sandu)',
						'htmlOptions' => array('class' => 'column40'), 
						'headerHtmlOptions' => array('class' => 'column40'),
					),
				);
				break;
				case 454:
				$diff_columns = array(
					array(
						'name' => '',
						'header' => '',
						'value' => '',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),array(
						'name' => '',
						'header' => '',
						'value' => '',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
					
				);
				break;	
				case 496:
				$diff_columns = array(
					array(
						'name' => '',
						'header' => '',
						'value' => '',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
				);
				break;	
				case 623:
				$diff_columns = array(
					
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
				);
				break;	
				case 24:
				$diff_columns = array(
					array(
						'name' => '',
						'header' => '',
						'value' => '',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),array(
						'name' => '',
						'header' => '',
						'value' => '',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),					
				);
				break;	
				case 28:
					$diff_columns = array(
								array(
									'name' => 'amount',
									'header' => Eas::getLabelByCategoryField($category, 'amount'),
									'value' => 'Utils::formatNumber($data->amount)',
									'htmlOptions' => array('class' => 'column90'), 
									'headerHtmlOptions' => array('class' => 'column90'),
								),
								array(
									'name' => 'man_days',
									'header' => Eas::getLabelByCategoryField($category, 'man_days'),
									'htmlOptions' => array('class' => 'column90'), 	
									'headerHtmlOptions' => array('class' => 'column90'),
								),
							);
					break;
				case 27:
					$diff_columns = array(
								array(
									'name' => 'amount',
									'header' => Eas::getLabelByCategoryField($category, 'amount'),
									'value' => 'Utils::formatNumber($data->amount)',
									'htmlOptions' => array('class' => 'column90'), 
									'headerHtmlOptions' => array('class' => 'column90'),
								),
								array(
									'name' => 'man_days',
									'header' => Eas::getLabelByCategoryField($category, 'man_days'),
									'htmlOptions' => array('class' => 'column90'), 	
									'headerHtmlOptions' => array('class' => 'column90'),
								),
							);
					break;
				default:
				$diff_columns = array(
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
					array(
						'name' => 'man_days',
						'header' => Eas::getLabelByCategoryField($category, 'man_days'),
						'htmlOptions' => array('class' => 'column90'), 	
						'headerHtmlOptions' => array('class' => 'column90'),
					),
				);
				break;
		}
		$columns = array_merge($columns, $diff_columns);		
		if ($category!=454 && $category!=496  && $category!=24 && $category!=623){
		$columns[] = array(
						'name' => 'MD Rate',
						'header' => Eas::getLabelByCategoryField($category, 'man_day_rate'),
						'value' => 'Utils::formatNumber($data->getManDayRate())',
						'htmlOptions' => array('class' => 'column65'), 
						'headerHtmlOptions' => array('class' => 'column65'),
					); }
		if(Customers::getCountryById($customer) == '398')
		{
			$columns[] = array(
						'header' => 'Offshore',
						'value' => '$data->offshore',
						'htmlOptions' => array('class' => 'column65'), 
						'headerHtmlOptions' => array('class' => 'column65'),
					); 
		}
		if ($can_modify){
			$columns[] = array(
				'class'=>'CCustomButtonColumn',
				'template'=>'{update} {delete}',
				'htmlOptions'=>array('class' => 'button-column'),
				'afterDelete'=>'function(link,success,data){ 
										if (success) {
											
											var response = jQuery.parseJSON(data); 
											// update amounts
						  					$.each(response.amounts, function(i, item) {
						  						if(i== "net_amount")
									  			{
									  				$("#"+i).val(item);
									  			}else if(i.indexOf("usd") !=-1)
										  		{
													$("[name="+i+"]").html(item);
										  		}else{
						  		    			$("#"+i).html(item);
						  		    			}
						  					});
						  					$.fn.yiiGridView.update("terms-grid");
						  					$.fn.yiiGridView.update("sanduterms-grid");
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
		}
		return $columns;
	}
	public static function getColumnsForTMGrid($id_category, $can_modify, $customer, $template, $customization){
		$category = (int)$id_category;
		$columns= array(
					array(
							'name' => 'description',
							'value' => '$data->getDescriptionGrid()',
							'type'=>'raw',
							'htmlOptions' => array('class' => 'column300', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"), 
							'headerHtmlOptions' => array('class' => 'column300'),
					),
					array(
						'name' => 'settingsCodelkup.codelkup',
						'header' => Eas::getEaItemsCodelist($category),
						'value' => 'isset($data->settingsCodelkup) ? $data->settingsCodelkup->codelkup : ""',
						'visible' => Eas::getEaItemsCodelist($category) ? true : false, 
						'htmlOptions' => array('class' => 'column50'), 
						'headerHtmlOptions' => array('class' => 'column50'),
					),
					array(
						'name' => 'man_days',
						'header' => 'ESTIMATED MAN DAYS',
						'htmlOptions' => array('class' => 'column130'), 	
						'headerHtmlOptions' => array('class' => 'column130'),
					),
				);
		$diff_columns = array();
		switch ($category){
			case 25:
				$diff_columns = array(
					array(
						'name' => 'man_days',
						'header' => Eas::getLabelByCategoryField($category, 'man_days'),
						'htmlOptions' => array('class' => 'column90'), 	
						'headerHtmlOptions' => array('class' => 'column90'),
					),
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
					array(
						'name' => 'sandu',
						'header' => Eas::getLabelByCategoryField($category, 'sandu'),
						'value' => 'Utils::formatNumber($data->sandu)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
				);
				break;			
			case 454:
				$diff_columns = array(
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
				);
				break;
				case 623:
				print_r($category);exit;
				$diff_columns = array(
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
				);
				break;
				case 623:
				$diff_columns = array(
					array(
						'name' => 'amount',
						'header' => Eas::getLabelByCategoryField($category, 'amount'),
						'value' => 'Utils::formatNumber($data->amount)',
						'htmlOptions' => array('class' => 'column90'), 
						'headerHtmlOptions' => array('class' => 'column90'),
					),
				);
				break;
				case 27:
					 
				break;
				case 28:
					 
				break;
			default:				
				break;
		}
		$columns = array_merge($columns, $diff_columns);		
		if ($category!=454 && $category!=496 && $category!=623){
		$columns[] = array(
						'header' => 'MD Rate',
						'header' => Eas::getLabelByCategoryField($category, 'man_day_rate'),
						'value' => 'Utils::formatNumber($data->man_day_rate_n)',
						'htmlOptions' => array('class' => 'column65'), 
						'headerHtmlOptions' => array('class' => 'column65'),
					); }
		if(Customers::getCountryById($customer) == '398')
		{
			$columns[] = array(
						'header' => 'Offshore',
						'value' => '$data->offshore',
						'htmlOptions' => array('class' => 'column50'), 
						'headerHtmlOptions' => array('class' => 'column50'),
					); 
		}
		if ($can_modify){
			$columns[] = array(
				'class'=>'CCustomButtonColumn',
				'template'=>'{update} {delete}',
				'htmlOptions'=>array('class' => 'button-column'),
				'afterDelete'=>'function(link,success,data){ 
										if (success) {
											var response = jQuery.parseJSON(data); 
											// update amounts
						  					$.each(response.amounts, function(i, item) {
						  		    			if(i=="net_amount")
										  			{
										  				$("#"+i).val(item);
										  			}else if(i.indexOf("usd") !=-1)
										  		   	{
														$("[name="+i+"]").html(item);
										  		   	}else
										  			{
										  		    	$("#"+i).html(item);
										  			}
						  					});
						  					$.fn.yiiGridView.update("terms-grid");
						  					$.fn.yiiGridView.update("sanduterms-grid");
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
		}
		return $columns;
	}	
	public static function getOffshoreDropdown(){
		return array('Yes' => 'Yes', 'No' => 'No');	
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_ea',$this->id_ea);
		$criteria->compare('description',$this->description,true);	$criteria->compare('amount',$this->amount);
		$criteria->compare('man_days',$this->man_days);	$criteria->compare('settings_codelist',$this->settings_codelist);
		$criteria->compare('settings_codelkup',$this->settings_codelkup);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	protected function beforeValidate(){
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {    $this->addError($param[0], $param[1]);  }
        return $r;
    }	
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
	public function getManDayRate()	{
		$man_days = (float)$this->man_days;
		$amount = (float)$this->amount;
		if ($this->man_days > 0){
			if ($this->ea->category == 25)
				return ($amount * $man_days);
			else
				return $amount/$man_days;
		}
		return '';
	}
	public function getSUTotal(){
		$sandu = $this->sandu;		
		$sutotal = (((float)$sandu)/100)*($this->getManDayRate());
		return $sutotal;
	}
	public function getYearlySupportTotal(){
		$sandu = $this->sandu;		
		$sutotal = (((float)$sandu)/100)*($this->amount);
		return $sutotal;
	}	
	public function getDescriptionGrid(){
		return '<div class="first_it panel_container">'
						.'<div class="item_clip_eas clip">'.$this->description.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->description.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div> ';
	}
	public function getDescriptionGridTrain(){
		return '<div class="first_it panel_container">'
						.'<div class="item_clip_eas clip" disabled>'.$this->description.'</div>'
						.'<u class="red">+</u>'
						.'<div class="panel">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover" disabled>'.$this->description.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div> ';
	}
	public function getActualMD($id_project){
		$sum = (float) Yii::app()->db->createCommand("SELECT SUM(amount) FROM user_time 
			WHERE id_task IN (
			SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp ON pp.id = pt.id_project_phase WHERE pp.id_project = {$id_project})")
			->queryScalar();
		return $sum/8;
	}
	public function requiredbycategory(){
 	 if( ($this->ea->category=='27'|| $this->ea->category=='28'|| $this->ea->category=='26') && (empty($this->man_days) && ($this->ea->TM<>'1'))  ){
    $this->addError('man_days','Man Days field required');
 		 }  
	}
}?>