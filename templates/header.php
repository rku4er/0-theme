<?php use Roots\Sage\Utils; ?>

<nav id="navbar" class="navbar <?php Utils\sage_header_navbar_class(); ?>" role="navigation">

  <div class="navbar-container" >

    <div class="brand">

      <?php echo Utils\sage_get_logo(); ?>

      <button
        class="navbar-toggler collapsed"
        type="button"
        data-toggle="collapse"
        data-target="#nav-menu"
        aria-expanded="false"
        aria-controls="nav-menu">

        <span class="icon icon-menu">&#9776;</span>
        <span class="icon icon-close">&#10006;</span>
      </button>

    </div>

    <div id="nav-menu" class="collapse" role="tabpanel" aria-labelledby="nav-menu">
      <?php if (has_nav_menu( 'primary_navigation')) wp_nav_menu(array(
        'theme_location'=>'primary_navigation',
        'menu_class' => 'nav navbar-nav'
      )); ?>
    </div>

  </div>

</nav>
