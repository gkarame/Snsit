$(window).load(function() {
	if ($('.mytabs').length > 0)
	{
		$('.mytabs').removeClass('hidden');
		
		if ($("form .saveDiv").length) {
			if ($(".documents_div").is(":visible")  || $("#eas-grid").is(":visible") || $("#invoices-grid").is(":visible")) {
				$("form .saveDiv").hide();
			} else {
				$("form .saveDiv").show();
			}
		}
	}
	if ($('.main_timesheet').length > 0)
	{
		$('.main_timesheet').removeClass('hidden');
	}
	
});
var idth = 0;
var to_left = 0;
var to_right = 0;
var submitted = false;
$(document).ready(function() {
	checkContainer1();
	checkContainer2();
	checkContainer3();
	checkContainer4();
	checkContainer5();
	checkContainer6();
	checkContainer7();
	checkContainer8();
	checkContainer9();
	checkContainer10();
	checkContainer11();
	checkContainer12();
	checkContainer13();
	checkContainer14();
	checkContainer15();
	checkContainer16();	
	checkContainer17();
	$('.sortableDash').sortable({
		items: '.board',
		opacity: 0.5,
		start: function(evt, ui) {
		    var link = ui.item.find('.dragg');
		    var id_widget = link.attr('data-id');
			link.data('click-event', link.attr('onclick'));
			link.attr('onclick', "mareste("+id_widget+")");
			link.addClass('select');
		},
		stop: function (event, ui) {
			var link = ui.item.find('.dragg');
			link.removeClass('select');
			var sortedIds = $(this ).sortable("toArray").join(',');
	        // POST to server using $.post or $.ajax
	        $.ajax({
	            data: {id:sortedIds},
	            type: 'POST',
	            url: configJs.urls.baseUrl +'/widgets/setOrder',
	            dataType:"json"
	        });
	    }
	});
	
	$('#menu').hover(function() {
		$('#content').addClass('zIndex');
	}, function() {
		$('#content').removeClass('zIndex');
	});
	
	$('.chk').each(function() {
		initializeCheckboxes($(this));	
	});
	
	$( ".popup_shareby" ).dialog({
		 autoOpen: false,
		 height: 'auto',
		 width: 'auto',
		 modal: true,
		 close: function() {
			 $(this).html('');
		 }
	});
	
	$('input').keydown(function(e) {
		var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
	    if(key == 13) {
	        e.preventDefault();
	    	$(this).closest('form:not(.ajax_submit)').submit();
	    }
	});
	
	$(document).ajaxStop(function() {
		// hide no results row on a grid when an add new row followes
		if ($('.tache').prev().find('td.empty')) {
			$('.tache').prev().find('td.empty').parent().hide();
		}
	});
	
	// hide no results row on a grid when an add new row followes
	if ($('.tache').prev().find('td.empty')) {
		$('.tache').prev().find('td.empty').parent().hide();
	}
	
	$(document)
		.on('mouseenter', '.links .link a', function(e) {
			$(this).siblings('.tabClose').addClass('hovered');
		})
		.on('mouseleave', '.links .link a', function(e) {
			$(this).siblings('.tabClose').removeClass('hovered');
		})
		.on('mouseenter', '.links .link .tabClose', function(e) {
			$(this).addClass('hovered');
			$(this).siblings('a').addClass('hovered');
		})
		.on('mouseleave', '.links .link .tabClose', function(e) {
			$(this).removeClass('hovered');
			$(this).siblings('a').removeClass('hovered');
		})
		.on('click', "body", function(e) {
			if ($('.panel').css("display") == "block") {
				$('.panel').fadeOut(100);
				e.stopPropagation();
			}
			if ($('.groups-list').is(":visible") && (!$(e.target).parents('.groups-list').length)) {
				$('.groups-list').fadeOut(100);
				e.stopPropagation();
			}
			if ($('#users-list').is(":visible") && (!$(e.target).parents('#users-list').length)) {
				$('#users-list').fadeOut(100);
				e.stopPropagation();
			}
			if ($('.popup_list').is(":visible") && (!$(e.target).parents('.popup_list').length) && 
					(!$(e.target).parents('.ui-autocomplete').length) && (!$(e.target).parents('.confirm_dialog'))) {
				$('.popup_list').fadeOut(100);
				e.stopPropagation();
			}
			if ($('.actionPanel').is(":visible") && (!$(e.target).parents('.actionPanel').length) && (!$(e.target).parents('.triggerAction').length)) {
				$('.actionPanel').fadeOut(100);
				e.stopPropagation();
			}
	    })
	    /* close tabs */
	    .on('click', ".tabClose", function(e) {
	    	var href = $(this).siblings('a').attr('href');
	    	if (href.indexOf("/documents/create") >= 0)
	    	{
	        	closeTab(href,true);
	        }else{
	        	closeTab(href);
	        }
	    })
	    .on('click', '.ui-tabs-nav li', function(e) {
	    	var href = $('.links li.selected a').attr('href');
	    	var currentController = configJs.current.controller;
	    	var ind = parseInt($(this).find('a').attr('href').split('_').slice(-1)[0]);
	    	$.ajax({
	     		type: "POST",					
	    	  	url: configJs.urls.baseUrl + '/site/rememberTab', 
	    	  	dataType: "json",
	    	  	data: {url : href, controller : currentController, index: ind},
	    	  	success: function(data) {
	    	  			console.log(data);
	      		}
	    	});
	    })
	    .on('keydown', '.auto_email', function(event) {
	    	if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "ui-autocomplete" ).menu.active ) {
	    		event.preventDefault();
	    	}
	    })
	    .on('focus', '.auto_email', function(e) {
	    	$(this).autocomplete({
    	      source: function( request, response ) {
    	          $.getJSON( configJs.urls.baseUrl + "/site/autocomplete?with=true", {
    	            term: extractLast( request.term )
    	          }, response );
    	        },
    	        search: function() {
    	          // custom minLength
    	          var term = extractLast( this.value );
    	          if ( term.length < 1 ) {
    	            return false;
    	          }
    	        },
    	        focus: function() {
    	          // prevent value inserted on focus
    	          return false;
    	        },
    	        select: function( event, ui ) {
    	          var terms = split( this.value );
    	          
    	          // remove the current input
    	          terms.pop();
    	          // add the selected item
    	          terms.push( ui.item.value );
    	          // add placeholder to get the comma-and-space at the end
    	          if ($('#ShareByForm_header').length > 0) {
    	        	  var head = [];
	    	          $.each(terms, function( index, value ) {
	    	        	  value = value.split(' <')[0];
	    	        	  firstname = value.split(' ')[0];
	    	        	 head.push(firstname);
	    	          });
	    	          $('#ShareByForm_header').val('Dear '+head.join(', ')+', ');
    	          }
    	          terms.push( "" );
    	          
    	          this.value = terms.join( ", " );
    	         
    	          return false;
    	        }
    	  	});
	    })
	    
	 ;
	
	/* links and prev, next buttons */
	setWidth();
	
	timeout = $('.next');
	timeout2 = $('.next');
	$('.next').mousedown(function() {
		timeout = setInterval(function() {
			if(parseInt($('.links').css('width')) - parseInt($('.links ul').css('width')) >= (parseInt($('.links ul').css('left')) - 170)){
				clearInterval(timeout);	
				return false;
			}
			var to_l = $('.links ul').position().left;
			$('.links ul').css('left', (to_l - 170));
		}, 70);
		return false;
	});
	
	$('.next').mouseup(function(){
		clearInterval(timeout);
		return false;
	});
	
	$('.next').mouseout(function() {
		clearInterval(timeout);
		return false;
	});
	
	$('.prev').mousedown(function() {
		timeout2 = setInterval(function() {
			if(parseInt($('.links ul').css('left')) == 0) {
				clearInterval(timeout2);	
				return false;
			}
			var to_l = $('.links ul').position().left;
			$('.links ul').css('left', (to_l + 170));
		}, 70);
		return false;
	});
	
	$('.prev').mouseup(function() {
		clearInterval(timeout2);
		return false;
	});
	
	$('.prev').mouseout(function() {
		clearInterval(timeout2);
		return false;
	});
	/* end links and prev, next buttons */
	
	/* date picker in expenses section*/
	$(function() {
	 	$("#Expenses_startDate").datepicker({ dateFormat: 'dd/mm/yy' });
	 	$("#Expenses_endDate").datepicker({ dateFormat: 'dd/mm/yy' });
	 	$("#ExpensesDetails_date").datepicker({ dateFormat: 'dd/mm/yy' });
	  
	 	 $("#Expenses_startDate").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#Expenses_startDate").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#Expenses_startDate").offset().left));
		 });
		 $("#Expenses_endDate").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#Expenses_endDate").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#Expenses_endDate").offset().left));
		 });
		 $("#ExpensesDetails_date").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#ExpensesDetails_date").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#ExpensesDetails_date").offset().left));
		 });
	});
	
	/* date picker in request section*/
	$(function() {
		$("#Requests_startDate").datepicker({ dateFormat: 'dd/mm/yy' });
	 	$("#Requests_endDate").datepicker({ dateFormat: 'dd/mm/yy' });
	  
	 	$("#Requests_startDate").click(function(){
	 		$('#ui-datepicker-div').css('top',parseFloat($("#Requests_startDate").offset().top) + 25.0);
	 		$('#ui-datepicker-div').css('left',parseFloat($("#Requests_startDate").offset().left));
		 });
		 $("#Requests_endDate").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#Requests_endDate").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#Requests_endDate").offset().left));
		 });
	});
	
	/* date picker in ea section*/
	$(function() {
		$("#Eas_start_date").datepicker({ dateFormat: 'dd/mm/yy' });
	 	$("#Eas_end_date").datepicker({ dateFormat: 'dd/mm/yy' });
	  
	 	$("#Eas_start_date").click(function(){
	 		$('#ui-datepicker-div').css('top',parseFloat($("#Eas_start_date").offset().top) + 25.0);
	 		$('#ui-datepicker-div').css('left',parseFloat($("#Eas_start_date").offset().left));
		 });
		 $("#Eas_end_date").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#Eas_end_date").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#Eas_end_date").offset().left));
		 });
	});
	
	/* date picker in maintenance section*/
	$(function() {
	 	$("#Maintenance_starting_date").datepicker({ dateFormat: 'dd/mm/yy' });
	  
	 	 $("#Maintenance_starting_date").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#Maintenance_starting_date").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#Maintenance_starting_date").offset().left));
		 });
	});
	
	/* date picker in maintenance section*/
	$(function() {
	 	$("#SupportDesk_due_date").datepicker({ dateFormat: 'dd/mm/yy' });
	  
	 	 $("#SupportDesk_date").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#SupportDesk_due_date").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#SupportDesk_due_date").offset().left));
		 });
	});
	/* date picker in hr request section*/
	$(function() {
	 	$("#Requests_startDate").datepicker({ dateFormat: 'dd/mm/yy' });
	  
	 	 $("#Requests_startDate").click(function(){
		 		$('#ui-datepicker-div').css('top',parseFloat($("#Requests_startDate").offset().top) + 25.0);
		 		$('#ui-datepicker-div').css('left',parseFloat($("#Requests_startDate").offset().left));
		 });
	});
});

/* Used functions */
function showFull(element) {
	var elem = $(element);
	var parent = elem.parent();
	parent.addClass('hidden');
	if (parent.hasClass('full'))
	{
		parent.siblings('.shortened').removeClass('hidden');
	}
	else
	{
		parent.siblings('.full').removeClass('hidden');
	}	
	
}
function closeTab(url , params) {
	$.ajax({
 		type: "POST",					
	  	url: configJs.urls.baseUrl + '/site/closeTab', 
	  	dataType: "json",
	  	data: {url : url,params:params},
	  	success: function(data) {
	  		if (data.status == "success") {
	  			window.location = configJs.urls.baseUrl + data.tab;
	  		}
  		}
	});
}
function custom_alert(title_msg, output_msg, action_buttons)
{
	$("#confirm_dialog .confirm_title").html(title_msg);
	$("#confirm_dialog .confirm_content").html(output_msg);
	$("#confirm_dialog").dialog({
        resizable: false,
        draggable: true,
        closeOnEscape: true,
        width: 'auto',
        buttons: action_buttons,
        modal: true,
        width: 379,
        height: 235,
        dialogClass: 'confirm_dialog'
    });
}
function delete_documents()
{
	buttons = {
	        "YES": {
	        	class: 'yes_button',
	        	click: function() 
		        {
		            $( this ).dialog( "close" );
		            deleteDocs();
		        }
	        },
	        "NO": {
	        	class: 'no_button',
	        	click: function() 
		        {
		            $( this ).dialog( "close" );
		            $('input').prop('checked', false);
		        }
	        }
	}
	custom_alert("DELETE MESSAGE", "Are you sure you want to delete these documents?", buttons);
}
function custom_cancel() {
	buttons = {
	        "YES": {
	        	class: 'yes_button',
	        	click: function() 
		        {
		            $( this ).dialog( "close" );
		            closeTab(configJs.current.url);
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
	custom_alert("CANCEL MESSAGE", "Are you sure you want to leave this page?", buttons);
}

function shareBySubmit(element, model) {
	if (submitted == false) {
		submitted = true;
		var dialog = $('.popup_list'), send = 'model='+model;
		if (!$(element).hasClass('shareby_button')) {
			send += '&'+ $(element).parents('.shareby_fieldset').serialize();
			$('.loader').show();
		}
		else {
			dialog.removeClass('popup_shareby').hide().html('');
		}
		
		var action_buttons = {
		        "Ok": {
			        	class: 'ok_button',
			        	click: function() 
				        {
				            $( this ).dialog( "close" );
				        }
		        }
  		}
		
		$.ajax({
	 		type: "POST",					
		  	url: $(element).attr('href'), 
		  	dataType: "json",
		  	data: send,
		  	success: function(data) {
		  		if (data) {
		  			submitted = false;
		  			if (dialog.find('.loader').length) {
		  				$('.loader').hide();
		  			}
			  		if (data.status == "failure") {
			  			dialog.html(data.form);
			  			dialog.addClass('popup_shareby');
			  			if (data.file_found == 0)
		  				{
			  				custom_alert('ERROR MESSAGE', 'The file was not found on the server', action_buttons);
		  				}
			  			dialog.show();
			  		} else {
			  			if (data.status == "success") {
			  				if (!data.not_sent_to)
			  				{
				  				dialog.fadeOut(100);
				  				dialog.html('');
			  				}
			  				else
		  					{
				  				custom_alert('ERROR MESSAGE', 'The email was not sent to '+data.not_sent_to, action_buttons);		  					
		  					}
			  			} else {
			  				custom_alert('ERROR MESSAGE', 'There was an error. Please try again!', action_buttons);
			  			}
			  		}
		  		}
	  		},
			error: function() {
				submitted = false;
				if (dialog.find('.loader').length) {
	  				$('.loader').hide();
	  			}
				custom_alert('ERROR MESSAGE', 'There was an error. Please try again!', action_buttons);
			}
		});
	}
}

function setWidth() {
	var len = $('.links ul li').size(); 
	$('.links ul li').each(function(key, val) {
		var curr_width = $(this).width();
		if(key == 0) to_left = key;
		idth += curr_width;
		if (key == 5 && len > 5) {
			$('.next').show();
			$('.prev').show();
		}
		if (key >= 5 && $(this).hasClass('selected')) {
			var left = 0;
			if (len - (key + 1) >= 2) {
				left = (key - 2) * 170;
			} else {
				if (len - (key + 1) == 1) {
					left = (key - 3) * 170;
				} else {
					left = (key - 4) * 170;
				}
			}
			$('.links ul').css('left', -left);
		}
	});
	$('.links ul').css('width', idth);
}

function CheckOrUncheckInput(obj)
{
	var checkBoxDiv = $(obj).find('.input');
	var input = $(obj).find('input[type="checkbox"]');
	if (input.is(':not(:disabled)') && !checkBoxDiv.hasClass('checkboxDisabled')) {
		if (checkBoxDiv.hasClass('checked')) {
			checkBoxDiv.removeClass('checked');
			input.prop('checked', false);
		}
		else {
			checkBoxDiv.addClass('checked');
			input.prop('checked', true);
		}
		if ($(obj).hasClass('read') || $(obj).hasClass('write')) {
			groupPermissions(obj);
		}
	}
}

function groupPermissions(obj) {
	var input = $(obj).find('input[type="checkbox"]');
	var name = input.attr('name');
	name = name.split('-');
	name = name[1];
	name = name.replace('[read]', '').replace('[write]', '').replace('[0]', '').replace('[1]', '').replace('[2]', '');
	console.log(name);
	var class_name = input.attr('class');
	var divClass = $(obj).hasClass('read') ? 'read' : ($(obj).hasClass('write') ? 'write' : '');
	if (input.is(':checked')) {
		switch (divClass) {
			case 'read':
				console.log('read');
				// Comment this if default is not checked all when check the parent
				// enable checkbox and corresponding div 
				$('.p_tab').find('.read .parent_' + name).attr('disabled', false);
				$('.p_tab').find('.read .parent_' + name).siblings('div.input').removeClass('checkboxDisabled');
				
				//check checkbox and corresponding div 
				$('.p_tab').find('.read .parent_' + name).prop('checked', true);
				$('.p_tab').find('.read .parent_' + name).siblings('div.input').addClass('checked');
				
				//$(this).parent().find('input.' + class_name).attr('disabled', false);
				//$(this).parent().parent().find('.write .' + class_name).attr('disabled', false);
				
				break;
			case 'write':
				console.log('write');
				// enable corresponding read checkbox and div 
				//input.parent().parent().find('.read input.' + class_name).attr('disabled', false);
				//input.parent().parent().find('.read input.' + class_name).siblings('div.input').removeClass('checkboxDisabled');
				// check corresponding read checkbox and div 
				//input.parent().parent().find('.read input.' + class_name).prop('checked', true);
				//input.parent().parent().find('.read input.' + class_name).siblings('div.input').addClass('checked');
				
				// enable write checkbox and corresponding div 
				$('.p_tab').find('.write .parent_' + name).attr('disabled', false);
				$('.p_tab').find('.write .parent_' + name).siblings('div.input').removeClass('checkboxDisabled');
				// enable read checkbox and corresponding div
				
				//$('.p_tab').find('.read .parent_' + name).attr('disabled', false);
				//$('.p_tab').find('.read .parent_' + name).siblings('div.input').removeClass('checkboxDisabled');
				
				//check write checkbox and corresponding div 
				$('.p_tab').find('.write .parent_' + name).prop('checked', true);
				$('.p_tab').find('.write .parent_' + name).siblings('div.input').addClass('checked');
				//check read checkbox and corresponding div 
				//$('.p_tab').find('.read .parent_' + name).prop('checked', true);
				//$('.p_tab').find('.read .parent_' + name).siblings('div.input').addClass('checked');
				
				break;
		}
	} else {
		switch (divClass) {
			case 'read':
				console.log('uncheck read');
				//uncheck corresponding write
				
				//input.parent().parent().find('.write input.' + class_name).prop('checked', false);
				//input.parent().parent().find('.write input.' + class_name).siblings('div.input').removeClass('checked');
				
				// disable read checkbox and corresponding div 
				$('.p_tab').find('.read .parent_' + name).attr('disabled', true);
				$('.p_tab').find('.read .parent_' + name).siblings('div.input').addClass('checkboxDisabled');
				// disable write checkbox and corresponding div 
				
				//$('.p_tab').find('.write .parent_' + name).attr('disabled', true);
				//$('.p_tab').find('.write .parent_' + name).siblings('div.input').addClass('checkboxDisabled');
				
				//uncheck read checkbox and corresponding div 
				$('.p_tab').find('.read .parent_' + name).attr('checked', false);
				$('.p_tab').find('.read .parent_' + name).siblings('div.input').removeClass('checked');
				//uncheck write checkbox and corresponding div 
				
				//$('.p_tab').find('.write .parent_' + name).attr('checked', false);
				//$('.p_tab').find('.write .parent_' + name).siblings('div.input').removeClass('checked');
				
				//$(this).parent().parent().find('.write .' + class_name).attr('checked', false);
				//$(this).parent().parent().find('.write .' + class_name).attr('disabled', true);
				break;
			case 'write':
				console.log('uncheck write');
				$('.p_tab').find('.write .parent_' + name).attr('disabled', true);
				$('.p_tab').find('.write .parent_' + name).siblings('div.input').addClass('checkboxDisabled');
				//check checkbox and corresponding div 
				$('.p_tab').find('.write .parent_' + name).attr('checked', false);
				$('.p_tab').find('.write .parent_' + name).siblings('div.input').removeClass('checked');
				break;
		}
	}
}

function initializeCheckboxes(obj)
{
	var checkBoxDiv = $(obj).find('div.input');
	var input = checkBoxDiv.siblings('input');
	if (input.is(':not(:disabled)') && !checkBoxDiv.hasClass('checkboxDisabled')) {
		if (input.is(':checked')) {
			checkBoxDiv.addClass('checked');
		} else {
			checkBoxDiv.removeClass('checked');
		}
	}
}
function chooseActions()
{
	if ($('.action_list').css('display')=="none") {
		if ($('#users-list').is(':visible')) {
			$('#users-list').fadeOut(100);
			$('.deletInv').hide();
		} else {
			//function: view/invoices/index
			checkStatus();
			$('.action_list').show();
		}
	}		
	else 
	{
		$('.action_list').fadeOut(100);
		$('.deletInv').hide();
	}
	return false;
}

function split( val ) {
    return val.split( /,\s*/ );
}
function extractLast( term ) {
    return split( term ).pop();
}

function showToolTip(element) {
	var parent = $(element).parent(); 
	if (parent.find('u').is(':visible')) {
		parent.find('.panel').show();
		height = parent.find('.cover').outerHeight();
		parent.find('.panel').css({'top':(-35-height)+'px'});
	}
}


function showToolTipM(element) {
	var parent = $(element).parent(); 
	if (parent.find('u').is(':visible')) {
		parent.find('.panelM').show();
		height = parent.find('.coverM').outerHeight();
		parent.find('.panelM').css({'top':(-35-height)+'px'});
	}
}

function hideToolTipM(element) {
	$(element).parent().find('.panelM').fadeOut(100);
}

function showToolTipExpenses(element) {
	var parent = $(element).parent(); 
	parent.find('.panel_expenses').show();
	height = parent.find('.cover').outerHeight();
	parent.find('.panel_expenses').css({'top':(-20-height)+'px'});
}

function hideToolTip(element) {
	$(element).parent().find('.panel').fadeOut(100);
}
function hideToolTipExpenses(element) {
	$(element).parent().find('.panel_expenses').fadeOut(100);
}

function createPickers(context) {
  $(".datefield", context || document).datepicker({
    showAnim:'fadeIn',
    dateFormat:'dd/mm/yy'
  });
}

function showErrors(errors)
{
	if (errors) {
		var err = "";		
		$.each(errors, function(i, item) {
			err += item + "<br />";
		});
		var action_buttons = {
	        "Ok": {
				click: function() 
		        {
		            $( this ).dialog( "close" );
		        },
		        class : 'ok_button'
	        }
		}
		custom_alert('ERROR MESSAGE', err, action_buttons);
	}
}

function number_format( number, decimals, dec_point, thousands_sep ) {
    // http://kevin.vanzonneveld.net
    // + original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + bugfix by: Michael White (http://crestidg.com)
    // + bugfix by: Benjamin Lupton
    // + bugfix by: Allan Jensen (http://www.winternet.no)
    // + revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // * example 1: number_format(1234.5678, 2, '.', '');
    // * returns 1: 1234.57
     
    var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
    var d = dec_point == undefined ? "," : dec_point;
    var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
     
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function rtrim (str, charlist) {
	  // http://kevin.vanzonneveld.net
	  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +      input by: Erkekjetter
	  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +   bugfixed by: Onno Marsman
	  // +   input by: rem
	  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	  // *     example 1: rtrim('    Kevin van Zonneveld    ');
	  // *     returns 1: '    Kevin van Zonneveld'
	  charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\\$1');
	  var re = new RegExp('[' + charlist + ']+$', 'g');
	  return (str + '').replace(re, '');
}

function formatNumber(number, decimal_no, decimal_char, thousand_char) 
{
	decimal_no = isNaN(decimal_no = Math.abs(decimal_no)) ? 3 : decimal_no;
	decimal_char = decimal_char == undefined ? "." : dec_point;
	thousand_char = thousand_char == undefined ? "," : thousand_char;
	number = parseFloat(number);
	return  rtrim(rtrim(number_format(number, decimal_no, decimal_char, thousand_char), '0'), decimal_char);
}


function setSelects() {
	$( ".firstOption .text" ).each(function( index ) {
		var parent = $(this).parents('.dropdownWrap'); 
	});
}

/* 
 * select functions START 
 * to use for all selects in the site 
 * */

// the selected option's value is written in a hidden field
function getThisOption(othis) {
	var parent = $(othis).parents('.dropdownWrap'); 
	parent.find('.firstOption .text').html($(othis).html());
	parent.find('.dropdownSelect .options').css({ 'height': '0px' });
	parent.find('.firstOption .hidden_select').val($(othis).attr('data-id'));
}
function openDropDown(othis) {
	closeDropDown("dropdownSelect");
	var parent = $(othis).parents('.dropdownSelect');
	if (parent.find('.options').css('height') == "0px") {
		parent.addClass('opn');
		parent.find('.options').css('width', parent.parents('.row').width());
		parent.find('.options').stop(false).animate({ 'height': 106 + 'px' }, '700', 'easeInQuart', function () {
			if (parent.find(".scrollOptions").length > 0) {
				if (!parent.find(".scrollOptions").find(".mCustomScrollBox").length > 0) {
					parent.find(".scrollOptions").mCustomScrollbar({horizontalScroll:true});
				}
			}
		});
	}
	else {
		closeDropDown("dropdownSelect");
	}
	closeDropDown("dropdownWrap");
}
function closeDropDown(element) {
	$(element).removeClass('opn');
	$(element).find('.options').stop(false).animate({ 'height': 0 + 'px' }, '700', 'easeInQuart', function () { });
}
/* select functions END */

// Function to use for autocomplete to select an item from the autcomplete list event
// if the user has written the word, without selecting an item from the list
// or if it doesn't match an item it empty's the input
function blurAutocomplete(event, element, hiddenElement, callback) {
	var myInput = $(element);
	var hiddenInput = $(hiddenElement);
    var autocomplete = myInput.data("uiAutocomplete");
    var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex(myInput.val()) + "$", "i");
   
    $.each(autocomplete.options.source, function(i, row) {
        // Check if each autocomplete item is a case-insensitive match on the input
        var item = row;
        if (matcher.test(item.label || item.value || item)) {
            //There was a match, lets stop checking
            autocomplete.selectedItem = item;
            console.log(item);
            return;
        }
    });
    //if there was a match trigger the select event on that match
    //I would recommend matching the label to the input in the select event
    if (autocomplete.selectedItem) {
    	console.log(autocomplete.selectedItem);
        autocomplete._trigger("select", event, {
            item: autocomplete.selectedItem
        });
        hiddenInput.val(autocomplete.selectedItem.id);
    //there was no match, clear the input
    } else {
        myInput.val('');
        hiddenInput.val('');
        
    }
    if (callback) {
    	callback();
	}
}



//open popups in header

$(document).ready(function(){
	$('.header .options .notification').click(function(event){
		$('.popups').hide();
		$('#popup1').show();
		$("#popup1 .container").mCustomScrollbar("destroy");
		$("#popup1 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});

	$('.header .options .audit').click(function(event){
		$('.popups').hide();
		$('#popup10').show();
		$("#popup10 .container").mCustomScrollbar("destroy");
		$("#popup10 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
	
	$('.header .options .birthday').click(function(event){
		$('.popups').hide();
		$('#popup2').show();
		$("#popup2 .container").mCustomScrollbar("destroy");
		$("#popup2 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
	
		$('.header .options .events').click(function(event){
		$('.popups').hide();
		$('#popup3').show();
		$("#popup3 .container").mCustomScrollbar("destroy");
		$("#popup3 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
	
	$('.header .options .remarks').click(function(event){
		$('.popups').hide();
		$('#popup4').show();
		$("#popup4 .container").mCustomScrollbar("destroy");
		$("#popup4 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
	
	$('.header .options .visa').click(function(event){
		$('.popups').hide();
		$('#popup5').show();
		$("#popup5 .container").mCustomScrollbar("destroy");
		$("#popup5 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
	
	$('.header .options .tasks').click(function(event){
		$('.popups').hide();
		$('#popup6').show();
		$("#popup6 .container").mCustomScrollbar("destroy");
		$("#popup6 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
	
		$('.header .options .budget').click(function(event){
		$('.popups').hide();
		$('#popup7').show();
		$("#popup7 .container").mCustomScrollbar("destroy");
		$("#popup7 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
	
	$('.header .options .calendar').click(function(event){
		$('.popups').hide();
		$('#popup8').show();
		$("#popup8 .container").mCustomScrollbar("destroy");
		$("#popup8 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});



	$('.header .options .calendar_new').click(function(event){
		$('.popups').hide();
		$('#popup9').show();
		$("#popup9 .container").mCustomScrollbar("destroy");
		$("#popup9 .container").mCustomScrollbar({   // initialise scroll plugin
		scrollButtons:{
		enable:true
		}
		});
		event.stopPropagation();
	});
	$(document).click(function(){
		if($('#popup1').is(':visible'))
		{
			$('#popup1').hide();
		}
		
		if($('#popup2').is(':visible'))
		{
			$('#popup2').hide();
		}
		
		if($('#popup3').is(':visible'))
		{
			$('#popup3').hide();
		}
		
		if($('#popup4').is(':visible'))
		{
			$('#popup4').hide();
		}
		
		if($('#popup5').is(':visible'))
		{
			$('#popup5').hide();
		}
		
		if($('#popup6').is(':visible'))
		{
			$('#popup6').hide();
		}
		
		if($('#popup7').is(':visible'))
		{
			$('#popup7').hide();
		}
		
		if($('#popup8').is(':visible'))
		{
			$('#popup8').hide();
		}
		if($('#popup9').is(':visible'))
		{
			$('#popup9').hide();
		}
		if($('#popup10').is(':visible'))
		{
			$('#popup10').hide();
		}
	});
	
	$('#popup1, #popup2, #popup3, #popup4, #popup5, #popup6, #popup7, #popup8, #popup9,#popup10').click(function(event){
		event.stopPropagation();
	});
});
function openNotification(dis) {
	dis.find('.notifWrapPlace').stop(false).animate({ 'height': 442 + 'px' }, '700', 'easeInQuart');
} 
function closeNotification(dis) {
	dis.find('.notifWrapPlace').stop(false).animate({ 'height': 0 + 'px' }, '700', 'easeInQuart');
} 

// maximise and minimise rows

$(document).ready(function(){
	
	$('#info .row').click(function(){
		$(this).find('.plus').toggleClass('minus');
		$(this).toggleClass('selected');
		
	});
	
	$('#info .bernardRow').click(function(){
		$('#info .row.bernard').toggle();
	});
	
	$('#info .elioRow').click(function(){
		$('#info .row.elio').toggle();
	});
	
	$('#info .johnRow').click(function(){
		$('#info .row.john').toggle();
	});
	
});

function addWidgetDashboard(element) {
	
	var dashboard = $(element).attr('data-dashboard');
	var div = $(element).siblings('.bggroup');
	var select = div.find('.widgets_select');
	$.ajax({
 		type: "POST",					
	  	url: configJs.urls.baseUrl + '/widgets/getWidgetsOff', 
	  	dataType: "json",
	  	data: {id:dashboard},
	  	success: function(data) {
	  		if (data.options)
	  		{
	  			select.html(data.options);
		  		div.show();
	  		}
  		}
	});
}

//ramy changing how widgets load
function loadWidget(element) {
	//var e = document.getElementById($(element).attr('widgets_sel'));
	var div = $(element).siblings('.bggroup');
	var select = div.find('.widgets_select');
	console.log(select);
	//var widgetid = select.options[select.selectedIndex].text;
	var widgetid = select;
	$.ajax({
 		type: "POST",					
	  	url: configJs.urls.baseUrl + '/site/GetWidget', 
	  	dataType: "json",
	  	data: {id:widgetid},
	  	success: function(data) {
	  		
  		}
	});
}

function deletewid(id) {
	var board = $(".board[data-id="+id+"]").parents('.sortableDash');
	 $(".sortableDash .board[data-id="+id+"]").animate({
		 opacity: 0
		}, 800, 
		function() {
			$(this).remove();
			board.sortable('refresh');
			jQuery.ajax({
				type: "POST",
				url: configJs.urls.baseUrl +'/widgets/delete',
				dataType: 'json',
				data: ({id:id}),
				beforeSend: function(x) {
					if (x && x.overrideMimeType) {
						x.overrideMimeType("application/j-son;charset=UTF-8");
					}
				},
				success: function(data) {}
			});
		});
}
function mareste(id){
	$("#u"+id).dialog().dialog('open'); //return false;
	
	if($('#graph-pop-up-'+id).is(':visible')){ //if the container is visible on the page
		changeMonthTopCustomer($(".status.sr_top_customer.colorRed").data("id"));
		
	}
	if($('#graph-billability1').is(':visible')){ //if the container is visible on the page
		if($(".status.status_billability.colorRed").data("id") == null)
		{
			createGridBillability();
		}else{
			changeBillability($(".status.status_billability.colorRed").data("id"));
		}
	}
	if($('#graph-country-revenues1').is(':visible')){ //if the container is visible on the page
		changeOldYear($(".status.sr_country_revenues.colorRed").data("id"));
	}
	if($('#graph-sr-submitted-reason1').is(':visible')){ //if the container is visible on the page
		changeSubmittedReason($(".status.sr_submitted_reas.colorRed").data("id"));
	}
	if($('#graph-sr-submitted-cust1').is(':visible')){ //if the container is visible on the page
		changeYearsSubmitted($(".status.sr_submitted_cust.colorRed").data("id"));
	}
	if($('#pieChartContainerTime1').is(':visible'))
	{ //if the container is visible on the page
		if($(".status.sr_time.colorRed").data("id") == null)
		{
			createGridTime();
		}else{
			changeTime($(".status.sr_time.colorRed").data("id"));
		}
	}
	if($('#graph-support1').is(':visible')){ //if the container is visible on the page
		changeMonthSupport($(".status.sr_support.colorRed").data("id"));
	}
	if($('#graph-sr-submitted1').is(':visible')){ //if the container is visible on the page
		changeMonthSubmitted($(".status.sr_submitted.colorRed").data("id"));
	}
	if($('#graph-sr-customer1').is(':visible')){ //if the container is visible on the page
		changeMonthCustomer($(".status.sr_customer.colorRed").data("id"));
	}
	if($('#graph-sr-close-resource1').is(':visible')){ //if the container is visible on the page
		if($(".status.sr_closeRes.colorRed").data("id") != null)
			changeMonthCloseRes($(".status.sr_closeRes.colorRed").data("id"));
	}
	if($('#graph-sr1').is(':visible')){ //if the container is visible on the page
		changeMonthClose($(".status.sr_close.colorRed").data("id"));
	}
	if($('#graph-soldBy-revenues1').is(':visible')){ //if the container is visible on the page
		changeSoldByYear($(".status.sr_soldBy_revenues.colorRed").data("id"));
	}
	if($('#graph-ea-revenues1').is(':visible')){ //if the container is visible on the page
		changeEAYear($(".status.sr_eaTypes_revenues.colorRed").data("id"));
	}
	if($('#graph-11').is(':visible')){ //if the container is visible on the page
		if($(".status.status_eas.colorRed").data("id") == null)
		{
			createGridCustomer1();
		}else{
			change($(".status.status_eas.colorRed").data("id"));
		}
	}
	if($('#graphCustomer-11').is(':visible')){ //if the container is visible on the page
		if($(".status.status_customer_top.colorRed").data("id") == null){
			createGridTime1();
		}else if($(".status.status_customer_top.colorRed").data("id") == 5 || $(".status.status_customer_top.colorRed").data("id") == 10 || $(".status.status_customer_top.colorRed").data("id") == 20){
			changeTop($(".status.status_customer_top.colorRed").data("id"));
		}else{
			changeYear($(".status.status_customer_top.colorRed").data("id"));
		}
	}
	if($('#graphCustomer-11').is(':visible')){ //if the container is visible on the page
		changeTop($(".status.status_customer_top.colorRed").data("id"));
	}
	$( "div.bcontenu.z-index" ).parent().css( "z-index", "1009" );
}
//inline edit
(function($) {
    $.fn.editable = function(target, options) {
        if ('disable' == target) {
            $(this).data('disabled.editable', true);
            return;
        }
        if ('enable' == target) {
            $(this).data('disabled.editable', false);
            return;
        }
        if ('destroy' == target) {
            $(this)
                .unbind($(this).data('event.editable'))
                .removeData('disabled.editable')
                .removeData('event.editable');
            return;
        }
        
        var settings = $.extend({}, $.fn.editable.defaults, {target:target}, options);
        
        /* setup some functions */
        var plugin   = $.editable.types[settings.type].plugin || function() { };
        var submit   = $.editable.types[settings.type].submit || function() { };
        var buttons  = $.editable.types[settings.type].buttons 
                    || $.editable.types['defaults'].buttons;
        var content  = $.editable.types[settings.type].content 
                    || $.editable.types['defaults'].content;
        var element  = $.editable.types[settings.type].element 
                    || $.editable.types['defaults'].element;
        var reset    = $.editable.types[settings.type].reset 
                    || $.editable.types['defaults'].reset;
        var callback = settings.callback || function() { };
        var onedit   = settings.onedit   || function() { }; 
        var onsubmit = settings.onsubmit || function() { };
        var onreset  = settings.onreset  || function() { };
        var onerror  = settings.onerror  || reset;
          
        /* Show tooltip. */
        if (settings.tooltip) {
            $(this).attr('title', settings.tooltip);
        }
        
        settings.autowidth  = 'auto' == settings.width;
        settings.autoheight = 'auto' == settings.height;
        
        return this.each(function() {
                        
            /* Save this to self because this changes when scope changes. */
            var self = this;  
                   
            /* Inlined block elements lose their width and height after first edit. */
            /* Save them for later use as workaround. */
            var savedwidth  = $(self).width();
            var savedheight = $(self).height();

            /* Save so it can be later used by $.editable('destroy') */
            $(this).data('event.editable', settings.event);
            
            /* If element is empty add something clickable (if requested) */
            if (!$.trim($(this).html())) {
                $(this).html(settings.placeholder);
            }
            
            $(this).bind(settings.event, function(e) {
                
                /* Abort if element is disabled. */
                if (true === $(this).data('disabled.editable')) {
                    return;
                }
                
                /* Prevent throwing an exeption if edit field is clicked again. */
                if (self.editing) {
                    return;
                }
                
                /* Abort if onedit hook returns false. */
                if (false === onedit.apply(this, [settings, self])) {
                   return;
                }
                
                /* Prevent default action and bubbling. */
                e.preventDefault();
                e.stopPropagation();
                
                /* Remove tooltip. */
                if (settings.tooltip) {
                    $(self).removeAttr('title');
                }
                
                /* Figure out how wide and tall we are, saved width and height. */
                /* Workaround for http://dev.jquery.com/ticket/2190 */
                if (0 == $(self).width()) {
                    settings.width  = savedwidth;
                    settings.height = savedheight;
                } else {
                    if (settings.width != 'none') {
                        settings.width = 
                            settings.autowidth ? $(self).width()  : settings.width;
                    }
                    if (settings.height != 'none') {
                        settings.height = 
                            settings.autoheight ? $(self).height() : settings.height;
                    }
                }
                
                /* Remove placeholder text, replace is here because of IE. */
                if ($(this).html().toLowerCase().replace(/(;|"|\/)/g, '') == 
                    settings.placeholder.toLowerCase().replace(/(;|"|\/)/g, '')) {
                        $(this).html('');
                }
                                
                self.editing    = true;
                self.revert     = $(self).html();
                $(self).html('');

                /* Create the form object. */
                var form = $('<form />');
                
                /* Apply css or style or both. */
                if (settings.cssclass) {
                    if ('inherit' == settings.cssclass) {
                        form.attr('class', $(self).attr('class'));
                    } else {
                        form.attr('class', settings.cssclass);
                    }
                }

                if (settings.style) {
                    if ('inherit' == settings.style) {
                        form.attr('style', $(self).attr('style'));
                        /* IE needs the second line or display wont be inherited. */
                        form.css('display', $(self).css('display'));                
                    } else {
                        form.attr('style', settings.style);
                    }
                }

                /* Add main input element to form and store it in input. */
                var input = element.apply(form, [settings, self]);

                /* Set input content via POST, GET, given data or existing value. */
                var input_content;
                
                if (settings.loadurl) {
                    var t = setTimeout(function() {
                        input.disabled = true;
                        content.apply(form, [settings.loadtext, settings, self]);
                    }, 100);

                    var loaddata = {};
                    loaddata[settings.id] = self.id;
                    if ($.isFunction(settings.loaddata)) {
                        $.extend(loaddata, settings.loaddata.apply(self, [self.revert, settings]));
                    } else {
                        $.extend(loaddata, settings.loaddata);
                    }
                    $.ajax({
                       type : settings.loadtype,
                       url  : settings.loadurl,
                       data : loaddata,
                       async : false,
                       success: function(result) {
                          window.clearTimeout(t);
                          input_content = result;
                          input.disabled = false;
                       }
                    });
                } else if (settings.data) {
                    input_content = settings.data;
                    if ($.isFunction(settings.data)) {
                        input_content = settings.data.apply(self, [self.revert, settings]);
                    }
                } else {
                    input_content = self.revert; 
                }
                content.apply(form, [input_content, settings, self]);

                input.attr('name', settings.name);
        
                /* Add buttons to the form. */
                buttons.apply(form, [settings, self]);
         
                /* Add created form to self. */
                $(self).append(form);
         
                /* Attach 3rd party plugin if requested. */
                plugin.apply(form, [settings, self]);

                /* Focus to first visible form element. */
                $(':input:visible:enabled:first', form).focus();

                /* Highlight input contents when requested. */
                if (settings.select) {
                    input.select();
                }
        
                /* discard changes if pressing esc */
                input.keydown(function(e) {
                    if (e.keyCode == 27) {
                        e.preventDefault();
                        reset.apply(form, [settings, self]);
                    }
                });

                /* Discard, submit or nothing with changes when clicking outside. */
                /* Do nothing is usable when navigating with tab. */
                var t;
                if ('cancel' == settings.onblur) {
                    input.blur(function(e) {
                        /* Prevent canceling if submit was clicked. */
                        t = setTimeout(function() {
                            reset.apply(form, [settings, self]);
                        }, 500);
                    });
                } else if ('submit' == settings.onblur) {
                    input.blur(function(e) {
                        /* Prevent double submit if submit was clicked. */
                        t = setTimeout(function() {
                            form.submit();
                        }, 200);
                    });
                } else if ($.isFunction(settings.onblur)) {
                    input.blur(function(e) {
                        settings.onblur.apply(self, [input.val(), settings]);
                    });
                } else {
                    input.blur(function(e) {
                      /* TODO: maybe something here */
                    });
                }

                form.submit(function(e) {

                    if (t) { 
                        clearTimeout(t);
                    }

                    /* Do no submit. */
                    e.preventDefault(); 
            
                    /* Call before submit hook. */
                    /* If it returns false abort submitting. */                    
                    if (false !== onsubmit.apply(form, [settings, self])) { 
                        /* Custom inputs call before submit hook. */
                        /* If it returns false abort submitting. */
                        if (false !== submit.apply(form, [settings, self])) { 

                          /* Check if given target is function */
                          if ($.isFunction(settings.target)) {
                              var str = settings.target.apply(self, [input.val(), settings]);
                              $(self).html(str);
                              self.editing = false;
                              callback.apply(self, [self.innerHTML, settings]);
                              /* TODO: this is not dry */                              
                              if (!$.trim($(self).html())) {
                                  $(self).html(settings.placeholder);
                              }
                          } else {
                              /* Add edited content and id of edited element to POST. */
                              var submitdata = {};
                              submitdata[settings.name] = input.val();
                              submitdata[settings.id] = self.id;
                              /* Add extra data to be POST:ed. */
                              if ($.isFunction(settings.submitdata)) {
                                  $.extend(submitdata, settings.submitdata.apply(self, [self.revert, settings]));
                              } else {
                                  $.extend(submitdata, settings.submitdata);
                              }

                              /* Quick and dirty PUT support. */
                              if ('PUT' == settings.method) {
                                  submitdata['_method'] = 'put';
                              }

                              /* Show the saving indicator. */
                              $(self).html(settings.indicator);
                              
                              /* Defaults for ajaxoptions. */
                              var ajaxoptions = {
                                  type    : 'POST',
                                  data    : submitdata,
                                  dataType: 'html',
                                  url     : settings.target,
                                  success : function(result, status) {
                                      if (ajaxoptions.dataType == 'html') {
                                        $(self).html(result);
                                      }
                                      self.editing = false;
                                      callback.apply(self, [result, settings]);
                                      if (!$.trim($(self).html())) {
                                          $(self).html(settings.placeholder);
                                      }
                                  },
                                  error   : function(xhr, status, error) {
                                      onerror.apply(form, [settings, self, xhr]);
                                  }
                              };
                              
                              /* Override with what is given in settings.ajaxoptions. */
                              $.extend(ajaxoptions, settings.ajaxoptions);   
                              $.ajax(ajaxoptions);          
                              
                            }
                        }
                    }
                    
                    /* Show tooltip again. */
                    $(self).attr('title', settings.tooltip);
                    
                    return false;
                });
            });
            
            /* Privileged methods */
            this.reset = function(form) {
                /* Prevent calling reset twice when blurring. */
                if (this.editing) {
                    /* Before reset hook, if it returns false abort reseting. */
                    if (false !== onreset.apply(form, [settings, self])) { 
                        $(self).html(self.revert);
                        self.editing   = false;
                        if (!$.trim($(self).html())) {
                            $(self).html(settings.placeholder);
                        }
                        /* Show tooltip again. */
                        if (settings.tooltip) {
                            $(self).attr('title', settings.tooltip);                
                        }
                    }                    
                }
            };            
        });

    };

    $.editable = {
        types: {
            defaults: {
                element : function(settings, original) {
                    var input = $('<input type="hidden"></input>');                
                    $(this).append(input);
                    return(input);
                },
                content : function(string, settings, original) {
                    $(':input:first', this).val(string);
                },
                reset : function(settings, original) {
                  original.reset(this);
                },
                buttons : function(settings, original) {
                    var form = this;
                    if (settings.submit) {
                        /* If given html string use that. */
                        if (settings.submit.match(/>$/)) {
                            var submit = $(settings.submit).click(function() {
                                if (submit.attr("type") != "submit") {
                                    form.submit();
                                }
                            });
                        /* Otherwise use button with given string as text. */
                        } else {
                            var submit = $('<button type="submit" />');
                            submit.html(settings.submit);                            
                        }
                        $(this).append(submit);
                    }
                    if (settings.cancel) {
                        /* If given html string use that. */
                        if (settings.cancel.match(/>$/)) {
                            var cancel = $(settings.cancel);
                        /* otherwise use button with given string as text */
                        } else {
                            var cancel = $('<button type="cancel" />');
                            cancel.html(settings.cancel);
                        }
                        $(this).append(cancel);

                        $(cancel).click(function(event) {
                            if ($.isFunction($.editable.types[settings.type].reset)) {
                                var reset = $.editable.types[settings.type].reset;                                                                
                            } else {
                                var reset = $.editable.types['defaults'].reset;                                
                            }
                            reset.apply(form, [settings, original]);
                            return false;
                        });
                    }
                }
            },
            text: {
                element : function(settings, original) {
                    var input = $('<input />');
                    if (settings.width  != 'none') { input.attr('width', settings.width);  }
                    if (settings.height != 'none') { input.attr('height', settings.height); }
                    /* https://bugzilla.mozilla.org/show_bug.cgi?id=236791 */
                    //input[0].setAttribute('autocomplete','off');
                    input.attr('autocomplete','off');
                    $(this).append(input);
                    return(input);
                }
            },
            textarea: {
                element : function(settings, original) {
                    var textarea = $('<textarea />');
                    if (settings.rows) {
                        textarea.attr('rows', settings.rows);
                    } else if (settings.height != "none") {
                        textarea.height(settings.height);
                    }
                    if (settings.cols) {
                        textarea.attr('cols', settings.cols);
                    } else if (settings.width != "none") {
                        textarea.width(settings.width);
                    }
                    $(this).append(textarea);
                    return(textarea);
                }
            },
            select: {
               element : function(settings, original) {
                    var select = $('<select />');
                    $(this).append(select);
                    return(select);
                },
                content : function(data, settings, original) {
                    /* If it is string assume it is json. */
                    if (String == data.constructor) {      
                        eval ('var json = ' + data);
                    } else {
                    /* Otherwise assume it is a hash already. */
                        var json = data;
                    }
                    for (var key in json) {
                        if (!json.hasOwnProperty(key)) {
                            continue;
                        }
                        if ('selected' == key) {
                            continue;
                        } 
                        var option = $('<option />').val(key).append(json[key]);
                        $('select', this).append(option);    
                    }                    
                    /* Loop option again to set selected. IE needed this... */ 
                    $('select', this).children().each(function() {
                        if ($(this).val() == json['selected'] || 
                            $(this).text() == $.trim(original.revert)) {
                                $(this).attr('selected', 'selected');
                        }
                    });
                    /* Submit on change if no submit button defined. */
                    if (!settings.submit) {
                        var form = this;
                        $('select', this).change(function() {
                            form.submit();
                        });
                    }
                }
            }
        },

        /* Add new input type */
        addInputType: function(name, input) {
            $.editable.types[name] = input;
        }
    };

    /* Publicly accessible defaults. */
    $.fn.editable.defaults = {
        name       : 'value',
        id         : 'id',
        type       : 'text',
        width      : 'auto',
        height     : 'auto',
        event      : 'click.editable',
        onblur     : 'cancel',
        loadtype   : 'GET',
        loadtext   : 'Loading...',
        placeholder: 'Click to edit',
        loaddata   : {},
        submitdata : {},
        ajaxoptions: {}
    };

})(jQuery);
function checkContainer1 () {
	  if($('#pieSubmittedCustomer').is(':visible')){ //if the container is visible on the page
	    createGridCustomer();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer1, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer2 () {
	  if($('#pieChartContainerTime').is(':visible')){ //if the container is visible on the page
	    createGridTime();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer2, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer3 () {
	  if($('#graph-sr-top-customer').is(':visible')){ //if the container is visible on the page
	    createGridTopCustomer();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer3, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer4 () {
	  if($('#graph-sr-customer').is(':visible')){ //if the container is visible on the page
	    createGridSrCustomer();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer4, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer5 () {
	  if($('#graph-soldBy-revenues').is(':visible')){ //if the container is visible on the page
	    createGridSoldRevenues();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer5, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer6 () {
	  if($('#graph-ea-revenues').is(':visible')){ //if the container is visible on the page
	    createGridTypeRevenues();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer6, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer7 () {
	  if($('#graph-country-revenues').is(':visible')){ //if the container is visible on the page
	    createGridCountry();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer7, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer8 () {
	  if($('#graphCustomer-1').is(':visible')){ //if the container is visible on the page
		  createGridTime1();  //Adds a grid to the html
	  } else {
		  setTimeout(checkContainer8, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer9 () {
	  if($('#graph-sr').is(':visible')){ //if the container is visible on the page
		  createGridStack();  //Adds a grid to the html
	  } else {
		  setTimeout(checkContainer9, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer10 () {
	  if($('#chartContainerMaintenance').is(':visible')){ //if the container is visible on the page
		  Maintenance1();  //Adds a grid to the html
	  } else {
		  setTimeout(checkContainer10, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer11 () {
	  if($('#graph-support').is(':visible')){ //if the container is visible on the page
		  createGridSupport();  //Adds a grid to the html
	  } else {
		  setTimeout(checkContainer11, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer12 () {
	  if($('#chartContainerLine').is(':visible')){ //if the container is visible on the page
		  Maintenance2();   //Adds a grid to the html
	  } else {
		  setTimeout(checkContainer12, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer13 () {
	  if($('#graph-sr-submitted').is(':visible')){ //if the container is visible on the page
		  createGridSubMonth();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer13, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer14 () {
	  if($('#graph-sr-submitted-reason').is(':visible')){ //if the container is visible on the page
		  CreateGridSubReason();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer14, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer15 () {
	  if($('#graph-1').is(':visible')){ //if the container is visible on the page
		  createGridCustomer1();  //Adds a grid to the html
	  } else {
	    setTimeout(checkContainer15, 10); //wait 50 ms, then try again
	  }
	}
function checkContainer16 () {
	if($('#graph-billability').is(':visible')){ //if the container is visible on the page
		createGridBillability();  //Adds a grid to the html
	} else {
	  setTimeout(checkContainer16, 10); //wait 50 ms, then try again
	}
}
function checkContainer17(){
	  if($('#graph-sr-system-down').is(':visible')){ //if the container is visible on the page
		  createGridStackSystemDown();  //Adds a grid to the html
	  } else {
		  setTimeout(checkContainer17, 10); //wait 50 ms, then try again
	  }
}
function checkContainer18(){
	  if($('#graph-sr-openstatus-customer').is(':visible')){ //if the container is visible on the page
		  createGridOpenStatusCustomer();  //Adds a grid to the html
	  } else {
		  setTimeout(checkContainer18, 10); //wait 50 ms, then try again
	  }
}

function checkContainer19(){
	  if($('#graph-sr-openseverity-customer').is(':visible')){ //if the container is visible on the page
		  createGridOpenSeverityCustomer();  //Adds a grid to the html
	  } else {
		  setTimeout(checkContainer19, 10); //wait 50 ms, then try again
	  }
}

function checkContainer20 () {
	  if($('#graph-submittedresolved').is(':visible')){ //if the container is visible on the page
		  createGridsubmittedresolved();  //Adds a grid to the html
	  } else {
		  setTimeout(checkContainer20, 10); //wait 50 ms, then try again
	  }
}
function drowChart(pieChartDataSource,id){
      $("#"+id).dxPieChart({
      dataSource: pieChartDataSource,
	     legend: {
    		orientation: "horizontal",
    		itemTextPosition: "left",
    		horizontalAlignment: "right",
    		verticalAlignment: "bottom",
    		rowCount: 0
        },
	    series: {
 		  argumentField: 'category',
 		  valueField: 'value',
	    },tooltip: {
            enabled: true,
            customizeText: function () {
                return this.valueText;
            }
        }
      });
}
function closeDialog(id){
	$("#u"+id).dialog("close");
}