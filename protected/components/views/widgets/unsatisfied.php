<div class="bcontenu"> 	<div class="stat_years">	<span class="status status_unsat_cust_1 unsat_customer" data-id ="1" onClick="changeMonthUnsatisfied(1)"><i> Current M/</i></span>
													<span class="status status_unsat_cust_2 unsat_customer" data-id ="2" onClick="changeMonthUnsatisfied(2)"><i> Last 3 M/</i></span>
													<span class="status status_unsat_cust_3 unsat_customer" data-id ="3" onClick="changeMonthUnsatisfied(3)"><i> Last 6 M/</i></span>													
													<span class="status status_unsat_cust_4 unsat_customer" data-id ="4" onClick="changeMonthUnsatisfied(4)"><i> Last Year</i></span>
 	 <span class="spliter status"  style="margin-left:6px;">Users</span>
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
               'id'   => "usersearchunsat",
              'style'=> "margin-left:5px;",   ),        ));
        ?></span>
      <span class="spliter status" style="margin-top:-9px;" onclick="searchUserunsat()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
    </div> 	<div class="style_chart700 graph" id="graph-unsat-customer" ></div></div>
<?php $id = WidgetUnsatisfied::getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
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
			<div class="title"><?php echo  WidgetUnsatisfied::getName(); ?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div> <div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_unsat_cust_1 unsat_customer" data-id ="1" onClick="changeMonthUnsatisfied(1)"><i> Current Mon/</i></span>
 			<span class="status status_unsat_cust_2 unsat_customer" data-id ="2" onClick="changeMonthUnsatisfied(2)"><i> Last 3 Mon/</i></span>
			<span class="status status_unsat_cust_3 unsat_customer" data-id ="3" onClick="changeMonthUnsatisfied(3)"><i> Last 6 Mon/</i></span>													
			<span class="status status_unsat_cust_4 unsat_customer" data-id ="4" onClick="changeMonthUnsatisfied(4)"><i> Last Year</i></span>
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
               'id'   => "usersearchunsat2",
              'style'=> "margin-left:10px; ",         ),        ));       ?></span>
      <span class="spliter status" onclick="searchUserunsat2()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
       </div> 	<div class="style_chart1000 graph" id="graph-unsat-customer1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
$(document).ready(function(){$('.closepopupwidget').click(function(){$('.popupwidget').addClass('hidden')})});function createGridUnsatCustomer(){var pieChartDataSource=<?php echo WidgetUnsatisfied::CharChart()?>;drowUnsatCustomerChart(pieChartDataSource,"graph-unsat-customer");drowUnsatCustomerChart(pieChartDataSource,"graph-unsat-customer1");$('.status_unsat_cust_3').addClass("colorRed")};function drowUnsatCustomerChart(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:3},series:{argumentField:'category',valueField:'value'},tooltip:{enabled:!0,customizeText:function(e){var cust=e.argument;var perc=e.percentText;user=$('#usersearchunsat').val();if(user.length===0||!user.trim()){user=$('#usersearchunsat2').val();}
if(user.length===0||!user.trim()){user=1;}
$.ajax({type:"POST",data:{'cust':cust,'perc':perc,'userunsatpop':user,},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ShowTable');?>",dataType:"json",success:function(data){if(data){if($('#graph-unsat-customer1').is(':visible')){$('.popupwidget').addClass('z-index')}
$('.popupwidget').removeClass('hidden');var val=data.caxis;var name=data.cname;document.getElementById("cust_name").innerHTML=name;ChartReasons(val,"graph-customer-profile")}}})}}})}
function changeMonthUnsatisfied(val){user=$('#usersearchunsat').val();if(user.length===0||!user.trim()){user=$('#usersearchunsat2').val();}
if(user.length===0||!user.trim()){user=1;}
$.ajax({type:"POST",data:{'val':val,'userunsat':user},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/UnsatBarSortCustomer');?>",dataType:"json",success:function(data){if(data){$('.unsat_customer').removeClass("colorRed");var pieChartDataSource=data;drowUnsatCustomerChart(pieChartDataSource,"graph-unsat-customer");drowUnsatCustomerChart(pieChartDataSource,"graph-unsat-customer1");$('.status_unsat_cust_'+val).addClass("colorRed")}}})}
function ChartReasons(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,rotated:!0,commonSeriesSettings:{argumentField:"label",type:"bar"},customizePoint:function(){return{color:'#33CC33',hoverStyle:{color:'#33CC33'}}},tooltip:{enabled:!0,background:'grey',font:{color:'black',size:15,},customizeText:function(arg){return arg.point.tag+" "}},valueAxis:{tickInterval:5,valueMarginsEnabled:!1,label:{format:"fixedPoint",precision:0,customizeText:function(){return this.value}}},legend:{verticalAlignment:'bottom',horizontalAlignment:'center',visible:!1},palette:["#7fbffe"],series:[{argumentField:'total1',valueField:'older1',tagField:'tag1'}],})}
function searchUserunsat(){user=$('#usersearchunsat').val();document.getElementById('usersearchunsat2').value=user;$.ajax({type:"POST",data:{'userunsat':user},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/UnsatBarSortCustomer');?>",dataType:"json",success:function(data){if(data){var pieChartDataSource=data;drowUnsatCustomerChart(pieChartDataSource,"graph-unsat-customer");drowUnsatCustomerChart(pieChartDataSource,"graph-unsat-customer1");}}})}
function searchUserunsat2(){user=$('#usersearchunsat2').val();document.getElementById('usersearchunsat').value=user;$.ajax({type:"POST",data:{'userunsat':user},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/UnsatBarSortCustomer');?>",dataType:"json",success:function(data){if(data){var pieChartDataSource=data;drowUnsatCustomerChart(pieChartDataSource,"graph-unsat-customer");drowUnsatCustomerChart(pieChartDataSource,"graph-unsat-customer1");}}})}
</script>	