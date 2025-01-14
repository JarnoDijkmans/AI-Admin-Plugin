<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

function register_user_meta_fields() {
    register_meta(
        'user',
        'surnameprefix', 
        array(
            'type' => 'string',
            'show_in_rest' => true,
            'single' => true
        )
    );
    
    register_meta(
        'user',
        'company',
        array(
            'type' => 'string',
            'show_in_rest' => true,
            'single' => true
        )
    );
    
    register_meta(
        'user',
        'phonenumber',
        array(
            'type' => 'string',
            'show_in_rest' => true,
            'single' => true
        )
    );
    
    register_meta(
        'user',
        'address',
        array(
            'type' => 'string',
            'show_in_rest' => true,
            'single' => true
        )
    );
    
    register_meta(
        'user',
        'zipcode',
        array(
            'type' => 'string',
            'show_in_rest' => true,
            'single' => true
        )
    );
    
    register_meta(
        'user',
        'town',
        array(
            'type' => 'string',
            'show_in_rest' => true,
            'single' => true
        )
    );

    register_meta(
        'user',
        'country',
        array(
            'type' => 'string',
            'show_in_rest' => true,
            'single' => true
        )
    );

    register_meta(
        'user',
        'clientnumber_gripp',
        [
            'type' => 'integer',
            'show_in_rest' => true,
            'single' => true
        ]
    );

    register_meta(
        'user',
        'subscriptions',
        array(
            'type' => 'string', // Store as a JSON-encoded string
            'show_in_rest' => array(
                'schema' => array(
                    'type' => 'array',
                    'items' => array(
                        'type' => 'string',
                    ),
                ),
            ),
            'single' => true
        )
    );

    register_meta(
        'user',
        'activated',
        [
            'type' => 'integer',
            'show_in_rest' => true,
            'single' => true
        ]
    );
}
add_action('init', 'register_user_meta_fields');
?>