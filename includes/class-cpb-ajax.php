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

    private function maybe_delay( $start, $minimum_time = CPB_MIN_EXECUTION_TIME ) {
        if ( $minimum_time <= 0 ) {
            return;
        }

        $elapsed = microtime( true ) - $start;

        if ( $elapsed < $minimum_time ) {
            usleep( ( $minimum_time - $elapsed ) * 1000000 );
        }
    }

    public function save_main_entity() {
        $start = microtime( true );
        check_ajax_referer( 'cpb_ajax_nonce' );
        global $wpdb;
        $table = $wpdb->prefix . 'cpb_main_entity';
        $id    = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
        $now   = current_time( 'mysql' );

        $data = array(
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
            'placeholder_13' => sanitize_text_field( $_POST['placeholder_13'] ?? '' ),
            'placeholder_14' => esc_url_raw( $_POST['placeholder_14'] ?? '' ),
            'placeholder_15' => isset( $_POST['placeholder_15'] ) ? floatval( $_POST['placeholder_15'] ) : 0,
            'placeholder_16' => isset( $_POST['placeholder_16'] ) ? floatval( $_POST['placeholder_16'] ) : 0,
            'placeholder_17' => isset( $_POST['placeholder_17'] ) ? floatval( $_POST['placeholder_17'] ) : 0,
            'placeholder_18' => isset( $_POST['placeholder_18'] ) ? intval( $_POST['placeholder_18'] ) : 0,
            'placeholder_19' => isset( $_POST['placeholder_19'] ) ? intval( $_POST['placeholder_19'] ) : 0,
            'placeholder_20' => sanitize_text_field( $_POST['placeholder_20'] ?? '' ),
            'updated_at'     => $now,
        );

        if ( $id > 0 ) {
            $wpdb->update( $table, $data, array( 'id' => $id ), null, array( '%d' ) );
            $message = __( 'Changes saved.', 'codex-plugin-boilerplate' );
        } else {
            $data['created_at'] = $now;
            $wpdb->insert( $table, $data );
            $message = __( 'Saved', 'codex-plugin-boilerplate' );
        }

        $this->maybe_delay( $start );
        wp_send_json_success( array( 'message' => $message ) );
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
        $page     = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;
        $per_page = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 20;

        if ( $per_page <= 0 ) {
            $per_page = 20;
        }

        $per_page = min( $per_page, 100 );

        $total       = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
        $total_pages = $per_page > 0 ? (int) ceil( $total / $per_page ) : 1;

        if ( $total_pages < 1 ) {
            $total_pages = 1;
        }

        if ( $page > $total_pages ) {
            $page = $total_pages;
        }

        $offset = ( $page - 1 ) * $per_page;

        if ( $offset < 0 ) {
            $offset = 0;
        }

        $entities = array();

        if ( $total > 0 ) {
            $entities = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $table ORDER BY placeholder_1 ASC, id ASC LIMIT %d OFFSET %d",
                    $per_page,
                    $offset
                )
            );
        }

        $this->maybe_delay( $start, 0 );
        wp_send_json_success(
            array(
                'entities'    => $entities,
                'page'        => $page,
                'per_page'    => $per_page,
                'total'       => $total,
                'total_pages' => $total_pages,
            )
        );
    }
}
