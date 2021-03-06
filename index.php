<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
<?php endif; ?>

<ul class="article-list">
  <?php while (have_posts()) : the_post(); ?>
    <?php get_template_part('templates/content', 'latest-article'); ?>
  <?php endwhile; ?>
</ul>

<?php the_posts_navigation(); ?>
