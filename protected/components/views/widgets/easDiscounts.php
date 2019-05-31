 <div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_eas_discount_4 ea_disc" data-id="4" onClick="changeYearEas(4)"><i> This Year / </i></span>
 			<span class="status status_eas_discount_2 ea_disc" data-id="2" onClick="changeYearEas(2)"><i> Last 3 Years / </i></span>
 			<span class="status status_eas_discount_1 ea_disc" data-id="1" onClick="changeYearEas(1)"><i> Last 5 Years /</i></span>
 			<span class="status status_eas_discount_3 ea_disc" data-id="3" onClick="changeYearEas(3)"><i> Last 10 Years </i></span>
 	</div> 	<div id="pieEasDiscount" class="style_chart700 graph"></div>
</div>
<?php $id = WidgetEasDiscounts::getId();
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
			<div class="title"><?php echo  WidgetEasDiscounts::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_eas_discount_4 ea_disc " onClick="changeYearEas(4)"><i> This Year / </i></span>
 			<span class="status status_eas_discount_2 ea_disc " onClick="changeYearEas(2)"><i> Last 3 Years / </i></span>
 			<span class="status status_eas_discount_1 ea_disc" onClick="changeYearEas(1)"><i> Last 5 Years /</i></span>
 			<span class="status status_eas_discount_3 ea_disc" onClick="changeYearEas(3)"><i> Last 10 Years </i></span>
 	</div>
 	<div class="graph" id="graph-ea-disc1" ></div>
 	<div id="pieEasDiscount1" class="style_chart1000 graph"></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<script type="text/javascript">
function createGrideasdisc(){$('.status_eas_discount_1').addClass("colorRed");var pieChartDataSource=<?php echo WidgetEasDiscounts::CharChart1();?>;bar(pieChartDataSource,1);bar(pieChartDataSource,0);$('.status_eas_discount_1').addClass("colorRed");};function bar(pieChartDataSource,val){$(function(){if(val==1){id="pieEasDiscount";}
else{;id="pieEasDiscount1";}
$("#"+id).dxChart({dataSource:pieChartDataSource,commonSeriesSettings:{argumentField:"state",type:"bar",hoverMode:"allArgumentPoints",selectionMode:"allArgumentPoints",label:{visible:true,format:"fixedPoint",precision:0}},legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"right",verticalAlignment:"bottom",rowCount:2},legend:{visible:false,},series:{argumentField:"label",valueField:"value",color:'#ffa500',},valueAxis:{title:{text:"Discount(%)"}},argumentAxis:{title:'Years',type:'discrete',grid:{visible:true}}});});}
function changeYearEas(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/getyeardiscount');?>",dataType:"json",success:function(data){if(data){$('.ea_disc').removeClass("colorRed");var pieChartDataSource=data;bar(pieChartDataSource,1);bar(pieChartDataSource,0);$('.status_eas_discount_'+val).addClass("colorRed");}}});}
</script>	