<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_sr_subCust_4 sr_submitted_cust" data-id="4" onClick="changeYearsSubmitted(4)"><i> This Year / </i></span>
 			<span class="status status_sr_subCust_2 sr_submitted_cust" data-id="2" onClick="changeYearsSubmitted(2)"><i> Last 3 Years / </i></span>
 			<span class="status status_sr_subCust_1 sr_submitted_cust" data-id="1" onClick="changeYearsSubmitted(1)"><i> Last 5 Years /</i></span>
 			<span class="status status_sr_subCust_3 sr_submitted_cust" data-id="3" onClick="changeYearsSubmitted(3)"><i> Last 10 Years </i></span>
 	</div> 	<div id="pieSubmittedCustomer" class="style_chart700 graph"></div>
</div>
<?php $id = WidgetSubmittedCustomer::getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Dialog box 1',
        'autoOpen'=>false,
		'modal'=>true,
	    'width'=>1006,
	    'height'=>810,
		'resizable'=>false,
		'class'=>"unaspeciala",
		'closeOnEscape' => true,    ),));?>
<div class="bcontenu z-index">
 	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetSubmittedCustomer::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_sr_subCust_4 sr_submitted_cust " onClick="changeYearsSubmitted(4)"><i> This Year / </i></span>
 			<span class="status status_sr_subCust_2 sr_submitted_cust " onClick="changeYearsSubmitted(2)"><i> Last 3 Years / </i></span>
 			<span class="status status_sr_subCust_1 sr_submitted_cust" onClick="changeYearsSubmitted(1)"><i> Last 5 Years /</i></span>
 			<span class="status status_sr_subCust_3 sr_submitted_cust" onClick="changeYearsSubmitted(3)"><i> Last 10 Years </i></span>
 	</div> 	<div id="pieSubmittedCustomer1" class="style_chart1000 graph"></div>
 	</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridCustomersub(){$('.status_sr_subCust_1').addClass("colorRed");var pieChartDataSource=<?php echo WidgetSubmittedCustomer::CharChart1();?>;barSRC(pieChartDataSource,1);barSRC(pieChartDataSource,0);$('.status_sr_subCust_1').addClass("colorRed")};function barSRC(pieChartDataSource,val){$(function(){if(val==1){id="pieSubmittedCustomer"}
else{;id="pieSubmittedCustomer1"}
$("#"+id).dxChart({dataSource:pieChartDataSource,commonSeriesSettings:{argumentField:"state",type:"bar",hoverMode:"allArgumentPoints",selectionMode:"allArgumentPoints",label:{visible:!0,format:"fixedPoint",precision:0}},legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"right",verticalAlignment:"bottom",rowCount:2},legend:{visible:!1,},series:{argumentField:"label",valueField:"value",color:'#ffa500',},valueAxis:{title:{text:"SRs"}},argumentAxis:{title:'Years',type:'discrete',grid:{visible:!0}}})})}
function changeYearsSubmitted(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/submittedCustomer');?>",dataType:"json",success:function(data){if(data){$('.sr_submitted_cust').removeClass("colorRed");var pieChartDataSource=data;barSRC(pieChartDataSource,1);barSRC(pieChartDataSource,0);$('.status_sr_subCust_'+val).addClass("colorRed")}}})}
</script>	