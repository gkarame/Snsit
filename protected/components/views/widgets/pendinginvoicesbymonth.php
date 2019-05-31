<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status pending_month_payment_5 pending_month_payment" data-id ="5" onClick="changependingpaymentbymonth(5)"><i>Last 12 Mon/</i></span>
 			<span class="status pending_month_payment_2 pending_month_payment" data-id ="2" onClick="changependingpaymentbymonth(2)"><i>Last 24 Mon/</i></span>
 			<span class="status pending_month_payment_3 pending_month_payment" data-id ="3" onClick="changependingpaymentbymonth(3)"><i>Last 36 Mon</i></span>
	</div><div class="style_chart700 graph" id="graph-pendingpaymentbymonth" ></div>
</div>
<?php $id = WidgetPendingInvoicesByMonth::getId();
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
			<div class="title"><?php echo  WidgetPendingInvoicesByMonth::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div>
 	<div class="stat_years">
 	  <span class="status pending_month_payment_5 pending_month_payment" data-id ="5" onClick="changependingpaymentbymonth(5)"><i>Last 12 Mon/</i></span>
      <span class="status pending_month_payment_2 pending_month_payment" data-id ="2" onClick="changependingpaymentbymonth(2)"><i>Last 24 Mon/</i></span>
      <span class="status pending_month_payment_3 pending_month_payment" data-id ="3" onClick="changependingpaymentbymonth(3)"><i>Last 36 Mon</i></span>
 	</div> 	<div class="graph style_chart1000" id="graph-pendingpaymentbymonth1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">function createGridPendingInvoicesByMonth(){$('.pending_month_payment_5').addClass("colorRed");val=<?php echo WidgetPendingInvoicesByMonth::CharChart()?>;ChartMonthlyPayment(val,"graph-pendingpaymentbymonth");ChartMonthlyPayment(val,"graph-pendingpaymentbymonth1")};function ChartMonthlyPayment(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:"line",argumentField:"month"},commonAxisSettings:{grid:{visible:!0}},tooltip:{enabled:!0,customizeText:function(e){return numberWithCommas(e.value)}},series:[{name:'Payment',argumentField:'month',valueField:'Payment',tagField:'lookupdateval'}],legend:{visible:!1},commonPaneSettings:{border:{visible:!0,bottom:!1}}})}
function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")}
function changependingpaymentbymonth(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/PendingPaymentsByMonthSort');?>",dataType:"json",success:function(data){if(data){$('.pending_month_payment').removeClass("colorRed");var pieChartDataSource=data;ChartMonthlyPayment(pieChartDataSource,"graph-pendingpaymentbymonth");ChartMonthlyPayment(pieChartDataSource,"graph-pendingpaymentbymonth1");$('.pending_month_payment_'+val).addClass("colorRed")}}})}
</script>	