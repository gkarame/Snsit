<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_resource_nb_3 resource_non_billable" data-id ="3" onClick="changeResourceNonBillable(3)"><i> Current Month / </i></span>
 			<span class="status status_resource_nb_2 resource_non_billable" data-id ="2" onClick="changeResourceNonBillable(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_resource_nb_1 resource_non_billable" data-id ="1" onClick="changeResourceNonBillable(1)"><i> Last 6 Months </i></span>
 			<span class="status top_resource_nb_3 top_nresc" data-id ="3" style="float:right;padding-right:20px;" onClick="changeTopNBResc(3)"><i>15</i></span>
      <span class="status top_resource_nb_2 top_nresc" data-id ="2" style="float:right" onClick="changeTopNBResc(2)"><i>10 /</i></span>
      <span class="status top_resource_nb_1 top_nresc" data-id ="1" style="float:right" onClick="changeTopNBResc(1)"><i> Top 5 /</i></span>
  	</div> 	<div class="style_chart700 graph" id="graph-resource-non-billable" ></div>
</div>
<?php $id = WidgetResourceNonBillable::getId();
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
			<div class="title"><?php echo  WidgetResourceNonBillable::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_resource_nb_3 resource_non_billable " onClick="changeResourceNonBillable(3)"><i> Current Month / </i></span>
 			<span class="status status_resource_nb_2 resource_non_billable" onClick="changeResourceNonBillable(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_resource_nb_1 resource_non_billable" onClick="changeResourceNonBillable(1)"><i> Last 6 Months  </i></span>
 	    <span class="status top_resource_nb_3 top_nresc" data-id ="3" style="float:right;padding-right:20px;" onClick="changeTopNBResc(3)"><i>15</i></span>
      <span class="status top_resource_nb_2 top_nresc" data-id ="2" style="float:right" onClick="changeTopNBResc(2)"><i>10 /</i></span>
      <span class="status top_resource_nb_1 top_nresc" data-id ="1" style="float:right" onClick="changeTopNBResc(1)"><i>Top 5 /</i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-resource-non-billable1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function CreateGridRescNonBill(){val=<?php print_r(WidgetResourceNonBillable::CharChart());?>;drowRescNonBill(val,"graph-resource-non-billable");drowRescNonBill(val,"graph-resource-non-billable1");$('.status_resource_nb_1').addClass("colorRed");$('.top_resource_nb_1').addClass("colorRed")}
function drowRescNonBill(val,id){var dataSource=val;var chart=$("#"+id).dxChart({dataSource:dataSource,rotated:!0,commonSeriesSettings:{argumentField:"label",type:"bar"},series:{valueField:"value",name:"Actual Non Billable MDs",color:"#FFB505",selectionStyle:{hatching:"none"}},legend:{visible:!0,verticalAlignment:"bottom",horizontalAlignment:"center",itemTextPosition:'right'},pointClick:function(point){point.isSelected()?point.clearSelection():point.select()},tooltip:{enabled:!0,customizeText:function(){return this.seriesName+" : "+this.valueText+" %"}}}).dxChart("instance")}
function changeResourceNonBillable(valnobill){$.ajax({type:"POST",data:{'valnobill':valnobill},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/rescourceBarNonBillable');?>",dataType:"json",success:function(data){if(data){$('.resource_non_billable').removeClass("colorRed");var pieChartDataSource=data;drowRescNonBill(pieChartDataSource,"graph-resource-non-billable");drowRescNonBill(pieChartDataSource,"graph-resource-non-billable1");$('.status_resource_nb_'+valnobill).addClass("colorRed")}}})}
function changeTopNBResc(topnobill){$.ajax({type:"POST",data:{'topnobill':topnobill},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/rescourceBarNonBillable');?>",dataType:"json",success:function(data){if(data){$('.top_nresc').removeClass("colorRed");var pieChartDataSource=data;drowRescNonBill(pieChartDataSource,"graph-resource-non-billable");drowRescNonBill(pieChartDataSource,"graph-resource-non-billable1");$('.top_resource_nb_'+topnobill).addClass("colorRed")}}})}
</script>	