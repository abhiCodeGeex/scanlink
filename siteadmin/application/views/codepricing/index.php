<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td colspan="3" width="100%" class="h1">&nbsp;</td>
    </tr>
    <tr>
      <td width="62%" class="orange13"><img width="13" height="13" src="<?php echo URL::base(); ?>images/arrow2.jpg">&nbsp; <strong>Update Code Pricing</strong></td>
      <td width="2%">&nbsp;</td>
    </tr>    
    <tr>
      <td width="62%" align="center" style=" <?php echo $succmsg ? 'color:#35D10A;' : 'color:#F00;'; ?>padding:2px 0px;font-size:12px;"><?php
if ($errors || $succmsg) {
  echo $errors ? $errors : ($succmsg ? $succmsg : '');
}
?></td>
      <td width="2%">&nbsp;</td>     
    </tr>
    <tr>
      <td width="100%" colspan="3"><table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><?php echo Form::open(URL::base() . 'codepricing/index', array('id' => 'frmcodeprising', 'name' => 'frmcodeprising', 'method' => 'post')) ?>
              <table align="left" width="100%" cellspacing="0" cellpadding="3" border="0" class="border">
                <tbody>
                  <tr>
                    <td align="right" class="vgg topleftright" colspan="4">*<font color="black"><b>&nbsp; Indicates Required Information</b></font>&nbsp;&nbsp;</td>
                  </tr>
                  <tr>
                    <td style="height:10px;"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><strong>Retail Pricing</strong></td>
                    <td>&nbsp;</td>
                    <td><strong>Reseller Pricing</strong></td>
                  </tr>
                  <tr>
                    <td style="height:10px;"></td>
                  </tr>
                  <?php
                  if (isset($codePrisingList) && !empty($codePrisingList)) {
                    foreach ($codePrisingList as $key => $val) {
                      ?>
                      <tr>
                        <td align="right" width="25%">
                          <strong>Codes &nbsp;<?php echo ucwords($val['code_min_qty']); ?> to <?php echo ucwords($val['code_max_qty']); ?>:</strong>
                        </td>
                        <td align="left" width="25%">
							<?php echo Form::input($val["id"], $val['amount'] ? $val['amount'] : '', array('id' => $val["id"])) ?>
                        </td>
                        <td align="right" width="25%">
                          <strong>Codes &nbsp;<?php echo ucwords($val['code_min_qty']); ?> to <?php echo ucwords($val['code_max_qty']); ?>:</strong>
                        </td>
                        <td align="left" width="25%">
							<?php echo Form::input($val["id"], $val['reseller_amount'] ? $val['reseller_amount'] : '', array('id' => $val["id"])) ?>
                        </td>
                      </tr>
                      <?php
                    }
                  }
                  ?>
                  <tr>
                    <td>&nbsp;</td>
                    <td><?php echo Form::submit('btnsettings', 'Edit Pricing', array('id' => 'btnsettings', 'class' => 'black-bt'));?></td>
                    <td>&nbsp;</td>
                    <td><?php echo Form::submit('btnresellersettings', 'Edit Pricing', array('id' => 'btnresellersettings', 'class' => 'black-bt'));?></td>
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