 <div class="bcontenu">
  <div id="pieInvoiceAging" style="padding-top:20px;" class="style_chart700 graph"></div>
</div>
<?php $id = WidgetInvoiceAging::getId();
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
    'class'=>"unaspeciala",
    'closeOnEscape' => true,
    ), )); ?>
<div class="bcontenu z-index">
  <div class="board inline-block ui-state-default noborder">
    <div class="bhead" id="maispecial">
      <div class="title"><?php echo  WidgetInvoiceAging::getName();?></div>
      <div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
    </div>
    <div class="ftr"></div>
  </div>  <div class="graph" id="graph-invoice-aging1" ></div>
  <div id="pieInvoiceAging1" style="padding-top:20px;"  class="style_chart1000 graph"></div>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<script type="text/javascript">
function changeYearage() { var pieChartDataSource = <?php echo WidgetInvoiceAging::CharChart1(0);?>; barinvaging(pieChartDataSource,0); }; 
function createGridInvAgedisc() { var pieChartDataSource = <?php echo WidgetInvoiceAging::CharChart1(0);?>; barinvaging(pieChartDataSource,1);}; 
function barinvaging(pieChartDataSource,val){$(function(){if(val==1)id="pieInvoiceAging";else id="pieInvoiceAging1";$("#"+id).dxChart({dataSource:pieChartDataSource,commonSeriesSettings:{argumentField:"state",type:"bar",hoverMode:"allArgumentPoints",selectionMode:"allArgumentPoints"},legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"right",verticalAlignment:"bottom",rowCount:2},legend:{visible:false},series:{argumentField:"label",valueField:"value",color:"#ffa500"},tooltip:{enabled:true,
customizeText:function(e){var range=e.argument;$.ajax({type:"POST",data:{"range":range},url:"<?php echo Yii::app()->createAbsoluteUrl('Widgets/ShowRangeInvoices');?>",dataType:"json",success:function(data){if(data){$(".popupwidgetMaint").addClass("z-index");$(".popupwidgetMaint").removeClass("hidden");$(".closepopupwidget").removeClass("hidden");$("#graph-services").html(data.servs)}}})}},valueAxis:{title:{text:"Amount $"}},argumentAxis:{title:"Days",type:"discrete",grid:{visible:true}}})})};
</script> 