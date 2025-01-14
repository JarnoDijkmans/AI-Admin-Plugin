<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

function createAiSubscriptionTable() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_subscriptions_table';

    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename) {
        $sql = "CREATE TABLE $tablename (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            short_description LONGTEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            version VARCHAR(255) NOT NULL,
            options JSON NOT NULL,
            model JSON NOT NULL,
            author VARCHAR(255) NOT NULL,
            long_description LONGTEXT NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        insert_default_subscription_data();
    }
}

function insert_default_subscription_data() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_subscriptions_table';

    $exists = $wpdb->get_var("SELECT COUNT(*) FROM $tablename");

    if ($exists == 0) {  
        // Define the default features as an array
        $default_features = json_encode(array(
            'features' => array('image_generation', 'text_generation')
        ));
        
        $default_model = json_encode(array(
            'models' => array('GPT 4o', 'GPT4o mini')
        ));

        // Insert the default data along with the features in JSON format
        $wpdb->insert(
            $tablename,
            array(
                'name' => 'Yooker AI Blogposts',
                'short_description' => 'Genereer, inspireer en herschrijf blogposts met AI.',
                'price' => 10.00,
                'version' => '1.0.0',
                'options' => $default_features,
                'model' => $default_model,
                'author' => 'Yooker - Full Service Webbureau',
                'long_description' => 'Default',
                
            ),
            array(
                '%s', // name
                '%s', // short_description
                '%f', // price
                '%s', // version
                '%s', // options
                '%s', // model
                '%s', // author
                '%s', // long_description
            )
        );
    }
}

add_action('init', 'createAiSubscriptionTable');
?>