<div class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('training_number')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->training_number); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('course_name')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->eCourse->codelkup); ?></div>
</div><div class="view_row">		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('start_date')); ?></div>
		<div class="general_col2 "><?php echo date('d/m/Y', strtotime($model->start_date)); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('end_date')); ?></div>
		<div class="general_col4 "><?php echo  date('d/m/Y', strtotime($model->end_date)); ?></div>
</div><div class="view_row">		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('city')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->city); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('country')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->eCountry->codelkup); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('instructor')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eInstructor->fullname); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('location')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->location); ?></div>
</div><div class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('type')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->eType->codelkup); ?></div></div>
	<?php if( TrainingsNewModule::getCertifiedUsers(Yii::app()->user->id) > 0 ) { ?>
<div  class="view_row">		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('profit')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Yii::app()->format->formatNumber( $model->getTrainingProfit($model->idTrainings) ).' $'); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getItemLabel('cost')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Yii::app()->format->formatNumber($model->getTrainingCosts($model->idTrainings) ).' $'); ?></div>
</div><div class="view_row">	<div class="general_col1 "><?php echo CHtml::encode( $model->getAttributeLabel('revenues')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Yii::app()->format->formatNumber($model->revenues).' $'); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('survey_score')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(isset($model->survey_score)?$model->survey_score." %":""); ?></div></div>
<?php switch ($model->type) { 
	case TrainingsNewModule::TYPE_PRIVATE :
		?>
		<div  class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('man_days')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->man_days); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('md_rate')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->md_rate); ?></div></div>
		
	<div  class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('customer_name')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCustomer->name); ?></div>	</div>
<?php 
		break;
	case TrainingsNewModule::TYPE_PARTNER:
?>
	<div  class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('partner')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->ePartner->name); ?></div>	</div>
<?php 
		break;
	case TrainingsNewModule::TYPE_PUBLIC:
?>
	

	<div  class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('min_participants')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->min_participants); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('confirmed_participants')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->confirmed_participants); ?></div></div>
	<div  class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('cost_per_participant')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Yii::app()->format->formatNumber( $model->cost_per_participant) ); ?></div>	</div>	
<?php 
	break;
} }else{  

	 switch ($model->type) { 
	case TrainingsNewModule::TYPE_PRIVATE :
		?>
	<div  class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('customer_name')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCustomer->name); ?></div>	</div>
<?php 
		break;
	case TrainingsNewModule::TYPE_PARTNER:
?>
	<div  class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('partner')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->ePartner->name); ?></div>	</div>
<?php 
		break;
	case TrainingsNewModule::TYPE_PUBLIC:
?>
	<div  class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('min_participants')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->min_participants); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('confirmed_participants')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->confirmed_participants); ?></div></div>
	<div  class="view_row">	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('cost_per_participant')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Yii::app()->format->formatNumber( $model->cost_per_participant) ); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('survey_score')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(isset($model->survey_score)?$model->survey_score." %":""); ?></div>
		</div>	
<?php 
	break;
} } ?>

<div class="horizontalLine smaller_margin"></div>