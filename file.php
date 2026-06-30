<?php 
	$url = "http://propertycode.com.au/web/qrcode/code.php?type=URL&height=165&width=165&block_size=5&url=http://www.google.co.in&foreground_color=397f5c&border_size=4&background_color=FFFFFF&block_style=square_blocks";
//	$url = "http://ravindra/scanlink/web/qrcode/code.php?type=URL&height=165&width=165&block_size=5&url=http://www.google.co.in&foreground_color=397f5c&border_size=4&background_color=FFFFFF&block_style=square_blocks";
	echo $var = file_get_contents($url);

		
	
?>