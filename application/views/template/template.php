<!DOCTYPE HTML>
<html>
  <head>
    <?php echo $head; ?>
  </head>
  <body>
    <section class="main-container clearfix"><!-- MAIN CONTAINER -->

      <?php echo $headers; 
      
      if(isset($currentPage) && ($currentPage=="marketing" || $currentPage=="workplace" || $currentPage=="mobilevideo" || $currentPage=="pricing"))
      {
          $wrapper_class= "wrapper-small";
      }
      else
      {
           $wrapper_class= "wrapper";
      }
      
      ?>
        
      <section class="<?php echo $wrapper_class;?> clerfix"><!-- Wrapper -->
        <section id="content"><!-- CONTENT -->

          <?php if (isset($currentPage) && $currentPage == 'home') { ?>
           <?php /* <section class="showcase"><!-- SHOWCASE -->
              <img src="<?php echo URL::base(); ?>images/main-img.png" alt="" />
            </section><!-- /SHOWCASE --> */ ?>
          <?php } ?>
          <?php if (isset($currentPage) && $currentPage == 'mobilevideo') { ?>
            <section class="showcase"><!-- SHOWCASE -->
              <img src="<?php echo URL::base(); ?>images/banner.jpg" alt="banner image" />
              <div class="iphone-banner"></div>
				  <div class="film-reel"></div>
            </section><!-- /SHOWCASE -->
          <?php } ?>
          <?php if (isset($currentPage) && $currentPage == 'workplace') { ?>
            <section class="showcase"><!-- SHOWCASE -->
              <!--<img src="<?php echo URL::base(); ?>images/banner_workplace.jpg" alt="banner image" />-->
              <img src="<?php echo URL::base(); ?>images/banner_workplace.png" width="1024" alt="banner image" />
            </section><!-- /SHOWCASE -->
          <?php } ?>
          <?php if (isset($currentPage) && $currentPage == 'marketing') { ?>
            <section class="showcase"><!-- SHOWCASE -->
              <img src="<?php echo URL::base(); ?>images/banner_marketing.jpg" alt="banner image" />
				  <div class="iphone-marketing"></div>
            </section><!-- /SHOWCASE -->
          <?php } ?>
          <?php if (isset($currentPage) && $currentPage == 'pricing') { ?>
            <section class="showcase"><!-- SHOWCASE -->
              <img src="<?php echo URL::base(); ?>images/banner_pricing.jpg" alt="banner image" />
            </section><!-- /SHOWCASE -->
          <?php } ?>

	<?php if ( (isset($currentPage) && $currentPage == 'workplace') || (isset($currentPage) && $currentPage == 'formbuilderpage') ) 
			{ 
				echo $content;
			}
			else
			{
			?>
			    <section class="content-container cearfix"><!-- CONTENT CONTAINER -->
		            <?php echo $content; ?>
		        </section>
			<?php	
			}?>
        </section><!-- /CONTENT -->				
        <?php echo $footer; ?> 
      </section><!-- /Wrapper -->

    </section><!-- /MAIN CONTAINER -->		
  </body>
</html>