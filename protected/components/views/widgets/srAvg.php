<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status Sr_Avg_1 Sr_Avg" data-id ="1" onClick="changesrAvg(1)"><i>Current Mon/</i></span>
 			<span class="status Sr_Avg_3 Sr_Avg" data-id ="3" onClick="changesrAvg(3)"><i>Last 3 Mon/</i></span>
 			<span class="status Sr_Avg_4 Sr_Avg" data-id ="4" onClick="changesrAvg(4)"><i>Last 6 Mon/</i></span>
			<span class="status Sr_Avg_5 Sr_Avg" data-id ="5" onClick="changesrAvg(5)"><i>Last 12 Mon/</i></span>
 			<span class="status Sr_Avg_2 Sr_Avg" data-id ="2" onClick="changesrAvg(2)"><i>Previous Year</i></span>
 <span class="spliter status" > Users</span><span class="status type_cs 150" id="150" >
        <?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Users::model(),
            'attribute' => 'id',   
            'source'=>Users::getAllAutocompleteTech(),
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold',            ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width135",
               'id'   => "usersearchSR",
              'style'=> "margin-left:10px; ",),        ));
        ?></span>
      <span class="spliter status" style="margin-top:-10px;" onclick="searchUserSR()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
  </div> 	<div class="style_chart700 graph" id="graph-SrAvg" ></div>
</div>
<?php $id = WidgetSrAvg::getId();
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
			<div class="title"><?php echo  WidgetSrAvg::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 	  <span class="status Sr_Avg_1 Sr_Avg" data-id ="1" onClick="changesrAvg(1)"><i>Current Month/</i></span>
      <span class="status Sr_Avg_3 Sr_Avg" data-id ="3" onClick="changesrAvg(3)"><i>Last 3 Mon/</i></span>
      <span class="status Sr_Avg_4 Sr_Avg" data-id ="4" onClick="changesrAvg(4)"><i>Last 6 Mon/</i></span>
	  <span class="status Sr_Avg_5 Sr_Avg" data-id ="5" onClick="changesrAvg(5)"><i>Last 12 Mon/</i></span>
      <span class="status Sr_Avg_2 Sr_Avg" data-id ="2" onClick="changesrAvg(2)"><i>Previous Year</i></span>
  <span class="spliter status" style="margin-left:370px;"> Users</span>
        <span class="status type_cs 150" id="150" >
        <?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Customers::model(),
            'attribute' => 'id',   
            'source'=>Users::getAllAutocompleteTech(),
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold',
            ),            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width161",
               'id'   => "usersearchSR2",
              'style'=> "margin-left:10px; ",
            ),        ));       ?>
            </span>
      <span class="spliter status" onclick="searchUserSR2()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
    </div><div class="graph style_chart1000" id="graph-SrAvg1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<script type="text/javascript">function createGridsrAvg(){$('.Sr_Avg_4').addClass("colorRed");var val=<?php echo WidgetSrAvg::CharChart();?>;ChartSrAvg(val,"graph-SrAvg");ChartSrAvg(val,"graph-SrAvg1")};function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")}
function ChartSrAvg(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:"line",argumentField:"month"},commonAxisSettings:{grid:{visible:!0}},tooltip:{enabled:!0,customizeText:function(e){return e.value}},valueAxis:[{valueType:'numeric',}],series:[{name:'Payment',argumentField:'month',valueField:'Payment',tagField:'lookupdateval'}],legend:{visible:!1},commonPaneSettings:{border:{visible:!0,bottom:!1}}})}
function changesrAvg(val){user=$('#usersearchSR').val();if(user.length===0||!user.trim())
{user=$('#usersearchSR2').val()}
if(user.length===0||!user.trim())
{user=$('#usersearchSR2').val()}
if(user.length===0||!user.trim())
{user=1}
$.ajax({type:"POST",data:{'val':val,'userSR':user},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/MonthlyAvgSort');?>",dataType:"json",success:function(data){if(data){$('.Sr_Avg').removeClass("colorRed");var pieChartDataSource=data;ChartSrAvg(pieChartDataSource,"graph-SrAvg");ChartSrAvg(pieChartDataSource,"graph-SrAvg1");$('.Sr_Avg_'+val).addClass("colorRed")}}})}
function searchUserSR(){user=$('#usersearchSR').val();$.ajax({type:"POST",data:{'userSR':user},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/MonthlyAvgSort');?>",dataType:"json",success:function(data){if(data){var pieChartDataSource=data;ChartSrAvg(pieChartDataSource,"graph-SrAvg");ChartSrAvg(pieChartDataSource,"graph-SrAvg1")}}})}
function searchUserSR2(){user=$('#usersearchSR2').val();$.ajax({type:"POST",data:{'userSR':user},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/MonthlyAvgSort');?>",dataType:"json",success:function(data){if(data){var pieChartDataSource=data;ChartSrAvg(pieChartDataSource,"graph-SrAvg");ChartSrAvg(pieChartDataSource,"graph-SrAvg1")}}})}
</script>	