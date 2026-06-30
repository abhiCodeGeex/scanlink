<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td colspan="3" width="100%" class="h1">&nbsp;</td>
    </tr>
    <tr>
      <td width="100%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg">&nbsp; <strong>Change Password</strong></td>
    </tr>    
    <tr>
      <td width="100%" align="center" style=" <?php echo $succmsg ? 'color:#35D10A;' : 'color:#F00;';?>padding:2px 0px;font-size:12px;"><?php if($errors || $succmsg){ echo $errors ? $errors: ($succmsg ? $succmsg: ''); }?></td>
    </tr>
    <tr>
      <td width="100%" colspan="3"><table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><?php echo Form::open(URL::base().'password/index', array('id'=>'frmchangepass','name'=>'frmchangepass','method'=>'post')) ?>
                <table align="left" width="100%" cellspacing="0" cellpadding="3" border="0" class="border">
                  <tbody>
                    <tr>
                      <td align="right" class="vgg topleftright" colspan="2">*<font color="black"><b>&nbsp; Indicates Required Information</b></font>&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="2" style="height:10px;"></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Old Password:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtoldpass', $post ? $post['txtoldpass'] : '', array('id'=>'txtoldpass','type'=>'password')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;New Password:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtnewpass', $post ? $post['txtnewpass'] : '', array('id'=>'txtnewpass','type'=>'password')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Confirm Password:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtconfirmpass', $post ? $post['txtconfirmpass'] : '', array('id'=>'txtconfirmpass','type'=>'password')) ?></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td><?php echo Form::submit('btnChangePass', 'Change Password', array('id'=>'btnChangePass','class'=>'black-bt','style'=>'width:135px;')) ?></td>
                    </tr>
                    <tr>
                      <td colspan="2" style="height:10px;"></td>
                    </tr>
                  </tbody>
                </table>
              <?php echo Form::close() ?></td>
              <td width="2%">&nbsp;</td>            
          </tr>
        </table></td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
  </tbody>
</table>
