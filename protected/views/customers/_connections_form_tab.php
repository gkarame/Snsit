<div class="connections" id="connections">
	<div class="omie">
		<?php $this->renderPartial('_conn_grid', array('model' => $model));	?>
	</div>
	<div class="tache new_conn">
		<div onclick="showConnectionForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW CONNECTION');?></b></u></div>
	</div>
</div>
<script type="text/javascript">
	function showConnectionForm(element, newConn) {
		if (false) {	$(element).addClass('invalid');	} else {		$(element).removeClass('invalid');	}
		if (!$(element).hasClass('invalid')) {
			var url;
			if (newConn){	url = "<?php echo Yii::app()->createAbsoluteUrl('customers/manageConnection');?>";	}else{	url = $(element).attr('href');	}
			$.ajax({
		 		type: "POST",  	url: url,
			  	data: 'update=<?php echo $model->isNewRecord ? 0 : 1; ?> + &Conn[id_customer]=<?php echo $model->id;?>', 
			  	dataType: "json",
			  	success: function(data) {
				  	if (data) {
					  	if (data.status == 'success') {
						  	if (newConn) {	$('.new_conn').hide();	$('.new_conn').after(data.form);
						  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>');
						  	}
					  	}
				  	}
		  		}
			});
		} else {		alert('The form is not valid!');}
	}	
	function saveConnection(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('customers/manageConnection');?>";
		if (id != 'new') {	url += '/'+parseInt(id);}
		$.ajax({
	 		type: "POST",
	 		data: $(element).parents('.new_connection').serialize() + '&Connections['+id+'][id_customer]=<?php echo $model->id;?>&update=<?php echo $model->isNewRecord ? 0 : 1;?>',					
		  	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved') {
					  	if (id == 'new') {
					  		$(element).parents('.tache.new').remove();		$('.new_conn').show();
					  	}
					  	location.reload();
				  		if (data.status == 'success') {
				  			$(element).parents('.tache.new').replaceWith(data.form);
				  		}
				  	}
			  	}
	  		}
		});
	}
</script>