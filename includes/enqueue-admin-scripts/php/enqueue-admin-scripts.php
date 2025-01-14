<?php
    if( ! function_exists('y_ai_plugin_admin_scripts')) :

        /**
         * Enqueue editor styles.
         * 
         * @since Yooker 1.0
         * 
         * @return void
         */

         function y_ai_plugin_admin_scripts() {

            global $pagenow;
            global $post;

            if(isset($_GET['page']) && $_GET['page'] == 'ai-gebruikers-admin-page'):
                wp_register_script(
					'yooker-blocks-js', // Handle.
					yai_url . 'build/index.js',
					array( 
						'wp-api-fetch', 
        				'wp-block-editor', 
						'wp-block-library', 
						'wp-api', 
						'wp-blocks', 
						'wp-editor', 
						'wp-components', 
						'wp-i18n', 
						'wp-element', 
						'wp-plugins', 
						'wp-edit-post', 
						'wp-edit-site', 
						'wp-api-fetch', 
					),
					filemtime(yai_path . 'build/index.js'), 
					false 
	 			);

				wp_localize_script(
                    'yooker-blocks-js',
                    'wpApiSettings',
                    array(
                        'root' => esc_url_raw( rest_url() ),
                        'nonce' => wp_create_nonce( 'wp_rest' )
                    )
                );
	
				wp_enqueue_script( 'yooker-blocks-js' );
			endif;
		}  

    endif;
    add_action( 'admin_enqueue_scripts', 'y_ai_plugin_admin_scripts');
?>