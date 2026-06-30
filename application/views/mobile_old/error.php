<style type="text/css">
.errormsg{
	text-align:center;
	margin:10px auto;
	height:70px;
	width:200px;
	padding:5px;
	border:5px solid #F30;
	background-color:#FFF;	
}
</style>
<div class="errormsg">
<img src="<?php echo url::base(); ?>images/caution.png" align="Caution" title="<?php echo $message; ?>" /><br />
<?php echo $message; ?>
</div>