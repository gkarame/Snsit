<div class="bcontenu">
 	<div class="stat_years">
			<span class="status month_payment_1 month_payment" data-id ="1" onClick="changeMonthlyPayment(1)"><i>Current Year/</i></span>
			<span class="status month_payment_5 month_payment" data-id ="5" onClick="changeMonthlyPayment(5)"><i>Last 12 Months/</i></span>
 			<span class="status month_payment_3 month_payment" data-id ="3" onClick="changeMonthlyPayment(3)"><i>Last 24 months/</i></span>
 			<span class="status month_payment_4 month_payment" data-id ="4" onClick="changeMonthlyPayment(4)"><i>Last 36 months</i></span>
	</div>	<div class="style_chart700 graph" id="graph-monthlypayment" ></div>
</div>
<?php $id = WidgetMonthlyPayment::getId();
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
		'closeOnEscape' => true,
    ),));?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetMonthlyPayment::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div>
 	<div class="stat_years">
 	  <span class="status month_payment_1 month_payment" data-id ="1" onClick="changeMonthlyPayment(1)"><i>Current Year/</i></span>
	  <span class="status month_payment_5 month_payment" data-id ="5" onClick="changeMonthlyPayment(5)"><i>Last 12 Months/</i></span>
      <span class="status month_payment_3 month_payment" data-id ="3" onClick="changeMonthlyPayment(3)"><i>Last 24 months/</i></span>
      <span class="status month_payment_4 month_payment" data-id ="4" onClick="changeMonthlyPayment(4)"><i>Last 36 months</i></span>
 	</div>	<div class="graph style_chart1000" id="graph-monthlypayment1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">function createGridMonthlyPayment(){$('.month_payment_1').addClass("colorRed");val=<?php echo WidgetMonthlyPayment::CharChart()?>;ChartMonthlyPayment(val,"graph-monthlypayment");ChartMonthlyPayment(val,"graph-monthlypayment1")};function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")}
function ChartMonthlyPayment(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:"line",argumentField:"month"},commonAxisSettings:{grid:{visible:!0}},tooltip:{enabled:!0,customizeText:function(e){return numberWithCommas(e.value)}},series:[{name:'Payment',argumentField:'month',valueField:'Payment',tagField:'lookupdateval'}],legend:{visible:!1},commonPaneSettings:{border:{visible:!0,bottom:!1}}})}
function changeMonthlyPayment(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/MonthlyPaymentSort');?>",dataType:"json",success:function(data){if(data){$('.month_payment').removeClass("colorRed");var pieChartDataSource=data;ChartMonthlyPayment(pieChartDataSource,"graph-monthlypayment");ChartMonthlyPayment(pieChartDataSource,"graph-monthlypayment1");$('.month_payment_'+val).addClass("colorRed")}}})}
</script>	