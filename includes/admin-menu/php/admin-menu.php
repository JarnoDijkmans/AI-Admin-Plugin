<?php
    if( !defined('ABSPATH'))
    {
        exit;
    }

    function yai_plugin_admin_menu() {
        global $admin_menu_settings;

        $admin_menu_settings = 
        add_menu_page(
            __('page', 'yooker-ai-admin'),
            __('Yooker AI Admin', 'yooker-ai-admin'),
            'manage_options',
            'ai-gebruikers-admin-page',
            'ai_gebruikers_admin_page',
            yai_url . 'assets/images/ai.svg',
            4
        );
    }
    add_action('admin_menu', 'yai_plugin_admin_menu', 1);
?>