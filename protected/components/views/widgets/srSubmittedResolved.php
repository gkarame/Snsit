<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_submittedresolved_1 sr_submittedresolved" data-id ="1" onClick="changeMonthsubmittedresolved(1)"><i> Current Month / </i></span>
 			<span class="status status_submittedresolved_2 sr_submittedresolved" data-id ="2" onClick="changeMonthsubmittedresolved(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_submittedresolved_3 sr_submittedresolved" data-id ="3" onClick="changeMonthsubmittedresolved(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_submittedresolved_4 sr_submittedresolved" data-id ="4" onClick="changeMonthsubmittedresolved(4)"><i> Last Year</i></span>
 	</div> 	<div class="style_chart700 graph" id="graph-submittedresolved" ></div>
</div>
<?php $id = WidgetSrSubmittedResolved::getId();
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
			<div class="title"><?php echo  WidgetSrSubmittedResolved::getName();?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_submittedresolved_1 sr_submittedresolved" onClick="changeMonthsubmittedresolved(1)"><i> Current Month / </i></span>
 			<span class="status status_submittedresolved_2 sr_submittedresolved" onClick="changeMonthsubmittedresolved(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_submittedresolved_3 sr_submittedresolved" onClick="changeMonthsubmittedresolved(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_submittedresolved_4 sr_submittedresolved" onClick="changeMonthsubmittedresolved(4)"><i> Last Year</i></span>
 	</div> 	<div class="graph style_chart1000" id="graph-submittedresolved1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridsubmittedresolved(){$('.status_submittedresolved_2').addClass("colorRed");val=<?php echo WidgetSrSubmittedResolved::CharChart1()?>;Chartsubmittedresolved(val,"graph-submittedresolved");Chartsubmittedresolved(val,"graph-submittedresolved1")};function Chartsubmittedresolved(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:"spline",argumentField:"month"},commonAxisSettings:{grid:{visible:!0}},series:<?php echo WidgetSrSubmittedResolved::getSeries();?>,tooltip:{enabled:!0},legend:{verticalAlignment:"bottom",horizontalAlignment:"center"},commonPaneSettings:{border:{visible:!0,bottom:!1}}})}
function changeMonthsubmittedresolved(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrSubmittedResolved');?>",dataType:"json",success:function(data){if(data){$('.sr_submittedresolved').removeClass("colorRed");var pieChartDataSource=data;Chartsubmittedresolved(pieChartDataSource,"graph-submittedresolved");Chartsubmittedresolved(pieChartDataSource,"graph-submittedresolved1");$('.status_submittedresolved_'+val).addClass("colorRed")}}})}
</script>	