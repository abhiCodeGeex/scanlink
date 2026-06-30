<?php
	if(isset($_FILES["file1"]['tmp_name']))
		echo $_FILES["file1"]['name'];
?>
<!DOCTYPE HTML>
<html>
  <head>
    	<title>Welcome to ScanLink</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
		<meta name='robots' content='noindex,nofollow' />
	</head>
<body>

<form name="frm" id="frm" action="" enctype="multipart/form-data"  method="post">

Select File<br>
<input type="file" name="file1">
<br><br>
<input type="submit" value="Submit">

</form>
</body>
</html>