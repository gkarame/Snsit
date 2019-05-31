<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){  $('.search-form').toggle();  return false;});$('.search-form form').submit(function(){  $.fn.yiiGridView.update('users-grid', {    data: $(this).serialize()  });  return false;});");?>
<div class="search-form">
<?php $this->renderPartial('_search',array( 'model'=>$model,)); ?></div>
<?php  $this->widget('zii.widgets.grid.CGridView', array('id'=>'users-grid', 'dataProvider'=>$model->search(), 'summaryText' => '',
  'selectableRows'=>1, 'selectionChanged'=>'function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "'.$this->createUrl('view').'/"+idg;}',
  'pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}', 'columns'=>array('firstname','lastname',array(
      'name' => 'userPersonalDetails.job_title','value' => 'isset($data->userPersonalDetails->job_title) ? $data->userPersonalDetails->job_title : ""'),
    array('name' => 'userPersonalDetails.unit','value' => 'isset($data->userPersonalDetails->unit) ? Codelkups::getCodelkup($data->userPersonalDetails->unit) : ""'
    ),array('name' => 'status','value'=>'$data->isActiveAsString()','htmlOptions' => array('class' => 'width62'),'headerHtmlOptions' => array('class' => 'width62'),
        )  ),)); ?>
<script type="text/javascript">
function getExcel() {     $('.action_list').hide();   window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('performance/getExcel');?>/?");  }
</script>