<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_sr_rsr_3 rsr_sr" data-id ="3" onClick="changeRsrVsSr(3)"><i> Current Month / </i></span>
 			<span class="status status_sr_rsr_2 rsr_sr" data-id ="2" onClick="changeRsrVsSr(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_rsr_1 rsr_sr" data-id ="1" onClick="changeRsrVsSr(1)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_rsr_4 rsr_sr" data-id ="4" onClick="changeRsrVsSr(4)"><i> Last Year </i></span>
</div>	<div class="style_chart700 graph" id="graph-rsrvssr" ></div> 	
</div>
<?php $id = WidgetRsrvsSr::getId();
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
			<div class="title"><?php echo  WidgetRsrvsSr::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_sr_rsr_3 rsr_sr" onClick="changeRsrVsSr(3)"><i> Current Month / </i></span>
 			<span class="status status_sr_rsr_2 rsr_sr" onClick="changeRsrVsSr(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_rsr_1 rsr_sr" onClick="changeRsrVsSr(1)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_rsr_4 rsr_sr" onClick="changeRsrVsSr(4)"><i> Last Year</i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-rsrvssr1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridRsrVsSr(){
	var pieChartDataSource=<?php echo WidgetRsrvsSr::CharChart();?>;
	srRsrBar(pieChartDataSource,1);
	srRsrBar(pieChartDataSource,0);$
	('.status_sr_rsr_1').addClass("colorRed")
};

function srRsrBar(pieChartDataSource,val){$(function(){
	if(val==1){id="graph-rsrvssr"}
	else{id="graph-rsrvssr1"}
$("#"+id).dxChart({dataSource:pieChartDataSource,commonSeriesSettings:{argumentField:"state",type:"bar",hoverMode:"allArgumentPoints",selectionMode:"allArgumentPoints",label:{visible:!0,format:"fixedPoint",precision:1}},legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:1,visible:!0,},series:[{argumentField:"label",valueField:"value",color:'#ffa500',name:'RSR'},{argumentField:"label",valueField:"value2",color:'#cf32cf',name:'SR'}],valueAxis:{title:{text:"Days"}},argumentAxis:{title:'Months',type:'discrete',grid:{visible:!0}}})})}
function changeRsrVsSr(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrVsRsrBarSort');?>",dataType:"json",success:function(data){if(data){$('.rsr_sr').removeClass("colorRed");var pieChartDataSource=data;srRsrBar(pieChartDataSource,1);srRsrBar(pieChartDataSource,0);$('.status_sr_rsr_'+val).addClass("colorRed")}}})}
</script>	