<?php
/**
 * Handle Ajax operations with configurable minimum execution time.
 *
 * @package Codex_Plugin_Boilerplate
 */

class CPB_Ajax {

    public function register() {
        add_action( 'wp_ajax_cpb_save_main_entity', array( $this, 'save_main_entity' ) );
        add_action( 'wp_ajax_cpb_delete_main_entity', array( $this, 'delete_main_entity' ) );
        add_action( 'wp_ajax_cpb_read_main_entity', array( $this, 'read_main_entity' ) );
    }

    private function maybe_delay( $start ) {
        $elapsed = microtime( true ) - $start;
        if ( $elapsed < CPB_MIN_EXECUTION_TIME ) {
            usleep( ( CPB_MIN_EXECUTION_TIME - $elapsed ) * 1000000 );
        }
    }

    public function save_main_entity() {
        $start = microtime( true );
        check_ajax_referer( 'cpb_ajax_nonce' );
        global $wpdb;
        $table = $wpdb->prefix . 'cpb_main_entity';
        $data  = array(
            'name'           => sanitize_text_field( $_POST['name'] ?? '' ),
            'placeholder_1'  => sanitize_text_field( $_POST['placeholder_1'] ?? '' ),
            'placeholder_2'  => sanitize_text_field( $_POST['placeholder_2'] ?? '' ),
            'placeholder_3'  => isset( $_POST['placeholder_3'] ) ? intval( $_POST['placeholder_3'] ) : 0,
            'placeholder_4'  => sanitize_text_field( $_POST['placeholder_4'] ?? '' ),
            'placeholder_5'  => sanitize_text_field( $_POST['placeholder_5'] ?? '' ),
            'placeholder_6'  => isset( $_POST['placeholder_6'] ) ? intval( $_POST['placeholder_6'] ) : 0,
            'placeholder_7'  => sanitize_text_field( $_POST['placeholder_7'] ?? '' ),
            'placeholder_8'  => sanitize_text_field( $_POST['placeholder_8'] ?? '' ),
            'placeholder_9'  => sanitize_text_field( $_POST['placeholder_9'] ?? '' ),
            'placeholder_10' => sanitize_text_field( $_POST['placeholder_10'] ?? '' ),
            'placeholder_11' => sanitize_text_field( $_POST['placeholder_11'] ?? '' ),
            'placeholder_12' => sanitize_text_field( $_POST['placeholder_12'] ?? '' ),
            'placeholder_13' => esc_url_raw( $_POST['placeholder_13'] ?? '' ),
            'placeholder_14' => sanitize_text_field( $_POST['placeholder_14'] ?? '' ),
            'placeholder_15' => isset( $_POST['placeholder_15'] ) ? floatval( $_POST['placeholder_15'] ) : 0,
            'placeholder_16' => isset( $_POST['placeholder_16'] ) ? floatval( $_POST['placeholder_16'] ) : 0,
            'placeholder_17' => isset( $_POST['placeholder_17'] ) ? floatval( $_POST['placeholder_17'] ) : 0,
            'placeholder_18' => isset( $_POST['placeholder_18'] ) ? intval( $_POST['placeholder_18'] ) : 0,
            'placeholder_19' => isset( $_POST['placeholder_19'] ) ? intval( $_POST['placeholder_19'] ) : 0,
            'placeholder_20' => sanitize_text_field( $_POST['placeholder_20'] ?? '' ),
            'created_at'     => current_time( 'mysql' ),
            'updated_at'     => current_time( 'mysql' ),
        );
        $wpdb->insert( $table, $data );
        $this->maybe_delay( $start );
        wp_send_json_success( array( 'message' => __( 'Saved', 'codex-plugin-boilerplate' ) ) );
    }

    public function delete_main_entity() {
        $start = microtime( true );
        check_ajax_referer( 'cpb_ajax_nonce' );
        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        global $wpdb;
        $table = $wpdb->prefix . 'cpb_main_entity';
        $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
        $this->maybe_delay( $start );
        wp_send_json_success( array( 'message' => __( 'Deleted', 'codex-plugin-boilerplate' ) ) );
    }

    public function read_main_entity() {
        $start = microtime( true );
        check_ajax_referer( 'cpb_ajax_nonce' );
        global $wpdb;
        $table    = $wpdb->prefix . 'cpb_main_entity';
        $entities = $wpdb->get_results( "SELECT * FROM $table ORDER BY id DESC" );
        $this->maybe_delay( $start );
        wp_send_json_success( array( 'entities' => $entities ) );
    }
}
