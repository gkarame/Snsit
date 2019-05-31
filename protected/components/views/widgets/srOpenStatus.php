<div class="bcontenu"><div class="style_chart700 graph" id="graph-sr-openstatus-customer" ></div></div>
<?php $id = WidgetSrOpenStatus::getId();
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
		'closeOnEscape' => true,   ),));?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetSrOpenStatus::getName();?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div> 	<div class="style_chart1000 graph" id="graph-sr-openstatus-customer1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridOpenStatusCustomer(){var pieChartDataSource=<?php echo WidgetSrOpenStatus::CharChart()?>;drowOpenStatusCustomerChart(pieChartDataSource,"graph-sr-openstatus-customer");drowOpenStatusCustomerChart(pieChartDataSource,"graph-sr-openstatus-customer1")};function drowOpenStatusCustomerChart(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:3},series:{argumentField:'category',valueField:'value',},tooltip:{enabled:!0,customizeText:function(e){return e.percentText}}})}
</script>	