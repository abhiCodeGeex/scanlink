<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td class="H1" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="50%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg"> &nbsp;<strong>Manage Gallery </strong></td>
      <td align="right" width="50%"><p class="fltrt">&nbsp;&nbsp;&nbsp;</p>
        <a class="fltrt" style="font-size:16px;" title="Add client" href="<?php echo URL::base();?>gallery/add"><img src="<?php echo URL::base();?>images/add-client.png" /> Add Image(s)</a></td>
    </tr>
    <tr>
      <td align="left" height="8px" colspan="2"></td>
    </tr>
    <tr>
      <td background="images/vdots.gif" colspan="2"><img width="1" height="1" border="0" src="images/spacer.gif"></td>
    </tr>
    <tr>
      <td colspan="2"><table class="client-list" width="100%" height="89" cellspacing="1" cellpadding="3" border="0">
          <!--DWLayoutTable-->
          <tbody>
            <tr>
              <td height="5" colspan="3"></td>
            </tr>
            <tr class="th-a">
              <td align="center" height="27" width="20%" class="vgg first"><strong>Image ID</strong></td>
              <td align="center" width="20%" class="vgg"><strong>Image</strong></td>
              <!--<td align="center" width="10%" class="vgg"><strong>Telephone</strong></td>
            <td align="center" width="15%" class="vgg"><strong>Contact Person</strong></td>
            <td align="center" width="10%" class="vgg"><strong>Registration Date</strong></td>
            <td align="center" width="15%" class="vgg"><strong>Email</strong></td>
            <td align="center" width="10%" class="vgg"><strong>URL</strong></td>-->
              <td align="center" height="27" class="vgg last"><strong>Option</strong></td>
            </tr>
          <tbody id="records">
            <?php 
				if(isset($images) && !empty($images)):
					$imgact = '';					
					foreach($images as $image):
						/*$ext = pathinfo($image['name'],PATHINFO_EXTENSION);
						$fnm = pathinfo($image['name'],PATHINFO_FILENAME);
						$fnm = $fnm."_thumb.".$ext;*/
						$fname = $image['name'];
						$thumb = "thumb_".$fname;
						if($image['approve'] == 1):
							$imgact = 'active';
						else:
							$imgact = 'blocked';
						endif;						
						?>
            <tr bgcolor="#CCCCCC">
              <td align="left" valign="middle" class="vgg1"><?php echo $image['id']?></td>
              <td align="center" valign="middle" class="vgg1"><img src="<?php echo kohana::$base_site_url."images/galleryApi/fullscreen/".$thumb;?>" /></td>
              <?php /*?><td align="left" valign="middle" class="vgg1"><?php echo $client['telephone']?></td>
            <td align="left" valign="middle" class="vgg1"><?php echo $client['contact_person']?></td>
            <td align="left" valign="middle" class="vgg1"><?php echo $client['regi_date']?></td>
            <td align="left" valign="middle" class="vgg1"><?php echo $client['email']?></td>
            <td align="left" valign="middle" class="vgg1"><?php echo $client['url']?></td><?php */?>
              <td align="center" class="vgg1"><div class="optionwrap"><a href="<?php echo URL::base();?>gallery/blocked/<?php echo $image['id']?>" onclick="if(confirm('Are you sure to <?php echo $imgact == 'active' ? 'blocked':'active'; ?> this Image?')){ return true; }else{ return false; }" class="option" title="click to <?php echo $imgact == 'active' ? 'blocked':'active'; ?>"> <img src="<?php echo URL::base();?>images/<?php echo $imgact;?>.png" width="20" /> </a> <a href="<?php echo URL::base();?>gallery/delete/<?php echo $image['id']?>" onclick="if(confirm('Are you sure to delete this image?')){ return true; }else{ return false; }" class="option mlft" title="Delete client"> <img src="<?php echo URL::base();?>images/delete.png" width="20" /> </a> </div></td>
            </tr>
            <?php
					endforeach;
				endif;
			?>
            <tr align="center">
              <td colspan="3"></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>
<script type="text/javascript">        
$(function(){
	itemsPerPage = 20;
	colSpan = 8;
	paginatorStyle = 6;
	paginatorPosition = 'bottom';
	enableGoToPage = false;
	showIfSinglePage = false;
	$("#records").pagination();
});
</script>