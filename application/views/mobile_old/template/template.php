<!DOCTYPE HTML>
<html>
  <head>
    <?php echo $head; ?>

    
  </head>

  <body class="MobilePage" onload="setTimeout(function() { window.scrollTo(0, 1) }, 100);">
    <!-- Wrap start -->

    <!-- Header start -->
    <?php echo $headers; ?>
    <!-- Header end --> 


    <!-- Container start -->

    <div id="container"> 
      <?php echo $content; ?>

    </div>
    <!-- Container end --> 

    <!-- Footer start -->
    <?php echo $footer; ?>
    <!-- Footer end -->   
  </section>
  <!-- /CONTENT CONTAINER --> 
</section>
<!-- /CONTENT -->
<footer id="footer" class="clearfix">
  <section class="footer-right">
    <figure><img src="<?php echo URL::base() ?>images/PoweredbyScanLink.png" alt="" /></figure>
  </section>
</footer>
</section>
<!-- /Wrapper --> 
</section>
<!-- /MAIN CONTAINER -->
</body>
</html>
