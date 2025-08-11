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
        // Placeholder save logic.
        $this->maybe_delay( $start );
        wp_send_json_success( array( 'message' => __( 'Saved', 'codex-plugin-boilerplate' ) ) );
    }

    public function delete_main_entity() {
        $start = microtime( true );
        // Placeholder delete logic.
        $this->maybe_delay( $start );
        wp_send_json_success( array( 'message' => __( 'Deleted', 'codex-plugin-boilerplate' ) ) );
    }

    public function read_main_entity() {
        $start = microtime( true );
        // Placeholder read logic.
        $this->maybe_delay( $start );
        wp_send_json_success( array( 'message' => __( 'Read', 'codex-plugin-boilerplate' ) ) );
    }
}
