<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_resource_b_3 resource_billable" data-id ="3" onClick="changeResourceBillable(3)"><i> Current Month / </i></span>
 			<span class="status status_resource_b_2 resource_billable" data-id ="2" onClick="changeResourceBillable(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_resource_b_1 resource_billable" data-id ="1" onClick="changeResourceBillable(1)"><i> Last 6 Months </i></span>
 			<span class="status top_resource_b_3 top_resc" data-id ="3" style="float:right;padding-right:20px;" onClick="changeTopBResc(3)"><i>15</i></span>
      <span class="status top_resource_b_2 top_resc" data-id ="2" style="float:right" onClick="changeTopBResc(2)"><i>10 /</i></span>
      <span class="status top_resource_b_1 top_resc" data-id ="1" style="float:right" onClick="changeTopBResc(1)"><i>Top 5 /</i></span>
 	</div> 	<div class="style_chart700 graph" id="graph-resource-billable" ></div>
</div>
<?php $id = WidgetResourceBillable::getId();
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
			<div class="title"><?php echo  WidgetResourceBillable::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_resource_b_3 resource_billable " onClick="changeResourceBillable(3)"><i> Current Month / </i></span>
 			<span class="status status_resource_b_2 resource_billable" onClick="changeResourceBillable(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_resource_b_1 resource_billable" onClick="changeResourceBillable(1)"><i> Last 6 Months  </i></span>
 	  <span class="status top_resource_b_3 top_resc" data-id ="3" style="float:right;padding-right:20px;" onClick="changeTopBResc(3)"><i>15</i></span>
      <span class="status top_resource_b_2 top_resc" data-id ="2" style="float:right" onClick="changeTopBResc(2)"><i>10 /</i></span>
      <span class="status top_resource_b_1 top_resc" data-id ="1" style="float:right" onClick="changeTopBResc(1)"><i>Top 5 /</i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-resource-billable1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">function CreateGridRescBill(){val=<?php print_r(WidgetResourceBillable::CharChart());?>;drowRescBill(val,"graph-resource-billable");drowRescBill(val,"graph-resource-billable1");$('.status_resource_b_1').addClass("colorRed");$('.top_resource_b_1').addClass("colorRed")}
function drowRescBill(val,id){var dataSource=val;var chart=$("#"+id).dxChart({dataSource:dataSource,rotated:!0,commonSeriesSettings:{argumentField:"label",type:"bar"},series:{valueField:"value",name:"Actual Billable MDs",color:"#15157e",selectionStyle:{hatching:"none"}},legend:{visible:!0,verticalAlignment:"bottom",horizontalAlignment:"center",itemTextPosition:'right'},pointClick:function(point){point.isSelected()?point.clearSelection():point.select()},tooltip:{enabled:!0,customizeText:function(){return this.seriesName+" : "+this.valueText+" %"}}}).dxChart("instance")}
function changeResourceBillable(valbill){$.ajax({type:"POST",data:{'valbill':valbill},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/rescourceBarBillable');?>",dataType:"json",success:function(data){if(data){$('.resource_billable').removeClass("colorRed");var pieChartDataSource=data;drowRescBill(pieChartDataSource,"graph-resource-billable");drowRescBill(pieChartDataSource,"graph-resource-billable1");$('.status_resource_b_'+valbill).addClass("colorRed")}}})}
function changeTopBResc(topbill){$.ajax({type:"POST",data:{'topbill':topbill},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/rescourceBarBillable');?>",dataType:"json",success:function(data){if(data){$('.top_resc').removeClass("colorRed");var pieChartDataSource=data;drowRescBill(pieChartDataSource,"graph-resource-billable");drowRescBill(pieChartDataSource,"graph-resource-billable1");$('.top_resource_b_'+topbill).addClass("colorRed")}}})}
</script>	