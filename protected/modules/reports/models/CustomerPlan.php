<?php

class CustomerPlan extends CFormModel
{	
	public $name ,$support_service, $industry,  $product , $id_account_manager,$strategic, $support_weekend, $cs_representative,  $account_manager,  $erp , $brands, $soft_version, $product_type, $id_ca,$ca , $wms_db_type, $file;
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.

			array('name , industry ,support_service, id_account_manager , account_manager, strategic, erp , brands, soft_version, support_weekend, product ,product_type, id_ca, ca, cs_representative, wms_db_type, file', 'safe'),

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				
			'user' => 'User',
			'from' => 'From',
			'to' => 'To'
			
		);
	}
}
