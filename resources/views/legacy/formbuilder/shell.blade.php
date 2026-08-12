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

        /* Professional, responsive form-config controls (Form Name / Recipients / Email Tag / Participant). */
        .from-box { font-family: Arial, Helvetica, sans-serif; color: #374151; }
        .top-part > h2, h2.click-drag {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            margin: 16px 0 6px;
            text-transform: none;
            letter-spacing: 0;
        }
        .top-part .rounded {
            margin: 0 0 6px;
            background: none;
            border: 0;
            padding: 0;
            box-shadow: none;
            /* flex row keeps the trailing required "*" inline to the right of the field */
            display: flex;
            align-items: center;
            gap: 8px;
            color: #dc2626;
            font-weight: 700;
        }
        .top-part .rounded input[type="text"] {
            flex: 1 1 auto;
            width: auto;
            min-width: 0;
            height: 42px;
            padding: 0 13px;
            box-sizing: border-box;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            font-size: 14px;
            background: #fff;
            color: #111827;
            font-weight: 400;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .top-part .rounded input[type="text"]:hover { border-color: #b6bcc4; }
        .top-part .rounded input[type="text"]:focus {
            outline: none;
            border-color: #008C00;
            box-shadow: 0 0 0 3px rgba(0, 140, 0, 0.15);
        }
        .top-part .parent_content { position: relative; }
        .top-part #remove_ele { margin: 2px 0 4px; }
        .top-part #remove_ele a { color: #dc2626; font-size: 12px; font-weight: 600; text-decoration: none; }
        .top-part #remove_ele a:hover { text-decoration: underline; }
        .top-part .add-another { text-align: right; margin: 2px 0 8px; }
        .top-part .add-another a { color: #008C00; font-weight: 600; font-size: 13px; text-decoration: none; }
        .top-part .add-another a:hover { text-decoration: underline; }
        .from-box .green-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #008C00;
            color: #fff;
            border: 0;
            border-radius: 9px;
            height: 42px;
            padding: 0 20px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 140, 0, 0.22);
            transition: background 0.15s ease;
        }
        .from-box .green-btn:hover { background: #00a300; }

        /* Drop area grows with its content but is capped, so it stays compact and
           short forms never clip. "Expand Window" adds .sl-fb-expanded to lift the
           cap and show the whole form; "Reduce Window" removes it. The parent iframe
           auto-fits to whichever height, so there is no dead empty space. */
        #div_drop_area.ui-widget-content,
        .ui-widget-content#div_drop_area {
            height: auto !important;
            min-height: 150px !important;
            max-height: 520px !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }
        #div_drop_area.ui-widget-content.sl-fb-expanded {
            max-height: none !important;
            overflow: visible !important;
        }
        #div_drop_area .ui-droppable,
        #div_drop_area ol {
            min-height: 120px;
            height: auto !important;
        }
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
