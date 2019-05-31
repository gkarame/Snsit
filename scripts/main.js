$(window).load(function(){if($('.mytabs').length>0)
{$('.mytabs').removeClass('hidden');if($("form .saveDiv").length){if($(".documents_div").is(":visible")||$("#eas-grid").is(":visible")||$("#invoices-grid").is(":visible")){$("form .saveDiv").hide()}else{$("form .saveDiv").show()}}}
if($('.perftabs').length>0)
{$('.perftabs').removeClass('hidden');if($("form .saveDiv").length){if($(".documents_div").is(":visible")||$("#eas-grid").is(":visible")||$("#invoices-grid").is(":visible")){$("form .saveDiv").hide()}else{$("form .saveDiv").show()}}}
if($('.main_timesheet').length>0)
{$('.main_timesheet').removeClass('hidden')}});var idth=0;var to_left=0;var to_right=0;var submitted=!1;$(document).ready(function(){checkContainer1();checkContainer2();checkContainer3();checkContainer4();checkContainer9();checkContainer10();checkContainer12();checkContainer45();checkContainer43();checkContainer11();checkContainer49();checkContainer13();checkContainer14();checkContainer18();checkContainer19();checkContainer46();checkContainer17();checkContainer23();checkContainer28();checkContainer29();checkContainer30();checkContainer47();checkContainer50();checkContainer31();checkContainer48();checkContainer32();checkContainer33();checkContainer34();checkContainer44();checkContainer35();checkContainer36();checkContainer37();checkContainer38();checkContainer39();checkContainer40();checkContainer41();checkContainer42();$('.sortableDash').sortable({items:'.board',opacity:0.5,start:function(evt,ui){var link=ui.item.find('.dragg');var id_widget=link.attr('data-id');link.data('click-event',link.attr('onclick'));link.attr('onclick',"mareste("+id_widget+")");link.addClass('select')},stop:function(event,ui){var link=ui.item.find('.dragg');link.removeClass('select');var sortedIds=$(this).sortable("toArray").join(',');$.ajax({data:{id:sortedIds},type:'POST',url:configJs.urls.baseUrl+'/widgets/setOrder',dataType:"json"})}});$('#menu').hover(function(){$('#content').addClass('zIndex')},function(){$('#content').removeClass('zIndex')});$('.chk').each(function(){initializeCheckboxes($(this))});$(".popup_shareby").dialog({autoOpen:!1,height:'auto',width:'auto',modal:!0,close:function(){$(this).html('')}});$('input').keydown(function(e){var key=e.charCode?e.charCode:e.keyCode?e.keyCode:0;if(key==13){e.preventDefault();$(this).closest('form:not(.ajax_submit)').submit()}});$(document).ajaxStop(function(){if($('.tache').prev().find('td.empty')){$('.tache').prev().find('td.empty').parent().hide()}});if($('.tache').prev().find('td.empty')){$('.tache').prev().find('td.empty').parent().hide()}
$(document).on('mouseenter','.links .link a',function(e){$(this).siblings('.tabClose').addClass('hovered')}).on('mouseleave','.links .link a',function(e){$(this).siblings('.tabClose').removeClass('hovered')}).on('mouseenter','.links .link .tabClose',function(e){$(this).addClass('hovered');$(this).siblings('a').addClass('hovered')}).on('mouseleave','.links .link .tabClose',function(e){$(this).removeClass('hovered');$(this).siblings('a').removeClass('hovered')}).on('click',"body",function(e){if($('.panel').css("display")=="block"){$('.panel').fadeOut(100);e.stopPropagation()}
if($('.groups-list').is(":visible")&&(!$(e.target).parents('.groups-list').length)){$('.groups-list').fadeOut(100);e.stopPropagation()}
if($('#users-list').is(":visible")&&(!$(e.target).parents('#users-list').length)){$('#users-list').fadeOut(100);e.stopPropagation()}
if($('.popup_list').is(":visible")&&(!$(e.target).parents('.popup_list').length)&&(!$(e.target).parents('.ui-autocomplete').length)&&(!$(e.target).parents('.confirm_dialog'))){$('.popup_list').fadeOut(100);e.stopPropagation()}
if($('.actionPanel').is(":visible")&&(!$(e.target).parents('.actionPanel').length)&&(!$(e.target).parents('.triggerAction').length)){$('.actionPanel').fadeOut(100);e.stopPropagation()}}).on('click',".tabClose",function(e){var href=$(this).siblings('a').attr('href');if(href.indexOf("/documents/create")>=0)
{closeTab(href,!0)}else{closeTab(href)}}).on('keydown','.auto_email',function(event){if(event.keyCode===$.ui.keyCode.TAB&&$(this).data("ui-autocomplete").menu.active){event.preventDefault()}}).on('focus','.auto_email',function(e){$(this).autocomplete({source:function(request,response){$.getJSON(configJs.urls.baseUrl+"/site/autocomplete?with=true",{term:extractLast(request.term)},response)},search:function(){var term=extractLast(this.value);if(term.length<1){return!1}},focus:function(){return!1},select:function(event,ui){var terms=split(this.value);terms.pop();terms.push(ui.item.value);if($('#ShareByForm_header').length>0){var head=[];$.each(terms,function(index,value){value=value.split(' <')[0];firstname=value.split(' ')[0];head.push(firstname)});$('#ShareByForm_header').val('Dear '+head.join(', ')+', ')}
terms.push("");this.value=terms.join(", ");return!1}})});setWidth();timeout=$('.next');timeout2=$('.next');$('.next').mousedown(function(){timeout=setInterval(function(){if(parseInt($('.links').css('width'))-parseInt($('.links ul').css('width'))>=(parseInt($('.links ul').css('left'))-170)){clearInterval(timeout);return!1}
var to_l=$('.links ul').position().left;$('.links ul').css('left',(to_l-170))},70);return!1});$('.next').mouseup(function(){clearInterval(timeout);return!1});$('.next').mouseout(function(){clearInterval(timeout);return!1});$('.prev').mousedown(function(){timeout2=setInterval(function(){if(parseInt($('.links ul').css('left'))==0){clearInterval(timeout2);return!1}
var to_l=$('.links ul').position().left;$('.links ul').css('left',(to_l+170))},70);return!1});$('.prev').mouseup(function(){clearInterval(timeout2);return!1});$('.prev').mouseout(function(){clearInterval(timeout2);return!1});$(function(){$("#Expenses_startDate").datepicker({dateFormat:'dd/mm/yy'});$("#Expenses_endDate").datepicker({dateFormat:'dd/mm/yy'});$("#ExpensesDetails_date").datepicker({dateFormat:'dd/mm/yy'});$("#Expenses_startDate").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#Expenses_startDate").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#Expenses_startDate").offset().left))});$("#Expenses_endDate").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#Expenses_endDate").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#Expenses_endDate").offset().left))});$("#ExpensesDetails_date").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#ExpensesDetails_date").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#ExpensesDetails_date").offset().left))})});$(function(){$("#Requests_startDate").datepicker({dateFormat:'dd/mm/yy'});$("#Requests_endDate").datepicker({dateFormat:'dd/mm/yy'});$("#Requests_startDate").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#Requests_startDate").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#Requests_startDate").offset().left))});$("#Requests_endDate").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#Requests_endDate").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#Requests_endDate").offset().left))})});$(function(){$("#Eas_start_date").datepicker({dateFormat:'dd/mm/yy'});$("#Eas_end_date").datepicker({dateFormat:'dd/mm/yy'});$("#Eas_start_date").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#Eas_start_date").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#Eas_start_date").offset().left))});$("#Eas_end_date").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#Eas_end_date").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#Eas_end_date").offset().left))})});$(function(){$("#Maintenance_starting_date").datepicker({dateFormat:'dd/mm/yy'});$("#Maintenance_starting_date").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#Maintenance_starting_date").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#Maintenance_starting_date").offset().left))})});$(function(){$("#SupportDesk_due_date").datepicker({dateFormat:'dd/mm/yy'});$("#SupportDesk_date").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#SupportDesk_due_date").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#SupportDesk_due_date").offset().left))})});$(function(){$("#Requests_startDate").datepicker({dateFormat:'dd/mm/yy'});$("#Requests_startDate").click(function(){$('#ui-datepicker-div').css('top',parseFloat($("#Requests_startDate").offset().top)+25.0);$('#ui-datepicker-div').css('left',parseFloat($("#Requests_startDate").offset().left))})})});function showFull(element){var elem=$(element);var parent=elem.parent();parent.addClass('hidden');if(parent.hasClass('full'))
{parent.siblings('.shortened').removeClass('hidden')}
else{parent.siblings('.full').removeClass('hidden')}}
function closeTab(url,params){$.ajax({type:"POST",url:configJs.urls.baseUrl+'/site/closeTab',dataType:"json",data:{url:url,params:params},success:function(data){if(data.status=="success"){window.location=configJs.urls.baseUrl+data.tab}}})}
function custom_alert(title_msg,output_msg,action_buttons)
{$("#confirm_dialog .confirm_title").html(title_msg);$("#confirm_dialog .confirm_content").html(output_msg);$("#confirm_dialog").dialog({resizable:!1,draggable:!0,closeOnEscape:!0,width:'auto',buttons:action_buttons,modal:!0,width:379,height:235,dialogClass:'confirm_dialog'})}
function custom_notification(title_msg,output_msg,action_buttons)
{$("#confirm_dialog_not .confirm_title").html(title_msg);$("#confirm_dialog_not .confirm_content").html(output_msg);$("#confirm_dialog_not").dialog({resizable:!1,draggable:!0,closeOnEscape:!0,buttons:action_buttons,modal:!0,width:700,height:600,dialogClass:'confirm_dialog_not'})}
function delete_documents()
{buttons={"YES":{class:'yes_button',click:function()
{$(this).dialog("close");deleteDocs()}},"NO":{class:'no_button',click:function()
{$(this).dialog("close");$('input').prop('checked',!1)}}}
custom_alert("DELETE MESSAGE","Are you sure you want to delete these documents?",buttons)}
function custom_cancel(){buttons={"YES":{class:'yes_button',click:function()
{$(this).dialog("close");closeTab(configJs.current.url)}},"NO":{class:'no_button',click:function()
{$(this).dialog("close")}}}
custom_alert("CANCEL MESSAGE","Are you sure you want to leave this page?",buttons)}
function shareBySubmit(element,model){if(submitted==!1){submitted=!0;var dialog=$('.popup_list'),send='model='+model;if(!$(element).hasClass('shareby_button')){send+='&'+$(element).parents('.shareby_fieldset').serialize();$('.loader').show()}
else{dialog.removeClass('popup_shareby').hide().html('')}
var action_buttons={"Ok":{class:'ok_button',click:function()
{$(this).dialog("close")}}}
$.ajax({type:"POST",url:$(element).attr('href'),dataType:"json",data:send,success:function(data){if(data){submitted=!1;if(dialog.find('.loader').length){$('.loader').hide()}
if(data.status=="failure"){dialog.html(data.form);dialog.addClass('popup_shareby');if(data.file_found==0)
{custom_alert('ERROR MESSAGE','The file was not found on the server',action_buttons)}
dialog.show()}else{if(data.status=="success"){if(!data.not_sent_to)
{dialog.fadeOut(100);dialog.html('')}
else{custom_alert('ERROR MESSAGE','The email was not sent to '+data.not_sent_to,action_buttons)}}else{custom_alert('ERROR MESSAGE','There was an error. Please try again!',action_buttons)}}}},error:function(){submitted=!1;if(dialog.find('.loader').length){$('.loader').hide()}
custom_alert('ERROR MESSAGE','There was an error. Please try again!',action_buttons)}})}}
function setWidth(){var len=$('.links ul li').size();$('.links ul li').each(function(key,val){var curr_width=$(this).width();if(key==0)to_left=key;idth+=curr_width;if(key==5&&len>5){$('.next').show();$('.prev').show()}
if(key>=5&&$(this).hasClass('selected')){var left=0;if(len-(key+1)>=2){left=(key-2)*170}else{if(len-(key+1)==1){left=(key-3)*170}else{left=(key-4)*170}}
$('.links ul').css('left',-left)}});$('.links ul').css('width',idth)}
function CheckOrUncheckInput(obj)
{var checkBoxDiv=$(obj).find('.input');var input=$(obj).find('input[type="checkbox"]');if(input.is(':not(:disabled)')&&!checkBoxDiv.hasClass('checkboxDisabled')){if(checkBoxDiv.hasClass('checked')){checkBoxDiv.removeClass('checked');input.prop('checked',!1)}
else{checkBoxDiv.addClass('checked');input.prop('checked',!0)}
if($(obj).hasClass('read')||$(obj).hasClass('write')){groupPermissions(obj)}}}
function groupPermissions(obj){var input=$(obj).find('input[type="checkbox"]');var name=input.attr('name');name=name.split('-');name=name[1];name=name.replace('[read]','').replace('[write]','').replace('[0]','').replace('[1]','').replace('[2]','');console.log(name);var class_name=input.attr('class');var divClass=$(obj).hasClass('read')?'read':($(obj).hasClass('write')?'write':'');if(input.is(':checked')){switch(divClass){case 'read':console.log('read');$('.p_tab').find('.read .parent_'+name).attr('disabled',!1);$('.p_tab').find('.read .parent_'+name).siblings('div.input').removeClass('checkboxDisabled');$('.p_tab').find('.read .parent_'+name).prop('checked',!0);$('.p_tab').find('.read .parent_'+name).siblings('div.input').addClass('checked');break;case 'write':console.log('write');$('.p_tab').find('.write .parent_'+name).attr('disabled',!1);$('.p_tab').find('.write .parent_'+name).siblings('div.input').removeClass('checkboxDisabled');$('.p_tab').find('.write .parent_'+name).prop('checked',!0);$('.p_tab').find('.write .parent_'+name).siblings('div.input').addClass('checked');break}}else{switch(divClass){case 'read':console.log('uncheck read');$('.p_tab').find('.read .parent_'+name).attr('disabled',!0);$('.p_tab').find('.read .parent_'+name).siblings('div.input').addClass('checkboxDisabled');$('.p_tab').find('.read .parent_'+name).attr('checked',!1);$('.p_tab').find('.read .parent_'+name).siblings('div.input').removeClass('checked');break;case 'write':console.log('uncheck write');$('.p_tab').find('.write .parent_'+name).attr('disabled',!0);$('.p_tab').find('.write .parent_'+name).siblings('div.input').addClass('checkboxDisabled');$('.p_tab').find('.write .parent_'+name).attr('checked',!1);$('.p_tab').find('.write .parent_'+name).siblings('div.input').removeClass('checked');break}}}
function initializeCheckboxes(obj)
{var checkBoxDiv=$(obj).find('div.input');var input=checkBoxDiv.siblings('input');if(input.is(':not(:disabled)')&&!checkBoxDiv.hasClass('checkboxDisabled')){if(input.is(':checked')){checkBoxDiv.addClass('checked')}else{checkBoxDiv.removeClass('checked')}}}
function chooseActions()
{if($('.action_list').css('display')=="none"){if($('#users-list').is(':visible')){$('#users-list').fadeOut(100);$('.deletInv').hide()}else{$('.action_list').show()}}
else{$('.action_list').fadeOut(100);$('.deletInv').hide()}
return!1}
function split(val){return val.split(/,\s*/)}
function extractLast(term){return split(term).pop()}
function showToolTip(element){var parent=$(element).parent();if(parent.find('u').is(':visible')){parent.find('.panel').show();height=parent.find('.cover').outerHeight();parent.find('.panel').css({'top':(-25-height)+'px'})}}
function showToolTaskTip(element){var parent=$(element).parent();if(parent.find('u').is(':visible')){parent.find('.panel_expenses').show();height=parent.find('.cover').outerHeight();parent.find('.panel_expenses').css({'top':(-25-height)+'px'})}}
function showToolNoteTip(element){var parent=$(element).parent();if(parent.find('u').is(':visible')){parent.find('.panelNotes').show();height=parent.find('.cover').outerHeight();parent.find('.panelNotes').css({'top':(-65-height)+'px'})}}
function showToolTipM(element){var parent=$(element).parent();if(parent.find('u').is(':visible')){parent.find('.panelM').show();height=parent.find('.coverM').outerHeight();parent.find('.panelM').css({'top':(-35-height)+'px'})}}
function showoffsetToolTipM(element){var parent=$(element).parent();if(parent.find('u').is(':visible')){parent.find('.paneloffset').show();height=parent.find('.coveroffset').outerHeight();parent.find('.paneloffset').css({'top':(-35-height)+'px'})}}
function hideToolTipM(element){$(element).parent().find('.panelM').fadeOut(100)}
function hideToolTipoffset(element){$(element).parent().find('.paneloffset').fadeOut(100)}
function showToolTipExpenses(element){var parent=$(element).parent();parent.find('.panel_expenses').show();height=parent.find('.cover').outerHeight();parent.find('.panel_expenses').css({'top':(-20-height)+'px'})}
function hideToolTip(element){$(element).parent().find('.panel').fadeOut(100)}
function hideToolTaskTip(element){$(element).parent().find('.panel_expenses').fadeOut(100)}
function hideToolNoteTip(element){$(element).parent().find('.panelNotes').fadeOut(100)}
function hideToolTipExpenses(element){$(element).parent().find('.panel_expenses').fadeOut(100)}
function createPickers(context){$(".datefield",context||document).datepicker({showAnim:'fadeIn',dateFormat:'dd/mm/yy'})}
function showErrors(errors)
{if(errors){var err="";$.each(errors,function(i,item){err+=item+"<br />"});var action_buttons={"Ok":{click:function()
{$(this).dialog("close")},class:'ok_button'}}
custom_alert('ERROR MESSAGE',err,action_buttons)}}
function number_format(number,decimals,dec_point,thousands_sep){var n=number,c=isNaN(decimals=Math.abs(decimals))?2:decimals;var d=dec_point==undefined?",":dec_point;var t=thousands_sep==undefined?".":thousands_sep,s=n<0?"-":"";var i=parseInt(n=Math.abs(+n||0).toFixed(c))+"",j=(j=i.length)>3?j%3:0;return s+(j?i.substr(0,j)+t:"")+i.substr(j).replace(/(\d{3})(?=\d)/g,"$1"+t)+(c?d+Math.abs(n-i).toFixed(c).slice(2):"")}
function rtrim(str,charlist){charlist=!charlist?' \\s\u00A0':(charlist+'').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g,'\\$1');var re=new RegExp('['+charlist+']+$','g');return(str+'').replace(re,'')}
function formatNumber(number,decimal_no,decimal_char,thousand_char)
{decimal_no=isNaN(decimal_no=Math.abs(decimal_no))?3:decimal_no;decimal_char=decimal_char==undefined?".":dec_point;thousand_char=thousand_char==undefined?",":thousand_char;number=parseFloat(number);return rtrim(rtrim(number_format(number,decimal_no,decimal_char,thousand_char),'0'),decimal_char)}
function setSelects(){$(".firstOption .text").each(function(index){var parent=$(this).parents('.dropdownWrap')})}
function getThisOption(othis){var parent=$(othis).parents('.dropdownWrap');parent.find('.firstOption .text').html($(othis).html());parent.find('.dropdownSelect .options').css({'height':'0px'});parent.find('.firstOption .hidden_select').val($(othis).attr('data-id'))}
function openDropDown(othis){closeDropDown("dropdownSelect");var parent=$(othis).parents('.dropdownSelect');if(parent.find('.options').css('height')=="0px"){parent.addClass('opn');parent.find('.options').css('width',parent.parents('.row').width());parent.find('.options').stop(!1).animate({'height':106+'px'},'700','easeInQuart',function(){if(parent.find(".scrollOptions").length>0){if(!parent.find(".scrollOptions").find(".mCustomScrollBox").length>0){parent.find(".scrollOptions").mCustomScrollbar({horizontalScroll:!0})}}})}
else{closeDropDown("dropdownSelect")}
closeDropDown("dropdownWrap")}
function closeDropDown(element){$(element).removeClass('opn');$(element).find('.options').stop(!1).animate({'height':0+'px'},'700','easeInQuart',function(){})}
function blurAutocomplete(event,element,hiddenElement,callback){var myInput=$(element);var hiddenInput=$(hiddenElement);var autocomplete=myInput.data("uiAutocomplete");var matcher=new RegExp("^"+$.ui.autocomplete.escapeRegex(myInput.val())+"$","i");$.each(autocomplete.options.source,function(i,row){var item=row;if(matcher.test(item.label||item.value||item)){autocomplete.selectedItem=item;console.log(item);return}});if(autocomplete.selectedItem){console.log(autocomplete.selectedItem);autocomplete._trigger("select",event,{item:autocomplete.selectedItem});hiddenInput.val(autocomplete.selectedItem.id)}else{myInput.val('');hiddenInput.val('')}
if(callback){callback()}}
$(document).ready(function(){$('.header .options .notification').click(function(event){$('.popups').hide();$('#popup1').show();$("#popup1 .container").mCustomScrollbar("destroy");$("#popup1 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$('.header .options .audit').click(function(event){$('.popups').hide();$('#popup10').show();$("#popup10 .container").mCustomScrollbar("destroy");$("#popup10 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$('.header .options .birthday').click(function(event){$('.popups').hide();$('#popup2').show();$("#popup2 .container").mCustomScrollbar("destroy");$("#popup2 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$('.header .options .events').click(function(event){$('.popups').hide();$('#popup3').show();$("#popup3 .container").mCustomScrollbar("destroy");$("#popup3 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$('.header .options .remarks').click(function(event){$('.popups').hide();$('#popup4').show();$("#popup4 .container").mCustomScrollbar("destroy");$("#popup4 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$('.header .options .visa').click(function(event){$('.popups').hide();$('#popup5').show();$("#popup5 .container").mCustomScrollbar("destroy");$("#popup5 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$('.header .options .tasks').click(function(event){$('.popups').hide();$('#popup6').show();$("#popup6 .container").mCustomScrollbar("destroy");$("#popup6 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$('.header .options .budget').click(function(event){$('.popups').hide();$('#popup7').show();$("#popup7 .container").mCustomScrollbar("destroy");$("#popup7 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$('.header .options .calendar').click(function(event){$('.popups').hide();$('#popup8').show();$("#popup8 .container").mCustomScrollbar("destroy");$("#popup8 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$('.header .options .calendar_new').click(function(event){$('.popups').hide();$('#popup9').show();$("#popup9 .container").mCustomScrollbar("destroy");$("#popup9 .container").mCustomScrollbar({scrollButtons:{enable:!0}});event.stopPropagation()});$(document).click(function(){if($('#popup1').is(':visible'))
{$('#popup1').hide()}
if($('#popup2').is(':visible'))
{$('#popup2').hide()}
if($('#popup3').is(':visible'))
{$('#popup3').hide()}
if($('#popup4').is(':visible'))
{$('#popup4').hide()}
if($('#popup5').is(':visible'))
{$('#popup5').hide()}
if($('#popup6').is(':visible'))
{$('#popup6').hide()}
if($('#popup7').is(':visible'))
{$('#popup7').hide()}
if($('#popup8').is(':visible'))
{$('#popup8').hide()}
if($('#popup9').is(':visible'))
{$('#popup9').hide()}
if($('#popup10').is(':visible'))
{$('#popup10').hide()}});$('#popup1, #popup2, #popup3, #popup4, #popup5, #popup6, #popup7, #popup8, #popup9,#popup10').click(function(event){event.stopPropagation()})});function openNotification(dis){dis.find('.notifWrapPlace').stop(!1).animate({'height':442+'px'},'700','easeInQuart')}
function closeNotification(dis){dis.find('.notifWrapPlace').stop(!1).animate({'height':0+'px'},'700','easeInQuart')}
$(document).ready(function(){$('#info .row').click(function(){$(this).find('.plus').toggleClass('minus');$(this).toggleClass('selected')});$('#info .bernardRow').click(function(){$('#info .row.bernard').toggle()});$('#info .elioRow').click(function(){$('#info .row.elio').toggle()});$('#info .johnRow').click(function(){$('#info .row.john').toggle()})});function addWidgetDashboard(element){var dashboard=$(element).attr('data-dashboard');var div=$(element).siblings('.bggroup');var select=div.find('.widgets_select');$.ajax({type:"POST",url:configJs.urls.baseUrl+'/widgets/getWidgetsOff',dataType:"json",data:{id:dashboard},success:function(data){if(data.options)
{select.html(data.options);div.show()}}})}
function deletewid(id){var board=$(".board[data-id="+id+"]").parents('.sortableDash');$(".sortableDash .board[data-id="+id+"]").animate({opacity:0},800,function(){$(this).remove();board.sortable('refresh');jQuery.ajax({type:"POST",url:configJs.urls.baseUrl+'/widgets/delete',dataType:'json',data:({id:id}),beforeSend:function(x){if(x&&x.overrideMimeType){x.overrideMimeType("application/j-son;charset=UTF-8")}},success:function(data){}})})}
function mareste(id){$("#u"+id).dialog().dialog('open');if($('#graph-pop-up-'+id).is(':visible')){changeMonthTopCustomer($(".status.sr_top_customer.colorRed").data("id"))}
if($('#graph-billability1').is(':visible')){if($(".status.month_billability.colorRed").data("id")==null)
{createGridBillability()}else{changeBillability($(".status.month_billability.colorRed").data("id"))}
if($(".status.resc_billability.colorRed").data("id")==null)
{createGridBillability()}else{changeResources($(".status.resc_billability.colorRed").data("id"))}}
if($('#graph-country-revenues1').is(':visible')){changeOldYear($(".status.sr_country_revenues.colorRed").data("id"))}
if($('#graph-sr-submitted-reason1').is(':visible')){changeSubmittedReason($(".status.sr_submitted_reas.colorRed").data("id"))}
if($('#graph-sr-average-resource1').is(':visible')){changeSrRec($(".status.sr_avg_rec.colorRed").data("id"))}
if($('#graph-rsr-average-resource1').is(':visible')){changeRsrRec($(".status.rsr_avg_rec.colorRed").data("id"))}
if($('#graph-resource-non-billable1').is(':visible')){changeResourceNonBillable($(".status.resource_non_billable.colorRed").data("id"))}
if($('#graph-resource-billable1').is(':visible')){changeResourceBillable($(".status.resource_billable.colorRed").data("id"))}
if($('#pieInvoiceAging1').is(':visible')){changeYearage()}
if($('#pieSrAging1').is(':visible')){changeSRage()}
if($('#graph-dso1').is(':visible')){changeYearsSubmitted2($(".status.dso_ind.colorRed").data("id"))}
if($('#graph-monthlypayment1').is(':visible')){changeMonthlyPayment($(".status.month_payment.colorRed").data("id"))}
if($('#graph-SrAvg1').is(':visible')){changesrAvg($(".status.Sr_Avg.colorRed").data("id"))}
if($('#graph-pendingpaymentbymonth1').is(':visible')){changependingpaymentbymonth($(".status.pending_month_payment.colorRed").data("id"))}
if($('#graph-most-active-projects1').is(':visible')){changeMonthProjects($(".status.active_projects.colorRed").data("id"));changeTopProjects($(".status.top_projects.colorRed").data("id"))}
if($('#graph-ea-disc1').is(':visible')){changeYearEas($(".status.ea_disc.colorRed").data("id"))}
if($('#pieSubmittedCustomer1').is(':visible')){changeYearsSubmitted($(".status.sr_submitted_cust.colorRed").data("id"))}
if($('#graphTopCustomer-11').is(':visible')){changeTopCust($(".status.status_customer_top.colorRed").data("id"))}
if($('#graph-sr-system-down1').is(':visible')){createGridStackSystemDown()}
if($('#pieChartContainerRsrcPrd1').is(':visible')){createGridRsrcPrd()}
if($('#graph-project-alerts1').is(':visible')){changeProjectAlerts()}
if($('#graph-sr-openseverity-customer1').is(':visible')){createGridopenseverityCustomer()}
if($('#graph-sr-openstatus-customer1').is(':visible')){createGridOpenStatusCustomer()}
if($('#pieChartContainerInvByPart1').is(':visible')){createGridInvByPart()}
if($('#pieChartContainerTime1').is(':visible'))
{if($(".status.sr_time.colorRed").data("id")==null)
{createGridTime()}else{changeTime($(".status.sr_time.colorRed").data("id"))}}
if($('#graph-rate1').is(':visible')){changeMonthrate($(".status.sr_rate.colorRed").data("id"))}
if($('#graph-agev1').is(':visible')){changeMonthagev($(".status.sr_agev.colorRed").data("id"))}
if($('#graph-support1').is(':visible')){changeMonthSupport($(".status.sr_support.colorRed").data("id"))}
if($('#graph-paymentbyresource1').is(':visible')){changePaymentByResource($(".status.payment_resource.colorRed").data("id"))}
if($('#graph-sr-submitted1').is(':visible')){changeMonthSubmitted($(".status.sr_submitted.colorRed").data("id"))}	
if($('#graph-rsrvssr1').is(':visible')){changeRsrVsSr($(".status.rsr_sr.colorRed").data("id"))}
if($('#graph-sr-customer1').is(':visible')){changeMonthCustomer($(".status.sr_customer.colorRed").data("id"));changeCustomer($(".status.nb_customer.colorRed").data("id"))}
if($('#graph-unsat-customer1').is(':visible')){changeMonthUnsatisfied($(".status.unsat_customer.colorRed").data("id"))}
if($('#graph-rsr-customer1').is(':visible')){changeTopRSR($(".status.rsr_customer.colorRed").data("id"))}
if($('#graph-rsr-permon1').is(':visible')){changePermonRSR($(".status.rsr_permon.colorRed").data("id"))}
if($('#graph-sr-close-resource1').is(':visible')){if($(".status.sr_closeRes.colorRed").data("id")!=null)
changeMonthCloseRes($(".status.sr_closeRes.colorRed").data("id"))}
if($('#graph-project-alerts1').is(':visible')){changeProjectAlerts()}
if($('#graph-sr1').is(':visible')){changeMonthClose($(".status.sr_close.colorRed").data("id"))}
if($('#graph-soldBy-revenues1').is(':visible')){changeSoldByYear($(".status.sr_soldBy_revenues.colorRed").data("id"))}
if($('#graph-ea-revenues1').is(':visible')){changeEAYear($(".status.sr_eaTypes_revenues.colorRed").data("id"))}
if($('#graph-11').is(':visible')){if($(".status.status_eas.colorRed").data("id")==null)
{createGridCustomer1()}else{change($(".status.status_eas.colorRed").data("id"))}}
if($('#graphCustomer-11').is(':visible')){if($(".status.status_customer_top.colorRed").data("id")==null){createGridTime1()}else if($(".status.status_customer_top.colorRed").data("id")==5||$(".status.status_customer_top.colorRed").data("id")==10||$(".status.status_customer_top.colorRed").data("id")==20){changeTopC($(".status.status_customer_top.colorRed").data("id"))}else{changeYear($(".status.status_customer_top.colorRed").data("id"))}}
if($('#graphCustomer-11').is(':visible')){changeTopC($(".status.status_customer_top.colorRed").data("id"))}
if($('#graph-actuals2').is(':visible')){if($(".status.status_actuals.colorRed").data("id")==null&&$(".status.type_actuals.colorRed").data("id")==null){createGridActuals()}else{if($(".status.status_actuals.colorRed").data("id")==0||$(".status.status_actuals.colorRed").data("id")==1||$(".status.status_actuals.colorRed").data("id")==2||$(".status.status_actuals.colorRed").data("id")==3){changeStatusActuals($(".status.status_actuals.colorRed").data("id"))}
if($(".status.type_actuals.colorRed").data("id")==26||$(".status.status_actuals.colorRed").data("id")==27||$(".status.status_actuals.colorRed").data("id")==28){changeStatusActuals($(".status.type_actuals.colorRed").data("id"))}}}

if($('#graph-maintprofit2').is(':visible')){if($(".status.year_maintp.colorRed").data("id")==null){createGridMaintProfit(); }else{ changeYearMaintP($(".status.year_maintp.colorRed").data("id")); }}

if($('#graph-country-pending-payments1').is(':visible')){if($(".status.status_country_pending_payments.colorRed").data("id")==null&&$(".status.status_pending_payments_amount.colorRed").data("id")==null){createGridCountryPayments()}else{if($(".status.status_country_pending_payments.colorRed").data("id")==1||$(".status.status_country_pending_payments.colorRed").data("id")==2||$(".status.status_country_pending_payments.colorRed").data("id")==3||$(".status.status_country_pending_payments.colorRed").data("id")==4){changeMonth($(".status.status_country_pending_payments.colorRed").data("id"))}
if($(".status.status_pending_payments_amount.colorRed").data("id")==1||$(".status.status_pending_payments_amount.colorRed").data("id")==2||$(".status.status_pending_payments_amount.colorRed").data("id")==3||$(".status.status_pending_payments_amount.colorRed").data("id")==4){changeAmount($(".status.status_pending_payments_amount.colorRed").data("id"))}}}
if($('#graph-customer-satisfaction2').is(':visible')){if($(".status.type_cs.colorRed").data("id")==100)
{createGridCustomerSatisfaction();changeTypeCS($(".status.type_cs.colorRed").data("id"))}else if($(".status.type_cs.colorRed").data("id")==50)
{createGridCustomerSatisfaction();showTrendDown($(".status.type_cs.colorRed").data("id"))}
else{createGridCustomerSatisfaction()}}
if($('#graph-support-performance1').is(':visible')){if($(".status.status_sp.colorRed").data("id")==null&&$(".status.type_sp.colorRed").data("id")==null){createGridSupportPerformance()}else{if($(".status.status_sp.colorRed").data("id")==0||$(".status.type_sp.colorRed").data("id")==1||$(".status.status_sp.colorRed").data("id")==2||$(".status.status_sp.colorRed").data("id")==3){changeStatusSupportPerformance($(".status.status_sp.colorRed").data("id"))}
if($(".status.status_sp.colorRed").data("id")==26||$(".status.status_sp.colorRed").data("id")==27||$(".status.status_sp.colorRed").data("id")==28){changeStatusSupportPerformance($(".status.type_sp.colorRed").data("id"))}}}
$("div.bcontenu.z-index").parent().css("z-index","1009");$("#graph-customer-profile").css("z-index","1050")}(function($){$.fn.editable=function(target,options){if('disable'==target){$(this).data('disabled.editable',!0);return}
if('enable'==target){$(this).data('disabled.editable',!1);return}
if('destroy'==target){$(this).unbind($(this).data('event.editable')).removeData('disabled.editable').removeData('event.editable');return}
var settings=$.extend({},$.fn.editable.defaults,{target:target},options);var plugin=$.editable.types[settings.type].plugin||function(){};var submit=$.editable.types[settings.type].submit||function(){};var buttons=$.editable.types[settings.type].buttons||$.editable.types.defaults.buttons;var content=$.editable.types[settings.type].content||$.editable.types.defaults.content;var element=$.editable.types[settings.type].element||$.editable.types.defaults.element;var reset=$.editable.types[settings.type].reset||$.editable.types.defaults.reset;var callback=settings.callback||function(){};var onedit=settings.onedit||function(){};var onsubmit=settings.onsubmit||function(){};var onreset=settings.onreset||function(){};var onerror=settings.onerror||reset;if(settings.tooltip){$(this).attr('title',settings.tooltip)}
settings.autowidth='auto'==settings.width;settings.autoheight='auto'==settings.height;return this.each(function(){var self=this;var savedwidth=$(self).width();var savedheight=$(self).height();$(this).data('event.editable',settings.event);if(!$.trim($(this).html())){$(this).html(settings.placeholder)}
$(this).bind(settings.event,function(e){if(!0===$(this).data('disabled.editable')){return}
if(self.editing){return}
if(!1===onedit.apply(this,[settings,self])){return}
e.preventDefault();e.stopPropagation();if(settings.tooltip){$(self).removeAttr('title')}
if(0==$(self).width()){settings.width=savedwidth;settings.height=savedheight}else{if(settings.width!='none'){settings.width=settings.autowidth?$(self).width():settings.width}
if(settings.height!='none'){settings.height=settings.autoheight?$(self).height():settings.height}}
if($(this).html().toLowerCase().replace(/(;|"|\/)/g,'')==settings.placeholder.toLowerCase().replace(/(;|"|\/)/g,'')){$(this).html('')}
self.editing=!0;self.revert=$(self).html();$(self).html('');var form=$('<form />');if(settings.cssclass){if('inherit'==settings.cssclass){form.attr('class',$(self).attr('class'))}else{form.attr('class',settings.cssclass)}}
if(settings.style){if('inherit'==settings.style){form.attr('style',$(self).attr('style'));form.css('display',$(self).css('display'))}else{form.attr('style',settings.style)}}
var input=element.apply(form,[settings,self]);var input_content;if(settings.loadurl){var t=setTimeout(function(){input.disabled=!0;content.apply(form,[settings.loadtext,settings,self])},100);var loaddata={};loaddata[settings.id]=self.id;if($.isFunction(settings.loaddata)){$.extend(loaddata,settings.loaddata.apply(self,[self.revert,settings]))}else{$.extend(loaddata,settings.loaddata)}
$.ajax({type:settings.loadtype,url:settings.loadurl,data:loaddata,async:!1,success:function(result){window.clearTimeout(t);input_content=result;input.disabled=!1}})}else if(settings.data){input_content=settings.data;if($.isFunction(settings.data)){input_content=settings.data.apply(self,[self.revert,settings])}}else{input_content=self.revert}
content.apply(form,[input_content,settings,self]);input.attr('name',settings.name);buttons.apply(form,[settings,self]);$(self).append(form);plugin.apply(form,[settings,self]);$(':input:visible:enabled:first',form).focus();if(settings.select){input.select()}
input.keydown(function(e){if(e.keyCode==27){e.preventDefault();reset.apply(form,[settings,self])}});var t;if('cancel'==settings.onblur){input.blur(function(e){t=setTimeout(function(){reset.apply(form,[settings,self])},500)})}else if('submit'==settings.onblur){input.blur(function(e){t=setTimeout(function(){form.submit()},200)})}else if($.isFunction(settings.onblur)){input.blur(function(e){settings.onblur.apply(self,[input.val(),settings])})}else{input.blur(function(e){})}
form.submit(function(e){if(t){clearTimeout(t)}
e.preventDefault();if(!1!==onsubmit.apply(form,[settings,self])){if(!1!==submit.apply(form,[settings,self])){if($.isFunction(settings.target)){var str=settings.target.apply(self,[input.val(),settings]);$(self).html(str);self.editing=!1;callback.apply(self,[self.innerHTML,settings]);if(!$.trim($(self).html())){$(self).html(settings.placeholder)}}else{var submitdata={};submitdata[settings.name]=input.val();submitdata[settings.id]=self.id;if($.isFunction(settings.submitdata)){$.extend(submitdata,settings.submitdata.apply(self,[self.revert,settings]))}else{$.extend(submitdata,settings.submitdata)}
if('PUT'==settings.method){submitdata._method='put'}
$(self).html(settings.indicator);var ajaxoptions={type:'POST',data:submitdata,dataType:'html',url:settings.target,success:function(result,status){if(ajaxoptions.dataType=='html'){$(self).html(result)}
self.editing=!1;callback.apply(self,[result,settings]);if(!$.trim($(self).html())){$(self).html(settings.placeholder)}},error:function(xhr,status,error){onerror.apply(form,[settings,self,xhr])}};$.extend(ajaxoptions,settings.ajaxoptions);$.ajax(ajaxoptions)}}}
$(self).attr('title',settings.tooltip);return!1})});this.reset=function(form){if(this.editing){if(!1!==onreset.apply(form,[settings,self])){$(self).html(self.revert);self.editing=!1;if(!$.trim($(self).html())){$(self).html(settings.placeholder)}
if(settings.tooltip){$(self).attr('title',settings.tooltip)}}}}})};$.editable={types:{defaults:{element:function(settings,original){var input=$('<input type="hidden"></input>');$(this).append(input);return(input)},content:function(string,settings,original){$(':input:first',this).val(string)},reset:function(settings,original){original.reset(this)},buttons:function(settings,original){var form=this;if(settings.submit){if(settings.submit.match(/>$/)){var submit=$(settings.submit).click(function(){if(submit.attr("type")!="submit"){form.submit()}})}else{var submit=$('<button type="submit" />');submit.html(settings.submit)}
$(this).append(submit)}
if(settings.cancel){if(settings.cancel.match(/>$/)){var cancel=$(settings.cancel)}else{var cancel=$('<button type="cancel" />');cancel.html(settings.cancel)}
$(this).append(cancel);$(cancel).click(function(event){if($.isFunction($.editable.types[settings.type].reset)){var reset=$.editable.types[settings.type].reset}else{var reset=$.editable.types.defaults.reset}
reset.apply(form,[settings,original]);return!1})}}},text:{element:function(settings,original){var input=$('<input />');if(settings.width!='none'){input.attr('width',settings.width)}
if(settings.height!='none'){input.attr('height',settings.height)}
input.attr('autocomplete','off');$(this).append(input);return(input)}},textarea:{element:function(settings,original){var textarea=$('<textarea />');if(settings.rows){textarea.attr('rows',settings.rows)}else if(settings.height!="none"){textarea.height(settings.height)}
if(settings.cols){textarea.attr('cols',settings.cols)}else if(settings.width!="none"){textarea.width(settings.width)}
$(this).append(textarea);return(textarea)}},select:{element:function(settings,original){var select=$('<select />');$(this).append(select);return(select)},content:function(data,settings,original){if(String==data.constructor){eval('var json = '+data)}else{var json=data}
for(var key in json){if(!json.hasOwnProperty(key)){continue}
if('selected'==key){continue}
var option=$('<option />').val(key).append(json[key]);$('select',this).append(option)}
$('select',this).children().each(function(){if($(this).val()==json.selected||$(this).text()==$.trim(original.revert)){$(this).attr('selected','selected')}});if(!settings.submit){var form=this;$('select',this).change(function(){form.submit()})}}}},addInputType:function(name,input){$.editable.types[name]=input}};$.fn.editable.defaults={name:'value',id:'id',type:'text',width:'auto',height:'auto',event:'click.editable',onblur:'cancel',loadtype:'GET',loadtext:'Loading...',placeholder:'Click to edit',loaddata:{},submitdata:{},ajaxoptions:{}}})(jQuery);function checkContainer32(){if($('#pieEasDiscount').is(':visible')){createGrideasdisc()}else{setTimeout(checkContainer32,5)}}
function checkContainer1(){if($('#pieSubmittedCustomer').is(':visible')){createGridCustomersub()}else{setTimeout(checkContainer1,5)}}
function checkContainer2(){if($('#pieChartContainerTime').is(':visible')){createGridTime()}else{setTimeout(checkContainer2,5)}}
function checkContainer3(){if($('#graph-sr-top-customer').is(':visible')){createGridTopCustomer()}else{setTimeout(checkContainer3,5)}}
function checkContainer4(){if($('#graph-sr-customer').is(':visible')){createGridSrCustomer()}else{setTimeout(checkContainer4,5)}}
function checkContainer5(){if($('#graph-soldBy-revenues').is(':visible')){createGridSoldRevenues()}else{setTimeout(checkContainer5,5)}}
function checkContainer6(){if($('#graph-ea-revenues').is(':visible')){createGridTypeRevenues()}else{setTimeout(checkContainer6,5)}}
function checkContainer7(){if($('#graph-country-revenues').is(':visible')){createGridCountry()}else{setTimeout(checkContainer7,5)}}
function checkContainer8(){if($('#graphCustomer-1').is(':visible')){createGridTime1()}else{setTimeout(checkContainer8,5)}}
function checkContainer22(){if($('#graphTest-1').is(':visible')){createGridTest1()}else{setTimeout(checkContainer22,5)}}
function checkContainer9(){if($('#graph-sr').is(':visible')){createGridStack()}else{setTimeout(checkContainer9,5)}}
function checkContainer10(){if($('#chartContainerMaintenance').is(':visible')){Maintenance1()}else{setTimeout(checkContainer10,5)}}
function checkContainer45(){if($('#graph-agev').is(':visible')){createGridAvgAge()}else{setTimeout(checkContainer45,5)}}
function checkContainer11(){if($('#graph-support').is(':visible')){createGridSupport()}else{setTimeout(checkContainer11,5)}}
function checkContainer12(){if($('#chartContainerLine').is(':visible')){Maintenance2()}else{setTimeout(checkContainer12,5)}}
function checkContainer13(){if($('#graph-sr-submitted').is(':visible')){createGridSubMonth()}else{setTimeout(checkContainer13,5)}}
function checkContainer49(){if($('#graph-rsrvssr').is(':visible')){createGridRsrVsSr()}else{setTimeout(checkContainer49,5)}}
function checkContainer14(){if($('#graph-sr-submitted-reason').is(':visible')){CreateGridSubReason()}else{setTimeout(checkContainer14,5)}}
function checkContainer15(){if($('#graph-1').is(':visible')){createGridCustomer1()}else{setTimeout(checkContainer15,5)}}
function checkContainer16(){if($('#graph-billability').is(':visible')){createGridBillability()}else{setTimeout(checkContainer16,5)}}
function checkContainer17(){if($('#graph-sr-system-down').is(':visible')){createGridStackSystemDown()}else{setTimeout(checkContainer17,5)}}
function checkContainer18(){if($('#graph-sr-openstatus-customer').is(':visible')){createGridOpenStatusCustomer()}else{setTimeout(checkContainer18,5)}}
function checkContainer19(){if($('#graph-sr-openseverity-customer').is(':visible')){createGridopenseverityCustomer()}else{setTimeout(checkContainer19,5)}}
function checkContainer20(){if($('#graph-submittedresolved').is(':visible')){createGridsubmittedresolved()}else{setTimeout(checkContainer20,5)}}
function checkContainer21(){if($('#graph-actuals').is(':visible')){createGridActuals()}else{setTimeout(checkContainer21,5)}}
function checkContainer46(){if($('#graph-maintprofit').is(':visible')){createGridMaintProfit(); }else{ setTimeout(checkContainer46,5)}}
function checkContainer23(){if($('#graph-sr-close-resource').is(':visible')){changeMonthCloseRes(3)}else{setTimeout(checkContainer23,5)}}
function checkContainer24(){if($('#graph-project-alerts').is(':visible')){changeProjectAlerts()}else{setTimeout(checkContainer24,5)}}
function checkContainer25(){if($('#graph-resource-non-billable').is(':visible')){CreateGridRescNonBill()}else{setTimeout(checkContainer25,5)}}
function checkContainer26(){if($('#graph-resource-billable').is(':visible')){CreateGridRescBill()}else{setTimeout(checkContainer26,5)}}
function checkContainer27(){if($('#graph-most-active-projects').is(':visible')){CreateGridMostActiveProjects()}else{setTimeout(checkContainer27,5)}}
function checkContainer28(){if($('#graph-support-performance').is(':visible')){createGridSupportPerformance()}else{setTimeout(checkContainer28,5)}}
function checkContainer29(){if($('#graph-customer-satisfaction').is(':visible')){createGridCustomerSatisfaction()}else{setTimeout(checkContainer29,5)}}
function checkContainer30(){if($('#graph-unsat-customer').is(':visible')){createGridUnsatCustomer()}else{setTimeout(checkContainer30,5)}}
function checkContainer47(){if($('#graph-rsr-customer').is(':visible')){createGridRSRCustomer()}else{setTimeout(checkContainer47,5)}}
function checkContainer50(){if($('#graph-rsr-permon').is(':visible')){createGridRsrMonth()}else{setTimeout(checkContainer50,5)}}
function checkContainer31(){if($('#graph-sr-average-resource').is(':visible')){CreateGridAvgResource()}else{setTimeout(checkContainer31,5)}}
function checkContainer48(){if($('#graph-rsr-average-resource').is(':visible')){CreateGridAvgResourceRsr()}else{setTimeout(checkContainer48,5)}}
function checkContainer33(){if($('#graph-rate').is(':visible')){createGridRate()}else{setTimeout(checkContainer33,5)}}
function checkContainer34(){if($('#pieInvoiceAging').is(':visible')){createGridInvAgedisc()}else{setTimeout(checkContainer34,5)}}
function checkContainer44(){if($('#pieSrAging').is(':visible')){createGridSrAgedisc()}else{setTimeout(checkContainer44,5)}}
function checkContainer35(){if($('#pieChartContainerRsrcPrd').is(':visible')){createGridRsrcPrd()}else{setTimeout(checkContainer35,5)}}
function checkContainer36(){if($('#graph-monthlypayment').is(':visible')){createGridMonthlyPayment()}else{setTimeout(checkContainer36,5)}}
function checkContainer43(){if($('#graph-SrAvg').is(':visible')){createGridsrAvg()}else{setTimeout(checkContainer43,5)}}
function checkContainer37(){if($('#pieChartContainerInvByPart').is(':visible')){createGridInvByPart()}else{setTimeout(checkContainer37,5)}}
function checkContainer38(){if($('#graphTopCustomer-1').is(':visible')){createGridTopCustomer1()}else{setTimeout(checkContainer38,5)}}
function checkContainer39(){if($('#graph-paymentbyresource').is(':visible')){createGridPaymentByResource()}else{setTimeout(checkContainer39,5)}}
function checkContainer40(){if($('#pieDSOIndex').is(':visible')){createGridDSOIndex()}else{setTimeout(checkContainer40,5)}}
function checkContainer41(){if($('#graph-country-pending-payments').is(':visible')){createGridCountryPayments()}else{setTimeout(checkContainer41,5)}}
function checkContainer42(){if($('#graph-pendingpaymentbymonth').is(':visible')){createGridPendingInvoicesByMonth()}else{setTimeout(checkContainer42,5)}}
function drowChart(pieChartDataSource,id){$("#"+id).dxPieChart({dataSource:pieChartDataSource,legend:{orientation:"horizontal",itemTextPosition:"right",horizontalAlignment:"center",verticalAlignment:"bottom",rowCount:5,},series:{argumentField:'category',valueField:'value',},tooltip:{enabled:!0,customizeText:function(){return numberWithCommas(this.valueText)}}})}
function numberWithCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")}
function closeDialog(id){$("#u"+id).dialog("close")}
$(document).ready(function(){$('.userData .add').click(function(){$(this).hide();$('.taskAdd').fadeIn()});$('.userData .qadd').click(function(){$(this).hide();$('.qualAdd').fadeIn()});$('.userData .cancel').click(function(){$('.userData .add').fadeIn();$('.taskAdd').hide()});$('.userData .qcancel').click(function(){$('.userData .qadd').fadeIn();$('.qualAdd').hide()})});$(document).ready(function(){$('.ratePop .close').click(function(){$('.popOpacity').hide()});$('#wrapper').click(function(){if($('.ratePop').is(':visible')){$('.popOpacity').hide()}});$('.ratePop').click(function(event){event.stopPropagation()})});$(document).ready(function(){$('.ratePop .rating .star').mouseenter(function(){$('.ratePop .rating .star').removeClass('selected');$(this).addClass('selected');$(this).prevAll().addClass('selected')})})