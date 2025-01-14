<?php
defined( 'ABSPATH' ) || exit;

function register_yookerai_admin_settings() {
    register_setting(
        'general',
        'grippAPIKEY',
        [
            'type' => 'string',
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
        ]
        );
}

add_action( 'admin_init',    'register_yookerai_admin_settings' );
add_action( 'rest_api_init', 'register_yookerai_admin_settings' );
?>