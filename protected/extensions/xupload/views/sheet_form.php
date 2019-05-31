<!-- The file upload form used as target for the file upload widget -->
<div class="fileupload-buttonbar" style="width: 150px !important;padding-bottom:20px !important;">
	<!-- The fileinput-button span is used to style the file input field as button -->
	<span class="btn btn-success fileinput-button" style="width: 150px !important;">
		<span class="attachFileSheetEa"></span>
		<?php
            if ($this -> hasModel()) :
                echo CHtml::activeFileField($this -> model, $this -> attribute, $htmlOptions) . "\n";
            else :
                echo CHtml::fileField($name, $this -> value, $htmlOptions) . "\n";
            endif;
            ?>
	</span>
</div>
