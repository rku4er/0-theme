<?php use Roots\Sage\Utils; ?>

<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>

    <p class="post-category"><?php Utils\sage_post_categories(); ?></p>

    <?php echo Utils\sage_get_heading('h1'); ?>

    <p class="post-metadata">
      <?php the_time('F j, Y'); ?> by <?php Utils\sage_author_archive_link(get_the_ID()); ?>
    </p>

    <?php the_content(); ?>

    <?php wp_link_pages(array(
      'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'),
      'after' => '</p></nav>')); ?>
    <?php comments_template('/templates/comments.php'); ?>

  </article>
<?php endwhile; ?>
