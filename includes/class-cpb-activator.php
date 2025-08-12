<?php
/**
 * Fired during plugin activation
 *
 * @package Codex_Plugin_Boilerplate
 */

class CPB_Activator {

    public static function activate() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $main_table      = $wpdb->prefix . 'cpb_main_entity';
        $settings_table  = $wpdb->prefix . 'cpb_settings';
        $content_log     = $wpdb->prefix . 'cpb_content_log';

        $sql_main = "CREATE TABLE $main_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name text NOT NULL,
            placeholder_1 varchar(191) DEFAULT '',
            placeholder_2 date DEFAULT NULL,
            placeholder_3 tinyint(1) DEFAULT 0,
            placeholder_4 time DEFAULT NULL,
            placeholder_5 time DEFAULT NULL,
            placeholder_6 tinyint(1) DEFAULT 0,
            placeholder_7 varchar(191) DEFAULT '',
            placeholder_8 varchar(191) DEFAULT '',
            placeholder_9 varchar(191) DEFAULT '',
            placeholder_10 varchar(191) DEFAULT '',
            placeholder_11 varchar(20) DEFAULT '',
            placeholder_12 varchar(191) DEFAULT '',
            placeholder_13 varchar(191) DEFAULT '',
            placeholder_14 varchar(50) DEFAULT '',
            placeholder_15 decimal(10,2) DEFAULT 0,
            placeholder_16 decimal(10,2) DEFAULT 0,
            placeholder_17 decimal(10,2) DEFAULT 0,
            placeholder_18 tinyint(1) DEFAULT 0,
            placeholder_19 tinyint(1) DEFAULT 0,
            placeholder_20 bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql_settings = "CREATE TABLE $settings_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            option_name varchar(191) NOT NULL,
            option_value longtext NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY option_name (option_name)
        ) $charset_collate;";

        $sql_content_log = "CREATE TABLE $content_log (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            post_type varchar(20) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id)
        ) $charset_collate;";

        dbDelta( $sql_main );
        dbDelta( $sql_settings );
        dbDelta( $sql_content_log );
    }
}
