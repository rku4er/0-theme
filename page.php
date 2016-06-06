<?php while (have_posts()) : the_post(); ?>
    <div class="page-header">
      <div class="container">
        <h1><?php the_title(); ?></h1>
        <?php do_action('sage_after_page_title'); ?>
      </div>
    </div>
    <div class="page-content">
        <?php get_template_part('templates/content', 'page'); ?>
    </div>
<?php endwhile; ?>
