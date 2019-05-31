<div class="bcontenu revenues">
 	<?php $snsRevenues = WidgetRevenues::getRevenues('No');  $oldRevenues = WidgetRevenues::getRevenues('Yes'); ?>
<div id="widget_projects_revenues">
 		<div class="boardrow color333">
		 	<div class="width215 inline-block">	<span class="width215"><?php echo Yii::t('translations', 'Month'); ?></span></div>
		 	<div class="width215 inline-block"><span class="width215"><?php echo Yii::t('translations', 'Total Revenues'); ?></span></div>
		 	<div class="width215 inline-block"><span class="width215"><?php echo Yii::t('translations', 'SNS Revenues'); ?></span></div>
		 	<div class="width215 inline-block nobackground"><span class="width215"><?php echo Yii::t('translations', 'Old Revenues'); ?></span></div>
		</div>
		<?php	$tr = 0; $sr = 0; $or = 0;		for ($i = 0; $i < 12; $i++) { $monthIndex = date('n', strtotime("+{$i} months"));  $monthName = date('F-Y', strtotime("+{$i} months")); ?>
			<div class="boardrow odd-even" >
				<div class="width215 inline-block">	 <span class="width215"><?php echo $monthName;?></span></div>
			 	<div class="width215 inline-block"> <span  class="width215"><?php echo Utils::formatNumber($snsRevenues[$monthIndex] + $oldRevenues[$monthIndex]); ?> USD</span>
			 		<?php $tr += $snsRevenues[$monthIndex] + $oldRevenues[$monthIndex]?>	</div>
			 	<div class="width215 inline-block"><span  class="width215"><?php echo Utils::formatNumber($snsRevenues[$monthIndex]); ?> USD</span>
			 		<?php $sr += $snsRevenues[$monthIndex]; ?></div>
			 	<div class="width215 inline-block nobackground"><span  class="width215"><?php echo Utils::formatNumber($oldRevenues[$monthIndex]); ?> USD</span>
			 		<?php $or += $oldRevenues[$monthIndex]; ?></div>
			</div>	
		<?php }  $snsRevenuesBlank = WidgetRevenues::getRevenuesBlank('No');  $oldRevenuesBlank = WidgetRevenues::getRevenuesBlank('Yes'); ?>
		<div class="boardrow odd-even" >
				<div class="width215 inline-block"><span class="width215"><?php echo "Blank";?></span></div>
			 	<div class="width215 inline-block"><span  class="width215"><?php echo Utils::formatNumber($snsRevenuesBlank['empty'] + $oldRevenuesBlank['empty']); ?> USD</span>
			 		<?php $tr += $snsRevenuesBlank['empty'] + $oldRevenuesBlank['empty']?></div>
			 	<div class="width215 inline-block"><span  class="width215"><?php echo Utils::formatNumber($snsRevenuesBlank['empty']); ?> USD</span>
			 		<?php $sr += $snsRevenuesBlank['empty']; ?></div>
			 	<div class="width215 inline-block nobackground"><span  class="width215"><?php echo Utils::formatNumber($oldRevenuesBlank['empty']); ?> USD</span>
			 		<?php $or += $oldRevenuesBlank['empty']; ?></div>
			</div>	
		<div class="boardrow odd-even" >
				<div class="width215 inline-block"><span class="width215 bold">TOTAL</span></div>
			 	<div class="width215 inline-block"><span  class="width215 bold"><?php echo Utils::formatNumber($tr) ?> USD</span></div>
			 	<div class="width215 inline-block"><span  class="width215 bold"><?php echo Utils::formatNumber($sr) ?> USD</span></div>
			 	<div class="width215 inline-block nobackground"><span  class="width215 bold"><?php echo Utils::formatNumber($or); ?> USD</span></div>
		</div>	
	</div>
</div><?php $id = WidgetRevenues::getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
    'options'=>array(
        'title'=>'Dialog box 1',
        'autoOpen'=>false,
		'modal'=>true,
	    'width'=>1006,
	    'height'=>810,
		'resizable'=>false,
		'closeOnEscape' => true,
    ),));?>
<div class="bcontenu revenues z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetRevenues::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div>	<div class="ftr"></div>
	</div>
 	<?php $snsRevenues = WidgetRevenues::getRevenues('No'); $oldRevenues = WidgetRevenues::getRevenues('Yes'); ?>
 	<div id="widget_projects_revenues" class="bigsize">
 		<div class="boardrow color333">
		 	<div class="width215 inline-block">	<span class="width215"><?php echo Yii::t('translations', 'Month'); ?></span></div>
		 	<div class="width215 inline-block"><span class="width215"><?php echo Yii::t('translations', 'Total Revenues'); ?></span></div>
		 	<div class="width215 inline-block"><span class="width215"><?php echo Yii::t('translations', 'SNS Revenues'); ?></span></div>
		 	<div class="width215 inline-block nobackground"><span class="width215"><?php echo Yii::t('translations', 'Old Revenues'); ?></span></div>
		</div>
		<?php $tr = 0; $sr = 0; $or = 0; for ($i = 0; $i < 12; $i++) {  $monthIndex = date('n', strtotime("+{$i} months"));  $monthName = date('F-Y', strtotime("+{$i} months")); ?>
			<div class="boardrow odd-even" >
				<div class="width215 inline-block"><span class="width215"><?php echo $monthName;?></span></div>
			 	<div class="width215 inline-block"><span  class="width215"><?php echo Utils::formatNumber($snsRevenues[$monthIndex] + $oldRevenues[$monthIndex]); ?> USD</span>
			 		<?php $tr += $snsRevenues[$monthIndex] + $oldRevenues[$monthIndex]?></div>
			 	<div class="width215 inline-block"><span  class="width215"><?php echo Utils::formatNumber($snsRevenues[$monthIndex]); ?> USD</span>
			 		<?php $sr += $snsRevenues[$monthIndex]; ?></div>
			 	<div class="width215 inline-block nobackground"><span  class="width215"><?php echo Utils::formatNumber($oldRevenues[$monthIndex]); ?> USD</span>
			 		<?php $or += $oldRevenues[$monthIndex]; ?></div>
			</div><?php } ?>
		<div class="boardrow odd-even" >
				<div class="width215 inline-block"><span class="width215 bold">TOTAL</span></div>
			 	<div class="width215 inline-block"><span  class="width215 bold"><?php echo Utils::formatNumber($tr) ?> USD</span></div>
			 	<div class="width215 inline-block"><span  class="width215 bold"><?php echo Utils::formatNumber($sr) ?> USD</span></div>
			 	<div class="width215 inline-block nobackground"><span  class="width215 bold"><?php echo Utils::formatNumber($or); ?> USD</span></div>
			</div>
		</div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>