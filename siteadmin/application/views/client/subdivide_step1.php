<div class="container">
<div class="contant inner-box">

<form method="post" name="frm_subdivide_client" id="frm_subdivide_client" class="subdivide">
<div class="penal">
<h3>
     <span class="fleft">*&nbsp;Please Select Client from which you want to get Profiles and Users</span>
     <span class="fright">&nbsp;</span>
	<div class="clear"></div>
</h3>
<?php if( ($errors || $succmsg)){ ?>
<div class="main-row">
	<div class="row"></div>
	<div class="row-r">
		<div class="mymsg" style=" <?php echo $succmsg ? 'color:#35D10A' : 'color:#F00';?>; <?php echo $succmsg ? 'border-color:#35D10A' : 'border-color:#F00';?>;"><?php if( ($errors || $succmsg)){ echo $errors ? $errors: ($succmsg ? $succmsg: 'k'); }?></div>
	</div>
</div>
<?php } ?>

<div class="main-row">
	<div class="row">Select Client</div>
     <div class="row-r">
          <select id="client" name="client" class="input">
               <option value="">Select</option>
               <?php foreach($clients as $client){ ?>
               <option value="<?php echo $client['id']; ?>"><?php echo $client['client_name']; ?></option>
               <?php } ?>
          </select>	
     </div>
</div>
<div class="main-row">
	<div class="row">&nbsp;</div>
     <div class="row-r">
     	<button type="submit" value="Go to next step" name="next" id="next">Go to next step</button>
     </div>
</div>


</div>
</form>
</div>
</div>