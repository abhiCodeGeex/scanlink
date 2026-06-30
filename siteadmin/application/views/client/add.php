<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td colspan="3" width="100%" class="h1">&nbsp;</td>
    </tr>
    <tr>
      <td width="62%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg">&nbsp; <strong>Add Client</strong></td>
      <td width="2%">&nbsp;</td>
      <td width="35%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg">&nbsp; <strong>Add User</strong></td>
    </tr>    
    <tr>
      <td width="62%" align="center" style=" <?php echo $succmsg ? 'color:#35D10A;' : 'color:#F00;';?>padding:2px 0px;font-size:12px;"><?php if($post && ($errors || $succmsg)){ echo $errors ? $errors: ($succmsg ? $succmsg: ''); }?></td>
      <td width="2%">&nbsp;</td>
      <td width="35%" align="center" style=" <?php echo $succmsg ? 'color:#35D10A;' : 'color:#F00;';?>padding:2px 0px;font-size:12px;"><?php if($post1 && ($errors || $succmsg)){ echo $errors ? $errors: ($succmsg ? $succmsg: ''); }?></td>
    </tr>
    <tr>
      <td width="100%" colspan="3"><table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><?php echo Form::open(URL::base().'client/add', array('id'=>'frmclient','name'=>'frmclient','method'=>'post')) ?>
                <table align="left" width="100%" cellspacing="0" cellpadding="3" border="0" class="border">
                  <tbody>
                    <tr>
                      <td align="right" class="vgg topleftright" colspan="2">*<font color="black"><b>&nbsp; Indicates Required Information</b></font>&nbsp;&nbsp;</td>
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
                      <td><?php echo "<span style='color:#000;font-size:13px;'>".str_replace('siteadmin/','',URL::base())."</span>".Form::input('txtUrl', $post ? $post['txtUrl'] : '', array('id'=>'txtUrl')) ?><span style='color:#000;font-size:11px;'>/{YOUR PRODUCT ID}</span></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td>(NOTE : Only characters,numbers,underscore and dash are allowed.)</td>
                    </tr>
										<tr>
											<td colspan="2">&nbsp;</td>
										</tr>
										<?php /*
                    <tr>
                      <td align="right"><span style="color: #FF0000;">*</span><strong>&nbsp;Checklist Option:</strong></td>
                      <td><div style="padding:5px 0;">
                      	<input style="width:25px; height:auto" type="radio" value="0" name="checklist_option" id="checklist_option_disallowed" <?php echo !empty($post['checklist_option']) && trim($post['checklist_option']) == "0" ? 'checked="checked"' : '';?> /> <label for="checklist_option_disallowed"> Disallow</label> &nbsp;&nbsp;&nbsp;&nbsp;
                        <input style="width:25px; height:auto" type="radio" value="1" name="checklist_option" id="checklist_option_allowed"  <?php echo !empty($post['checklist_option']) && trim($post['checklist_option']) == "1" ? 'checked="checked"' : '';?>/> <label for="checklist_option_allowed"> Allow</label>
                        </div>
                      </td>
                    </tr>      
                    <tr>
                      <td align="right"><span style="color: #FF0000;">*</span><strong>&nbsp;CustomQR Option:</strong></td>
                      <td><div style="padding:5px 0;">
                      	<input style="width:25px; height:auto" type="radio" value="0" name="customqr_option" id="customqr_option_disabled" <?php echo !empty($post['customqr_option']) && $post['customqr_option'] == "0" ? 'checked="checked"' : '';?> /> <label for="customqr_option_disabled"> Disabled</label> &nbsp;&nbsp;&nbsp;&nbsp;
                        <input style="width:25px; height:auto" type="radio" value="1" name="customqr_option" id="customqr_option_enabled" <?php echo !empty($post['customqr_option']) && $post['customqr_option'] == "1" ? 'checked="checked"' : '';?>/> <label for="customqr_option_enabled"> Enabled</label>
                        </div>
                      </td>
                    </tr>
										*/
										?>
                    <tr>
                      <td>&nbsp;</td>
                      <td><?php echo Form::submit('btnAddclient', 'Add Client', array('id'=>'btnAddclient','class'=>'black-bt')) ?></td>
                    </tr>
                  </tbody>
                </table>
              <?php echo Form::close() ?></td>
              <td width="2%">&nbsp;
              </td>
            <td width="35%" valign="top"><?php echo Form::open(URL::base().'client/add', array('id'=>'frmuser','name'=>'frmuser','method'=>'post')) ?>
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
                      <td align="left" width="75%"><?php //echo Form::input('videopermission', 1, array('id'=>'videopermission','type'=>'checkbox','style'=>'width:auto;')) ?><input type="checkbox" name="videopermission" value="1" id="videopermission" <?php echo (isset($post1['videopermission']) && $post1['videopermission'] == 1) ? "checked='checked'":"";?> style="width:auto;"/></td>
                    </tr>                
                    <tr>
                      <td><?php echo form::hidden("clientid",$last_id ? $last_id[0] : ($post1 ? $post1['clientid']:'')); ?></td>
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
