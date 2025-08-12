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
            'name'          => sanitize_text_field( $_POST['name'] ?? '' ),
            'placeholder_1' => sanitize_text_field( $_POST['placeholder_1'] ?? '' ),
            'thing_1'       => sanitize_text_field( $_POST['thing_1'] ?? '' ),
            'thing_2'       => sanitize_text_field( $_POST['thing_2'] ?? '' ),
            'created_at'    => current_time( 'mysql' ),
            'updated_at'    => current_time( 'mysql' ),
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
