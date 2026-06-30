<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){
	
		$('.add_field').click(function(){
	
			var input = $('#gallery');
			var clone = input.clone(true);
			clone.removeAttr ('id');
			clone.val('');
			clone.appendTo('.input_holder'); 
			
		});

		$('.remove_field').click(function(){
		
			if($('.input_holder input:last-child').attr('id') != 'gallery'){
				$('.input_holder input:last-child').remove();
			}
		
		});
	
	});
	</script>
<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td width="100%" class="h1">&nbsp;</td>
    </tr>
    <tr>
      <td class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg">&nbsp; <strong>Add Gallery</strong></td>
    </tr>    
    <tr>
      <td align="center" style="padding:2px 0px;font-size:12px;"></td>      
    </tr>
    <tr>
      <td width="100%" colspan="3"><table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><?php echo Form::open(URL::base().'gallery/add', array('id'=>'frmgallery','name'=>'frmgallery','method'=>'post','enctype'=>'multipart/form-data')) ?>
                <table align="left" width="100%" cellspacing="0" cellpadding="3" border="0" class="border">
                  <tbody>
                    <tr>
                      <td align="right" valign="top" width="25%"><strong>&nbsp;Upload Image:&nbsp;</strong></td>
                      <td align="left" width="75%"><input type="file" name="gallery[]" id="gallery" /><span class="add_field"><img src="<?php echo URL::base();?>images/plus.png"></span><span class="remove_field"><img src="<?php echo URL::base();?>images/minus.png"></span><br /><div class="input_holder"></div></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td><?php echo Form::submit('btnAddGallery', 'Add Image(s)', array('id'=>'btnAddGallery','class'=>'black-bt')) ?></td>
                    </tr>
                  </tbody>
                </table>
              <?php echo Form::close() ?></td>            
          </tr>
        </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>
