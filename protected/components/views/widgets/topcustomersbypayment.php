<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_customer_top 5"  data-id="5"  onClick="changeTopCust(5)"><i>Top 5 / </i></span>
 			<span class="status status_customer_top 10" data-id="10" onClick="changeTopCust(10)"><i>Top 10 / </i></span>
 			<span class="status status_customer_top 20" data-id="20" onClick="changeTopCust(20)"><i>Top 20 /</i></span>			
 			<span class="status status_customer_top 30" data-id="30" onClick="changeTopCust(30)"><i>Top 30 /</i></span>			
 			<span class="status status_customer_top 40" data-id="40" onClick="changeTopCust(50)"><i>Top 50 </i></span> 	</div>
 	<div class="style_chart700 graph" id="graphTopCustomer-1" ></div></div>
<?php $id = $this->getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
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
			<div class="title"><?php echo  WidgetTopCustomersByPayment::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_customer_top 5" data-id="5" onClick="changeTopCust(5)"><i>Top 5 / </i></span>
 			<span class="status status_customer_top 10" data-id="10" onClick="changeTopCust(10)"><i>Top 10 / </i></span>
 			<span class="status status_customer_top 20" data-id="20" onClick="changeTopCust(20)"><i>Top 20 /</i></span>
			<span class="status status_customer_top 30" data-id="30" onClick="changeTopCust(30)"><i>Top 30 /</i></span>
			<span class="status status_customer_top 40" data-id="40" onClick="changeTopCust(50)"><i>Top 50 </i></span>
 	</div> 	<div class="style_chart1000 graph" id="graphTopCustomer-11" ></div></div>
<?php 	$this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridTopCustomer1(){var pieChartDataSource=<?php echo WidgetTopCustomersByPayment::getTopCustomers()?>;drowTimeChart2(pieChartDataSource,"graphTopCustomer-1");drowTimeChart2(pieChartDataSource,"graphTopCustomer-11");$('.status_customer_top.5').addClass("colorRed")}
function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")}
function drowTimeChart2(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:5},series:{argumentField:'Customer',valueField:'value'},tooltip:{enabled:!0,customizeText:function(e){return "<span style='font-size:55%;' >"+e.argument+" - "+numberWithCommas(e.value)+" $</span>"}}})}
function changeTopCust(top){$.ajax({type:"POST",data:{'top':top},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/TopCustomerPieSort');?>",dataType:"json",success:function(data){if(data){$('.status_customer_top').removeClass("colorRed");var pieChartDataSource=data;drowTimeChart2(pieChartDataSource,"graphTopCustomer-1");drowTimeChart2(pieChartDataSource,"graphTopCustomer-11");$('.status_customer_top.'+top).addClass("colorRed")}}})}
</script>	