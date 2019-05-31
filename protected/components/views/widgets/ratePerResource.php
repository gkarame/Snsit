<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_rate_support_1 sr_rate" data-id ="1" onClick="changeMonthrate(1)"><i> Current Month / </i></span>
 			<span class="status status_rate_support_2 sr_rate" data-id ="2" onClick="changeMonthrate(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_rate_support_3 sr_rate" data-id ="3" onClick="changeMonthrate(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_rate_support_4 sr_rate" data-id ="4" onClick="changeMonthrate(4)"><i> Last Year</i></span>
 	</div> 	<div class="style_chart700 graph" id="graph-rate" ></div>
</div><?php $id = WidgetRatePerResource::getId();
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
			<div class="title"><?php echo  WidgetRatePerResource::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_rate_support_1 sr_rate" onClick="changeMonthrate(1)"><i> Current Month / </i></span>
 			<span class="status status_rate_support_2 sr_rate" onClick="changeMonthrate(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_rate_support_3 sr_rate" onClick="changeMonthrate(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_rate_support_4 sr_rate" onClick="changeMonthrate(4)"><i> Last Year</i></span>
 	</div> 	<div class="graph style_chart1000" id="graph-rate1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridRate(){$('.status_rate_support_2').addClass("colorRed");val=<?php echo WidgetRatePerResource::CharChart1()?>;ChartSupport(val,"graph-rate");ChartSupport(val,"graph-rate1")};function ChartSupport(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:"spline",argumentField:"month"},commonAxisSettings:{grid:{visible:!0,},},series:<?php echo WidgetRatePerResource::getSeries();?>,tooltip:{enabled:!0},legend:{verticalAlignment:"bottom",horizontalAlignment:"center",rowCount:4},valueAxis:{max:100,name:'Value Axis 1',valueType:"numeric"},commonPaneSettings:{border:{visible:!0,bottom:!1}}})}
function changeMonthrate(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/srRate');?>",dataType:"json",success:function(data){if(data){$('.sr_rate').removeClass("colorRed");var pieChartDataSource=data;ChartSupport(pieChartDataSource,"graph-rate");ChartSupport(pieChartDataSource,"graph-rate1");$('.status_rate_support_'+val).addClass("colorRed")}}})}
</script>	