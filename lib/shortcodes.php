<?php

namespace Roots\Sage\Shortcodes;

add_shortcode( 'row', __NAMESPACE__ .'\\sage_row' );
function sage_row( $args, $content = null ) {
  $defaults = array ();
  $args = wp_parse_args( $args, $defaults );
  array_walk($args, function(&$a, $b) { $a = "$b: $a"; });
  $style = !empty($args) ? sprintf('style="%s"', implode('; ', $args)) : '';
  return do_shortcode(sprintf('<div class="row" %s>%s</div>', $style, $content));
}

add_shortcode( 'col', __NAMESPACE__ .'\\sage_col' );
function sage_col( $args, $content = null ) {
  $defaults = array ();
  $args = wp_parse_args( $args, $defaults );
  array_walk($args, function(&$a, $b) { $a = "$b: $a"; });
  $style = !empty($args) ? sprintf('style="%s"', implode('; ', $args)) : '';
  return do_shortcode(sprintf('<div class="col-xs-auto" %s>%s</div>', $style, $content));
}
