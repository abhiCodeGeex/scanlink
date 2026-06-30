<p>
  <?php /* This is the second step Register page. You can select code  and calculate subscription from here */ ?>
</p>

<style>
.registerli{
	min-width:300px;
}
.listing-table a{
	color:#000;
}
</style>
<section>
  <h2 class="page-title">Code Balance</h2>
  
  <div class="colorcode_define fright">
  	<div class="mainbox">
    	<div class="colorcodebox red">&nbsp;</div>
        <div class="colorbox_text">Exipred</div>
    </div>
    <div class="mainbox">
    	<div class="colorcodebox orange">&nbsp;</div>
        <div class="colorbox_text">Expires within 30 days</div>
    </div>
    <div class="mainbox">
    	<div class="colorcodebox green">&nbsp;</div>
        <div class="colorbox_text">Active</div>
    </div>
  </div>
  
  <div class="clear"></div>
  
  <div class="secondstep-title-purchase-code_balance">
    <div class="SectionTitle-purchase-right"> 
           <input type="button" id="exit"  name="exit" class="text-fi" value="EXIT" onclick="window.location.href='<?php echo URL::base(); ?>dashboard/code'" >
      </div>
      <div class="SectionTitle-purchase-right rightpadding">       
             <?php if (isset($pending_codes) && $pending_codes > 0) { ?>
		        <input type="button" id="renew_code" name="renew_code"   value="Renew Selected Codes" />
		      <?php } else { ?>
        		<input type="button" disabled="disabled"  value="Renew Selected Codes" />
		      <?php } ?> 
      </div>
    <?php if (isset($sessionmsg) && $sessionmsg != '') { ?>
    <p><?php echo $sessionmsg; ?></p>
    <?php } ?>
    <div class="current-code-balance-text">
      <div class="current-code-balance-text-left">Your current code availability balance</div>
      <div class="current-code-balance-text-right"><?php echo $pending_codes ?> </div>
    </div>
  </div>
  
  <div class="clear"></div>
  <div>
  		<div class="search_code_no">
  			Search Code No.<input type="text" class="text-fi" name="code_no" id="code_no" maxlength="10" >
  		</div>  
  		<div class="search_code_no_submit">
  			<input type="button" id="search_code" name="search_code" value="Search" />
  			<input type="hidden" id="total_pages" name="total_pages" value="" >
  		</div> 
  </div>
  
  
  <div class="clear"></div>
  <div style="min-height:50:px;">&nbsp;</div>
  <?php if (isset($pending_codes) && $pending_codes > 0) { ?>
  
  		  
          
          <?php echo $expired_popup; ?>

		  <?php
          echo Form::open(
                  URL::base() . 'code/renewCode', array(
              'id' => 'frmdashcode',
              'name' => 'frmdashcode',
              'method' => 'post',
              'enctype' => 'multipart/form-data'
                  )
          );
          ?>
          <div class="clear"></div>
          <table width="100%" cellspacing="0" cellpadding="0" id="table" class="listing-table">
            <thead>
              <tr>
                <th width="10%" align="center" class="left-round"><strong>Code No.</strong></th>
                <th width="10%" align="center"><strong class="txt-left">Code expiry date</strong></th>
                <th width="10%" align="center"><strong class="txt-left">Renewal Select</strong></th>
                <th class="right-round"><strong class="txt-left">Create - Select a code profile template to activate your code</strong></th>
              </tr>
            </thead>
            <tbody id="records">
            </tbody>
            <tr>
              <td colspan="6" align="center" id="paging"></td>
            </tr>
          </table>
          <?php echo Form::close() ?>
          <table width="100%" cellpadding="0" cellspacing="0" id="table">
            <tr>
              <td colspan="6" align="center" id="paging"></td>
            </tr>
          </table>
          
          <script type="text/javascript">  
		  		$(document).ready(function(){
    				getResults(1,'','','asc');
				});
				
				$('#search_code').click(function () {
					code_no = $.trim($('#code_no').val());
					
					if(isNaN(code_no)==true)
					{
						alert("Please enter valid code number");
					}
					else
					{
						code_no_arr = code_no.split('.');
						
						if(code_no_arr.length>1)
						{
							alert("Please enter valid code number");
						}
						else
						{
							getResults(1,code_no,'','asc');
						}
						
					}
				});
				
				
				/* //for text box
				$('#paging').on('keypress','.goto_page',function (event) {
					
					if(window.event)// IE
        				key_code = event.keyCode;	
        			else 
        				key_code = event.which;					
					
					if(key_code==13)
					{
						event.preventDefault();
						goto_page = $.trim($(this).val());
						total_pages = $('#total_pages').val();
						
						if(goto_page=="") 
						{
							alert("Please enter page number");
						}
						else if(isNaN(goto_page)==true)
						{
							alert("Please enter valid page number");
						}
						else
						{
							goto_page_arr = goto_page.split('.');
							
							if(goto_page_arr.length>1)
							{
								alert("Please enter valid page number");
							}
							else
							{
								if((goto_page*1)>total_pages || goto_page==0)
									alert("Please enter valid page number");
								else
									getResults(goto_page,'','','asc');
							}
							
						}
						
						
					}					
					
				});
				*/
				
				$('#paging').on('change','.goto_page',function () {
					goto_page = $.trim($(this).val());
					
					getResults(goto_page,'','','asc');
				});
								
				$('#renew_code').click(function(){
					var isSelected = false;
					$('.selectprofile').each(function(){
					  if($(this).is(':checked')){
						isSelected = true;			
					  }
					});
					if(isSelected){
					  $('#frmdashcode').submit();
					}else{
					  alert("Please select the code to be renew.");
					  return false;
					}
					return false;
			   });
			   
			   function getResults(page,filter,sorton,sortby){
					if(sorton == ''){
					  sorton = 'name';
					}
					<!--- WI_MS END ---->
					var request = $.ajax({
					  url:'<?php echo URL::base(); ?>test/getData',
					  type: "POST",
					  dataType: "json",
					  data:{
						curpage : page,
						perpage : 10,
						entity : 'profiles',
						filter : filter,
						filter_on : sorton,
						filter_by : sortby,
						params : { type_id:'code_balance', deleted:0, client_id:<?php echo $client_id ?> }
					  }
						
					});
					
					request.done(function(data) {
					  var html="";
					  for(var i=0;i<data.results.length;i++){
							
						var imgact = data.results[i].approve==1?"active":"blocked";
						var opp= (imgact=="active")?"blocked":"active";
						
						if(data.results[i].free_code=="1" && data.results[i].renewal_required=="0")
						{
							exp_date = "N/A";
							my_class = "green";
							checkbox_disabled = 'disabled="disabled"';
						}
						else
						{
							exp_date = data.results[i].expired_at;
							my_class = data.results[i].myclass;
							checkbox_disabled = '';
						}
						
						
						
						html+= '<tr class="'+my_class+'">'+
						  '<td align="center" class="left-border">'+data.results[i].id+'</td>'+
						  '<td align="center">'+exp_date+'</td>'+
						  '<td align="center"><input type="checkbox" class="selectprofile" '+ checkbox_disabled + ' id="selectprofile'+data.results[i].id+'" name="selectprofile[]" value="'+data.results[i].id+'" /></td>'+
						  '<td class="right-border">';
                          
                          if(my_class != 'red'){                         
                             html+= '<table width="100%" cellspacing="0" cellpadding="0" class="">'+
                              '<tbody><tr>'+
                              //'<td align="center" class="border-none"><a href="<?php echo URL::base(); ?>plant/index/'+data.results[i].id+'" class="Box_recycle tooltip  Edit" title="Plant & Equipment Add">Plant & Equipment</a></td>'+
                              '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>plant/index/&quot;,'+data.results[i].id+')"  class="Box_recycle tooltip  Edit" title="Plant & Equipment Add">Plant & Equipment</a></td>'+
                              '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>location/index/&quot;,'+data.results[i].id+')" class="Box_recycle tooltip Edit" title="Location Add">Location</a></td>'+
                              '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>asset/index/&quot;,'+data.results[i].id+')" class="Box_recycle tooltip Edit" title="Person/Business Add">Person/Business</a></td>'+
                              '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>product/index/&quot;,'+data.results[i].id+')" class="Box_recycle tooltip Edit" title="Product Add">Product</a></td>'+
                              '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>procedure/index/&quot;,'+data.results[i].id+')" class="Box_recycle tooltip Edit" title="Procedure  Add">Procedure</a></td>'+
                              '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>misc/index/&quot;,'+data.results[i].id+')" class="Box_recycle tooltip Edit"  title="Misc Add">Misc</a></td>'+
                              '<td align="center" class="border-none"><a href="<?php echo URL::base(); ?>code/index/'+data.results[i].id+'" class="Box_recycle tooltip Edit"  title="URL Link Add">URL Link</a></td>'+
							  '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>survey/index/&quot;,'+data.results[i].id+')" class="Box_recycle tooltip Edit"  title="Form/Survey/Checklist Add">Form/Survey/Checklist</a></td>'+
							  '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>exhibit/index/&quot;,'+data.results[i].id+')" class="Box_recycle tooltip Edit"  title="Exhibit Add">Exhibit</a></td>'+
							  '<td align="center" class="border-none"><a href="javascript:;" onclick= "remove_temp_files(&quot;<?php echo URL::base(); ?>voc/index/&quot;,'+data.results[i].id+')" class="Box_recycle tooltip Edit"  title="VOCC Add">VOCC</a></td>'+
                              '</tr></tbody>'+
                              '</table>';
                          
                          }else{
                          	 
                          }
                          html+= '</td>'+
						  '</tr>';
					  }
					  $("#records").html(html);
					  $( "input:checkbox").uniform();	
					 					  
					  
					  var html =[];
					  
					  for(var i=0;i<data.links.length;i++){
						  
						   if(data.links[i].total_pages)
						   	$('#total_pages').val(data.links[i].total_pages);
						   
						   if(data.links[i].current_page)
						   {
						   	//html.push('Page <input type="text" class="goto_page" value="'+data.links[i].current_page+'" > of '+data.links[i].total_pages);
						   	html.push('Page ');
						   	html.push('<select  class="goto_page" name="goto_page" id="goto_page">');
						   	for(k=1;k<=data.links[i].total_pages;k++)
						   	{
						   		if(data.links[i].current_page==k)
						   			html.push('<option value="'+k+'" selected>'+k+'</option>');
						   		else
						   			html.push('<option value="'+k+'">'+k+'</option>');
						   	}
						   	html.push('</select>');
						   	html.push(' of '+data.links[i].total_pages)
						   }
							else
								html.push('<a href="javascript:'+data.links[i].href+'" class="'+data.links[i].myClass+'">'+data.links[i].label+'</a>');
					  }
					  
					  
					  if(data.results.length>0){
						$("#paging").html(html.join(""));
					  }else{
						$("#paging").html("<h3>No Results Found</h3>");
					  }
						
					});
					
					request.fail(function(jqXHR, textStatus) {
					  alert( "Request failed: " + textStatus );
					});
				  }
		  </script>
  <?php
  } else {
      echo '<div class="clear"></div><div style="text-align:center;color:#5286BE;"></div>';
  }
  ?>
  <div class="clear"></div>
  <h3>Enter the amount of codes you would like to activate and click the calculate button to display the total order value</h3>
  <div class="clear"></div>
  
  

  <div class="SecondPurchaseBox">
    <div class="GreenTitlePurchaseCodes" id="purchase_code">Purchase Codes</div>
    <div class="BlackTitlePurchaseCodes" id="agency_reseller" >Agency/Reseller</div>
    <div class="content-register" id="purchase_code_content">
    	<?php 
		   echo Form::open(
					URL::base() . 'profile/billing', array('id' => 'frmpurchase',
				'name' => 'frmpurchase',
				'method' => 'post',
				'enctype' => 'multipart/form-data',
					)
			) 
	  ?>
      <div class="content-main-register">
        <div class="content-dispaly-amount-register">
          <div class="code-amount"><sup>$</sup><span id="code_amount">0.00</span> <br />
          </div>
          <div class="code-permonth">Per Code / Month <br />
          </div>
          <div class="code-total">Total Annual subscription <b>$<span id="subscription_amount">0.00</span></b></div>
        </div>
        <div class="content-label-register">
          <label>Enter a number of codes required:</label>
        </div>
        <div class="content-textbox-register"> <?php echo Form::input(
                                'no_codes', 
                                isset($fields['no_codes']) ? $fields['no_codes'] : '', 
                                array(
                                        'id' => 'no_codes', 
                                        'class' => 'text-fi',
                                        
                                        'maxlength'=>'4'
                                    )
                                ); ?> <?php echo Form::label(
                                    'no_codes', 
                                    isset($errors['no_codes']) ? $errors['no_codes'] : '', 
                                    array(
                                            'class' => 'error', 
                                            'generated' => 'true'
                                        )
                                ); ?> </div>
        <div class="content-button-register"> <?php echo Form::input(
                                    'calculate', 
                                    'Calculate', 
                                    array(
                                            'id' => 'calculate', 
                                            'class' => 'text-fi',
                                            'type'=>'button',
                                        )
                                    ); ?> <?php echo Form::label(
                                        'calculate', 
                                        isset($errors['calculate']) ? $errors['calculate'] : '', 
                                        array(
                                                'class' => 'error', 
                                                'generated' => 'true'
                                            )
                                    ); ?> </div>
      </div>
      <div class="clear"></div>
      <div class="secondstep-submit"> <?php echo Form::input(
                            'order-summary', 
                            'Purchase', 
                            array(
                                    'id' => 'order-summary', 
                                    'class' => 'text-fi',
                                    'type'=>'button',
                                )
                            ); ?> <?php echo Form::label(
                                'order-summary', 
                                isset($errors['order-summary']) ? $errors['order-summary'] : '', 
                                array(
                                        'class' => 'error', 
                                        'generated' => 'true'
                                    )
                            ); ?> </div>
       	<?php echo Form::close() ?> 
    </div>
    
    
    <div class="content-register" id="agency_reseller_content">
      <?php 
	   echo Form::open(
				URL::base() . 'profile/billing', array('id' => 'frmpurchase',
			'name' => 'frmpurchase',
			'method' => 'post',
			'enctype' => 'multipart/form-data',
				)
		) 
	  ?>
      
      <div class="content-main-register">
      		<div class="content-label-register reseller">
              <label>Enter your reseller password:</label>
            </div>
      		<div class="content-textbox-register reseller">
            	<?php echo Form::input(
							  'reseller_code_client', 
							  isset($fields['reseller_code_client']) ? $fields['reseller_code_client'] : '', 
							  array(
									  'id' => 'reseller_code_client', 
									  'class' => 'text-fi',
								  )
							  ); ?> 
				<?php echo Form::label(
								'reseller_code_client', 
								isset($errors['resller_code_client']) ? $errors['reseller_code_client'] : '', 
								array(
										'class' => 'error', 
										'generated' => 'true'
									)
							); ?> 
            </div>
            <div class="clear"></div>
      		<div class="content-dispaly-amount-register">
          		<div class="code-amount"><sup>$</sup><span id="reseller_code_amount">0.00</span> <br /></div>
          		<div class="code-permonth">Per Code / Month <br /></div>
          		<div class="code-total">Total Annual subscription <b>$<span id="reseller_subscription_amount">0.00</span></b></div>
                <div class="code-total resellerMarginDiff">Reseller margin <b>$<span id="reseller_margin">0.00</span></b></div>
      		</div>
		   <div class="content-label-register">
          	<label>Enter a number of codes required:</label>
      	   </div>
      	   <div class="content-textbox-register"> 
			  <?php echo Form::input(
                              'reseller_code_qty', 
                              isset($fields['reseller_code_qty']) ? $fields['reseller_code_qty'] : '', 
                              array(
                                      'id' => 'reseller_code_qty', 
                                      'class' => 'text-fi',
                                      'maxlength'=>'4'
                                  )
                              ); ?> 
              <?php echo Form::label(
                              'reseller_code_qty', 
                              isset($errors['reseller_code_qty']) ? $errors['reseller_code_qty'] : '', 
                              array(
                                      'class' => 'error', 
                                      'generated' => 'true'
                                  )
                          ); ?> 
       		</div>
		    <div class="content-button-register"> 
				<?php echo Form::input(
                                'calculate', 
                                'Calculate', 
                                array(
                                        'id' => 'calculateRe', 
                                        'class' => 'text-fi',
                                        'type'=>'button',
                                    )
                                ); ?> 
                <?php echo Form::label(
                                'calculate', 
                                isset($errors['calculate']) ? $errors['calculate'] : '', 
                                array(
                                        'class' => 'error', 
                                        'generated' => 'true'
                                    )
                            ); ?> 
       		</div>
         
      </div>
      <div class="clear"></div>
      <div class="secondstep-submit"> <?php echo Form::input(
                            'order-summary', 
                            'Purchase', 
                            array(
                                    'id' => 'order-summaryRe', 
                                    'class' => 'text-fi',
                                    'type'=>'button',
                                )
                            ); ?> <?php echo Form::label(
                                'order-summary', 
                                isset($errors['order-summary']) ? $errors['order-summary'] : '', 
                                array(
                                        'class' => 'error', 
                                        'generated' => 'true'
                                    )
                            ); ?> 
     	</div>
                            
    	<?php echo Form::close() ?> 
    </div>
  </div>
  
  
</section>

<script type="text/javascript">

  $('#no_codes').bind('keypress',function(e){
	  	 return isNumberKey(e);
  });
  
  $('#reseller_code_qty').bind('keypress',function(e){
	  	 return isNumberKey(e);
  });
  
  $('#order-summaryRe').bind('click',function(e){
	  	 return checkResellervalidation(true);
  });
  
  $('#calculateRe').bind('click',function(e){
	  	 return checkResellervalidation(false);
  });
  
  $('#order-summary').bind('click',function(e){
	  	 return checkvalidation(true);
  });
  
  $('#calculate').bind('click',function(e){
	  	 return checkvalidation(false);
  });

  function isNumberKey(evt)
  {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
      return false;

    return true;
  }
  function checkvalidation(submitbuttonpress){
    if($('#no_codes').val() == ''){
      alert("Enter a number of code required.");
      $('#no_codes').focus();
      return false;
    }else if($('#no_codes').length > 4){
      alert("Enter a number of code less than 4 character.");
      $('#no_codes').focus();
      return false;
    }else if($('#no_codes').val() > 1000){
      alert("Enter a number of code less than 1000.");
      $('#no_codes').focus();
      return false; 
    }else{
      $('.loader-image').show();
      var request = $.ajax({
        url:'<?php echo URL::base(); ?>register/getData/',
        type: "POST",
        dataType: "json",
        data:{
          no_codes 			: $('#no_codes').val(),
          submit_button_press 	: submitbuttonpress
        }
		
      });
      request.done(function(data) {
			 
        var error = data.errors.length;
        if(error > 0){
          for(var i=0;i<data.errors.length;i++){
            alert(data.errors[i]);
          }
        }else{
          $('#code_amount').html(data.amount);
          $('#subscription_amount').html(data.totalsubscrption);
          $('.loader-image').hide();
        }
        if(submitbuttonpress){
          window.location.href = "<?php echo URL::base(); ?>profile/billing/";
        }
      });
      request.fail(function(jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
      });
			
    }
  }
  
  function checkResellervalidation(submitbuttonpress){

	if($('#reseller_code_client').val() == ''){
      alert("Enter your reseller password.");
      $('#reseller_code_client').focus();
      return false;
    }else if($('#reseller_code_qty').val() == ''){
      alert("Enter a reseller of code.");
      $('#reseller_code_qty').focus();
      return false;
    }else{
      $('.loader-image').show();
      var request = $.ajax({
        url:'<?php echo URL::base(); ?>profile/getResellerdata/',
        type: "POST",
        dataType: "json",
        data:{
          no_codes 			: $('#reseller_code_qty').val(),
		  reseller_code     : $('#reseller_code_client').val(),
		  submit_button_press 	: submitbuttonpress
        }
      });
      request.done(function(data) {

        var error = data.errors.length;

        if(error > 0){
          for(var i=0;i<data.errors.length;i++){
            alert(data.errors[i]);
          }
        }else{
          $('#reseller_code_amount').html(data.amount);
          $('#reseller_subscription_amount').html(data.totalsubscrption);
		  $('#reseller_margin').html(data.reseller_margin);
          $('.loader-image').hide();
		  if(submitbuttonpress){
          	window.location.href = "<?php echo URL::base(); ?>profile/billing/";
          }
        }
        
      });
      request.fail(function(jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
      });
			
    }
    
  }

function remove_temp_files(redirect_url,profile_id)
{
	location.href='<?php echo URL::base() ?>profile/delete_temp_files/?redirect_url='+redirect_url+'&profile_id='+profile_id;
}

</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#purchase_code').click(function(){
			$(this).addClass('GreenTitlePurchaseCodes');
			$(this).removeClass('BlackTitlePurchaseCodes');
			$('#agency_reseller').addClass('BlackTitlePurchaseCodes');
			$('#agency_reseller').removeClass('GreenTitlePurchaseCodes');
			$('#agency_reseller_content').hide();
			$('#purchase_code_content').show();
		});
		$('#agency_reseller').click(function(){
			$(this).removeClass('BlackTitlePurchaseCodes');
			$(this).addClass('GreenTitlePurchaseCodes');
			$('#purchase_code').removeClass('GreenTitlePurchaseCodes');
			$('#purchase_code').addClass('BlackTitlePurchaseCodes');
			$('#purchase_code_content').hide();
			$('#agency_reseller_content').show();
		});
		
		$('#reseller_code_client').focusout(function(){
			//checkResellervalidation(false);
		});
	});
</script>
