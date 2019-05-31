<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
	{% if (!file.error) { %}
	<div class="box template-download fade">
		<div class="title">
			<a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}">{%=file.name%}</a>
		</div>
       	<div class="size">
        	<span>{%=o.formatFileSize(file.size)%}</span>
        </div>
		<div class="delete">
			<button class="btn btn-danger delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}" />
		</div>
	</div>
	{% } %}
{% } %}
</script>


