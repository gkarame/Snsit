<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_sr_sub_3 sr_submitted" data-id ="3" onClick="changeMonthSubmitted(3)"><i> Current Month / </i></span>
 			<span class="status status_sr_sub_2 sr_submitted" data-id ="2" onClick="changeMonthSubmitted(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_sub_1 sr_submitted" data-id ="1" onClick="changeMonthSubmitted(1)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_sub_4 sr_submitted" data-id ="4" onClick="changeMonthSubmitted(4)"><i> Last Year </i></span>
</div>	<div class="style_chart700 graph" id="graph-sr-submitted" ></div> 	
</div>
<?php $id = WidgetSrSubmitted::getId();
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
			<div class="title"><?php echo  WidgetSrSubmitted::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_sr_sub_3 sr_submitted" onClick="changeMonthSubmitted(3)"><i> Current Month / </i></span>
 			<span class="status status_sr_sub_2 sr_submitted" onClick="changeMonthSubmitted(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_sub_1 sr_submitted" onClick="changeMonthSubmitted(1)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_sub_4 sr_submitted" onClick="changeMonthSubmitted(4)"><i> Last Year</i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-sr-submitted1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridSubMonth(){var pieChartDataSource=<?php echo WidgetSrSubmitted::CharChart();?>;bar1(pieChartDataSource,1);bar1(pieChartDataSource,0);$('.status_sr_sub_1').addClass("colorRed")};function bar1(pieChartDataSource,val){$(function(){if(val==1){id="graph-sr-submitted"}
else{id="graph-sr-submitted1"}
$("#"+id).dxChart({dataSource:pieChartDataSource,commonSeriesSettings:{argumentField:"state",type:"bar",hoverMode:"allArgumentPoints",selectionMode:"allArgumentPoints",label:{visible:!0,format:"fixedPoint",precision:0}},legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:1,visible:!0,},series:[{argumentField:"label",valueField:"value",color:'#ffa500',name:'Submitted'},{argumentField:"label",valueField:"value2",color:'#cf32cf',name:'Closed'}],valueAxis:{title:{text:"SRs"}},argumentAxis:{title:'Months',type:'discrete',grid:{visible:!0}}})})}
function changeMonthSubmitted(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrBarSortSubmitted');?>",dataType:"json",success:function(data){if(data){$('.sr_submitted').removeClass("colorRed");var pieChartDataSource=data;bar1(pieChartDataSource,1);bar1(pieChartDataSource,0);$('.status_sr_sub_'+val).addClass("colorRed")}}})}
</script>	