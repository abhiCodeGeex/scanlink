<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>ScanLink</title>
</head>
<body>
<?php

$vid = '';
if(isset($_GET['id']) && $_GET['id']== 1){
	$vid = 'c8wWV9mgDYY';
}
if(isset($_GET['id']) && $_GET['id']== 2){
	$vid = '9dHlLi1oc_Y';
}
if(isset($_GET['id']) && $_GET['id']== 3){
	$vid = 'onj4lshalLk';
}
if(isset($_GET['id']) && $_GET['id']== 4){
	$vid = 'kcPUTZS1OIk';
}
?>
<iframe id="ytplayer" type="text/html" width="530" height="345" src="http://www.youtube.com/embed/<?php echo $vid;?>?autoplay=1&origin=http://codesafe.com.au/" frameborder="0"/>
</body>
</html>


