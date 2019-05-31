<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_srRes_1 sr_closeRes" data-id ="1" onClick="changeMonthCloseRes(1)"><i> Current Month / </i></span>
 			<span class="status status_srRes_2 sr_closeRes" data-id ="2" onClick="changeMonthCloseRes(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_srRes_3 sr_closeRes" data-id ="3" onClick="changeMonthCloseRes(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_srRes_4 sr_closeRes" data-id ="4" onClick="changeMonthCloseRes(4)"><i> Last Year</i></span>
 	</div> 	<div class="style_chart700 graph" id="graph-sr-close-resource" ></div> 	
</div>
<?php $id = WidgetSrCloseResource::getId();
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
			<div class="title"><?php echo  WidgetSrCloseResource::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_srRes_1 sr_closeRes" onClick="changeMonthCloseRes(1)"><i> Current Month / </i></span>
 			<span class="status status_srRes_2 sr_closeRes" onClick="changeMonthCloseRes(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_srRes_3 sr_closeRes" onClick="changeMonthCloseRes(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_srRes_4 sr_closeRes" onClick="changeMonthCloseRes(4)"><i> Last Year</i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-sr-close-resource1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function drowChartClosedByResource(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:5},series:{argumentField:'category',valueField:'value',tagField:'label',},tooltip:{enabled:!0,customizeText:function(e){return e.point.tag+' '+e.percentText}}})}
function changeMonthCloseRes(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/srBarSortResource');?>",dataType:"json",success:function(data){if(data){$('.sr_closeRes').removeClass("colorRed");var pieChartDataSource=data;drowChartClosedByResource(pieChartDataSource,"graph-sr-close-resource");drowChartClosedByResource(pieChartDataSource,"graph-sr-close-resource1");$('.status_srRes_'+val).addClass("colorRed")}}})}
</script>	