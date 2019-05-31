<div class="bcontenu">
 	<div class="stat_years">
 		<span class="status type_actuals 100" id="alltypes" onClick="changeTypeActuals(<?php echo "100";?>)"><i>All /</i></span>
 		<span class="status type_actuals <?php echo Projects::TYPE_SW;?>" data-id="<?php echo Projects::TYPE_SW;?>" onClick="changeTypeActuals(<?php echo Projects::TYPE_SW;?>)"><i>SW / </i></span>
 		<span class="status type_actuals <?php echo Projects::TYPE_CONSULTING;?>" data-id="<?php echo Projects::TYPE_CONSULTING;?>" onClick="changeTypeActuals(<?php echo Projects::TYPE_CONSULTING;?>)"><i>Cons.</i></span>
 	<span class="status budget_actuals 100" id="allbudgets" style="text-align:center;padding-left :5px;" onClick="changeBudgetActuals(<?php echo "100";?>)"><i>All /</i></span>
    <span class="status budget_actuals lt25 " data-id="lt25"  onClick="changeBudgetActuals('lt25')"><i> <25k/ </i></span>
    <span class="status budget_actuals lt75 " data-id="lt75" style="text-align:center"   onClick="changeBudgetActuals('lt75')"><i> <75k/</i></span>        
    <span class="status budget_actuals mt75 "data-id="mt75"  style="text-align:center" onClick="changeBudgetActuals('mt75')"><i> >75k</i></span>
 <span class="status filter_actuals 100" data-id="100" style="text-align:center;padding-left :30px;" onClick="changecolor(100)"><i>All / </i></span>
    <span class="status filter_actuals 80" data-id="80" style="text-align:center;"   onClick="changecolor(80)"><i>Red /</i></span>        
    <span class="status filter_actuals 60" data-id="60"  style="text-align:center;" onClick="changecolor(60)"><i>Blue /</i></span>
    <span class="status filter_actuals 40" data-id="40"  style="text-align:center;" onClick="changecolor(40)"><i>Green</i></span>
<span class="status status_actuals <?php echo Projects::STATUS_INACTIVE;?>" data-id="<?php echo Projects::STATUS_INACTIVE;?>" style="float:right;padding-right:0px;" onClick="changeStatusActuals(<?php echo Projects::STATUS_INACTIVE;?>)"><i>Inactive </i></span>
 		<span class="status status_actuals <?php echo Projects::STATUS_CLOSED;?>" data-id="<?php echo Projects::STATUS_CLOSED;?>" style="float:right;"   onClick="changeStatusActuals(<?php echo Projects::STATUS_CLOSED;?>)"><i>Closed /</i></span>	 			
 		<span class="status status_actuals <?php echo Projects::STATUS_ACTIVE;?>"data-id="<?php echo Projects::STATUS_ACTIVE;?>"  style="float:right;" onClick="changeStatusActuals(<?php echo Projects::STATUS_ACTIVE;?>)"><i>Active /</i></span>
 		<span class="status status_actuals 100" id ="allstatuses" style="float:right" data-id="100" onClick="changeStatusActuals(<?php echo 100;?>)"><i>All /</i></span>
 	</div> 	<div class="style_chart700 graph" id="graph-actuals" ></div>
</div>
<?php $id = WidgetTestActuals::getId();
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
        'closeOnEscape' => true,    ),));?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetTestActuals::getName(); ?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 		<span class="status type_actuals 100" id="allcat" onClick="changeTypeActuals(<?php echo "100";?>)"><i>All /</i></span>
 		<span class="status type_actuals <?php echo Projects::TYPE_SW;?>" onClick="changeTypeActuals(<?php echo Projects::TYPE_SW;?>)"><i>SW / </i></span>
 		<span class="status type_actuals <?php echo Projects::TYPE_CONSULTING;?>" onClick="changeTypeActuals(<?php echo Projects::TYPE_CONSULTING;?>)"><i>Consultancy  </i></span>
 	<span class="status budget_actuals 100" id="allbudgets" style="text-align:center; padding-left :250px;" onClick="changeBudgetActuals(<?php echo "100";?>)"><i>All /</i></span>
    <span class="status budget_actuals lt25 " data-id="lt25" style="text-align:center;" onClick="changeBudgetActuals('lt25')"><i> <25k / </i></span>
    <span class="status budget_actuals lt75 " data-id="lt75" style="text-align:center"   onClick="changeBudgetActuals('lt75')"><i> <75k /</i></span>    
    <span class="status budget_actuals mt75" style="text-align:center" data-id="mt75" onClick="changeBudgetActuals('mt75')"><i> >75k </i></span>
<span class="status filter_actuals 100" data-id="100" style="text-align:center;padding-left:40px;" onClick="changecolor(100)"><i>All / </i></span>
    <span class="status filter_actuals 80" data-id="80" style="text-align:center;"   onClick="changecolor(80)"><i>Red /</i></span>        
    <span class="status filter_actuals 60" data-id="60"  style="text-align:center;" onClick="changecolor(60)"><i>Blue /</i></span>
    <span class="status filter_actuals 40" data-id="40"  style="text-align:center;" onClick="changecolor(40)"><i>Green</i></span>
<span class="status status_actuals <?php echo Projects::STATUS_INACTIVE;?>" style="float:right;padding-right:20px;" onClick="changeStatusActuals(<?php echo Projects::STATUS_INACTIVE;?>)"><i>Inactive </i></span>
 		<span class="status status_actuals <?php echo Projects::STATUS_CLOSED;?>" style="float:right;"   onClick="changeStatusActuals(<?php echo Projects::STATUS_CLOSED;?>)"><i>Closed /</i></span>	 			
 		<span class="status status_actuals <?php echo Projects::STATUS_ACTIVE;?>" style="float:right;" onClick="changeStatusActuals(<?php echo Projects::STATUS_ACTIVE;?>)"><i>Active /</i></span>
 		<span class="status status_actuals 100" id ="allsts" style="float:right" onClick="changeStatusActuals(<?php echo 100;?>)"><i>All /</i></span>
 	</div><div class="graph style_chart1000" id="graph-actuals2" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
$(document).ready(function(){var id_type=100;var id_status=100;$('.status_actuals.'+'100').addClass("colorRed");$('.type_actuals.'+'100').addClass("colorRed");$('.filter_actuals.'+'100').addClass("colorRed");if($('#graph-actuals2').is(':visible')){createGridActuals()}});function getProjectInfo(element){var url="<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectInfo'); ?>";$.ajax({type:"POST",url:url,dataType:"json",data:{'id_project':element},success:function(data){if(data){if(data.status=='success'){return data.name}}}})}
function createGridActuals(){val=<?php echo WidgetTestActuals::CharChart();?>;ChartActuals(val,"graph-actuals");ChartActuals(val,"graph-actuals2")};function ChartActuals(val,id){var dataSource=val;var lowAverage=<?php echo SystemParameters::getCost()*8;?>;var highAverage=800;var budgeted=<?php echo Projects::getBudgetedMD(204)?>;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:'scatter'},customizePoint:function(){if(this.value>highAverage){return{color:'#33CC33',hoverStyle:{color:'#33CC33'}}}else if(this.value<lowAverage){return{color:'#FF6565',hoverStyle:{color:'#FF6565'}}}},tooltip:{enabled:!0,background:'grey',font:{color:'black',size:15,},customizeText:function(arg){return arg.point.tag}},argumentAxis:{label:{customizeText:function(){return this.value}},title:'Budgeted Amount'},valueAxis:{label:{customizeText:function(){return this.value}},constantLines:[{label:{text:'Low Average'},width:2,value:lowAverage,color:'#E60000',dashStyle:'dash'},{label:{text:'High Average'},width:2,value:highAverage,color:'#30AC30',dashStyle:'dash'}],title:'Actual Rate'},legend:{verticalAlignment:'bottom',horizontalAlignment:'center',visible:!1},palette:["#7fbffe"],series:[{name:'name',argumentField:'total1',valueField:'older1',sizeField:'perc1',tagField:'tag1'}]})}
function addCommas(n){var s="",r;while(n){r=n%1000;s=r+s;n=(n-r)/1000;s=(n?",":"")+s}
return s}
function changeStatusActuals(status){$.ajax({type:"POST",data:{'status':status},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/TestActuals');?>",dataType:"json",success:function(data){if(data){$('.status_actuals').removeClass("colorRed");$('.filter_actuals').removeClass("colorRed");var pieChartDataSource=data;ChartActuals(pieChartDataSource,"graph-actuals");ChartActuals(pieChartDataSource,"graph-actuals2");$('.status_actuals.'+status).addClass("colorRed");$('.filter_actuals.100').addClass("colorRed")}}})}
function changecolor(colorfilter){$.ajax({type:"POST",data:{'colorfilter':colorfilter},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/TestActuals');?>",dataType:"json",success:function(data){if(data){$('.filter_actuals').removeClass("colorRed");var pieChartDataSource=data;ChartActuals(pieChartDataSource,"graph-actuals");ChartActuals(pieChartDataSource,"graph-actuals2");$('.filter_actuals.'+colorfilter).addClass("colorRed")}}})}
function changeBudgetActuals(budget){$.ajax({type:"POST",data:{'budget':budget},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/TestActuals');?>",dataType:"json",success:function(data){if(data){$('.budget_actuals').removeClass("colorRed");$('.filter_actuals').removeClass("colorRed");var pieChartDataSource=data;ChartActuals(pieChartDataSource,"graph-actuals");ChartActuals(pieChartDataSource,"graph-actuals2");$('.budget_actuals.'+budget).addClass("colorRed");$('.filter_actuals.100').addClass("colorRed")}}})}
function changeTypeActuals(type){$.ajax({type:"POST",data:{'type':type},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/TestActuals');?>",dataType:"json",success:function(data){if(data){$('.type_actuals').removeClass("colorRed");$('.filter_actuals').removeClass("colorRed");var pieChartDataSource=data;ChartActuals(pieChartDataSource,"graph-actuals");ChartActuals(pieChartDataSource,"graph-actuals2");$('.type_actuals.'+type).addClass("colorRed");$('.filter_actuals.100').addClass("colorRed")}}})}
</script>	