<div class="bcontenu"> 	<div class="stat_years">	<span class="status status_rsr_cust_1 rsr_customer" data-id ="1" onClick="changeTopRSR(1)"><i> Top 10/</i></span>
													<span class="status status_rsr_cust_2 rsr_customer" data-id ="2" onClick="changeTopRSR(2)"><i> Top 20/</i></span>
													<span class="status status_rsr_cust_3 rsr_customer" data-id ="3" onClick="changeTopRSR(3)"><i> All</i></span>	
 	 <span class="spliter status"  style="margin-left:105px;">Customers</span>
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
               'id'   => "custsearchrsr",
              'style'=> "margin-left:5px;",   ),        ));
        ?></span>
      <span class="spliter status" style="margin-top:-9px;" onclick="searchCustRSR()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
    </div> 	<div class="style_chart700 graph" id="graph-rsr-customer" ></div></div>
<?php $id = WidgetRsrCustomer::getId();
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
			<div class="title"><?php echo  WidgetRsrCustomer::getName(); ?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div> <div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_rsr_cust_1 rsr_customer" data-id ="1" onClick="changeTopRSR(1)"><i> Top 10/</i></span>
 			<span class="status status_rsr_cust_2 rsr_customer" data-id ="2" onClick="changeTopRSR(2)"><i> Top 20/</i></span>
			<span class="status status_rsr_cust_3 rsr_customer" data-id ="3" onClick="changeTopRSR(3)"><i> All</i></span>	
			  <span class="spliter status"  style="margin-left:600px;"> Customers</span>
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
               'id'   => "custsearchrsr2",
              'style'=> "margin-left:10px; ",         ),        ));       ?></span>
      <span class="spliter status" onclick="searchCustRSR2()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
       </div> 	<div class="style_chart1000 graph" id="graph-rsr-customer1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript"> 
function createGridRSRCustomer(){
	var pieChartDataSource=<?php echo WidgetRsrCustomer::CharChart()?>;
	drowRSRCustomerChart(pieChartDataSource,"graph-rsr-customer");
	drowRSRCustomerChart(pieChartDataSource,"graph-rsr-customer1");
	$('.status_rsr_cust_1').addClass("colorRed");
}
function drowRSRCustomerChart(pieChartDataSource,id){
	$("#"+id).dxPieChart({
		dataSource:pieChartDataSource,
		legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:3},
		series:{argumentField:'category',valueField:'value'},
		tooltip:{enabled:!0,customizeText:function(e)
			{return e.argument+': '+e.value+ ' RSR';}
			}})}
function changeTopRSR(val){
	cust=$('#custsearchrsr').val();
	if(cust.length===0||!cust.trim()){cust=$('#custsearchrsr2').val();}
	if(cust.length===0||!cust.trim()){cust=1;}
	$.ajax({type:"POST",data:{'valrsr':val,'custrsr':cust},
		url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/RsrBarSortCustomer');?>",
		dataType:"json",success:function(data){
			if(data){
				$('.rsr_customer').removeClass("colorRed");var pieChartDataSource=data;
				drowRSRCustomerChart(pieChartDataSource,"graph-rsr-customer");
				drowRSRCustomerChart(pieChartDataSource,"graph-rsr-customer1");
				$('.status_rsr_cust_'+val).addClass("colorRed")}}})
}
function searchCustRSR(){
	cust=$('#custsearchrsr').val();document.getElementById('custsearchrsr2').value=cust;
	$.ajax({type:"POST",data:{'custrsr':cust},
		url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/RsrBarSortCustomer');?>",
		dataType:"json",success:function(data){if(data){
			var pieChartDataSource=data;
			drowRSRCustomerChart(pieChartDataSource,"graph-rsr-customer");
			drowRSRCustomerChart(pieChartDataSource,"graph-rsr-customer1");}}})
}
function searchCustRSR2(){
	cust=$('#custsearchrsr2').val();
	document.getElementById('custsearchrsr').value=cust;
	$.ajax({type:"POST",data:{'custrsr':cust},
		url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/RsrBarSortCustomer');?>",
		dataType:"json",success:function(data){
			if(data){
				var pieChartDataSource=data;
				drowRSRCustomerChart(pieChartDataSource,"graph-rsr-customer");
				drowRSRCustomerChart(pieChartDataSource,"graph-rsr-customer1");
			}}})
}
</script>	