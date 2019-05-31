<div class="bcontenu">    <div id="pieChartContainerRsrcPrd" style="padding-top:10px;"  class="style_chart700 graph"></div></div>
<?php $id = WidgetResourceProductivityPie::getId();
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
		'closeOnEscape' => true, ),));?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetResourceProductivityPie::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div> 	<div id="pieChartContainerRsrcPrd1" style="padding-top:10px;" class="style_chart1000 graph"></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">function createGridRsrcPrd(){var pieChartDataSource=<?php echo WidgetResourceProductivityPie::getResources()?>;drowTimeChart(pieChartDataSource,"pieChartContainerRsrcPrd");drowTimeChart(pieChartDataSource,"pieChartContainerRsrcPrd1")}
function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")}
function drowTimeChart(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:2},series:{argumentField:'Resource',valueField:'value'},tooltip:{enabled:!0,customizeText:function(e){return numberWithCommas(e.value)}}})}
</script>	