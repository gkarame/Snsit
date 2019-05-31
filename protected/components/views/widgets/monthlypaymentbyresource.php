<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status payment_resource_1 payment_resource" data-id ="1" onClick="changePaymentByResource(1)"><i> Current Mo / </i></span>
 			<span class="status payment_resource_2 payment_resource" data-id ="2" onClick="changePaymentByResource(2)"><i> Last 3 Mo /</i></span>
 			<span class="status payment_resource_3 payment_resource" data-id ="3" onClick="changePaymentByResource(3)"><i> Last 6 Mo /</i></span>
 			<span class="status payment_resource_4 payment_resource" data-id ="4" onClick="changePaymentByResource(4)"><i> Last Year</i></span>
 	</div><div class="style_chart700 graph" id="graph-paymentbyresource" ></div>
</div>
<?php $id = WidgetMonthlyPaymentByResource::getId();
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
			<div class="title"><?php echo  WidgetMonthlyPaymentByResource::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status payment_resource_1 payment_resource" onClick="changePaymentByResource(1)"><i> Current Mo / </i></span>
 			<span class="status payment_resource_2 payment_resource" onClick="changePaymentByResource(2)"><i> Last 3 Mo /</i></span>
 			<span class="status payment_resource_3 payment_resource" onClick="changePaymentByResource(3)"><i> Last 6 Mo /</i></span>
 			<span class="status payment_resource_4 payment_resource" onClick="changePaymentByResource(4)"><i> Last Year</i></span>
 	</div> 	<div class="graph style_chart1000" id="graph-paymentbyresource1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridPaymentByResource(){$('.payment_resource_2').addClass("colorRed");val=<?php echo WidgetMonthlyPaymentByResource::CharChart()?>;ChartPaymentByResource(val,"graph-paymentbyresource");ChartPaymentByResource(val,"graph-paymentbyresource1")};function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")}
function ChartPaymentByResource(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:"line",argumentField:"month",},commonAxisSettings:{grid:{visible:!0}},series:<?php echo WidgetMonthlyPaymentByResource::getSeries();?>,tooltip:{enabled:!0,customizeText:function(e){return numberWithCommas(e.value)}},legend:{verticalAlignment:"bottom",horizontalAlignment:"center",rowCount:3},valueAxis:{name:'Value Axis 1',valueType:"numeric"},commonPaneSettings:{border:{visible:!0,bottom:!1}}})}
function changePaymentByResource(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/MonthlyPaymentByResourceSort');?>",dataType:"json",success:function(data){if(data){$('.payment_resource').removeClass("colorRed");var pieChartDataSource=data;ChartPaymentByResource(pieChartDataSource,"graph-paymentbyresource");ChartPaymentByResource(pieChartDataSource,"graph-paymentbyresource1");$('.payment_resource_'+val).addClass("colorRed")}}})}
</script>	