<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#CCC;">
  <tr height="25px" bgcolor="#EAEAEA">
    <td valign="middle" colspan="2" class="nav"> 
      <?php if (isset($HeadLinksArray)): ?>   
        <ul id="nav" class="dropdown dropdown-horizontal">
          <?php
          foreach ($HeadLinksArray as $HeadValue):
            if ($HeadValue[1] > 0):
              ?>
              <li class="dir"><?php echo $HeadValue[0]; ?><ul>
                  <?php
                  foreach ($HeadValue[2] as $HeadSecondValue):
                    if ($HeadSecondValue[1] > 0):
                      ?>
                      <li class="dir"><a href="<?php echo URL::base() . $HeadSecondValue[3]; ?>"><?php echo $HeadSecondValue[0]; ?></a><ul>
                          <?php
                          foreach ($HeadSecondValue[2] as $HeadThirdValue):
                            ?>
                            <li><a href="<?php echo URL::base() . $HeadThirdValue[2]; ?>"><?php echo $HeadThirdValue[0]; ?></a></li>
                            <?php
                          endforeach;
                          ?>
                        </ul>
                      </li>
                      <?php
                    else:
                      ?>
                      <li><a href="<?php echo URL::base() . $HeadSecondValue[2]; ?>"><?php echo $HeadSecondValue[0]; ?></a></li>
                    <?php
                    endif;
                  endforeach;
                  ?>
                </ul>
              </li>
              <?php
            else:
              ?>
              <li><a href="<?php echo URL::base() . $HeadValue[2]; ?>"><?php echo $HeadValue[0]; ?></a></li>
            <?php
            endif;
          endforeach;
          ?>      
        </ul>
      <?php endif; ?></td>
  </tr>
</table>
