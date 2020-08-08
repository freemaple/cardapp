<form class="upload-form avatar-upload-form" method="post" enctype="multipart/form-data" action="/background/upload">
    <a class="js-change-avatar">
		<input name="image" accept="mp3*" type="file" class="upload-file avatar-upload-file" />
		<input type="hidden" name="_token" class="art_upload_form_token" value="{{ csrf_token() }}" />
    </a>
    <input type="submit" value="保存">
</form>