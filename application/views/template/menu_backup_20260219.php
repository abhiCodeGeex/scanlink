<nav class="top-navigation"><!-- NAVIGATION -->
    <section class="nav-in">
        <?php if ( isset($logoutlnk) && $logoutlnk != '') { ?>
            <section class="welcome-box"><!-- WELCOME -->
                <?php /*<span class="logout-btn"><input onclick="javascript: location.href='<?php echo URL::base() . "logout/index"; ?>'" type="submit" value="Logout" /></span> */ ?>
                <?php /*<span class="logout-btn"><input onclick="javascript: logoutFunction('<?php if(isset($msgPopup)){echo $msgPopup; }else{echo 'false'; } ?>');" type="submit" value="Logout" /></span>*/ ?>
                
                <span class="logout-btn logout-btn-new">
				<?php 
				if(isset($profile_id))
					echo '<a href="' .URL::base() . 'logout/index?profile_id='.$profile_id.'" >Logout</a>';
				else
				    echo '<a href="' .URL::base() . 'logout/index" >Logout</a>';
				?>
                </span>
                
                <div style='display:none;'>
                	<a class='popup_logout_class' href="#popup_logout" style="padding: 0;" id="popup_logout_tag">
                    	<input class="expried" type="button" id="expriedbutton" value="expried div" />
                    </a>
  					<div id='popup_logout' style='padding:10px; background:#fff;'>
					    Please click save before logging out
					 </div>
				</div>

				<script type="text/javascript">
				  $(document).ready(function(){
				    $(".popup_logout_class").colorbox({inline:true, width:"25%"});
				  });
				</script>

                <span class="label">Welcome:</span><span class="username"><?php echo $logoutlnk; ?></span>
            </section><!-- /WELCOME -->
            <?php
        }
        ?>
        <ul class="menu" >
            <?php 
            if ($menu):
                foreach ($menu as $menuitem):
                    $selected = '';
                    $dashboarditems = explode('/', $menuitem[2]);
                    $selectitem = explode('/', $currentactive);
                         //echo $menuitem;
                if ($currentactive != "welcome/index") {
                        if ($menuitem[2] == $currentactive):
                            $selected = 'active';
                        elseif ($dashboarditems[0] == 'dashboard' && ($currentactive == 'dashboard/mastercode'||$currentactive == 'dashboard/location' || $currentactive == 'dashboard/asset' || $currentactive == 'dashboard/product' || $currentactive == 'dashboard/procedure' || $currentactive == 'plant/index' || $currentactive == 'location/index' || $currentactive == 'asset/index' || $currentactive == 'product/index' || $currentactive == 'procedure/index' || $currentactive == 'plant/edit' || $currentactive == 'location/edit' || $currentactive == 'asset/edit' || $currentactive == 'product/edit' || $currentactive == 'procedure/edit' || $currentactive == "dashboard/scanalytics" || $currentactive == "dashboard/orderLabel" || $currentactive == 'dashboard/changepwd')):
                            $selected = 'active';
                        endif;
                    }
                    
                    if( ($menuitem[2]=="voclogin" && (!isset($logoutlnk) ||  $logoutlnk=="")) || $menuitem[2]!="voclogin")
                    {?>  
                    <li <?php if ($menuitem[0] == "My Account") {?> id="my_account" <?php }else if($menuitem[0] == "How to"){ ?> id="how_to" <?php  } ?>>
                     <?php   if($menuitem[0] == "Dashboard")
						{?>
							<a  href="<?php echo URL::base();?>dashboard/mastercode"; 
						<?php }else{?>
                        <a href="<?php echo URL::base() . $menuitem[2]; ?>" 
                        <?php }
                        if ($menuitem[0] == "My Account" || $menuitem[0] == "How to") {
                            echo "class='selectSub " . $menuitem[3] . " " . $selected . "'";
                        } 
                        else if($menuitem[2]=="voclogin")
                        {
                            echo "class='voclogin'";
                        }
						
                        else {
                            echo "class=" . $menuitem[3] . " " . $selected;
							
                        }
                        
                        ?> 
                           >
                            <?php if($menuitem[2]=="voclogin")
                            {
                            ?>
                                <span class="text" >
                                    <span><?php echo $menuitem[0]; ?></span>
                                </span>
                            <?php 
                            }
                            else
                            {
                            ?>
                                <span class="text">
                                    <span><?php echo $menuitem[0]; ?></span>
                                </span>
                            <?php
                            }?>
                        </a>
                            <?php if ($menuitem[0] == "My Account"): ?>
                            <ul class="subMenu" id="subMenu" ><?php if ($menuSubmenu): ?><?php foreach ($menuSubmenu as $submenuitem): ?><li><a href="<?php echo URL::base() . $submenuitem[2]; ?>"  ><?php echo $submenuitem[0]; ?> </a></li><?php endforeach; ?><?php endif; ?></ul>
                            <?php elseif($menuitem[0] == "How to"): ?>
                            	<ul class="subMenu" id="subMenu2" >
                                    <li><a href="https://www.youtube.com/embed/9aTjweHyAWw?rel=0" title="Create a ScanLink account">Create a ScanLink account</a></li>
                                    <li><a href="https://www.youtube.com/embed/o6NxTt0CmYI?rel=0" title="Getting Started">Getting Started</a></li>
                                    <li><a href="https://www.youtube.com/embed/GZ12nXTO7_w?rel=0" title="Register a new code">Register a new code</a></li>
                                    <li><a href="https://www.youtube.com/embed/hGrBYsys2Oo?rel=0" title="Upload a logo">Upload a logo</a></li>
                                    <li><a href="https://www.youtube.com/embed/H33caspIlcc?rel=0" title="Upload a video">Upload a video</a></li>
                                    <li><a href="https://www.youtube.com/embed/CZx8xplEfoU?rel=0" title="Add text and phone numbers">Add text and phone numbers</a></li>
                                    <li><a href="https://www.youtube.com/embed/GshHCp9F0wU?rel=0" title="Upload pictures">Upload pictures</a></li>
                                    <li><a href="https://www.youtube.com/embed/ujiEr65yg30?rel=0" title="Upload documents">Upload documents</a></li>
                                    <li><a href="https://www.youtube.com/embed/id0I8j8RTuY?rel=0" title="Add web link buttons">Add web link buttons</a></li>
                                    <li><a href="https://www.youtube.com/embed/qOi6tSBsII4?rel=0" title="Add social media and email share buttons">Add social media and email share buttons</a></li>
                                    <li><a href="https://www.youtube.com/embed/C_vH14MFtXA?rel=0" title="Create pop up messages to collect data">Create pop up messages to collect data</a></li>
                                    <li><a href="https://www.youtube.com/embed/jCeyQOfm7uc?rel=0" title="Select a code type - QR or Data matrix">Select a code type - QR or Data matrix</a></li>
                                    <li><a href="https://www.youtube.com/embed/eJtzHbZoCPw?rel=0" title="Feature code profile number on mobile display">Feature code profile number on mobile display</a></li>
                                    <li><a href="https://www.youtube.com/embed/KcXJnxuMVyc?rel=0" title="Password protect a code profile">Password protect a code profile</a></li>
                                    <li><a href="https://www.youtube.com/embed/uEDTnBPUk28?rel=0" title="Link a code to a URL">Link a code to a URL</a></li>
									<li><a href="https://www.youtube.com/embed/Gu12cnKn16s?rel=0" title="Delete a code profile">Delete a code profile</a></li>
									<li><a href="https://www.youtube.com/embed/Y0bVkzDA5Rc?rel=0" title="View and download scan activity">View and download scan activity</a></li>
									<li><a href="https://www.youtube.com/embed/cYQnzxkp528?rel=0" title="Create a form">Create a form</a></li>
									
									</ul>
                    <?php endif; ?>
                    </li>
                    <?php }
                    
                endforeach;
            endif;
            ?>  
        </ul>

<?php if ( (!isset($logoutlnk) || $logoutlnk == '') && $voc_user_logged_in==0) { ?>
            <span class="login-menu">
                <a href="#">
                    <span class="text">Login</span>
                </a>
            </span>
            <section class="login-box" id="login_box">
                    <?php //echo isset($loginbox) ? $loginbox : '';  ?>
                    <?php if (isset($errors['eadd'])): ?>
                    <span style="color: rgb(255, 0, 0); height: 14px; line-height: 12px; padding-bottom: 10px; display: block;">
                    <?php echo $errors['eadd']; ?>
                    </span>
                <?php endif; ?>

                    <?php if (isset($errors['pass'])): ?>
                    <span style="color: rgb(255, 0, 0); height: 14px; line-height: 12px; padding-bottom: 10px; display: block;">
                    <?php echo $errors['pass']; ?>
                    </span>
                <?php endif; ?>

                <?php echo Form::open(URL::base() . 'welcome/index#lgbox', array('id' => 'signin', 'method' => 'post')) ?>
                
                <?php echo Form::label('name', 'Email') ?>
                <?php echo Form::input('email', isset($post['email']) ? $post['email'] : '', array('class' => 'text-fi')) ?>
                <br/>
                <?php echo Form::label('pass', 'Password') ?>
                <?php echo Form::input('password', '', array('type' => 'password', 'class' => 'text-fi')) ?>
                <br/>
                <label class="lesslineheight">&nbsp;</label>
                <a href="javascript:void(0);" id="forgot_pass_link" >Forgot your password? </a>
                <br/>
            <?php echo Form::submit(NULL, 'Login', array('id' => 'signin_submit')) ?>
            <?php echo Form::close() ?>
            </section>
            
            <section class="forgot-box" id="forgot-box">
            	<span id="emailNill" style="color: rgb(255, 0, 0); height: 14px; line-height: 12px; padding-bottom: 10px; display: none;">Please enter a email</span>
                <span id="emailInvalid" style="color: rgb(255, 0, 0); height: 14px; line-height: 12px; padding-bottom: 10px; display: none;">Please enter a valid email</span>
                <span id="message_forgot_error" style="color: rgb(255, 0, 0); height: 14px; line-height: 12px; padding-bottom: 10px; display: none;"></span>
                <span id="message_forgot_success" style="color: #278B28; height: 14px; line-height: 12px; padding-bottom: 10px; display: none;"></span>
                <?php echo Form::open(URL::base() . 'welcome/index', array('id' => 'signin', 'method' => 'post')) ?>
                
                <?php echo Form::label('name', 'Email') ?>
                <?php echo Form::input('txtemail', isset($post['txtemail']) ? $post['txtemail'] : '', array('class' => 'text-fi','id'=>'txtemail')) ?>
                <br/>
            <?php echo Form::submit('btnforgot', 'Forgot', array('id' => 'btnforgot')) ?>
            <?php echo Form::close() ?>
            </section>
            <?php
            if (isset($errors['eadd']) || isset($errors['pass'])):
                ?>
                <script language="javascript" type="text/javascript">
                    $('.login-menu a').addClass("active");
                    $('.login-box').show();
                    $('.selectSub').click(function() {
                        $('#subMenu').toggle('slow', function() {
                        });
                    });
                </script>
                <?php
            endif;
        }
        ?>
    </section>

</nav><!-- /NAVIGATION -->

<script type="text/javascript">
	$('#my_account').mouseout(function() {
		$('#subMenu').hide();
		$('#my_account a').removeClass('active');
	});
	
	$('#my_account').mouseover(function() {
		$('#subMenu').show();
		$('#my_account a').addClass('active');
	});
	$('#subMenu').mouseout(function(){
		$('#my_account a').removeClass('active');
		
	});
	$('#subMenu').mouseover(function(){
		$('#my_account a').addClass('active');
	});
	
	
	
	
	$('#how_to').mouseout(function() {
		$('#subMenu2').hide();
		$('#how_to a').removeClass('active');
		<?php if (!(isset($logoutlnk) && $logoutlnk != '')) { ?>
			$('.login-menu a').removeClass('active');
			$('.login-box').hide();
			$('.forgot-box').hide();
		<?php } ?>
	});
	
	$('#how_to').mouseover(function() {
		$('#subMenu2').show();
		$('#how_to a').addClass('active');
		<?php if (!(isset($logoutlnk) && $logoutlnk != '')) { ?>
			$('.login-menu a').removeClass('active');
			$('.login-box').hide();
			$('.forgot-box').hide();
		<?php } ?>
	});
	$('#subMenu2').mouseout(function(){
		$('#how_to a').removeClass('active');
		<?php if (!(isset($logoutlnk) && $logoutlnk != '')) { ?>
			$('.login-menu a').removeClass('active');
			$('.login-box').hide();
			$('.forgot-box').hide();
		<?php } ?>
		
	});
	$('#subMenu2').mouseover(function(){
		$('#how_to a').addClass('active');
		<?php if (!(isset($logoutlnk) && $logoutlnk != '')) { ?>
			$('.login-menu a').removeClass('active');
			$('.login-box').hide();
			$('.forgot-box').hide();
		<?php } ?>
	});
	
	
</script>


<?php if (!(isset($logoutlnk) && $logoutlnk != '')) { ?>
<script type="text/javascript">
	
	$('#forgot_pass_link').click(function(){
		$('.login-box').hide();
		$('.forgot-box').show();
	});
	$('.login-menu').click(function(){
		$('.forgot-box').hide();
		$('#message_forgot_success').html('');
		$('#message_forgot_error').html('');
		$('#message_forgot_success').hide();
		$('#message_forgot_error').hide();
		$('#emailNill').hide();
		$('#emailInvalid').hide();
		$('#txtemail').val('');
	});
	$('#btnforgot').click(function(){
		$('#message_forgot_success').html('');
		$('#message_forgot_error').html('');
		if($('#txtemail').val() == ''){
			$('#emailNill').show();
			$('#txtemail').focus();
			return false;
		}else{
			$('#emailNill').hide();
			var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
		 	if (!emailRegexStr.test($('#txtemail').val())) {
 				$('#emailInvalid').show();
				$('#txtemail').focus();
				return false;
 			}else{
				$('#emailInvalid').hide();
				var email = $('#txtemail').val();
				var request = $.ajax({
				  url:'<?php echo URL::base(); ?>welcome/forgot',
				  type: "POST",
				  dataType: "Json",
				  data:{
					txtemail : email,
					btnforgot : 'btnforgot'
				  }
					
				});
				request.done(function(data) {
					if(data.error == '1'){
						$('#message_forgot_success').html(data.message);
						$('#message_forgot_error').hide();
						$('#message_forgot_success').show();
						$('#txtemail').val('');
					}else{
						$('#message_forgot_error').html(data.message);
						$('#message_forgot_success').hide();
						$('#message_forgot_error').show();
					}
				});
				request.fail(function(jqXHR, textStatus) {
      				alert( "Request failed: " + textStatus );
    				});
				
			}
		}
		return false;
	});
	
</script>

<?php } 

if (isset($_REQUEST["open_login_box"]) && $_REQUEST["open_login_box"] ==1) { ?>
<script type="text/javascript">
	$('.login-menu a').addClass("active");
	$('.login-box').show();
	
</script>
<?php } 


?>