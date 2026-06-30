<?php  echo $nearly_done_popup; ?>
<!--HOME PAGE NEW HEAD INFO -->
<div class="scanlink-container">
	<div class="home-new-head">
	    <h2>Create<span>.</span>&nbsp;&nbsp;&nbsp;&nbsp;Connect<span>.</span>&nbsp;&nbsp;&nbsp;&nbsp;Measure<span>.</span></h2>
	    <p>Welcome to ScanLink. The worlds best QR Code generator and content<br />
	        management platform for creating mobile interactive experiences that<br />
	        <span>educate</span>, <span>inform</span> and <span>sell</span>...
	    </p>
	</div>
	<!--HOME PAGE NEW HEAD INFO -->

	<!--EASY VERSATILE SMART INFO ROW -->
	<div class="home-smartInfo">
	    <div class="smartInfo-box">
	        <img src="<?php echo URL::base() ?>images/easy-info-icon.png" alt="" />
	        <h3>Its easy</h3>
	        <p>You simply upload your<br />
	            content and then download<br />
	            your 'dynamic' QR code.</p>
	    </div>

	    <div class="smartInfo-box">
	        <img src="<?php echo URL::base() ?>images/versatile-info-icon.png" alt="" />
	        <h3>Versatile</h3>
	        <p>Print your QR code on <br />
	            product labels, brochures,<br />
	            signage, business cards<br />
	            - Anywhere!</p>
	    </div>

	    <div class="smartInfo-box">
	        <img src="<?php echo URL::base() ?>images/smart-info-icon.png" alt="" />
	        <h3>Smart</h3>
	        <p>Each time someone scans your code<br />
	            ScanLink can record the time, date,<br />
	            type of device used and even the<br />
	            GPS location. You can also collect<br />
	            names, mobile numbers and email<br />
	            addresses to build your database.</p>
	    </div>
	</div>

	<section class="workplace-block-raw  museum-video-div">
	    <div class="workplace-box fleft">
	        <div class="home-content-block-new clearfix">
	            <div class="m20">&nbsp;</div>
	            <p class="large-size">See how the Hellenic Museum uses ScanLink to create engaging mobile interactive exhibitions...</p>
	            <div class="watch-the-video-right taRight"><strong>Watch the video</strong></div>
	        </div>
	    </div>
	    <div class="workplace-box fright">      	  
	        <iframe width="480" height="300" frameborder="0" allowfullscreen="" src="https://www.youtube.com/embed/kzARc58KXTA?rel=0&amp;autoplay=0&amp;loop=0&amp;wmode=opaque" marginwidth="0" marginheight="0"></iframe>

	    </div>
	</section>

	<div class="clearfix"></div>
	<div class="mobileProds">
    <h5>There are hundreds of applications for Scanlink.</h5>
    <p class="green_text_normal_pdf">Click to download a PDF information sheet...</p>
    <div class="mobileProdsRow">
        
        <div class="pdf-info-sheet">
            <a href="<?php echo URL::base() ?>download_file.php?filename=ScanLink-Hotel-Surveys.pdf&filepath=images/brochure">
                <img style="" alt="" src="<?php echo URL::base() ?>images/ScanLink-Hotel-Surveys.jpg">
            </a>
            <p>Hotel Surveys</p>
        </div>

        <div class="pdf-info-sheet">
        <a href="<?php echo URL::base() ?>download_file.php?filename=Museum-Flyer.pdf&filepath=images/brochure">
            <img alt="" src="<?php echo URL::base() ?>images/Museum-Flyer.jpg">
        </a><p>Arts &amp; Culture</p>
        </div>

        <div class="pdf-info-sheet">
        <a href="<?php echo URL::base() ?>download_file.php?filename=ScanLink-Intercative-Memorials.pdf&filepath=images/brochure">
            <img alt="" src="<?php echo URL::base() ?>images/ScanLink-Memorial-Flyer.jpg">
        </a>
            <p>Monuments &amp; Memorials</p>
        </div>
        
        <div class="pdf-info-sheet">
        <a target="_blank" href="<?php echo URL::base() ?>download_file.php?filename=Business-mobile-brochure-HR.pdf&filepath=images/brochure">
            <img alt="" src="<?php echo URL::base() ?>images/Business-mobile-brochure.jpg">
        </a>
            <p>Business Profile</p>
        </div>
        
        <div class="pdf-info-sheet">
        <a target="_blank" href="<?php echo URL::base() ?>download_file.php?filename=ScanLink-Mobile-Surveys-2.pdf&filepath=images/brochure">
            <img alt="" src="<?php echo URL::base() ?>images/ScanLink-Customer-feedback-Surveys.jpg">
        </a>
            <p>Customer Feedback</p>
        </div>
        
        <div class="pdf-info-sheet">
        <a target="_blank" href="<?php echo URL::base() ?>download_file.php?filename=ScanLink-for-product-packaging.pdf&filepath=images/brochure">
            <img alt="" src="<?php echo URL::base() ?>images/ScanLink-Product-packaging.jpg">
        </a>
            <p>Product Packaging</p>
        </div>
        
        <div class="pdf-info-sheet">
            <div style="height:188px;">
            <a target="_blank" href="<?php echo URL::base() ?>download_file.php?filename=Mobile-Interactive-Workplace.pdf&filepath=images/brochure">
                <img alt="" src="<?php echo URL::base() ?>images/Mobile-Interactive-Workplace.jpg">
            </a>
            </div>
            <p>The Workplace</p>
        </div>
    </div>
	</div>
</div>


<!--EASY VERSATILE SMART INFO ROW -->
<div style="margin-left:-20px;margin-right:-20px; ">
	<div class="scanlink-container">
		<!-- basic code generator -->
		<div id="ExpressCodeGenBlock" class="ExpressCodeGenBlock clearfix"><!-- ExpressCodeGenBlock -->
	    	<div class="ExpressCodeLeft"><!-- ExpressCodeLeft -->
	      	<h2>Express Code Generator</h2>
	        <div class="ExpressCodeLeftInn">
	          <p class="Exp1">
	            Just need to create and download<br>
	            a basic mobile code fast..?
	          </p>
	          <div class="ExpForm clearfix"	>
	          	<div class="ExpFi1">
	            	<label>Enter the Web page address (URL) here</label>
	              <input type="text" value="http://" name="txt_url" id="txt_url" class="url-textbox"  />
	            </div>
	            <div class="ExpFi2">
	            	<label>QR</label>
	              <input type="radio" name="code_type" class="type_of_code" value="0" checked="" />
	            </div>
	            <div class="ExpFi2">
	            	<label>DM</label>
	              <input type="radio" name="code_type" class="type_of_code" value="1" />
	            </div>
	          </div>
	          <div class="monitor">
	          	<img src="<?php echo URL::base() ?>images/monitor3.png"  class="monitor3" alt="" />
				
	          	<h3>Create a mobile code with the works...</h3>
	            <p>Powerful analytics, data collection functions,
					content creation templates, mobile interactive forms and more</p>
				<p class="green_text_normal"><a href="<?php echo URL::base() ?>register/index" ><b>Click here </b>to get started with your <b>first code FREE</b> for a year!</a></p>
	          </div>
	        </div>
	        
	      </div><!-- /ExpressCodeLeft -->
	      <div class="ExpressCodeRight">
	      	<h2>Code Preview</h2>
	        <div class="qrCodeBox">
	        	<img src="<?php echo URL::base() ?>images/qr_code_sample.png" id="code_preview_img" alt="" />
				
				<input type="hidden" name="code_preview_flag" id="code_preview_flag" value="0"/>
	        </div>
	        <div class="Express-combo">
				<div class="form-view1">
					<select name="download_option" id="download_option" class="">
				  		<option value="" selected="">Download As</option>
				  		<option value="pdf">PDF</option>
				  		<option value="tiff">TIFF</option>
				  		<option value="eps">Eps(Vector)</option>
					</select>
				</div>
	          <br>
	          <input type="button" onclick="download_qrcode('<?php echo session_id(); ?>');" value="Download" />
			  <input type="hidden" name="tmp_session_id" id="tmp_session_id" value="<?php echo session_id(); ?>">
	        </div>
	        
	        
	      </div>
	    </div><!-- /ExpressCodeGenBlock -->
		<!-- basic code generator end -->
		<div class="clearfix"></div>
</div>
<div class="genQr">
	<div class="scanlink-container">
		<div class="genqr-dtl">
			<p>Scanlink is a powerful QR Code generator<br>
					and Mobile Engagement Platform<br>
					featuring a variety of solutions<br>
					for business of all sizes</p>
			<p class="readyStart"> <a href="<?php echo URL::base() ?>register/index" id="register-link">Try it now with your first code<br><b>FREE for a year!</b></a> </p>
			<div class="Arrow-Down"><img src="<?php echo URL::base() ?>images/arrow-down-png.png" width="50" height="68"></div>
			<div class="Scroll-down">Scroll down to learn more</div>
		</div>
		<div class="Hend_image"><img src="<?php echo URL::base() ?>images/hand-phone-icon-original.png" >
		</div>
	</div>
</div>
		
<div class="clearfix"></div>
    <!--WORKPLACE AND MARKETING BLOCK -->
    <section>
    	<div class="URL-image"><img src="<?php echo URL::base() ?>images/website-680x249.jpg" width="100%" ></div>
   		<div class="clear"></div>
        <div class="green-content">
	        <div class="scanlink-container">
	        	<div class="Hend-img"><img src="<?php echo URL::base() ?>images/Your-website.png" width="323" height="390"></div>
	        	<div class="Green-Right" style="margin-right: 175px;">Create a Scanlink QR Code<br> 
	         	linked to any URL...</div>
		        <div class="Links">
		         <div class="Show-Me"><a href="https://www.youtube.com/embed/uEDTnBPUk28?rel=0" id="show_me_how"><img src="<?php echo URL::base() ?>images/Show-me-how.png" width="42" height="41"></a></div>
		         <div class="show-me-title">SHOW ME HOW</div>
		        </div>
	           <div class="Links">
		         <div class="Show-Me"><a href="<?php echo URL::base() ?>register/index"><img src="<?php echo URL::base() ?>images/dO-IT-nOW.png" width="42" height="41"></a></div>
		         <div class="show-me-title">  &nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
		       </div>

	        </div>
        </div>
    </section>

    <section>
    	<div class="URL-image"><img src="<?php echo URL::base() ?>images/Low-res2.png" width="100%" ></div>
    	<div class="clear"></div>
        <div class="green-content">
	        <div class="scanlink-container">
	        	<div class="Hend-img"><img src="<?php echo URL::base() ?>images/Finalist-Hend.png" width="323" height="390"></div>
				<div class="Green-Right" style="margin-right: 175px;">Create mobile interactive<br>
				packaging & signage...</div>
	            <div class="Links">
		          <div class="Show-Me"><a href="<?php echo URL::base() ?>marketing/index/"><img src="<?php echo URL::base() ?>images/Show-me-More.png" height="41"></a></div>
		         <div class="show-me-title">SHOW ME MORE</div>
		        </div>
	            <div class="Links">
		         <div class="Show-Me"><a href="<?php echo URL::base() ?>register/index"><img src="<?php echo URL::base() ?>images/dO-IT-nOW.png" width="42" height="41"></a></div>
		         <div class="show-me-title">  &nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
		        </div>
	        </div>
        </div>
    </section>
    <section>
    	<div class="URL-image"><img src="<?php echo URL::base() ?>images/banner_workplace3.png" height="410" width="100%" ></div>
    	<div class="clear"></div>
    
        <div class="green-content" style="height: 223px;">
	        <div class="scanlink-container">
		        <div class="Hend-img-new" style="margin-top: -181px;"><img src="<?php echo URL::base() ?>images/Civil-Globale.png" width="323" height="390"></div>
		        <div class="Green-Right" style="margin-right: 138px;">Create a mobile interactive workplace <br>
				to enhance Health safety sustainability <br>
					& productivity...</div>
		        <div class="Links">
		          <div class="Show-Me"><a href="<?php echo URL::base() ?>workplace/index/"><img src="<?php echo URL::base() ?>images/Show-me-More.png"  height="41"></a></div>
		         <div class="show-me-title">SHOW ME MORE</div>
		        </div>
		        <div class="Links">
		         <div class="Show-Me"><a href="<?php echo URL::base() ?>register/index"><img src="<?php echo URL::base() ?>images/dO-IT-nOW.png" width="42" height="41"></a></div>
		         <div class="show-me-title">  &nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
		        </div>
	        </div>
        </div>
    </section>
    <section>
        <div class="URL-image"><img src="<?php echo URL::base() ?>images/20140320_113407 LR.jpg" width="100%" ></div>
	    <div class="clear"></div>
	        
	    <div class="green-content">
	        <div class="scanlink-container">
		        <div class="Hend-img"><img src="<?php echo URL::base() ?>images/Chubb.png" width="323" height="390"></div>
		        <div class="Green-Right" style="margin-right: 155px;">Create mobile interactive forms,<br>
					surveys & checklists...</div>
		        <div class="Links">
		         <div class="Show-Me"><a href="<?php echo URL::base() ?>formbuilderpage/index"><img src="<?php echo URL::base() ?>images/Show-me-More.png" height="41"></a></div>
		         <div class="show-me-title">SHOW ME MORE</div>
		        </div>
		        <div class="Links">
		         <div class="Show-Me"><a href="<?php echo URL::base() ?>register/index"><img src="<?php echo URL::base() ?>images/dO-IT-nOW.png" width="42" height="41"></a></div>
		         <div class="show-me-title">  &nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
		        </div>
	        </div>
        </div>
    </section>
    <section class="Main">
    	<div class="URL-image"><img src="<?php echo URL::base() ?>images/20140604_135849_new.jpg" width="100%" ></div>
    	<div class="clear"></div>
    
    	<div class="green-content">
	        <div class="scanlink-container">
		        <div class="Hend-img"><img src="<?php echo URL::base() ?>images/Gala.png" width="323" height="390"></div>
		        <div class="Green-Right" style="margin-right: 175px;">Create a mobile profile for you <br>
					or your business...</div>
		        <div class="Links">
		         <div class="Show-Me"><a href="<?php echo URL::base() ?>marketing/foryou/"><img src="<?php echo URL::base() ?>images/Show-me-More.png" height="41"></a></div>
		         <div class="show-me-title">SHOW ME MORE</div>
		        </div>
		        <div class="Links">
		         <div class="Show-Me"><a href="<?php echo URL::base() ?>register/index"><img src="<?php echo URL::base() ?>images/dO-IT-nOW.png" width="42" height="41"></a></div>
		         <div class="show-me-title">  &nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
		        </div>
	        </div>
    	</div>
    </section>
</div>

<div class="clearfix">&nbsp;</div>
<div class="scanlink-container">
	<div class="client_tiker" >
		<ul>
			<li><img src="<?php echo URL::base() ?>images/client_logo/iga.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/cellarbrations.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/dare-to-be-great.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/the-bottle.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/ergon-energy.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/raine-and-horne.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/victoria-university.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/fm-fleetmark.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/first-nationl-real-estate.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/scan-media.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/lj-hooker.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/super-coal-limited.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/bawbaw-shire-council.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/melbourne-water.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/ray-white.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/st-john.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/adelaide-hills-council.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/centra-wealth.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/ancare.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/mr-fothergills.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/angus-member.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/new-government.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/queensland.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/kyneton-ridge-estate.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/red-roo.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/bloriz.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/kanga_logo.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/Hellenic_Museum.jpg" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/BD_Newcastle.png" height="70" /></li>
			<li><img src="<?php echo URL::base() ?>images/client_logo/hilton.jpg" height="70" /></li>
		 </ul>	
		</div>
	<div class="clearfix"></div>
</div>
<!--WORKPLACE AND MARKETING BLOCK -->

<div class="clearfix">&nbsp;</div>

<section class="workplace-block-raw">
    <div class="scanlink-container">
	    <div class="workplace-box fleft">
	        <div class="home-content-block-new clearfix">
	            <div class="m20">&nbsp;</div>
	            <p class="large-size taRight">5 Tips to help make your<br/>QR Code initiative<br/>a success</p>
	            <div class="watch-the-video-right taRight"><strong>WATCH THE VIDEO</strong></div>
	        </div>
	    </div>
	    <div class="workplace-box fright">      	  
	        <iframe width="480" height="300" frameborder="0" allowfullscreen="" src="https://www.youtube.com/embed/5c8EnTAMEl4?rel=0&amp;autoplay=0&amp;loop=0&amp;wmode=opaque" marginwidth="0" marginheight="0"></iframe>
	    </div>
	</div>
</section>
<div class="clearfix">&nbsp;</div>
<!--CHECKOUT OTHER MOBILE -->
<!-- <div class="mobileProds">
    <h5>Check out our other mobile interactive code solutions for...</h5>
    <div class="mobileProdsRow">
        <a href="http://www.propertycode.com.au" class="fleft" target="_blank">
            <img src="<?php echo URL::base() ?>images/propertycode-logo.png" alt="" />
            <p>Real Estate</p>
        </a>

        <a href="http://www.scanoftheday.com.au" target="_blank">
            <img src="<?php echo URL::base() ?>images/scanoftheday-logo.png" alt="" />
            <p>Mobile Coupons</p>
        </a>

        <a href="http://www.youtube.com/watch?v=Wiojf3ZJKvY" class="fright" target="_blank" >
            <img src="<?php echo URL::base() ?>images/scansave-logo.png" alt="" />
            <p>Retail Marketing</p>
        </a>
    </div>
</div> -->
<!--CHECKOUT OTHER MOBILE -->
<script>
	
	function download_qrcode(session_id)	
	{	
		download_as = $("#download_option").val();
		code_preview_flag = $("#code_preview_flag").val();
		
		code_type = $("input:radio[name='code_type']:checked").val();
		
		
		
		if(code_preview_flag=="0")
		{
			alert("Please enter valid URL");
			return false;
		}
		
		if(download_as=="")
		{
			alert("Please select download as");
			return false;
		}
		
		location.href='<?php echo URL::base() ?>qrhome/downloadQR?session_id='+ session_id + '&download_as='+download_as+ '&code_type='+code_type;  
		
		
	}

	$(document).ready(function(){
		var request;
		//change on url entered
		$('.ExpFi1').on('keyup','#txt_url',function()
		{
			
			
			url = $(this).val();
			code_type = $("input:radio[name='code_type']:checked").val();
			tmp_session_id = $('#tmp_session_id').val();
			
			var pattern = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	        if (!pattern.test(url)) {
	            return false;
	        } 
            
			if(typeof request !== 'undefined') //to abort previous ajax request
        		request.abort();
				
			$('#code_preview_img').attr('src','<?php echo URL::base(); ?>images/ajax_loader1.gif');
			
			request = $.ajax({
			      url:'<?php echo URL::base(); ?>qrhome/createQRcode',
			      type: "POST",
				  dataType: "html",
			      data:{
			        url : url,
					code_type:	code_type,
					session_id:	tmp_session_id		       
			      }
					
	    	});
			
			
		
		    request.done(function(data) 
			{
				
				if(data!="")
				{
					$('#code_preview_img').attr('src','<?php echo URL::base(); ?>images/home_qrcode/'+data);
		     	
					$("#code_preview_flag").val('1');
				}
				else
				{
					$('#code_preview_img').attr('src','<?php echo URL::base(); ?>images/qr_code_sample.png');
					$("#code_preview_flag").val('0');
				}
			
			});
			
		    request.fail(function(jqXHR, textStatus) {
		      //alert( "Request failed: " + textStatus );
		    });
			
			
		});
		
		//click on code type  
		$('.ExpFi2').on('click','.type_of_code',function()
		{
			url = $('#txt_url').val();
			code_type = $("input:radio[name='code_type']:checked").val();
			tmp_session_id = $('#tmp_session_id').val();
						
			var pattern = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	        if (!pattern.test(url)) {
	            return false;
	        } 
            
			if(typeof request !== 'undefined') //to abort previous ajax request
        		request.abort();
				
			$('#code_preview_img').attr('src','<?php echo URL::base(); ?>images/ajax_loader1.gif');
			
			request = $.ajax({
			      url:'<?php echo URL::base(); ?>qrhome/createQRcode',
			      type: "POST",
				  dataType: "html",
			      data:{
			        url : url,
					code_type:	code_type	,
					session_id:	tmp_session_id		
			      }
					
	    	});
			
		    request.done(function(data) 
			{
				if(data!="")
				{
					
					$('#code_preview_img').attr('src','<?php echo URL::base(); ?>images/home_qrcode/'+data);
		     	
					$("#code_preview_flag").val('1');
				}
				else
				{
					$('#code_preview_img').attr('src','<?php echo URL::base(); ?>images/qr_code_sample.png');
					$("#code_preview_flag").val('0');
				}
			
			});
			
		    request.fail(function(jqXHR, textStatus) {
		      //alert( "Request failed: " + textStatus );
		    });
			
			
		});
	
		
		setTimeout(function(){
								
				$('.client_tiker').jcarousel(
					{
						 auto: 0.1,
						 wrap:'circular', // both,last, circular
						 animation: {
					      	duration: 2000,
					        easing:   'linear'
					       }		
					}	
				).jcarouselAutoscroll({
					interval: 10,
					autostart:true
					
				});	
				
				
		},1000);
		
		$('#show_me_how').click(function(e){
		
		e.preventDefault();
		var url = $(this).attr("href");
		
		if(url != ''){
		  var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
		  var result = (url.match(p)) ? RegExp.$1 : false;
		  
		  if(result){
			$('#how_to_pop_up').html('<iframe src="'+url+'" frameborder="0" allowfullscreen wmode="transparent" width="480" height="300" ></iframe>');
			$('.black-tint').show();
			$('.player-window').show(500);
		  }else{
			  
			  var substr = 'image#';
			  if(url.indexOf(substr) !== -1){
				  url = url.replace(substr,"");
				  /*$('#how_to_pop_up_image').html('<img src="'+url+'" ></img>');*/
				  
				  $('.black-tint').show();
		  		  $('.player-window-image').show(500);3
			  }else{
			  	return false;
			  }
		  }
		}else{
			return false;
		}
	});
		
	});
	
	
	
</script>