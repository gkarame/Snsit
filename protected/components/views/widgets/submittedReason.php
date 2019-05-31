<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_sr_subReas_2 sr_submitted_reas" data-id ="1" onClick="changeSubmittedReason(2)"><i> Current Month / </i></span>
 			<span class="status status_sr_subReas_1 sr_submitted_reas" data-id ="2" onClick="changeSubmittedReason(1)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_subReas_3 sr_submitted_reas" data-id ="3" onClick="changeSubmittedReason(3)"><i> Last 6 Months / </i></span>
 			<span class="status status_sr_subReas_4 sr_submitted_reas" data-id ="4" onClick="changeSubmittedReason(4)"><i> Last Year </i></span>
 	</div> 	<div class="style_chart700 graph" id="graph-sr-submitted-reason" ></div> 	
</div>
<?php $id = WidgetSubmittedReason::getId();
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
			<div class="title"><?php echo  WidgetSubmittedReason::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_sr_subReas_2 sr_submitted_reas " onClick="changeSubmittedReason(2)"><i> Current Month / </i></span>
 			<span class="status status_sr_subReas_1 sr_submitted_reas" onClick="changeSubmittedReason(1)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_subReas_3 sr_submitted_reas" onClick="changeSubmittedReason(3)"><i> Last 6 Months / </i></span>
 			<span class="status status_sr_subReas_4 sr_submitted_reas" onClick="changeSubmittedReason(4)"><i> Last Year </i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-sr-submitted-reason1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function CreateGridSubReason(){val=<?php print_r(WidgetSubmittedReason::CharChart1());?>;drowSubReason(val,"graph-sr-submitted-reason");drowSubReason(val,"graph-sr-submitted-reason1");$('.status_sr_subReas_1').addClass("colorRed")}
function drowSubReason(val,id){var dataSource=val;var chart=$("#"+id).dxChart({
		dataSource:dataSource,rotated:!0,
		commonSeriesSettings:{argumentField:"label",type:"bar"},
		series:{valueField:"value",name:"val",color:"#FFB505",selectionStyle:{hatching:"none"}},
		legend:{visible:!1,},
		pointClick:function(point){			point.isSelected()?point.clearSelection():point.select()},
			tooltip:{enabled:!0,customizeText:function(){return this.seriesName+" : "+this.valueText}}}).dxChart("instance")}
function changeSubmittedReason(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/srBarSortReason');?>",dataType:"json",success:function(data){if(data){$('.sr_submitted_reas').removeClass("colorRed");var pieChartDataSource=data;drowSubReason(pieChartDataSource,"graph-sr-submitted-reason");drowSubReason(pieChartDataSource,"graph-sr-submitted-reason1");$('.status_sr_subReas_'+val).addClass("colorRed")}}})}
</script>	