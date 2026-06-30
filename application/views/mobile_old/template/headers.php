<?php if ($show_header == 1): ?>
  <nav class="top-navigation"><!-- NAVIGATION -->
    <section class="nav-in">
      <h2>Profile No: <?php echo $profile_id; ?></h2>
    </section>
  </nav>
<?php endif; ?>
<!-- /NAVIGATION -->
<section class="wrapper clerfix"><!-- Wrapper -->
  <section id="content"><!-- CONTENT -->
    <section class="content-container clearfix"><!-- CONTENT CONTAINER -->
      <?php if (isset($logo) && $logo != ""): ?>
        <div class="BannerImage">
          <a href="#"><img src="<?php echo URL::base() . $logo; ?>" width="100%" alt=""></a>
        </div>
      <?php endif; ?>
