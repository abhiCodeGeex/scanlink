<table align="left" width="100%" cellspacing="0" cellpadding="3" border="0">
  <tr>
    <td width="35%" class="orange13"><img width="13" height="13" src="<?php print URL::base();?>images/arrow2.jpg">&nbsp; <strong>Edit User</strong></td>
  </tr>
  <tr>    
    <td align="center" width="35%" style=" <?php echo $successmsg ? 'color:#35D10A;' : 'color:#F00;';?> padding:2px 0px;font-size:12px;"><?php echo $successmsg ? $successmsg : ($errmsg ? $errmsg : '');?></td>
  </tr>
  <tr>
    <td><?php echo Form::open(URL::base().'user/edit/'.$userid, array('id'=>'frmedituser','name'=>'frmedituser','method'=>'post')) ?>
      <table align="left" width="100%" cellspacing="0" cellpadding="3" border="0" class="border">
        <tbody>
          <tr>
            <td align="right" class="vgg topleftright" colspan="2">*<font color="black"><b>&nbsp; Indicates Required Information</b></font>&nbsp;&nbsp;</td>
          </tr>
          <tr>
            <td align="right" width="25%">&nbsp;</td>
            <td align="left" width="75%">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Email:</strong></td>
            <td align="left" width="75%"><?php echo Form::input('txtUseremail', $post1 ? $post1['txtUseremail'] : '', array('id'=>'txtUseremail')) ?></td>
          </tr>
          <tr>
            <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Password:</strong></td>
            <td align="left" width="75%"><?php echo Form::input('txtUserpassword', $post1 ? $post1['txtUserpassword'] : '', array('id'=>'txtUserpassword','type'=>'text')) ?></td>
          </tr>
          <?php /*
          <tr>
             <td align="right" width="25%"><strong>&nbsp;Video Upload Permission:</strong></td>
             <td align="left" width="75%"><?php //echo Form::input('videopermission', 1, array('id'=>'videopermission','type'=>'checkbox','style'=>'width:auto;')) ?><input type="checkbox" name="videopermission" value="1" id="videopermission" <?php echo (isset($post1['videopermission']) && $post1['videopermission'] == 1) ? "checked='checked'":"";?> style="width:auto;"/></td>
          </tr> 
		  */
		  ?>
          <tr>
            <td><?php echo form::hidden("clientid",$userid ? $userid : ($post1 ? $post1['clientid']:'')); ?></td>
            <td><?php echo Form::submit('btnEdituser', 'Edit User', array('id'=>'btnEdituser','class'=>'black-bt')) ?></td>
          </tr>
          <tr>
            <td align="right" width="25%">&nbsp;</td>
            <td align="left" width="75%">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" width="25%">&nbsp;</td>
            <td align="left" width="75%">&nbsp;</td>
          </tr>
        </tbody>
      </table>
      <?php echo Form::close() ?></td>
  </tr>
</table>
