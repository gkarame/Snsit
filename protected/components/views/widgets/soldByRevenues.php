<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_soldBy_revenues_current sr_soldBy_revenues" data-id="current" onClick="changeSoldByYear('current')"><i> <?php echo Yii::t('translations', 'Current Year'); ?> / </i></span>
 			<span class="status status_soldBy_revenues_last sr_soldBy_revenues" data-id="last" onClick="changeSoldByYear('last')"><i> <?php echo Yii::t('translations', 'Last Year'); ?> /</i></span>
 			<span class="status status_soldBy_revenues_last3 sr_soldBy_revenues" data-id="last3" onClick="changeSoldByYear('last3')"><i> <?php echo Yii::t('translations', 'Last 3 Years'); ?></i></span>
 	</div> 	<div class="style_chart700 graph" id="graph-soldBy-revenues" ></div>
</div>
<?php $id = WidgetSoldByRevenues::getId();
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
			<div class="title"><?php echo  WidgetSoldByRevenues::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_soldBy_revenues_current sr_soldBy_revenues" onClick="changeSoldByYear('current')"><i> <?php echo Yii::t('translations', 'Current Year'); ?> / </i></span>
 			<span class="status status_soldBy_revenues_last sr_soldBy_revenues" onClick="changeSoldByYear('last')"><i> <?php echo Yii::t('translations', 'Last Year'); ?> /</i></span>
 			<span class="status status_soldBy_revenues_last3 sr_soldBy_revenues" onClick="changeSoldByYear('last3')"><i> <?php echo Yii::t('translations', 'Last 3 Years'); ?></i></span>
 	</div> 	<div class="style_chart1000 graph" id="graph-soldBy-revenues1" ></div> 	
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">function createGridSoldRevenues(){var pieChartDataSource=<?php echo WidgetSoldByRevenues::CharChart('current')?>;drowChart(pieChartDataSource,"graph-soldBy-revenues");drowChart(pieChartDataSource,"graph-soldBy-revenues1");$('.status_soldBy_revenues_current').addClass("colorRed")}
function changeSoldByYear(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/SoldByRevenues');?>",dataType:"json",success:function(data){if(data){$('.sr_soldBy_revenues').removeClass("colorRed");var pieChartDataSource=data;drowChart(pieChartDataSource,"graph-soldBy-revenues");drowChart(pieChartDataSource,"graph-soldBy-revenues1");$('.status_soldBy_revenues_'+val).addClass("colorRed")}}})}
</script>	