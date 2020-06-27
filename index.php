<?php 
/*
Plugin Name: ALT Lab Google Fed
Plugin URI:  https://github.com/RVA-ALT-Lab/alt-lab-google-fed
Description: Sync a public Google Document to a WordPress Post via the URL
Version:     1.0
Author:      ALT Lab
Author URI:  http://altlab.vcu.edu
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



// Define path and URL to the ACF plugin.
define( 'GOOGLE_FED_ACF_PATH', plugin_dir_path(__FILE__) . 'includes/acf/' );
define( 'GOOGLE_FED_ACF_URL', plugin_dir_url(__FILE__) . 'includes/acf/' );

// Include the ACF plugin.
include_once( plugin_dir_path( __FILE__ ) . 'includes/acf/acf.php' );


// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'google_fed_acf_settings_url');
function google_fed_acf_settings_url( $url ) {
    return GOOGLE_FED_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', 'google_fed_acf_settings_show_admin');
function google_fed_acf_settings_show_admin( $show_admin ) {
    return true;
}



//LOGGER -- like frogger but more useful

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

  //print("<pre>".print_r($a,true)."</pre>");

//MAKE THE ACF FIELD
function google_fed_acf_add_local_field_groups() {
  
  acf_add_local_field_group(array(
    'key' => 'g_url',
    'title' => 'Google URL',
    'fields' => array (
      array (
        'key' => 'g_doc_url',
        'label' => 'The Published Google Document URL',
        'name' => 'sub_title',
        'type' => 'url',
      )
    ),
    'location' => array (
      array (
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'post',
        ),
      ),
      array (
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'page',
        ),
      ),
    ),
  ));
  
}

add_action('acf/init', 'google_fed_acf_add_local_field_groups');


//add the google doc content 


function google_fed_display_doc($content){
  global $post;
  $post_id = $post->ID;
  if (get_field('g_doc_url', $post_id)){
    $g_url = get_field('g_doc_url', $post_id);
    $page = file_get_contents($g_url);

    //this works
    $first_step = explode( '<div id="contents">' , $page );
    $second_step = explode("</div>" , $first_step[1] );
    return $content . $second_step[0];
  } else {
    return $content;
  }
}

add_filter( 'the_content', 'google_fed_display_doc');