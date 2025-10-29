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
        add_action( 'admin_post_cpb_delete_cron_event', array( $this, 'handle_delete_cron_event' ) );
        add_action( 'admin_post_cpb_run_cron_event', array( $this, 'handle_run_cron_event' ) );
    }

    public function add_menu() {
        add_menu_page(
            __( 'CPB Main Entity', 'codex-plugin-boilerplate' ),
            __( 'CPB Main Entity', 'codex-plugin-boilerplate' ),
            'manage_options',
            'cpb-main-entity',
            array( $this, 'render_main_entity_page' )
        );

        add_menu_page(
            __( 'CPB Settings', 'codex-plugin-boilerplate' ),
            __( 'CPB Settings', 'codex-plugin-boilerplate' ),
            'manage_options',
            'cpb-settings',
            array( $this, 'render_settings_page' )
        );

        add_menu_page(
            __( 'CPB Communications', 'codex-plugin-boilerplate' ),
            __( 'CPB Communications', 'codex-plugin-boilerplate' ),
            'manage_options',
            'cpb-communications',
            array( $this, 'render_communications_page' )
        );

        add_menu_page(
            __( 'CPB Logs', 'codex-plugin-boilerplate' ),
            __( 'CPB Logs', 'codex-plugin-boilerplate' ),
            'manage_options',
            'cpb-logs',
            array( $this, 'render_logs_page' )
        );
    }

    public function render_communications_page() {
        $tabs = array(
            'email-templates' => __( 'Email Templates', 'codex-plugin-boilerplate' ),
            'email-logs'      => __( 'Email Logs', 'codex-plugin-boilerplate' ),
            'sms-templates'   => __( 'SMS Templates', 'codex-plugin-boilerplate' ),
            'sms-logs'        => __( 'SMS Logs', 'codex-plugin-boilerplate' ),
        );

        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'email-templates';

        if ( ! array_key_exists( $active_tab, $tabs ) ) {
            $active_tab = 'email-templates';
        }

        echo '<div class="wrap"><h1>' . esc_html__( 'CPB Communications', 'codex-plugin-boilerplate' ) . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';

        foreach ( $tabs as $tab_slug => $label ) {
            $classes = array( 'nav-tab' );

            if ( $tab_slug === $active_tab ) {
                $classes[] = 'nav-tab-active';
            }

            printf(
                '<a href="%1$s" class="%2$s">%3$s</a>',
                esc_url( add_query_arg( array( 'page' => 'cpb-communications', 'tab' => $tab_slug ), admin_url( 'admin.php' ) ) ),
                esc_attr( implode( ' ', $classes ) ),
                esc_html( $label )
            );
        }

        echo '</h2>';

        $this->top_message_center();

        if ( 'email-templates' === $active_tab ) {
            $this->render_email_templates_tab();
        } elseif ( 'email-logs' === $active_tab ) {
            $this->render_communications_placeholder_tab(
                __( 'Email history tooling is coming soon.', 'codex-plugin-boilerplate' )
            );
        } elseif ( 'sms-templates' === $active_tab ) {
            $this->render_communications_placeholder_tab(
                __( 'SMS template management is coming soon.', 'codex-plugin-boilerplate' )
            );
        } else {
            $this->render_communications_placeholder_tab(
                __( 'SMS log history is coming soon.', 'codex-plugin-boilerplate' )
            );
        }

        $this->bottom_message_center();
        echo '</div>';
    }

    private function render_email_templates_tab() {
        $templates    = $this->get_sample_email_templates();
        $meta_labels  = array(
            'trigger'             => __( 'Trigger', 'codex-plugin-boilerplate' ),
            'communication_type'  => __( 'Communication Type', 'codex-plugin-boilerplate' ),
            'category'            => __( 'Category', 'codex-plugin-boilerplate' ),
        );

        echo '<div class="cpb-communications">';
        echo '<p class="description">' . esc_html__( 'Review placeholder email templates that demonstrate how communications can be grouped for future automation requests.', 'codex-plugin-boilerplate' ) . '</p>';
        echo '<div class="cpb-accordion-group" data-cpb-accordion-group="communications">';

        foreach ( $templates as $template ) {
            $item_id    = sanitize_html_class( $template['id'] );
            $panel_id   = $item_id . '-panel';
            $header_id  = $item_id . '-header';
            $tooltip    = isset( $template['tooltip'] ) ? $template['tooltip'] : '';
            $meta_items = isset( $template['meta'] ) ? $template['meta'] : array();

            echo '<div class="cpb-accordion__item">';
            printf(
                '<button type="button" id="%1$s" class="cpb-accordion__header" aria-expanded="false" aria-controls="%2$s">',
                esc_attr( $header_id ),
                esc_attr( $panel_id )
            );

            echo '<span class="cpb-accordion__summary">';
            echo '<span class="cpb-accordion__primary">';

            if ( $tooltip ) {
                printf(
                    '<span class="dashicons dashicons-info cpb-tooltip-icon" aria-hidden="true" data-tooltip="%1$s"></span><span class="screen-reader-text">%2$s</span>',
                    esc_attr( $tooltip ),
                    esc_html( $tooltip )
                );
            }

            echo '<span class="cpb-accordion__title">' . esc_html( $template['title'] ) . '</span>';
            echo '</span>';

            if ( ! empty( $meta_items ) ) {
                echo '<span class="cpb-accordion__meta">';

                foreach ( $meta_items as $meta_key => $meta_value ) {
                    if ( empty( $meta_value ) || ! isset( $meta_labels[ $meta_key ] ) ) {
                        continue;
                    }

                    printf(
                        '<span class="cpb-accordion__meta-item"><span class="cpb-accordion__meta-label">%1$s:</span> %2$s</span>',
                        esc_html( $meta_labels[ $meta_key ] ),
                        esc_html( $meta_value )
                    );
                }

                echo '</span>';
            }

            echo '</span>';
            echo '<span class="dashicons dashicons-arrow-down-alt2 cpb-accordion__icon" aria-hidden="true"></span>';
            echo '</button>';

            printf(
                '<div id="%1$s" class="cpb-accordion__panel" role="region" aria-labelledby="%2$s" aria-hidden="true">',
                esc_attr( $panel_id ),
                esc_attr( $header_id )
            );
            echo '<p>' . esc_html( $template['content'] ) . '</p>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    private function render_communications_placeholder_tab( $message ) {
        echo '<div class="cpb-communications cpb-communications--placeholder">';
        echo '<p>' . esc_html( $message ) . '</p>';
        echo '</div>';
    }

    private function get_sample_email_templates() {
        return array(
            array(
                'id'       => 'cpb-email-welcome',
                'title'    => __( 'Welcome Aboard', 'codex-plugin-boilerplate' ),
                'tooltip'  => __( 'Sent after a customer signs up to introduce key onboarding steps.', 'codex-plugin-boilerplate' ),
                'meta'     => array(
                    'trigger'            => __( 'New registration', 'codex-plugin-boilerplate' ),
                    'communication_type' => __( 'External', 'codex-plugin-boilerplate' ),
                    'category'           => __( 'Onboarding', 'codex-plugin-boilerplate' ),
                ),
                'content'  => __( 'Test text', 'codex-plugin-boilerplate' ),
            ),
            array(
                'id'       => 'cpb-email-follow-up',
                'title'    => __( 'Consultation Follow Up', 'codex-plugin-boilerplate' ),
                'tooltip'  => __( 'Delivers recap notes and next steps after a discovery call wraps up.', 'codex-plugin-boilerplate' ),
                'meta'     => array(
                    'trigger'            => __( 'Completed consultation', 'codex-plugin-boilerplate' ),
                    'communication_type' => __( 'External', 'codex-plugin-boilerplate' ),
                    'category'           => __( 'Sales Enablement', 'codex-plugin-boilerplate' ),
                ),
                'content'  => __( 'Test text', 'codex-plugin-boilerplate' ),
            ),
            array(
                'id'       => 'cpb-email-renewal',
                'title'    => __( 'Membership Renewal Reminder', 'codex-plugin-boilerplate' ),
                'tooltip'  => __( 'Warns members that their plan expires soon and outlines renewal options.', 'codex-plugin-boilerplate' ),
                'meta'     => array(
                    'trigger'            => __( 'Approaching renewal date', 'codex-plugin-boilerplate' ),
                    'communication_type' => __( 'External', 'codex-plugin-boilerplate' ),
                    'category'           => __( 'Retention', 'codex-plugin-boilerplate' ),
                ),
                'content'  => __( 'Test text', 'codex-plugin-boilerplate' ),
            ),
            array(
                'id'       => 'cpb-email-alert',
                'title'    => __( 'Internal Alert: Payment Review', 'codex-plugin-boilerplate' ),
                'tooltip'  => __( 'Flags the support team when a payment requires manual approval.', 'codex-plugin-boilerplate' ),
                'meta'     => array(
                    'trigger'            => __( 'Payment pending review', 'codex-plugin-boilerplate' ),
                    'communication_type' => __( 'Internal', 'codex-plugin-boilerplate' ),
                    'category'           => __( 'Operations', 'codex-plugin-boilerplate' ),
                ),
                'content'  => __( 'Test text', 'codex-plugin-boilerplate' ),
            ),
        );
    }

    public function enqueue_assets( $hook ) {
        if ( false === strpos( $hook, 'cpb' ) ) {
            return;
        }
        wp_enqueue_style( 'cpb-admin', CPB_PLUGIN_URL . 'assets/css/admin.css', array(), CPB_VERSION );
        wp_enqueue_script( 'cpb-admin', CPB_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), CPB_VERSION, true );
        wp_enqueue_media();
        wp_enqueue_editor();
        wp_localize_script( 'cpb-admin', 'cpbAjax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'cpb_ajax_nonce' ),
        ) );
        wp_localize_script( 'cpb-admin', 'cpbAdmin', array(
            'placeholders' => $this->get_placeholder_labels(),
            'delete'       => __( 'Delete', 'codex-plugin-boilerplate' ),
            'none'         => __( 'No entries found.', 'codex-plugin-boilerplate' ),
            'mediaTitle'   => __( 'Select Image', 'codex-plugin-boilerplate' ),
            'mediaButton'  => __( 'Use this image', 'codex-plugin-boilerplate' ),
            'itemPlaceholder' => __( 'Item #%d', 'codex-plugin-boilerplate' ),
            'error'        => __( 'Something went wrong. Please try again.', 'codex-plugin-boilerplate' ),
        ) );
    }

    private function get_placeholder_labels() {
        $labels = array();
        for ( $i = 1; $i <= 28; $i++ ) {
            $labels[] = sprintf( __( 'Placeholder %d', 'codex-plugin-boilerplate' ), $i );
        }
        return $labels;
    }

    private function get_us_states() {
        return array(
            __( 'Alabama', 'codex-plugin-boilerplate' ),
            __( 'Alaska', 'codex-plugin-boilerplate' ),
            __( 'Arizona', 'codex-plugin-boilerplate' ),
            __( 'Arkansas', 'codex-plugin-boilerplate' ),
            __( 'California', 'codex-plugin-boilerplate' ),
            __( 'Colorado', 'codex-plugin-boilerplate' ),
            __( 'Connecticut', 'codex-plugin-boilerplate' ),
            __( 'Delaware', 'codex-plugin-boilerplate' ),
            __( 'Florida', 'codex-plugin-boilerplate' ),
            __( 'Georgia', 'codex-plugin-boilerplate' ),
            __( 'Hawaii', 'codex-plugin-boilerplate' ),
            __( 'Idaho', 'codex-plugin-boilerplate' ),
            __( 'Illinois', 'codex-plugin-boilerplate' ),
            __( 'Indiana', 'codex-plugin-boilerplate' ),
            __( 'Iowa', 'codex-plugin-boilerplate' ),
            __( 'Kansas', 'codex-plugin-boilerplate' ),
            __( 'Kentucky', 'codex-plugin-boilerplate' ),
            __( 'Louisiana', 'codex-plugin-boilerplate' ),
            __( 'Maine', 'codex-plugin-boilerplate' ),
            __( 'Maryland', 'codex-plugin-boilerplate' ),
            __( 'Massachusetts', 'codex-plugin-boilerplate' ),
            __( 'Michigan', 'codex-plugin-boilerplate' ),
            __( 'Minnesota', 'codex-plugin-boilerplate' ),
            __( 'Mississippi', 'codex-plugin-boilerplate' ),
            __( 'Missouri', 'codex-plugin-boilerplate' ),
            __( 'Montana', 'codex-plugin-boilerplate' ),
            __( 'Nebraska', 'codex-plugin-boilerplate' ),
            __( 'Nevada', 'codex-plugin-boilerplate' ),
            __( 'New Hampshire', 'codex-plugin-boilerplate' ),
            __( 'New Jersey', 'codex-plugin-boilerplate' ),
            __( 'New Mexico', 'codex-plugin-boilerplate' ),
            __( 'New York', 'codex-plugin-boilerplate' ),
            __( 'North Carolina', 'codex-plugin-boilerplate' ),
            __( 'North Dakota', 'codex-plugin-boilerplate' ),
            __( 'Ohio', 'codex-plugin-boilerplate' ),
            __( 'Oklahoma', 'codex-plugin-boilerplate' ),
            __( 'Oregon', 'codex-plugin-boilerplate' ),
            __( 'Pennsylvania', 'codex-plugin-boilerplate' ),
            __( 'Rhode Island', 'codex-plugin-boilerplate' ),
            __( 'South Carolina', 'codex-plugin-boilerplate' ),
            __( 'South Dakota', 'codex-plugin-boilerplate' ),
            __( 'Tennessee', 'codex-plugin-boilerplate' ),
            __( 'Texas', 'codex-plugin-boilerplate' ),
            __( 'Utah', 'codex-plugin-boilerplate' ),
            __( 'Vermont', 'codex-plugin-boilerplate' ),
            __( 'Virginia', 'codex-plugin-boilerplate' ),
            __( 'Washington', 'codex-plugin-boilerplate' ),
            __( 'West Virginia', 'codex-plugin-boilerplate' ),
            __( 'Wisconsin', 'codex-plugin-boilerplate' ),
            __( 'Wyoming', 'codex-plugin-boilerplate' ),
        );
    }

    private function get_us_states_and_territories() {
        return array_merge(
            $this->get_us_states(),
            array(
                __( 'District of Columbia', 'codex-plugin-boilerplate' ),
                __( 'American Samoa', 'codex-plugin-boilerplate' ),
                __( 'Guam', 'codex-plugin-boilerplate' ),
                __( 'Northern Mariana Islands', 'codex-plugin-boilerplate' ),
                __( 'Puerto Rico', 'codex-plugin-boilerplate' ),
                __( 'U.S. Virgin Islands', 'codex-plugin-boilerplate' ),
            )
        );
    }

    private function get_tooltips() {
        $tooltips = array();
        for ( $i = 1; $i <= 28; $i++ ) {
            $tooltips[ 'placeholder_' . $i ] = sprintf(
                __( 'Tooltip placeholder text for Placeholder %d', 'codex-plugin-boilerplate' ),
                $i
            );
        }
        return $tooltips;
    }

    private function top_message_center() {
        echo '<div class="cpb-top-message">';
        echo '<div class="cpb-top-row">';
        echo '<div class="cpb-top-left">';
        echo '<h3>' . esc_html__( 'Need help? Watch the Tutorial video!', 'codex-plugin-boilerplate' ) . '</h3>';
        echo '<div class="cpb-video-container"><iframe width="100%" height="200" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>';
        echo '</div>';
        echo '<div class="cpb-top-right">';
        echo '<h3>' . esc_html__( 'Upgrade to Premium Today', 'codex-plugin-boilerplate' ) . '</h3>';
        $upgrade_text = sprintf(
            __( 'Upgrade to the Premium version of Codex Plugin Boilerplate today and receive additional features, options, priority customer support, and a dedicated hour of setup and customization! %s', 'codex-plugin-boilerplate' ),
            '<a href="https://levelupmarketers.com" target="_blank">' . esc_html__( 'Click here to upgrade now.', 'codex-plugin-boilerplate' ) . '</a>'
        );
        echo '<p>' . wp_kses_post( $upgrade_text ) . '</p>';
        echo '<a class="cpb-upgrade-button" href="https://levelupmarketers.com" target="_blank">' . esc_html__( 'Upgrade Now', 'codex-plugin-boilerplate' ) . '</a>';
        echo '<a href="https://levelupmarketers.com" target="_blank"><img src="' . esc_url( CPB_PLUGIN_URL . 'assets/images/levelup-logo.svg' ) . '" alt="' . esc_attr__( 'Level Up Digital Marketing logo', 'codex-plugin-boilerplate' ) . '" class="cpb-premium-logo" /></a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private function bottom_message_center() {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        $plugin_data = get_plugin_data( CPB_PLUGIN_DIR . 'codex-plugin-boilerplate.php' );
        $plugin_name = $plugin_data['Name'];

        echo '<div class="cpb-top-message cpb-bottom-message-digital-marketing-section">';
        echo '<div class="cpb-top-logo-row">';
        echo '<a href="https://levelupmarketers.com" target="_blank"><img src="' . esc_url( CPB_PLUGIN_URL . 'assets/images/levelup-logo.svg' ) . '" alt="' . esc_attr__( 'Level Up Digital Marketing logo', 'codex-plugin-boilerplate' ) . '" class="cpb-premium-logo" /></a>';
        $thanks = sprintf(
            /* translators: %s: Plugin name. */
            __( 'Thanks <span class="cpb-so-much">SO MUCH</span> for using %s - a Level Up plugin!', 'codex-plugin-boilerplate' ),
            esc_html( $plugin_name )
        );
        echo '<p class="cpb-thanks-message">' . wp_kses_post( $thanks ) . '</p>';
        $tagline = sprintf(
            __( 'Need marketing or custom software development help? Email %1$s or call %2$s now!', 'codex-plugin-boilerplate' ),
            '<a href="mailto:contact@levelupmarketers.com">contact@levelupmarketers.com</a>',
            '<a href="tel:18044898188">(804) 489-8188</a>'
        );
        echo '<p class="cpb-top-tagline">' . wp_kses_post( $tagline ) . '</p>';
        echo '</div>';
        echo '</div>';
    }

    public function render_main_entity_page() {
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'create';
        echo '<div class="wrap"><h1>' . esc_html__( 'CPB Main Entity', 'codex-plugin-boilerplate' ) . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=cpb-main-entity&tab=create" class="nav-tab ' . ( 'create' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Create a Main Entity', 'codex-plugin-boilerplate' ) . '</a>';
        echo '<a href="?page=cpb-main-entity&tab=edit" class="nav-tab ' . ( 'edit' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Edit Main Entity', 'codex-plugin-boilerplate' ) . '</a>';
        echo '</h2>';
        $this->top_message_center();

        if ( 'edit' === $active_tab ) {
            $this->render_edit_tab();
        } else {
            $this->render_create_tab();
        }

        $this->bottom_message_center();
        echo '</div>';
    }

    private function render_create_tab() {
        $tooltips = $this->get_tooltips();
        $fields    = array(
            array(
                'name'    => 'placeholder_1',
                'label'   => __( 'Placeholder 1', 'codex-plugin-boilerplate' ),
                'type'    => 'text',
                'tooltip' => $tooltips['placeholder_1'],
            ),
            array(
                'name'    => 'placeholder_2',
                'label'   => __( 'Placeholder 2', 'codex-plugin-boilerplate' ),
                'type'    => 'text',
                'tooltip' => $tooltips['placeholder_2'],
            ),
            array(
                'name'    => 'placeholder_3',
                'label'   => __( 'Placeholder 3', 'codex-plugin-boilerplate' ),
                'type'    => 'date',
                'tooltip' => $tooltips['placeholder_3'],
            ),
            array(
                'name'    => 'placeholder_4',
                'label'   => __( 'Placeholder 4', 'codex-plugin-boilerplate' ),
                'type'    => 'select',
                'options' => array(
                    ''  => __( 'Make a Selection...', 'codex-plugin-boilerplate' ),
                    '0' => __( 'No', 'codex-plugin-boilerplate' ),
                    '1' => __( 'Yes', 'codex-plugin-boilerplate' ),
                ),
                'tooltip' => $tooltips['placeholder_4'],
            ),
            array(
                'name'    => 'placeholder_5',
                'label'   => __( 'Placeholder 5', 'codex-plugin-boilerplate' ),
                'type'    => 'time',
                'tooltip' => $tooltips['placeholder_5'],
            ),
            array(
                'name'    => 'placeholder_6',
                'label'   => __( 'Placeholder 6', 'codex-plugin-boilerplate' ),
                'type'    => 'time',
                'tooltip' => $tooltips['placeholder_6'],
            ),
            array(
                'name'    => 'placeholder_7',
                'label'   => __( 'Placeholder 7', 'codex-plugin-boilerplate' ),
                'type'    => 'select',
                'options' => array(
                    ''  => __( 'Make a Selection...', 'codex-plugin-boilerplate' ),
                    '0' => __( 'No', 'codex-plugin-boilerplate' ),
                    '1' => __( 'Yes', 'codex-plugin-boilerplate' ),
                ),
                'tooltip' => $tooltips['placeholder_7'],
            ),
            array(
                'name'    => 'placeholder_8',
                'label'   => __( 'Placeholder 8', 'codex-plugin-boilerplate' ),
                'type'    => 'text',
                'tooltip' => $tooltips['placeholder_8'],
            ),
            array(
                'name'    => 'placeholder_9',
                'label'   => __( 'Placeholder 9', 'codex-plugin-boilerplate' ),
                'type'    => 'text',
                'tooltip' => $tooltips['placeholder_9'],
            ),
            array(
                'name'    => 'placeholder_10',
                'label'   => __( 'Placeholder 10', 'codex-plugin-boilerplate' ),
                'type'    => 'text',
                'tooltip' => $tooltips['placeholder_10'],
            ),
            array(
                'name'    => 'placeholder_11',
                'label'   => __( 'Placeholder 11', 'codex-plugin-boilerplate' ),
                'type'    => 'state',
                'tooltip' => $tooltips['placeholder_11'],
            ),
            array(
                'name'    => 'placeholder_12',
                'label'   => __( 'Placeholder 12', 'codex-plugin-boilerplate' ),
                'type'    => 'text',
                'tooltip' => $tooltips['placeholder_12'],
            ),
            array(
                'name'    => 'placeholder_13',
                'label'   => __( 'Placeholder 13', 'codex-plugin-boilerplate' ),
                'type'    => 'text',
                'tooltip' => $tooltips['placeholder_13'],
            ),
            array(
                'name'    => 'placeholder_14',
                'label'   => __( 'Placeholder 14', 'codex-plugin-boilerplate' ),
                'type'    => 'url',
                'tooltip' => $tooltips['placeholder_14'],
            ),
            array(
                'name'    => 'placeholder_15',
                'label'   => __( 'Placeholder 15', 'codex-plugin-boilerplate' ),
                'type'    => 'select',
                'options' => array(
                    ''        => __( 'Make a Selection...', 'codex-plugin-boilerplate' ),
                    'option1' => __( 'Option 1', 'codex-plugin-boilerplate' ),
                    'option2' => __( 'Option 2', 'codex-plugin-boilerplate' ),
                    'option3' => __( 'Option 3', 'codex-plugin-boilerplate' ),
                ),
                'tooltip' => $tooltips['placeholder_15'],
            ),
            array(
                'name'    => 'placeholder_16',
                'label'   => __( 'Placeholder 16', 'codex-plugin-boilerplate' ),
                'type'    => 'number',
                'attrs'   => 'step="0.01" min="0"',
                'tooltip' => $tooltips['placeholder_16'],
            ),
            array(
                'name'    => 'placeholder_17',
                'label'   => __( 'Placeholder 17', 'codex-plugin-boilerplate' ),
                'type'    => 'number',
                'attrs'   => 'step="0.01" min="0"',
                'tooltip' => $tooltips['placeholder_17'],
            ),
            array(
                'name'    => 'placeholder_18',
                'label'   => __( 'Placeholder 18', 'codex-plugin-boilerplate' ),
                'type'    => 'number',
                'attrs'   => 'step="0.01" min="0"',
                'tooltip' => $tooltips['placeholder_18'],
            ),
            array(
                'name'    => 'placeholder_19',
                'label'   => __( 'Placeholder 19', 'codex-plugin-boilerplate' ),
                'type'    => 'select',
                'options' => array(
                    ''  => __( 'Make a Selection...', 'codex-plugin-boilerplate' ),
                    '0' => __( 'No', 'codex-plugin-boilerplate' ),
                    '1' => __( 'Yes', 'codex-plugin-boilerplate' ),
                ),
                'tooltip' => $tooltips['placeholder_19'],
            ),
            array(
                'name'    => 'placeholder_20',
                'label'   => __( 'Placeholder 20', 'codex-plugin-boilerplate' ),
                'type'    => 'select',
                'options' => array(
                    ''  => __( 'Make a Selection...', 'codex-plugin-boilerplate' ),
                    '0' => __( 'No', 'codex-plugin-boilerplate' ),
                    '1' => __( 'Yes', 'codex-plugin-boilerplate' ),
                ),
                'tooltip' => $tooltips['placeholder_20'],
            ),
            array(
                'name'    => 'placeholder_21',
                'label'   => __( 'Placeholder 21', 'codex-plugin-boilerplate' ),
                'type'    => 'state',
                'options' => $this->get_us_states_and_territories(),
                'tooltip' => $tooltips['placeholder_21'],
            ),
            array(
                'name'    => 'placeholder_22',
                'label'   => __( 'Placeholder 22', 'codex-plugin-boilerplate' ),
                'type'    => 'text',
                'tooltip' => $tooltips['placeholder_22'],
            ),
            array(
                'name'    => 'placeholder_23',
                'label'   => __( 'Placeholder 23', 'codex-plugin-boilerplate' ),
                'type'    => 'radio',
                'options' => array(
                    'option1' => array(
                        'label'   => __( 'Option 1', 'codex-plugin-boilerplate' ),
                        'tooltip' => __( 'Tooltip placeholder text for Placeholder 22 Option 1', 'codex-plugin-boilerplate' ),
                    ),
                    'option2' => array(
                        'label'   => __( 'Option 2', 'codex-plugin-boilerplate' ),
                        'tooltip' => __( 'Tooltip placeholder text for Placeholder 22 Option 2', 'codex-plugin-boilerplate' ),
                    ),
                    'option3' => array(
                        'label'   => __( 'Option 3', 'codex-plugin-boilerplate' ),
                        'tooltip' => __( 'Tooltip placeholder text for Placeholder 22 Option 3', 'codex-plugin-boilerplate' ),
                    ),
                ),
                'tooltip' => $tooltips['placeholder_23'],
            ),
            array(
                'name'    => 'placeholder_24',
                'label'   => __( 'Placeholder 24', 'codex-plugin-boilerplate' ),
                'type'    => 'opt_in',
                'tooltip' => $tooltips['placeholder_24'],
            ),
            array(
                'name'    => 'placeholder_25',
                'label'   => __( 'Placeholder 25', 'codex-plugin-boilerplate' ),
                'type'    => 'items',
                'tooltip' => $tooltips['placeholder_25'],
            ),
            array(
                'name'    => 'placeholder_26',
                'label'   => __( 'Placeholder 26', 'codex-plugin-boilerplate' ),
                'type'    => 'color',
                'attrs'   => 'value="#000000"',
                'tooltip' => $tooltips['placeholder_26'],
            ),
            array(
                'name'    => 'placeholder_27',
                'label'   => __( 'Placeholder 27', 'codex-plugin-boilerplate' ),
                'type'    => 'image',
                'tooltip' => $tooltips['placeholder_27'],
            ),
            array(
                'name'    => 'placeholder_28',
                'label'   => __( 'Placeholder 28', 'codex-plugin-boilerplate' ),
                'type'    => 'editor',
                'tooltip' => $tooltips['placeholder_28'],
                'full_width' => true,
            ),
        );
        echo '<form id="cpb-create-form"><div class="cpb-flex-form">';
        foreach ( $fields as $field ) {
            $classes = 'cpb-field';
            if ( ! empty( $field['full_width'] ) ) {
                $classes .= ' cpb-field-full';
            }
            echo '<div class="' . $classes . '">';
            echo '<label><span class="cpb-tooltip-icon dashicons dashicons-editor-help" data-tooltip="' . esc_attr( $field['tooltip'] ) . '"></span>' . esc_html( $field['label'] ) . '</label>';
            switch ( $field['type'] ) {
                case 'select':
                    echo '<select name="' . esc_attr( $field['name'] ) . '">';
                    foreach ( $field['options'] as $value => $label ) {
                        if ( '' === $value ) {
                            echo '<option value="" disabled selected>' . esc_html( $label ) . '</option>';
                        } else {
                            echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
                        }
                    }
                    echo '</select>';
                    break;
                case 'state':
                    $states = isset( $field['options'] ) ? $field['options'] : $this->get_us_states();
                    echo '<select name="' . esc_attr( $field['name'] ) . '">';
                    echo '<option value="" disabled selected>' . esc_html__( 'Make a Selection...', 'codex-plugin-boilerplate' ) . '</option>';
                    foreach ( $states as $state ) {
                        echo '<option value="' . esc_attr( $state ) . '">' . esc_html( $state ) . '</option>';
                    }
                    echo '</select>';
                    break;
                case 'radio':
                    foreach ( $field['options'] as $value => $opt ) {
                        echo '<label class="cpb-radio-option"><input type="radio" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $value ) . '" />';
                        echo ' <span class="cpb-tooltip-icon dashicons dashicons-editor-help" data-tooltip="' . esc_attr( $opt['tooltip'] ) . '"></span>';
                        echo esc_html( $opt['label'] ) . '</label>';
                    }
                    break;
                case 'editor':
                    wp_editor( '', $field['name'], array( 'textarea_name' => $field['name'] ) );
                    break;
                case 'opt_in':
                    $opts = array(
                        array(
                            'name'    => 'opt_in_marketing_email',
                            'label'   => __( 'Option 1', 'codex-plugin-boilerplate' ),
                            'tooltip' => __( 'Tooltip placeholder text for Placeholder 23 Option 1', 'codex-plugin-boilerplate' ),
                        ),
                        array(
                            'name'    => 'opt_in_marketing_sms',
                            'label'   => __( 'Option 2', 'codex-plugin-boilerplate' ),
                            'tooltip' => __( 'Tooltip placeholder text for Placeholder 23 Option 2', 'codex-plugin-boilerplate' ),
                        ),
                        array(
                            'name'    => 'opt_in_event_update_email',
                            'label'   => __( 'Option 3', 'codex-plugin-boilerplate' ),
                            'tooltip' => __( 'Tooltip placeholder text for Placeholder 23 Option 3', 'codex-plugin-boilerplate' ),
                        ),
                        array(
                            'name'    => 'opt_in_event_update_sms',
                            'label'   => __( 'Option 4', 'codex-plugin-boilerplate' ),
                            'tooltip' => __( 'Tooltip placeholder text for Placeholder 23 Option 4', 'codex-plugin-boilerplate' ),
                        ),
                    );
                    echo '<fieldset>';
                    foreach ( $opts as $opt ) {
                        echo '<label class="cpb-opt-in-option"><input type="checkbox" name="' . esc_attr( $opt['name'] ) . '" value="1" />';
                        echo ' <span class="cpb-tooltip-icon dashicons dashicons-editor-help" data-tooltip="' . esc_attr( $opt['tooltip'] ) . '"></span>';
                        echo esc_html( $opt['label'] ) . '</label>';
                    }
                    echo '</fieldset>';
                    break;
                case 'items':
                    echo '<div id="cpb-items-container">';
                    echo '<div class="cpb-item-row" style="margin-bottom:8px; display:flex; align-items:center;">';
                    echo '<input type="text" name="' . esc_attr( $field['name'] ) . '[]" class="regular-text cpb-item-field" placeholder="' . esc_attr__( 'Item #1', 'codex-plugin-boilerplate' ) . '" />';
                    echo '</div></div>';
                    echo '<button type="button" class="button" id="cpb-add-item" style="margin-top:8px;">' . esc_html__( '+ Add Another Item', 'codex-plugin-boilerplate' ) . '</button>';
                    break;
                case 'textarea':
                    echo '<textarea name="' . esc_attr( $field['name'] ) . '"></textarea>';
                    break;
                case 'image':
                    echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" />';
                    echo '<button type="button" class="button cpb-upload" data-target="#' . esc_attr( $field['name'] ) . '">' . esc_html__( 'Select Image', 'codex-plugin-boilerplate' ) . '</button>';
                    echo '<div id="' . esc_attr( $field['name'] ) . '-preview" style="margin-top:10px;"></div>';
                    break;
                default:
                    $attrs = isset( $field['attrs'] ) ? ' ' . $field['attrs'] : '';
                    echo '<input type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $field['name'] ) . '"' . $attrs . ' />';
                    break;
            }
            echo '</div>';
        }
        echo '</div>';
        $submit_button = get_submit_button( __( 'Save', 'codex-plugin-boilerplate' ), 'primary', 'submit', false );
        echo '<p class="submit">' . $submit_button;
        echo '<span class="cpb-feedback-area cpb-feedback-area--inline"><span id="cpb-spinner" class="spinner" aria-hidden="true"></span><span id="cpb-feedback" role="status" aria-live="polite"></span></span>';
        echo '</p>';
        echo '</form>';
    }

    private function render_edit_tab() {
        echo '<div id="cpb-entity-list" class="cpb-accordion"></div>';
        echo '<div class="cpb-feedback-area cpb-feedback-area--block"><span id="cpb-spinner" class="spinner" aria-hidden="true"></span><span id="cpb-feedback" role="status" aria-live="polite"></span></div>';
    }

    public function render_settings_page() {
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
        echo '<div class="wrap"><h1>' . esc_html__( 'CPB Settings', 'codex-plugin-boilerplate' ) . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=cpb-settings&tab=general" class="nav-tab ' . ( 'general' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'General Settings', 'codex-plugin-boilerplate' ) . '</a>';
        echo '<a href="?page=cpb-settings&tab=style" class="nav-tab ' . ( 'style' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Style Settings', 'codex-plugin-boilerplate' ) . '</a>';
        echo '<a href="?page=cpb-settings&tab=cron" class="nav-tab ' . ( 'cron' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Cron Jobs', 'codex-plugin-boilerplate' ) . '</a>';
        echo '</h2>';
        $this->top_message_center();

        if ( 'style' === $active_tab ) {
            $this->render_style_settings_tab();
        } elseif ( 'cron' === $active_tab ) {
            $this->render_cron_jobs_tab();
        } else {
            $this->render_general_settings_tab();
        }

        $this->bottom_message_center();
        echo '</div>';
    }

    private function render_general_settings_tab() {
        echo '<form id="cpb-general-settings-form">';
        echo '<label>' . esc_html__( 'Option', 'codex-plugin-boilerplate' ) . ' <span class="cpb-tooltip-icon dashicons dashicons-editor-help" data-tooltip="' . esc_attr__( 'Tooltip placeholder text for Option', 'codex-plugin-boilerplate' ) . '"></span></label>';
        echo '<input type="text" name="option" />';
        $submit_button = get_submit_button( __( 'Save Settings', 'codex-plugin-boilerplate' ), 'primary', 'submit', false );
        echo '<p class="submit">' . $submit_button;
        echo '<span class="cpb-feedback-area cpb-feedback-area--inline"><span id="cpb-spinner" class="spinner" aria-hidden="true"></span><span id="cpb-feedback" role="status" aria-live="polite"></span></span>';
        echo '</p>';
        echo '</form>';
    }

    private function render_style_settings_tab() {
        echo '<form id="cpb-style-settings-form">';
        echo '<label>' . esc_html__( 'Custom CSS', 'codex-plugin-boilerplate' ) . ' <span class="cpb-tooltip-icon dashicons dashicons-editor-help" data-tooltip="' . esc_attr__( 'Tooltip placeholder text for Custom CSS', 'codex-plugin-boilerplate' ) . '"></span></label>';
        echo '<textarea name="custom_css"></textarea>';
        $submit_button = get_submit_button( __( 'Save Settings', 'codex-plugin-boilerplate' ), 'primary', 'submit', false );
        echo '<p class="submit">' . $submit_button;
        echo '<span class="cpb-feedback-area cpb-feedback-area--inline"><span id="cpb-spinner" class="spinner" aria-hidden="true"></span><span id="cpb-feedback" role="status" aria-live="polite"></span></span>';
        echo '</p>';
        echo '</form>';
    }

    private function render_cron_jobs_tab() {
        $messages = array(
            'deleted'       => array(
                'type'    => 'success',
                'message' => __( 'Cron event deleted successfully.', 'codex-plugin-boilerplate' ),
            ),
            'delete_failed' => array(
                'type'    => 'error',
                'message' => __( 'Unable to delete the cron event. Please try again.', 'codex-plugin-boilerplate' ),
            ),
            'run'           => array(
                'type'    => 'success',
                'message' => __( 'Cron event executed immediately.', 'codex-plugin-boilerplate' ),
            ),
            'run_failed'    => array(
                'type'    => 'error',
                'message' => __( 'Unable to execute the cron event. Ensure the hook is registered.', 'codex-plugin-boilerplate' ),
            ),
        );

        $notice_key = isset( $_GET['cpb_cron_message'] ) ? sanitize_text_field( wp_unslash( $_GET['cpb_cron_message'] ) ) : '';

        if ( $notice_key && isset( $messages[ $notice_key ] ) ) {
            $notice = $messages[ $notice_key ];
            printf(
                '<div class="notice notice-%1$s"><p>%2$s</p></div>',
                esc_attr( $notice['type'] ),
                esc_html( $notice['message'] )
            );
        }

        $events    = CPB_Cron_Manager::get_plugin_cron_events();
        $per_page  = 20;
        $total     = count( $events );
        $page      = isset( $_GET['cpb_cron_page'] ) ? max( 1, absint( wp_unslash( $_GET['cpb_cron_page'] ) ) ) : 1;
        $max_pages = max( 1, (int) ceil( $total / $per_page ) );

        if ( $page > $max_pages ) {
            $page = $max_pages;
        }

        $offset          = ( $page - 1 ) * $per_page;
        $displayed_events = array_slice( $events, $offset, $per_page );

        $pagination_base = add_query_arg(
            array(
                'page' => 'cpb-settings',
                'tab'  => 'cron',
                'cpb_cron_page' => '%#%',
            ),
            admin_url( 'admin.php' )
        );

        $pagination = paginate_links(
            array(
                'base'      => $pagination_base,
                'format'    => '%#%',
                'current'   => $page,
                'total'     => $max_pages,
                'prev_text' => __( '&laquo; Previous', 'codex-plugin-boilerplate' ),
                'next_text' => __( 'Next &raquo;', 'codex-plugin-boilerplate' ),
                'type'      => 'list',
            )
        );

        echo '<p>' . esc_html__( 'Review and manage every scheduled cron event created by Codex Plugin Boilerplate. Each row is populated automatically from events that use the cpb_ hook prefix.', 'codex-plugin-boilerplate' ) . '</p>';

        if ( $pagination ) {
            echo '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post( $pagination ) . '</div></div>';
        }

        echo '<table class="widefat striped cpb-cron-table">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__( 'Cron Job', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Description', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Type', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Schedule', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Hook', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Next Run', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Countdown', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Arguments', 'codex-plugin-boilerplate' ) . '</th>';
        echo '<th>' . esc_html__( 'Actions', 'codex-plugin-boilerplate' ) . '</th>';
        echo '</tr></thead><tbody>';

        if ( empty( $displayed_events ) ) {
            echo '<tr><td colspan="9">' . esc_html__( 'No cron events found for Codex Plugin Boilerplate.', 'codex-plugin-boilerplate' ) . '</td></tr>';
        } else {
            $redirect = add_query_arg(
                array(
                    'page' => 'cpb-settings',
                    'tab'  => 'cron',
                ),
                admin_url( 'admin.php' )
            );

            if ( $page > 1 ) {
                $redirect = add_query_arg( 'cpb_cron_page', $page, $redirect );
            }

            foreach ( $displayed_events as $event ) {
                $hook_data      = CPB_Cron_Manager::get_hook_display_data( $event['hook'] );
                $type_label     = CPB_Cron_Manager::is_recurring( $event['schedule'] ) ? esc_html__( 'Recurring', 'codex-plugin-boilerplate' ) : esc_html__( 'One-off', 'codex-plugin-boilerplate' );
                $schedule_label = CPB_Cron_Manager::get_schedule_label( $event['schedule'], $event['interval'] );
                $next_run       = CPB_Cron_Manager::format_timestamp( $event['timestamp'] );
                $countdown      = CPB_Cron_Manager::get_countdown( $event['timestamp'] );
                $args_display   = empty( $event['args'] ) ? '&mdash;' : esc_html( wp_json_encode( $event['args'] ) );
                $args_encoded   = base64_encode( wp_json_encode( $event['args'] ) );

                if ( false === $args_encoded ) {
                    $args_encoded = '';
                }

                echo '<tr>';
                echo '<td><strong>' . esc_html( $hook_data['name'] ) . '</strong> <span class="cpb-tooltip-icon dashicons dashicons-editor-help" data-tooltip="' . esc_attr( $hook_data['description'] ) . '"></span></td>';
                echo '<td>' . esc_html( $hook_data['description'] ) . '</td>';
                echo '<td>' . esc_html( $type_label ) . '</td>';
                echo '<td>' . esc_html( $schedule_label ) . '</td>';
                echo '<td><code>' . esc_html( $event['hook'] ) . '</code></td>';
                echo '<td>' . esc_html( $next_run ) . '</td>';
                echo '<td>' . esc_html( $countdown ) . '</td>';
                echo '<td>' . ( empty( $event['args'] ) ? '&mdash;' : $args_display ) . '</td>';
                echo '<td>';
                echo '<div class="cpb-cron-actions">';
                echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="cpb-cron-action-form">';
                wp_nonce_field( 'cpb_run_cron_event', 'cpb_run_cron_event_nonce' );
                echo '<input type="hidden" name="action" value="cpb_run_cron_event" />';
                echo '<input type="hidden" name="hook" value="' . esc_attr( $event['hook'] ) . '" />';
                echo '<input type="hidden" name="args" value="' . esc_attr( $args_encoded ) . '" />';
                echo '<input type="hidden" name="redirect" value="' . esc_attr( $redirect ) . '" />';
                echo '<button type="submit" class="button button-secondary">' . esc_html__( 'Run Now', 'codex-plugin-boilerplate' ) . '</button>';
                echo '</form>';

                $confirm = esc_js( __( 'Are you sure you want to delete this cron event?', 'codex-plugin-boilerplate' ) );

                echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="cpb-cron-action-form" onsubmit="return confirm(\'' . $confirm . '\');">';
                wp_nonce_field( 'cpb_delete_cron_event', 'cpb_delete_cron_event_nonce' );
                echo '<input type="hidden" name="action" value="cpb_delete_cron_event" />';
                echo '<input type="hidden" name="hook" value="' . esc_attr( $event['hook'] ) . '" />';
                echo '<input type="hidden" name="timestamp" value="' . esc_attr( $event['timestamp'] ) . '" />';
                echo '<input type="hidden" name="args" value="' . esc_attr( $args_encoded ) . '" />';
                echo '<input type="hidden" name="redirect" value="' . esc_attr( $redirect ) . '" />';
                echo '<button type="submit" class="button button-link-delete">' . esc_html__( 'Delete Event', 'codex-plugin-boilerplate' ) . '</button>';
                echo '</form>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table>';

        if ( $pagination ) {
            echo '<div class="tablenav"><div class="tablenav-pages">' . wp_kses_post( $pagination ) . '</div></div>';
        }
    }

    private function decode_cron_args( $encoded ) {
        if ( empty( $encoded ) ) {
            return array();
        }

        $decoded = base64_decode( wp_unslash( $encoded ), true );

        if ( false === $decoded ) {
            return array();
        }

        $args = json_decode( $decoded, true );

        return is_array( $args ) ? $args : array();
    }

    private function get_cron_redirect_url() {
        $fallback = add_query_arg(
            array(
                'page' => 'cpb-settings',
                'tab'  => 'cron',
            ),
            admin_url( 'admin.php' )
        );

        if ( empty( $_POST['redirect'] ) ) {
            return $fallback;
        }

        $redirect = esc_url_raw( wp_unslash( $_POST['redirect'] ) );

        return $redirect ? $redirect : $fallback;
    }

    private function redirect_with_cron_message( $redirect, $message ) {
        $url = add_query_arg( 'cpb_cron_message', $message, $redirect );
        wp_safe_redirect( $url );
        exit;
    }

    public function handle_delete_cron_event() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions.', 'codex-plugin-boilerplate' ) );
        }

        check_admin_referer( 'cpb_delete_cron_event', 'cpb_delete_cron_event_nonce' );

        $redirect = $this->get_cron_redirect_url();
        $hook     = isset( $_POST['hook'] ) ? sanitize_text_field( wp_unslash( $_POST['hook'] ) ) : '';
        $timestamp = isset( $_POST['timestamp'] ) ? absint( wp_unslash( $_POST['timestamp'] ) ) : 0;
        $args     = $this->decode_cron_args( isset( $_POST['args'] ) ? $_POST['args'] : '' );

        if ( empty( $hook ) || 0 !== strpos( $hook, CPB_Cron_Manager::HOOK_PREFIX ) || empty( $timestamp ) ) {
            $this->redirect_with_cron_message( $redirect, 'delete_failed' );
        }

        $deleted = wp_unschedule_event( $timestamp, $hook, $args );

        if ( $deleted ) {
            $this->redirect_with_cron_message( $redirect, 'deleted' );
        }

        $this->redirect_with_cron_message( $redirect, 'delete_failed' );
    }

    public function handle_run_cron_event() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions.', 'codex-plugin-boilerplate' ) );
        }

        check_admin_referer( 'cpb_run_cron_event', 'cpb_run_cron_event_nonce' );

        $redirect = $this->get_cron_redirect_url();
        $hook     = isset( $_POST['hook'] ) ? sanitize_text_field( wp_unslash( $_POST['hook'] ) ) : '';
        $args     = $this->decode_cron_args( isset( $_POST['args'] ) ? $_POST['args'] : '' );

        if ( empty( $hook ) || 0 !== strpos( $hook, CPB_Cron_Manager::HOOK_PREFIX ) ) {
            $this->redirect_with_cron_message( $redirect, 'run_failed' );
        }

        if ( ! has_action( $hook ) ) {
            $this->redirect_with_cron_message( $redirect, 'run_failed' );
        }

        do_action_ref_array( $hook, $args );

        $this->redirect_with_cron_message( $redirect, 'run' );
    }

    public function render_logs_page() {
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'generated_content';
        echo '<div class="wrap"><h1>' . esc_html__( 'CPB Logs', 'codex-plugin-boilerplate' ) . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=cpb-logs&tab=generated_content" class="nav-tab ' . ( 'generated_content' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Generated Content', 'codex-plugin-boilerplate' ) . '</a>';
        echo '</h2>';
        $this->top_message_center();

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
