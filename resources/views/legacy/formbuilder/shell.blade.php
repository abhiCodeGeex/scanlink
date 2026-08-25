<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

        /* ============ Canvas ("CREATE YOUR FORM HERE") — clean field design ============
           The legacy stylesheet leaves dropped-question inputs at odd widths with cramped
           padding, cropped edges and inconsistent spacing. Normalise every field inside the
           drop area into one consistent system. Checkboxes/radios/buttons are excluded. */
        #div_drop_area input[type="text"],
        #div_drop_area input[type="number"],
        #div_drop_area input[type="email"],
        #div_drop_area textarea,
        #div_drop_area select {
            display: block;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            height: 36px;
            padding: 0 12px !important;
            margin: 4px 0 10px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            background: #fff !important;
            font: 13px/1.4 Arial, Helvetica, sans-serif !important;
            color: #111827 !important;
            float: none !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        #div_drop_area textarea {
            height: auto;
            min-height: 64px;
            padding: 9px 12px !important;
            resize: vertical;
        }
        #div_drop_area input[type="text"]:focus,
        #div_drop_area input[type="number"]:focus,
        #div_drop_area input[type="email"]:focus,
        #div_drop_area textarea:focus,
        #div_drop_area select:focus {
            outline: none;
            border-color: #008C00 !important;
            box-shadow: 0 0 0 3px rgba(0, 140, 0, 0.14);
        }
        #div_drop_area input[type="checkbox"],
        #div_drop_area input[type="radio"] {
            display: inline-block;
            width: 16px !important;
            height: 16px !important;
            margin: 0 5px 0 0 !important;
            padding: 0 !important;
            border: 0 !important;
            vertical-align: middle;
            cursor: pointer;
        }
        /* uniform.js wraps checkboxes/radios in styled spans and hides the real input
           (opacity 0, absolutely positioned). Those spans aren't in jQuery-UI's drag-cancel
           list, so clicking them sometimes started a drag instead of toggling — the
           "sometimes clickable" checkboxes. Neutralise uniform inside the canvas: show the
           REAL native input (always clickable) and flatten the wrapper chrome. */
        #div_drop_area div.checker,
        #div_drop_area div.checker span,
        #div_drop_area div.radio,
        #div_drop_area div.radio span {
            display: inline-block !important;
            position: static !important;
            width: auto !important;
            height: auto !important;
            padding: 0 !important;
            margin: 0 !important;
            background: none !important;
            border: 0 !important;
        }
        #div_drop_area div.checker input[type="checkbox"],
        #div_drop_area div.radio input[type="radio"] {
            opacity: 1 !important;
            position: static !important;
            display: inline-block !important;
        }
        /* Same for uniform-wrapped selects: keep the native select usable. */
        #div_drop_area div.selector {
            display: block !important;
            position: static !important;
            width: 100% !important;
            height: auto !important;
            padding: 0 !important;
            background: none !important;
            border: 0 !important;
        }
        #div_drop_area div.selector span { display: none !important; }
        #div_drop_area div.selector select {
            opacity: 1 !important;
            position: static !important;
            height: 36px !important;
        }
        /* List rhythm: uniform gutters, no stray browser margins on the <p> labels the legacy
           markup sprinkles between fields (they caused the uneven left insets and gaps). */
        #div_drop_area ol { margin: 0 !important; padding: 0 !important; list-style: none; }
        #div_drop_area li { padding: 10px 12px !important; margin: 0 0 4px !important; box-sizing: border-box; }
        #div_drop_area li p {
            margin: 8px 0 3px !important;
            padding: 0 !important;
            font: 700 12px Arial, Helvetica, sans-serif;
            color: #374151;
        }
        /* Dummy display "fields" (Signature/comments/date/time previews are DIVS, not inputs).
           Legacy CSS pins them to height:17px with NO left padding — text sat on the border.
           Force the same geometry as real inputs. */
        #div_drop_area div.text-box-black {
            box-sizing: border-box !important;
            display: block;
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            min-height: 36px !important;
            line-height: 34px !important;
            padding: 0 12px !important;
            margin: 4px 0 10px !important;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
            color: #6b7280;
            font-size: 13px;
            font-family: Arial, Helvetica, sans-serif;
            overflow: hidden;
            float: none !important;
        }
        /* Coloured preview bars (Upload / MAP / Web Link / Document / SWMS / Menu): each carries
           different inline styles (left vs center text, padding-left:10px, width:140px on MAP).
           Normalise ALL of them into one identical bar: full width, centered, 38px tall. */
        #div_drop_area div.text-box-black[style*="background-color"],
        #div_drop_area .checkin-form-btn {
            box-sizing: border-box !important;
            display: block;
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            min-height: 38px !important;
            line-height: 38px !important;
            padding: 0 12px !important;
            margin: 6px 0 10px !important;
            border: 0 !important;
            border-radius: 8px !important;
            text-align: center !important;
            font-weight: 700;
            font-size: 13px;
            color: #fff;
            overflow: hidden;
        }
        #div_drop_area .checkin-form-btn { background: #007A01; }
        /* "Send email notifications to" recipient preview: inline width:90% input → full width. */
        #div_drop_area .add_recipient { padding: 0 !important; }
        #div_drop_area .add_recipient input { width: 100% !important; }
        #div_drop_area .add_recipient .rounded { margin: 4px 0 !important; padding: 0 !important; background: none; border: 0; }
        /* Uploaded image preview: breathing room + rounded thumbnail. */
        #div_drop_area .fb-image-preview { margin: 6px 0 10px; }
        #div_drop_area .fb-image-preview img {
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            padding: 3px;
            background: #fff;
        }
        /* Labels above fields (Image Title / Row Label / question labels). */
        #div_drop_area .image-label,
        #div_drop_area .row-label,
        #div_drop_area .column-label {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            margin: 8px 0 2px;
        }
        /* Question blocks: consistent inner padding so nothing touches the edges. */
        #div_drop_area li { box-sizing: border-box; }
        #div_drop_area .parent_content { padding: 2px 2px 4px; }

        /* Mobile: the legacy form builder is a fixed 440px+440px / 980px desktop layout. Now
           that the viewport is device-width, fluidize the remaining fixed-width containers so
           dropped questions, previews and inputs fit the phone instead of clipping. */
        @media (max-width: 640px) {
            .from-box,
            .text-black,
            .text-box-black,
            .text-box-black2,
            #div_drop_area,
            #div_drop_area .ui-droppable,
            #div_drop_area ol,
            .top-part,
            .rounded {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
                float: none !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .text-black .text-box-black,
            .text-box-black input[type="text"],
            .text-box-black input[type="number"],
            .text-box-black textarea,
            .text-box-black select,
            .text-black input[type="text"],
            .text-black textarea,
            .text-black select {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
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
<script>
    // jQuery-UI drag/sort only ignores real form elements by default; uniform.js spans,
    // labels and links inside a question box would start a drag instead of clicking.
    // Extend the cancel list on every sortable/draggable, and keep re-applying it because
    // the canvas re-initialises them as questions are added/edited.
    (function () {
        function relaxDragCancel() {
            try {
                var cancel = 'input,textarea,button,select,option,label,a,.checker,.radio,.selector,.image-label';
                $('.ui-sortable').each(function () {
                    try { $(this).sortable('option', 'cancel', cancel); } catch (e) {}
                });
                $('.ui-draggable').each(function () {
                    try { $(this).draggable('option', 'cancel', cancel); } catch (e) {}
                });
            } catch (e) {}
        }
        // Uniform.js must never wrap canvas controls (its spans break clicking). The
        // uniform() calls exclude #div_drop_area now; this strips any wrapper that still
        // sneaks in (e.g. from older markup) back to the native control.
        function stripUniformInCanvas() {
            try {
                $('#div_drop_area div.checker input, #div_drop_area div.radio input, #div_drop_area div.selector select').each(function () {
                    try { $.uniform.restore($(this)); } catch (e) {}
                });
            } catch (e) {}
        }
        $(function () {
            relaxDragCancel();
            stripUniformInCanvas();
            setInterval(function () { relaxDragCancel(); stripUniformInCanvas(); }, 1000);
            $(document).ajaxComplete(function () { setTimeout(function () { relaxDragCancel(); stripUniformInCanvas(); }, 100); });
        });
    })();
</script>
</body>
</html>
