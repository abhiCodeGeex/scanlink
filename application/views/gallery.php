<div class="title">
  <p>ScanLink Solutions are out and about in the community all the time.  Our photo gallery gives you a chance to see who has joined us in our quest to value people and value safety.</p>
</div>
<div class="content">
<div class="simple-rounded-box">
  <ul class="gallery clearfix">
    <?php /*?><li><a href="<?php echo URL::base();?>images/galleryApi/fullscreen/3.jpg" rel="prettyPhoto[gallery2]"><img src="<?php echo URL::base();?>images/galleryApi/fullscreen/3.jpg" alt="" /></a></li>
    <li><a href="<?php echo URL::base();?>images/galleryApi/fullscreen/4.jpg" rel="prettyPhoto[gallery2]"><img src="<?php echo URL::base();?>images/galleryApi/fullscreen/4.jpg" alt="" /></a></li>
    <li><a href="<?php echo URL::base();?>images/galleryApi/fullscreen/5.jpg" rel="prettyPhoto[gallery2]"><img src="<?php echo URL::base();?>images/galleryApi/fullscreen/5.jpg" alt="" /></a></li>
    <li><a href="<?php echo URL::base();?>images/galleryApi/fullscreen/1.jpg" rel="prettyPhoto[gallery2]"><img src="<?php echo URL::base();?>images/galleryApi/fullscreen/1.jpg" alt="" /></a></li>
    <li><a href="<?php echo URL::base();?>images/galleryApi/fullscreen/2.jpg" rel="prettyPhoto[gallery2]"><img src="<?php echo URL::base();?>images/galleryApi/fullscreen/2.jpg" alt="" /></a></li>
    <li><a href="<?php echo URL::base();?>images/galleryApi/fullscreen/6.jpg" rel="prettyPhoto[gallery2]"><img src="<?php echo URL::base();?>images/galleryApi/fullscreen/6.jpg" alt="" /></a></li><?php */?>
    <?php if(isset($images) && !empty($images)){
			foreach($images as $image){
				//$ext = pathinfo($image['name'], PATHINFO_EXTENSION);
				//$fname = pathinfo($image['name'],PATHINFO_FILENAME);
				$fname = $image['name'];
				$thumb = "thumb_".$fname;
		?>
    <li><a href="<?php echo URL::base();?>images/galleryApi/fullscreen/<?php echo $fname;?>" rel="prettyPhoto[gallery2]"><img src="<?php echo URL::base();?>images/galleryApi/fullscreen/<?php echo $thumb;?>" alt="" /></a></li>
    <?php }
		}
		else{
			echo '<div style="text-align:center;color:#5286BE;">No Profile Found..!</div>';
		}
	?>
  </ul>
</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal',slideshow:3000, autoplay_slideshow: true});
});
</script>