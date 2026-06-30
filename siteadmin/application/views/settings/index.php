<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td colspan="3" width="100%" class="h1">&nbsp;</td>
    </tr>
    <tr>
      <td width="62%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg">&nbsp; <strong>Global Settings</strong></td>
      <td width="2%">&nbsp;</td>
    </tr>    
    <tr>
      <td width="62%" align="center" style=" <?php echo $succmsg ? 'color:#35D10A;' : 'color:#F00;';?>padding:2px 0px;font-size:12px;"><?php if($errors || $succmsg){ echo $errors ? $errors: ($succmsg ? $succmsg: ''); }?></td>
      <td width="2%">&nbsp;</td>     
    </tr>
    <tr>
      <td width="100%" colspan="3"><table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><?php echo Form::open(URL::base().'settings/index', array('id'=>'frmsettings','name'=>'frmsettings','method'=>'post')) ?>
                <table align="left" width="100%" cellspacing="0" cellpadding="3" border="0" class="border">
                  <tbody>
                    <tr>
                      <td align="right" class="vgg topleftright" colspan="2">*<font color="black"><b>&nbsp; Indicates Required Information</b></font>&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                    	<td style="height:10px;"></td>
                    </tr>
                    <?php 
					if(isset($settings) && !empty($settings)){
						foreach($settings as $key=>$val){
					?>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;<?php echo ucwords(str_replace('_',' ',$val['title']));?>:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input($val["title"], $val['values'] ? $val['values'] : '', array('id'=>$val["title"])) ?></td>
                    </tr>
                    <?php 
						}
					}
					/*?><tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Address:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtAddress', $settings ? $settings['txtAddress'] : '', array('id'=>'txtAddress')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Telephone:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtTelephone', $settings ? $settings['txtTelephone'] : '', array('id'=>'txtTelephone')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Contact Person:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtContactperson', $settings ? $settings['txtContactperson'] : '', array('id'=>'txtContactperson')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Registration date:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtRegidate', $settings ? $settings['txtRegidate'] : '', array('id'=>'txtRegidate')) ?></td>
                    </tr>
                    <tr>
                      <td align="right" width="25%"><span style="color: #FF0000;">*</span><strong>&nbsp;Email:</strong></td>
                      <td align="left" width="75%"><?php echo Form::input('txtEmail', $settings ? $settings['txtEmail'] : '', array('id'=>'txtEmail')) ?></td>
                    </tr>
                    <tr>
                      <td align="right"><span style="color: #FF0000;">*</span><strong>&nbsp;Password:</strong></td>
                      <td align="left"><?php echo Form::input('txtPassword', $settings ? $settings['txtPassword'] : '',array('type'=>'text','id'=>'txtPassword')) ?></td>
                    </tr>
                    <tr>
                      <td align="right"><span style="color: #FF0000;">*</span><strong>&nbsp;URL:</strong></td>
                      <td><?php echo Form::input('txtUrl', $settings ? $settings['txtUrl'] : '', array('id'=>'txtUrl','readonly'=>'readonly')) ?></td>
                    </tr><?php */?>                    
                    <tr>
                      <td>&nbsp;</td>
                      <td><?php echo Form::submit('btnsettings', 'Edit Settings', array('id'=>'btnsettings','class'=>'black-bt')) ?></td>
                    </tr>
                    <tr>
                    	<td style="height:10px;"></td>
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