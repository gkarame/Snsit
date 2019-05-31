<div class="bcontenu">
 	<div class="stat_years">
		<span class="status status_sr_avg_1 sr_avg_rec" data-id ="1" onClick="changeSrRec(1)"><i> Current Month /  </i></span>
      <span class="status status_sr_avg_2 sr_avg_rec" data-id ="1" onClick="changeSrRec(2)"><i> Last 3 Months / </i></span>
      <span class="status status_sr_avg_3 sr_avg_rec" data-id ="1" onClick="changeSrRec(3)"><i> Last 6 Months / </i></span>
      <span class="status status_sr_avg_4 sr_avg_rec" data-id ="1" onClick="changeSrRec(4)"><i> Last Year </i></span>
  </div>	<div class="style_chart700 graph" id="graph-sr-average-resource" ></div>
</div>
<?php $id = WidgetSrPerResource::getId();
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
			<div class="title"><?php echo  WidgetSrPerResource::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 	<span class="status status_sr_avg_1 sr_avg_rec" data-id ="1" onClick="changeSrRec(1)"><i> Current Month /  </i></span>
      <span class="status status_sr_avg_2 sr_avg_rec" data-id ="1" onClick="changeSrRec(2)"><i> Last 3 Months / </i></span>
      <span class="status status_sr_avg_3 sr_avg_rec" data-id ="1" onClick="changeSrRec(3)"><i> Last 6 Months / </i></span>
      <span class="status status_sr_avg_4 sr_avg_rec" data-id ="1" onClick="changeSrRec(4)"><i> Last Year </i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-sr-average-resource1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
if($('#graph-sr-average-resource1').is(':visible')){CreateGridAvgResource()}
function CreateGridAvgResource(){val=<?php print_r(WidgetSrPerResource::CharChart1());?>;drowAvgRes(val,"graph-sr-average-resource");drowAvgRes(val,"graph-sr-average-resource1");$('.status_sr_avg_3').addClass("colorRed")}
function drowAvgRes(val,id){var dataSource=val;var chart=$("#"+id).dxChart({dataSource:dataSource,rotated:!0,commonSeriesSettings:{argumentField:"label",type:"bar"},series:{valueField:"value",name:"Days",color:"#FFB505",selectionStyle:{hatching:"none"}},legend:{visible:!0,verticalAlignment:"bottom",horizontalAlignment:"center",itemTextPosition:'right'},pointClick:function(point){point.isSelected()?point.clearSelection():point.select()},tooltip:{enabled:!0,customizeText:function(){return this.seriesName+" : "+this.valueText}}}).dxChart("instance")}
function changeSrRec(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/changeSrAvgRec');?>",dataType:"json",success:function(data){if(data){$('.sr_avg_rec').removeClass("colorRed");var pieChartDataSource=data;drowAvgRes(pieChartDataSource,"graph-sr-average-resource");drowAvgRes(pieChartDataSource,"graph-sr-average-resource1");$('.status_sr_avg_'+val).addClass("colorRed")}}})}
</script>	