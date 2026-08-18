<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload Document</title>
    <style>
        html, body { margin: 0; padding: 6px 8px; background: #f1f1f1; font: 12px Arial, sans-serif; }
        .row { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        input[type=file] { max-width: 220px; }
        button { background: #008901; color: #fff; border: 1px solid #006201; padding: 4px 10px; cursor: pointer; font-weight: bold; }
        .hint { color: #666; font-size: 11px; }
    </style>
</head>
<body>
<form method="post" action="{{ url('/portal/legacy-form-builder/upload-doc') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="profile_id" value="{{ $profileId }}">
    <input type="hidden" name="random_id" value="{{ $randomId }}">
    <input type="hidden" name="textbox_id" value="textbox{{ $randomId }}">
    <input type="hidden" name="multiple" value="{{ $multiple ? 1 : 0 }}">
    <div class="row">
        <input
            type="file"
            name="userfile{{ $multiple ? '[]' : '' }}"
            @if ($multiple) multiple @endif
            accept=".jpg,.jpeg,.png,.gif,.doc,.docx,.pdf"
            required
        >
        <button type="submit">Upload</button>
    </div>
    <div class="hint">DOC, DOCX, PDF, JPG, GIF, JPEG</div>
</form>
</body>
</html>
