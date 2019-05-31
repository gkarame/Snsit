<div class="bcontenu">
 	<div class="stat_years">
 		<?php foreach ($years as $k=>$year){?>
 			<span class="status status_eas <?php echo $k?>" data-id="<?php echo $k?>" onClick="change(<?php echo $k?>)"><i><?php echo $year." / "; ?></i></span>
 		<?php }?>
 	</div>	<div class="style_chart700 graph" id="graph-1" ></div>
 </div>
 <?php $id = $this->getId();
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
    ),)); ?>
<div class="bcontenu z-index">
 	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetEas::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 		<?php foreach ($years as $k=>$year){?>
 			<span class="status status_eas <?php echo $k?>" onClick="change(<?php echo $k?>)"><i><?php echo $year." / "; ?></i></span>
 		<?php }?>
 	</div> <div class="style_chart1000 graph" id="graph-11" ></div>
 </div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridCustomer1(){var pieChartDataSource=<?php echo WidgetEas::CharChart();?>;bar4(pieChartDataSource,1);bar4(pieChartDataSource,0);};function bar4(pieChartDataSource,val){if(val==1){id="graph-1";}
else{;id="graph-11";}
$("#"+id).dxChart({dataSource:pieChartDataSource,commonSeriesSettings:{argumentField:"state",type:"bar",hoverMode:"allArgumentPoints",selectionMode:"allArgumentPoints",label:{visible:true,format:"fixedPoint",precision:0}},legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"right",verticalAlignment:"bottom",rowCount:2},series:{argumentField:"label",valueField:"value",color:'#ffa500'},valueAxis:{title:{text:"Total Amount"}},legend:{visible:false,},argumentAxis:{title:'Months',type:'discrete',grid:{visible:true}}});}
function change(year){$.ajax({type:"POST",data:{'year':year},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/EasBarSort');?>",dataType:"json",success:function(data){if(data){$('.status_eas').removeClass("colorRed");var pieChartDataSource=data;bar4(pieChartDataSource,1);bar4(pieChartDataSource,0);$('.status_eas.'+year).addClass("colorRed");}}});}
</script>	