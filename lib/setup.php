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
  $gaID    = (isset($options['google_analytics_id']) && !empty($options['google_analytics_id'])) ? $options['google_analytics_id'] : '';

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
  // gets rid of the word "archive:" in front of the archive title
  add_filter( 'get_the_archive_title', function( $title ) {
    if ( is_category() ) {
        $title = sprintf( __( 'Category: %s' ), single_cat_title( '', false ) );
    } elseif ( is_tag() ) {
        $title = sprintf( __( 'tag: %s' ), single_tag_title( '', false ) );
    } elseif ( is_author() ) {
        $title = sprintf( __( '<span class="profile-picture">%s</span> %s' ), get_avatar( get_the_author_meta('id'), '64'), '<span class="vcard">' . get_the_author() . '</span>' );
    } elseif ( is_year() ) {
        $title = sprintf( __( 'year: %s' ), get_the_date( _x( 'y', 'yearly archives date format' ) ) );
    } elseif ( is_month() ) {
        $title = sprintf( __( 'month: %s' ), get_the_date( _x( 'f y', 'monthly archives date format' ) ) );
    } elseif ( is_day() ) {
        $title = sprintf( __( 'day: %s' ), get_the_date( _x( 'f j, y', 'daily archives date format' ) ) );
    } elseif ( is_tax( 'post_format' ) ) {
        if ( is_tax( 'post_format', 'post-format-aside' ) ) {
            $title = _x( 'asides', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
            $title = _x( 'galleries', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
            $title = _x( 'images', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
            $title = _x( 'videos', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
            $title = _x( 'quotes', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
            $title = _x( 'links', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
            $title = _x( 'statuses', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
            $title = _x( 'audio', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
            $title = _x( 'chats', 'post format archive title' );
        }
    } elseif ( is_post_type_archive() ) {
        $title = sprintf( __( 'archives: %s' ), post_type_archive_title( '', false ) );
    } elseif ( is_tax() ) {
        $tax = get_taxonomy( get_queried_object()->taxonomy );
        /* translators: 1: taxonomy singular name, 2: current taxonomy term */
        $title = sprintf( __( '%1$s: %2$s' ), $tax->labels->singular_name, single_term_title( '', false ) );
    } else {
        $title = __( 'archives' );
    }

    return $title;
  });

  // add excerpt to pages
  add_post_type_support( 'page', 'excerpt' );

}



/**
 * Register sidebars
 */
add_action('widgets_init', __NAMESPACE__ . '\\widgets_init');

function widgets_init() {

  register_sidebar(array(
    'name'          => __('Primary', 'sage'),
    'id'            => 'sidebar-primary',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ));

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
  wp_enqueue_script('sage_js', Assets\asset_path('scripts/main.js'), array('jquery', 'modernizr'), null, true);

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

    }

}
