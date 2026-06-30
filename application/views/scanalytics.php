<style type="text/css">.chart{ float:left; }</style>
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<?php
	$durations=array("allTime","month","week","day");
	foreach($durations as $Duration){
		//$Duration = 'allTime'; 	//allTime,month,week,day,twoHours
		$shortUrlClicks = ''; $TotalClicks = ''; $Countries = ''; $Browsers = ''; $Platforms = '';
		$shortUrlClicks = $analytics['analytics'][$Duration]['shortUrlClicks'];
		$TotalClicks = $analytics['analytics'][$Duration]['referrers']['0']['count'];
		$Countries = count($analytics['analytics'][$Duration]['countries']);
		$Browsers = count($analytics['analytics'][$Duration]['browsers']);
		$Platforms = count($analytics['analytics'][$Duration]['platforms']);
		
		$TotalCnt1='';
		for($ab=0;$ab<$Browsers;$ab++)
		{
			$TotalCnt1 += $analytics['analytics'][$Duration]['browsers'][$ab]['count'];
		}
?>
<script type='text/javascript'>
  //Browsers chart start
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChartBrowser);
      function drawChartBrowser() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Year');
		data.addColumn('number', 'Scan Count');
		data.addRows(<?php if($Browsers <= 4){ echo '4'; } else { echo $Browsers; } ?>);
		<?php
			for($b=0;$b<$Browsers;$b++)
			{
				echo "data.setValue(".$b.", 0, '".$analytics['analytics'][$Duration]['browsers'][$b]['id']."');";
				echo "data.setValue(".$b.", 1, ".$analytics['analytics'][$Duration]['browsers'][$b]['count'].");";
			}
		?>
		var chart = new google.visualization.ColumnChart(document.getElementById('Browsers_div_<?php echo $Duration; ?>'));
		chart.draw(data, {width: 400, height: 330, title: 'Type of browser used',
							hAxis: {titleTextStyle: {color: 'red'}},
							chartArea: {left:60,top:50},
							colors:['#A7C736','#A7C736']
                         });
	}
  //Browsers chart end
  
  //Platforms chart start
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChartPlatforms);
      function drawChartPlatforms() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Year');
		data.addColumn('number', 'Scan Count');
		data.addRows(<?php if($Platforms <= 4){ echo '4'; } else { echo $Platforms; } ?>);
		<?php
			for($c=0;$c<$Platforms;$c++)
			{
				echo "data.setValue(".$c.", 0, '".$analytics['analytics'][$Duration]['platforms'][$c]['id']."');";
				echo "data.setValue(".$c.", 1, ".$analytics['analytics'][$Duration]['platforms'][$c]['count'].");";
			}
		?>

		var chart = new google.visualization.ColumnChart(document.getElementById('Platforms_div_<?php echo $Duration; ?>'));
		chart.draw(data, {width: 400, height: 330, title: 'Type of device used',
							hAxis: {titleTextStyle: {color: 'red'}},
							chartArea: {left:60,top:50},
							colors:['#A7C736','#A7C736']
                         });
	}
  //Platforms chart end
</script>
<?php } ?>
"scanalytics from"
<div id="Browsers_div_" class="chart"></div>
<div id="Platforms_div_" class="chart"></div>
<div class="clear"></div>