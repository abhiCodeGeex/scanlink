<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td colspan="3" width="100%" class="h1">&nbsp;</td>
    </tr>
    <tr>
      <td width="62%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg">&nbsp; <strong>Edit Client</strong></td>
      <td width="2%">&nbsp;</td>
      <td width="35%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg">&nbsp; <strong>Add User</strong></td>
    </tr>    
    <tr>
      <td width="62%" align="center" style=" <?php echo $succmsg ? 'color:#35D10A;' : 'color:#F00;';?>padding:2px 0px;font-size:12px;"><?php if(isset($post['btnEditclient']) && ($errors || $succmsg)){ echo $errors ? $errors: ($succmsg ? $succmsg: ''); }?></td>
      <td width="2%">&nbsp;</td>
      <td width="35%" align="center" style=" <?php echo $succmsg ? 'color:#35D10A;' : 'color:#F00;';?>padding:2px 0px;font-size:12px;"><?php if($post1 && ($errors || $succmsg)){ echo $errors ? $errors: ($succmsg ? $succmsg: ''); }?></td>
    </tr>
    <tr>
      <td width="100%" colspan="3"><table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><?php echo Form::open(URL::base().'client/edit/'.$last_id, array('id'=>'frmeditclient','name'=>'frmeditclient','method'=>'post')) ?>
                <table align="left" width="100%" cellspacing="0" cellpadding="3" border="0" class="border">
                  <tbody>
                    <tr>
                      <td align="right" class="vgg topleftright" colspan="2">*<font color="black"><b>&nbsp; Indicates Required Information</b></font>&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                    	<td style="height:10px;"></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Client Name:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtClientname', $post ? $post['txtClientname'] : '', array('id'=>'txtClientname')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Address:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtAddress', $post ? $post['txtAddress'] : '', array('id'=>'txtAddress')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Telephone:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtTelephone', $post ? $post['txtTelephone'] : '', array('id'=>'txtTelephone')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Contact Person:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtContactperson', $post ? $post['txtContactperson'] : '', array('id'=>'txtContactperson')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Registration date:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtRegidate', $post ? $post['txtRegidate'] : '', array('id'=>'txtRegidate')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Email:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtEmail', $post ? $post['txtEmail'] : '', array('id'=>'txtEmail')) ?></td>
                    </tr>
                    <tr>
                      <td align="right"><span style="color: #FF0000;">*</span><strong>&nbsp;Password:</strong></td>
                      <td align="left"><?php echo Form::input('txtPassword', $post ? $post['txtPassword'] : '',array('type'=>'text','id'=>'txtPassword')) ?></td>
                    </tr>
                    <tr>
                      <td align="right"><span style="color: #FF0000;">*</span><strong>&nbsp;URL:</strong></td>
                      <td><?php echo Form::input('txtUrl', $post ? $post['txtUrl'] : '', array('id'=>'txtUrl','readonly'=>'readonly')) ?></td>
                    </tr>                    
                    <tr>
                      <td>&nbsp;</td>
                      <td><?php echo Form::submit('btnEditclient', 'Edit Client', array('id'=>'btnEditclient','class'=>'black-bt')) ?></td>
                    </tr>
                    <tr>
                    	<td style="height:10px;"></td>
                    </tr>
                  </tbody>
                </table>
              <?php echo Form::close() ?></td>
              <td width="2%">&nbsp;
              </td>
            <td width="35%" valign="top"><?php echo Form::open(URL::base().'client/edit/'.$last_id, array('id'=>'frmuser','name'=>'frmuser','method'=>'post')) ?>
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
                    <tr>
                      <td align="right" width="25%"><strong>&nbsp;Video Upload Permission:</strong></td>
                      <td align="left" width="75%"><input type="checkbox" name="videopermission" value="1" id="videopermission" <?php echo (isset($post1['videopermission']) && $post1['videopermission'] == 1) ? "checked='checked'":"";?> style="width:auto;"/></td>
                    </tr>               
                    <tr>
                      <td><?php echo form::hidden("clientid",$last_id ? $last_id : ($post1 ? $post1['clientid']:'')); ?></td>
                      <td><?php echo Form::submit('btnAdduser', 'Add User', array('id'=>'btnAdduser','class'=>'black-bt')) ?></td>
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
        </table></td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
  </tbody>
</table>
<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td class="H1" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="50%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg"> &nbsp;<strong>Manage
        User </strong></td>
      <td align="right" width="50%"></td>
    </tr>
    <tr>
      <td align="left" height="8px" colspan="2"></td>
    </tr>
    <tr>
      <td background="images/vdots.gif" colspan="2"><img width="1" height="1" border="0" src="images/spacer.gif"></td>
    </tr>
    <tr>
      <td colspan="2">
        <table class="client-list" width="100%" cellspacing="1" cellpadding="3" border="0">
          <!--DWLayoutTable-->
          <tbody>            
            <tr>
              <td height="5" colspan="6"></td>
            </tr>
            <tr class="th-a">
              <td align="center" height="27" width="50%" class="vgg first"><strong>Email Address</strong></td>
              <td align="center" height="27" class="vgg last"><strong>Option</strong></td>
            </tr>
            <?php 
				if(isset($users) && $users):
					$imgact = '';
					$imgper = '';
					foreach($users as $user):
						if($user['status'] == 1):
							$imgact = 'active';
						else:
							$imgact = 'blocked';
						endif;
						if($user['video_upload'] == 1):
							$imgper = 'youtube-yes';
						else:
							$imgper = 'youtube-no';
						endif;
						?>
                        <tr bgcolor="#CCCCCC">
                          <td align="left" valign="middle" class="vgg1"><?php echo $user['email']?></td>
                          <td align="center" class="vgg1"><div class="optionwrap"> <a href="<?php echo URL::base();?>user/edit/<?php echo $user['id']?>/<?php echo $user['client_id']?>/cled" class="option mlftrt edt" title="Edit user"> <img src="<?php echo URL::base();?>images/edit.png" width="20" /> </a> <a href="<?php echo URL::base();?>user/blocked/<?php echo $user['id']?>/<?php echo $user['client_id']?>/cled" onclick="if(confirm('Are you sure to <?php echo $imgact == 'active' ? 'blocked':'active'; ?> this user?')){ return true; }else{ return false; }" class="option" title="click to <?php echo $imgact == 'active' ? 'blocked':'active'; ?>"> <img src="<?php echo URL::base();?>images/<?php echo $imgact;?>.png" width="20" /> </a> <a href="<?php echo URL::base();?>user/delete/<?php echo $user['id']?>/<?php echo $user['client_id']?>/cled" onclick="if(confirm('Are you sure to delete this user?')){ return true; }else{ return false; }" class="option mlft" title="Delete user"> <img src="<?php echo URL::base();?>images/delete.png" width="20" /> </a> <a href="<?php echo URL::base();?>user/videop/<?php echo $user['id'];?>/<?php echo $user['client_id']?>/cled" onclick="if(confirm('Are you sure to change video permission to client?')){ return true; }else{ return false; }" class="option mlft" title="video permission to client"> <img src="<?php echo URL::base();?>images/<?php echo $imgper;?>.png" width="20" /> </a></div></td>
                        </tr>
                        <?php
					endforeach;
				endif;
			?>            
            <tr align="center">
              <td colspan="6"></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>
