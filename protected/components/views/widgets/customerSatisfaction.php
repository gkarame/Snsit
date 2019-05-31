<div class="bcontenu">
  <div class="stat_years">
    <span class="status type_cs 50" id="50" onClick="changeTypeCS(<?php echo "50";?>)"><i> ALL/ </i></span>
    <span class="status type_cs 100" id="100" onClick="showTrendDown(<?php echo "100";?>)"><i> TrendDown/ </i></span>
	<span class="spliter status" > Customers</span>
        <span class="status type_cs 150" id="150" >
        <?php 
        $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Customers::model(),
            'attribute' => 'id',   
            'source'=>Projects::getCustomersAutocomplete(),
            // additional javascript options for the autocomplete plugin
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold',            ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width135",
               'id'   => "custsearch",
              'style'=> "margin-left:10px; ",
            ), )); ?>
        </span>
	<span class="spliter status" style="margin-top:-10px;" onclick="searchcustomer()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
   </div> <div class="style_chart700 graph" id="graph-customer-satisfaction" ></div>
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
    ), ));?>
<div class="bcontenu z-index">
  <div class="board inline-block ui-state-default noborder">
    <div class="bhead" id="maispecial">
      <div class="title"><?php echo  WidgetCustomerSatisfaction::getName(); ?></div>
      <div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
    </div>
    <div class="ftr"></div>
  </div>
  <div class="stat_years">
    <span class="status type_cs 50" id="50" onClick="changeTypeCS(<?php echo "50";?>)"><i> ALL/ </i></span>
    <span class="status type_cs 100" id="100" onClick="showTrendDown(<?php echo "100";?>)" ><i> TrendDown/ </i></span>
    <span class="spliter status" > Customers</span>
        <span class="status type_cs 150" id="150" >
        <?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Customers::model(),
            'attribute' => 'id',   
            'source'=>Projects::getCustomersAutocomplete(),
            // additional javascript options for the autocomplete plugin
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold', ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width161",
               'id'   => "custsearch2",
              'style'=> "margin-left:10px; ",), ));?>
        </span>
		<span class="spliter status" onclick="searchcustomer2()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
    </div>
  <div class="graph style_chart1000" id="graph-customer-satisfaction2" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
$(document).ready(function(){var id_type=100;var id_status=100;if($('#graph-customer-satisfaction2').is(':visible')){createGridCustomerSatisfaction();}
$('.closepopupwidget').click(function(){$('.popupwidget').addClass('hidden');});});function createGridCustomerSatisfaction(){$('.type_cs.'+'50').addClass("colorRed");val=<?php echo WidgetCustomerSatisfaction::CharChart();?>;ChartCustomerSatisfaction(val,"graph-customer-satisfaction");ChartCustomerSatisfaction(val,"graph-customer-satisfaction2");};function ChartCustomerSatisfaction(val,id){var dataSource=val;var midrate=3;var upperbound=4;$("#"+id).dxChart({dataSource:dataSource,commonSeriesSettings:{type:'scatter'},customizePoint:function(){if(this.value>upperbound){return{color:'#33CC33',hoverStyle:{color:'#33CC33'}};}else if(this.value<midrate){return{color:'#E60000',hoverStyle:{color:'#E60000'}};}else if(this.value==midrate)
{return{color:'#7fbffe',hoverStyle:{color:'#7fbffe'}};}else if(this.value>midrate&&this.value<upperbound)
{return{color:'#7fbffe',hoverStyle:{color:'#7fbffe'}};}else if(this.value==upperbound)
{return{color:'#33CC33',hoverStyle:{color:'#33CC33'}};}},tooltip:{enabled:true,background:'grey',font:{color:'black',size:15,},customizeText:function(arg){var splitted=arg.point.tag.split('-');var id=splitted.shift();$.ajax({type:"POST",data:{'id':id},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ShowGraph');?>",dataType:"json",success:function(data){if(data){if($('#graph-customer-satisfaction2').is(':visible')){$('.popupwidget').addClass('z-index');}
$('.popupwidget').removeClass('hidden');var val=data.caxis;var name=data.cname;document.getElementById("cust_name").innerHTML=name;ChartCustomerProfile(val,"graph-customer-profile");}}});}},argumentAxis:{label:{customizeText:function(){return this.value;}},title:'Number of Issues'},valueAxis:{min:1,max:5,tickInterval:1,valueMarginsEnabled:false,label:{format:"fixedPoint",precision:2,customizeText:function()
{if(this.value<2)
{return"Very Unsatisfied";}else if(this.value<3)
{return"Unsatisfied";}if(this.value<4)
{return"Somewhat Satisfied";}if(this.value<5)
{return"Satisfied";}if(this.value==5)
{return"Very Satisfied";}}},constantLines:[{label:{text:''},width:2,value:midrate,color:'#7fbffe',dashStyle:'dash'}]},legend:{verticalAlignment:'bottom',horizontalAlignment:'center',visible:false},palette:["#7fbffe"],onSeriesClick:function(e){var series=e.target;series.isVisible()?series.hide():series.show();},series:[{name:'name',argumentField:'total1',valueField:'older1',tagField:'tag1'}]});}
function ChartCustomerProfile(val,id){var dataSource=val;$("#"+id).dxChart({dataSource:dataSource,rotated:false,commonSeriesSettings:{type:'line'},customizePoint:function(){if(this.value>3){return{color:'#33CC33',hoverStyle:{color:'#33CC33'}};}else{return{color:'#FF6565',hoverStyle:{color:'#FF6565'}};}},tooltip:{enabled:true,background:'grey',font:{color:'black',size:15,},customizeText:function(arg){return arg.point.tag+" \n  ";}},argumentAxis:{label:{customizeText:function(){return this.value;}},},valueAxis:{min:1,max:5,tickInterval:1,valueMarginsEnabled:false,label:{format:"fixedPoint",precision:2,customizeText:function()
{if(this.value<2)
{return"Very Unsatisfied";}else if(this.value<3)
{return"Unsatisfied";}if(this.value<4)
{return"Somewhat Satisfied";}if(this.value<5)
{return"Satisfied";}if(this.value==5)
{return"Very Satisfied";}}}},legend:{verticalAlignment:'bottom',horizontalAlignment:'center',visible:false},palette:["#7fbffe"],series:[{name:'name',argumentField:'total1',valueField:'older1',tagField:'tag1'}],});}
function showTrendDown(val)
{$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/showTrendDown');?>",dataType:"json",success:function(data){if(data){$('.type_cs').removeClass("colorRed");var value=data;ChartCustomerSatisfaction(value,"graph-customer-satisfaction");ChartCustomerSatisfaction(value,"graph-customer-satisfaction2");$('.type_cs.'+val).addClass("colorRed");document.getElementById("custsearch").value="";document.getElementById("custsearch2").value="";}}});}
function changeTypeCS(type){$.ajax({type:"POST",data:{'type':type},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/customerSatisfaction');?>",dataType:"json",success:function(data){if(data){$('.type_cs').removeClass("colorRed");var pieChartDataSource=data;ChartCustomerSatisfaction(pieChartDataSource,"graph-customer-satisfaction");ChartCustomerSatisfaction(pieChartDataSource,"graph-customer-satisfaction2");$('.type_cs.'+type).addClass("colorRed");document.getElementById("custsearch").value="";document.getElementById("custsearch2").value="";}}});}
function searchcustomer(){cust=$('#custsearch').val();$.ajax({type:"POST",data:{'cust':cust},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/customerSatisfaction');?>",dataType:"json",success:function(data){if(data){$('.type_cs').removeClass("colorRed");var pieChartDataSource=data;ChartCustomerSatisfaction(pieChartDataSource,"graph-customer-satisfaction");ChartCustomerSatisfaction(pieChartDataSource,"graph-customer-satisfaction2");$('.type_cs.'+type).addClass("colorRed");}}});}
function searchcustomer2(){cust=$('#custsearch2').val();$.ajax({type:"POST",data:{'cust':cust},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/customerSatisfaction');?>",dataType:"json",success:function(data){if(data){$('.type_cs').removeClass("colorRed");var pieChartDataSource=data;ChartCustomerSatisfaction(pieChartDataSource,"graph-customer-satisfaction");ChartCustomerSatisfaction(pieChartDataSource,"graph-customer-satisfaction2");$('.type_cs.'+type).addClass("colorRed");}}});}
</script>
