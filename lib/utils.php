<?php

namespace Roots\Sage\Utils;
use Roots\Sage\Titles;


/**
 * Determine which pages should NOT display the sidebar
 */

function display_sidebar() {

  static $display;

  isset($display) || $display = !in_array(true, array(
    // The sidebar will NOT be displayed if ANY of the following return true.
    // @link https://codex.wordpress.org/Conditional_Tags
    is_404(),
    is_page() && !is_front_page()
  ));

  return apply_filters('sage/display_sidebar', $display);

}


/**
 * Custom Excerpt
 */
function sage_get_excerpt($limit = 35) {
  global $post;

  $content = get_post_field('post_content', $post->ID);
  $content = wp_strip_all_tags( $content );
  $content = explode(' ', $content, $limit);


  if (count($content) >= $limit) {
    array_pop($content);
    $content = '
      <p>' . implode(" ",$content) . '&hellip;</p>
      <a class="more" href="' . get_the_permalink($post->ID) . '">' . __('Read More', 'sage') . '</a>
    ';
  } else {
    $content = '<p>' . implode(" ",$content) . '</p>';
  }

  //$content = preg_replace('/\[.+\]/','', $content);
  //$content = apply_filters('the_content', $content);
  //$content = str_replace(']]>', ']]&gt;', $content);

  return $content;
}


/**
 * Get Theme Options Fields
 */

function sage_get_options() {

    if( function_exists('get_fields') ) {

        $options = get_fields('option');

        return $options;
    }
}

/**
 * Return a custom field stored by the Advanced Custom Fields plugin
 */

function sage_get_field( $key, $id=false, $format=true ) {

  global $post;

  $key = trim( filter_var( $key, FILTER_SANITIZE_STRING ) );
  $result = '';

  if ( function_exists( 'get_field' ) ) {
    if ( isset( $post->ID ) && !$id )
      $result = get_field( $key, $post->ID, $format);
    else
      $result = get_field( $key, $id, $format );
  }

  return $result;
}


/**
 * Shortcut for 'echo _get_field()'
 */

function sage_the_field( $key, $id=false ) {
  echo sage_get_field( $key, $id );
}


/**
 * Get a sub field of a Repeater field
 */

function sage_get_sub_field( $key, $format=true ) {
  if ( function_exists( 'get_sub_field' ) &&  get_sub_field( $key ) ) {
    return get_sub_field( $key, $format );
  }
}


/**
 * Shortcut for 'echo _get_sub_field()'
 */

function sage_the_sub_field( $key, $format=true ) {
  echo sage_get_sub_field( $key, $format );
}


/**
 * Check if a given field has a sub field
 */

function sage_has_sub_field( $key, $id=false ) {
  if ( function_exists('has_sub_field') ) {
    return has_sub_field( $key, $id );
  } else {
    return false;
  }
}


/**
 * Get loop html
 */

function sage_get_loop_html( $args = array(), $template = 'templates/content' ) {

   global $wp_query;

   $my_query = new \WP_Query($args);

   if( $my_query->have_posts() ) {

     ob_start();

     while ($my_query->have_posts()) : $my_query->the_post();
       get_template_part($template);
     endwhile;

     wp_reset_postdata();

     return ob_get_clean();
   }
}


/**
 * Flexible Layout content
 */

function sage_get_row_content ( $row ) {

  global $post;

  $layout        = (isset($row['acf_fc_layout']) && !empty($row['acf_fc_layout'])) ? $row['acf_fc_layout'] : '';
  $section_id    = (isset($row['section_id']) && !empty($row['section_id']) ? $row['section_id'] : uniqid($layout .'_'));
  $section_title = (isset($row['section_title']) && !empty($row['section_title'])) ? $row['section_title'] : '';
  $field_name    = (isset($row['field_name']) && !empty($row['field_name'])) ? $row['field_name'] : 'sections';
  $post_type     = (isset($row['display']) && !empty($row['display'])) ? $row['display'] : 'post';

  $sections = array();

  if ($layout === 'editor') {
    $sections[] = (isset($row['content']) && !empty($row['content'])) ? $row['content'] : null;
  }

  if (empty($sections)) {
    $sections[] = apply_filters('the_content', get_post_field('post_content', $post->ID));;
  }

  $section_title_html   = $section_title ? sprintf('<div class="section-title">%s</div>', sage_get_heading('h3', $section_title)) : '';
  $section_content_html = $sections ? implode('', $sections) : '';

  return "
    <div id=\"{$section_id}\" class=\"section section-{$layout}\" data-layout=\"{$layout}\" data-field-name=\"{$field_name}\" data-post-type=\"{$post_type}\">
      <div class=\"section-container\">
        {$section_title_html}
        {$section_content_html}
      </div>
    </div>
  ";

}


/**
 * Creates flexible content instanse
 */

function sage_get_sections($field_name = 'sections') {

  if (!$field_name) return;

  $field_data = sage_get_field( $field_name );

  // check if the flexible content field exists
  if( !$field_data ) return;

  // loop through the rows of data
  $content = array();
  $i = 0;

  foreach ( $field_data as $field ) {

    $field['field_name'] = $field_name;

    // collect layout content
    $content[] = sage_get_row_content($field);

  }

  return implode('', $content);

}


/**
 *  Header Logo
 */

function sage_get_logo() {

  if ( has_site_icon() ) {
    $brand = sprintf('<img src="%s" alt="%s">',
      str_replace('cropped-', '', wp_get_attachment_image_url( get_option( 'site_icon' ), 'full'  )),
      get_bloginfo('name')
    );
  } else {
    $brand = get_bloginfo('name');
  }

  return sprintf('<a class="navbar-brand" href="%s" title="%s">%s</a>',
    esc_url(home_url('/')),
    get_bloginfo('name'),
    sprintf('%s<span>%s<span>', $brand, get_bloginfo('description'))
  );

}


/**
 *  Header navbar class
 *  @todo adjust to new position modes(normal/sticky)
 */

function sage_header_navbar_class() {

    $options = sage_get_options();

    $navbar_position = $options['navbar_position'];
    $navbar_class = $navbar_position ? 'navbar-' . $navbar_position : 'navbar-static';

    echo $navbar_class;

}


/**
 * Footer info
 */

function sage_copyright() {
    $options = sage_get_options();

    if($options['copyright']) printf('<div class="copyright">%s</div>',
        $options['copyright']
    );
}


/**
 *  Section header
 */

function sage_get_heading($tag = 'h1', $title = null) {

  $title = $title ? $title : Titles\title();

  return sprintf('<%1$s>%2$s</%1$s>', $tag, $title);

}


/*
 * Get categories
 */
function sage_post_categories( $sep = '', $id = null ) {

  global $post;

  if ( isset( $post->ID ) && !$id ) $id = $post->ID;

  $terms = get_the_category($id);

  $terms_arr = array();

  if ( count($terms) > 0 ){
    foreach ( $terms as $term ) {
      $terms_arr[] = sprintf('<a href="%s">%s</a>', get_term_link($term->term_id, 'post_tag'), $term->name);
    }
  }

  $terms = implode($sep, $terms_arr);

  echo $terms;

}



/*
 * Author archive link
 */
function sage_author_archive_link( $id = null ) {
  global $post;

  if ( isset( $post->ID ) && !$id ) $id = $post->ID;
  echo '<a href="' . get_author_posts_url( get_the_author_meta('ID'), get_the_author_meta( 'user_nicename'  )  ) . '">' . get_the_author() . '</a>';
}
