<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status month_active_projects_3 active_projects" data-id ="3" onClick="changeMonthProjects(3)"><i> Current Month / </i></span>
 			<span class="status month_active_projects_2 active_projects" data-id ="2" onClick="changeMonthProjects(2)"><i> Last 3 Months /</i></span>
 			<span class="status month_active_projects_1 active_projects" data-id ="1" onClick="changeMonthProjects(1)"><i> Last 6 Months </i></span>
<span class="status top_projects_3 top_projects" data-id ="3" style="float:right;padding-right:20px;" onClick="changeTopProjects(3)"><i>15</i></span>
      <span class="status top_projects_2 top_projects" data-id ="2" style="float:right" onClick="changeTopProjects(2)"><i>10 /</i></span>
      <span class="status top_projects_1 top_projects" data-id ="1" style="float:right" onClick="changeTopProjects(1)"><i>5 /</i></span>
 		   <span class="status top_projects_0 top_projects" data-id ="0" style="float:right"><i>Top</i></span>	
 	</div> 	<div class="style_chart700 graph" id="graph-most-active-projects" ></div>
</div>
<?php $id = WidgetMostActiveProject::getId();
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
			<div class="title"><?php echo  WidgetMostActiveProject::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div><div class="ftr"></div>
	</div>
  <div class="stat_years">
      <span class="status month_active_projects_3 active_projects" data-id ="3" onClick="changeMonthProjects(3)"><i> Current Month / </i></span>
      <span class="status month_active_projects_2 active_projects" data-id ="2" onClick="changeMonthProjects(2)"><i> Last 3 Months /</i></span>
      <span class="status month_active_projects_1 active_projects" data-id ="1" onClick="changeMonthProjects(1)"><i> Last 6 Months </i></span>
      <span class="status top_projects_3 top_projects" data-id ="3" style="float:right;padding-right:20px;" onClick="changeTopProjects(3)"><i> Top 15  </i></span>
      <span class="status top_projects_2 top_projects" data-id ="2" style="float:right" onClick="changeTopProjects(2)"><i> Top 10 /</i></span>
      <span class="status top_projects_1 top_projects" data-id ="1" style="float:right" onClick="changeTopProjects(1)"><i> Top 5 / </i></span>
    </div> 	<div class="style_chart1000 graph" id="graph-most-active-projects1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function CreateGridMostActiveProjects(){val=<?php print_r(WidgetMostActiveProject::CharChart());?>;drowMostActiveProjects(val,"graph-most-active-projects");drowMostActiveProjects(val,"graph-most-active-projects1");$('.month_active_projects_1').addClass("colorRed");$('.top_projects_1').addClass("colorRed")}
function drowMostActiveProjects(val,id){var dataSource=val;var chart=$("#"+id).dxChart({dataSource:dataSource,rotated:!0,commonSeriesSettings:{argumentField:"label",type:"bar",scaleFontSize:1},series:{valueField:"value",name:"Man Days",color:"#d61200",selectionStyle:{hatching:"none"}},legend:{visible:!1,},pointClick:function(point){point.isSelected()?point.clearSelection():point.select()},tooltip:{enabled:!0,customizeText:function(){return this.valueText+" "+this.seriesName}}}).dxChart("instance")}
function changeMonthProjects(month){$.ajax({type:"POST",data:{'month':month},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/mostActiveProject');?>",dataType:"json",success:function(data){if(data){$('.active_projects').removeClass("colorRed");var pieChartDataSource=data;drowMostActiveProjects(pieChartDataSource,"graph-most-active-projects");drowMostActiveProjects(pieChartDataSource,"graph-most-active-projects1");$('.month_active_projects_'+month).addClass("colorRed")}}})}
function changeTopProjects(top){$.ajax({type:"POST",data:{'top':top},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/mostActiveProject');?>",dataType:"json",success:function(data){if(data){$('.top_projects').removeClass("colorRed");var pieChartDataSource=data;drowMostActiveProjects(pieChartDataSource,"graph-most-active-projects");drowMostActiveProjects(pieChartDataSource,"graph-most-active-projects1");$('.top_projects_'+top).addClass("colorRed")}}})}
</script>	