 <div class="bcontenu"> <div class="stat_years" style="padding-bottom:-10px !important;">
 <span class="spliter status"  style="margin-left:12px;">Users</span>
        <span class="status type_cs 150" id="150" >
        <?php unset($_SESSION['userAging']);
        $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Users::model(),
            'attribute' => 'id',   
            'source'=>Users::getAllAutocompleteTech(),
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold', ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width131",
               'id'   => "usersearchAging",
              'style'=> "margin-left:5px;",),  )); ?></span>
      <span class="spliter status" style="margin-top:-9px;" onclick="searchUserAging()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
    </div> <div id="pieSrAging" class="style_chart700 graph"></div>
</div>
<?php $id = WidgetSrsAging::getId();
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
    'closeOnEscape' => true,    ),));?>
<div class="bcontenu z-index">
  <div class="board inline-block ui-state-default noborder">
    <div class="bhead" id="maispecial">
      <div class="title"><?php echo  WidgetSrsAging::getName();?></div>
      <div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
    </div>  <div class="ftr"></div>
  </div>
  <div class="stat_years">
    <span class="spliter status"  style="margin-left:20px;"> Users</span>
        <span class="status type_cs 150" id="150" >
        <?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model' => Users::model(),
            'attribute' => 'id',   
            'source'=>Users::getAllAutocompleteTech(),
            // additional javascript options for the autocomplete plugin
            'options'=>array(
              'minLength' =>'0',
              'showAnim'  =>'fold',  ),
            'htmlOptions' =>array(
              'onfocus'   => "javascript:$(this).autocomplete('search','');",
              'class'   => "width161",
               'id'   => "usersearchAging2",
              'style'=> "margin-left:10px; ", ),  ));
        ?></span>
	<span class="spliter status" onclick="searchUserAging2()" >  <img  style="margin-left:10px;" width='22'  src="<?php echo Yii::app()->request->baseUrl; ?>/images/widgetsearch.png"></span>
     <div class="graph" id="graph-sr-aging1" ></div>  <div id="pieSrAging1" class="style_chart1000 graph"></div>    
</div></div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
<script type="text/javascript">
function changeSRage(){
	 var pieChartDataSource = <?php echo WidgetSrsAging::CharChart1(0);?>;  barsraging(pieChartDataSource,0);   
}
function createGridSrAgedisc() {
      var pieChartDataSource = <?php echo WidgetSrsAging::CharChart1(0);?>;    barsraging(pieChartDataSource,1);  
}; 
function barsraging(pieChartDataSource,val){
  $(function () {
    if(val == 1){   id = "pieSrAging";  user=$('#usersearchAging').val();	}
    else{     id = "pieSrAging1";    user=$('#usersearchAging2').val();}
     $("#"+id).dxChart({
          dataSource: pieChartDataSource,
          commonSeriesSettings: {  argumentField: "state",   type: "bar",  hoverMode: "allArgumentPoints",   selectionMode: "allArgumentPoints" },
          legend: {  orientation: "horizontal",  itemTextPosition: "right",    horizontalAlignment: "right",    verticalAlignment: "bottom",  rowCount: 10 },
                legend: {      visible: false,       },
          series: {  argumentField: "label",    valueField: "value", color: '#ffa500', },
          tooltip: {   enabled: true,  customizeText: function (e) {
                  var range2 = e.argument;
                  $.ajax({
                            type: "POST",     data: {'range2':range2, 'userAging':user},          
                              url: "<?php echo Yii::app()->createAbsoluteUrl('Widgets/ShowRangeSrs');?>", 
                              dataType: "json",
                              success: function(data) {    if (data) { 
                                 $('.popupwidgetsrs').addClass('z-index');       $('.popupwidgetsrs').removeClass('hidden');                        
                         $('.closepopupwidget').removeClass('hidden');   $('#srs-services').html(data.srs);                                               
                      }
                     }
                   });
             }
       },valueAxis: {   title: {  text: "SR#"  } },
          argumentAxis: {  title: 'Days',    type: 'discrete',   grid: {    visible: true   }        }      });
  });
}
function searchUserAging2(){
<?php unset($_SESSION['userAging']);?>  user=$('#usersearchAging2').val(); 
  $.ajax({
    type: "POST",  data: {'userAging':user},    url: "<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrsAgingUser');?>", 
      dataType: "json",     success: function(data) {      if (data) {         
            var val = data;   barsraging(val,0);  
      }
     }
});  }
function searchUserAging(){
<?php unset($_SESSION['userAging']);?>
  user=$('#usersearchAging').val();
  $.ajax({
    type: "POST",   data: {'userAging':user},    url: "<?php echo Yii::app()->createAbsoluteUrl('Widgets/SrsAgingUser');?>", 
      dataType: "json",  success: function(data) {    if (data) {         
            var val = data;     barsraging(val,1);           
        }
     }
}); }
</script> 