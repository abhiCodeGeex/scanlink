<?php
	function monthsBetween($startDate, $endDate) {
		$retval = "";
		// Assume YYYY-mm-dd - as is common MYSQL format
		$splitStart = explode('-', $startDate);
		$splitEnd = explode('-', $endDate);
		if (is_array($splitStart) && is_array($splitEnd)) {
			$difYears = $splitEnd[0] - $splitStart[0];
			$difMonths = $splitEnd[1] - $splitStart[1];
			$difDays = $splitEnd[2] - $splitStart[2];
			$retval = ($difDays > 0) ? $difMonths : $difMonths - 1;
			$retval += $difYears * 12;
		}
		return $retval;
	}
?>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/vader/jquery-ui.css" />
<script src="<?php echo URL::base();?>js/jquery-ui-timepicker-addon.js"></script>
<script>
$(document).ready(function(e) {
     $('.selectedDateTime').datetimepicker({
		timeFormat: 'HH:mm:ss',
		dateFormat: 'yy-mm-dd'
		//minDateTime:new Date()
	}); 
	
	$('.update-expire').live('click', function(){
		if(confirm("Are you sure you want to update expire date?")){
			var objThis = $(this);
			var expireDate = objThis.parents('.parent-block').find('input[name="expire_at"]').val();
			$.ajax({
				type:'post',
				data:{
					'expireDate':expireDate,
					'id':objThis.attr('rel')
				},
				url:'<?php echo URL::base();?>user/update',
				success:function(data){
					if(data){
						alert("Expire date updated successfully.");
						return false;
					}
				}
			});
		}
	});
	
	
	$('.renew-account').live('click', function(){
		var objThis = $(this);
		if(confirm("Are you sure you want to renew this account?")){
			$.ajax({
				type:'post',
				data:{
					'id':objThis.attr('rel')
				},
				url:'<?php echo URL::base();?>user/renewaccount',
				success:function(data){
					if(data){
						alert("Account has been actived successfully.");
						//document.location.href='<?php echo URL::base();?>user/index/';
						window.location.reload();
					}
				}
			});
		}
	});
	
	
	
});
</script>
<style>
.mini-input{
	width:120px !important;
}

</style>


<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td class="H1" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="50%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg"> &nbsp;<strong>Manage
        User </strong></td>
      <td align="right" width="50%"></td>
    </tr>
    <tr>
      <td align="left" height="8px" colspan="2"></td>
    </tr>
    <tr>
      <td background="images/vdots.gif" colspan="2"><img width="1" height="1" border="0" src="images/spacer.gif"></td>
    </tr>
    <tr>
      <td colspan="2">
        <table class="client-list" width="100%" cellspacing="1" cellpadding="3" border="0">
          <!--DWLayoutTable-->
            <tr>
              <td height="5" colspan="2"></td>
            </tr>
            <tr class="th-a">
              <td align="center" height="27" width="40%" class="vgg first"><strong>Email Address</strong></td>
              <td align="center" height="27" width="15%" class="vgg"><strong>Created</strong></td>
              <td align="center" height="27" width="15%" class="vgg"><strong>Expires</strong></td>
              <td align="center" height="27" width="30%"  class="vgg last"><strong>Option</strong></td>
            </tr>
            <tbody id="records">
            <?php 
				if(isset($userlist) && $userlist):
					$imgact = '';
					$imgper = '';
					foreach($userlist as $user):
						if($user['status'] == 1):
							$imgact = 'active';
						else:
							$imgact = 'blocked';
						endif;
						if($user['video_upload'] == 1):
							$imgper = 'youtube-yes';
						else:
							$imgper = 'youtube-no';
						endif;
						?>
                        <tr bgcolor="#CCCCCC">
                          <td align="left" valign="middle" class="vgg1"><?php echo $user['email']?></td>
                          <td align="left" valign="middle" class="vgg1">
					 	<?php echo $user['created_at'];?>
                              </td>
                          <td align="left" valign="middle" class="vgg1 parent-block">
                          	<input type="text"  class="mini-input selectedDateTime" name="expire_at" value="<?php echo $user['expire_at'];?>" />
                              <a href="javascript:void(0);" class="update-expire" rel="<?php echo $user['id']; ?>" >
                          <img src="<?php echo URL::base();?>images/save.png" width="20" /> 
                          </a>
                          </td>
                          <td align="center" class="vgg1">
                          	<div class="optionwrap"> 
                              <?php
							$month = monthsBetween(date('Y-m-d H:i:s'), $user['expire_at']);
							if($month <= 2){ ?>
								<a href="javascript:void(0);" class="option acount renew-account" title="Re-new user account" rel="<?php echo $user['id']; ?>"   > <img src="<?php echo URL::base();?>images/renew.png" width="30" /> </a>
							<?php }
						?>
                          	<a href="<?php echo URL::base();?>user/edit/<?php echo $user['id']?>/<?php echo $user['client_id']?>" class="option mlftrt edt" title="Edit user"> <img src="<?php echo URL::base();?>images/edit.png" width="20" /> </a> 
                            <a href="<?php echo URL::base();?>user/blocked/<?php echo $user['id']?>/<?php echo $user['client_id']?>" onclick="if(confirm('Are you sure to <?php echo $imgact == 'active' ? 'blocked':'active'; ?> this user?')){ return true; }else{ return false; }" class="option" title="click to <?php echo $imgact == 'active' ? 'blocked':'active'; ?>"> <img src="<?php echo URL::base();?>images/<?php echo $imgact;?>.png" width="20" /> </a> <a href="<?php echo URL::base();?>user/delete/<?php echo $user['id']?>/<?php echo $user['client_id']?>" onclick="if(confirm('Are you sure to delete this user?')){ return true; }else{ return false; }" class="option mlft" title="Delete user"> <img src="<?php echo URL::base();?>images/delete.png" width="20" /> </a> 
                            <?php /*<a href="<?php echo URL::base();?>user/videop/<?php echo $user['id'];?>/<?php echo $user['client_id']?>" onclick="if(confirm('Are you sure to change video permission to client?')){ return true; }else{ return false; }" class="option mlft" title="video permission to client"> <img src="<?php echo URL::base();?>images/<?php echo $imgper;?>.png" width="20" /> </a> */ ?>
                              </div>
                          </td>
                        </tr>
                        <?php
					endforeach;
				endif;
			?>
            </tbody>
            <tr align="center">
              <td colspan="6"></td>
            </tr>
        </table></td>
    </tr>
  </tbody>
</table>
<script type="text/javascript">        
$(document).ready(function(){
	$(".edt").colorbox({
		iframe:true,
		width:"30%",
		height:"55%",
		onClosed:function(){
			parent.location.reload()
		}
	});
	
	$(".edtplant").colorbox({iframe:true, width:"30%", height:"55%", onClosed:function(){parent.location.reload()}
});
});

$(function(){
	itemsPerPage = 15;
	paginatorStyle = 6;
	colSpan = 2;
	paginatorPosition = 'bottom';
	enableGoToPage = false;
	showIfSinglePage = false;
	$("#records").pagination();
});
</script>