<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_support_1 sr_support" data-id ="1" onClick="changeMonthSupport(1)"><i> Current Month / </i></span>
 			<span class="status status_support_2 sr_support" data-id ="2" onClick="changeMonthSupport(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_support_3 sr_support" data-id ="3" onClick="changeMonthSupport(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_support_4 sr_support" data-id ="4" onClick="changeMonthSupport(4)"><i> Last Year</i></span>
 	</div> 	<div class="style_chart700 graph" id="graph-support" ></div>
</div>
<?php $id = WidgetSupport::getId();
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
			<div class="title"><?php echo  WidgetSupport::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_support_1 sr_support" onClick="changeMonthSupport(1)"><i> Current Month / </i></span>
 			<span class="status status_support_2 sr_support" onClick="changeMonthSupport(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_support_3 sr_support" onClick="changeMonthSupport(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_support_4 sr_support" onClick="changeMonthSupport(4)"><i> Last Year</i></span>
 	</div> 	<div class="graph style_chart1000" id="graph-support1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridSupport(){$('.status_support_2').addClass("colorRed");val=<?php echo WidgetSupport::CharChart1()?>;ChartSupport(val,"graph-support");ChartSupport(val,"graph-support1")};function ChartSupport(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:"spline",argumentField:"month"},commonAxisSettings:{grid:{visible:!0}},series:<?php echo WidgetSupport::getSeries();?>,tooltip:{enabled:!0},legend:{verticalAlignment:"bottom",horizontalAlignment:"center"},commonPaneSettings:{border:{visible:!0,bottom:!1}}})}
function changeMonthSupport(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/srSupport');?>",dataType:"json",success:function(data){if(data){$('.sr_support').removeClass("colorRed");var pieChartDataSource=data;ChartSupport(pieChartDataSource,"graph-support");ChartSupport(pieChartDataSource,"graph-support1");$('.status_support_'+val).addClass("colorRed")}}})}
</script>	