<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

function createAiPaymentTable() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_payment_table';

    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename) {
        $sql = "CREATE TABLE $tablename (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            post_id INT(11) NOT NULL,
            post_name VARCHAR(255) NOT NULL,
            post_type VARCHAR(255) NOT NULL,
            sub_type LONGTEXT NOT NULL,
            value DECIMAL(12,8) NOT NULL,
            timestamp DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        insert_default_payment_data();
    }
}

function insert_default_payment_data() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'ai_payment_table';

    // Check if there are existing records before inserting default data.
    $exists = $wpdb->get_var("SELECT COUNT(*) FROM $tablename");

    if ($exists == 0) {
        $wpdb->insert(
            $tablename,
            array(
                'user_id' => 1,
                'post_id' => 1,
                'post_name' => 'Voorbeeld 1',
                'post_type' => 'Post',
                'sub_type' => 'Text Generate',
                'value' => 0.10,
                'timestamp' => current_time('mysql', 1) // Get the current time in MySQL DATETIME format.
            ),
            array(
                '%d', //user_id
                '%d', //post_id
                '%s', // post_name
                '%s', // post_type
                '%s', // sub_type
                '%f', // value
                '%s', // timestamp
            )
        );
    }
}

add_action('init', 'createAiPaymentTable');
?>