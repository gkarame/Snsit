<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_customer_top 5"  data-id="5"  onClick="changeTopC(5)"><i>Top 5 / </i></span>
 			<span class="status status_customer_top 10" data-id="10" onClick="changeTopC(10)"><i>Top 10 / </i></span>
 			<span class="status status_customer_top 20" data-id="20" onClick="changeTopC(20)"><i>Top 20 / </i></span>
 		<?php foreach ($years as $k=>$year){?>
 			<span class="status status_customer_year <?php echo $year?>" data-id ="<?php echo $year;?>"  onClick="changeYear(<?php echo $year?>)"><i><?php echo $year." / "; ?></i></span>
 		<?php }?>
 	</div>
 	<div class="style_chart700 graph" id="graphCustomer-1" ></div>
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
    ), )); ?>
<div class="bcontenu z-index">
 	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetCustomers::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_customer_top 5" onClick="changeTopC(5)"><i>Top 5 / </i></span>
 			<span class="status status_customer_top 10" onClick="changeTopC(10)"><i>Top 10 / </i></span>
 			<span class="status status_customer_top 20" onClick="changeTopC(20)"><i>Top 20 / </i></span>
 		<?php foreach ($years as $k=>$year){?>
 			<span class="status status_customer_year <?php echo $year?>" onClick="changeYear(<?php echo $year?>)"><i><?php echo $year." / "; ?></i></span>
 		<?php }?>
 	</div>
 	<div class="style_chart1000 graph" id="graphCustomer-11" ></div>
 </div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<script type="text/javascript">
function createGridTime1(){var pieChartDataSource=<?php echo WidgetCustomers::CharChart()?>;drowChart(pieChartDataSource,"graphCustomer-1");drowChart(pieChartDataSource,"graphCustomer-11");$('.status_customer_top.5').addClass("colorRed");$('.status_customer_year.'+<?php echo date('Y');?>).addClass("colorRed");}
function changeYear(year){$.ajax({type:"POST",data:{'year':year},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/CustomerPieSort');?>",dataType:"json",success:function(data){if(data){$('.status_customer_year').removeClass("colorRed");var pieChartDataSource=data;drowChart(pieChartDataSource,"graphCustomer-1");drowChart(pieChartDataSource,"graphCustomer-11");$('.status_customer_year.'+year).addClass("colorRed");}}});}
function changeTopC(top){$.ajax({type:"POST",data:{'top':top},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/CustomerPieSort');?>",dataType:"json",success:function(data){if(data){$('.status_customer_top').removeClass("colorRed");var pieChartDataSource=data;drowChart(pieChartDataSource,"graphCustomer-1");drowChart(pieChartDataSource,"graphCustomer-11");$('.status_customer_top.'+top).addClass("colorRed");}}});}
</script>	