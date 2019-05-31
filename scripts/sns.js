// JavaScript Document
var boolEsc = false;
$(document).keydown(function (event) {

    if (event.which == 27) { 
	closeNotification();
    }

    if ($('.ratePop').is(':visible')) {
        $('.popOpacity').hide();
    }
 

});



function showPanel(obj){
	$(obj).parent().find('.panel').show();
	$('.tabs').css('z-index','2');
	}
	
	function hidePanel(obj){
	$(obj).parent().find('.panel').hide();
	}
	function CheckOrUncheckInput(obj)
	{
		if($(obj).find('.input').hasClass('checked'))
		$(obj).find('.input').removeClass('checked');
		else
		$(obj).find('.input').addClass('checked');
	}
	$(document).ready(function(){
	
		if($('.scrollNotification').length > 0 ){
			if(!$('.scrollNotification').find('.mCustomScrollbar').length > 0 ){
				$('.scrollNotification').mCustomScrollbar();
			}
		}
	if($('.tree').length!=0)
	{
		if($('.viewer').outerHeight(true)>$('.tree').height())
		$('.tree').find('.bg').css('height',$('.viewer').outerHeight(true));
	}
	if($('.expensesection').length!=0 && $('.expapprovalpage').length==0)
	{
		$('.row').click(function(){
			if($(this).find('.firstdivision').find('input').length!=0)
			window.location.href='expenseSheet.html';
		});
	}
	
	if($('.expensesection').length!=0 && $('.expapprovalpage').length!=0)
	{
		$('.row').click(function(){
			if($(this).find('.firstdivision').find('input').length!=0)
			window.location.href='expenseSheetApprovalID.html';
		});
	}
	if($('.approvalpage').length!=0)
	{
		$('.row').click(function(){
			if($(this).find('.firstdivision').find('input').length!=0)
			window.location.href='timeSheetApprovalID.html';
		});
	}
	if($('.timesheetsection').length!=0 && $('.approvalpage').length==0)
	{
		$('.row').click(function(){
			if($(this).find('.firstdivision').find('input').length!=0)
			window.location.href='timeSheetID.html';
		});
	}
	if($('.timesheetidsection').length!=0)
	{/*
		$('.row').click(function(){
			window.location.href='timeSheetApproval.html';
		});*/
		
		$('.flag').click(function(){
			$('.tabs').css('zIndex','2');
			$('.info').css('zIndex','4')
			$(this).find('.panel').show();
			$('.listofactions ').hide();
		});
		$('.row').find('.item').each(function(){
		if(!$(this).hasClass('flag'))
		{
			$(this).find('input').focus(function(){
				  if(!$(this).parent().hasClass('commentstyle'))
				 $(this).parent().addClass('focusstyle');
			})
		}
		
		});

	}
		
	$('#menu').hover(function(){
		$('#content').addClass('zIndex');
	},function(){
		$('#content').removeClass('zIndex');
		
	});	
	$('body').click(function (e) {
            if (!$(e.target).closest('.dropdownProdCatWrap').length 
			&& !$(e.target).closest('.delete').length 
			&& !$(e.target).closest('.popupDelete').length 
			&& !$(e.target).closest('.letter').length 
			&& !$(e.target).closest('.popLetter').length 
			&& !$(e.target).closest('.panel').length 
			&& !$(e.target).closest('u').length 
			&& !$(e.target).closest('.impdiv').length
			&& !$(e.target).closest('.assdiv').length
			&& !$(e.target).closest('.flag').length 
			&& !$(e.target).closest('.notificationWraping').length 
			&& !$(e.target).closest('.li').length) {
				if($('.panel').length!=0)
				{
                if ($('.panel').css("display") == "block") {
                    $('.panel').fadeOut(1000);
                }
				if ($('.listofactions').css("display") == "block") {
                    $('.listofactions').fadeOut(1000);
                }
				if ($('.importTasks').css("display") == "block" && !$(e.target).closest('.importTasks').length) {
                    $('.importTasks').fadeOut(1000);
                }
				}
				$('.popLetter').fadeOut();
				if($('.popupDelete').length!=0)
				{
					closeMessage();
				}
				if($('.neweasection').length!=0)
				closeDropProdCat();
				
				if($('.notificationWraping').length!=0)
				closeNotification();
            }
      });
	});


function addTask(obj){
	if($(obj).parent().next().css('display')=="none")
	{
	$(obj).parent().hide();
	$(obj).parent().next().slideToggle();
	}
}
function hideTask(obj){
	if($(obj).parent().prev().css('display')=="none")
	{
	$(obj).parent().hide();
	$(obj).parent().prev().slideToggle();
	}
}

function chooseActions()
{
	$('.importTasks').hide();
	$('.assignedtasks').hide();
	$('#groupMenu').hide();
	$('.listofactions .li .whiteBg').hide();
	if($('.listofactions').css('display')=="none")
	$('.listofactions').slideToggle();
	if($('.timesheetidsection').length!=0)
	{
		$('.grid').find('.row').css('zIndex','1');
		$('.grid').find('.item').css('zIndex','1');
		$('.commentstyle').removeClass('commentstyle');
		$('.grouppermissions').css('zIndex','1');
		$('.panel').hide();
		
	}
		if ($(".scroll2").length > 0) {
			if (!$(".scroll2").find(".mCustomScrollBox").length > 0) {
			  $(".scroll2").mCustomScrollbar();
			 }
			}

}

function importPhase(){
	$('.listofactions').hide();
	$('.importTasks').slideToggle();
}

function grpMenu(){
$('#groupMenu').filter(':not(:animated)').slideToggle();
$('.listofactions .li.group').toggleClass('selected2');
$('.listofactions .whiteBg').toggle();
}

function assignTasks(){
	$('.importTasks').hide();
	$('.listofactions').hide();
	if($('.assignedtasks').css('display')=="none")
	$('.assignedtasks').slideToggle();
}	

function addComment(obj){
	$('.row').find('.commentstyle').removeClass('commentstyle');
	$(obj).parent().removeClass('focusstyle');
	$(obj).parent().unbind('focus');
	$('.row').css('zIndex','1');
	$(obj).parent().parent().css('zIndex','2');
	$('.item').css('zIndex','1')
	$(obj).parent().css('zIndex','10');
	$(obj).parent().addClass('commentstyle');
	}
	
function undoFunction(obj){
	$(obj).parent().parent().parent().removeClass('commentstyle');
	$(obj).parent().parent().parent().find('textarea').attr("value","");
	}
	
	function editHeader(){
		$('.theme').find('.edit').hide();
		$('.theme').find('.cancel').show();
		$('.theme').find('.save').show();
		$('.firstpart').hide();
		$('.secondpart').show();
	}
	
	
	function editWidget(obj)
	{
		if($(obj).parent().parent().find('.editwidget').css("display")=="none")
		{
		$(obj).addClass('selected');
		$(obj).parent().parent().find('.editwidget').show();
		}
	}
	
	function cancelEditTitle(){
		$('.editwidget').hide();
		$('.editpencil').removeClass('selected');
	}
	function addWidgetDashboard(){
		$('.bggroup').show();
		}
		var weekIndex=0;
		function nextWeek(){
			var weeks=new Array("WEEK1","WEEK2","WEEK3","WEEK4");
			weekIndex++;
			if(weekIndex==weeks.length)
			weekIndex=0;
			$('.tweek').find('input').attr('value',weeks[weekIndex]);
		}
		function prevWeek(){
			var weeks=new Array("WEEK1","WEEK2","WEEK3","WEEK4");
			weekIndex--;
			if(weekIndex==-1)
			weekIndex=weeks.length-1;
			$('.tweek').find('input').attr('value',weeks[weekIndex]);
		}	
		
		var monthIndex=0;
		function nextMonth(){
			var month=new Array("JANUARY","FEBRUARY","MARCH","APRIL","MAY","JUNE","JULY","AUGUST","SEPTEMBER","OCTOBER","NOVEMBER","DECEMBER");
			monthIndex++;
			if(monthIndex==month.length)
			monthIndex=0;
			$('.tmonth').find('input').attr('value',month[monthIndex]);
		}
		function prevMonth(){
			var month=new Array("JANUARY","FEBRUARY","MARCH","APRIL","MAY","JUNE","JULY","AUGUST","SEPTEMBER","OCTOBER","NOVEMBER","DECEMBER");
			monthIndex--;
			if(monthIndex==-1)
			monthIndex=month.length-1;
			$('.tmonth').find('input').attr('value',month[monthIndex]);
		}	
			
		
		
		function nextYear(){
			var currentYear=parseInt($('.tyear').find('input').attr('value'));
			currentYear++;
			$('.tyear').find('input').attr('value',currentYear);
		}
		function prevYear(){
			var currentYear=parseInt($('.tyear').find('input').attr('value'));
			currentYear--;
			$('.tyear').find('input').attr('value',currentYear);
		}		
		
		
		function approve(){
			$('.reject').removeClass('selected');
			$('.approve').removeClass('notselected');
			$('.saveDiv1').hide();
		}
		function reject(){
			$('.approve').addClass('notselected');
			$('.reject').addClass('selected');
			$('.saveDiv1').show();
		}
		
		function openLetter(obj)
		{
			$(obj).parent().parent().find('.popLetter').fadeIn();
			
			
		}
		
		function deleteProject()
		{
			$('.grayshadow').show();
			$('.popDelete').fadeIn(1000);
		}
		
		
		function checkUncheckInput(obj){
			if($(obj).find('input').is(':checked'))
			{
				$(obj).find('input').removeAttr('checked');
				$(obj).removeClass('selected');
			}
			else
			{
				$(obj).find('input').attr('checked','checked');
				$(obj).addClass('selected');
			
			}
			
		}
		
		function editDash(obj)
		{
			$(obj).parent().parent().parent().find('.bg1').show();
			$(obj).parent().hide();
			$(obj).parent().parent().find('.input_text_desc').html("EDIT "+$(obj).parent().parent().find('.input_text_desc').text());
			$(obj).parent().next().show();
		}
		
		function redoEdit(obj)
		{
			$(obj).parent().hide();
			$(obj).parent().parent().parent().find('.bg1').hide();
			$(obj).parent().parent().find('.input_text_desc').html($(obj).parent().parent().find('.input_text_desc').text().replace("EDIT ",""));
			$(obj).parent().prev().show();
		}
		
		
		function closeMessage(){
			$('.popDelete').hide();
			$('.grayshadow').fadeOut();
			}
			
			
			function moreGraphs(){
				$('.graph_div').find('img').show();
			}
			
			function hideGraphs(){
				if($('.descgraph').hasClass('hidden'))
				{
				  $('.descgraph').removeClass('hidden');
				  $('.graph_div').hide();
				}
				else
				{
					$('.descgraph').addClass('hidden');
				 	$('.graph_div').show();
				}
				}
				
				


function getThisOption(othis) {
$(othis).parents('.dropdownProdCatWrap').find('.firstOption .text').html($(othis).html());
$(othis).parents('.dropdownProdCatWrap').find('.dropdownProdCat .options').css({ 'height': '0px' });
}

function opDdlProdCat(othis) {
closeDropProdCat();
if ($(othis).parents('.dropdownProdCat').find('.options').css('height') != "106px") {
$(othis).parents('.dropdownProdCat').addClass('opn');
$(othis).parents('.dropdownProdCat').find('.options').stop(false).animate({ 'height': 106 + 'px' }, '700', 'easeInQuart', function () {

if ($(othis).parents('.dropdownProdCat').find(".scrollProduct").length > 0) {
if (!$(othis).parents('.dropdownProdCat').find(".scrollProduct").find(".mCustomScrollBox").length > 0) {
$(othis).parents('.dropdownProdCat').find(".scrollProduct").mCustomScrollbar();
}
}
});
}
else {
closeDropProdCat();
}
closeDrop();
}

function closeDrop() {
$('.dropDownProductsWrap').removeClass('opn');
$('.dropDownProductsWrap').find('.options').stop(false).animate({ 'height': 0 + 'px' }, '700', 'easeInQuart', function () { });
$('.dropDownProducts').animate({ 'width': '50px' }, '700', 'easeInQuart', function () {
});
}

function closeDropProdCat() {
$('.dropdownProdCat').removeClass('opn');
$('.dropdownProdCat').find('.options').stop(false).animate({ 'height': 0 + 'px' }, '700', 'easeInQuart', function () { });
}


function openNotification() {
	$('.notifWrapPlace').stop(false).animate({ 'height': 442 + 'px' }, '700', 'easeInQuart');
} 
function closeNotification() {
	$('.notifWrapPlace').stop(false).animate({ 'height': 0 + 'px' }, '700', 'easeInQuart');
} 




// open popups in header

$(document).ready(function(){
	$('#header .options .notification').click(function(event){
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
	
	$('#header .options .birthday').click(function(event){
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
	
		$('#header .options .events').click(function(event){
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
	
	$('#header .options .remarks').click(function(event){
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
	
	$('#header .options .visa').click(function(event){
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
	
	$('#header .options .tasks').click(function(event){
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
	
		$('#header .options .budget').click(function(event){
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
	
	$('#header .options .calendar').click(function(event){
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
	});
	
	$('#popup1, #popup2, #popup3, #popup4, #popup5, #popup6, #popup7, #popup8').click(function(event){
		event.stopPropagation();
	});
});


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



// user page, open add task pop
$(document).ready(function () {
    $('.userData .add').click(function () {
        $(this).hide();
        $('.taskAdd').fadeIn();
    });

    $('.userData .cancel').click(function () {
        $('.userData .add').fadeIn();
        $('.taskAdd').hide();
    });
});


// user page, open rate popup
$(document).ready(function () {
//    $('.userData .table2 .link').click(function (event) {
//        $("html, body").animate({ scrollTop: 0 });
//        $('.popOpacity').show();
//        event.stopPropagation();
//    });

    $('.ratePop .close').click(function () {
        $('.popOpacity').hide();
   });

    $('#wrapper').click(function () {
       if ($('.ratePop').is(':visible')) {
           $('.popOpacity').hide();
       }
    });

  $('.ratePop').click(function (event) {
       event.stopPropagation();
   });
});


// user page, the stars rollover in rate popup
$(document).ready(function () {
    $('.ratePop .rating .star').mouseenter(function () {
        $('.ratePop .rating .star').removeClass('selected');
        $(this).addClass('selected');
        $(this).prevAll().addClass('selected');
    });

});