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
</style>

<table border="0" cellspacing="0" cellpadding="0" align="center" width="80%" >
	<tr><td>&nbsp;&nbsp;</td></tr>
    <tr><td><table width="100%" class="order_main">
    	<tr>
        	<td align="left">Order Status:<?php echo $order["status"]; ?></td>
			<td align="right"><form method="post" name="frm_cng_status" id="frm_cng_status" action="">Change Order Status to :
            	
                
				<select id="orderByStatus" name="status">
                	<option <?php if($order["status"]=='New'){ ?> selected="selected" <?php } ?> value="New">New</option>
                   	<option <?php if($order["status"]=='Paid'){ ?> selected="selected" <?php } ?> value="Paid">Paid</option>
                    <option <?php if($order["status"]=='Shipped'){ ?> selected="selected" <?php } ?> value="Shipped">Shipped</option>
                    <option <?php if($order["status"]=='Completed'){ ?> selected="selected" <?php } ?> value="Completed">Completed</option>
                    <option <?php if($order["status"]=='Cancelled'){ ?> selected="selected" <?php } ?> value="Cancelled">Cancelled</option>
                </select><input type="hidden" name="id" value="<?php echo $order['id']; ?>" />
                </form></td>
        </tr>
        <?php if($order['status']!="New" && $order['transaction_id']!=""){ ?>
        <tr>
        	<td colspan="2" align="left">Transaction Id : <strong style="font-size:12px; font-weight:900"><?php echo $order['transaction_id']; ?></strong></td>
        </tr>
        <?php } ?>
    </table></td></tr>
   	<tr><td>&nbsp;&nbsp;</td></tr>
	<tr>
    	<td><table border="0" cellspacing="1" cellpadding="0" align="center" width="100%" class="order_detail" >
        	<tr><th align="left" class="order_head" colspan="3">Profile Detail:</th></tr>
			<tr>
            	<td class="lbl">Code Number:</td>
                <td> : </td>
                <td class="val"><?php echo $order['profile_id']; ?></td>
			</tr>
			<tr>
            	<td class="lbl" width="150">Name</td>
                <td> : </td>
                <td class="val"><?php echo $profile['name']; ?></td>
			</tr>
			<tr>
            	<td class="lbl">Identification</td>
                <td> : </td>
                <td class="val"><?php echo $profile['identification']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Serial No:</td>
                <td> : </td>
                <td class="val"><?php echo $profile['serial_no']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Description</td>
                <td> : </td>
                <td class="val"><?php echo $profile['description']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Notes</td>
                <td> : </td>
                <td class="val"><?php echo $profile['notes']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Company Name</td>
                <td> : </td>
                <td class="val"><?php echo $profile['name_company']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Telephone</td>
                <td> : </td>
                <td class="val"><?php echo $profile['telephone']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Address</td>
                <td> : </td>
                <td class="val"><?php echo $profile['address']; ?></td>
			</tr>
        </table></td>
	</tr>
    <tr><td>&nbsp;&nbsp;</td></tr>
    <tr><td><table border="0" cellspacing="1" cellpadding="0" align="center" width="100%" class="shipping_detail" >
    	<tr>
    		<th align="left" class="order_head" colspan="4">Order Detail:</th>
		</tr>
					
			
        <tr class="tr_head">
    		<th align="center" class="lbl">Item</th>
            <th align="center" class="lbl">Quantity</th>
            <th align="center" class="lbl">Unit Price</th>
            <th align="center" class="lbl">Total</th>
		</tr>
        <tr><?php 
		$order_date_arr= explode(" ",$order["ordered_on"]);
		if($order_date_arr[0]>"2014-07-31")
		{
			$small_label = '50 X 40 mm label';
			$big_label = '100 X 75 mm label';
		}
		else
		{
			$small_label = '45 X 60 mm label';
			$big_label = '90 X 120 mm label';
		}
		?>
    		<td align="left" class="val">1) <?php echo $small_label;?></td>
            <?php $total=0; $sub1=0; $sub2=0; ?>
            <td align="right" class="val"><?php echo $order['qty_small']; ?></td>
            <td align="right" class="val">$<?php echo $order['price_small']; ?> AUD</td>
            <td align="right" class="val">$<?php echo $sub1+=$order['qty_small']*$order['price_small']; ?> AUD</td>
		</tr>
		<tr>
    		<td align="left" class="val">2) <?php echo $big_label;?></td>
            <td align="right" class="val"><?php echo $order['qty_large']; ?></td>
            <td align="right" class="val">$<?php echo $order['price_large']; ?> AUD</td>
            <td align="right" class="val">$<?php echo $sub2+=$order['qty_large']*$order['price_large']; ?> AUD</td>
		</tr>
        <tr>
    		<td colspan="3" align="right" class="val">Total : </td>
            <td align="right" class="val">$<?php echo $total=$sub1+$sub2; ?> AUD</td>
		</tr>
        <tr>
    		<td colspan="3" align="right" class="val">Postage &amp; Handling : </td>
            <td align="right" class="val">$2.95<?php $gtotal=$total+2.95; ?> AUD</td>
		</tr>
        
		<tr>
    		<td colspan="3" align="right" class="val">Grand Total : </td>
            <td align="right" class="val">$<?php echo $gtotal; ?> AUD</td>
		</tr>
       </table></td>
    </tr>
    <tr><td>&nbsp;&nbsp;</td></tr>
    <tr>
    	<td><table border="0" cellspacing="1" cellpadding="0" align="center" width="100%" class="shipping_detail" >
        	<tr><th align="left" class="order_head" colspan="3">Shipping Detail:</th></tr>
           
			
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
            	<td class="lbl">Address 1</td>
                <td> : </td>
                <td class="val"><?php echo $order['address1']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Address 2</td>
                <td> : </td>
                <td class="val"><?php echo $order['address2']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">City</td>
                <td> : </td>
                <td class="val"><?php echo $order['city']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">State</td>
                <td> : </td>
                <td class="val"><?php echo $order['state']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Zip</td>
                <td> : </td>
                <td class="val"><?php echo $order['zip']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Country</td>
                <td> : </td>
                <td class="val"><?php echo $order['country']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">E-mail</td>
                <td> : </td>
                <td class="val"><?php echo $order['email']; ?></td>
			</tr>
            <tr>
            	<td class="lbl">Contact</td>
                <td> : </td>
                <td class="val"><?php echo $order['contact']; ?></td>
			</tr>
        </table></td>
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