<div class="bcontenu">
<div class="style_chart700 graph" id="graph-project-alerts" ></div>
</div>
<?php $id = WidgetProjectsAlerts::getId();
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
			<div class="title"><?php echo  WidgetProjectsAlerts::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div> 	<div class="style_chart1000 graph" id="graph-project-alerts1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function changeProjectAlerts(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ProjectAlerts');?>",dataType:"json",success:function(data){if(data){var pieChartDataSource=data;drowChartPA(pieChartDataSource,"graph-project-alerts");drowChartPA(pieChartDataSource,"graph-project-alerts1")}}})}
function drowChartPA(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:5},series:{argumentField:'category',valueField:'value',},tooltip:{enabled:!0,customizeText:function(){return this.argumentText+" ("+this.valueText+")"}}})}
</script>	