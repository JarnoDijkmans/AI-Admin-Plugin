<?php
    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;

    $includes = array(
        'get-subscription',
        'post-subscription',
        'get-all-subscriptions',
        'get-all-user-subscriptions',
        'get-all-ai-subscribers',
        'get-ai-subscriber-details',
        'post-ai-subscriber',
        'reusable-functions',
        'new-subscription',
        'end-subscription-admin',
        'delete-subscriber',
        'end-subscription',
        'get-subscription-details',
        'verify-subscription',
        'new-ai-subscriber',
        'register-ai-cost',
        'get-payment-history',
        'get-payment-history-monthly',
        'get-totalprice',
        'get-nonce',
        'activate-account',
        'get-gripp-company-id'
    );

    foreach( $includes as $include) :
        $file = plugin_dir_path( __FILE__ ) . 'php/' .$include.'.php';
        include_once $file;
    endforeach;
?>