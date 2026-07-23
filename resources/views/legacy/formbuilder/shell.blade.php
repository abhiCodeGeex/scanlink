<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Builder</title>
    <link rel="stylesheet" href="{{ asset('form-builder/css/style.css') }}?v=fluid-3" media="screen">
    <link rel="stylesheet" href="{{ asset('form-builder/css/uniform.default.css') }}" media="screen">
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('form-builder/js/jquery-1.9.1.js') }}"></script>
    <script src="{{ asset('form-builder/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('form-builder/js/jquery.uniform.js') }}"></script>
    <script src="{{ asset('js/jscolor/jscolor.js') }}"></script>
    <script type="text/javascript">
        function url_base() { return @json(rtrim(url('/'), '/').'/'); }
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
    </script>
    <style>
        html, body { margin: 0; padding: 0; background: #fff; width: 100%; max-width: 100%; overflow-x: hidden; box-sizing: border-box; }
        .progressbar { display: none; }
    </style>
</head>
<body>
@php
    extract([
        'profile_id' => $profile_id,
        'form_name' => $form_name,
        'email_subject' => $email_subject,
        'enable_form_analytics' => $enable_form_analytics,
        'recipient_email_arr' => $recipient_email_arr,
        'form_questions_arr' => $form_questions_arr,
        'question_type_0_arr' => $question_type_0_arr,
        'question_type_1_arr' => $question_type_1_arr,
        'question_type_2_arr' => $question_type_2_arr,
    ], EXTR_SKIP);
    include resource_path('views/legacy/formbuilder/index.php');
@endphp
</body>
</html>
