 <?php if(true){ ?> <div class="customerInputs em">
	<div class="addwidget" data-dashboard="<?php echo $this->id_dashboard;?>" onclick="addWidgetDashboard(this);"></div>
	<div class="bggroup add-form">
		<form method="get" action="<?php echo Yii::app()->getBaseUrl(true);?>/widgets/index" id="yw0">	
			<div class="hdselect">
				<select class="widgets_select" name="Widgets[id]">
					<option value="">Choose widget</option>
				</select>	
			</div>
			<input type="submit" value="SAVE" name="yt0" class="save" style="margin-top:2px;">			
			<span class="cancel" onclick="$('.bggroup').fadeOut();" style="margin-top:1px;">CANCEL</span>
		</form>
	</div><!-- form -->	
</div> <?php }?>