<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload Image</title>
    <style>
        html, body { margin: 0; padding: 6px 8px; background: #f1f1f1; font: 12px Arial, sans-serif; }
        .row { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        input[type=file] { max-width: 220px; }
        button { background: #008901; color: #fff; border: 1px solid #006201; padding: 4px 10px; cursor: pointer; font-weight: bold; }
        .hint { color: #666; font-size: 11px; }
    </style>
</head>
<body>
<form method="post" action="{{ url('/portal/legacy-form-builder/upload-image') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="profile_id" value="{{ $profileId }}">
    <input type="hidden" name="textbox_id" value="textbox{{ $randomId }}">
    <div class="row">
        <input type="file" name="userfile" accept=".jpg,.jpeg,.png,.gif,image/*" required>
        <button type="submit">Upload</button>
    </div>
    <div class="hint">JPG, JPEG, PNG, GIF</div>
</form>
</body>
</html>
