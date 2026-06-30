<style>
  .loaderdiv {
    width: 195px;
    height: 350px;
    display: none;
    filter: alpha(opacity=100);
    -moz-opacity: 0.7;
    -khtml-opacity: 0.7;
    opacity: 0.5;
    position: absolute;
    z-index: 999999;
  }

  .loaderdiv img {
    text-align: center;
    margin: 69% auto;
    padding-left: 103%;
    z-index: 999999;
    width: 50%;
    height: 50%
  }
</style>
<div class="loaderdiv">
  <img src="<?php echo url::base() . 'images/ajax_loader.gif'; ?>" id="loaderimg" />
</div>
<header id="header">
  <!-- HEADER -->
  <section class="header-in clearfix">
    <h1 id="logo">
      <a href="<?php print URL::base(); ?>"><img src="<?php print URL::base(); ?>images/logo.png" alt="" />
      </a>
    </h1>
  </section>
</header><!-- /HEADER -->
<?php echo $menu; ?>