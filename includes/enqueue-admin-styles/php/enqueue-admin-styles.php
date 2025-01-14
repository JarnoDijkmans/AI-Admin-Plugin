<?php
    if( !defined('ABSPATH'))
    {
        exit;
    }

    if ( ! function_exists('y_ai_plugin_admin_styles')) : 
        /**
         * Enqueue editor styles.
         * 
         * @since Yooker 1.0
         * 
         * @return void
         */

         function y_ai_plugin_admin_styles() {
            if(isset($_GET['page']) && $_GET['page'] == 'ai-gebruikers-admin-page'):
                wp_enqueue_style('wp-edit-blocks');  
                    wp_register_style(
                        'ai-admin',
                        yai_url. 'build/index.css',
                        array(),
                        filemtime(yai_path. 'build/index.css')
                    );

                    wp_enqueue_style('ai-admin');
                endif;
         }
        endif;

    add_action( 'admin_enqueue_scripts', 'y_ai_plugin_admin_styles');
?>