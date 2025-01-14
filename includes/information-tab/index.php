<?php
    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;

    $includes = array(
        'information-tab',
    );

    foreach( $includes as $include) :
        $file = plugin_dir_path( __FILE__ ) . 'php/' .$include.'.php';
        include_once $file;
    endforeach;
?>