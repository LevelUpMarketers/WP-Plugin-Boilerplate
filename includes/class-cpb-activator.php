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
            thing_1 date DEFAULT NULL,
            thing_2 varchar(191) DEFAULT '',
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
