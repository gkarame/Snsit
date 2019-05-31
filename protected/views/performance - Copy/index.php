
<div class="customer-view mytabs hidden" >
<div style="background-color:#f8f8f8; height:210px;"> 	

<?php $cs_rep='9' ; $id_doc = SupportDesk::getPicture($cs_rep); ?>
<table  cellspacing="0" cellpadding="0">
	<tr>
		<td  rowspan="8" style="width:220px;">
			<div class="division_1 inline-block">
			<div class="pic" style="width:150px;height:150px; margin:30px; background-color:#ccc; text-align:center">
				<img width="100%" height="80%" src="<?php echo Yii::app()->getBaseUrl().'/uploads/users/'.$cs_rep.'/documents/'.$id_doc['id'].'/'.$id_doc['file'];?>">
				<div style=" background-color:#31ab70; height:20%; "><div style=" padding-top:6px; color:white; font-family:Calibri" >Super Performer </div> </div>
			</div>
			</div>
		</td>		
	</tr>
	<tr><td> &nbsp; </td></tr>
	<tr><td colspan="4" style="font-size:20px;color:#333333; "> <?php echo Users::getNameById($cs_rep); ?> </td> <td  width="100px;"  class="red" >dropdown year</td></tr>
	<tr><td colspan="5"><?php echo UserPersonalDetails::getJobTitle($cs_rep); ?></td> </tr>
	<tr><td colspan="5">---</td></tr>
	<tr style="color:#9f9f9f"><td width="189px">Productivity</td> <td  width="189px">Quality</td> <td  width="189px" >Customer Satisfaction</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
	<tr ><td style="font-size:19px; color:#333333">90%</td> <td style="font-size:19px; color:#333333">6/10</td> <td style="font-size:19px; color:#333333">8/10</td> <td style="font-size:19px; color:#333333">8/10</td> <td>&nbsp;</td></tr>
	<tr><td> </td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td></tr>

</table>



</div>
<div style="height:3px; background-color:#cccccc; margin-bottom:30px;"></div>
<?php 
$tabs = array();

		$tabs[Yii::t('translations', 'Productivity')] =$this->renderPartial('_productivity', array('model'=>$model), true);
		
		$tabs[Yii::t('translations', 'Quality')] =$this->renderPartial('_quality', array('model'=>$model), true);

		$tabs[Yii::t('translations', 'Cust. Satisfaction')] =$this->renderPartial('_Sunday_support_tab', array('model'=>$model), true);

	$this->widget('CCustomJuiTabs', array(
	    'tabs'=>$tabs,
	    // additional javascript options for the tabs plugin
	    'options'=>array(
	        'collapsible'=>false,
	    	'active' =>  'js:configJs.current.activeTab',
	    ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>'
	));?> 
	
</div>