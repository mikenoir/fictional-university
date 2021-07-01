<!DOCTYPE html>
<html <?=language_attributes();?>>
<head>
    <meta charset="<?=bloginfo('charset');?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?=body_class();?>>
    <header class="site-header">
        <div class="container">
            <h1 class="school-logo-text float-left">
                <a href="<?=site_url();?>"><strong>Fictional</strong> University</a>
            </h1>
            <span class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
            <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
            <div class="site-header__menu group">
                <nav class="main-navigation">
                    <?php wp_nav_menu([
                        'theme_location'    => 'headerMenuLocation'
                    ]); ?>
                </nav>
                <div class="site-header__util">
                    <?php
                    if( is_user_logged_in() ) { ?>
                        <a href="<?php echo wp_logout_url(); ?>" class="btn btn--small btn--dark-orange float-left">Log Out</a>
                        <?php
                    } else { ?>
                         <a href="#" class="btn btn--small btn--orange float-left push-right">Login</a>
                        <a href="<?php echo wp_registration_url(); ?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a>
                        <?php
                    } ?>
                    <span class="search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
                </div>
            </div>
        </div>
    </header>
