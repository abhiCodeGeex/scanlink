<script language="javascript">
  $(document).ready(function(){
    $("#switcher").themeswitcher({
      imgpath: '../../../../images/images/',
      loadTheme: "start"
    });
    
    $("#chart_div_country").ddBarChart({
      chartData: <?php echo html_entity_decode($jsonData); ?>,
      action: 'init', 
      xOddClass: "ui-state-active",
      xEvenClass: "ui-state-default",
      yOddClass: "ui-state-active",
      yEvenClass: "ui-state-default",
      xWrapperClass: "ui-widget-content",
      chartWrapperClass: "ui-widget-content",
      chartBarClass: "ui-state-focus ui-corner-top",
      chartBarHoverClass: "ui-state-highlight",
      callBeforeLoad: function (){$('#loading-Notification_static').fadeIn(500);},
      callAfterLoad: function (){$('#loading-Notification_static').stop().fadeOut(0);},
      tooltipSettings: {extraClass: "ui-widget ui-widget-content ui-corner-all"}
    });
		$("#chart_div_device").ddBarChart({
      chartData: <?php echo html_entity_decode($jsonDevices); ?>,
      action: 'init', 
      xOddClass: "ui-state-active",
      xEvenClass: "ui-state-default",
      yOddClass: "ui-state-active",
      yEvenClass: "ui-state-default",
      xWrapperClass: "ui-widget-content",
      chartWrapperClass: "ui-widget-content",
      chartBarClass: "ui-state-focus ui-corner-top",
      chartBarHoverClass: "ui-state-highlight",
      callBeforeLoad: function (){$('#loading-Notification_static').fadeIn(500);},
      callAfterLoad: function (){$('#loading-Notification_static').stop().fadeOut(0);},
      tooltipSettings: {extraClass: "ui-widget ui-widget-content ui-corner-all"}
    });
		$("#chart_div_browser").ddBarChart({
      chartData: <?php echo html_entity_decode($jsonBrowsers); ?>,
      action: 'init', 
      xOddClass: "ui-state-active",
      xEvenClass: "ui-state-default",
      yOddClass: "ui-state-active",
      yEvenClass: "ui-state-default",
      xWrapperClass: "ui-widget-content",
      chartWrapperClass: "ui-widget-content",
      chartBarClass: "ui-state-focus ui-corner-top",
      chartBarHoverClass: "ui-state-highlight",
      callBeforeLoad: function (){$('#loading-Notification_static').fadeIn(500);},
      callAfterLoad: function (){$('#loading-Notification_static').stop().fadeOut(0);},
      tooltipSettings: {extraClass: "ui-widget ui-widget-content ui-corner-all"}
    });
    
  });
</script>
<div id="sf_admin_container">
  <div class="dashboard">
    <h1>View Chart <span><div id="switcher" style="position:relative; height:5%; width:100%;"></div></span></h1>
  </div>
  <div class="ui-widget" style="height: 500px !important;">
    <div style="position:relative; width:100%;height:95%;">
      <div id="chart_div_country" style="position:relative; height:95%; width:100%;"></div>
    </div>
  </div>
  <div class="dashboard">
    <h1>Device</h1>
  </div>
  <div class="ui-widget" style="height: 500px !important;">
    <div id="switcher" style="position:relative; height:5%; width:100%;"></div>
    <div style="position:relative; width:100%;height:95%;">
      <div id="chart_div_device" style="position:relative; height:95%; width:100%;"></div>
    </div>
  </div>
  <div class="dashboard">
    <h1>Browser</h1>
  </div>
  <div class="ui-widget" style="height: 500px !important;">
    <div id="switcher" style="position:relative; height:5%; width:100%;"></div>
    <div style="position:relative; width:100%;height:95%;">
      <div id="chart_div_browser" style="position:relative; height:95%; width:100%;"></div>
    </div>
  </div>
</div>
