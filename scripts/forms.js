$(document).ready(function(){
	$('.cancel').click(function(event) {
	    event.preventDefault();
	    var r = confirm("Are you sure you want to cancel?");
	    if (r==true)   {  
	       window.location = $(this).attr('href');
	    }
	});
	$('.attach').click(function(){
	        $('.upload-file').click();
	});
	$('.upload-file').change(function() {
		$('#change-pic-form').submit();return false;
	});
});