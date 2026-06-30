<style type="text/css">.editdlt{ width:60px; }</style>
<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td class="H1" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="50%" class="orange13"><img width="13" height="13" src="<?php echo URL::base(); ?>images/arrow2.jpg"> &nbsp;<strong>Manage Form Builder Orders</strong></td>
      <td align="right" width="50%">&nbsp;</td>
    </tr>
    <tr>
      <td align="left" height="8px" colspan="2"></td>
    </tr>
    <tr>
      <td background="images/vdots.gif" colspan="2"><img width="1" height="1" border="0" src="images/spacer.gif"></td>
    </tr>
    <tr>
      <td colspan="2" align="right">
        <form method="post" name="frm_status" id="frm_status" action="">
          <span>View Orders by Status</span> :
          <select id="orderByStatus" name="status">
            <option <?php if ($status == 'All') { ?> selected="selected" <?php } ?> value="All">All</option>
            <option <?php if ($status == '0') { ?> selected="selected" <?php } ?> value="0">New</option>
            <option <?php if ($status == '2') { ?> selected="selected" <?php } ?> value="2">Invoice Send</option>
            <option <?php if ($status == '3') { ?> selected="selected" <?php } ?> value="3">Paid</option>
          </select>
        </form>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <form method="post" action="#" name="form2">
          <table width="100%" height="89" cellspacing="1" cellpadding="3" border="0">
            <!--DWLayoutTable-->
            <thead>
              <tr class="th-a">
			  	<td align="left" width="5%" class="vgg"><strong>ID</strong></td>
                <td align="left" height="27" class="vgg"><strong>Full Name</strong></td>
                <td align="left" width="10%" class="vgg"><strong>Zipcode</strong></td>
                <td align="left" width="20%" class="vgg"><strong>Email</strong></td>
                <td align="left" width="15%" class="vgg"><strong>Phone no</strong></td>
                <td align="left" width="10%" class="vgg"><strong>Order Type</strong></td>                    
                <td align="left" width="10%" class="vgg"><strong>Profile ID</strong></td>                    
                <td align="center" width="15%" height="27" class="vgg"><strong>Option</strong></td>
              </tr>
            </thead>
            <tbody id="records">

              <?php
              /* echo "<pre>";
                print_r ($orders);
                echo "</pre>";
                exit; */
              ?>

              <?php foreach ($orders as $order) { ?>
                <tr bgcolor="#CCCCCC">
                  <td align="left" valign="middle" class="vgg1"><?php echo $order["id"]; ?></td>
				  <td align="left" valign="middle" class="vgg1"><?php echo $order["first_name"]; ?> <?php echo $order["last_name"]; ?></td>
                  <td align="left" valign="middle" class="vgg1"><?php echo $order["postal_code"]; ?></td>                    
                  <td align="left" valign="middle" class="vgg1"><?php echo $order["email"]; ?></td>
                  <td align="left" valign="middle" class="vgg1"><?php echo $order["phone"]; ?></td>
                  <td align="left" valign="middle" class="vgg1">
                    <?php
                    switch ($order["status"]) {
                      case '0':
                        echo "New";
                        break;
                      case '1':
                        echo "Renew";
                        break;
                      case '2':
                        echo "Invoice Send";
                        break;
                      case '3':
                        echo "Paid";
                        break;
                      case '4':
                        echo "Cancelled";
                        break;
                    }
                    ?>
                  </td>
                  <td><?php echo $order['profile_id'] ?></td>
                  <td align="center" class="vgg1">
                    <input type="button" class="black-bt editdlt" onclick="location.href='<?php echo url::base(); ?>formbuilderorder/view/<?php echo $order['id']; ?>'" value="View" name="view">

                    <input type="hidden" value="" id="up_val" name="up_val">
                    <input type="hidden" value="" id="down_val" name="down_val">
                    <input type="hidden" value="" id="cur_id" name="cur_id">
                  </td>
                </tr>
<?php } ?>
            </tbody>
            <tfoot>
              <tr align="center">
                <td colspan="6"></td>
              </tr>
            </tfoot>
          </table>
        </form>
      </td>
    </tr>
  </tbody>
</table>
<script type="text/javascript" language="javascript">
  $(document).ready(function(){
    $("#orderByStatus").change(function(){
      $('#frm_status').submit();
    });
  });
  $(function(){
    itemsPerPage = 20;
    paginatorStyle = 6;
    paginatorPosition = 'bottom';
    enableGoToPage = false;
    showIfSinglePage = false;
    $("#records").pagination();
  });
</script>