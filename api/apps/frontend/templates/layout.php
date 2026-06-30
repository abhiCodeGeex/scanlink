<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="<?php echo image_path('favicon.ico'); ?>" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <script>
      $(function(){
        $('input[type=button]').each(function(){
          $(this).addClass("btn");
        });	
        $('input[type=submit]').each(function(){
          $(this).addClass("btn btn-success");
        });	
      });
    </script>
  </head>
  <body>
    <?php include_partial('global/message'); ?>
    <?php if ($sf_user->isAuthenticated() == true): ?>

      <header>
        <div class="navbar navbar-inverse navbar-fixed-top">
          <div class="navbar-inner">
            <div class="container"> <a href="<?php echo url_for("dashboard/index", true); ?>" class="brand">Analytics</a>
              <div class="nav-collapse collapse">
                <ul class="nav">
                  <?php if ($sf_user->hasCredential('admin')): ?>
                    <li class="dropdown"> <a class="dropdown-toggle"   data-toggle="dropdown"  href="#"> Clients <b class="caret"></b> </a>
                      <ul class="dropdown-menu">
                        <li><a href="<?php echo url_for("client/new"); ?>">Create User</a></li>
                        <li><a href="<?php echo url_for("client/index"); ?>">Manage Users</a></li>
                      </ul>
                    </li>
                  <?php endif; ?>
                  <li class="dropdown"> <a class="dropdown-toggle"   data-toggle="dropdown"  href="#"> Projects <b class="caret"></b> </a>
                    <ul class="dropdown-menu">
                      <li><a href="<?php echo url_for("project/new"); ?>">Create Project</a></li>
                      <li><a href="<?php echo url_for("project/index"); ?>">Manage Projects</a></li>
                    </ul>
                  </li>
                  <li class="dropdown"> <a class="dropdown-toggle"   data-toggle="dropdown"  href="#"> Items <b class="caret"></b> </a>
                    <ul class="dropdown-menu">
                      <li><a href="<?php echo url_for("item/index"); ?>">Manage Items</a></li>
                    </ul>
                  </li>
                  <?php if ($sf_user->hasCredential('admin')): ?>
                    <li class="dropdown"> <a class="dropdown-toggle"   data-toggle="dropdown"  href="#"> Platforms <b class="caret"></b> </a>
                      <ul class="dropdown-menu">
                        <li><a href="<?php echo url_for("platform/new"); ?>">Create Platform</a></li>
                        <li><a href="<?php echo url_for("platform/index"); ?>">Manage Platforms</a></li>
                      </ul>
                    </li>
                    <li class="dropdown"> <a class="dropdown-toggle"   data-toggle="dropdown"  href="#"> Devices <b class="caret"></b> </a>
                      <ul class="dropdown-menu">
                        <li><a href="<?php echo url_for("device/new"); ?>">Create Device</a></li>
                        <li><a href="<?php echo url_for("device/index"); ?>">Manage Devices</a></li>
                      </ul>
                    </li>
                    <li class="dropdown"> <a class="dropdown-toggle"   data-toggle="dropdown"  href="#"> Browsers <b class="caret"></b> </a>
                      <ul class="dropdown-menu">
                        <li><a href="<?php echo url_for("browser/new"); ?>">Create Browser</a></li>
                        <li><a href="<?php echo url_for("browser/index"); ?>">Manage Browsers</a></li>
                      </ul>
                    </li>
                  <?php endif; ?>
                  
                </ul>
                <ul class="nav pull-right">
                  <li class="dropdown"> <a class="dropdown-toggle"   data-toggle="dropdown"  href="#"> Welcome  <?php echo $sf_user->getAttribute('username'); ?> <b class="caret"></b> </a>
                    <ul class="dropdown-menu">
                      <li><a href="#">Profile</a></li>
                      <li><a href="<?php echo url_for('client/changepassword'); ?>">Change Password</a></li>
                      <li><a href="<?php echo url_for("client/loginreport"); ?>">Check Login Report</a></li>
                      <li id="fat-menu" > <a href="<?php echo url_for('auth/logout'); ?>" role="button">Logout</a> </li>
                    </ul>
                  </li>

                </ul>
              </div>
            </div>
          </div>
        </div>
      </header>
    <?php endif; ?>
    <?php echo $sf_content ?>
    <?php if ($sf_user->isAuthenticated() == true): ?>
      <div class="clear"></div>
      <footer>
        <div class="page-header">
          <h4 align="center"><small>&copy;Copyright 2012 All rights reserved. </small></h4>
        </div>
      </footer>
    <?php endif; ?>
  </body>
</html>
