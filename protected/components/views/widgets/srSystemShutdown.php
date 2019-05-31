<div class="bcontenu">
 	<div class="stat_years">
 			<?php if(Yii::app()->user->isAdmin){?>
 			<span class="status status_srsystemdown_3 sr_systemdown" data-id="3" onClick="changeMonthSystemShutdown(3)"><i> Current Month / </i></span>
 			<span class="status status_srsystemdown_2 sr_systemdown" data-id="2" onClick="changeMonthSystemShutdown(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_srsystemdown_1 sr_systemdown" data-id="1" onClick="changeMonthSystemShutdown(1)"><i> Last 6 Months </i></span>
 			<?php } else{?>
 			<span class="status status_srsystemdown_3 sr_systemdown" data-id="3" onClick="changeMonthSystemShutdown(3)"><i> Current Month / </i></span>
 			<span class="status status_srsystemdown_1 sr_systemdown" data-id="1" onClick="changeMonthSystemShutdown(1)"><i> Last 3 Months /</i></span>
 			<span class="status status_srsystemdown_2 sr_systemdown" data-id="2" onClick="changeMonthSystemShutdown(2)"><i> Last 6 Months </i></span>
 			<?php } ?>
 	</div> 	<div class="style_chart700 graph" id="graph-sr-system-down" ></div>
</div>
<?php $id = WidgetSrSystemShutdown::getId();
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
			<div class="title"><?php echo  WidgetSrSystemShutdown::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<?php if(Yii::app()->user->isAdmin){?>
 			<span class="status status_srsystemdown_3 sr_systemdown" onClick="changeMonthSystemShutdown(3)"><i> Current Month / </i></span>
 			<span class="status status_srsystemdown_2 sr_systemdown" onClick="changeMonthSystemShutdown(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_srsystemdown_1 sr_systemdown" onClick="changeMonthSystemShutdown(1)"><i> Last 6 Months </i></span>
 			<?php } else{?>
 			<span class="status status_srsystemdown_3 sr_systemdown" onClick="changeMonthSystemShutdown(3)"><i> Current Month / </i></span>
 			<span class="status status_srsystemdown_1 sr_systemdown" onClick="changeMonthSystemShutdown(1)"><i> Last 3 Months /</i></span>
 			<span class="status status_srsystemdown_2 sr_systemdown" onClick="changeMonthSystemShutdown(2)"><i> Last 6 Months </i></span>
 			<?php }?>
 	</div> 	<div class="style_chart1000 graph" id="graph-sr-system-down1" ></div> 	
</div><?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridStackSystemDown(){$('.status_srsystemdown_1').addClass("colorRed");val=<?php echo WidgetSrSystemShutdown::CharChart1()?>;StackedBarSystemShutDown(val,"graph-sr-system-down");StackedBarSystemShutDown(val,"graph-sr-system-down1")}
function StackedBarSystemShutDown(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{argumentField:"state",type:"bar"},series:[{valueField:"value",name:"System Down",color:"#8b0000"},{valueField:"escalate",name:"Escalated",color:"#191970"},],legend:{verticalAlignment:"bottom",horizontalAlignment:"center",itemTextPosition:'right'},valueAxis:{title:{text:"SRs"},position:"left"},tooltip:{enabled:!0,customizeText:function(){return this.seriesName+" : "+this.valueText}}})}
function changeMonthSystemShutdown(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrBarSortSystemShutdown');?>",dataType:"json",success:function(data){if(data){$('.sr_systemdown').removeClass("colorRed");var vall=data;StackedBarSystemShutDown(vall,"graph-sr-system-down");StackedBarSystemShutDown(vall,"graph-sr-system-down1");$('.status_srsystemdown_'+val).addClass("colorRed")}}})}
</script>	