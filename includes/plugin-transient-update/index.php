<?php
    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;

    $includes = array(
        'plugin-transient-update',
    );

    foreach( $includes as $include) :
        $file = plugin_dir_path( __FILE__ ) . 'php/' .$include.'.php';
        include_once $file;
    endforeach;
?>