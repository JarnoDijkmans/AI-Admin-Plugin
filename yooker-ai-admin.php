<?php
    /**
     * @package yooker_ai_admin
     * @version 1.1.1
     */
    /*
    Plugin Name: Yooker AI Admin
    Description: Admin plugin voor Yooker AI
    Author: J. Dijkmans - Yooker
    Author URI: https://www.yooker.nl/
    Version: 1.1.1
    Text Domain: yooker-ai-admin
    Plugin URI: https://ai.yookerdesign.nl/updates/?plugin=yooker-ai-admin
    Update URI: https://ai.yookerdesign.nl/updates/?plugin=yooker-ai-admin
    Requires at least: 5.4
    Requires PHP: 7.4
    License: GPLv2
    License URI: https://www.yooker.nl/
    */

    defined( 'ABSPATH' ) || exit;

    function load_yai_plugin_data() {
        if ( !function_exists('get_plugin_data')):
            require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );
        endif;
        define( 'yai_plugindata', get_plugin_data( __FILE__ ) );
    }
    add_action( 'init', 'load_yai_plugin_data', 10, 2);
    define( 'yai_basename', plugin_basename( __FILE__ ) );
    define( 'yai_slug', dirname( plugin_basename( __FILE__ )) );
    define( 'yai_path', plugin_dir_path(__FILE__));
    define( 'yai_url', plugin_dir_url(__FILE__));

    require_once yai_path . 'includes/utils/index.php';

    $includes = array(
        'plugin-transient-update',
        'information-tab',
        'admin-menu',
        'admin-pages',
        'enqueue-admin-styles',
        'enqueue-admin-scripts',
        'register-meta-fields',
        'register-plugin-settings',
        'api',
        'database',
    );

    foreach( $includes as $include ) :
        $file = plugin_dir_path(__FILE__) . 'includes/' . $include . '/index.php';
        if (file_exists($file)) {
            include_once $file;
        } else {
            error_log("File not found: $file");
        }
    endforeach;
?>