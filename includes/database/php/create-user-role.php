<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( !function_exists('ai_setup_subscriber_role') ) {
    function ai_setup_subscriber_role() {
        $current_version = get_option( 'ai_subscriber_role_version' );
        $new_version = 1;

        if ( $current_version < $new_version ) {
            // Add role if it doesn't exist
            if ( !get_role( 'ai_subscriber' ) ) {
                add_role(
                    'ai_subscriber',
                    'AI Subscriber',
                    array(
                        'read' => true,
                        'delete_posts' => true,
                        'delete_pages' => true,
                        'level_0' => true
                    )
                );
            }

            // Update capabilities
            $role = get_role( 'ai_subscriber' );
            if ( $role ) {
                $role->add_cap( 'delete_posts' );
                $role->add_cap( 'delete_pages' );
            }

            // Update the version option
            update_option( 'ai_subscriber_role_version', $new_version );
        }
    }
}

add_action( 'init', 'ai_setup_subscriber_role' );
?>
