/**
* on client select, activate and refresh projects list
*/
function refreshProjectsList()
{
	var customer = $('#Expenses_customer_id').val();
	if(customer)
	{
		$('#Expenses_project_id').removeAttr('disabled');
		$.ajax({
 			type: "GET",
 			data: {id : customer},					
 			url: getProjectsByClientUrl, 
 			dataType: "json",
 			success: function(data) {
			  	if (data) {
			  		
			  		var arr = [];

			  		for (var key in data) {
			  		    if (data.hasOwnProperty(key)) {
			  		        arr.push({'id': key, 'label': data[key]});
			  		    }
			  		}
			  		
			  		 var sorted = arr.sort(function (a, b) {
		    				if (a.label > b.label) {
		      					return 1;
		      				}
		    				if (a.label < b.label) {
		     					 return -1;
		     				}

		    				return 0;
					 });
			  		
			  		var selectOptions = '<option value="choose">'+'Choose project'+'</option>';
			  		$.each(sorted,function(index, val){
				        selectOptions += '<option value="'+val.id+'">'+val.label+'</option>';
				    });
				    $('#Expenses_project_id').html(selectOptions);
			  	}
	  		}
		});
	}
	else
	{
		$('#Expenses_project_id').attr('disabled', 'disabled');
	}
}

/**
* show edit form for header
*/	
function showHeader(element)
{
	var url = $(element).attr('href');
	$.ajax({
 		type: "POST",
	  	url: url,
	  	dataType: "json",
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
					$('.edit_header_content').html(data.html);
					$('.edit_header_content').removeClass('hidden');
					$('.header_content').addClass('hidden');
					$('#showHeader').hide();
					$('#saveHeader').show();
					$('#hideHeader').show();
					
					$("#Expenses_startDate").datepicker({ dateFormat: 'dd/mm/yy' });
				 	$("#Expenses_endDate").datepicker({ dateFormat: 'dd/mm/yy' });
				 	
				  
				 	 $("#Expenses_startDate").click(function(){
					 		$('#ui-datepicker-div').css('top',parseFloat($("#Expenses_startDate").offset().top) + 25.0);
					 		$('#ui-datepicker-div').css('left',parseFloat($("#Expenses_startDate").offset().left));
					 })
					 $("#Expenses_endDate").click(function(){
					 		$('#ui-datepicker-div').css('top',parseFloat($("#Expenses_endDate").offset().top) + 25.0);
					 		$('#ui-datepicker-div').css('left',parseFloat($("#Expenses_endDate").offset().left));
					 });
			  	}
		  	}
  		}
	});
}
	$(document).ready(function() {
		if ($(".scroll1").length > 0) {
			if (!$(".scroll1").find(".mCustomScrollBox").length > 0) {
				$(".scroll1").mCustomScrollbar();
			}
		}
		
		
        $(".qualdatacontainer").mCustomScrollbar();
	
	});


	function panelClip(element) {
		var width = 0;
		if (element == '.item_clip')
			width = 300;
		else
			width = 90;
			
		$(element).each(function() {
			if ($(this).width() < width) {
				$(this).parent().find('u').hide();
				console.log($(this).parent().find('u').attr('class'));
			}
		});
	}
/**
* hide edit for header, show header details
*/
function hideHeader()
{
	$('.edit_header_content').addClass('hidden');
	$('.header_content').removeClass('hidden');
	$('#showHeader').show();
	$('#saveHeader').hide();
	$('#hideHeader').hide();
}

/**
* show edit form for header
*/	
function saveHeader(element)
{
	var url = $(element).attr('href');
	$.ajax({
 		type: "POST",
 		data: $('#expenses-form').serialize()  + '&ajax=expenses-form',					
	  	url: url, 
	  	dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.status == 'saved' && data.html) {
			  		$('.header_content').html(data.html);
			  		hideHeader();
			  	} else {
			  		if (data.status == 'success' && data.html) {
			  			$('.edit_header_content').html(data.html);
			  		}
			  	}
			  	showErrors(data.errors);
			  	showErrors(data.alert);
		  	}
  		}
	});
}

/**
* show notices on expenses details
*
*/
function showNotice(after)
{
	var url = $(after).attr('href');
	alert(url);
	$.ajax({
 		type: "POST",
 		data: $('#expenses-form').serialize()  + '&ajax=expenses-form',					
	  	url: url, 
	  	dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		var notice = data;
				$('body').append('<div class="panel infoNotice">'+
		                    			'<div class="phead"></div>' +
		                    				'<div class="pcontent">' +
		                    					'<div class="cover">'+notice+'</div>'+
		                    				'</div>'+
		                    			'<div class="pftr"></div>'+
		                    		'</div>');
		        $('.infoNotice').css({'left':($(after).offset().left-15)+'px', 'top': ($(after).offset().top-50)+'px'});
		        $('.infoNotice').show(); 
		  	}
  		}
	});           		
}

/**
* show notices on expenses details
*
*/
function hideNotice()
{
	$('.infoNotice').remove();
}
/**
* SHOW NEW ITEM EXPENS FORM
*/
function showItemForm(element, newItem) 
{
	var url;
	if (newItem) {
		url = updateItemExpensUrl;
	} else {
		url = $(element).attr('href');
	}
	$.ajax({
 		type: "POST",
	  	url: url,
	  	dataType: "json",
	  	data: {'expenses_id':modelId},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
					if (newItem) {
					  	$('.new_item').hide();
					  	$('.new_item').after(data.form);
					  } else {
							$(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>');
					  }
					  
					  $("#ExpensesDetails_date").datepicker();
					  $("#ExpensesDetails_date").click(function(){
						 		$('#ui-datepicker-div').css('top',parseFloat($("#ExpensesDetails_date").offset().top) + 25.0);
						 		$('#ui-datepicker-div').css('left',parseFloat($("#ExpensesDetails_date").offset().left));
						 });
				  }
		  	}
  		}
	});
}

/** 
* create new expens
*/
function createItem(element, expensId, url)
{
	$.ajax({
	 		type: "POST",
	 		data: $(element).parents('#expenses-form').serialize() + '&expenses_id='+expensId+'&ajax=expenses-form',				
	 		url: url, 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache.new').remove();
					  	$('.new_item').show();
					  		
					 	// update amounts
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});
				  				
				  		$.fn.yiiGridView.update('items-grid');
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache.new').replaceWith(data.form);
				  		}
				  	}
				  	showErrors(data.errors);
				  	showErrors(data.alert);
			  	}
	  		}
		});

}




function showAfterItemForm(element, newItem) 
{
	var url;
	if (newItem) {
		url = updateItemAfterUrl;
	} else {
		url = $(element).attr('href');
	}
	$.ajax({
 		type: "POST",
	  	url: url,
	  	dataType: "json",
	  	data: {'expenses_id':modelId},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
					if (newItem) {
					  	$('.new_item_after').hide();
					  	$('.new_item_after').after(data.form);
					  } else {
							$(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>');
					  }
					  
					  $("#ExpensesDetails_date").datepicker();
					  $("#ExpensesDetails_date").click(function(){
						 		$('#ui-datepicker-div').css('top',parseFloat($("#ExpensesDetails_date").offset().top) + 25.0);
						 		$('#ui-datepicker-div').css('left',parseFloat($("#ExpensesDetails_date").offset().left));
						 });
				  }
		  	}
  		}
	});
}


function createAfterItem(element, expensId, url)
{
	$.ajax({
	 		type: "POST",
	 		data: $(element).parents('#after-form').serialize() + '&expenses_id='+expensId+'&ajax=after-form',				
	 		url: url, 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-after.new').remove();
					  	$('.new_item_after').show();
					  		$.fn.yiiGridView.update('items-grid-after');
					 	// update amounts
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});
				  				
				  		
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache-after.new').replaceWith(data.form);
				  		}
				  	}
				  	showErrors(data.errors);
				  	showErrors(data.alert);
			  	}
	  		}
		});

}




function showSundayItemForm(element, newItem) 
{
	var url;
	if (newItem) {
		url = updateItemSundayUrl;
	} else {
		url = $(element).attr('href');
	}
	$.ajax({
 		type: "POST",
	  	url: url,
	  	dataType: "json",
	  	data: {'expenses_id':modelId},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
					if (newItem) {
					  	$('.new_item_sunday').hide();
					  	$('.new_item_sunday').after(data.form);
					  } else {
							$(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>');
					  }
					  
					  $("#ExpensesDetails_date").datepicker();
					  $("#ExpensesDetails_date").click(function(){
						 		$('#ui-datepicker-div').css('top',parseFloat($("#ExpensesDetails_date").offset().top) + 25.0);
						 		$('#ui-datepicker-div').css('left',parseFloat($("#ExpensesDetails_date").offset().left));
						 });
				  }
		  	}
  		}
	});
}


function createSundayItem(element, expensId, url)
{
	$.ajax({
	 		type: "POST",
	 		data: $(element).parents('#sunday-form').serialize() + '&expenses_id='+expensId+'&ajax=sunday-form',				
	 		url: url, 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-sunday.new').remove();
					  	$('.new_item_sunday').show();
					  			$.fn.yiiGridView.update('items-grid-sunday');
					 	// update amounts
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});
				  				
				  	
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache-sunday.new').replaceWith(data.form);
				  		}
				  	}
				  	showErrors(data.errors);
				  	showErrors(data.alert);
			  	}
	  		}
		});

}

/** 
* update new expens
*/
function updateItem(element, id, url)
{
	$.ajax({
	 		type: "POST",
	 		data: $(element).parents('#expenses-form').serialize() +'&ajax=expenses-form',				
	 		url: url+'?id='+id, 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache.new').remove();
					  	$('.new_item').show();
					  		
					 	// update amounts
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});
				  				
				  		$.fn.yiiGridView.update('items-grid');
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache.new').replaceWith(data.form);
				  		}
				  	}
				  	showErrors(data.errors);
				  	showErrors(data.alert);
			  	}
	  		}
		});

}



function updateAfterItem(element, id, url)
{
	$.ajax({
	 		type: "POST",
	 		data: $(element).parents('#after-form').serialize() +'&ajax=after-form',				
	 		url: url+'?id='+id, 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-after.new').remove();
					  	$('.new_item').show();
					  				
				  		$.fn.yiiGridView.update('items-grid-after');
					 	// update amounts
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});
				  		
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache-after.new').replaceWith(data.form);
				  		}
				  	}
				  	showErrors(data.errors);
				  	showErrors(data.alert);
			  	}
	  		}
		});

}

function updateSundayItem(element, id, url)
{
	$.ajax({
	 		type: "POST",
	 		data: $(element).parents('#sunday-form').serialize() +'&ajax=sunday-form',				
	 		url: url+'?id='+id, 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-sunday.new').remove();
					  	$('.new_item').show();
					  			
				  		$.fn.yiiGridView.update('items-grid-sunday');	
					 	// update amounts
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});
				  		
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache-sunday.new').replaceWith(data.form);
				  		}
				  	}
				  	showErrors(data.errors);
				  	showErrors(data.alert);
			  	}
	  		}
		});

}








/**
* calculate precis round
*/
function precise_round(num,decimals){
	return Math.round(num*Math.pow(10,decimals))/Math.pow(10,decimals);
	}
/**
* calculate USD amout
*/
function getUSDAmount(rateUrl)
{
	val = $('#ExpensesDetails_original_amount').val();
	currency = $('#ExpensesDetails_original_currency').val();
	$.ajax({
 		type: "GET",
	  	url: rateUrl,
	  	dataType: "json",
	  	data: {'id':currency},
	  	success: function(data) {
		  	if (data) {
			  	usdVal = val*data.rate;	
				$('#ExpensesDetails_amount').val(precise_round(usdVal,3)); 	
				$('#ExpensesDetails_currency_rate_id').val(data.id);	  	
			}
			showErrors(data.error);
			showErrors(data.alert);
  		}
	});
}

/**
* click the reject button
*/
function reject()
{
	$('.pay').addClass('notselected');
	$('.approve').addClass('notselected');
	$('.reject').addClass('selected');
	$('.saveDiv1').show();
}

/**
* click the pay button
*/
function pay()
{
	buttons = {
			"Print": {
	        	class: 'yes_button',
	        	click: function() 
		        {
		            $( this ).dialog( "close" );
		            //printBankTransfer();
		            //window.location.replace(PrintBankTransfer);
		            
		            submitPay(true);
		        }
	        },
	        "Cancel": {
	        	class: 'no_button',
	        	click: function() 
		        {
		            $( this ).dialog( "close" );
		            submitPay(false);
		        }
	        }
	}
	custom_alert("PRINT MESSAGE", 'Do you want to print the bank transfer', buttons);
	$('.pay').removeClass('notselected');
	$('.approve').removeClass('notselected');
	$('.reject').removeClass('selected');
	$('.saveDiv1').hide();
	//createInv();
}
function submitPay(printBank) {
	$.ajax({
 		type: "POST",
	  	url: updateExpensUrl,
	  	dataType: "json",
	  	data: {"Expenses[status]":'Paid'},
		success: function(data) {
			if(printBank == true)
			{	
				$('#expenses_header .status_expenses').html('Paid');
				$('.pay').addClass('hidden');
				$('.reject').addClass('hidden');
				window.location.replace(PrintBankTransfer);
			}else{
				window.location.replace(approval);
			}	
		}
	  	
	});
}

function printBankTransfer() {
	$.ajax({
 		type: "POST",
	  	url: PrintBankTransfer,
	  	dataType: "json",
	  	data: {'asa':'asa'},
		success: function(data) {
			submitPay();
		}
	  	
	});	
}

/**
* click the approve button
*/
function approve()
{
	$('.pay').removeClass('notselected');
	$('.approve').removeClass('notselected');
	$('.reject').removeClass('selected');
	$('.saveDiv1').hide();
}
	