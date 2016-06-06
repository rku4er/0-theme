<?php use Roots\Sage\Utils; ?>

<nav class="navbar container-fluid <?php Utils\sage_navbar_position_class(); ?>" role="navigation">

    <?php Utils\sage_header_logo(); ?>

    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#nav-menu">
        <span class="icon-menu">&#9776;</span>
        <span class="icon-close">&#10006;</span>
    </button>

    <div class="navbar-nav-wrapper collapse navbar-toggleable-xs" id="nav-menu">
        <?php if (has_nav_menu( 'primary_navigation')) wp_nav_menu(array(
            'theme_location'=>'primary_navigation', 'menu_class' => 'nav navbar-nav')); ?>
    </div>

</nav>
