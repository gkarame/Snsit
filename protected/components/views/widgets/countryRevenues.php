<div class="bcontenu">
 	<div class="stat_years">
 			<span class="status status_country_revenues_current sr_country_revenues" data-id="current" onClick="changeOldYear('current')"><i> <?php echo Yii::t('translations', 'Current Year'); ?> / </i></span>
 			<span class="status status_country_revenues_last sr_country_revenues" data-id="last" onClick="changeOldYear('last')"><i> <?php echo Yii::t('translations', 'Last Year'); ?> /</i></span>
 			<span class="status status_country_revenues_last3 sr_country_revenues" data-id="last3" onClick="changeOldYear('last3')"><i> <?php echo Yii::t('translations', 'Last 3 Years'); ?></i></span>
 	</div> 	<div id="graph-country-revenues" class="style_chart700 graph"></div>
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
			<div class="title"><?php echo  WidgetCountryRevenues::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>
		<div class="ftr"></div>
	</div>
 	<div class="stat_years">
 			<span class="status status_country_revenues_current sr_country_revenues" onClick="changeOldYear('current')"><i> <?php echo Yii::t('translations', 'Current Year'); ?> / </i></span>
 			<span class="status status_country_revenues_last sr_country_revenues"  onClick="changeOldYear('last')"><i> <?php echo Yii::t('translations', 'Last Year'); ?> /</i></span>
 			<span class="status status_country_revenues_last3 sr_country_revenues" data-id ="last3" onClick="changeOldYear('last3')"><i> <?php echo Yii::t('translations', 'Last 3 Years'); ?></i></span>
 	</div>
 	<div class="style_chart1000 graph" id="graph-country-revenues1" ></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function createGridCountry(){var pieChartDataSource=<?php echo WidgetCountryRevenues::CharChart('current')?>;drowChart(pieChartDataSource,"graph-country-revenues");drowChart(pieChartDataSource,"graph-country-revenues1");$('.status_country_revenues_current').addClass("colorRed");}
function changeOldYear(val){$.ajax({type:"POST",data:{'val':val},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/CountryRevenues');?>",dataType:"json",success:function(data){if(data){$('.sr_country_revenues').removeClass("colorRed");var pieChartDataSource=data;drowChart(pieChartDataSource,"graph-country-revenues");drowChart(pieChartDataSource,"graph-country-revenues1");$('.status_country_revenues_'+val).addClass("colorRed");}}});}
</script>	