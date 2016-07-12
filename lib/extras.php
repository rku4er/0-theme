<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Utils;

/**
 * Add <body> classes
 */

add_filter('body_class', __NAMESPACE__ . '\\sage_body_class');

function sage_body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }
  // Add class if sidebar is active
  if (Utils\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }
  return $classes;
}


/**
 * Custom excerpt length
 */

function sage_excerpt_length( $length ) {
    return 35;
}
add_filter( 'excerpt_length', __NAMESPACE__ . '\\sage_excerpt_length', 999 );


/**
 * Clean up the_excerpt()
 */

add_filter('excerpt_more', __NAMESPACE__ . '\\sage_excerpt_more');

function sage_excerpt_more() {
  return '&hellip; <a class="more" href="' . get_permalink() . '">' . __('Read More', 'sage') . '</a>';
}


/**
 * Filtering the Wrapper: Custom Post Types
 */

add_filter('sage/wrap_base', __NAMESPACE__ . '\\sage_wrap_base_cpts');

function sage_wrap_base_cpts($templates) {
    $cpt = get_post_type();
    if ($cpt) {
       array_unshift($templates, __NAMESPACE__ . 'base-' . $cpt . '.php');
    }
    return $templates;
}


/**
 * Search Filter
 */

add_action('pre_get_posts', __NAMESPACE__ . '\\sage_search_filter');

function sage_search_filter($query) {
  if ( !is_admin() && $query->is_main_query() ) {
    if ($query->is_search) {
      $query->set('post_type', array('post'));
    }
  }
}


/**
 * Login Image
 * @todo set proper image size
 */

add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\sage_login_logo' );

function login_logo() {

  if (has_site_icon()) {

    $logo_src = str_replace('cropped-', '', wp_get_attachment_image_url( get_option( 'site_icon' ), 'full'  ));

     ?><style type="text/css">
        #login h1 a {
          background-image: url(<?php echo $logo_src; ?>);
          background-size: contain;
          width: 100% !important;
        }
    </style>
<?php }}


/**
 * Gravity Forms Field Choice Markup Pre-render
 */

add_filter( 'gform_field_choice_markup_pre_render', __NAMESPACE__ . '\\sage_choice_render', 10, 4 );

function sage_choice_render($choice_markup, $choice, $field, $value){
    if ( $field->get_input_type() == 'radio' || 'checkbox' ) {
        $choice_markup = preg_replace("/(<li[^>]*>)\s*(<input[^>]*>)\s*(<label[^>]*>)\s*([\w\s]*<\/label>\s*<\/li>)/", '$1$3$2$4', $choice_markup);
        return $choice_markup;
    }
    return $choice_markup;
}


/**
 * Custom HTML
 */

add_action( 'get_header', __NAMESPACE__ . '\\sage_custom_html', 999 );

function sage_custom_html(){
    $options = Utils\sage_get_options();
    $editor_content = $options['custom_html'];
    echo $editor_content ? $editor_content : '';
}


/**
 * Custom CSS
 */

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\sage_custom_css', 999 );

function sage_custom_css(){
    $options = Utils\sage_get_options();
    $editor_content = $options['custom_css'];
    wp_add_inline_style( 'sage_css', $editor_content );
}


/**
 * Custom JS
 */

add_action( 'wp_footer', __NAMESPACE__ . '\\sage_custom_js', 999 );

function sage_custom_js(){
    $options = Utils\sage_get_options();
    $editor_content = $options['custom_js'];
    echo $editor_content ? '<script>'. $editor_content .'</script>' : '';
}


/**
 * Remove image attributes
 */

add_filter( 'post_thumbnail_html', __NAMESPACE__ . '\\sage_remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', __NAMESPACE__ . '\\sage_remove_thumbnail_dimensions', 10 );
add_filter( 'the_content', __NAMESPACE__ . '\\sage_remove_thumbnail_dimensions', 10 );
add_filter( 'get_avatar', __NAMESPACE__ . '\\sage_remove_thumbnail_dimensions', 10 );

function sage_remove_thumbnail_dimensions( $html ) {
    // Loop through all <img> tags
    if (preg_match_all('/<img[^>]+>/ims', $html, $matches)) {
        foreach ($matches as $match) {
            // Replace all occurences of width/height
            $clean = preg_replace('/(width|height)=["\'\d%\s]+/ims', "", $match);
            // Replace with result within html
            $html = str_replace($match, $clean, $html);
        }
    }
    return $html;
}


/**
 * Register the html5 figure-non-responsive code fix.
 */

add_filter( 'img_caption_shortcode', __NAMESPACE__ . '\\sage_img_caption_shortcode_filter', 10, 3 );

function sage_img_caption_shortcode_filter($dummy, $attr, $content) {
  $atts = shortcode_atts( array(
      'id'      => '',
      'align'   => 'alignnone',
      'width'   => '',
      'caption' => '',
      'class'   => '',
  ), $attr, 'caption' );

  $atts['width'] = (int) $atts['width'];
  if ( $atts['width'] < 1 || empty( $atts['caption'] ) )
      return $content;

  if ( ! empty( $atts['id'] ) )
      $atts['id'] = 'id="' . esc_attr( $atts['id'] ) . '" ';

  $class = trim( 'wp-caption figure ' . $atts['align'] . ' ' . $atts['class'] );

  if ( current_theme_supports( 'html5', 'caption' ) ) {
      return '<figure ' . $atts['id'] . 'style="max-width: ' . (int) $atts['width'] . 'px;" class="' . esc_attr( $class ) . '">'
      . do_shortcode( $content ) . '<figcaption class="wp-caption-text figure-caption">' . $atts['caption'] . '</figcaption></figure>';
  }

  // Return nothing to allow for default behaviour!!!
  return '';

}


/**
 * Allow upload SVG
 */

add_filter('upload_mimes', __NAMESPACE__ . '\\sage_mime_types');

function sage_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}


/**
 * Set prev/next posts links classes
 */

add_filter('previous_posts_link_attributes', __NAMESPACE__ . '\\prev_posts_link_attributes');
add_filter('next_posts_link_attributes', __NAMESPACE__ . '\\next_posts_link_attributes');

function prev_posts_link_attributes() {
    return 'class="prev-posts-link"';
}

function next_posts_link_attributes() {
    return 'class="next-posts-link"';
}



/*
 * Customize featured image output position
 */
add_filter( 'the_content', __NAMESPACE__ . '\\featured_image_before_content' );

function featured_image_before_content( $content ) {
  global $post;

   if ( is_singular('post') && has_post_thumbnail()) {
       $thumbnail = get_the_post_thumbnail();
       $data = Utils\sage_first_letter();

       $content = "
          <p class=\"post-thumbnail-wrapper\">{$thumbnail}</p>
          <div class=\"post-content\" {$data}>{$content}</div>
       ";

   }

   return $content;
}


/*
 * Move textarea to the top
 */

add_filter( 'comment_form_fields', __NAMESPACE__ . '\\sage_comment_form_fields', 99 );

function sage_comment_form_fields( $fields ) {

  // If the comment field is set.
  if ( isset( $fields['comment'] ) ) {

    // Grab the comment field.
    $comment_field = $fields['comment'];

    // Remove the comment field from its current position.
    unset( $fields['comment'] );

    // Put the comment field at the end.
    $fields['comment'] = $comment_field;
  }

  return $fields;
}


/*
 * Adding the Open Graph in the Language Attributes
 */
add_filter('language_attributes', __NAMESPACE__ . '\\add_opengraph_doctype');

function add_opengraph_doctype( $output ) {
  return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
}

/*
 * Lets add Open Graph Meta Info
 */

add_action( 'wp_head', __NAMESPACE__ . '\\insert_fb_in_head', 5 );

function insert_fb_in_head() {
  global $post;
  if ( !is_singular()) return;

  //echo '<meta property="fb:admins" content="YOUR USER ID"/>';
  echo '<meta property="og:title" content="' . get_the_title() . '"/>';
  echo '<meta property="og:type" content="article"/>';
  echo '<meta property="og:url" content="' . get_permalink() . '"/>';
  echo '<meta property="og:site_name" content="' . get_bloginfo('name') . '"/>';

  if(has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
    $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
    echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
  }
}



/*
 * Defered scripts
 */
add_filter('script_loader_tag', __NAMESPACE__ . '\\add_defer_attribute', 10, 2);

function add_defer_attribute($tag, $handle) {
  $scripts_to_defer = array('google_maps');

  foreach($scripts_to_defer as $defer_script) {
    if ($defer_script !== $handle) return $tag;
    return str_replace(' src', ' defer="defer" src', $tag);
  }

  return $tag;
}



/*
 * Async scripts
 */
add_filter('script_loader_tag', __NAMESPACE__ . '\\add_async_attribute', 10, 2);

function add_async_attribute($tag, $handle) {
  $scripts_to_async = array('google_maps');

  foreach($scripts_to_async as $async_script) {
    if ($async_script !== $handle) return $tag;
    return str_replace(' src', ' async="async" src', $tag);
  }

  return $tag;
}
