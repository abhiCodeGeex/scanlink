<p style="height:25px;">&nbsp;</p>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr> 
      <!--<td width="211" valign="top"></td>-->
      <td valign="top"><table align="center" width="70%" cellspacing="0" cellpadding="0" border="0">
          <tbody>
            <tr>
              <td height="45" class="orange16"><strong>Admin Control Panel</strong></td>
            </tr>
            <tr>
              <td class="border"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tbody>
                    <tr>
                      <?php
                      if (isset($menu)):
                        foreach ($menu as $menuitems):
                          if ($menuitems[1] > 0):
                            ?>
                            <td width="25%" valign="top"><table align="center" width="94%" cellspacing="0" cellpadding="0" border="0">
                                <tbody>
                                  <tr>
                                    <td height="35" class="tahoma14"><strong><?php echo $menuitems[0]; ?></strong></td>
                                  </tr>
                                  <tr>
                                    <?php
                                    foreach ($menuitems[2] as $submenuitems):
                                      if ($submenuitems[1] > 0):
                                        ?>
                                        <td align="left" valign="top" height="70"><table width="90%" cellspacing="2" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                <td align="center" width="8%"><img width="4" height="4" src="<?php echo URL::base() ?>images/arrow.gif"></td>
                                                <td width="94%"><a class="gray11-link" href="<?php echo URL::base() . $submenuitems[3]; ?>"><b><?php echo $submenuitems[0]; ?></b></a></td>
                                              </tr>
                                              <?php
                                              foreach ($submenuitems[2] as $subsubmenuitems):
                                                ?>
                                                <tr>
                                                  <td align="center" width="8%">&nbsp;</td>
                                                  <td align="left"><table align="left" width="100%" border="0">
                                                      <tbody>
                                                        <tr>
                                                          <td><a class="gray11-link" href="<?php echo URL::base() . $subsubmenuitems[2]; ?>"><?php echo $subsubmenuitems[0]; ?></a></td>
                                                        </tr>
                                                      </tbody>
                                                    </table></td>
                                                </tr>
                                              <?php endforeach; ?>
                                            </tbody>
                                          </table></td>
                                        <?php
                                      endif;
                                    endforeach;
                                    ?>
                                  </tr>
                                </tbody>
                              </table></td>
                            <?php
                          endif;
                        endforeach;
                      endif;
                      ?>
                    </tr>
                  </tbody>
                </table></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
    </tr>
  </tbody>
</table>
