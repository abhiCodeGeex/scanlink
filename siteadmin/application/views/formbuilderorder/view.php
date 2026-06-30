<style type="text/css">
  table table td{
    margin:1px;
    background-color:#EEE;
    height:25px;
    line-height:25px;
    vertical-align:middle;
  }

  .order_head{
    font-size:16px;
    background-color:#CCC;
    border:1px solid #999;
    color:#FFF;
    padding-left:10px;

  }

  .lbl, .val{
    font-size:14px;
    padding-left:10px;
    padding-right:10px;
    height:25px;
    line-height:25px;
    vertical-align:middle;
  }

  table table{
    border-left:1px solid #999;
    border-bottom:1px solid #999;
    border-right:1px solid #999;
    border-top:1px solid #999;
  }
  .tr_head th{
    background-color:#CCC;

  }
  .order_main{
    border:none 0px;
  }
  .order_main td{ background-color:#FFF; font-size:16px; font-weight:100; }
  
  .return_to_list{
    font-size: 10px;
    padding:5px; 
    height: 24px !important;
    border: 0px;
    width: 85px;
    float: none
  }
</style>

<table border="0" cellspacing="0" cellpadding="0" align="center" width="80%" >
  <tr><td>&nbsp;&nbsp;</td></tr>
  <tr>
    <td><table width="100%" class="order_main">
        <tr>
          <td align="left">Form Builder Order Status:
            <?php 
            switch ($order["status"]){
              case '0':
                echo "New"; break;
              case '1':
                echo "Renew"; break;
              case '2':
                echo "Invoice Send"; break;
              case '3':
                echo "Paid"; break;
              case '4':
                echo "Cancelled"; break;
            }
            ?>
          </td>
          <td align="right">
            
            <form method="post" name="frm_cng_status" id="frm_cng_status" action="">
              <a class="black-bt return_to_list" href="<?php echo url::base(); ?>formbuilderorder/index" class="return_to_list">Return to list</a>
              Change Order Status to :
              
              <select id="orderByStatus" name="status">
                <option <?php if ($order["status"] == '0') { ?> selected="selected" <?php } ?> value="0">New</option>
                <option <?php if ($order["status"] == '2') { ?> selected="selected" <?php } ?> value="2">Invoice Send</option>
                <option <?php if ($order["status"] == '3') { ?> selected="selected" <?php } ?> value="3">Paid</option>
              </select>
              <input type="hidden" name="id" value="<?php echo $order['id']; ?>" />
            </form>
          </td>
        </tr>
        <?php /*if ($order['status'] != "New" && $order['transaction_id'] != "") { ?>
          <tr>
            <td colspan="2" align="left">Transaction Id : <strong style="font-size:12px; font-weight:900"><?php echo $order['transaction_id']; ?></strong></td>
          </tr>
        <?php } */?>
      </table>
    </td>
  </tr>
  <tr><td>&nbsp;&nbsp;</td></tr>

  <tr>
    <td>
      <table border="0" cellspacing="1" cellpadding="0" align="center" width="100%" class="shipping_detail" >
        <tr>
          <th align="left" class="order_head" colspan="4">Order Detail:</th>
        </tr>
        <tr class="tr_head">
          <th align="center" class="lbl">Order Number</th>
          <th align="center" class="lbl">Number Of Item</th>
          <th align="center" class="lbl">Price Per Item</th>
          <th align="center" class="lbl">Total</th>
        </tr>
        <tr>
          <?php
            $total = 0;
          $sub1 = 0;
          
          ?>
          <td align="center" class="val"><?php echo $order['id']; ?></td>
          <td align="center" class="val"><?php echo $order['no_of_codes']; ?></td>
          <td align="right" class="val">$<?php echo $order['per_code_amount']; ?> AUD</td>
          <td align="right" class="val">$<?php $sub1 += $order['per_code_amount'];
		  		echo number_format($sub1,2);
			 ?> AUD</td>
        </tr>
        
        <tr>
          <td colspan="3" align="right" class="val">Grand Total : </td>
          <td align="right" class="val">$<?php $total = $sub1;
		  	echo number_format($total,2);
		   ?> AUD</td>
        </tr>
        
      </table>
    </td>
  </tr>
  <tr><td>&nbsp;&nbsp;</td></tr>
  <tr>
    <td>
      <table border="0" cellspacing="1" cellpadding="0" align="center" width="100%" class="shipping_detail" >
        <tr><th align="left" class="order_head" colspan="3">Billing Detail:</th></tr>
        <tr>
          <td class="lbl">First Name:</td>
          <td> : </td>
          <td class="val"><?php echo $order['first_name']; ?></td>
        </tr>
        <tr>
          <td class="lbl">Last Name:</td>
          <td> : </td>
          <td class="val"><?php echo $order['last_name']; ?></td>
        </tr>
        <tr>
          <td class="lbl">Company Name</td>
          <td> : </td>
          <td class="val"><?php echo $order['company_name']; ?></td>
        </tr>
        <tr>
          <td class="lbl">Billing Address</td>
          <td> : </td>
          <td class="val"><?php echo $order['billing_address']; ?></td>
        </tr>
        
        <tr>
          <td class="lbl">Town</td>
          <td> : </td>
          <td class="val"><?php echo $order['town']; ?></td>
        </tr>
        <tr>
          <td class="lbl">Postal Code</td>
          <td> : </td>
          <td class="val"><?php echo $order['postal_code']; ?></td>
        </tr>
        
        <tr>
          <td class="lbl">E-mail</td>
          <td> : </td>
          <td class="val"><?php echo $order['email']; ?></td>
        </tr>
        <tr>
          <td class="lbl">Contact</td>
          <td> : </td>
          <td class="val"><?php echo $order['phone']; ?></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr><td>&nbsp;&nbsp;</td></tr>
  <tr><td>&nbsp;&nbsp;</td></tr>
  <tr><td>&nbsp;&nbsp;</td></tr>
</table>
<script type="text/javascript">
  $(document).ready(function (){
    $('#orderByStatus').change(function(){
      if(confirm("Are you sure ?")){
        $('#frm_cng_status').submit();
      }
    });
  });
</script>