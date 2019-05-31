<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status month_billability_1 month_billability" data-id ="1" onClick="changeBillability(1)"><i>Current Mo/</i></span>
 			<span class="status month_billability_2 month_billability" data-id ="2" onClick="changeBillability(2)"><i>Last 3Mo/</i></span>
 			<span class="status month_billability_4 month_billability" data-id ="4" onClick="changeBillability(4)"><i>12Mo/</i></span>
 			<span class="status month_billability_3 month_billability" data-id ="3" onClick="changeBillability(3)"><i>24Mo</i></span>
			<span class="status resc_billability_5 resc_billability"  style="float:right;" data-id ="5" onClick="changeResources(5)"><i>Tech-CS/</i></span>
 			<span class="status resc_billability_4 resc_billability"  style="float:right;" data-id ="4" onClick="changeResources(4)"><i>Tech-PS/</i></span>
			<span class="status resc_billability_1 resc_billability"  style="float:right;" data-id ="1" onClick="changeResources(1)"><i>Tech/</i></span>
 			<span class="status resc_billability_2 resc_billability" style="float:right;" data-id ="2" onClick="changeResources(2)"><i>Ops/</i></span>
 			<span class="status resc_billability_3 resc_billability" style="float:right;" data-id ="3" onClick="changeResources(3)"><i>All/</i></span>
 			<span class="status resc_billability_6 resc_billability"  style="float:right;" data-id ="6" onClick="changeResources(6)"><i>Core-Tech</i></span>
 	</div> <div class="style_chart700 graph" id="graph-billability" ></div>
</div>
<?php $id = WidgetBillability::getId();
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
    ), )); ?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetBillability::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status month_billability_1 month_billability" data-id ="1" onClick="changeBillability(1)"><i> Current Mo / </i></span>
 			<span class="status month_billability_2 month_billability" data-id ="2" onClick="changeBillability(2)"><i> Last 3 Mo /</i></span>
 			<span class="status month_billability_4 month_billability" data-id ="4" onClick="changeBillability(4)"><i> Last 12 Mo /</i></span>
 			<span class="status month_billability_3 month_billability" data-id ="3" onClick="changeBillability(3)"><i> Last 24 Mo</i></span>
			<span class="status resc_billability_6 resc_billability"  style="float:right;" data-id ="6" onClick="changeResources(6)"><i> Core-Tech</i></span>
			<span class="status resc_billability_5 resc_billability"  style="float:right;" data-id ="5" onClick="changeResources(5)"><i> Tech-CS/</i></span>
 			<span class="status resc_billability_4 resc_billability"  style="float:right;" data-id ="4" onClick="changeResources(4)"><i> Tech-PS/</i></span>
			<span class="status resc_billability_1 resc_billability"  style="float:right;" data-id ="1" onClick="changeResources(1)"><i> Tech/</i></span>
 			<span class="status resc_billability_2 resc_billability" style="float:right;" data-id ="2" onClick="changeResources(2)"><i> Ops/</i></span>
 			<span class="status resc_billability_3 resc_billability" style="float:right;" data-id ="3" onClick="changeResources(3)"><i> All/</i></span>
 	</div> 	<div class="graph style_chart1000" id="graph-billability1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridBillability(){$('.resc_billability_3').addClass("colorRed");$('.month_billability_4').addClass("colorRed");val=<?php echo WidgetBillability::CharChart()?>;ChartBillability(val,"graph-billability");ChartBillability(val,"graph-billability1");};function ChartBillability(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:"line",argumentField:"month"},commonAxisSettings:{grid:{visible:true}},tooltip:{enabled:true,background:'grey',font:{color:'black',size:15,},customizeText:function(arg){var id=arg.point.tag;$.ajax({type:"POST",data:{'id':id},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ShowGraphMonthBillability');?>",dataType:"json",success:function(data){if(data){if($('#graph-billability1').is(':visible')){$('.popupwidget').addClass('z-index');}
$('.popupwidget').removeClass('hidden');var val=data.caxis;var name=data.cname;document.getElementById("cust_name").innerHTML=name;ChartTaskBillability(val,"graph-customer-profile");}}});}},series:[{name:'billability',argumentField:'month',valueField:'billability',tagField:'lookupdate'}],legend:{visible:false},commonPaneSettings:{border:{visible:true,bottom:false}}});}
function ChartTaskBillability(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,rotated:true,commonSeriesSettings:{argumentField:"label",type:"bar"},customizePoint:function(){return{color:'#33CC33',hoverStyle:{color:'#33CC33'}};},tooltip:{enabled:true,background:'grey',font:{color:'black',size:15,},customizeText:function(arg){return arg.point.tag+" \n  ";}},valueAxis:{min:0,max:400,tickInterval:100,valueMarginsEnabled:false,label:{format:"fixedPoint",precision:0,customizeText:function()
{return this.value;}}},legend:{verticalAlignment:'bottom',horizontalAlignment:'center',visible:false},palette:["#7fbffe"],series:[{argumentField:'total1',valueField:'older1',tagField:'tag1'}],});}
function changeBillability(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/BillabilityBarSort');?>",dataType:"json",success:function(data){if(data){$('.month_billability').removeClass("colorRed");var pieChartDataSource=data;ChartBillability(pieChartDataSource,"graph-billability");ChartBillability(pieChartDataSource,"graph-billability1");$('.month_billability_'+val).addClass("colorRed");}}});}
function changeResources(resc){$.ajax({type:"POST",data:{'resc':resc},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/BillabilityBarSort');?>",dataType:"json",success:function(data){if(data){$('.resc_billability').removeClass("colorRed");var pieChartDataSource=data;ChartBillability(pieChartDataSource,"graph-billability");ChartBillability(pieChartDataSource,"graph-billability1");$('.resc_billability_'+resc).addClass("colorRed");}}});}
</script>	