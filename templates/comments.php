<?php
if (post_password_required()) {
  return;
}
?>

<?php
  function custom_comments($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment; ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
      <article class="comment-body" id="div-comment-<?php comment_ID() ?>">

       <div class="comment-meta">

         <span class="comment-author vcard">
           <?php echo get_avatar( $comment, 56 ); ?>
           <?php printf(__('%s'), get_comment_author_link()) ?>
         </span>

         <span class="comment-metadata">
           <em>on</em>
           <a class="comment-permalink" href="<?php echo htmlspecialchars ( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date('F j, Y'), get_comment_time('g:i A')) ?></a>
         </span>

         <span class="reply">
           <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
         </span>

       </div>

       <?php if ($comment->comment_approved == '0') : ?>
          <em><?php _e('Your comment is awaiting moderation.') ?></em>
       <?php endif; ?>

       <div class="comment-content">
         <?php comment_text(); ?>
       </div>

     </article>

   <?php
  }
?>

<section id="comments" class="comments-wrapper">
  <div class="post-comments-container">

    <?php if (have_comments()) : ?>

      <a id="comment-button" class="comment-toggler" data-toggle="collapse" href="#comments-container" aria-expanded="true" aria-controls="comments-container">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-comments">
          <use xlink:href="#comment"></use>
        </svg>
        <?php printf(_nx('One comment', '%1$s comments', get_comments_number(), 'comments title', 'sage'), number_format_i18n(get_comments_number()), '<span>' . get_the_title() . '</span>'); ?>
      </a>

      <div class="comment-list-container collapse in" id="comments-container">
        <ol class="comment-list">
          <?php wp_list_comments(array('style' => 'ul', 'short_ping' => true, 'callback' => __NAMESPACE__ . '\\custom_comments')); ?>
        </ol>
      </div>

      <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
        <nav>
          <ul class="pager">
            <?php if (get_previous_comments_link()) : ?>
              <li class="previous"><?php previous_comments_link(__('&larr; Older comments', 'sage')); ?></li>
            <?php endif; ?>
            <?php if (get_next_comments_link()) : ?>
              <li class="next"><?php next_comments_link(__('Newer comments &rarr;', 'sage')); ?></li>
            <?php endif; ?>
          </ul>
        </nav>
      <?php endif; ?>

    <?php else : ?>
      <a id="comment-button" class="comment-toggler collapsed" href="#comments-container">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-comments">
          <use xlink:href="#comment"></use>
        </svg>
        <?php _e('No comments yet', 'sage'); ?>
      </a>
    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() != '0' && post_type_supports(get_post_type(), 'comments')) : ?>
      <div class="alert alert-warning">
        <?php _e('Comments are closed.', 'sage'); ?>
      </div>
    <?php endif; ?>

  </div>

  <?php
   $commenter = wp_get_current_commenter();
   $req = get_option( 'require_name_email' );
   $aria_req = ( $req ? " aria-required='true'" : '' );

   $args = array(
    'fields' => apply_filters(
      'comment_form_default_fields', array(
        'author' =>'<p class="comment-form-author">' . '<input id="author" placeholder="Name" name="author" type="text" value="' .
          esc_attr( $commenter['comment_author'] ) . '" size="30" ' . $aria_req . '/>'. '</p>' ,
        'email'  => '<p class="comment-form-email">' . '<input id="email" placeholder="Email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
          '" size="30" ' . $aria_req . '/>'  . '</p>',
        'url'    => '<p class="comment-form-url">' .
         '<input id="url" name="url" placeholder="Website" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /> ' .
         '</p>'
      )
    ),
    'comment_field' => '<p class="comment-form-comment">' .
      '<textarea id="comment" name="comment" placeholder="Your comment" cols="45" rows="8" ' . $aria_req .'></textarea>' . '</p>',
      'comment_notes_before' => sprintf('<p>%s</p>', __('Your email address will not be published.', 'sage')),
      'comment_notes_after' => '',
      'label_submit' => __('Submit Comment', 'sage')
   );
  ?>

  <?php comment_form($args); ?>

</section>
