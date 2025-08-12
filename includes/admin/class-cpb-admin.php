<?php
/**
 * Admin pages for Codex Plugin Boilerplate
 *
 * @package Codex_Plugin_Boilerplate
 */

class CPB_Admin {

    public function register() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_post_cpb_delete_generated_content', array( $this, 'handle_delete_generated_content' ) );
    }

    public function add_menu() {
        add_menu_page(
            __( 'CPB Main Entity', 'codex-plugin-boilerplate' ),
            __( 'CPB Main Entity', 'codex-plugin-boilerplate' ),
            'manage_options',
            'cpb-main-entity',
            array( $this, 'render_main_entity_page' )
        );

        add_submenu_page(
            'cpb-main-entity',
            __( 'Settings', 'codex-plugin-boilerplate' ),
            __( 'Settings', 'codex-plugin-boilerplate' ),
            'manage_options',
            'cpb-settings',
            array( $this, 'render_settings_page' )
        );

        add_submenu_page(
            'cpb-main-entity',
            __( 'Logs', 'codex-plugin-boilerplate' ),
            __( 'Logs', 'codex-plugin-boilerplate' ),
            'manage_options',
            'cpb-logs',
            array( $this, 'render_logs_page' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( false === strpos( $hook, 'cpb' ) ) {
            return;
        }
        wp_enqueue_style( 'cpb-admin', CPB_PLUGIN_URL . 'assets/css/admin.css', array(), CPB_VERSION );
        wp_enqueue_script( 'cpb-admin', CPB_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), CPB_VERSION, true );
        wp_localize_script( 'cpb-admin', 'cpbAjax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'cpb_ajax_nonce' ),
        ) );
    }

    private function top_message_center() {
        echo '<div class="cpb-top-message">';
        echo '<p><a href="https://www.youtube.com" target="_blank">' . esc_html__( 'Watch tutorial', 'codex-plugin-boilerplate' ) . '</a> | ';
        echo esc_html__( 'Premium support available.', 'codex-plugin-boilerplate' ) . ' ';
        echo '<a href="https://levelupmarketers.com" target="_blank">levelupmarketers.com</a></p>';
        echo '</div>';
    }

    private function bottom_message_center() {
        echo '<div class="cpb-bottom-message">';
        echo '<p><a href="https://levelupmarketers.com" target="_blank">' . esc_html__( 'Upgrade for more features', 'codex-plugin-boilerplate' ) . '</a></p>';
        echo '</div>';
    }

    public function render_main_entity_page() {
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'create';
        echo '<div class="wrap"><h1>' . esc_html__( 'CPB Main Entity', 'codex-plugin-boilerplate' ) . '</h1>';
        $this->top_message_center();
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=cpb-main-entity&tab=create" class="nav-tab ' . ( 'create' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Create a Main Entity', 'codex-plugin-boilerplate' ) . '</a>';
        echo '<a href="?page=cpb-main-entity&tab=edit" class="nav-tab ' . ( 'edit' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Edit Main Entity', 'codex-plugin-boilerplate' ) . '</a>';
        echo '</h2>';

        if ( 'edit' === $active_tab ) {
            $this->render_edit_tab();
        } else {
            $this->render_create_tab();
        }

        $this->bottom_message_center();
        echo '</div>';
    }

    private function render_create_tab() {
        echo '<form id="cpb-create-form">';
        echo '<label>' . esc_html__( 'Name', 'codex-plugin-boilerplate' ) . ' <span title="' . esc_attr__( 'Enter a name for the entity.', 'codex-plugin-boilerplate' ) . '">?</span></label>';
        echo '<input type="text" name="name" />';
        submit_button( __( 'Save', 'codex-plugin-boilerplate' ) );
        echo '</form>';
        echo '<div id="cpb-feedback"></div><div id="cpb-spinner" class="spinner"></div>';
    }

    private function render_edit_tab() {
        echo '<div id="cpb-entity-list" class="cpb-accordion"></div>';
        echo '<div id="cpb-feedback"></div><div id="cpb-spinner" class="spinner"></div>';
    }

    public function render_settings_page() {
        echo '<div class="wrap"><h1>' . esc_html__( 'Settings', 'codex-plugin-boilerplate' ) . '</h1>';
        $this->top_message_center();
        echo '<form id="cpb-settings-form">';
        echo '<h2>' . esc_html__( 'General Settings', 'codex-plugin-boilerplate' ) . '</h2>';
        echo '<label>' . esc_html__( 'Option', 'codex-plugin-boilerplate' ) . ' <span title="' . esc_attr__( 'General option.', 'codex-plugin-boilerplate' ) . '">?</span></label>';
        echo '<input type="text" name="option" />';
        echo '<h2>' . esc_html__( 'Style Settings', 'codex-plugin-boilerplate' ) . '</h2>';
        echo '<label>' . esc_html__( 'Custom CSS', 'codex-plugin-boilerplate' ) . ' <span title="' . esc_attr__( 'CSS for styling shortcodes/blocks.', 'codex-plugin-boilerplate' ) . '">?</span></label>';
        echo '<textarea name="custom_css"></textarea>';
        submit_button( __( 'Save Settings', 'codex-plugin-boilerplate' ) );
        echo '</form>';
        echo '<div id="cpb-feedback"></div><div id="cpb-spinner" class="spinner"></div>';
        $this->bottom_message_center();
        echo '</div>';
    }

    public function render_logs_page() {
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'generated_content';
        echo '<div class="wrap"><h1>' . esc_html__( 'Logs', 'codex-plugin-boilerplate' ) . '</h1>';
        $this->top_message_center();
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=cpb-logs&tab=generated_content" class="nav-tab ' . ( 'generated_content' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Generated Content', 'codex-plugin-boilerplate' ) . '</a>';
        echo '</h2>';

        if ( 'generated_content' === $active_tab ) {
            $this->render_generated_content_log();
        }

        $this->bottom_message_center();
        echo '</div>';
    }

    private function render_generated_content_log() {
        $logger  = new CPB_Content_Logger();
        $entries = $logger->get_logged_content();
        echo '<table class="widefat"><thead><tr>';
        echo '<th>' . esc_html__( 'Title', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Type', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Actions', 'codex-plugin-boilerplate' ) . '</th>';
        echo '</tr></thead><tbody>';
        if ( $entries ) {
            foreach ( $entries as $entry ) {
                $post = get_post( $entry->post_id );
                if ( ! $post ) {
                    continue;
                }
                $view   = get_permalink( $post );
                $edit   = get_edit_post_link( $post->ID );
                $delete = wp_nonce_url( admin_url( 'admin-post.php?action=cpb_delete_generated_content&post_id=' . $post->ID ), 'cpb_delete_generated_content_' . $post->ID );
                echo '<tr>';
                echo '<td><a href="' . esc_url( $view ) . '" target="_blank">' . esc_html( get_the_title( $post ) ) . '</a></td>';
                echo '<td>' . esc_html( ucfirst( $entry->post_type ) ) . '</td>';
                echo '<td><a href="' . esc_url( $edit ) . '">' . esc_html__( 'Edit', 'codex-plugin-boilerplate' ) . '</a> | ';
                $confirm = esc_js( __( 'Are you sure you want to delete this item?', 'codex-plugin-boilerplate' ) );
                echo '<a href="' . esc_url( $delete ) . '" onclick="return confirm(\'' . $confirm . '\');">' . esc_html__( 'Delete', 'codex-plugin-boilerplate' ) . '</a></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3">' . esc_html__( 'No generated content found.', 'codex-plugin-boilerplate' ) . '</td></tr>';
        }
        echo '</tbody></table>';
    }

    public function handle_delete_generated_content() {
        if ( ! current_user_can( 'delete_posts' ) ) {
            wp_die( esc_html__( 'Insufficient permissions.', 'codex-plugin-boilerplate' ) );
        }
        $post_id = isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : 0;
        check_admin_referer( 'cpb_delete_generated_content_' . $post_id );
        wp_delete_post( $post_id, true );
        wp_redirect( admin_url( 'admin.php?page=cpb-logs&tab=generated_content' ) );
        exit;
    }
}
