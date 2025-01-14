<?php
    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;

    $includes = array(
        'create-subscription-table',
        'create-user-role',
        'create-payment-table',
        'create-user-subscriptions-table'
    );

    foreach( $includes as $include) :
        $file = plugin_dir_path( __FILE__ ) . 'php/' .$include.'.php';
        include_once $file;
    endforeach;
?>