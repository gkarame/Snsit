<div class="bcontenu">
 	<div class="stat_years">
		<span class="status status_rsr_avg_1 rsr_avg_rec" data-id ="1" onClick="changeRsrRec(1)"><i> Current Month /  </i></span>
      <span class="status status_rsr_avg_2 rsr_avg_rec" data-id ="1" onClick="changeRsrRec(2)"><i> Last 3 Months / </i></span>
      <span class="status status_rsr_avg_3 rsr_avg_rec" data-id ="1" onClick="changeRsrRec(3)"><i> Last 6 Months / </i></span>
      <span class="status status_rsr_avg_4 rsr_avg_rec" data-id ="1" onClick="changeRsrRec(4)"><i> Last Year </i></span>
  </div>	<div class="style_chart700 graph" id="graph-rsr-average-resource" ></div>
</div>
<?php $id = WidgetRsrPerResource::getId();
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
			<div class="title"><?php echo  WidgetRsrPerResource::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 	<span class="status status_rsr_avg_1 rsr_avg_rec" data-id ="1" onClick="changeRsrRec(1)"><i> Current Month /  </i></span>
      <span class="status status_rsr_avg_2 rsr_avg_rec" data-id ="1" onClick="changeRsrRec(2)"><i> Last 3 Months / </i></span>
      <span class="status status_rsr_avg_3 rsr_avg_rec" data-id ="1" onClick="changeRsrRec(3)"><i> Last 6 Months / </i></span>
      <span class="status status_rsr_avg_4 rsr_avg_rec" data-id ="1" onClick="changeRsrRec(4)"><i> Last Year </i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-rsr-average-resource1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
//if($('#graph-rsr-average-resource1').is(':visible')){CreateGridAvgResourceRsr()}
function CreateGridAvgResourceRsr(){val=<?php print_r(WidgetRsrPerResource::CharChart1());?>;drowAvgResRsr(val,"graph-rsr-average-resource");drowAvgResRsr(val,"graph-rsr-average-resource1");$('.status_rsr_avg_3').addClass("colorRed")}
function drowAvgResRsr(val,id){var dataSource=val;var chart=$("#"+id).dxChart({dataSource:dataSource,rotated:!0,commonSeriesSettings:{argumentField:"label",type:"bar"},series:{valueField:"value",name:"Days",color:"#FFB505",selectionStyle:{hatching:"none"}},legend:{visible:!0,verticalAlignment:"bottom",horizontalAlignment:"center",itemTextPosition:'right'},pointClick:function(point){point.isSelected()?point.clearSelection():point.select()},tooltip:{enabled:!0,customizeText:function(){return this.seriesName+" : "+this.valueText}}}).dxChart("instance")}
function changeRsrRec(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/changeRsrAvgRec');?>",dataType:"json",success:function(data){if(data){$('.rsr_avg_rec').removeClass("colorRed");var pieChartDataSource=data;drowAvgResRsr(pieChartDataSource,"graph-rsr-average-resource");drowAvgResRsr(pieChartDataSource,"graph-rsr-average-resource1");$('.status_rsr_avg_'+val).addClass("colorRed")}}})}
</script>	