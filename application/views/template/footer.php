    <footer id="footer" class="clearfix">
      <section class="footer-left">
        <ul>
          <li><a href="<?php echo URL::base(); ?>">Home</a></li>
          <li><a href="#" class="scan-link">What is ScanLink?</a></li>
          <?php /* <li><a href="<?php echo URL::base();?>welcome/testimonial">Testimonial</a></li> */ ?>
          <li><a href="<?php echo URL::base(); ?>faq/index/">FAQ</a></li>
          <li><a href="<?php echo URL::base(); ?>welcome/contact">Contact us</a></li>
          <li><a href="<?php echo URL::base(); ?>termsnndcondition/index">Terms and Conditions</a></li>
          <li class="last"><a href="<?php echo URL::base(); ?>privacypolicy/index">Privacy Policy</a></li>
        </ul>
      </section>
      <section class="footer-right">
        <div class="aus-tag">
          <img src="<?php echo URL::base(); ?>images/aus-map-icon.png" alt="" />
          <span>An Australian Innovation</span>
        </div>
        <div class="fleft">
          <figure class="fleft"><a href="http://www.scanlink.com.au/"><img src="<?php echo URL::base(); ?>images/img3.png" alt="" /></a></figure>
          <span>Powered by <a href="http://www.scanlink.com.au/">ScanLink Technologies</a></span>
        </div>
      </section>
    </footer>

    <?php /*
	<!-- custome pop up -->
  <section class="black-tint"></section>
  <section class="player-window">
  <span class="close-btn"></span>
  	<section class="player-window-in">
   	 	<iframe src="http://www.youtube.com/embed/dtjwTrk-ons?rel=0" frameborder="0" allowfullscreen wmode="transparent" width="540" height="430" ></iframe>
  	 	<?php //<img src="<?php echo URL::base();?>images/player-screen.png" alt="" /> ?>
    </section>
  </section><!-- /custome pop up -->
  */ ?>
    <!-- custome pop up 2-->
    <section class="black-tint"></section>
    <section class="player-window">
      <span class="close-btn"></span>
      <section class="player-window-in" id="how_to_pop_up">

      </section>
    </section><!-- /custome pop up 2-->

    <section class="black-tint"></section>
    <section class="player-window-new">
      <span class="close-btn"></span>
      <section class="player-window-in" id="details_pop_up">

      </section>
    </section><!-- /custome pop up -->

    <!-- custome pop image up 2-->
    <section class="black-tint"></section>
    <section class="player-window-image">
      <span class="close-btn"></span>
      <div class="player-window-img-div">
        <section class="player-window-image-in" id="how_to_pop_up_image">
          <img src="<?php echo URL::base(); ?>images/user_account_open.jpg">
        </section>
      </div>
    </section><!-- /custome pop up 2-->
    <script>
      (function(i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function() {
          (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
          m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
      })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

      ga('create', 'UA-47157478-1', 'scanlink.com.au');
      ga('send', 'pageview');
    </script>

    <script type="text/javascript">
      $(document).ready(function() {
        <?php if (!empty($_SESSION['clientuserid']) && empty($_SESSION['is_password_change'])) { ?>

          $.colorbox({
            width: '40%',
            height: '40%',
            html: '<iframe width="504" height="350" src="<?php echo URL::base(); ?>passwordchange/change_password_iframe" ></iframe>',
            onOpen: function() {
              $("#colorbox").addClass("colorbox-container");
              $("body").addClass('popup-overflow');
            },
            onClosed: function() {
              $("body").removeClass('popup-overflow');
            }
          });

        <?php } ?>
      });
    </script>
    <style>
      .popup-overflow {
        overflow: hidden;
      }

      #colorbox {
        height: 400px !important;
        position: absolute !important;
        top: 19% !important;
        padding-right: 42px !important;
        padding-bottom: 15px !important;
      }

      #cboxWrapper {
        height: 400px !important;
      }

      #cboxMiddleLeft {
        height: 362px !important;
      }

      #cboxContent {
        height: 362px !important;
      }

      #cboxLoadedContent {
        height: 607px !important;
      }

      #cboxLoadedContent iframe {
        border: none;
        height: 400px;
      }

      #cboxMiddleRight {
        height: 362px !important;
      }
    </style>