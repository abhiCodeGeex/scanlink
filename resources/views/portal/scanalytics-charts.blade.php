<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scanalytics</title>
    <link type="text/css" href="{{ asset('styles/style.css') }}?v=sa-2" rel="stylesheet" />
    <link type="text/css" href="{{ asset('styles/chart/style/ddchart.css') }}" rel="stylesheet" />
    <link type="text/css" href="{{ asset('styles/chart/style/theme.css') }}" rel="stylesheet" />
    <link id="sl-ui-theme" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/start/jquery-ui.css" rel="stylesheet" />
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/chart/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/chart/js/jquery.themeswitcher.js') }}"></script>
    <script src="{{ asset('js/chart/js/jquery.tooltip.js') }}"></script>
    <script src="{{ asset('js/chart/js/jquery.ddchart.js') }}"></script>
    <style>
        html, body { margin: 0; padding: 12px 16px 20px; background: #fff; font-family: Arial, Helvetica, sans-serif; color: #333; }
        .link-button { display: inline-block; background: #007a01; color: #fff !important; border: 1px solid #006201; border-radius: 6px; text-transform: uppercase; font-weight: bold; font-size: 12px; padding: 8px 12px; text-decoration: none; cursor: pointer; line-height: 1.2; }
        .link-button:hover { background: #008901; }
        .scananalytic-buttons { margin: 0; padding: 0; }
        .scananalytic-buttons li { float: left; list-style: none; margin: 0; padding-left: 8px; }
        .print-btn { background: #eee; border: 1px solid #bbb; border-radius: 3px; padding: 5px 9px; cursor: pointer; font-weight: bold; font-size: 12px; }
        .gray-text { color: #333; }
        .scanalytics-box { text-align: center; }
        .scanalytics-box b { display: block; font-size: 13px; color: #444; margin-bottom: 4px; }
        .ui-widget { height: 500px !important; margin-bottom: 24px; }
        .c-form { clear: both; }
    </style>
</head>
<body>
    <div style="float:left;" id="divToPrint"><h3 style="margin:0;">Profile {{ (int) $profileId }}.@if (filled($profileName)) {{ ucwords($profileName) }}@endif</h3></div>
    <div class="btn-inline" style="float:right;">
        <ul class="scananalytic-buttons">
            <li><a class="link-button" href="{{ $mapUrl }}" target="_top">Location Map</a></li>
            <li><a class="link-button" href="{{ $locationsUrl }}" target="_top">Location List</a></li>
            <li><a class="link-button" href="{{ $exportUrl }}" target="_top">Export Analytics</a></li>
            <li><a class="link-button" href="{{ $returnUrl }}" target="_top">Return to list</a></li>
        </ul>
    </div>
    <div class="clear" style="clear:both;"></div>

    <div class="All" id="divToPrint2">
        <div class="clear" style="clear:both;"></div>
        <div id="switcher" style="position:relative; height:5%; float:left;"></div>
        <button onclick="window.print()" class="print-btn" style="float:left; margin-left:10px;">
            <img src="https://scanlink.com.au/images/print.png" alt="Print" onerror="this.style.display='none'" />
        </button>&nbsp;
        <button onclick="window.print()" class="print-btn">Download PDF</button>
        <div class="clear" style="clear:both;"></div>
        <br/>

        <div align="center" class="gray-text"><strong>Click on the bar graph for more detailed analytics</strong></div>

        <div class="scanalytics_tot_div">
            <div class="row" style="display:flex;">
                <div class="scanalytics-box" style="margin-right:15px;">
                    <b>Total Form Submission </b>
                    <div class="scanalytics_tot_count">{{ (int) $formSubmissionCount }}</div>
                </div>
                <div class="scanalytics-box">
                    <b>Analytics Count</b>
                    <div class="scanalytics_tot_count">{{ (int) $analyticsCount }}</div>
                </div>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>

        <div class="c-form">
            <div align="center" style="position:relative; margin-top:20px;">
                <div class="ui-widget" style="height:500px !important;"><div style="position:relative; width:100%; height:95%;"><div id="chart_div_country" style="position:relative; height:95%; width:100%;"></div></div></div>
                <div class="ui-widget" style="height:500px !important;"><div style="position:relative; width:100%; height:95%;"><div id="chart_div_device" style="position:relative; height:95%; width:100%;"></div></div></div>
                <div class="ui-widget" style="height:500px !important;"><div style="position:relative; width:100%; height:95%;"><div id="chart_div_browser" style="position:relative; height:95%; width:100%;"></div></div></div>
            </div>
        </div>
    </div>

    <div id="loading-Notification_static" style="display:none; position:fixed; top:8px; right:12px; font-weight:bold; color:#555;">Loading…</div>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#switcher").themeswitcher({
                imgpath: @json(asset('styles/chart/images/').'/'),
                loadTheme: "start"
            });

            var common = {
                action: 'init',
                animationDelay: 700,
                xOddClass: "ui-state-active", xEvenClass: "ui-state-default",
                yOddClass: "ui-state-active", yEvenClass: "ui-state-default",
                xWrapperClass: "ui-widget-content", chartWrapperClass: "ui-widget-content",
                chartBarClass: "ui-state-focus ui-corner-top", chartBarHoverClass: "ui-state-highlight",
                callBeforeLoad: function () { $('#loading-Notification_static').fadeIn(500); },
                callAfterLoad: function () { $('#loading-Notification_static').stop().fadeOut(0); },
                tooltipSettings: { extraClass: "ui-widget ui-widget-content ui-corner-all" }
            };

            var pid = {{ (int) $profileId }};
            $("#chart_div_country").ddBarChart($.extend({}, common, { chartDataLink: @json(route('portal.graphengine.country')) + "?pid=" + pid }));
            $("#chart_div_device").ddBarChart($.extend({}, common, { chartDataLink: @json(route('portal.graphengine.device')) + "?pid=" + pid }));
            $("#chart_div_browser").ddBarChart($.extend({}, common, { chartDataLink: @json(route('portal.graphengine.browser')) + "?pid=" + pid }));

            function postHeight() { try { parent.postMessage({ slChartsHeight: document.body.scrollHeight }, '*'); } catch (e) {} }
            [700, 1600, 3200].forEach(function (ms) { setTimeout(postHeight, ms); });
        });
    </script>
</body>
</html>
