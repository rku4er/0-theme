<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;
use Roots\Sage\Utils;

/**
 * Theme setup
 */
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');
function setup() {
  $options = Utils\sage_get_options();
  $gaID    = isset($options['google_analytics_id']) ? $options['google_analytics_id'] : '';

  add_theme_support('soil-clean-up');         // Enable clean up from Soil
  add_theme_support('soil-relative-urls');    // Enable relative URLs from Soil
  add_theme_support('soil-nice-search');      // Enable nice search from Soil
  add_theme_support('soil-google-analytics', $gaID); // Enable Google Analytics
  //add_theme_support('soil-jquery-cdn');     // Enable jQuery from the Google CDN
  //add_theme_support('soil-js-to-footer');   // Enable js to footer
  add_theme_support('soil-nav-walker');       // Enable clean nav walker

  // Make theme available for translation
  // Community translations can be found at https://github.com/roots/sage-translations
  load_theme_textdomain('sage', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus(array(
    'primary_navigation' => __('Primary Navigation', 'sage')
  ));

  // Add post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');
  //update_option( 'medium_crop', 1 ); //Turn on image crop at medium size

  // Add post formats
  // http://codex.wordpress.org/Post_Formats
  //add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio'));

  // Add HTML5 markup for captions
  // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
  add_theme_support('html5', array('caption', 'comment-form', 'comment-list', 'gallery', 'search-form'));

  // Tell the TinyMCE editor to use a custom stylesheet
  add_editor_style(Assets\asset_path('styles/editor-style.css'));

  // Allow shortcode execution in widgets
  add_filter('widget_text', 'do_shortcode');

  // Gets rid of the word "Archive:" in front of the Archive title
  add_filter( 'get_the_archive_title', function( $title ) {
    if ( is_post_type_archive() ) {
      $title = post_type_archive_title();
    }
    return $title;
  } );

  // add excerpt to pages
  add_post_type_support( 'page', 'excerpt' );

}


/**
 * Custom Post Type
 */

add_action( 'init', __NAMESPACE__ . '\\create_custom_post_types' );

function create_custom_post_types() {

  // Estate Sales CPT
  register_post_type( 'product',
    array(
      'labels' => array(
        'name' => __( 'Products' ),
        'singular_name' => __( 'Product' ),
        'add_new' => __( 'Add Product' ),
        'add_new_item' => __( 'Add New Product' ),
      ),
      'rewrite' => array('slug' => __( 'products' )),
      'public' => true,
      'exclude_from_search' => false,
      'has_archive' => true,
      'hierarchical' => true,
      'capability_type' => 'post',
      'can_export' => true,
      'menu_position' => 25,
      'menu_icon' => 'dashicons-cart',
      'supports' => array(
        'title',
        'editor',
        'thumbnail'
      )
    )
  );
}


/**
 * Theme assets
 */

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\sage_assets', 100);

function sage_assets() {

  wp_enqueue_style('sage_css', Assets\asset_path('styles/main.css'), false, null);

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

  wp_enqueue_script('modernizr', Assets\asset_path('scripts/modernizr.js'), array(), null, true);
  wp_enqueue_script('sage_js', Assets\asset_path('scripts/main.js'), array('jquery'), null, true);

  // ajax helper
  wp_localize_script( 'sage_js', 'ajax_helper', array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'post_id' => get_the_ID()
  ));

}


/**
 * Theme Options page
 */

add_action('init', __NAMESPACE__ . '\\sage_options_page', 100);

function sage_options_page() {

    if( function_exists('acf_add_options_page') ) {

        acf_add_options_page(array(
            'page_title'    => __('Theme Options', 'sage'),
            'menu_title'    => __('Theme Options', 'sage'),
            'menu_slug'     => 'theme_options',
            'capability'    => 'manage_options',
            'redirect'      => true
        ));

        acf_add_options_sub_page(array(
            'page_title'    => __('Header', 'sage'),
            'menu_title'    => __('Header', 'sage'),
            'parent_slug'   => 'theme_options',
        ));

        acf_add_options_sub_page(array(
            'page_title'    => __('Footer', 'sage'),
            'menu_title'    => __('Footer', 'sage'),
            'parent_slug'   => 'theme_options',
        ));

        acf_add_options_sub_page(array(
            'page_title'    => __('Editor', 'sage'),
            'menu_title'    => __('Editor', 'sage'),
            'parent_slug'   => 'theme_options',
        ));

    }

}
