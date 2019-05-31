<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_sr_top_cust_2 sr_top_customer" data-id ="2" onClick="changeMonthTopCustomer(2)"><i> Current Month / </i></span>
 			<span class="status status_sr_top_cust_1 sr_top_customer" data-id ="1" onClick="changeMonthTopCustomer(1)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_top_cust_3 sr_top_customer" data-id ="3" onClick="changeMonthTopCustomer(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_top_cust_4 sr_top_customer" data-id ="4" onClick="changeMonthTopCustomer(4)"><i> Last Year</i></span>
 	</div> 	<div class="style_chart700 graph" id="graph-sr-top-customer" ></div> 	
</div>
<?php $id = WidgetSrTopCustomer::getId();
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
		'closeOnEscape' => true,    ),));?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetSrTopCustomer::getName();?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 		<span class="status status_sr_top_cust_2 sr_top_customer" data-id ="2" onClick="changeMonthTopCustomer(2)"><i> Current Month / </i></span>
 		<span class="status status_sr_top_cust_1 sr_top_customer" data-id ="1" onClick="changeMonthTopCustomer(1)"><i> Last 3 Months /</i></span>
 		<span class="status status_sr_top_cust_3 sr_top_customer" data-id ="3" onClick="changeMonthTopCustomer(3)"><i> Last 6 Months /</i></span>
 		<span class="status status_sr_top_cust_4 sr_top_customer" data-id ="4" onClick="changeMonthTopCustomer(4)"><i> Last Year</i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-pop-up-<?php echo $id?>" ></div> 	
</div>
<?php 	$this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridTopCustomer(){var pieChartDataSource=<?php echo WidgetSrTopCustomer::CharChart()?>;drowChartTopCustomer(pieChartDataSource,"graph-sr-top-customer");drowChartTopCustomer(pieChartDataSource,"graph-pop-up-<?php echo $id?>");$('.status_sr_top_cust_1').addClass("colorRed")};function changeMonthTopCustomer(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrBarSortTopCustomer');?>",dataType:"json",success:function(data){if(data){$('.sr_top_customer').removeClass("colorRed");var pieChartDataSource=data;drowChartTopCustomer(pieChartDataSource,"graph-sr-top-customer");drowChartTopCustomer(pieChartDataSource,"graph-pop-up-<?php echo $id?>");$('.status_sr_top_cust_'+val).addClass("colorRed")}}})}
function drowChartTopCustomer(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:2},series:{argumentField:'category',valueField:'value',},tooltip:{enabled:!0,customizeText:function(e){return e.argument+" "+e.percentText}}})}
</script>	