<?php
///*    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly
//
//    class BDTSettingsPage
//    {
//        /**
//         * Holds the values to be used in the fields callbacks
//         */
//        private $options;
//
//        /**
//         * Start up
//         */
//        public function __construct()
//        {
//            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
//            add_action( 'admin_init', array( $this, 'page_init' ) );
//            add_action( 'update_option_bdt_detail_template_page_id', array( $this, 'update_option_bdt_detail_template_page_id_cb' ));
//        }
//
//        /**
//         * Add options page
//         */
//        public function add_plugin_page()
//        {
//            // This page will be under "Settings"
//            add_options_page(
//                __( 'Biltorvet Settings', 'biltorvet-dealer-tools' ),
//                'Biltorvet',
//                'manage_options',
//                'bdt-settings',
//                array( $this, 'create_admin_page' )
//            );
//        }
//
//        /**
//         * Options page callback
//         */
//        public function create_admin_page()
//        {
//            // Set class property
//            $this->options = get_option( 'bdt_options' );
//            */?><!--<!---->
<!--            <div class="wrap">-->
<!--                <h1>--><?php ///*_e('Biltorvet Settings', 'biltorvet-dealer-tools'); */?><!--</h1>-->
<!--                <form method="post" action="options.php">-->
<!--                --><?php
///*                    // This prints out all hidden setting fields
//                    settings_fields( 'bdt_settings_group' );
//                    do_settings_sections( 'bdt-settings' );
//                    submit_button();
//                */?>
<!--                </form>-->
<!--            </div>-->
<!--            -->--><?php
///*        }
//
//        /**
//         * Register and add settings
//         */
//        public function page_init()
//        {
//            register_setting(
//                'bdt_settings_group', // Option group
//                'bdt_options' // Option name
//            );
//
//            add_settings_section(
//                'bdt_settings_section',
//                __( 'Plugin Settings', 'biltorvet-dealer-tools' ),
//                array( $this, 'print_section_info' ), // Callback
//                'bdt-settings' // Page
//            );
//
//            add_settings_field(
//                'api_key',
//                __( 'API key', 'biltorvet-dealer-tools' ),
//                array( $this, 'api_key_callback' ), // Callback
//                'bdt-settings', // Page
//                'bdt_settings_section' // Section
//            );
//
//            add_settings_field(
//                'contact_page',
//                __( 'Contact page to receive leads', 'biltorvet-dealer-tools' ),
//                array( $this, 'contact_page_callback' ),
//                'bdt-settings',
//                'bdt_settings_section'
//            );
//
//            add_settings_field(
//                'detail_template_page',
//                __( 'Vehicle detail template page', 'biltorvet-dealer-tools' ),
//                array( $this, 'template_page_callback' ),
//                'bdt-settings',
//                'bdt_settings_section'
//            );
//        }
//
//        /**
//         * Print the Section text
//         */
//        public function print_section_info()
//        {
//            print __( 'Enter your Biltorvet API key to activate the service. To obtain your own API key, please contact a Biltorvet sales representative.', 'biltorvet-dealer-tools' );
//        }
//
//        public function api_key_callback()
//        {
//            printf(
//                '<input type="text" id="bdt_options" name="bdt_options[api_key]" value="%s" />',
//                isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key']) : ''
//            );
//        }
//
//        public function contact_page_callback()
//        {
//            wp_dropdown_pages(array(
//                'depth'                 => 1,
//                'echo'                  => 1,
//                'name'                  => 'bdt_options[contact_page_id]',
//                'selected'              => isset($this->options['contact_page_id']) ? intval($this->options['contact_page_id']) : 0
//            ));
//        }
//        public function template_page_callback()
//        {
//            wp_dropdown_pages(array(
//                'depth'                 => 1,
//                'echo'                  => 1,
//                'name'                  => 'bdt_options[detail_template_page_id]',
//                'selected'              => isset($this->options['detail_template_page_id']) ? intval($this->options['detail_template_page_id']) : 0
//            ));
//        }
//
//        // Refresh the rewrite rules when the detail template page had been changed
//        public function update_option_bdt_detail_template_page_id_cb($oldVal = null, $newVal = null)
//        {
//            bdt_rewriterules();
//            flush_rewrite_rules();
//        }
//    }
//
//    if( is_admin() )
//        new BDTSettingsPage();*/
