<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_agev_1 sr_agev" data-id ="1" onClick="changeMonthagev(1)"><i> Current Month / </i></span>
 			<span class="status status_agev_2 sr_agev" data-id ="2" onClick="changeMonthagev(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_agev_3 sr_agev" data-id ="3" onClick="changeMonthagev(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_agev_4 sr_agev" data-id ="4" onClick="changeMonthagev(4)"><i> Last Year</i></span>
 	</div>
 	<div class="style_chart700 graph" id="graph-agev" ></div>
</div>
<?php $id = WidgetAgeVariance::getId();
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
    ),
));?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetAgeVariance::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_agev_1 sr_agev" onClick="changeMonthagev(1)"><i> Current Month / </i></span>
 			<span class="status status_agev_2 sr_agev" onClick="changeMonthagev(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_agev_3 sr_agev" onClick="changeMonthagev(3)"><i> Last 6 Months /</i></span>
 			<span class="status status_agev_4 sr_agev" onClick="changeMonthagev(4)"><i> Last Year</i></span>
 	</div>
 	<div class="graph style_chart1000" id="graph-agev1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<script type="text/javascript">
function createGridAvgAge(){$('.status_agev_2').addClass("colorRed");val=<?php echo WidgetAgeVariance::CharChartAge();?>;Chartagev(val,"graph-agev");Chartagev(val,"graph-agev1");};function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");}
function Chartagev(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:"spline",argumentField:"month"},commonAxisSettings:{grid:{visible:true}},series:<?php echo WidgetAgeVariance::getSeries();?>,tooltip:{enabled:true,customizeText:function(e){return numberWithCommas(e.value);}},legend:{verticalAlignment:"bottom",horizontalAlignment:"center"},commonPaneSettings:{border:{visible:true,bottom:false}}});}
function changeMonthagev(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/MonthlyAgeAvg');?>",dataType:"json",success:function(data){if(data){$('.sr_agev').removeClass("colorRed");var pieChartDataSource=data;Chartagev(pieChartDataSource,"graph-agev");Chartagev(pieChartDataSource,"graph-agev1");$('.status_agev_'+val).addClass("colorRed");}}});}
</script>	