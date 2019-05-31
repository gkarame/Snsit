<div class="bcontenu"> 	<div class="stat_years">	
			<span class="status status_rsr_permonth_1 rsr_permon" data-id ="1" onClick="changePermonRSR(1)"><i> Last 3 M./</i></span>
			<span class="status status_rsr_permonth_2 rsr_permon" data-id ="2" onClick="changePermonRSR(2)"><i> Last 6 M./</i></span>
			<span class="status status_rsr_permonth_3 rsr_permon" data-id ="3" onClick="changePermonRSR(3)"><i> Last 12 M.</i></span>	
 	 <span class="spliter status"  style="margin-left:30px;">Customers</span>
        <span class="status type_cs 150" id="150" >
        <?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Customers::model(),
            'attribute' => 'id',   
            'source'=>Customers::getAllAutocomplete(),
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold',          ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width80",
               'id'   => "monthsearchrsr",
              'style'=> "margin-left:5px;",   ),        ));
        ?></span>
      <span class="spliter status" style="margin-top:-9px;" onclick="searchMonthRSR()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
    </div> 	<div class="style_chart700 graph" id="graph-rsr-permon" ></div></div>
<?php $id = WidgetRsrMonth::getId();
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
			<div class="title"><?php echo  WidgetRsrMonth::getName(); ?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div> <div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_rsr_permonth_1 rsr_permon" data-id ="1" onClick="changePermonRSR(1)"><i> Last 3 M./</i></span>
 			<span class="status status_rsr_permonth_2 rsr_permon" data-id ="2" onClick="changePermonRSR(2)"><i> Last 6 M./</i></span>
			<span class="status status_rsr_permonth_3 rsr_permon" data-id ="3" onClick="changePermonRSR(3)"><i> Last 12 M.</i></span>	
			  <span class="spliter status"  style="margin-left:530px;"> Customers</span>
        <span class="status type_cs 150" id="150" >
        <?php      $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Customers::model(),
            'attribute' => 'id',   
            'source'=>Customers::getAllAutocomplete(),
            // additional javascript options for the autocomplete plugin
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold', ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width161",
               'id'   => "monthsearchrsr2",
              'style'=> "margin-left:10px; ",         ),        ));       ?></span>
      <span class="spliter status" onclick="searchMonthRSR2()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
       </div> 	<div class="style_chart1000 graph" id="graph-rsr-permon1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript"> 
function createGridRsrMonth(){
	var pieChartDataSource=<?php echo WidgetRsrMonth::CharChart()?>;
	drowRsrMonthChart(pieChartDataSource,"graph-rsr-permon");
	drowRsrMonthChart(pieChartDataSource,"graph-rsr-permon1");
	$('.status_rsr_permonth_1').addClass("colorRed");
}
function drowRsrMonthChart(pieChartDataSource,id){
	
	$("#"+id).dxChart({dataSource:pieChartDataSource,
		commonSeriesSettings:{argumentField:"state",type:"bar",hoverMode:"allArgumentPoints",selectionMode:"allArgumentPoints",
			label:{visible:!0,format:"fixedPoint",precision:0}},
		legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",
			verticalAlignment:"bottom",rowCount:1,visible:!0,},
		series:[{argumentField:"label",valueField:"value",color:'#ffa500',name:'Months'}],
				valueAxis:{title:{text:"RSR(s)"}},
		argumentAxis:{}})
}
function changePermonRSR(val){
	cust=$('#monthsearchrsr').val();
	if(cust.length===0||!cust.trim()){cust=$('#monthsearchrsr2').val();}
	if(cust.length===0||!cust.trim()){cust=1;}
	$.ajax({type:"POST",data:{'valrsrmonth':val,'MonthRSR':cust},
		url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/RsrBarSortPerMon');?>",
		dataType:"json",success:function(data){
			if(data){
				$('.rsr_permon').removeClass("colorRed");var pieChartDataSource=data;
				drowRsrMonthChart(pieChartDataSource,"graph-rsr-permon");
				drowRsrMonthChart(pieChartDataSource,"graph-rsr-permon1");
				$('.status_rsr_permonth_'+val).addClass("colorRed")}}})
}
function searchMonthRSR(){
	cust=$('#monthsearchrsr').val();	document.getElementById('monthsearchrsr2').value=cust;
	$.ajax({type:"POST",data:{'MonthRSR':cust},
		url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/RsrBarSortPerMon');?>",
		dataType:"json",success:function(data){if(data){
			var pieChartDataSource=data;
			drowRsrMonthChart(pieChartDataSource,"graph-rsr-permon");
			drowRsrMonthChart(pieChartDataSource,"graph-rsr-permon1");}}})
}
function searchMonthRSR2(){
	cust=$('#monthsearchrsr2').val();
	document.getElementById('monthsearchrsr').value=cust;
	$.ajax({type:"POST",data:{'MonthRSR':cust},
		url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/RsrBarSortPerMon');?>",
		dataType:"json",success:function(data){
			if(data){
				var pieChartDataSource=data;
				drowRsrMonthChart(pieChartDataSource,"graph-rsr-permon");
				drowRsrMonthChart(pieChartDataSource,"graph-rsr-permon1");
			}}})
}
</script>	