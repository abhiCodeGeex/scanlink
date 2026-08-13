<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Upload result</title>
    <style>
        body { margin: 0; padding: 8px; font: 12px Arial, sans-serif; background: #f1f1f1; }
        .ok { color: #006201; font-weight: bold; }
        .err { color: #b00000; font-weight: bold; }
    </style>
</head>
<body>
{{-- The jQuery File Uploader (ckeditor/fileuploader) reads #status ('success'|'error')
     and #message from the response to stop its "Uploading file..." progress bar. Without
     these it never clears the "uploading" state and the bar sticks. Harmless to the
     iframe-form mechanism (its window.parent script below still runs). --}}
<div id="status" style="display:none">{{ $ok ? 'success' : 'error' }}</div>
<div id="message" style="display:none">{{ $ok ? 'Uploaded: '.$filename : $message }}</div>
@if ($ok)
    <div class="ok">Uploaded: {{ $filename }}</div>
    <script>
        (function () {
            var flag = @json($uploadedFlag);
            var textboxId = @json($textboxId);
            var filename = @json($filename);
            try {
                if (flag && window.parent.document.getElementById(flag)) {
                    window.parent.document.getElementById(flag).value = '1';
                }
                if (textboxId && window.parent.document.getElementById(textboxId)) {
                    window.parent.document.getElementById(textboxId).value = filename;
                }
                window.parent.saveornot = false;
            } catch (e) {}
        })();
    </script>
@else
    <div class="err">{{ $message }}</div>
@endif
</body>
</html>
