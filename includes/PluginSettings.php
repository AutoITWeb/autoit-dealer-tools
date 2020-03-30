<?php

use Biltorvet\Controller\ApiController;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

    class BDTSettingsPage
    {
        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;
        private $options_2;
        private $options_3;

        /**
         * Start up
         */
        public function __construct($options, $options_2, $options_3)
        {
            if($options === null)
            {
                throw new Exception( __('No Options specified', 'biltorvet-dealer-tools') );
            }
            $this->options = $options;
            $this->options_2 = $options_2;
            $this->options_3 = $options_3;

            add_action( 'admin_menu', array( $this, 'bdt_add_plugin_page' ) );
            add_action( 'admin_init', array( $this, 'bdt_page_init' ) );
            add_action( 'update_option_bdt_options', array( $this, 'bdt_update_option_cb' ) );
            add_filter( 'wp_dropdown_pages', array( $this, 'bdt_filter_out_home' ) );
        }

        /**
         * Add options page
         */
        public function bdt_add_plugin_page()
        {
            // This page will be under "Settings"
            add_menu_page(
                __('AutoIT Dealer Tools Settings', 'biltorvet-dealer-tools'),
                'AutoIt Dealer Tools',
                'manage_options',
                'autoit-dealer-tools-options',
                array($this, 'bdt_create_admin_page')
            );
        }

        /**
         * Options page callback
         */
        public function bdt_create_admin_page()
        {
            ?>
            <?php
            if( isset( $_GET[ 'tab' ] ) ) {
                $active_tab = $_GET[ 'tab' ];
            } else {
                $active_tab = 'tab_one';
            }
            ?>
            <div class="wrap">
                <h1><?php _e('AutoIT Settings', 'biltorvet-dealer-tools'); ?></h1>
                <?php settings_errors(); ?>

                <?php
                $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'tab_one';
                ?>

                <h2 class="nav-tab-wrapper">
                    <a href="?page=autoit-dealer-tools-options&tab=tab_one" class="nav-tab <?= $active_tab == 'tab_one' ? 'nav-tab-active' : ''; ?>"><?= __( 'General Settings', 'biltorvet-dealer-tools' ) ?></a>
                    <a href="?page=autoit-dealer-tools-options&tab=tab_two" class="nav-tab <?= $active_tab == 'tab_two' ? 'nav-tab-active' : ''; ?>"><?= __( 'Search result settings', 'biltorvet-dealer-tools' ) ?></a>
                    <a href="?page=autoit-dealer-tools-options&tab=tab_three" class="nav-tab <?= $active_tab == 'tab_three' ? 'nav-tab-active' : ''; ?>"><?= __( 'Vehicle details settings', 'biltorvet-dealer-tools' ) ?></a>
                </h2>

                <form method="post" action="options.php">
                    <?php
                    if( $active_tab == 'tab_one' ) {

                        settings_fields( 'bdt-settings-group-1');
                        do_settings_sections( 'bdt-settings-group-1');

                    } else if( $active_tab == 'tab_two' )  {

                        settings_fields( 'bdt-settings-group-2' );
                        do_settings_sections( 'bdt-settings-group-2' );

                    } else if ( $active_tab == 'tab_three') {

                        settings_fields( 'bdt-settings-group-3' );
                        do_settings_sections( 'bdt-settings-group-3' );
                    }

                    ?>

                    <?php submit_button(); ?>
                </form>

            </div>
            <?php
        }

        /**
         * Register and add settings
         */
        public function bdt_page_init()
        {
            add_settings_section(
                'bdt_settings_section_1',
                __( 'General Settings', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_print_section_info' ), // Callback
                'bdt-settings-group-1' // Page
            );

            add_settings_section(
                'bdt_settings_section_2',
                __( 'Search result settings', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_print_section_info_search_results_settings' ), // Callback
                'bdt-settings-group-2' // Page
            );

            add_settings_section(
                'bdt_settings_section_3',
                __( 'Vehicle details settings', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_print_section_info_vehicle_details_settings' ), // Callback
                'bdt-settings-group-3' // Page
            );

            register_setting(
                'bdt-settings-group-1', // Option group
                'bdt_options' // Option name
            );

            register_setting(
                'bdt-settings-group-2', // Option group
                'bdt_options_2' // Option name
            );
            register_setting(
                'bdt-settings-group-3', // Option group
                'bdt_options_3' // Option name
            );

            add_settings_field(
                'api_key',
                __( 'API key', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_api_key_callback' ), // Callback
                'bdt-settings-group-1', // Page
                'bdt_settings_section_1' // Section
            );

            add_settings_field(
                'primary_color',
                __( 'Primary color', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_primary_color_callback' ), // Callback
                'bdt-settings-group-1', // Page
                'bdt_settings_section_1' // Section
            );

            add_settings_field(
                'contact_page',
                __( 'Contact page to receive leads', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_contact_page_callback' ),
                'bdt-settings-group-1', // Page
                'bdt_settings_section_1' // Section
            );

            add_settings_field(
                'booking_page',
                __( 'Booking page to receive leads', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_booking_page_callback' ),
                'bdt-settings-group-1', // Page
                'bdt_settings_section_1' // Section
            );

            add_settings_field(
                'vehiclesearch_page',
                __( 'Vehicle search page', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_vehiclesearch_page_callback' ),
                'bdt-settings-group-1', // Page
                'bdt_settings_section_1' // Section
            );

            add_settings_field(
                'detail_template_page',
                __( 'Vehicle detail template page', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_detail_template_page_callback' ),
                'bdt-settings-group-1', // Page
                'bdt_settings_section_1' // Section
            );

            add_settings_field(
                'bdt_default_sorting_value',
                __( 'Default sorting', 'biltorvet-dealer-tools' ),
                array( $this, 'default_sorting_value_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );



            add_settings_field(
                'adt_email_receipt',
                __( 'Send AutoDesktop receipts by e-mail', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_adt_email_receipt_callback' ),
                'bdt-settings-group-1', // Page
                'bdt_settings_section_1' // Section
            );

            add_settings_field(
                'bdt_hide_leasing_prices_details',
                __( 'Do not show leasing prices on vehicle details', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_leasing_prices_details_callback' ),
                'bdt-settings-group-3', // Page
                'bdt_settings_section_3' // Section
            );

            add_settings_field(
                'bdt_hide_financing_prices_details',
                __( 'Do not show financing prices on vehicle details', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_financing_prices_details_callback' ),
                'bdt-settings-group-3', // Page
                'bdt_settings_section_3' // Section
            );

            // temp field: add option that is always checked to avoid TypeError: Return Value
            add_settings_field(
                'bdt_hidden_field_3',
                '',
                array( $this, 'bdt_hidden_field_3_callback' ),
                'bdt-settings-group-3', // Page
                'bdt_settings_section_3' // Section
            );

            add_settings_field(
                'hide_vehicles_ad',
                sprintf(__( 'Hide %s vehicles', 'biltorvet-dealer-tools' ), 'AutoDesktop'),
                array( $this, 'bdt_hide_ad_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_vehicles_bi',
                sprintf(__( 'Hide %s vehicles', 'biltorvet-dealer-tools' ), 'Bilinfo'),
                array( $this, 'bdt_hide_bi_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_sold_vehicles',
                __( 'Hide sold vehicles', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_sold_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_leasing_vehicles',
                __( 'Hide vehicles with status Leasing', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_leasing_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_flexleasing_vehicles',
                __( 'Hide vehicles with status Flexleasing', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_flexleasing_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );
            add_settings_field(
                'hide_warehousesale_vehicles',
                __( 'Hide vehicles with status Warehousesale', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_warehousesale_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_export_vehicles',
                __( 'Hide vehicles with status Export', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_export_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_rental_vehicles',
                __( 'Hide vehicles with status Rental', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_rental_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_commission_vehicles',
                __( 'Hide vehicles with status Commission', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_commission_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_upcoming_vehicles',
                __( 'Hide vehicles with status Upcoming', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_upcoming_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_wholesale_vehicles',
                __( 'Hide vehicles with status Wholesale', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_wholesale_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_hide_leasing_prices_cards',
                __( 'Do not show leasing prices on vehicle cards', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_leasing_prices_card_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_hide_financing_prices_cards',
                __( 'Do not show financing prices on vehicle cards', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_financing_prices_card_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

        }

        /**
         * Callbacks
         */

        /**
         * options "Generelle indstillinger" tab
         */

        public function bdt_print_section_info()
        {
            print __( 'Enter your AutoIT API key to activate the service. To obtain your own API key, please contact an AutoIt sales representative.', 'biltorvet-dealer-tools' );
        }

        public function bdt_print_section_info_search_results_settings()
        {
            print __( 'Customize the vehicle search page.', 'biltorvet-dealer-tools' );
        }

        public function bdt_print_section_info_vehicle_details_settings()
        {
            print __( 'Customize the vehicle details page.', 'biltorvet-dealer-tools' );
        }

        public function bdt_api_key_callback()
        {
            printf(
                '<input type="text" id="bdt_options" name="bdt_options[api_key]" value="%s" size="40"/>',
                isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key']) : ''
            );
        }

        public function bdt_primary_color_callback()
        {
            printf(
                '<input type="text" id="bdt_options" name="bdt_options[primary_color]" value="%s" />',
                isset( $this->options['primary_color'] ) ? esc_attr( $this->options['primary_color']) : ''
            );
        }

        public function bdt_contact_page_callback()
        {
            wp_dropdown_pages(array(
                'depth'                 => 2,
                'echo'                  => 1,
                'name'                  => 'bdt_options[contact_page_id]',
                'selected'              => isset($this->options['contact_page_id']) ? intval($this->options['contact_page_id']) : 0
            ));
        }
        public function bdt_booking_page_callback()
        {
            wp_dropdown_pages(array(
                'depth'                 => 2,
                'echo'                  => 1,
                'name'                  => 'bdt_options[booking_page_id]',
                'selected'              => isset($this->options['booking_page_id']) ? intval($this->options['booking_page_id']) : 0
            ));
        }

        public function bdt_vehiclesearch_page_callback()
        {
            wp_dropdown_pages(array(
                'depth'                 => 2,
                'echo'                  => 1,
                'name'                  => 'bdt_options[vehiclesearch_page_id]',
                'selected'              => isset($this->options['vehiclesearch_page_id']) ? intval($this->options['vehiclesearch_page_id']) : 0
            ));
        }
        public function bdt_detail_template_page_callback()
        {
            wp_dropdown_pages(array(
                'depth'                 => 2,
                'echo'                  => 1,
                'name'                  => 'bdt_options[detail_template_page_id]',
                'selected'              => isset($this->options['detail_template_page_id']) ? intval($this->options['detail_template_page_id']) : 0
            ));
        }

        public function bdt_adt_email_receipt_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options" value="on" name="bdt_options[adt_email_receipt]"%s />',
                isset( $this->options['adt_email_receipt'] ) && $this->options['adt_email_receipt'] === 'on' ? ' checked="checked"' : ''
            );
        }

        // Refresh the rewrite rules when the detail template page had been changed
        public function bdt_update_option_cb()
        {
            Biltorvet::bdt_rewriterules();
            Biltorvet::bdt_flushrewriterules();
        }

        /**
         * Homepage isn't allowed to be set as a search page for cars, because internal WordPress redirection strips all extra parameters as soon as pagename parameter exuals to whatever is set as the frontpage in add_rewrite_rules. Maybe this can be prevented with template_redirect hook?
         */
        public function bdt_filter_out_home($output, $r = null, $pages = null)
        {
            $currentScreen = get_current_screen();
            $frontpage_id = get_option( 'page_on_front', -1 );
            if($frontpage_id === -1 || $currentScreen->id !== 'settings_page_bdt-settings')
            {
                return $output;
            }
            return preg_replace('/<option (.*)value="' . $frontpage_id . '">(.+)<\/option>/m', '<option $1value="" disabled>Disabled - $2</option>', $output);
        }

        /**
         * Options_2 : "Brugtbilsliste (Bilsøgning)" tab
         */
        public function bdt_hide_ad_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_ad_vehicles]"%s />',
                isset( $this->options_2['hide_ad_vehicles'] ) && $this->options_2['hide_ad_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_bi_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_bi_vehicles]"%s />',
                isset( $this->options_2['hide_bi_vehicles'] ) && $this->options_2['hide_bi_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function default_sorting_value_callback()
        {
            $api = new ApiController();

            $HTML = '<select id="bdt_options_2" value="on" name="bdt_options_2[default_sorting_value]"/>';
            $HTML .= '<option value="-1">None</option>';

            foreach ($api->GetOrderByValues() as $orderByValue) {
                $selected = isset( $this->options_2['default_sorting_value']) && $this->options_2['default_sorting_value'] == $orderByValue;
                $HTML .= '<option value="' . $orderByValue . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $orderByValue . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function bdt_hide_financing_prices_card_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[bdt_hide_financing_prices_card]"%s />',
                isset( $this->options_2['bdt_hide_financing_prices_card'] ) && $this->options_2['bdt_hide_financing_prices_card'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_leasing_prices_card_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[bdt_hide_leasing_prices_card]"%s />',
                isset( $this->options_2['bdt_hide_leasing_prices_card'] ) && $this->options_2['bdt_hide_leasing_prices_card'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_sold_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_sold_vehicles]"%s />',
                isset( $this->options_2['hide_sold_vehicles'] ) && $this->options_2['hide_sold_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_leasing_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_leasing_vehicles]"%s />',
                isset( $this->options_2['hide_leasing_vehicles'] ) && $this->options_2['hide_leasing_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_flexleasing_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_flexleasing_vehicles]"%s />',
                isset( $this->options_2['hide_flexleasing_vehicles'] ) && $this->options_2['hide_flexleasing_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_warehousesale_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_warehousesale_vehicles]"%s />',
                isset( $this->options_2['hide_warehousesale_vehicles'] ) && $this->options_2['hide_warehousesale_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_export_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_export_vehicles]"%s />',
                isset( $this->options_2['hide_export_vehicles'] ) && $this->options_2['hide_export_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_upcoming_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_upcoming_vehicles]"%s />',
                isset( $this->options_2['hide_upcoming_vehicles'] ) && $this->options_2['hide_upcoming_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_rental_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_rental_vehicles]"%s />',
                isset( $this->options_2['hide_rental_vehicles'] ) && $this->options_2['hide_rental_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_commission_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_commission_vehicles]"%s />',
                isset( $this->options_2['hide_commission_vehicles'] ) && $this->options_2['hide_commission_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_wholesale_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_wholesale_vehicles]"%s />',
                isset( $this->options_2['hide_wholesale_vehicles'] ) && $this->options_2['hide_wholesale_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        /**
         * Options_3 : "Bildetaljeside" tab
         */

        public function bdt_hide_leasing_prices_details_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_3" value="on" name="bdt_options_3[bdt_hide_leasing_prices_details]"%s />',
                isset( $this->options_3['bdt_hide_leasing_prices_details'] ) && $this->options_3['bdt_hide_leasing_prices_details'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_financing_prices_details_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_3" value="on" name="bdt_options_3[bdt_hide_financing_prices_details]"%s />',
                isset( $this->options_3['bdt_hide_financing_prices_details'] ) && $this->options_3['bdt_hide_financing_prices_details'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hidden_field_3_callback()
        {
            printf(
                '<input type="hidden" id="bdt_options_3" value="on"  checked="checked" name="bdt_options_3[bdt_hidden_field_3_callback]"%s />',
                isset( $this->options_3['bdt_hidden_field_3_callback'] ) && $this->options_3['bdt_hidden_field_3_callback'] === 'on'
            );
        }
    }
