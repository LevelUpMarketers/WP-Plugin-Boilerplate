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

        add_menu_page(
            __( 'CPB Settings', 'codex-plugin-boilerplate' ),
            __( 'CPB Settings', 'codex-plugin-boilerplate' ),
            'manage_options',
            'cpb-settings',
            array( $this, 'render_settings_page' )
        );

        add_menu_page(
            __( 'CPB Logs', 'codex-plugin-boilerplate' ),
            __( 'CPB Logs', 'codex-plugin-boilerplate' ),
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
        submit_button( __( 'Save', 'codex-plugin-boilerplate' ) );
        echo '</form>';
        echo '<div id="cpb-feedback"></div><div id="cpb-spinner" class="spinner"></div>';
    }

    private function render_edit_tab() {
        echo '<div id="cpb-entity-list" class="cpb-accordion"></div>';
        echo '<div id="cpb-feedback"></div><div id="cpb-spinner" class="spinner"></div>';
    }

    public function render_settings_page() {
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
        echo '<div class="wrap"><h1>' . esc_html__( 'CPB Settings', 'codex-plugin-boilerplate' ) . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=cpb-settings&tab=general" class="nav-tab ' . ( 'general' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'General Settings', 'codex-plugin-boilerplate' ) . '</a>';
        echo '<a href="?page=cpb-settings&tab=style" class="nav-tab ' . ( 'style' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Style Settings', 'codex-plugin-boilerplate' ) . '</a>';
        echo '</h2>';
        $this->top_message_center();

        if ( 'style' === $active_tab ) {
            $this->render_style_settings_tab();
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
        submit_button( __( 'Save Settings', 'codex-plugin-boilerplate' ) );
        echo '</form>';
        echo '<div id="cpb-feedback"></div><div id="cpb-spinner" class="spinner"></div>';
    }

    private function render_style_settings_tab() {
        echo '<form id="cpb-style-settings-form">';
        echo '<label>' . esc_html__( 'Custom CSS', 'codex-plugin-boilerplate' ) . ' <span class="cpb-tooltip-icon dashicons dashicons-editor-help" data-tooltip="' . esc_attr__( 'Tooltip placeholder text for Custom CSS', 'codex-plugin-boilerplate' ) . '"></span></label>';
        echo '<textarea name="custom_css"></textarea>';
        submit_button( __( 'Save Settings', 'codex-plugin-boilerplate' ) );
        echo '</form>';
        echo '<div id="cpb-feedback"></div><div id="cpb-spinner" class="spinner"></div>';
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
