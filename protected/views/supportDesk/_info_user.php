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
<?php $id_doc = SupportDesk::getPicture($cs_rep); ?><div class="division_1 inline-block">
	<div class="pic"><img width="80px" height="80px" src="<?php echo Yii::app()->getBaseUrl().'/uploads/users/'.$cs_rep.'/documents/'.$id_doc['id'].'/'.$id_doc['file'];?>"></div>
	<div class="mask"></div><div class="s_desc width179">	<div class="label uppercase">	HI I'M <font style="color:#8D0719"><?php echo Users::getUsername($cs_rep) ?></font>,<br>
			I AM HERE TO HELP YOU	</div>	  <br><br></div></div><div class="division_2 inline-block width158">
<div class="s_desc"><div class="label">PRIMARY # <?php echo Users::getMobilebyID($cs_rep);?></div></div>
<?php if ( ($valid_saturday=='1' || $valid_sunday=='1') && $second_cs_rep!='0') { ?> 
<div class="s_desc"><div class="label">SECONDARY # <?php echo Users::getMobilebyID($second_cs_rep);?></div>
</div><?php } ?></div> <div class="division_2 inline-block"><div class="s_desc"><div class="label">EMAIL</div><a href="mailTo:'<?php echo Users::getEmailbyID($cs_rep);?>'">
<?php  echo Users::getEmailbyID($cs_rep);?></a></div></div>
<div class="division_2 inline-block noimg"><div class="s_desc"><div class="label">SKYPE<span>(In case of Emergency only)</span></div><?php echo Users::getSkypebyID($cs_rep) ; ?></div></div>
