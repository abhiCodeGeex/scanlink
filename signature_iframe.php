<!DOCTYPE HTML>
<html lang="en">
<head>
  	<meta charset="utf-8">
  	<title>Signature Pad demo</title>
  
  	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />

  	<meta name="apple-mobile-web-app-capable" content="yes" />
  	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<!--[if lt IE 9]><script src="js/flashcanvas.js"></script><![endif]-->
	<!--[if IEMobile]><![if lt IE 10]> <script src="js/flashcanvas.js"></script> <![endif]><![endif]-->
	

  

<style >
canvas
{
	border:1px solid #DDDDDD;
	
	background-image: url("form-builder/images/writing-hand-icon.png");
  	background-repeat: no-repeat;
	background-position: 99% 97%;	
    -ms-touch-action: none;
    -ms-user-select: none;
    user-select: none;
	
}

.button.clear
{
	background:none;
	height: auto;
	background-color: #808080;
	cursor: pointer;
	
	border-radius: 5px;
	border: 1px solid #808080;
	color: #ffffff;
	padding: 5px;
	margin-top: 8px;
}
.sigPad {
  margin: 0;
  padding: 0;
  height: 250px;
}

.sigWrapper {
  clear: both;
  height: 200px; 
   -ms-touch-action: none;
   -ms-user-select: none;
    user-select: none;
}



</style>
</head>
<body >
<form method="post" action="" class="sigPad">
	
	
    <div class="sig sigWrapper">
      <div class="typed"></div>
      <canvas class="pad" id="canvas_id">Your browser does not support the HTML5 canvas tag.</canvas>
	  
	  
      <input type="hidden" id="output" name="output" class="output">
      <input type="hidden" name="sign_width" id="sign_width">
	  
    </div>
	
	
	<button class="button clear clearButton" data-action="clear"  type="button">Clear Signature</button>
	
 </form>

<script src="form-builder/js/jquery-1.9.1.js"></script> 
<script src="js/jquery.signaturepad.js"></script>
<script  >
	$(document).ready(function() {
	 resizeCanvas();
		
      $('.sigPad').signaturePad({
	  	drawOnly:true
		});
		
		//width =  Math.ceil((window.innerWidth*86)/100);
		width =  Math.ceil(($(window).innerWidth()*86)/100);
		
		
	 	$('#canvas_id').attr('height',200);
	 	$('#canvas_id').attr('width',width);
		
	  	$('#sign_width').val(width); //store width in hidden
		
    });
	
	
	function resizeCanvas() {
	    
		//width = Math.ceil((window.innerWidth*86)/100);
		width =  Math.ceil(($(window).innerWidth()*86)/100);
		$('#canvas_id').attr('width',width);
	  	$('#sign_width').val(width); //store width in hidden
		
	}

	window.onresize = resizeCanvas;
	
	
	
	
  	
    </script>
</body>
</html>