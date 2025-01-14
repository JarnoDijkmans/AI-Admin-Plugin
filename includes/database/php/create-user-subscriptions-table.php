<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

function createUserAiSubscriptionTable() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_user_subscriptions_table';

    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename) {
        $sql = "CREATE TABLE $tablename (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            subscription_id INT(11) NOT NULL,
            start_date DATETIME NOT NULL,
            end_date DATETIME,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        insert_default_user_subscription_data();
    }
}

function insert_default_user_subscription_data() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_user_subscriptions_table';

    $exists = $wpdb->get_var("SELECT COUNT(*) FROM $tablename");

    if ($exists == 0) {  
        // Insert the default data along with the features in JSON format
        $wpdb->insert(
            $tablename,
            array(
                'user_id' => 33,
                'subscription_id' => 1,
                'status' => 'active',
                'start_date' => '2024-11-11 14:30:00',
                'end_date' => current_time('mysql', 1),
            ),
            array(
                '%d', // user_id
                '%d', // subscription_id
                '%s', // start_date
                '%s', // end_date
            )
        );
    }
}

add_action('init', 'createUserAiSubscriptionTable');
?>