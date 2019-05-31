<div class="customer-view perftabs hidden" ><?php if (isset($_GET['id'])){ $userid= $_GET['id']; }else { } 
    if(count($prod_list)>0){    
     $totalexpec=0;     $totalact=0; 
    foreach ($prod_list as $key => $value) { 
    $value['id_phase']=='0'? $actuals=Utils::formatNumber(ProjectsTasks::getTimeSpentperTask($value['id_task'] , $value['id_user']) ): $actuals= Utils::formatNumber( ProjectsPhases::getTimeSpentperPhase($value['id_phase'], $value['id_user']) );  
  	$totalexpec+=$value['expected_mds'];
	$totalact+=$actuals; 
   } 
   $total_productivity=(($totalexpec - $totalact)/$totalexpec)*100;}
     if(count($qual_list)>0){ $qaverage=0; $count=0;
    foreach ($qual_list as $key => $value) { if(isset($value['score'])){ if($value['score']=='0'){ $qaverage++;   }   $count++; } }
    if($count==0){ $count=1;} $total_quality= ($qaverage*100)/$count;  } 
    if(count($ps_list)>0){  $psaverage=0; $count=0; $customer=" "; foreach ($ps_list as $key => $value) {
	if(Projects::getCustomerByProject($value['id_project'])!=$customer){ $count++; $psaverage=  $psaverage+ CustomerSatisfaction::getTotalSurveyProjectType($value['id_project'],$value['surv_type']);
    }   $customer=Projects::getCustomerByProject($value['id_project']); }  $total_ps= ($psaverage/$count)*10;   }
 if(count($cs_list)>0){ $csaverage=0; $count=0; foreach ($cs_list as $key => $value) {     if($value['rate']>2){   $csaverage+=$value['count'];  }
           $count+=$value['count'];  }  $total_cs= (($csaverage*10)/$count)*10;  } $performer=0;  if(count($prod_list)>0){  if ($total_productivity>=0) {
     $performer++;  } else{$performer-- ;} }  if(count($qual_list)>0){  if ($total_quality>=50 ) {   $performer++;  } else{$performer-- ;}  }
     if(count($ps_list)>0){  if ( $total_ps>=50) { $performer++; } else{$performer-- ;} } if(count($cs_list)>0){ if($total_cs>=50){ $performer++; } else{$performer-- ;} } ?>
<div class="userSection">
<?php $form=$this->beginWidget('CActiveForm', array( 'id'=>'performance-form','enableAjaxValidation'=>false,)); ?>  
      <div class="sort"> <?php echo $form->dropDownList($model, 'year', Performance::getYears() , array('onchange'=>'changeYear(this,'.$userid.')','id'=>'perf_year','options'=>$year)); ?>
       </div> <?php $this->endWidget(); ?><div class="perf_left good"><?php $id_doc = SupportDesk::getPicture($userid); ?>
       <div class="pic"><img src="<?php echo Yii::app()->getBaseUrl().'/uploads/users/'.$userid.'/documents/'.$id_doc['id'].'/'.$id_doc['file'];?>"  alt="Image not found"  onError="this.src='../../uploads/pictures/profile.jpg'" width="159" height="140"/></div>
            <div class="cap"><?php if($performer<0){ ?> <div class="caption bad">Bad Performer</div> <?php } ?><?php if($performer==0 ){ ?>    <div class="caption good">Good Performer</div>  <?php } ?>
               <?php if($performer>0 ){ ?>      <div class="caption super">Super Performer </div>  <?php } ?></div><div class="newcap hidden"></div>
          </div><div class="perf_right"><div class="name"><?php echo Users::getNameByID($userid) ; ?></div><div class="position"><?php echo UserPersonalDetails::getJobTitle($userid) ; ?> </div><hr />
               <div class="data"><div class="item"><div class="label">Productivity</div><div class="number"><?php  if(count($prod_list)>0){  echo Utils::formatNumber($total_productivity,2); echo "%";} ?></div>
               </div><div class="item"><div class="label">Quality</div><div class="number"><?php if(count($qual_list)>0){  echo Utils::formatNumber($total_quality,2); echo "%"; } ?></div>
                </div><div class="item"><div class="label" style="padding-right:50px;">Customer Satisfaction</div><div class="smallitem">
                  <?php if(count($ps_list)>0){ ?> <div class="number"><?php  echo Utils::formatNumber($total_ps,2); ?>%</div> <?php } ?> 
                        <div class="perf_note">Project Surveys</div></div><div class="smallitem">
                           <?php if(count($cs_list)>0){ ?>   <div class="number"><?php  echo Utils::formatNumber($total_cs,2); ?>%</div> <?php } ?> 
                        <div class="perf_note">Support Ratings</div></div></div></div><div id="newdata" class="data hidden"> </div></div></div>
       <div class="userData">
<?php $tabs = array();         
if(GroupPermissions::checkPermissions('performance-productivity')){
           $tabs[Yii::t('translations', 'Productivity')] =$this->renderPartial('_productivity', array('userid'=>$userid, 'model'=>$model , 'productivity'=>$productivity , 'prod_list'=>$prod_list ,'pages'=>$pages), true); }
if(GroupPermissions::checkPermissions('performance-quality'))
  {    $tabs[Yii::t('translations', 'Quality')] =$this->renderPartial('_quality', array('userid'=>$userid,  'model'=>$model , 'quality'=>$quality ,'qual_list'=>$qual_list), true); }
if(GroupPermissions::checkPermissions('performance-customer_satisfaction')) 
  {    $tabs[Yii::t('translations', 'Customer Satisfaction')] =$this->renderPartial('_customer_satisfaction', array('userid'=>$userid, 'ps_list'=>$ps_list ,'cs_list'=>$cs_list,  'model'=>$model, 'year'=>$year), true); }
	$this->widget('CCustomJuiTabs', array('tabs'=>$tabs,'options'=>array('collapsible'=>false,'active' =>  'js:configJs.current.activeTab',    ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',)); ?> </div> </div>
 <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>
<script type="text/javascript">
 (function($){
        $(window).load(function(){ $(".datacontainer").mCustomScrollbar({ advanced:{ updateOnContentResize: true}});
           $(".cspsdatacontainer").mCustomScrollbar({ advanced:{ updateOnContentResize: true}});             
          $('#popupcs').hide(); $('#popupps').hide(); $('#qajxLoader').hide(); });  })(jQuery);
 $(".fcs").click(function() {  var customer= $(this).attr('customer'); var user= $(this).attr('user'); var year= $(this).attr('year');
         cshover(customer,user,year);  $('#popupcs').stop().show();  } );
$(".closefcs").click(function() { $('#popupcs').stop().hide(); } );
 $(".fq").click(function(){ var project= $(this).attr('project'); var type= $(this).attr('type');
         pshover(project,type);  $('#popupps').stop().show(); });
$(".closefq").click(function() { $('#popupps').stop().hide(); });
$(".prodproj").hover(function() { var project=$(this).attr('project');  showToolTip(document.getElementById('prodproj_'+project));
    },function() { var project=$(this).attr('project');   hideToolTip(document.getElementById('prodproj_'+project)); } );
$(".prodtask").hover(function() { var project=$(this).attr('project');  showToolTaskTip(document.getElementById('prodtask_'+project));
    },function() { 
      var project=$(this).attr('project'); hideToolTaskTip(document.getElementById('prodtask_'+project)); }  );
function editScore(taskid){ var score=document.getElementById('qual_score_'+taskid).value;
  $.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('performance/editScore');?>", dataType: "json", data: {'score': score , 'taskid':taskid },
          success: function(data) {
           if (data) {  document.getElementById('score_'+taskid).innerHTML= data.score==0?"Yes":"No"; } } }); }
function pshover(project,surv_type){
   $.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('performance/readsurvey');?>", dataType: "json", data: {'project': project , 'surv_type':surv_type  },
          success: function(data)
          { if(data){
             if(data.status=="success"){ $(".surveyscontainer").html(" "); $(".surveyscontainer").append(data.readsurvey); 
                $(".titre").html(" "); $(".titre").append('Project Survey <b>'+data.pname+'</b>'); } } } }); }
function cshover(customer,user,year){
   $.ajax({  type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('performance/checkrates');?>", dataType: "json", data: {'customer': customer , 'user':user , 'year':year},
          success: function(data)
          { if(data){
             if(data.status=="success"){
                 $(".ratingcontainer").html(" "); $(".ratingcontainer").append(data.checkrates); $(".cstitre").html(" ");
                    $(".cstitre").append('SR Ratings for: <b>'+data.pname+'</b>'); $(".ratingcontainer").mCustomScrollbar({
              advanced:{ updateOnContentResize: true}});
             } } } }); }
function hidecshover(){  $('#popupcs').addClass("hidden");}
$("#expec_date").datepicker({ dateFormat: 'dd/mm/yy' });
function refreshTaskList(tab){ 
      if(tab=='P'){ var project_id = $("#prod_proj").val();      
    }else{  var project_id = $("#qual_proj").val(); }           
      if(project_id == ''){
        var action_buttons = {
                "Ok": {
                    click: function() 
                    {
                        $(this).dialog('close');
                    },
              class: 'ok_button'  
                }
          };
        custom_alert('ERROR MESSAGE', 'You must select a project in order to choose tasks!', action_buttons);
      }else{ 
          $.ajax({ type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('performance/getTasks');?>",dataType: "json",data: {'id_project': project_id , 'id_user': <?php echo $userid; ?> },
          success: function(tasks){ 
            var tasksFormInputs = '<option value=""> Choose Phase/Task</option>'; var phaseArr = new Array();
            $.each(tasks, function(index, task){ if(phaseArr.indexOf(task.id_phase) == -1){
                phaseArr.push(task.id_phase);
                tasksFormInputs += '<option style="color:#CC0C22" id="phase_' + task.id_phase + '" value="phase_' + task.id_phase + '" > Phase: '+ task.phase_name+ '</option>';
              } tasksFormInputs += ''+ '<option id="task_' + task.id + '" value="task_' + task.id + '" > &nbsp; Task: ' + task.name+ '</option>'; });     
            if(tasksFormInputs == ''){
              var action_buttons = {
                      "Ok": {
                          click: function() 
                          {
                              $(this).dialog('close');
                          },
                    class: 'ok_button'  
                      }
                };
              custom_alert('ERROR MESSAGE', 'There are no tasks in this project!', action_buttons);
            }else{
              if(tab=='P'){
                $('#prod_task').html(tasksFormInputs); $('#prod_task').show(); } else{
                $('#qual_task').html(tasksFormInputs); $('#qual_task').show();} } } }); } }
    function SaveProductivity(){
      var id_project= document.getElementById('prod_proj').value; var id_task_phase = document.getElementById('prod_task').value; var estimate = document.getElementById('prod_est').value;
$.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('performance/SaveProductivity');?>",
          dataType: "json",data: {'id_project': id_project , 'id_user': <?php echo $userid; ?> , 'id_task_phase':id_task_phase , 'estimate': estimate },
          beforeSend: function() { $("#ajxLoader").fadeIn(); },
            complete: function() { $("#ajxLoader").fadeOut(); },
          success: function(data){ 
              if(data){ if(data.status=="success"){ $('#newlyprodadded').prepend(data.div); $('.delete_P'+data.taskid).attr('onclick', "deleteButton('P', "+data.taskid+")");
                $('.taskAdd').hide(); $('.add').show(); document.getElementById('productivity-form').reset(); $('#prod_task').html(" ");
                  $(".prodproj").hover(function(){ var project=$(this).attr('project'); showToolTip(document.getElementById('prodproj_'+project));
                      },function(){ 
                        var project=$(this).attr('project'); hideToolTip(document.getElementById('prodproj_'+project)); } ); 
						$(".prodtask").hover(function(){ var project=$(this).attr('project'); showToolTaskTip(document.getElementById('prodtask_'+project));
                      },function(){ var project=$(this).attr('project'); hideToolTaskTip(document.getElementById('prodtask_'+project)); } );
              }else{  document.getElementById('perr').innerHTML=data.message; $('#perr').show(); } } } }); }
    function SaveQuality(){  var id_project= document.getElementById('qual_proj').value;  var id_task_phase = document.getElementById('qual_task').value; 
      var resc = document.getElementById('qual_resc').value; var expec_date = document.getElementById('expec_date').value;        
      var notes = document.getElementById('qual_notes').value;
       $.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('performance/SaveQuality');?>",
          dataType: "json", data: {'id_project': id_project , 'id_user': <?php echo $userid; ?> , 'resc': resc , 'id_task_phase':id_task_phase , 'expec_date': expec_date , 'notes':notes  },
           beforeSend: function() { $("#qajxLoader").fadeIn(); },
            complete: function() { $("#qajxLoader").fadeOut(); },
          success: function(data,message){ 
		  if(data){    if(data.status=="success"){ $('#newlyqualadded').prepend(data.div); $('.delete_Q'+data.taskid).attr('onclick', "deleteButton('Q', "+data.taskid+")");
                $('.qualAdd').hide(); $('.qadd').show(); document.getElementById('quality-form').reset(); $('#qual_task').html(" ");
                  $(".prodproj").hover(function(){ var project=$(this).attr('project'); showToolTip(document.getElementById('prodproj_'+project));
                      },function() { 
                        var project=$(this).attr('project');   hideToolTip(document.getElementById('prodproj_'+project));                          
                      } );
                  $(".prodtask").hover(function() {
                         var project=$(this).attr('project');  showToolTaskTip(document.getElementById('prodtask_'+project));
                      },function() { var project=$(this).attr('project');  hideToolTaskTip(document.getElementById('prodtask_'+project));  }  );   
              }else{  document.getElementById('qerr').innerHTML=data.message;  $('#qerr').show();  } } } }); }
function deleteButton(element,taskid){
  buttons = {
            "YES": {
              class: 'yes_button',
              click: function(){
                  $( this ).dialog( "close" );               
                 if(element=='P'){ deleteTask('P', taskid ); }else{ deleteTask('Q', taskid ); }
              }
            },
            "NO": {
              class: 'no_button',
              click: function(){
                  $( this ).dialog( "close" );
              }
            }
    }
    custom_alert("DELETE MESSAGE", "Are you sure you want to delete this task?", buttons);
}
function deleteTask(tasktype,taskid) {
  $.ajax({ type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('performance/deleteTask');?>",dataType: "json",data: {'tasktype': tasktype , 'taskid':taskid },
          success: function(data) {
           if (data){ if (data.status == 'success') { $('#'+tasktype+taskid).addClass('hidden'); } } } }); }
function updateReason(id, text){   
  $.ajax({  type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('performance/updateReason');?>", dataType: "json",data: {'reason': text , 'prod':id },
          success: function(data) { if (data) { } } }); }
function assignDate (date, id){
  $.ajax({  type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('performance/assignDate');?>",  dataType: "json",  data: {'date': date , 'taskid':id },
          success: function(data) {  if (data) {  }  }  }); }
function assignQA(taskid){
var user=document.getElementById('qual_resc_'+taskid).value;
  $.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('performance/assignQA');?>", dataType: "json", data: {'user': user , 'taskid':taskid },
          success: function(data) {
           if (data) { }   }  }); }
function changeYear(year,userid){
  if(year.value=='2017'){
        $('.table').addClass('hidden'); $('#currentyear').removeClass('hidden'); $('#currentqualyear').removeClass('hidden');
        $('#currentcsyear').removeClass('hidden'); $('#currentpsyear').removeClass('hidden');  $('.add').removeClass('hidden');
        $('.qadd').removeClass('hidden');  $('.taskAdd').removeClass('hidden'); $('.qualAdd').removeClass('hidden');
        $('.hiddenadd').addClass('hidden');  $('.data').removeClass('hidden');  $('#newdata').addClass('hidden');
		$('.cap').removeClass('hidden');  $('.newcap').addClass('hidden');
  }else{
  $.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('performance/changeYear');?>", dataType: "json", data: {'year': year.value , 'userid':userid},          
          success: function(data) {
           if (data) {   if(data.status=='success'){   $('.table').addClass('hidden');   $('.add').addClass('hidden');  $('.qadd').addClass('hidden');
                        $('.taskAdd').addClass('hidden');   $('.qualAdd').addClass('hidden');
                        $('.hiddenadd').removeClass('hidden');  $('.cap').addClass('hidden');   $('.newcap').html(' ');
                        $('.newcap').prepend(data.perf);   $('.newcap').removeClass('hidden'); $('#newprodtable').prepend(data.prod_div);
						$('#newprodtable').removeClass('hidden'); $('#newqualtable').prepend(data.qual_div);  $('#newqualtable').removeClass('hidden');
                        $('#newcstable').prepend(data.cs_div); $('#newcstable').removeClass('hidden'); $('#newpstable').prepend(data.ps_div);
                        $('#newpstable').removeClass('hidden'); $('.data').addClass('hidden'); $('#newdata').html(' ');
                        $('#newdata').html(data.sum); $('#newdata').removeClass('hidden'); $(".newdatacontainer").mCustomScrollbar({
              advanced:{ updateOnContentResize: true}});  
                  $(".newcspsdatacontainer").mCustomScrollbar({
              advanced:{ updateOnContentResize: true}}); 
                  $(".prodproj").hover(function() {  var project=$(this).attr('project');  showToolTip(document.getElementById('prodproj_'+project));
                      },function() {
					  var project=$(this).attr('project');  hideToolTip(document.getElementById('prodproj_'+project));                           
                      } );
                  $(".prodtask").hover(function() { var project=$(this).attr('project');  showToolTaskTip(document.getElementById('prodtask_'+project));
                      },function() { 
                        var project=$(this).attr('project');  hideToolTaskTip(document.getElementById('prodtask_'+project)); } );
                     $(".fcs").click(function() {  var customer= $(this).attr('customer');  var user= $(this).attr('user');
                            var year= $(this).attr('year'); cshover(customer,user,year);  $('#popupcs').css("top", "370px");  $('#popupcs').stop().show();  } );
                    $(".closefcs").click(function() {  $('#popupcs').stop().hide(); }  );
                     $(".fq").click(function() {  var project= $(this).attr('project');  var type= $(this).attr('type');
                             pshover(project,type);  $('#popupps').stop().show(); } );
                    $(".closefq").click(function() { $('#popupps').stop().hide();  }  ); } } } }); } }
 </script>
 

