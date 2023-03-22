<?php
/**
 * Coolgrid for WPBakery
 *
 * @encoding        UTF-8
 * @version         1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: Coolgrid for WPBakery
 * Plugin URI: https://enderkus.com.tr/wp-bakery-addons/coolgrid-wpbakery-addon/
 * Description: Modern post grid for wpbakery.
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.1
 * Author: Ender KUS
 * Author URI: https://github.com/enderkus/
 * Text Domain: coolgrid-wpbakery
 **/

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

 class CoolGrid {
    function __construct() {
        add_action( 'init', array( $this, 'create_shortcode' ), 999 );            
        add_shortcode( 'vc_coolgrid', array( $this, 'render_shortcode' ) );
        wp_enqueue_style( 'vc-coolgrid-css', plugins_url('', dirname(__FILE__) ).'/coolgrid-wpbakery/assets/css/coolgrid.css' );
    }        

    public function create_shortcode() {
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }        

        vc_map( array(
            'name'          => __('Cool Grid', 'coolgrid'),
            'base'          => 'vc_coolgrid',
            'description'  	=> __( '', 'coolgrid' ),
            'category'      => __( 'Coolgrid Modules', 'coolgrdi'),                
            'params' => array(

                array(
                    'type'          => 'textfield',
                    'heading'       => __( 'Grid title', 'coolgrid' ),
                    'param_name'    => 'coolgrid_title',
                    'description'   => __( 'Post grid title.', 'coolgrid' ),
                ),

                array(
                  'type'          => 'textfield',
                  'heading'       => __( 'Number of posts', 'coolgrid' ),
                  'param_name'    => 'coolgrid_number_of_posts',
                  'description'   => __( 'Enter number of posts to display(If you leave it blank, the default value : 7.).', 'coolgrid' ),
              ),

                array(
                    'type'          => 'posttypes',
                    'heading'       => __( 'Post type', 'coolgrid' ),
                    'param_name'    => 'coolgrid_post_type',
                    'value'             => __( '', 'coolgrid' ),
                    'description'   => __( 'Enter post type(If you leave it blank, the default post type will be : post.).', 'coolgrid' ),
                ),            
            ),
        ));             

    }

    public function render_shortcode( $atts, $content, $tag ) {
        $atts = (shortcode_atts(array(
            'coolgrid_title'        => '',
            'coolgrid_number_of_posts' => '',
            'coolgrid_post_type'    => ''
        ), $atts));

        //Title
        $coolgrid_title = esc_html($atts['coolgrid_title']);
        //Number of posts
        $coolgrid_number_of_posts = esc_html($atts['coolgrid_number_of_posts']);
        // Post type
        $coolgrid_post_type = esc_html($atts['coolgrid_post_type']);

        if (empty($coolgrid_number_of_posts)) {
            $coolgrid_number_of_posts = '7';
        }

        if(empty($coolgrid_post_type)) {
            $coolgrid_post_type = 'post';
        }

        $q_args = array(
                'posts_per_page' =>  $coolgrid_number_of_posts,
                'post_type' => $coolgrid_post_type,
                'post_status' => 'publish',
        );

        

        $output = '<header class="coolgrid-header">
        <h1>'.$coolgrid_title.'</h1>
      </header>
      <div class="coolgrid-band">';
      $i = 1;
      $q = new WP_Query($q_args);
      if( $q->have_posts() ) :
        while( $q->have_posts() ) : $q->the_post();
          global $post;

          $post_title = get_the_title();
          $post_excerpt = get_the_excerpt();
          $post_author = get_the_author();
          $permalink = get_the_permalink();

          $output .= '<div class="coolgrid-item-'.$i.'">
          <a href="'.$permalink.'" class="coolgrid-card">';
          if(has_post_thumbnail( $post->ID )) {
            $output .= '<div class="thumb" style="background-image: url('.get_the_post_thumbnail_url($post->ID).');"></div>';
          }
            $output .='<article>
              <h1>'.get_the_title().'</h1>';
              if (!empty($post_excerpt)) {
                $output .= '<p>'.$post_excerpt.'</p>';
              }
              
          $output .= '<span>'.get_the_author().'</span>
            </article>
          </a>
        </div>';
        $i++;

        endwhile;
      endif;

      $output .= '</div>';

      return $output;                  

    }
 }

 new CoolGrid();
