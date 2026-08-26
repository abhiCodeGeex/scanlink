<?php

// Harness generator: emits public/debug-fb.html containing the EXACT markup the form
// builder produces for (a) every palette element's just-dropped edit box and (b) every
// saved question preview — styled by the real style.css + the shell.blade.php <style>.
// Iterate: edit shell.blade.php CSS -> re-run this -> screenshot with Playwright.

$shell = file_get_contents(__DIR__.'/resources/views/legacy/formbuilder/shell.blade.php');
preg_match('#<style>(.*?)</style>#s', $shell, $m);
$shellCss = $m[1] ?? '';

$uploadSrcdoc = htmlspecialchars(
    '<style>html,body{margin:0;padding:6px 8px;background:#f1f1f1;font:12px Arial,sans-serif;}'
    .'.row{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}input[type=file]{max-width:220px;}'
    .'button{background:#008901;color:#fff;border:1px solid #006201;padding:4px 10px;cursor:pointer;font-weight:bold;}'
    .'.hint{color:#666;font-size:11px;}</style>'
    .'<form><div class="row"><input type="file"><button type="button">Upload</button></div>'
    .'<div class="hint">JPG, JPEG, PNG, GIF</div></form>',
    ENT_QUOTES
);

function uploadIframe(string $srcdoc): string
{
    return '<iframe srcdoc="'.$srcdoc.'" scrolling="no" frameborder="0" width="94%" style="padding-left:10px" height="70"></iframe>';
}

$rid = 900000;

function mandatoryH2(int $rid): string
{
    return '<h2><span style="text-transform:capitalize;float:left;">Mandatory*</span>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="mandatory'.$rid.'" checked id="mandatory'.$rid.'" />&nbsp;&nbsp;<a href="javascript:;">save</a></h2>';
}

function saveH2(): string
{
    return '<h2><a href="javascript:;">save</a></h2>';
}

function logP(int $rid): string
{
    return '<p style="margin-top:50px;display: inline-flex; flex-direction: row; justify-content: flex-start; align-items: center; flex-wrap: wrap; padding: 5px;">'
        .'<img style="float:left;width:30px;height:31px;" src="/images/form_submission.png">'
        .'<span>Record entry on Form Submission Log&nbsp;&nbsp;&nbsp;&nbsp;</span>'
        .'<input type="checkbox" name="is_logchecked'.$rid.'" id="is_logchecked'.$rid.'"/>'
        .'<input type="text" name="log_columntitle'.$rid.'" id="log_columntitle'.$rid.'" placeholder="Enter column title">'
        .'</p>';
}

// [type_id, palette class, label]
$elements = [
    [2, 'green', 'Text'],
    [25, 'green', 'Covid check-in'],
    [13, 'orange', 'Line Divider'],
    [14, 'orange', 'Blank Space'],
    [22, 'orange', 'SWMS Hazard/Risk'],
    [24, 'orange', 'Add recipient'],
    [11, 'orange', 'Image'],
    [16, 'orange', 'Signature Panel'],
    [17, 'orange', 'Upload Button'],
    [18, 'orange', 'Participant Name'],
    [19, 'orange', 'Location Function'],
    [20, 'orange', 'Web Link Button'],
    [21, 'orange', 'Document Button'],
    [1, 'blue', 'Text Field'],
    [3, 'blue', 'Multiple Choices'],
    [4, 'blue', 'Check Box'],
    [5, 'blue', 'Drop Down Menu'],
    [6, 'blue', 'Number Scale'],
    [7, 'blue', 'Grid'],
    [8, 'blue', 'Date'],
    [9, 'blue', 'Time'],
    [15, 'blue', 'Comments'],
    [23, 'blue', 'Document Menu'],
];

$editBoxes = '';
foreach ($elements as [$tid, $ul, $name]) {
    $rid++;
    $isMandatoryType = (($tid >= 1 && $tid <= 9 && $tid !== 2) || in_array($tid, [16, 18, 19, 22, 23, 24], true));
    $isLogType = (($tid >= 1 && $tid <= 9 && $tid !== 2) || in_array($tid, [15, 23], true));

    $html = '<div class="'.$ul.'-bx'.($tid === 25 ? ' covid-checkin-header' : '').'" id="'.$rid.'">';
    $html .= '<div class="'.$ul.'-first-box">';
    $html .= '<h1>'.$name.'</h1>';
    $html .= '<a href="javascript:;"><img src="/form-builder/images/close-button.png" border="0" alt="Remove" title="Remove" /></a>';
    if ($isMandatoryType) {
        $html .= mandatoryH2($rid);
    } elseif ($tid === 25) {
        $html .= '<div class="covid-checkin-header">'.saveH2().'</div>';
    } else {
        $html .= saveH2();
    }
    $html .= '</div>';
    if ($isLogType) {
        $html .= logP($rid);
    }

    switch ($tid) {
        case 1:
        case 13:
        case 14:
        case 15:
        case 24:
            $html .= '<p><input type="text" id="textbox'.$rid.'" readonly name="textbox'.$rid.'" ></p>';
            break;
        case 2:
            $html .= '<textarea rows="1" id="textbox'.$rid.'" name="textbox'.$rid.'"></textarea>';
            break;
        case 3:
        case 4:
        case 5:
            $html .= '<p><input type="text" id="option_cntrl" class="first_ele" name="option_text'.$rid.'[]" placeholder="Enter Your Text"></p>';
            $html .= '<div id="option-list"><p class="add-other-option" ><a href="javascript:;">Add Another</a></p></div>';
            break;
        case 6:
            $html .= '<p class="scale">From&nbsp;&nbsp;<input type="text" id="scale_from" class="first_ele" name="scale_from" maxlength="5" >&nbsp;&nbsp; To&nbsp;&nbsp;<input type="text" id="scale_to" maxlength="5" name="scale_to" ></p>';
            break;
        case 7:
            $html .= '<div class="row-label">Row Label</div><input type="text" class="first_ele" name="row'.$rid.'[]" placeholder="Enter Your Text">';
            $html .= '<div id="grid_row"><p class="add-other-row" ><a href="javascript:;">Add Another Row</a></p></div>';
            $html .= '<div class="column-label">Column Label</div><input type="text" name="column'.$rid.'[]" placeholder="Enter Your Text">';
            $html .= '<div id="grid_column"><p class="add-other-column" ><a href="javascript:;">Add Another Column</a></p></div>';
            break;
        case 8:
        case 9:
            $html .= '<p><input type="text" id="textbox'.$rid.'" readonly name="textbox'.$rid.'" ></p>';
            break;
        case 11:
            $html .= '<div class="image-label">Image Title</div><input type="text" id="image_title'.$rid.'" name="image_title'.$rid.'" placeholder="Enter Your Text">';
            $html .= '<div class="image-label">Select File<span class="hint-info">(File type JPG, JPEG, PNG, GIF)</span></div>';
            $html .= uploadIframe($GLOBALS['uploadSrcdoc']);
            $html .= '<div class="image-label">Alignment :&nbsp;<input type="radio" class="image_align" value="left" checked name="image_align'.$rid.'" >Left&nbsp;<input type="radio" value="center" name="image_align'.$rid.'" >Center&nbsp;<input type="radio" value="right" name="image_align'.$rid.'" >Right</div>';
            break;
        case 16:
            $html .= '<p><input type="text" id="textbox'.$rid.'" name="textbox'.$rid.'" placeholder="Enter Your Text"></p>';
            $html .= '<div class="hazard-class"><div><input class="sign-text include-name" type="checkbox"> Include Name</div></div>';
            $html .= '<div class="hazard-class"><div><input class="sign-text include-employer" type="checkbox"> Include Employer/Company</div></div>';
            $html .= '<div class="hazard-class"><div><input class="sign-text include-email" type="checkbox"> Include Email</div></div>';
            $html .= '<div class="hazard-class"><div><input class="sign-text include-phone" type="checkbox"> Include Phone</div></div>';
            break;
        case 20:
            $html .= '<input type="text" class="btn_text" id="textbox'.$rid.'" name="textbox'.$rid.'" placeholder="Button Text" ><br>';
            $html .= '<input type="text" class="btn_link" id="btn_link'.$rid.'" name="btn_link'.$rid.'" placeholder="Button Link URL (start with http:// or https://) " ><br>';
            $html .= '<input type="text" class="btn_colour color" id="btn_colour'.$rid.'" maxlength="6" value="007A01" >';
            break;
        case 21:
            $html .= '<div class="image-label">Document Title</div><input type="text" id="doc_title'.$rid.'" name="doc_title'.$rid.'" placeholder="Enter Your Text">';
            $html .= '<div class="image-label">Select File<span class="hint-info">(File type  DOC, DOCX, PDF, JPG, GIF, JPEG)</span></div>';
            $html .= uploadIframe($GLOBALS['uploadSrcdoc']);
            $html .= '<input type="text" class="btn_colour color" id="doc_color'.$rid.'" maxlength="6" value="007A01" >';
            break;
        case 22:
            $html .= '<p><input type="text" id="textbox'.$rid.'" name="textbox'.$rid.'" placeholder="Enter Your Text"></p>';
            $html .= '<div class="image-label">Task | Activity</div>';
            $html .= '<div class="hazard-class"><div class="text-box-black2"></div></div>';
            $html .= '<div class="image-label">Potential Hazards</div>';
            $html .= '<div class="hazard-class"><div class="text-box-black2"></div></div>';
            $html .= '<div class="image-label">Risk Score (Before)</div>';
            $html .= '<div style="display: flex; width: 347px;">';
            $html .= '<div class="hazard-class" style="width: 100%;"><div class="selector"><span style="width: 126px;">Select</span></div></div>';
            $html .= '<div style="display: flex;align-items: center;min-width: 70px;margin-left:15px;"><img src="/form-builder/images/camera.png" style="height:25px;width:25px;" />&nbsp;<span>Photo</span></div>';
            $html .= '</div>';
            $html .= '<div class="image-label">Control Measures</div>';
            $html .= '<div class="hazard-class"><div class="text-box-black2"></div></div>';
            $html .= '<div class="image-label">Risk Score (After)</div>';
            $html .= '<div class="hazard-class"><div class="selector"><span style="width: 126px;">Select</span></div></div>';
            break;
        case 23:
            $html .= '<div class="image-label">Document Title <small>(Please separate each title with a comma)</small></div><input type="text" id="doc_title'.$rid.'" name="doc_title'.$rid.'" placeholder="Enter Your Text">';
            $html .= '<div class="image-label">Select File<span class="hint-info">(File type  DOC, DOCX, PDF, JPG, GIF)</span></div>';
            $html .= uploadIframe($GLOBALS['uploadSrcdoc']);
            break;
        case 18:
            $html .= '<p><input type="text" id="textbox'.$rid.'" name="textbox'.$rid.'" placeholder="Enter Your Text"></p>';
            $html .= '<div class="hazard-class"><div><input class="sign-text include-name" type="checkbox"> Include Signature</div></div>';
            $html .= '<div class="hazard-class"><div><input class="sign-text include-employer" type="checkbox"> Include Employer/Company</div></div>';
            break;
        case 25:
            $html .= '<textarea rows="1" id="textbox'.$rid.'" name="textbox'.$rid.'" ></textarea>';
            $html .= '<hr>';
            $html .= '<br>';
            $html .= '<div class="box-main">';
            $html .= '<div class="row">';
            $html .= '<div class="col">';
            $html .= '<div class="image-label text-center">Text colour</div>';
            $html .= '<div class="hazard-class"><input type="text" class="text-fi color" value="#000000" name="text_color" id="text_color'.$rid.'" autocomplete="off"></div>';
            $html .= '</div>';
            $html .= '<div class="col">';
            $html .= '<div class="image-label text-center">Background color</div>';
            $html .= '<div class="hazard-class"><input type="text" class="text-fi color" value="#ffffff" name="bg_color" id="bg_color'.$rid.'" autocomplete="off"></div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<p style="display: inline-flex; flex-direction: row; justify-content: flex-start; align-items: center; flex-wrap: wrap; padding: 10px 0px 20px 0px;">';
            $html .= '<img style="float:left;width:30px;height:31px;" src="/images/form_submission.png">';
            $html .= '<span>Record entry on Form Submission Log&nbsp;&nbsp;&nbsp;&nbsp;</span>';
            $html .= '<input type="checkbox" name="is_logchecked'.$rid.'" checked="checked"/>';
            $html .= '<div class="image-label">Visitor Name</div>';
            $html .= '<div class="hazard-class"><div class="text-box-black2"></div></div>';
            $html .= '<div class="image-label ">Visitor Phone</div>';
            $html .= '<div class="hazard-class"><div class="text-box-black2 "></div></div>';
            $html .= '<div class="row">';
            $html .= '<div class="col"><div class="hazard-class"><div class="image-label">Date</div><div class="hazard-class"><div class="text-box-black2 "></div></div></div></div>';
            $html .= '<div class="col"><div class="hazard-class"><div class="image-label">Time</div><div class="hazard-class"><div class="text-box-black2 "></div></div></div></div>';
            $html .= '</div>';
            $html .= '<div class="image-label">Venue Name</div>';
            $html .= '<div class="hazard-class"><div class="text-box-black2"></div></div>';
            $html .= '<div class="image-label">Venue address</div>';
            $html .= '<div class="hazard-class"><div class="text-box-black2 "></div></div>';
            $html .= '<div class="image-label">Location Description/Type	</div>';
            $html .= '<div class="hazard-class"><div class="text-box-black2"></div></div>';
            $html .= '</div>';
            break;
        default: // 17 upload button and any other
            $html .= '<p><input type="text" id="textbox'.$rid.'" name="textbox'.$rid.'" placeholder="Enter Your Text"></p>';
    }

    $html .= '</div>';

    $editBoxes .= '<div class="sl-harness-cap">'.$tid.' — '.$name.' (edit box)</div>';
    $editBoxes .= '<div class="'.$ul.'-box" id="'.$rid.'">'.$html.'</div>';
}

// ---- Saved previews (the PHP switch markup) ----
$qrData = 'data:image/png;base64,'.base64_encode((function () {
    $img = imagecreatetruecolor(100, 100);
    imagefilledrectangle($img, 0, 0, 99, 99, imagecolorallocate($img, 230, 230, 230));
    imagefilledrectangle($img, 20, 20, 79, 79, imagecolorallocate($img, 0, 137, 1));
    ob_start();
    imagepng($img);

    return ob_get_clean();
})());

$previews = [
    1 => '<div class="text-box-black" ></div><br>',
    2 => '<div class="text_block" ><p>Some <b>rich</b> text content of the Text element sits here.</p></div><div style="clear: both;"></div>',
    3 => "<input type='radio' name='q3' />&nbsp;Option one<br><input type='radio' name='q3' />&nbsp;Option two<br><input type='radio' name='q3' />&nbsp;Option three<br><br>",
    4 => "<input type='checkbox' name='q4[]' />&nbsp;Choice A<br><input type='checkbox' name='q4[]' />&nbsp;Choice B<br><br>",
    5 => '<div name="q5" class="selector" ><span style="width: 126px;">Pick an option</span></div><br>',
    6 => "<input type='radio' name='q6' />&nbsp;1<br><input type='radio' name='q6' />&nbsp;2<br><input type='radio' name='q6' />&nbsp;3<br><input type='radio' name='q6' />&nbsp;4<br><input type='radio' name='q6' />&nbsp;5<br><br>",
    7 => "<table border='0' cellpadding='3' cellspacing='15'><tr><td>&nbsp;</td><td align='center'>Good</td><td align='center'>OK</td><td align='center'>Bad</td></tr>"
        ."<tr><td>Service</td><td align='center'><input type='radio' name='q7_r0'></td><td align='center'><input type='radio' name='q7_r0'></td><td align='center'><input type='radio' name='q7_r0'></td></tr>"
        ."<tr><td>Quality</td><td align='center'><input type='radio' name='q7_r1'></td><td align='center'><input type='radio' name='q7_r1'></td><td align='center'><input type='radio' name='q7_r1'></td></tr>"
        .'</table><br>',
    8 => '<div class="text-box-black" id="dtpicker_date" >Date Picker</div><br>',
    9 => '<div class="text-box-black" id="dtpicker_time" >Time Picker</div><br>',
    10 => "<h1 style='font-size:20px;'>Heading text</h1>",
    11 => '<div align="left" class="fb-image-preview"><img src="'.$qrData.'" width="100" title="Site photo" ></div><br><br>',
    12 => "<h3  style='font-size:16px;'>Sub heading text</h3>",
    13 => '<hr>',
    14 => '<br><br>',
    15 => '<div name="div_comments" class="text-box-black">comments</div>',
    16 => '<div class="image-label">Name</div><div class="text-box-black"></div><br>'
        .'<div class="image-label">Employer/Company</div><div class="text-box-black"></div><br>'
        .'<p>Please sign below</p><br>'
        .'<div name="div_sign" class="text-box-black">Signature</div>',
    17 => '<p>Upload your SWMS document</p><br><div name="" style="text-align:center; border:none; background-color:#007A01;color:#ffffff;" class="text-box-black" >Upload</div>',
    18 => '<p>Name</p><div name="q18" class="text-box-black" ></div><br><div class="image-label">Employer/Company</div><div class="text-box-black"></div><br><div name="div_sign" class="text-box-black">Signature</div>',
    19 => '<p>Where are you?</p><br><div name="q19" class="text-box-black" ></div><div name="" style="text-align:center; border:none;width:140px;margin-top:5px; background-color:#808080;color:#ffffff;" class="text-box-black" >MAP</div>',
    20 => '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#007A01;color:#ffffff;" class="text-box-black" >Visit our website</div>',
    21 => '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#007A01;color:#ffffff;" class="text-box-black" >Safety procedure document</div>',
    22 => '<p>swms 2</p><br><div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#808080;color:#ffffff;" class="text-box-black" >SWMS Hazard/Risk</div>',
    23 => '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#808080;color:#ffffff;" class="text-box-black" >Document Menu</div>',
    24 => "<div class='add_recipient' style='height:auto;text-align:left;padding-left:10px; border:none;'>Send email notifications to<div class='rounded' ><input style='width:90%' class='email_recipient_additional' type='text'></div><div class='add-another'><a href='javascript:;'>Add Another</a></div></div>",
    25 => "<div class='checkin-form-btn'>COVID CHECK-IN</div>",
];

$names = [
    1 => 'Text Field', 2 => 'Text', 3 => 'Multiple Choices', 4 => 'Check Box', 5 => 'Drop Down Menu',
    6 => 'Number Scale', 7 => 'Grid', 8 => 'Date', 9 => 'Time', 10 => 'Heading', 11 => 'Image',
    12 => 'Sub heading', 13 => 'Line Divider', 14 => 'Blank Space', 15 => 'Comments', 16 => 'Signature Panel',
    17 => 'Upload Button', 18 => 'Participant Name', 19 => 'Location Function', 20 => 'Web Link Button',
    21 => 'Document Button', 22 => 'SWMS Hazard/Risk', 23 => 'Document Menu', 24 => 'Add recipient', 25 => 'Covid check-in',
];

$previewHtml = '';
foreach ($previews as $tid => $body) {
    $previewHtml .= '<div class="sl-harness-cap">'.$tid.' — '.$names[$tid].' (saved preview)</div>';
    $previewHtml .= '<div id="question_div_p'.$tid.'" class="text-black" title="drag to change order">'
        .'<span class="question_id_span" style="display:none;">p'.$tid.'</span>'.$body.'</div>';
}

$out = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>FB harness</title>
<link rel="stylesheet" href="/form-builder/css/style.css?v=fluid-3" media="screen">
<link rel="stylesheet" href="/form-builder/css/uniform.default.css" media="screen">
<style>{$shellCss}</style>
<style>
/* harness-only chrome */
body { background:#fff; }
.sl-harness-cap { font:700 12px Arial; color:#7c3aed; margin:22px 0 4px; }
.sl-harness-col { width: 460px; float:left; margin-right: 30px; }
#div_drop_area.ui-widget-content { max-height:none !important; overflow:visible !important; }
</style>
</head>
<body>
<div class="from-box">
<div id="drop_area">
<div class="ui-widget-content" id="div_drop_area" style="height:auto;">
<ol><li class="placeholder">
<div class="sl-harness-col">
<h2 style="font:700 15px Arial;">EDIT BOXES (just dropped)</h2>
{$editBoxes}
</div>
<div class="sl-harness-col">
<h2 style="font:700 15px Arial;">SAVED PREVIEWS</h2>
{$previewHtml}
</div>
<div style="clear:both;"></div>
</li></ol>
</div>
</div>
</div>
</body>
</html>
HTML;

file_put_contents(__DIR__.'/public/debug-fb.html', $out);
echo "written\n";
