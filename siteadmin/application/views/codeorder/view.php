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
          <td align="left">Order Status:
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
			<br>
			<?php 
			echo "Code ID: ". $order["id"]."<br>";
			echo "Date: ";
			
			$order_date_arr = explode(" ",$order["created_at"]);
					
			$order_date = explode("-",$order_date_arr[0]);
			$order_time = $order_date_arr[1];
			
			echo date("d/m/Y",mktime(0,0,0,$order_date[1],$order_date[2],$order_date[0])) ." ".$order_time;
			
			?>
          </td>
          <td align="right" style="vertical-align: top;">
            
            <form method="post" name="frm_cng_status" id="frm_cng_status" action="">
              <a class="black-bt return_to_list" href="<?php echo url::base(); ?>codeorder/index" class="return_to_list">Return to list</a>
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
          <th align="center" class="lbl">Number Of Codes</th>
          <th align="center" class="lbl">Price Per Code</th>
          <th align="center" class="lbl">Total</th>
        </tr>
        <tr>
          <?php
          $total = 0;
          $sub1 = 0;
          $sub2 = 0;
          ?>
          <td align="right" class="val"><?php echo $order['no_of_codes']; ?></td>
          <td align="right" class="val"><?php 
		  if($order['per_code_amount']!="0.00")
		  	echo "$".$order['per_code_amount']." AUD"; 
		  else
		  {
		  		$codeamount =  array();	
		  		$amount_arr = array();
		  		
				for($j=0;$j<count($order_details);$j++)
				{
					array_push($codeamount,$order_details[$j]["amount"]);
				}
				
				foreach($codeamount as $amount)
				{
					if(!in_array($amount, $amount_arr))
					{
						array_push($amount_arr,$amount);
						$cnt = count(array_keys($codeamount, $amount));
						$total_amt =number_format(12*$amount,2);
						
						echo "$cnt code/s @ $$total_amt<br>";
					} 		
				}
				
		  	 /*for($j=0;$j<count($order_details);$j++)
			 {
			 	echo "$".$order_details[$j]["amount"]." AUD<br>";
			 }*/
		  }	
			
			?> </td>
          <td align="right" class="val">$<?php 
		  if($order['total_amount']!="0.00")
		  		echo $order['total_amount'];
		  else
		 	 	echo $sub1 += $order['no_of_codes'] * $order['per_code_amount'] * 12; ?> AUD</td>
        </tr>
        
        <tr>
          <td colspan="2" align="right" class="val">Grand Total : </td>
          <td align="right" class="val">$<?php 
		  if($order['total_amount']!="0.00")
		  	echo $order['total_amount'];
		  else
		  	echo $total = $sub1 + $sub2; ?> AUD</td>
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
          <td width="47%" class="lbl">First Name:</td>
          <td width="1%"> : </td>
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
  
  <tr>
    <td>
      <table border="0" cellspacing="1" cellpadding="0" align="center" width="100%" class="shipping_detail" >
        <tr><th align="left" class="order_head" colspan="3">Reseller Detail:</th></tr>
        <tr>
          <td width="47%" class="lbl" >First Name:</td>
          <td width="1%"> : </td>
          <td class="val"><?php echo (isset($reseller_details['first_name']))? $reseller_details['first_name']: "&nbsp;"; ?></td>
        </tr>
        <tr>
          <td class="lbl">Last Name:</td>
          <td> : </td>
          <td class="val"><?php echo (isset($reseller_details['last_name']))? $reseller_details['last_name']: "&nbsp;"; ?></td>
        </tr>
        <tr>
          <td class="lbl">Company Name</td>
          <td> : </td>
          <td class="val"><?php echo (isset($reseller_details['company_name']))? $reseller_details['company_name']: "&nbsp;"; ?></td>
        </tr>
        <tr>
          <td class="lbl">Billing Address</td>
          <td> : </td>
          <td class="val"><?php echo (isset($reseller_details['billing_address']))? $reseller_details['billing_address']: "&nbsp;"; ?></td>
        </tr>
        
        <tr>
          <td class="lbl">Town</td>
          <td> : </td>
          <td class="val"><?php echo (isset($reseller_details['town']))? $reseller_details['town']: "&nbsp;"; ?></td>
        </tr>
        <tr>
          <td class="lbl">Postal Code</td>
          <td> : </td>
          <td class="val"><?php echo (isset($reseller_details['postal_code']))? $reseller_details['postal_code']: "&nbsp;"; ?></td>
        </tr>
        
        <tr>
          <td class="lbl">E-mail</td>
          <td> : </td>
          <td class="val"><?php echo (isset($reseller_details['email']))? $reseller_details['email']: "&nbsp;"; ?></td>
        </tr>
        <tr>
          <td class="lbl">Contact</td>
          <td> : </td>
          <td class="val"><?php echo (isset($reseller_details['phone']))? $reseller_details['phone']: "&nbsp;"; ?></td>
        </tr>
      </table>
    </td>
  </tr>
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