<body bgcolor="#00ff00" ><div id="customers-grid" class="grid-view"><table class="items"><thead><tr>
<th id="customers-grid_c0"><a class="sort-link" >Holiday</a></th><th id="customers-grid_c0"><a class="sort-link" >Holiday Date</a></th><th id="customers-grid_c0"><a class="sort-link" >Office</a></th><th id="customers-grid_c0"><a class="sort-link" >Comments</a></th></tr>
<tbody><?php  $office=" "; ?><?php foreach ($public_holidays as $value) { ?> 
<?php   if($office!=$value['office']) { ?><tr  ><td colspan='4'style='font-size:20px;'  align="center"> <b><?php echo $value['office']; ?></b> </td></tr>
<tr><td ><?php echo $value['public_holiday']; ?> </td><td><?php echo Utils::formatDate($value['date']); ?></td><td ><?php echo $value['office']; ?></td><td><?php echo $value['comments']; ?></td></tr>
 <?php }else{?>
<tr><td ><?php echo $value['public_holiday']; ?> </td><td><?php echo  Utils::formatDate($value['date']); ?></td><td ><?php echo $value['office']; ?></td><td><?php echo $value['comments']; ?></td></tr>
<?php } $office=$value['office']; }; ?><tr  ><td colspan='4'  >  </td></tr></table><script>
function checkStatus(){}
</script></div>	<br clear="all" />	</div>	</div>	</div><div class="popup_list" style="display:none"></div>
	<div style="display:none" id="confirm_dialog" class="confirm dialog"><h1 class="confirm_title"></h1><div class="confirm_content"></div></div>
<script type="text/javascript" src="/sns/assets/7690d8a7/gridview/jquery.yiigridview.js"></script></body>
