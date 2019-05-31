<div class="bcontenu">
    <div id="pieChartContainerInvByPart" style="padding-top:10px;" class="style_chart700 graph"></div>
</div>
<?php $id = WidgetPendingInvoicesByPartner::getId();
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
			<div class="title"><?php echo  WidgetPendingInvoicesByPartner::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div> 	<div id="pieChartContainerInvByPart1" style="padding-top:10px;" class="style_chart1000 graph"></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<script type="text/javascript">
function createGridInvByPart(){var pieChartDataSource=<?php echo WidgetPendingInvoicesByPartner::getAmounts()?>;drowTimeChart1(pieChartDataSource,"pieChartContainerInvByPart");drowTimeChart1(pieChartDataSource,"pieChartContainerInvByPart1")}
function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")}
function drowTimeChart1(e,t){$("#"+t).dxPieChart({dataSource:e,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:3},series:{argumentField:"Partner",valueField:"value"},tooltip:{enabled:!0,customizeText:function(e){return numberWithCommas(e.value)+" $ - "+ e.percentText }}})}</script>	