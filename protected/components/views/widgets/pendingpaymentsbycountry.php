<div class="bcontenu">
 	<div class="stat_years"  >
 	<div style="margin-top:17px;"></div>
 	</div>
 <div id="graph-country-pending-payments"  class="style_chart700 graph"></div>
</div>
<?php $id = $this->getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Dialog box 1',
        'autoOpen'=>false,
		'modal'=>true,
	    'width'=>1003,
	    'height'=>810,
		'resizable'=>false,
		'closeOnEscape' => true,
    ),));?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetPendingPaymentsByCountry::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	 	<div class="stat_years">
 			<div style="float:right"> </div>
 	</div> 	<div class="style_chart1000 graph" id="graph-country-pending-payments1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridCountryPayments(){var pieChartDataSource=<?php echo WidgetPendingPaymentsByCountry::CharChart()?>;drowChart(pieChartDataSource,"graph-country-pending-payments");drowChart(pieChartDataSource,"graph-country-pending-payments1");$('.status_country_pending_payments_3').addClass("colorRed");$('.status_pending_payments_amount_1').addClass("colorRed")}
function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")}
function changeMonth(month){$.ajax({type:"POST",data:{'month':month},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/CountryPayments');?>",dataType:"json",success:function(data){if(data){$('.sr_country_pending_payments').removeClass("colorRed");var pieChartDataSource=data;drowChart(pieChartDataSource,"graph-country-pending-payments");drowChart(pieChartDataSource,"graph-country-pending-payments1");$('.status_country_pending_payments_'+month).addClass("colorRed")}}})}
function changeAmount(amount){$.ajax({type:"POST",data:{'amount':amount},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/CountryPayments');?>",dataType:"json",success:function(data){if(data){$('.sr_pending_payments_amount').removeClass("colorRed");var pieChartDataSource=data;drowChart(pieChartDataSource,"graph-country-pending-payments");drowChart(pieChartDataSource,"graph-country-pending-payments1");$('.status_pending_payments_amount_'+amount).addClass("colorRed")}}})}
</script>	