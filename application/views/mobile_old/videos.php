<div class="MobileInnerList">
	<ul>
	<?php if(count($videos)!=0){ ?>
  	<?php $i=0; foreach($videos as $key => $video): $i++;?>
      <li><a href="<?php echo 'http://www.youtube.com/watch?v='.$video['video_name']; ?>" target="_blank"><?php echo $video['title']; ?></a></li>
    <?php endforeach; ?>
    <?php }else{ ?>
    	<li>No Videos</li>
    <?php } ?>
	</ul>
</div>