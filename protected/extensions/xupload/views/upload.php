<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
	{% if (!file.error) { %}
	<div class="box template-upload fade" id="specialid">
		
		<div class="title file">
			<a href="#" title="{%=file.name%}">{%=file.name%}</a>
		</div>
		<div class="progress loading">
			<div class="color">
				<div class="middle bar">
					<div class="corner1"></div>
					<div class="corner2"></div>
				</div>
			</div>
		</div>
	</div>
	{% } %}
{% } %}
</script>
