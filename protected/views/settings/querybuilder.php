<div class="row">	<span class="query_title">Query Editor</span>	<span class="horizontalLine"></span><textarea id="query" class="ta4" cols="200" name="ta4" rows="1"></textarea>
	</div><div class="buttttttton" onclick="js:exestatment()">	<span onclick="js:exestatment()" class="search-btn" ></span></div><br clear="all" /><div id="result"> </div>
<script type="text/javascript">
	function exestatment(){
		var query=$('#query').val();
			$.ajax({type: "POST",data: {"query":query},	url: "<?php echo Yii::app()->createAbsoluteUrl('settings/exestatment')?>", 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {	$('#result').html(data.resultset);  	} 		}	});	}
	</script>