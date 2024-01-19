<?php
/*
Plugin Name: Light Member
Plugin URI: https://ektorcaba.com
Description: Ultra low resources member plugin, turn your site in a membership site. If it has helped you, please consider making a donation.
Version: 1.3
Author: ektorcaba
Author URI: https://ektorcaba.com
Text Domain: lightmember
Domain Path: /languages
License: GPL v2 or later
*/

defined('ABSPATH') or die( "Bye bye" );

function lm_translation_plugins_loaded() {
    load_plugin_textdomain( 'lightmember', false, basename( dirname( __FILE__ ) ) . '/languages/' );
  
  }
add_action( 'init', 'lm_translation_plugins_loaded', 0 );

define('LIGHTM_PATH',plugin_dir_path(__FILE__));
define('LM_DONATION_URL','https://donate.stripe.com/6oEg2gcKo9v59he5kl');


include(LIGHTM_PATH.'/includes/options.php');




 ?>
