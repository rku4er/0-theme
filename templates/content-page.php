<?php use Roots\Sage\Utils; ?>
<?php echo Utils\sage_get_sections(); ?>
<?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>')); ?>
