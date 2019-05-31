<?php 	$customer = Customers::model()->findByPk(Yii::app()->user->customer_id);	 $today= date('Y-m-d', strtotime('now'));	$normal_rep =$customer->cs_representative;		$dat=date('h', strtotime('now'));			
		$valid_saturday=Yii::app()->db->createCommand("select case when ((TIME_TO_SEC(NOW()+ INTERVAL 10 HOUR) NOT BETWEEN TIME_TO_SEC('08:00:00') and TIME_TO_SEC('18:00:00') ) or (DAYNAME(NOW()+ INTERVAL 10 HOUR)='Saturday') )then TRUE else FALSE END")->queryScalar();
		$valid_sunday=Yii::app()->db->createCommand("select case when ((TIME_TO_SEC(NOW()+ INTERVAL 10 HOUR) NOT BETWEEN TIME_TO_SEC('08:00:00') and TIME_TO_SEC('18:00:00') ) or (DAYNAME(NOW()+ INTERVAL 10 HOUR)='Sunday') )then TRUE else FALSE END")->queryScalar();
	if ( $valid_saturday=='1'){
			$cs_rep = Yii::app()->db->createCommand("SELECT ifnull(primary_contact,0) FROM fullsupport where '".$today."' BETWEEN from_date and to_date and type=402")->queryScalar();
			$second_cs_rep = Yii::app()->db->createCommand("SELECT ifnull(secondary_contact,0) FROM fullsupport where '".$today."' BETWEEN from_date and to_date and type=402")->queryScalar();
			if ( is_null($cs_rep) || $cs_rep==' ' || $cs_rep==0 ){  $cs_rep= $customer->cs_representative;  }; 						
			}else if( $valid_sunday=='1' ) { 
			$cs_rep = Yii::app()->db->createCommand("SELECT ifnull(primary_contact,0) FROM fullsupport where '".$today."'=from_date and type=403")->queryScalar();
			$second_cs_rep = Yii::app()->db->createCommand("SELECT ifnull(secondary_contact,0) FROM fullsupport where '".$today."'=from_date and type=403")->queryScalar();
			if (is_null($cs_rep) || $cs_rep==' ' || $cs_rep==0){  $cs_rep= $customer->cs_representative;  }; 				 
			}else {		$cs_rep =$customer->cs_representative;	}; ?>
<?php $id_doc = SupportDesk::getPicture($cs_rep); $msg = 	SupportDesk::checkUnratedbyCustomer($model_customer->id);?>

	<div class="division_1 inline-block" style="width: 260px;    height: 100px;">
	<div class="pic"><img width="80px" height="80px" src="<?php echo Yii::app()->getBaseUrl().'/uploads/users/'.$cs_rep.'/documents/'.$id_doc['id'].'/'.$id_doc['file'];?>"></div>
	<div class="mask"></div><div class="s_desc width179" style="    width: 50% !important;">	<div class="label " style="    width: 105%;">	Hi, I'm <font style="color:#8D0719;text-transform:CAPITALIZE;"><?php echo Users::getUsername($cs_rep) ?></font>,<br>
			I am here for your <font style="color:#8D0719">support requests</font> !	</div>	  
			
			<div class="btn_search_support">
<?php if($msg==''){ echo CHtml::link(Yii::t('translation', '+ New Issue'), array('create'), array('class'=>'add-btn','id'=>'reset','style'=>'float: left;
    text-indent: 0;
    background-size: 130px 35px;
    width: 130px !important;
    height: 35px;
    font-size: 15px;
    text-decoration: underline;   margin-top: 15px;','title'=>'Click here for assistance in any technical issue. Issues will be directed to your support desk representative')); } ?>


			<br>
			<br></div></div></div>
			<div class="division_2 inline-block width158" style="width: 20% !important;padding-top: 22px;">
				<div class="s_desc"><div class="label">PRIMARY # <?php echo Users::getMobilebyID($cs_rep);?></div></div>
				<?php if ( ($valid_saturday=='1' || $valid_sunday=='1') && $second_cs_rep!='0') { ?> 
				<div class="s_desc"><div class="label">SECONDARY # <?php echo Users::getMobilebyID($second_cs_rep);?></div>
				</div><?php } ?>
				<div class="s_desc" ><a href="mailTo:'<?php echo Users::getEmailbyID($cs_rep);?>'">
<?php  echo Users::getEmailbyID($cs_rep);?></a></div>
				<div class="s_desc" style="margin-top: 8px;"><div class="label">SKYPE<span>(In case of Emergency only)</span></div><?php echo Users::getSkypebyID($cs_rep) ; ?></div>
			</div>
 
<?php 	$ca =$customer->ca; ?>
<?php $id_doc_ca = SupportDesk::getPicture($ca); ?>


 	<div class="division_1 inline-block" style="width: 260px;   height: 100px;">
	<div class="pic" style="left: 470px;"><img width="80px" height="80px" src="<?php echo Yii::app()->getBaseUrl().'/uploads/users/'.$ca.'/documents/'.$id_doc_ca['id'].'/'.$id_doc_ca['file'];?>"></div>
	<div class="mask"  style="left: 470px;"></div><div class="s_desc width179" style="    width: 50% !important;">	<div class="label " style="    width: 108%;">	Hi, I'm <font style="color:#8D0719;text-transform:CAPITALIZE;"><?php echo Users::getUsername($ca) ?></font>,<br>
			I am here for your <font style="color:#8D0719">inquiries</font> !	</div>
			<div class="btn_search_support">
<?php if($msg==''){ echo CHtml::link(Yii::t('translation', '+ New Inquiry'), array('createCR'), array('class'=>'add-btn','id'=>'reset','style'=>'float: left;
    text-indent: 0;
    background-size: 130px 35px;
    width: 130px !important;
    height: 35px;
    font-size: 15px;
    text-decoration: underline;
margin-top: 15px;','title'=>'Click here for assistance in any new inquiry or change. Inquiries will be directed to your operational consultant')); } ?>
    	  <br><br></div></div></div>
			<div class="division_2 inline-block width158" style="width: 20% !important;padding-top: 22px;">
				<div class="s_desc"><div class="label">PRIMARY # <?php echo Users::getMobilebyID($ca);?></div></div>				
				<div class="s_desc" ><a href="mailTo:'<?php echo Users::getEmailbyID($ca);?>'">
<?php  echo Users::getEmailbyID($ca);?></a></div>
				<div class="s_desc" style="margin-top: 8px;"><div class="label">SKYPE<span>(In case of Emergency only)</span></div><?php echo Users::getSkypebyID($ca) ; ?></div>
			</div>