<div class="bcontenu">
  <div class="stat_years">
      <span class="status user_Avg_5 user_Avg" data-id ="5" onClick="changeUserAvg(5)"><i>Last 12 M/</i></span>
      <span class="status user_Avg_1 user_Avg" data-id ="1" onClick="changeUserAvg(1)"><i>Last Year/</i></span>
      <span class="status user_Avg_3 user_Avg" data-id ="3" onClick="changeUserAvg(3)"><i>2Y. Ago/</i></span>
      <span class="status user_Avg_4 user_Avg" data-id ="4" onClick="changeUserAvg(4)"><i>3Y. Ago</i></span>
       <span class="status" style="width:30px;" ><i> </i></span>      
 <span class="spliter status"  style="margin-left:12px;">Users</span>
        <span class="status type_cs 150" id="150" >
        <?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Users::model(),
            'attribute' => 'id',   
            'source'=>Users::getAllAutocompleteTech(),
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold',          ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width80",
               'id'   => "usersearchPerf",
              'style'=> "margin-left:5px;",   ),        ));
        ?></span>
      <span class="spliter status" style="margin-top:-9px;" onclick="searchUserPerf()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
     </div>  <div class="style_chart700 graph" id="graph-support-performance" ></div>
</div>
<?php $id = WidgetSupportPerformance::getId();
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
      <div class="title"><?php echo  WidgetSupportPerformance::getName(); ?></div>
      <div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
    </div>  <div class="ftr"></div>
  </div>
  <div class="stat_years">
      <span class="status user_Avg_5 user_Avg" data-id ="5" onClick="changeUserAvg(5)"><i>Last 12 Mon/</i></span>
      <span class="status user_Avg_1 user_Avg" data-id ="1" onClick="changeUserAvg(1)"><i>Last Year/</i></span>
      <span class="status user_Avg_3 user_Avg" data-id ="3" onClick="changeUserAvg(3)"><i>2 Years Ago/</i></span>
      <span class="status user_Avg_4 user_Avg" data-id ="4" onClick="changeUserAvg(4)"><i>3 Years Ago</i></span>
  <span class="spliter status"  style="margin-left:400px;"> Users</span>
        <span class="status type_cs 150" id="150" >
        <?php      $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Customers::model(),
            'attribute' => 'id',   
            'source'=>Users::getAllAutocompleteTech(),
            // additional javascript options for the autocomplete plugin
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold', ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width161",
               'id'   => "usersearchPerf2",
              'style'=> "margin-left:10px; ",         ),        ));       ?></span>
      <span class="spliter status" onclick="searchUserPerf2()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
       </div>  <div class="graph style_chart1000" id="graph-support-performance1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
$(document).ready(function(){var id_type=100;var id_status=100;if($('#graph-support-performance1').is(':visible')){createGridSupportPerformance()}});function createGridSupportPerformance(){$('.user_Avg').removeClass("colorRed");$('.user_Avg_5').addClass("colorRed");val=<?php echo WidgetSupportPerformance::CharChart();?>;ChartSupportPerformance(val,"graph-support-performance");ChartSupportPerformance(val,"graph-support-performance1")};function ChartSupportPerformance(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:'line'},customizePoint:function(){if(this.value>3){return{color:'#33CC33',hoverStyle:{color:'#33CC33'}}}else{return{color:'#FF6565',hoverStyle:{color:'#FF6565'}}}},tooltip:{enabled:!0,background:'grey',font:{color:'black',size:15,},customizeText:function(arg){return arg.point.tag+" \n  "}},argumentAxis:{label:{customizeText:function(){return this.value}},},valueAxis:{min:50,max:100,tickInterval:10,valueMarginsEnabled:!1,label:{format:"fixedPoint",precision:2,customizeText:function()
{return this.value+'%'}}},legend:{verticalAlignment:'bottom',horizontalAlignment:'center',visible:!1},palette:["#7fbffe"],series:[{name:'name',argumentField:'total1',valueField:'older1',tagField:'tag1'}]})}
function changeUserAvg(val2){user=$('#usersearchPerf').val();if(user.length===0||!user.trim())
{user=$('#usersearchPerf2').val()}
if(user.length===0||!user.trim())
{user=1}
$.ajax({type:"POST",data:{'timeval':val2,'userPerf':user},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/supportPerf');?>",dataType:"json",success:function(data){if(data){$('.user_Avg').removeClass("colorRed");var val=data;ChartSupportPerformance(val,"graph-support-performance");ChartSupportPerformance(val,"graph-support-performance1");$('.user_Avg_'+val2).addClass("colorRed")}}})}
function searchUserPerf(){user=$('#usersearchPerf').val();$.ajax({type:"POST",data:{'userPerf':user},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/supportPerf');?>",dataType:"json",success:function(data){if(data){var val=data;ChartSupportPerformance(val,"graph-support-performance");ChartSupportPerformance(val,"graph-support-performance1")}}})}
function searchUserPerf2(){user=$('#usersearchPerf2').val();$.ajax({type:"POST",data:{'userPerf':user},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/supportPerf');?>",dataType:"json",success:function(data){if(data){var val=data;ChartSupportPerformance(val,"graph-support-performance");ChartSupportPerformance(val,"graph-support-performance1")}}})}
</script>
