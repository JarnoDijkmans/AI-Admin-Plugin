<?php

    defined( 'ABSPATH' ) || exit;

    $includes = array(
        'admin-menu',
    );

    foreach ( $includes as $include ) {
        $file = plugin_dir_path(__FILE__) . 'php/' .$include.'.php';
        include_once $file;
    }
?>