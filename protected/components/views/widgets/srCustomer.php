<div class="bcontenu">
 	<div class="stat_years">
		<span class="status status_sr_cust_2 sr_customer" data-id ="2" onClick="changeMonthCustomer(2)"><i> Current Mon/</i></span>
		<span class="status status_sr_cust_3 sr_customer" data-id ="3" onClick="changeMonthCustomer(3)"><i>Last 3 Mon/</i></span>
		<span class="status status_sr_cust_4 sr_customer" data-id ="4" onClick="changeMonthCustomer(4)"><i>Last 6 Mon/</i></span>
		<span class="status status_sr_cust_1 sr_customer" data-id ="1" onClick="changeMonthCustomer(1)"><i>Last Year</i></span>
		<span class="status status_nb_customer_20 nb_customer" data-id ="20" style="float:right;padding-right=20px;" onClick="changeCustomer(20)"><i> 20</i></span>
		<span class="status status_nb_customer_10 nb_customer" data-id ="10" style="float:right;" onClick="changeCustomer(10)"><i>10 /</i></span>
		<span class="status status_nb_customer_5 nb_customer" data-id ="5" style="float:right;" onClick="changeCustomer(5)"><i> Top 5/</i></span>
		<span class="status status_nb_customer_100 nb_customer" data-id ="100" style="float:right;" onClick="changeCustomer(100)"><i> All/</i></span>
</div>	<div class="style_chart700 graph" id="graph-sr-customer" ></div>
</div>
<?php $id = WidgetSrCustomer::getId();
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
			<div class="title"><?php echo  WidgetSrCustomer::getName(); ?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_sr_cust_2 sr_customer" data-id ="2" onClick="changeMonthCustomer(2)"><i> Current Month / </i></span>
 			<span class="status status_sr_cust_3 sr_customer" data-id ="3" onClick="changeMonthCustomer(3)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_cust_4 sr_customer" data-id ="4" onClick="changeMonthCustomer(4)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_cust_1 sr_customer" data-id ="1" onClick="changeMonthCustomer(1)"><i> Last Year</i></span>
<span class="status status_nb_customer_20 nb_customer" data-id ="20" style="float:right;padding-right=20px;" onClick="changeCustomer(20)"><i>/ Top 20</i></span>
				<span class="status status_nb_customer_10 nb_customer" data-id ="10" style="float:right;" onClick="changeCustomer(10)"><i>/ Top 10 </i></span>
  				<span class="status status_nb_customer_5 nb_customer" data-id ="5" style="float:right;" onClick="changeCustomer(5)"><i>/ Top 5</i></span>
				<span class="status status_nb_customer_100 nb_customer" data-id ="100" style="float:right;" onClick="changeCustomer(100)"><i> All</i></span>
</div> 	<div class="style_chart1000 graph" id="graph-sr-customer1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
$(document).ready(function(){$('.closepopupwidget').click(function(){$('.popupwidget').addClass('hidden')})});function createGridSrCustomer(){var pieChartDataSource=<?php echo WidgetSrCustomer::CharChart()?>;drowCustomerChart(pieChartDataSource,"graph-sr-customer");drowCustomerChart(pieChartDataSource,"graph-sr-customer1");$('.status_sr_cust_1').addClass("colorRed");$('.status_nb_customer_100').addClass("colorRed")};function drowCustomerChart(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:5},series:{argumentField:'category',valueField:'value'},tooltip:{enabled:!0,customizeText:function(e){var cust=e.argument;var perc=e.percentText;$.ajax({type:"POST",data:{'cust':cust,'perc':perc},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ShowTable2');?>",dataType:"json",success:function(data){if(data){if($('#graph-unsat-customer1').is(':visible')){$('.popupwidget').addClass('z-index')}
$('.popupwidget').removeClass('hidden');var val=data.caxis;var name=data.cname;document.getElementById("cust_name").innerHTML=name;ChartReasons(val,"graph-customer-profile")}}})}}})}
function changeCustomer(slice){$.ajax({type:"POST",data:{'slice':slice},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrBarSortCustomer');?>",dataType:"json",success:function(data){if(data){$('.nb_customer').removeClass("colorRed");var pieChartDataSource=data;drowCustomerChart(pieChartDataSource,"graph-sr-customer");drowCustomerChart(pieChartDataSource,"graph-sr-customer1");$('.status_nb_customer_'+slice).addClass("colorRed")}}})}
function changeMonthCustomer(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrBarSortCustomer');?>",dataType:"json",success:function(data){if(data){$('.sr_customer').removeClass("colorRed");var pieChartDataSource=data;drowCustomerChart(pieChartDataSource,"graph-sr-customer");drowCustomerChart(pieChartDataSource,"graph-sr-customer1");$('.status_sr_cust_'+val).addClass("colorRed")}}})}
function ChartReasons(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,rotated:!0,commonSeriesSettings:{argumentField:"label",type:"bar"},customizePoint:function(){return{color:'#33CC33',hoverStyle:{color:'#33CC33'}}},tooltip:{enabled:!0,background:'grey',font:{color:'black',size:15,},customizeText:function(arg){return arg.point.tag+" "}},valueAxis:{tickInterval:5,valueMarginsEnabled:!1,label:{format:"fixedPoint",precision:0,customizeText:function()
{return this.value}}},legend:{verticalAlignment:'bottom',horizontalAlignment:'center',visible:!1},palette:["#7fbffe"],series:[{argumentField:'total1',valueField:'older1',tagField:'tag1'}],})}
</script>	