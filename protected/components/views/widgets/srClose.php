<div class="bcontenu">
 	<div class="stat_years">
 			<?php if(Yii::app()->user->isAdmin){?>
 			<span class="status status_sr_3 sr_close" data-id="3" onClick="changeMonthClose(3)"><i> Current Month / </i></span>
 			<span class="status status_sr_2 sr_close" data-id="2" onClick="changeMonthClose(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_1 sr_close" data-id="1" onClick="changeMonthClose(1)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_4 sr_close" data-id="1" onClick="changeMonthClose(4)"><i> Last Year</i></span>
 			<?php } else{?>
 			<span class="status status_sr_3 sr_close" data-id="3" onClick="changeMonthClose(3)"><i> Current Month / </i></span>
 			<span class="status status_sr_1 sr_close" data-id="1" onClick="changeMonthClose(1)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_2 sr_close" data-id="2" onClick="changeMonthClose(2)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_4 sr_close" data-id="1" onClick="changeMonthClose(4)"><i> Last Year</i></span>
 			<?php } ?>
 	</div>
 	<div class="style_chart700 graph" id="graph-sr" ></div>
</div>
<?php $id = WidgetSrClose::getId();
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
));
?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetSrClose::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<?php if(Yii::app()->user->isAdmin){?>
 			<span class="status status_sr_3 sr_close" onClick="changeMonthClose(3)"><i> Current Month / </i></span>
 			<span class="status status_sr_2 sr_close" onClick="changeMonthClose(2)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_1 sr_close" onClick="changeMonthClose(1)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_4 sr_close" onClick="changeMonthClose(4)"><i> Last Year</i></span>
 			<?php } else{?>
 			<span class="status status_sr_3 sr_close" onClick="changeMonthClose(3)"><i> Current Month / </i></span>
 			<span class="status status_sr_1 sr_close" onClick="changeMonthClose(1)"><i> Last 3 Months /</i></span>
 			<span class="status status_sr_2 sr_close" onClick="changeMonthClose(2)"><i> Last 6 Months /</i></span>
 			<span class="status status_sr_4 sr_close" onClick="changeMonthClose(4)"><i> Last Year</i></span>
 			<?php }?>
 	</div>
 	<div class="style_chart1000 graph" id="graph-sr1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<script type="text/javascript">
function createGridStack() {
     $('.status_sr_1').addClass("colorRed"); val = <?php echo WidgetSrClose::CharChart1()?>;   StackedBar(val,"graph-sr"); StackedBar(val,"graph-sr1");
} 
function StackedBar(val,id){
	<?php if(Yii::app()->user->isAdmin){ ?>
	var dataSource = val;
	              $("#"+id).dxChart({
	                  dataSource: dataSource,
	                  commonSeriesSettings: {   argumentField: "state",  type: "stackedBar" },
	                  series: [    { valueField: "ps", name: "PS" },  { valueField: "cs", name: "CS" },	   ],
	                  legend: {    verticalAlignment: "bottom",   horizontalAlignment: "center",	itemTextPosition: 'top' },
	                  valueAxis: {  title: {  text: "SRs"  },  position: "left" },
	                  tooltip: {  enabled: true,  customizeText: function () {  return this.seriesName + " : " + this.valueText;   }
	                  }
	              });
<?php } else{ ?>
		var dataSource = val;
	              $("#"+id).dxChart({
	                  dataSource: dataSource,
	                  commonSeriesSettings: {  argumentField: "state",  type: "Bar" },
	                  series: {    valueField: "value",name:"Total",color: '#CAE443',   },
	                  legend: {   verticalAlignment: "bottom",  horizontalAlignment: "center",	itemTextPosition: 'top'  },
	                  valueAxis: {      title: {  text: "SRs"  },   position: "left"  },
	                  tooltip: {  enabled: true,  customizeText: function () {  return this.seriesName + " : " + this.valueText;  }
	                  }
	              });
<?php } ?>
}
function changeMonthClose(val){
	$.ajax({
 		type: "POST",	data: {'val':val},	url: "<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrBarSortClosed');?>", dataType: "json",
	  	success: function(data) {	if (data) {	$('.sr_close').removeClass("colorRed");    var vall = data;   StackedBar(vall,"graph-sr");
			    StackedBar(vall,"graph-sr1");  	$('.status_sr_'+val).addClass("colorRed");	} }
});}
</script>	