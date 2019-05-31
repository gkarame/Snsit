<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_dso_ind_4 dso_ind" data-id="4" onClick="changeYearsSubmitted2(4)"><i> This Year / </i></span>
 			<span class="status status_dso_ind_2 dso_ind" data-id="2" onClick="changeYearsSubmitted2(2)"><i> Last 3 Years / </i></span>
 			<span class="status status_dso_ind_1 dso_ind" data-id="1" onClick="changeYearsSubmitted2(1)"><i> Last 5 Years /</i></span>
 			<span class="status status_dso_ind_3 dso_ind" data-id="3" onClick="changeYearsSubmitted2(3)"><i> Last 10 Years </i></span>
 	</div>
 	<div id="pieDSOIndex" class="style_chart700 graph"></div>
</div>
<?php $id = WidgetDsoIndex::getId();?>
<?php 
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
		'class'=>"unaspeciala",
		'closeOnEscape' => true,
    ), )); ?>
<div class="bcontenu z-index">
 	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetDsoIndex::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_dso_ind_4 dso_ind " onClick="changeYearsSubmitted2(4)"><i> This Year / </i></span>
 			<span class="status status_dso_ind_2 dso_ind " onClick="changeYearsSubmitted2(2)"><i> Last 3 Years / </i></span>
 			<span class="status status_dso_ind_1 dso_ind" onClick="changeYearsSubmitted2(1)"><i> Last 5 Years /</i></span>
 			<span class="status status_dso_ind_3 dso_ind" onClick="changeYearsSubmitted2(3)"><i> Last 10 Years </i></span>
 	</div>
 	<div class="graph" id="graph-dso1" ></div>
 	<div id="pieDSOIndex1" class="style_chart1000 graph"></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<script type="text/javascript">
function createGridDSOIndex(){$('.status_dso_ind_1').addClass("colorRed");var pieChartDataSource=<?php echo WidgetDsoIndex::CharChart1();?>;bar2(pieChartDataSource,1);bar2(pieChartDataSource,0);$('.status_dso_ind_1').addClass("colorRed");};function bar2(pieChartDataSource,val){$(function(){if(val==1){id="pieDSOIndex";}
else{;id="pieDSOIndex1";}
$("#"+id).dxChart({dataSource:pieChartDataSource,commonSeriesSettings:{argumentField:"label",type:"bar",hoverMode:"allArgumentPoints",selectionMode:"allArgumentPoints",label:{visible:true,format:"fixedPoint",precision:0}},legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"right",verticalAlignment:"bottom",rowCount:2},legend:{visible:false,},series:{argumentField:"label",valueField:"value",color:'#ffa500',},valueAxis:{title:{text:"DSO Index"}},argumentAxis:{title:'Years',type:'discrete',grid:{visible:true}}});});}
function changeYearsSubmitted2(val){$.ajax({type:"POST",data:{'valdso':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/DSOIndex');?>",dataType:"json",success:function(data){if(data){$('.dso_ind').removeClass("colorRed");var pieChartDataSource=data;bar2(pieChartDataSource,1);bar2(pieChartDataSource,0);$('.status_dso_ind_'+val).addClass("colorRed");}}});}
</script>	