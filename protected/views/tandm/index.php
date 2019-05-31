<?php $id=$model->id; ?>
	<div id="popuptandm" style="width:500px"> <div class='titre red-bold'>Input Rate</div> <div class='closetandm'> </div>	<div class='tandratecontainer'></div> 
		<div class='submitandm'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:135px;' ,'onclick' => 'submitRates();return false;','id'=>'createbut')); ?>
		</div>	</div><div class="search-form"  style="overflow: inherit;"><?php $this->renderPartial('_search',array(	'model'=>$model,)); ?></div>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'tandm-grid','dataProvider'=>$model->search(),'summaryText' => '','pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}',
	'columns'=>array(
		array('class'=>'CCheckBoxColumn',	'id'=>'checktandm','value'=>'$data->id_project.",".$data->tandm_month.",".$data->tandm_year',	'htmlOptions' => array('class' => 'item checkbox_grid_tandm'),'selectableRows'=>2,),
		array('header'=>Yii::t('translations', 'Customer'),'value'=>'Customers::getNameById(Projects::getCustomerByProject($data->id_project))','name' => 'project_id','htmlOptions' => array('class' => 'color')),
        array('header'=>Yii::t('translations', 'Project Name'),'value'=>'Projects::getNameById($data->id_project)','name' => 'project_name','htmlOptions' => array('class' => 'color')     ),
        array('header'=>Yii::t('translations', 'status'),'value'=>'$data->status','name' => 'status','htmlOptions' => array('class' => 'color')),
      array('header'=>Yii::t('translations', 'EA#'),'value'=>'$data->renderEANumber()','name' => 'project_name','htmlOptions' => array('class' => 'color')),
		

         array('header'=>Yii::t('translations', 'Total Hrs'),'value'=>'TandM::getAmounttimeByProject($data->id_project,$data->tandm_month,$data->tandm_year)','name' => 'time','htmlOptions' => array('class' => 'color','style'=>'text-align:center;width:65px;')),
         array(  'header'=>Yii::t('translations', 'Billable Hrs'),'value'=>'TandM::getAmounttimeByProjectbillable($data->id_project,$data->tandm_month,$data->tandm_year)','name' => 'time','htmlOptions' => array('class' => 'color','style'=>'text-align:center;width:65px;')),
         array('header'=>Yii::t('translations', 'Amount'),'value'=>'Utils::formatNumber(TandM::getAmountByProject($data->id_project,$data->tandm_month,$data->tandm_year),2)','name' => 'time','htmlOptions' => array('class' => 'color','style'=>'text-align:center;width:65px;')),
         array( 'header'=>Yii::t('translations', 'Date'),'value'=>'substr(Utils::formatDate($data->tandm_month,"!m","F"),0,3)." - ".$data->tandm_year;','name' => 'month','htmlOptions' => array('class' => 'color','style'=>'width:70px;')),
         array( 'header'=>Yii::t('translations', 'Resources'),'value'=>'$data->getResources($data->id_project,$data->tandm_month,$data->tandm_year)','name' => 'resources','htmlOptions' => array('class' => 'color')),),)); ?>
<br clear="all"><br>
<script type="text/javascript">
	var interval = null;
	$(document).ready(function(){		submitted_loc = false;		$('.deletInv').hide();		$('#popuptandm').hide();	});
	function showdropdown(){	document.getElementById('inv_type').style.visibility="visible";	}
	function changeStatus(){
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('tandM/print_timesheet');?>", 	dataType: "json",  	data: $('.checkbox_grid_tandm input').serialize(),
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
				  		$('.action_list').hide(); 		$('.search-form form').submit();
				  		if(data.ok == false){
				  			var action_buttons = {
							        "Ok": {
										click: function() 
								        {
								            $( this ).dialog( "close" );
								        },
								        class : 'ok_button'
							        }
								}
				  			custom_alert('ERROR MESSAGE', "Please ensure that all invoices have an Invoice Date", action_buttons);
					  	}  	} else {
				  		var action_buttons = {
						        "Ok": {
									click: function() 
							        {
							            $( this ).dialog( "close" );
							        },
							        class : 'ok_button'
						        }
							}
			  			custom_alert('ERROR MESSAGE', data.message, action_buttons);
						$('.action_list').hide();
					}  	} 		}		});	}
	function changeInvoiceDate(date,type,id){
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/changeInvoiceDate');?>",  	dataType: "json",  	data: {'value':date,'type':type,'id':id},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {  		$('.search-form form').submit();  	} else {
				  		var action_buttons = {
						        "Ok": {
									click: function() 
							        {
							            $( this ).dialog( "close" );
							        },
							        class : 'ok_button'
						        }
							}
			  			custom_alert('ERROR MESSAGE', data.message, action_buttons);
							$('.action_list').hide();
					}
			  	}
	  		}	});	}
	function checkPrint() {		
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/checkPrint');?>",  	dataType: "json",
		  	data: $('.checkbox_grid_tandm input').serialize(),
		  	success: function(data) {
			  	if (data) {
				  	if (data.inv.length == 40) {
				  		var action_buttons = {
						        "Ok": {
									click: function() 
							        {
							            $( this ).dialog( "close" );
							        },
							        class : 'ok_button'
						        }
							}
			  			custom_alert('ERROR MESSAGE', data.inv, action_buttons);
					}else if(data.inv == 'not bill') {
						var action_buttons = {
						        "Ok": {
									click: function() 
							        {
							            $( this ).dialog( "close" );
							        },
							        class : 'ok_button'
						        }
							}
			  			custom_alert('ERROR MESSAGE', "Please ensure all Bill To Information is properly filled", action_buttons);
					}
					else if (data.inv == 'printed') {
						$('.action_list').hide();
						var token = new Date().getTime();
						blockUIForDownload(token);
						window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('invoices/print');?>'+'?token='+token+'&checkinvoice='+data.invoices_ids);
					} else {
				  		buttons = {
						        "Print": {
						        	class: 'yes_button',
						        	click: function() 
							        {
							            $( this ).dialog( "close" );
							            var token = new Date().getTime();
										blockUIForDownload(token);
										window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('invoices/print');?>'+'?token='+token+'&checkinvoice='+data.invoices_ids);
									}
						        },
						        "Cancel": {
						        	class: 'no_button',
						        	click: function() 
							        {
							            $( this ).dialog( "close" );
							        }
						        }
						}
						custom_alert("PRINT MESSAGE", data.inv, buttons);
					}
			  	}
	  		}	});	}
	function deleteInv(){
		$('.action_list').hide();
		buttons = {
		        "YES": {
		        	class: 'yes_button',
		        	click: function() 
			        {
			            $( this ).dialog( "close" );
			            
			            deleteInvoices();
			        }
		        },
		        "NO": {
		        	class: 'no_button',
		        	click: function() 
			        {
			            $( this ).dialog( "close" );
			        }
		        }
		}
		custom_alert("DELETE MESSAGE", "Are you sure you want to delete these invoices?", buttons);
	}
	var fileDownloadCheckTimer;
	function blockUIForDownload(token) {
    	$.blockUI();
    	fileDownloadCheckTimer = window.setInterval(function () {
      	var cookieValue = $.cookie('fileDownloadToken');
      	if (cookieValue == token)
       		finishDownload();
    	}, 1000);
  	}
	function finishDownload() {
		 window.clearInterval(fileDownloadCheckTimer);
		 $.removeCookie('fileDownloadToken'); //clears this cookie value
		 $('.search-form form').submit();
		 $.unblockUI();
	}	  
	function deleteInvoices(){
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/delete');?>",  	dataType: "json",
		  	data: $('.checkbox_grid_tandm input').serialize(),
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
				  		$('.action_list').hide(); 		$.fn.yiiGridView.update('tandm-grid');	 } } }	});	}
	function checkStatus(){
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/checkStatus');?>",  	dataType: "json",  	data: $('.checkbox_grid_tandm input').serialize(),
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {		$('.deletInv').show();  	}else{ 		$('.deletInv').hide();	 }	  	}  		}	});	}
function updateInput(id,ish){    $inpt = document.getElementById(id);   $inpt.setAttribute("value",ish);   $inpt.closest('tr').childNodes[3].childNodes[0].selectedIndex =0; }
function addrow(argument){
		$row = argument.closest('tr');		$rowind = $row.rowIndex;	var table = document.getElementById("inputratetable");
	    var newrow = table.insertRow($rowind+1);    var cell1 = newrow.insertCell(0);	    var cell2 = newrow.insertCell(1);
	    var cell3 = newrow.insertCell(2);	    var cell4 = newrow.insertCell(3);	    var cell5 = newrow.insertCell(4);
	    cell1.innerHTML = $row.childNodes[0].innerHTML;	    cell2.innerHTML = $row.childNodes[1].innerHTML;	    cell3.innerHTML = $row.childNodes[2].innerHTML;
	    cell3.childNodes[0].setAttribute("id", $row.childNodes[2].childNodes[0].getAttribute('id').concat(Math.floor((Math.random() * 1000) + 1)));
	    cell4.innerHTML = $row.childNodes[3].innerHTML;	}
function inputrate(element){
		var send = $('.checkbox_grid_tandm input').serialize();
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('tandM/inputrate');?>", 	dataType: "json", 	data: send,
		  	success: function(data) {
		  		if (data) {
		  			if(data.status=='success'){
		  				$('.closetandm').attr("id_project",data.id_project);
		  				$('.closetandm').attr("month",data.month);
		  				$('.closetandm').attr("year",data.year);
		  				$('.tandratecontainer').html(data.rate_table);
		  				$('#popuptandm').stop().show();
		  			}else{					
				  		var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', data.message, action_buttons);
		  			}		}}});  		}
function changeInputRate(value,id_tandm,MD,type){
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('tandM/changeInputRate');?>",  	dataType: "json",  	data: {'value':value,'id_tandm':id_tandm,'MD':MD,'type':type},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {	  	}
			  else	if(data.status == 'warning'){ alert('Total Man Days Exceed Limit');  	}
		  	 else{
			  		var action_buttons = {
					        "Ok": {
						        	class: 'ok_button',
						        	click: function() 
							        {
							            $( this ).dialog( "close" );
							        }
					        }
			  		}
			  		custom_alert('ERROR MESSAGE', 'ERROR', action_buttons);
			  		$('.search-form form').submit();
				 }
		  	} 		}	}); }
 $(".closetandm").click(function() {
	var	id_project=$('.closetandm').attr('id_project');		var	month=$('.closetandm').attr('month');		var	year=$('.closetandm').attr('year');
		checkRates(id_project,1,month,year);		});
 function checkRates(element,type,month,year){
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('tandM/closeInputRate');?>",  	dataType: "json",  	data: { 'id_project': element , 'type': type , 'month':month , 'year':year },
		  	success: function(data) {
			  	if (data) {
				  if (data.status == 'success') { 		$('#popuptandm').hide(); 		$.fn.yiiGridView.update('tandm-grid');	
			  	}else{
			  		var buttons = {
						        "Yes": {
						        	class: 'yes_button',
						        	click: function() 
							        {
							            $( this ).dialog( "close" );
							           
										checkRates(element,2,month,year);

										$('#popuptandm').hide();
										
									}
						        },
						        "No": {
						        	class: 'no_button',
						        	click: function() 
							        {
							            $( this ).dialog( "close" );
							        }
						        }
						}
			  		custom_alert('ERROR MESSAGE', data.message, buttons);			  		
				 }
			  	} 		}		});}

 function submitRates(){
 	var	id_project=$('.closetandm').attr('id_project');		var	month=$('.closetandm').attr('month');		var	year=$('.closetandm').attr('year');
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('tandM/closeInputRate');?>",  	dataType: "json",
		  	data: { 'id_project': id_project , 'type': 1  ,'month': month, 'year': year },
		  	success: function(data) {
			  	if (data) {
				  if (data.status == 'success') { 			$('#popuptandm').hide();			$.fn.yiiGridView.update('tandm-grid');	
			  	}else{
			  		var buttons = {
						        "Yes": {
						        	class: 'yes_button',
						        	click: function() 
							        {
							            $( this ).dialog( "close" );
							           
										       
										checkRates(id_project,2,month,year);

									
										$('#popuptandm').hide();
										
									}
						        },
						        "No": {
						        	class: 'no_button',
						        	click: function() 
							        {
							            $( this ).dialog( "close" );
							        }
						        }
						}
			  		custom_alert('ERROR MESSAGE', "Please make sure that all the rates has been set otherwise all the changes will be discarded. Are you sure you want to submit ?", buttons);
				 }	  	} 		}		}); }		
	function generateInvoice(element){
		var send = $('.checkbox_grid_tandm input').serialize();
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('tandM/generateInvoice');?>", 	dataType: "json", 	data: send,
		  	success: function(data) {
		  		if (data) {
		  			if(data.status=='success'){
		  				var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		$.fn.yiiGridView.update('tandm-grid');	
				  		custom_alert('NOTIFICATION', "Invoice generated.", action_buttons);
				  	}else{
				  		var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', data.message , action_buttons);
		  			}	}}});  }
	function generateInvoice(element){
		var send = $('.checkbox_grid_tandm input').serialize();
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('tandM/generateInvoice');?>",  	dataType: "json",  	data: send,
		  	success: function(data) {
		  		if (data) {
		  			if(data.status=='success'){
		  				var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('NOTIFICATION', "Invoice generated.", action_buttons);
				  		$.fn.yiiGridView.update('tandm-grid');	
		  			}else{
						var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', data.message , action_buttons);
		  			}  	}}}); 		}
  function deletetm(){		
		buttons = {
	        "YES": {
	        	class: 'yes_button',
	        	click: function() 
		        {
		            $( this ).dialog( "close" );
		            
		            deletetmconfirm();
		        }
	        },
	        "NO": {
	        	class: 'no_button',
	        	click: function() 
		        {
		            $( this ).dialog( "close" );
		        }
	        }
	}
	custom_alert("DELETE MESSAGE", "Are you sure you want to delete these records?", buttons);
}
function deletetmconfirm(){
		var send = $('.checkbox_grid_tandm input').serialize();	
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('tandM/delete');?>",  	dataType: "json",  	data: send,
		  	success: function(data) {
		  		if (data) {
				if(data.status=='success'){		$.fn.yiiGridView.update('tandm-grid');
		  			}else{	
				  		var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', data.message , action_buttons);
		  			}	}}}); 		}
	function printtimesheets(){
		var send = $('.checkbox_grid_tandm input').serialize();		
		var res = send.split("&");
		if(res.length>1){
				  		var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', "You can't choose more than one record to print timesheet." , action_buttons);
				  	return false;

		}else{
			$('#mylink').attr('href', "<?php echo Yii::app( )->getBaseUrl( ); ?>"+"/tandM/printtimesheet?"+send+" ");
		} 		}
</script>