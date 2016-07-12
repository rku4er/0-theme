<?php
  use Roots\Sage\Wrapper;
  use Roots\Sage\Utils;
?>

<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>

  <?php get_template_part('templates/head'); ?>

  <body <?php body_class(); ?> id="document">

    <!--[if IE]>
      <div class="alert alert-warning">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?>
      </div>
    <![endif]-->

    <?php get_template_part('templates/icons-svg'); ?>

    <div class="wrap" role="document">

      <?php
          do_action('get_header');
          get_template_part('templates/header');
      ?>

      <main class="main" role="main">
          <?php include Wrapper\template_path(); ?>
      </main><!-- /.main -->

      <?php if (Utils\display_sidebar()) : ?>
        <aside class="sidebar" role="complementary">
          <?php include Wrapper\sidebar_path(); ?>
        </aside><!-- /.sidebar -->
      <?php endif; ?>


    </div><!-- /.wrap -->

    <?php
        do_action('get_footer');
        get_template_part('templates/footer');
        wp_footer();
    ?>

  </body>
</html>
