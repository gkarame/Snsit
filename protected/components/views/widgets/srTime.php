<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_sr_time_2 sr_time" data-id ="2" onClick="changeTime(2)"><i> Current Month / </i></span>
 			<span class="status status_sr_time_1 sr_time" data-id ="1" onClick="changeTime(1)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_time_3 sr_time" data-id ="3" onClick="changeTime(3)"><i> Last 6 Months  </i></span> 			
 	</div>    <div id="pieChartContainerTime" class="style_chart700 graph"></div> 	
</div>
<?php $id = WidgetTime::getId();
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
			<div class="title"><?php echo  WidgetTime::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_sr_time_2 sr_time" onClick="changeTime(2)"><i> Current Month / </i></span>
 			<span class="status status_sr_time_1 sr_time" onClick="changeTime(1)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_time_3 sr_time" onClick="changeTime(3)"><i> Last 6 Months </i></span>
 	</div>
 	<div id="pieChartContainerTime1" class="style_chart1000 graph"></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridTime(){$('.status_sr_time_3').addClass("colorRed");var pieChartDataSource=<?php echo WidgetTime::CharChart()?>;drowTimeChart4(pieChartDataSource,"pieChartContainerTime");drowTimeChart4(pieChartDataSource,"pieChartContainerTime1")}
function drowTimeChart4(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:4},series:{argumentField:'category',valueField:'value'},tooltip:{enabled:!0,customizeText:function(e){return e.percentText}}})}
function changeTime(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/srTime');?>",dataType:"json",success:function(data){if(data){$('.sr_time').removeClass("colorRed");var pieChartDataSource=data;drowTimeChart4(pieChartDataSource,"pieChartContainerTime");drowTimeChart4(pieChartDataSource,"pieChartContainerTime1");$('.status_sr_time_'+val).addClass("colorRed")}}})}
</script>	