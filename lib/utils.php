<?php

namespace Roots\Sage\Utils;

use Roots\Sage\Titles;


/**
 * Custom Excerpt
 * @param integer $limit Limit of words
 * @uses get_the_content()
 * @uses get_permalink()
 * @uses apply_filters()
 * @return void
 */

function sage_excerpt($limit = 35) {
  $content = explode(' ', get_the_content(), $limit);

  if (count($content) >= $limit) {
    array_pop($content);
    $content = sprintf('%s&hellip; <a class="more" href="%s">%s</a>',
      implode(" ",$content),
      get_permalink(),
      __('Read More', 'sage')
    );
  } else {
    $content = implode(" ",$content);
  }

  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content);
  $content = str_replace(']]>', ']]&gt;', $content);

  echo $content;
}


/**
 * Get the excerpt
 * @param mixed $post_id the post id (int|str, defaults to $post->id)
 * @return mixed
 * @uses get_the_excerpt()
 */

function sage_get_the_excerpt($post_id) {
    global $post;

    $save_post = $post;

    if ( isset( $post->ID ) && !$post_id )
        $post = get_post($post->ID);
    else
        $post = get_post($post_id);

    $output = get_the_excerpt();
    $post = $save_post;

    return $output;
}


/**
 * Get Theme Options Fields
 * @uses get_fields()
 * @return array
 */

function sage_get_options() {

    if( function_exists('get_fields') ) {

        $options = get_fields('option');

        return $options;
    }
}

/**
 * Return a custom field stored by the Advanced Custom Fields plugin
 *
 * @global $post
 * @param str $key The key to look for
 * @param mixed $id The post ID (int|str, defaults to $post->ID)
 * @uses get_field()
 * @return mixed
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
 * @param str $key The meta key
 * @param mixed $id The post ID (int|str, defaults to $post->ID)
 * @uses sage_get_field()
 * @return void
 */

function sage_the_field( $key, $id=false ) {

  echo sage_get_field( $key, $id );

}


/**
 * Get a sub field of a Repeater field
 * @param str $key The meta key
 * @return mixed
 * @uses get_sub_field()
 */

function sage_get_sub_field( $key, $format=true ) {

    if ( function_exists( 'get_sub_field' ) &&  get_sub_field( $key ) ) {

        return get_sub_field( $key, $format );

    }


}


/**
 * Shortcut for 'echo _get_sub_field()'
 * @param str $key The meta key Value to return if there's no value for the custom field $key
 * @param str $format Whether to enable output content formating
 * @uses sage_get_sub_field()
 * @return void
 */

function sage_the_sub_field( $key, $format=true ) {

  echo sage_get_sub_field( $key, $format );

}


/**
 * Check if a given field has a sub field
 * @param str $key The meta key
 * @param mixed $id The post ID
 * @return bool
 * @uses has_sub_field()
 */

function sage_has_sub_field( $key, $id=false ) {

  if ( function_exists('has_sub_field') )

    return has_sub_field( $key, $id );

  else

    return false;

}


/**
 *  Header Logo
 */

function sage_header_logo() {

    $options = sage_get_options();
    $logo_image = $options['logo'];

    if($logo_image) {

        $logo_html = sprintf('<img src="%s" alt="%s">',
            $logo_image['url'],
            get_bloginfo('name')
        );


        printf('<%1$s class="navbar-brand-wrapper"><a class="%2$s" href="%3$s">%4$s</a></%1$s>',
            (is_front_page()) ? 'h1' : 'strong',
            'navbar-brand withoutripple',
            esc_url(home_url('/')),
            $logo_html
        );

    }
}


/**
 *  Header navbar class
 */

function sage_header_navbar_class() {

    $options = sage_get_options();

    $navbar_position = $options['navbar_position'];
    $navbar_class = $navbar_position ? 'navbar-'. $navbar_position : 'navbar-static-top';

    echo $navbar_class;

}


/**
 * Footer info
 */

function sage_info() {
    $options = sage_get_options();

    if($options['content-info']) printf('<div class="info">%s</div>',
        $options['content-info']
    );
}
