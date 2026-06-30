<div style="float:right;margin-right:10px;">Download the ScanLink User Guide &nbsp; <a href="<?php echo URL::base(); ?>download_file.php?filename=ScanLink_User_ Guide.pdf&filepath=images"><img src="<?php echo URL::base(); ?>images/pdf-download-icon.png" style="vertical-align:middle;" height="30" border="0" /></a></div>
<div class="clear"></div>

<ul class="sub-navigation"><!-- SUB NAVIGATION -->
  <?php
  $curritem = "";
  
  if ($activemenu == "dashboard/people" || "people/edit" == $activemenu || "people/index" == $activemenu)
    $curritem = "class='active'";
  ?>
  <?php /* <li class="first"><a href="<?php echo URL::base(); ?>dashboard/people" <?php echo isset($curritem) && $curritem ? $curritem : ''; ?>>People</a></li> */ ?>

  <?php
  if (isset($equiptypes) && $equiptypes != '') {
    $equipaction = array("dashboard/index", "dashboard/location", "dashboard/asset", "dashboard/product", "dashboard/procedure", "dashboard/customqr", "dashboard/misc","dashboard/code","dashboard/survey","dashboard/exhibit","dashboard/voc");
    $add = array("plant/index", "location/index", "asset/index", "product/index", "procedure/index", "customqr/index", "misc/index","code/index","survey/index","exhibit/index","voc/index");
    $edit = array("plant/edit", "location/edit", "asset/edit", "product/edit", "procedure/edit", "customqr/edit", "misc/edit","code/index","survey/edit","exhibit/edit","voc/edit");

    $counter = 0;
    foreach ($equiptypes as $key => $eqtype) {

      if ($eqtype['id'] == 6)
        continue;
      //if($eqtype['id']==7 && $customqr == '0') continue;
      if ($eqtype['id'] == 7)
        continue;
      if ($eqtype['id'] > 6)
        $key -= 1;
      if ($equipaction[$key] == $activemenu || $add[$key] == $activemenu || $edit[$key] == $activemenu) {
        $curritem = "class='active'";
      } else {
        $curritem = "";
      }
      ?>
      <li <?php echo $counter == "0" ? 'class="first"' : '' ?> >
      	<a href="<?php echo URL::base() . $equipaction[$key]; ?>" <?php echo isset($curritem) && $curritem ? $curritem : ''; ?>>
        	<?php if($eqtype['name'] == 'Code'){ ?>
            	<?php echo 'URL Link'; ?>
            <?php }else{ ?>
	  			<?php echo $eqtype['name']; ?>
            <?php } ?>
         </a>
		 <a href="<?php echo URL::base();?>dashboard/preview/<?php echo $eqtype['id'];?>" class="preview-icon-link">
		 	<img src="<?php echo URL::base();?>images/preview_profile.png" border="0" class="preview-icon" align="absmiddle" />
		 </a>
		 <?php
		 switch ($eqtype['id'])
		 {
		 	case "1" :// plant
				$margin_left = "109px";
				break;
			
			case "2" :// location
				$margin_left = "50px";
				break;
				
			case "3" :// person
				$margin_left = "96px";
				break;
				
			case "4" :// product
				$margin_left = "40px";
				break;
				
			case "5" :// procedure
				$margin_left = "58px";
				break;
				
			case "8" :// misc
				$margin_left = "20px";
				break;
				
			case "9" :// url only
				$margin_left = "49px";
				break;
				
			case "10" :// Survey
				$margin_left = "133px";
				break;
				
			case "11" :// exhibit
				$margin_left = "35px";
				break;
			case "12" :// VOC
				$margin_left = "21px";
				break;
				
		 }
		 
		 ?>
		 <div class="preview-div" style="margin-left: <?php echo $margin_left;?>;">Preview
			<div class="arrow-bg"></div>
		 </div>
       </li>
      <?php
      $counter++;
    }
  }
  ?> 
  <?php 
  		if(isset($colorcode_display) && $colorcode_display == true){
  ?>
  <div class="colorcode_define">
  	<div class="mainbox">
    	<div class="colorcodebox red">&nbsp;</div>
        <div class="colorbox_text">Exipred</div>
    </div>
    <div class="mainbox">
    	<div class="colorcodebox orange">&nbsp;</div>
        <div class="colorbox_text">Expires within 30 days</div>
    </div>
    <div class="mainbox">
    	<div class="colorcodebox green">&nbsp;</div>
        <div class="colorbox_text">Active</div>
    </div>
  </div>
  <?php } ?>
 <?php /* <li><a href="<?php echo URL::base(); ?>dashboard/changepwd" <?php echo isset($activemenu) && $activemenu == 'dashboard/changepwd' ? 'class="active"' : ''; ?>>Change Password</a></li> */?>
</ul><!-- /SUB NAVIGATION -->

<div style="clear:both;"></div>
<script>
$(document).ready(function(){	
		//for preview template
		//hide preview tooltip when mouseleave
		$('.sub-navigation li').on('mouseleave',function(){
			$(this).children('.preview-div').hide();
		});
		
		// show preview tooltip
		$('.preview-icon-link').on('mouseenter',function(){
			$(this).next().show();
		});
		
		//open preview 
		$('.preview-icon').click(function(e){
			$(this).next().hide();
			
			
		});
		
		jQuery(".preview-icon-link").colorbox({
	      iframe:true, 
	      width:"920", 
	      height:"600", 
	      onClosed:function(){
	        //			parent.location.reload()
	      }
	    });
});	
</script>