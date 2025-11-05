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
            'name'           => $this->sanitize_text_value( 'name' ),
            'placeholder_1'  => $this->sanitize_text_value( 'placeholder_1' ),
            'placeholder_2'  => $this->sanitize_text_value( 'placeholder_2' ),
            'placeholder_3'  => $this->sanitize_date_value( 'placeholder_3' ),
            'placeholder_4'  => $this->sanitize_select_value( 'placeholder_4', array( '0', '1' ) ),
            'placeholder_5'  => $this->sanitize_time_value( 'placeholder_5' ),
            'placeholder_6'  => $this->sanitize_time_value( 'placeholder_6' ),
            'placeholder_7'  => $this->sanitize_select_value( 'placeholder_7', array( '0', '1' ) ),
            'placeholder_8'  => $this->sanitize_text_value( 'placeholder_8' ),
            'placeholder_9'  => $this->sanitize_text_value( 'placeholder_9' ),
            'placeholder_10' => $this->sanitize_text_value( 'placeholder_10' ),
            'placeholder_11' => $this->sanitize_text_value( 'placeholder_11' ),
            'placeholder_12' => $this->sanitize_text_value( 'placeholder_12' ),
            'placeholder_13' => $this->sanitize_text_value( 'placeholder_13' ),
            'placeholder_14' => $this->sanitize_url_value( 'placeholder_14' ),
            'placeholder_15' => $this->sanitize_select_value( 'placeholder_15', array( 'option1', 'option2', 'option3' ) ),
            'placeholder_16' => $this->sanitize_decimal_value( 'placeholder_16' ),
            'placeholder_17' => $this->sanitize_decimal_value( 'placeholder_17' ),
            'placeholder_18' => $this->sanitize_decimal_value( 'placeholder_18' ),
            'placeholder_19' => $this->sanitize_select_value( 'placeholder_19', array( '0', '1' ) ),
            'placeholder_20' => $this->sanitize_select_value( 'placeholder_20', array( '0', '1' ) ),
            'updated_at'     => $now,
        );

        $formats = array_fill( 0, count( $data ), '%s' );

        if ( $id > 0 ) {
            $result  = $wpdb->update( $table, $data, array( 'id' => $id ), $formats, array( '%d' ) );
            $message = __( 'Changes saved.', 'codex-plugin-boilerplate' );

            if ( false === $result && $wpdb->last_error ) {
                $this->maybe_delay( $start );
                wp_send_json_error(
                    array(
                        'message' => __( 'Unable to save changes. Please try again.', 'codex-plugin-boilerplate' ),
                    )
                );
            }
        } else {
            $data['created_at'] = $now;
            $formats[]          = '%s';
            $result             = $wpdb->insert( $table, $data, $formats );
            $message            = __( 'Saved', 'codex-plugin-boilerplate' );

            if ( false === $result ) {
                $this->maybe_delay( $start );
                wp_send_json_error(
                    array(
                        'message' => __( 'Unable to save the record. Please try again.', 'codex-plugin-boilerplate' ),
                    )
                );
            }
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

    private function get_post_value( $key ) {
        if ( ! isset( $_POST[ $key ] ) ) {
            return null;
        }

        $value = $_POST[ $key ];

        if ( is_array( $value ) ) {
            return array_map( 'wp_unslash', $value );
        }

        return wp_unslash( $value );
    }

    private function sanitize_text_value( $key ) {
        $value = $this->get_post_value( $key );

        if ( null === $value ) {
            return '';
        }

        if ( is_array( $value ) ) {
            $value = implode( ',', $value );
        }

        return sanitize_text_field( $value );
    }

    private function sanitize_select_value( $key, $allowed, $allow_empty = true ) {
        $value = $this->get_post_value( $key );

        if ( null === $value ) {
            return $allow_empty ? '' : reset( $allowed );
        }

        if ( is_array( $value ) ) {
            $value = reset( $value );
        }

        $value = sanitize_text_field( $value );

        if ( '' === $value && $allow_empty ) {
            return '';
        }

        if ( in_array( $value, $allowed, true ) ) {
            return $value;
        }

        return $allow_empty ? '' : reset( $allowed );
    }

    private function sanitize_date_value( $key ) {
        $value = $this->get_post_value( $key );

        if ( null === $value ) {
            return '';
        }

        if ( is_array( $value ) ) {
            $value = reset( $value );
        }

        $value = sanitize_text_field( $value );

        return $value;
    }

    private function sanitize_time_value( $key ) {
        $value = $this->get_post_value( $key );

        if ( null === $value ) {
            return '';
        }

        if ( is_array( $value ) ) {
            $value = reset( $value );
        }

        return sanitize_text_field( $value );
    }

    private function sanitize_decimal_value( $key ) {
        $value = $this->get_post_value( $key );

        if ( null === $value ) {
            return '0.00';
        }

        if ( is_array( $value ) ) {
            $value = reset( $value );
        }

        $value = trim( (string) $value );

        if ( '' === $value ) {
            return '0.00';
        }

        $normalized = preg_replace( '/[^0-9\-\.]/', '', $value );

        if ( '' === $normalized ) {
            return '0.00';
        }

        return number_format( (float) $normalized, 2, '.', '' );
    }

    private function sanitize_url_value( $key ) {
        $value = $this->get_post_value( $key );

        if ( null === $value ) {
            return '';
        }

        if ( is_array( $value ) ) {
            $value = reset( $value );
        }

        return esc_url_raw( $value );
    }
}
