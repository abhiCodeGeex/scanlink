<nav class="top-navigation"><!-- NAVIGATION -->
    <section class="nav-in">
        <?php if (isset($logoutlnk) && $logoutlnk!="") { ?>
            <section class="welcome-box"><!-- WELCOME -->
                <span class="logout-btn logout-btn-new">
				<?php 
				if(isset($profile_id))
					echo '<a href="' .URL::base() . 'logout/index?profile_id='.$profile_id.'" >Logout</a>';
				else
				    echo '<a href="' .URL::base() . 'logout/index" >Logout</a>';
				?>
                </span>
                
            

                <span class="label">Welcome:</span><span class="username"><?php echo $logoutlnk; ?></span>
            </section><!-- /WELCOME -->
            <?php
        }
        ?>
        

            <ul class="menu" style="float: right;">
                <?php
                if($voc_user_logged_in==1)
                {
                ?>
                        <li>
                            <a href="<?php echo URL::base();?>dashboard/voc_user" class="dash-board" title="VOCC Dashboard">
                                <span class="text" style="padding-left:5px;" >
                                        <span>VOCC Dashboard</span>
                                </span>
                            </a>
                        </li>
                <?php
                }?>
                <li >
                    <a href="<?php echo URL::base();?>">
                        <img style="margin: 16px 20px 0 30px;" width="49" src="<?php echo URL::base();?>images/home_icon.png" alt="home" title="home">
                    </a>

                </li>
                <li >
                    <a href="<?php echo URL::base();?>welcome/contact" >
                        <img style="margin: 23px 20px 0 30px;" width="55" src="<?php echo URL::base();?>images/contact_us_icon.png" alt="contact us" title="contact us">
                    </a>
                </li>
            </ul>
    </section>

</nav><!-- /NAVIGATION -->

