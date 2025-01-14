<?php
    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;

    $includes = array(
        'user-utils',
        'gripp-utils',
        'rest-response-utils',
        'send-mail'
    );

    foreach( $includes as $include) :
        $file = plugin_dir_path( __FILE__ ) . 'php/' .$include.'.php';
        include_once $file;
    endforeach;
?>