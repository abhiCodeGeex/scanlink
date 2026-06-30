<script>
	$(document).ready(function(){
		setTimeout(function(){
			$('.ui-notify-message').animate({top: '-300px',}, 1000);		
		},5000);
		
		$('.ui-notify-close').live('click',function(){
			$('.ui-notify-message').animate({top: '-300px',}, 1000);
		});
	});
	
	
</script>
<?php if($sf_user->hasFlash("success")): ?>
<div class="ui-notify-message ui-notify-message-style ui-notify-success" ><a href="#" class="ui-notify-cross ui-notify-close">x</a>
  <h1>Success!</h1>
  <p><?php echo $sf_user->getFlash("success"); ?></p>
</div>
<?php endif; ?>
<?php if($sf_user->hasFlash("info")): ?>
<div class="ui-notify-message ui-notify-message-style ui-notify-info" ><a href="#" class="ui-notify-cross ui-notify-close">x</a>
  <h1>Information!</h1>
  <p><?php echo $sf_user->getFlash("info"); ?></p>
</div>
<?php endif; ?>

<?php if($sf_user->hasFlash("warn")): ?>
<div class="ui-notify-message ui-notify-message-style ui-notify-warn" ><a href="#" class="ui-notify-cross ui-notify-close">x</a>
  <h1>Warnning!</h1>
  <p><?php echo $sf_user->getFlash("warn"); ?></p>
</div>
<?php endif; ?>
<?php if($sf_user->hasFlash("error")): ?>
<div class="ui-notify-message ui-notify-message-style ui-notify-error" ><a href="#" class="ui-notify-cross ui-notify-close">x</a>
  <h1>Error!</h1>
  <p><?php echo $sf_user->getFlash("error"); ?></p>
</div>
<?php endif; ?>