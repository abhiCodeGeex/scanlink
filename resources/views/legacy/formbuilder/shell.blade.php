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
            margin: 8px 0 4px;
            text-transform: none;
            letter-spacing: 0;
        }
        .top-part .rounded {
            margin: 0 0 2px;
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
            height: 36px;
            padding: 0 12px;
            box-sizing: border-box;
            border: 1px solid #d1d5db;
            border-radius: 8px;
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
        .top-part #remove_ele { margin: 0; }
        .top-part #remove_ele a { color: #dc2626; font-size: 11.5px; font-weight: 600; text-decoration: none; }
        .top-part #remove_ele a:hover { text-decoration: underline; }
        .top-part .add-another { text-align: right; margin: 2px 0 4px; }
        /* "Remove" (left, inside the preceding recipient row) and "Add Another" (right)
           share one line instead of stacking — saves a full row per recipient block.
           position:relative lifts the overlay above the positioned .parent_content, and
           pointer-events keep ONLY the link clickable so the underlying Remove still works. */
        .top-part .parent_content + .add-another {
            margin-top: -20px;
            position: relative;
            pointer-events: none;
        }
        .top-part .parent_content + .add-another a { pointer-events: auto; }
        .top-part #remove_ele { display: inline-block; }
        .top-part .add-another a { color: #008C00; font-weight: 600; font-size: 13px; text-decoration: none; }
        .top-part .add-another a:hover { text-decoration: underline; }
        .from-box .green-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #008C00;
            color: #fff;
            border: 0;
            border-radius: 8px;
            height: 36px;
            margin-top: 4px;
            padding: 0 16px;
            font-weight: 700;
            font-size: 13px;
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
        #div_drop_area div.selector:has(select) span { display: none !important; }
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
        /* ============ Edit-box (dropped element) redesign  ============ */

        /* Box shells: full-width, single palette-coloured border, rounded, clipped corners. */
        #div_drop_area .green-box, #div_drop_area .orange-box, #div_drop_area .blue-box {
            width: 100% !important;
            display: block;
            box-sizing: border-box;
            margin: 8px 0 14px !important;
            padding: 0 !important;
        }
        #div_drop_area .green-bx, #div_drop_area .orange-bx, #div_drop_area .blue-bx {
            display: block !important;
            width: 100% !important;
            box-sizing: border-box;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            padding: 0 12px 12px !important;
            margin: 0 !important;
        }
        #div_drop_area .green-bx  { border: 1px solid #86e08a !important; }
        #div_drop_area .orange-bx { border: 1px solid #f3b06c !important; }
        #div_drop_area .blue-bx   { border: 1px solid #92bdf7 !important; }

        /* Header bar: title left, mandatory + SAVE pill right, close X far right. */
        #div_drop_area .green-first-box, #div_drop_area .orange-first-box, #div_drop_area .blue-first-box {
            display: flex !important;
            align-items: center;
            gap: 10px;
            width: calc(100% + 24px) !important;
            margin: 0 -12px 10px !important;
            padding: 9px 14px !important;
            box-sizing: border-box;
            line-height: normal !important;
        }
        #div_drop_area .green-first-box  { background-color: #d9f5db !important; }
        #div_drop_area .orange-first-box { background-color: #fde8d2 !important; }
        #div_drop_area .blue-first-box   { background-color: #dbeafe !important; }
        #div_drop_area .green-first-box h1, #div_drop_area .orange-first-box h1, #div_drop_area .blue-first-box h1 {
            float: none !important;
            order: 1;
            flex: 1 1 auto;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 13px !important;
            font-weight: 700;
            color: #1f2937 !important;
        }
        #div_drop_area .green-first-box h2, #div_drop_area .orange-first-box h2, #div_drop_area .blue-first-box h2 {
            float: none !important;
            order: 2;
            display: flex;
            align-items: center;
            gap: 7px;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 12px !important;
            font-weight: 600;
            color: #374151 !important;
            text-transform: none !important;
            white-space: nowrap;
        }
        #div_drop_area .green-first-box h2 span, #div_drop_area .orange-first-box h2 span, #div_drop_area .blue-first-box h2 span {
            float: none !important;
            text-transform: none !important;
        }
        #div_drop_area .green-first-box h2 a, #div_drop_area .orange-first-box h2 a, #div_drop_area .blue-first-box h2 a {
            display: inline-block;
            background: #008C00;
            color: #fff !important;
            border-radius: 6px;
            padding: 4px 14px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            text-decoration: none !important;
        }
        #div_drop_area .green-first-box h2 a:hover, #div_drop_area .orange-first-box h2 a:hover, #div_drop_area .blue-first-box h2 a:hover {
            background: #00a300;
        }
        #div_drop_area .green-first-box > a, #div_drop_area .orange-first-box > a, #div_drop_area .blue-first-box > a {
            order: 3;
            display: inline-flex;
            align-items: center;
        }
        #div_drop_area .green-first-box img, #div_drop_area .orange-first-box img, #div_drop_area .blue-first-box img {
            float: none !important;
            margin: 0 !important;
            width: 15px;
            height: 15px;
            opacity: 0.85;
        }

        /* Pointless readonly dummy inputs (Text Field / Date / Time / Comments / dividers):
           hide them and collapse their wrapper so the box stays compact. */
        #div_drop_area input[id^="textbox"][readonly] { display: none !important; }
        #div_drop_area p:has(> input[id^="textbox"][readonly]) { margin: 0 !important; padding: 0 !important; }

        /* Body rhythm inside edit boxes. */
        #div_drop_area .green-bx p, #div_drop_area .orange-bx p, #div_drop_area .blue-bx p {
            margin: 6px 0 !important;
            padding: 0 !important;
            border: 0 !important;
        }
        /* "Record entry on Form Submission Log" row (inline margin-top:50px in legacy JS). */
        #div_drop_area p[style*="margin-top"] {
            margin: 2px 0 6px !important;
            padding: 6px 0 !important;
            gap: 6px;
            font-size: 12.5px;
            color: #374151;
            width: 100%;
            box-sizing: border-box;
        }
        #div_drop_area p[style*="margin-top"] img { width: 22px !important; height: auto !important; float: none !important; }

        /* Selector previews: legacy uniform markup is a DIV + SPAN with no real select.
           Show it as a proper disabled-select lookalike with a caret. */
        #div_drop_area div.selector:not(:has(select)) {
            display: block !important;
            position: relative !important;
            width: 100% !important;
            height: 36px !important;
            box-sizing: border-box;
            padding: 0 30px 0 12px !important;
            margin: 4px 0 10px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px;
            background: #fff !important;
        }
        #div_drop_area div.selector:not(:has(select)) span {
            display: block !important;
            width: auto !important;
            float: none !important;
            line-height: 34px;
            font-size: 13px;
            color: #6b7280;
            overflow: hidden;
            text-align: left;
        }
        #div_drop_area div.selector:not(:has(select))::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 15px;
            border: 5px solid transparent;
            border-top-color: #9ca3af;
        }

        /* Colour swatch inputs (Web Link / Document Button / Covid colours): legacy pins them
           to 10px tall x 55px — restore a real field; jscolor paints the background inline. */
        #div_drop_area input.btn_colour, #div_drop_area input.color, #div_drop_area input.text-fi {
            display: block;
            height: 36px !important;
            width: 140px !important;
            box-sizing: border-box !important;
            margin: 4px 0 10px !important;
            padding: 0 12px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            font: 700 13px/34px Arial, Helvetica, sans-serif !important;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            float: none !important;
        }

        /* hazard-class wrappers: kill the legacy 5px/10px inset that misaligned nested fields. */
        #div_drop_area .hazard-class {
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13px;
            color: #374151;
        }
        /* Checkbox rows (Include Name / Employer / Email / Phone / Signature). */
        #div_drop_area .hazard-class > div { padding: 4px 0; }

        /* Covid check-in colour pickers + Date/Time: true two-column rows. */
        #div_drop_area .row { display: flex !important; gap: 12px; }
        #div_drop_area .col { flex: 1 1 0; min-width: 0; }
        #div_drop_area .image-label.text-center { text-align: left !important; }

        /* Display-only mini fields (SWMS / Covid preview boxes): same geometry as real inputs. */
        #div_drop_area .text-box-black2 {
            box-sizing: border-box !important;
            display: block;
            width: 100% !important;
            height: auto !important;
            min-height: 36px !important;
            padding: 0 12px !important;
            margin: 2px 0 8px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px;
            background: #fff;
        }

        /* SWMS "Risk Score (Before)" row: fluid width + centered camera hint. */
        #div_drop_area .orange-bx div[style*="347px"] { width: 100% !important; align-items: center !important; }
        #div_drop_area .orange-bx div[style*="347px"] .selector { margin-bottom: 4px !important; }

        /* Number-scale From/To on one line with compact fields. */
        #div_drop_area .scale {
            display: flex !important;
            align-items: center;
            gap: 8px;
            padding: 8px 0 !important;
            margin: 0 !important;
            font-size: 13px;
            color: #374151;
            clear: both;
        }
        #div_drop_area .scale input[type="text"] {
            display: inline-block !important;
            width: 90px !important;
            flex: 0 0 auto;
            margin: 0 !important;
        }

        /* Add Another / Add Row / Add Column links: brand green, right-aligned, breathing room. */
        #div_drop_area .add-other-option, #div_drop_area .add-other-row, #div_drop_area .add-other-column, #div_drop_area .add-another {
            text-align: right;
            padding: 0 !important;
            margin: 2px 0 8px !important;
            background: none !important;
            border: 0 !important;
        }
        #div_drop_area .add-other-option a, #div_drop_area .add-other-row a, #div_drop_area .add-other-column a, #div_drop_area .add-another a {
            color: #008C00 !important;
            font-weight: 700;
            font-size: 12.5px;
            text-decoration: none !important;
        }
        #div_drop_area .add-other-option a:hover, #div_drop_area .add-other-row a:hover, #div_drop_area .add-other-column a:hover, #div_drop_area .add-another a:hover {
            text-decoration: underline !important;
        }
        #div_drop_area #grid_row, #div_drop_area #grid_column { background: none !important; border: 0 !important; padding: 0 !important; }

        /* File-type hints: quiet gray instead of alarm orange. */
        #div_drop_area .image-label .hint-info, #div_drop_area .image-label small {
            color: #9ca3af !important;
            font-weight: 400 !important;
            font-size: 11px !important;
            margin-left: 6px;
        }

        /* Upload iframes: full width, rounded, no stray inline padding. */
        #div_drop_area iframe {
            display: block;
            width: 100% !important;
            box-sizing: border-box;
            padding: 0 !important;
            margin: 4px 0 8px !important;
            border-radius: 8px;
            background: #f1f1f1;
        }

        /* Saved question previews: readable rhythm + a quiet hover hint that they're editable. */
        #div_drop_area .text-black {
            width: 100% !important;
            box-sizing: border-box;
            min-height: 20px;
            padding: 6px 10px !important;
            margin: 0 0 2px !important;
            border: 1px dashed transparent;
            border-radius: 8px;
            font-size: 13.5px;
            color: #374151;
            line-height: 1.7;
        }
        /* The legacy preview markup sprinkles trailing <br>s after every field — the blocks
           already carry their own margins, so the extra line-boxes only doubled the gaps.
           (Blank Space stays selectable through the block's min-height.) */
        #div_drop_area .text-black > br { display: none; }
        #div_drop_area .text-black h1 { margin: 4px 0 !important; }
        #div_drop_area .text-black h3 { margin: 3px 0 !important; }
        #div_drop_area .text-black .text-box-black { margin: 2px 0 6px !important; }
        #div_drop_area .text-black div.selector:not(:has(select)) { margin: 2px 0 6px !important; }
        /* Current-file preview link shown when re-editing Image / Document elements. */
        #div_drop_area .image-doc-link {
            display: inline-block;
            margin: 2px 0 8px;
            color: #008C00;
            font-weight: 600;
            font-size: 12.5px;
            text-decoration: none;
            word-break: break-all;
        }
        #div_drop_area .image-doc-link:hover { text-decoration: underline; }
        #div_drop_area .text-black:hover { border-color: #d1d5db; background: #fafafa; }
        #div_drop_area .text-black table { border-spacing: 10px 6px !important; font-size: 13px; color: #374151; }

        /* Covid check-in: its save-h2 is nested in an extra header div — keep it right of the title. */
        #div_drop_area .green-first-box > div, #div_drop_area .orange-first-box > div, #div_drop_area .blue-first-box > div {
            order: 2;
            display: flex;
            align-items: center;
        }

        /* Legacy uniform sprite on the selector span draws stray carets/squares — remove it. */
        #div_drop_area div.selector:not(:has(select)) span { background: none !important; }

        /* Web Link Button: the <br> between stacked inputs doubles the gap. */
        #div_drop_area .orange-bx > br { display: none !important; }
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
