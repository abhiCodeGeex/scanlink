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
