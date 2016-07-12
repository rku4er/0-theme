<?php use Roots\Sage\Utils; ?>

<article <?php post_class(); ?>>

  <?php if( has_post_thumbnail() ) : ?>
    <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'alignleft')); ?>
  <?php else : ?>
    <img class="alignleft wp-post-image" src="<?php echo get_template_directory_uri (); ?>/dist/images/placeholder.png" alt="">
  <?php endif; ?>

  <p class="post-category"><?php the_category(', '); ?></p>

  <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

  <?php echo Utils\sage_get_excerpt(); ?>

</article>
