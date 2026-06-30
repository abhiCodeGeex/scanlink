<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td colspan="3" width="100%" class="h1">&nbsp;</td>
    </tr>
    <tr>
      <td width="62%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg">&nbsp; <strong>Edit Testimonial</strong></td>
    </tr>    
    <tr>
      <td width="62%" align="center" style=" <?php echo $succmsg ? 'color:#35D10A;' : 'color:#F00;';?>padding:2px 0px;font-size:12px;"><?php if(isset($post['btnEdittestimonial']) && ($errors || $succmsg)){ echo $errors ? $errors: ($succmsg ? $succmsg: ''); }?></td>
    </tr>
    <tr>
      <td width="100%" colspan="3"><table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><?php echo Form::open(URL::base().'testimonial/edit/'.$last_id, array('id'=>'frmedittestimonial','name'=>'frmedittestimonial','method'=>'post')) ?>
                <table align="left" width="100%" cellspacing="0" cellpadding="3" border="0" class="border">
                  <tbody>
                    <tr>
                      <td align="right" class="vgg topleftright" colspan="2">*<font color="black"><b>&nbsp; Indicates Required Information</b></font>&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                    	<td style="height:10px;"></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Title:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txttitle', $post ? $post['txttitle'] : '', array('id'=>'txttitle')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Video embed code:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtvideo', $post ? $post['txtvideo'] : '', array('id'=>'txtvideo')) ?></td>
                    </tr>
										<tr>
											<td colspan="2">&nbsp;</td>
										</tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td><?php echo Form::submit('btnEdittestimonial', 'Edit Testimonial', array('id'=>'btnEdittestimonial','class'=>'black-bt', 'style'=>'width:140px;')) ?></td>
                    </tr>
                    <tr>
                    	<td style="height:10px;"></td>
                    </tr>
                  </tbody>
                </table>
              <?php echo Form::close() ?></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
  </tbody>
</table>