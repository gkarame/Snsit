<div class="perftabs expenses_edit" ><div style="height:1px; width:100%; background-color:#ddd; margin-top:4px; margin-left:-5px;padding-right:10px; margin-bottom:40px; "></div>
<div id="popupps" > <div class='titre'>Project Survey</div> <div class='closefq'> </div> <div class='surveyscontainer'></div> </div>
<div class="tableCont"><div class="table" id="currentpsyear"><div class="titret">Projects Surveys</div><div class="perfrow titleRow">
<div class="item first">Customer Name</div><div class="item second">Project Name</div><div class="item surveyee">Surveyee</div><div class="item response">Response Date</div>
<div class="item type">Type</div><div class="item third">Rates</div><div class="item satisf" >Satis.%</div></div>
<div class="cspsdatacontainer">
<?php	$customer=" ";	$totalsatisfied=0;	 if(count($ps_list)>0){	 foreach ($ps_list as $key => $value) { if(Projects::getCustomerByProject($value['id_project'])!=$customer){ ?>
<div class="perfrowsmall"> <div class="perfhorizontalLine"></div> <div class="item first " ><?php echo Customers::getNameById(Projects::getCustomerByProject($value['id_project'])); ?></div>
<div class="item second"  onmouseenter="showToolTaskTip(this)" onmouseleave="hideToolTaskTip(this)"><?php 
echo strlen(Projects::getNamebyId($value['id_project']))<32?Projects::getNamebyId($value['id_project']): Performance::getPopupTaskGrid(Projects::getNamebyId($value['id_project']));
?> </div><div class="item surveyee"><?php echo  $value['first']." ".$value['last']; ?> </div><div class="item response"> <?php	echo Utils::formatDate(substr($value['response_date'], 0,10)); ?> </div>
<div class="item type"> <?php	echo $value['surv_type']=='close'?'Closure':'Intermediate'; ?> </div>
<div class="item third fq" id="readsurvey_<?php echo $value['id_project']; ?>_<?php  echo $value['surv_type']; ?>" project="<?php echo $value['id_project']; ?>" type="<?php echo $value['surv_type']; ?>" ><?php echo ($value['count']*10)."%"; ?> <img height='10' src="../../images/<?php echo $value['rate'];?>stars.png" />  </div>
<div class="item satisf" >  <?php echo CustomerSatisfaction::getTotalSurveyProjectType($value['id_project'],$value['surv_type'])*10 ."%" ?></div>
 <br/> <br/><br/> <br/></div><?php   }else { ?>
<div class='fq' id="readsurvey_<?php echo $value['id_project']; ?>_<?php  echo $value['surv_type']; ?>" project="<?php echo $value['id_project']; ?>" type="<?php echo $value['surv_type']; ?>" style="margin-left:657px; text-align:left;" ><?php echo ($value['count']*10)."%";?> <img height='10' src="../../images/<?php echo $value['rate'];?>stars.png" /> </div> <br/>
<?php } $customer=Projects::getCustomerByProject($value['id_project']); }  }?></div></div><div id="newpstable" class="hidden"></div></div><br /><br />
	<div id="popupcs"> <div class='cstitre'>SR Ratings for </div><div class='closefcs'> </div> <div class='ratingcontainer'></div></div>
<div class="tableCont"><div class="table table2" id="currentcsyear"><div class="titret">Support Ratings </div><div class="perfrow titleRow">
<div class="item customer">Customer Name</div><div class="item closed">Closed SRs</div><div class="item rating">Rates</div><div class="item satisf">Satis.%</div>
</div><div class="cspsdatacontainer"><?php	$customer=" ";	 if(count($cs_list)>0){	 foreach ($cs_list as $key => $value) {
	 		$SRCustomerResc= Utils::formatNumber(CustomerSatisfaction::getCountSRCustomerResource($value['id_user'],$value['id_customer'] , $year),2);
	 		if ($SRCustomerResc==0){ $SRCustomerResc=1;	}
	 		$totalSRclosedSatisf= Utils::formatNumber((CustomerSatisfaction::getTotalSRClosedCustomerSatis($value['id_user'],$value['id_customer'], $year)*100)/$SRCustomerResc,1);
	if($value['id_customer']!=$customer){	  ?>	
<div class="perfrowsmall">  <div class="perfhorizontalLine" ></div> <div class="item customer  "  ><?php echo Customers::getNamebyId($value['id_customer']); ?></div>
<div class="item closed"><?php  echo  $SRCustomerResc ;?> </div> <div class="item rating fcs" id="checkrates_<?php echo $value['id_customer']; ?>" customer="<?php echo $value['id_customer']; ?>" user="<?php echo $userid; ?>"  year="<?php echo $year; ?>" > <span style="width:48px;"><?php echo Utils::formatNumber(($value['count']*100)/$SRCustomerResc,1). "% " ; ?></span><img height='10' src="../../images/<?php echo $value['rate'];?>stars.png" /> </div>
<div class="item satisf"> <?php echo $totalSRclosedSatisf."%"; ?> </div> <br/><br/><br/><br/>
</div> <?php   }else { ?> <div style="padding-left:650px; " class="fcs" id="checkrates_<?php echo $value['id_customer']; ?>" customer="<?php echo $value['id_customer']; ?>" user="<?php echo $userid; ?>"  year="<?php echo $year; ?>" ><?php echo Utils::formatNumber(($value['count']*100)/$SRCustomerResc,1). "% " ; ?><img height='10' src="../../images/<?php echo $value['rate'];?>stars.png" /> </div><br/>
<?php } $customer=$value['id_customer']; } } ?></div></div><div id="newcstable" class="hidden"></div></div>	<div id="expenses_items"  style="background-color:#cccccc">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'after-form-details',	'enableAjaxValidation'=>false,'htmlOptions' => array(	'class' => 'ajax_submit','enctype' => 'multipart/form-data',	'action' => Yii::app()->createUrl("fullsupport/update", array("id"=>$model->id))),	)); ?>
<?php $this->endWidget(); ?></div></div>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; var modelId = '<?php echo $model->id;?>';
	var updateItemAfterUrl = '<?php echo Yii::app()->createUrl("fullsupport/createAfterItem"); ?>';
</script>	
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>