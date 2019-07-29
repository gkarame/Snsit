<?php
/* @var $this EasController */
/* @var $model Eas */
/* @var $form CActiveForm */
?>

<div class="wide search">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'post',
        'id'=>'licensing-form',
    )); ?>

    <div class="row margint10">
        <div class="selectBg_search">
            <?php echo $form->labelEx($model,'id_user'); ?>
            <span class="spliter"></span>
            <div class="select_container width111" >
                <?php
                $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
                    'model' => $model,
                    'attribute' => 'id_user',
                    'source'=>vacationAudit::getAllAutocomplete(),
                    // additional javascript options for the autocomplete plugin
                    'options'=>array(
                        'minLength'	=>'0',
                        'showAnim'	=>'fold'
                    ),
                    'htmlOptions'	=>array(
                        'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
                        'class'=>'width111',

                    ),
                ));
                ?>
            </div>
        </div>
    </div>

    <div class="row margint10">
        <div class="selectBg_search">
            <?php echo $form->labelEx($model,'year'); ?>
            <span class="spliter"></span>
            <div class="select_container width111" >
                <?php echo $form->telField($model, 'year'); ?>
            </div>
        </div>
    </div>

    <div class="row margint10">
        <div class="selectBg_search">
            <?php echo $form->labelEx($model,'branch'); ?>
            <span class="spliter"></span>
            <div class="select_container width111" >
                <?php echo $form->dropDownList($model, 'branch', Codelkups::getCodelkupsDropDown('branch'), array('prompt' => Yii::t('translations', 'All'))); ?>
            </div>
        </div>
        <?php echo $form->error($model,'branch'); ?>
    </div>

    <div class="row margint10">
        <div class="selectBg_search">
            <?php echo $form->labelEx($model,'format'); ?>
            <span class="spliter"></span>
            <div class="select_container width111" >
                <?php echo $form->dropDownList($model, 'format', array('Excel'=>'Excel','Pdf'=>'Pdf'), array('prompt' => Yii::t('translations', 'Choose formats'))); ?>
            </div>
        </div>
    </div>



    <div class="btn">
        <?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>

    </div>
    <div class="horizontalLine search-margin"></div>


    <?php $this->endWidget(); ?>

</div><!-- search-form -->