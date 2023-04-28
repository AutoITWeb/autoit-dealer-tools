<?php

use Biltorvet\Controller\ApiController;
use Biltorvet\Helper\ProductHelper;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

    class BDTSettingsPage
    {
        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;
        private $options_2;
        private $options_3;
        private $options_4;
        private $options_5;
        private $options_6;
        private $biltorvetAPI;

        /**
         * Start up
         */
        public function __construct($options, $options_2, $options_3, $options_4, $options_5, $options_6, $biltorvetAPI)
        {
            if($options === null)
            {
                throw new Exception( __('No Options specified', 'biltorvet-dealer-tools') );
            }
            $this->options = $options;
            $this->options_2 = $options_2;
            $this->options_3 = $options_3;
            $this->options_4 = $options_4;
            $this->options_5 = $options_5;
            $this->options_6 = $options_6;
            $this->biltorvetAPI = $biltorvetAPI;

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
                    <a href="?page=autoit-dealer-tools-options&tab=tab_four" class="nav-tab <?= $active_tab == 'tab_four' ? 'nav-tab-active' : ''; ?>"><?= __( 'Map settings', 'biltorvet-dealer-tools' ) ?></a>
                    <a href="?page=autoit-dealer-tools-options&tab=tab_five" class="nav-tab <?= $active_tab == 'tab_five' ? 'nav-tab-active' : ''; ?>"><?= __( 'Frontpage search', 'biltorvet-dealer-tools' ) ?></a>
                    <a href="?page=autoit-dealer-tools-options&tab=tab_six" class="nav-tab <?= $active_tab == 'tab_six' ? 'nav-tab-active' : ''; ?>"><?= __( 'Departments', 'biltorvet-dealer-tools' ) ?></a>
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

                    } else if ( $active_tab == 'tab_four') {

                        settings_fields( 'bdt-settings-group-4' );
                        do_settings_sections( 'bdt-settings-group-4' );

                    } else if ( $active_tab == 'tab_five') {

                        settings_fields( 'bdt-settings-group-5' );
                        do_settings_sections( 'bdt-settings-group-5' );

                    } else if ( $active_tab == 'tab_six') {

                        settings_fields( 'bdt-settings-group-6' );
                        do_settings_sections( 'bdt-settings-group-6' );
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

            add_settings_section(
                'bdt_settings_section_4',
                __( 'Map settings', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_print_section_info_map_settings' ), // Callback
                'bdt-settings-group-4' // Page
            );

            add_settings_section(
                'bdt_settings_section_5',
                __( 'Frontpage Search settings', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpage_search_settings' ), // Callback
                'bdt-settings-group-5' // Page
            );

            add_settings_section(
                'bdt_settings_section_6',
                __( 'Department settings', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_departments_settings' ), // Callback
                'bdt-settings-group-6' // Page
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
            register_setting(
                'bdt-settings-group-4', // Option group
                'bdt_options_4' // Option name
            );
            register_setting(
                'bdt-settings-group-5', // Option group
                'bdt_options_5' // Option name
            );
            register_setting(
                'bdt-settings-group-6', // Option group
                'bdt_options_6' // Option name
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
                'fulltextsearch_or_quicksearch',
                __( 'Fulltextsearch or quicksearch', 'biltorvet-dealer-tools' ),
                array( $this, 'fulltextsearch_or_quicksearch_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_default_sorting_value',
                __( 'Default sorting', 'biltorvet-dealer-tools' ),
                array( $this, 'default_sorting_value_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_default_sorting_order',
                __( 'Faldende / stigende rækkefølge', 'biltorvet-dealer-tools' ),
                array( $this, 'default_sorting_order_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_vehiclecard_prop_one',
                __( 'Vehiclecard properties column one', 'biltorvet-dealer-tools' ),
                array( $this, 'vehiclecard_prop_one_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_vehiclecard_prop_two',
                __( 'Vehiclecard properties column two', 'biltorvet-dealer-tools' ),
                array( $this, 'vehiclecard_prop_two_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_vehiclecard_prop_three',
                __( 'Vehiclecard properties column three', 'biltorvet-dealer-tools' ),
                array( $this, 'vehiclecard_prop_three_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_leads',
                __( 'How to recieve leads', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_leads_callback' ),
                'bdt-settings-group-1', // Page
                'bdt_settings_section_1' // Section
            );

            /*
            *  This function is depricated but is still show to avoid errors
            */
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

            add_settings_field(
                'bdt_show_thumbnails_details',
                __( 'Show thumbnails', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_show_thumbnails_details_callback' ),
                'bdt-settings-group-3', // Page
                'bdt_settings_section_3' // Section
            );

            add_settings_field(
                'bdt_set_data_scale_details',
                __( 'Set image aspect ratio', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_set_data_scale_details_callback' ),
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
                'carlite_dealer_label',
                __( 'Carlite dealer label', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_carlite_dealer_label_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
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
                'hide_carlite_dealer_label_vehicles',
                __( 'Hide vehicles with status Carlite Dealer Label', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_carlite_dealer_label_vehicles_callback' ), // Callback
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
                'hide_trailer_vehicles',
                __( 'Hide vehicles with status Trailer', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_trailer_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_typecar_vehicles',
                __( 'Hide vehicles with type Car', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_typecar_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_typevan_vehicles',
                __( 'Hide vehicles with type Van', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_typevan_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_typemotorcycle_vehicles',
                __( 'Hide vehicles with type Motorcycle', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_typemotorcycle_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_typetruck_vehicles',
                __( 'Hide vehicles with type Truck', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_typetruck_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_typebus_vehicles',
                __( 'Hide vehicles with type Bus', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_typebus_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'hide_brandnew_vehicles',
                __( 'Hide brandnew vehicles', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_brandnew_vehicles_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_show_all_labels',
                __( 'Show all labels set in Autodesktop', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_show_all_labels_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_hide_leasing_prices_card',
                __( 'Do not show leasing prices on vehicle cards', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_leasing_prices_card_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_hide_financing_prices_card',
                __( 'Do not show financing prices on vehicle cards', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_financing_prices_card_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_hide_cashprices_card',
                __( 'Skjul kontantpriser (bilkort)', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_cashprices_card_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_pricetypes',
                __( 'Choose pricetype to show', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_pricetypes_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'no_price_label',
                __( 'Sæt label tekst ved manglende kontantpris' ),
                array( $this, 'bdt_no_price_label_callback' ), // Callback
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_prioritized_price',
                __( 'Vælg prisprioritering'),
                array( $this, 'bdt_prioritized_price_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_hide_secondary_price',
                __( 'Skjul sekundær og tertiær pris', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_secondary_price_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'vehiclesearch_icon_based_search',
                __( 'Activate iconbased search', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_vehiclesearch_icon_based_search_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'frontpagesearch_iconbased_search_icon_color',
                __( 'Icon color', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_vehiclesearch_icon_based_search_icon_color_callback' ),
                'bdt-settings-group-2', // Page
                'bdt_settings_section_2' // Section
            );

            add_settings_field(
                'bdt_activate_map',
                __( 'Activate map', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_activate_map_callback' ),
                'bdt-settings-group-4', // Page
                'bdt_settings_section_4' // Section
            );

            add_settings_field(
                'bdt_set_view',
                __( 'Set view (latitude and longitude)', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_set_view_callback' ),
                'bdt-settings-group-4', // Page
                'bdt_settings_section_4' // Section
            );

            add_settings_field(
                'bdt_zoom_level',
                __( 'Set zoom level for global map', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_zoom_level_callback' ),
                'bdt-settings-group-4', // Page
                'bdt_settings_section_4' // Section
            );
            add_settings_field(
                'bdt_zoom_level_detailspage',
                __( 'Set zoom level for map on detailspage', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_zoom_level_detailspage_callback' ),
                'bdt-settings-group-4', // Page
                'bdt_settings_section_4' // Section
            );
            add_settings_field(
                'bdt_tile_layer',
                __( 'Set tile layer', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_tile_layer_callback' ),
                'bdt-settings-group-4', // Page
                'bdt_settings_section_4' // Section
            );

            add_settings_field(
                'bdt_marker_color',
                __( 'Marker color', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_marker_color_callback' ),
                'bdt-settings-group-4', // Page
                'bdt_settings_section_4' // Section
            );
            add_settings_field(
                'bdt_custom_marker',
                __( 'Custom marker URL', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_custom_marker_callback' ),
                'bdt-settings-group-4', // Page
                'bdt_settings_section_4' // Section
            );

            /**
             * Frontpage Seach setting fields
             */

            add_settings_field(
                'bdt_set_frontpagesearch_column',
                __( 'Set column size', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_setcolumn_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_fulltextsearch',
                __( 'Activate fulltextsearch', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_fulltextsearch_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_quicksearch',
                __( 'Activate quicksearch', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_quicksearch_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_company',
                __( 'Activate companies', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_company_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_vehiclestate',
                __( 'Activate vehiclestates', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_vehiclestate_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_makemodel',
                __( 'Activate makes and models', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_makemodel_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_producttype',
                __( 'Activate product types', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_producttype_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_pricetype',
                __( 'Activate price types', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_pricetype_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_bodytype',
                __( 'Activate body types', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_bodytype_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_propellants',
                __( 'Activate propellants', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_propellants_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_pricerange',
                __( 'Activate price range', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_pricerange_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_fuelconsumption',
                __( 'Activate fuel consumption', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_fuelconsumption_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_icon_based_search',
                __( 'Activate iconbased search', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_icon_based_search_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            add_settings_field(
                'frontpagesearch_iconbased_search_icon_color',
                __( 'Icon color', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_icon_based_search_icon_color_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );


            add_settings_field(
                'frontpagesearch_iconbased_search_background_color',
                __( 'Background color', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_frontpagesearch_icon_based_search_background_color_callback' ),
                'bdt-settings-group-5', // Page
                'bdt_settings_section_5' // Section
            );

            /**
             * Deparment setting fields
             */

            add_settings_field(
                'departments_list',
                __( 'Departments', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_departments_list_callback' ),
                'bdt-settings-group-6', // Page
                'bdt_settings_section_6' // Section
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

        public function bdt_print_section_info_map_settings()
        {
            print __( 'Customize the map', 'biltorvet-dealer-tools' );
        }

        public function bdt_frontpage_search_settings()
        {
            print __( 'Customize frontpage search', 'biltorvet-dealer-tools' );
        }

        public function bdt_departments_settings()
        {
            print __( 'Department information for use with a department dropdown selector in Divi Contact Form.', 'biltorvet-dealer-tools' );
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

        public function bdt_leads_callback()
        {
            $api = new ApiController();

            $leadOptions = ["Autodesktop" => 0, "Mail" => 1, "Autodesktop & mail" => 2];

            $companyProducts = $api->getCompanyProducts();

            if($companyProducts !== null)
            {
                try{
                    if(!ProductHelper::hasAccess("External User", $companyProducts) && ProductHelper::hasAccess("Leads to ADT", $companyProducts)) {

                        $HTML = '<select id="bdt_options" value="on" name="bdt_options[bdt_leads]"/>';
                        $HTML .= '<option value="-1">Vælg hvor leads skal sendes hen</option>';

                        foreach($leadOptions as $key => $value) {
                            $selected = isset( $this->options['bdt_leads']) && $this->options['bdt_leads'] == $value;
                            $HTML .= '<option value="' . $value . '"';
                            $HTML .= $selected ? 'selected="selected"' : '';
                            $HTML .= '>' . $key . '</option>';
                        }

                        $HTML .= '</select>';

                        echo $HTML;

                    } else {
                        echo "<br>I kan kun modtage leads på mail. <br>Kontakt <a ahref='mail:web@autoit.dk'>AutoIt</a> for at få kunne sende leads til Autodesktop og mail";
                    }
                }
                catch (Exception $ex)
                {
                    echo "<br>I kan kun modtage leads på mail. <br>Kontakt <a ahref='mail:web@autoit.dk'>AutoIt</a> for at få kunne sende leads til Autodesktop og mail";
                }
            }
        }
        /*
         *  This function is depricated but is still has to be shown to avoid errors
         */

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

        public function fulltextsearch_or_quicksearch_callback()
        {
            $searchOptions = ["Fritekstsøgning" => 0, "Hurtigsøgning" => 1];

            $HTML = '<select id="bdt_options_2" name="bdt_options_2[fulltextsearch_or_quicksearch]"/>';

            foreach($searchOptions as $key => $value) {
                $selected = isset( $this->options_2['fulltextsearch_or_quicksearch']) && $this->options_2['fulltextsearch_or_quicksearch'] == $value;
                $HTML .= '<option value="' . $value . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $key . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function default_sorting_value_callback()
        {
            $api = new ApiController();

            $HTML = '<select id="bdt_options_2" value="on" name="bdt_options_2[default_sorting_value]"/>';
            $HTML .= '<option value="-1">Default</option>';

            foreach ($api->GetOrderByValues() as $orderByValue) {
                $selected = isset( $this->options_2['default_sorting_value']) && $this->options_2['default_sorting_value'] == $orderByValue;
                $HTML .= '<option value="' . $orderByValue . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $orderByValue . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function default_sorting_order_callback()
        {
            $sortOrderOptions = array("Descending", "Ascending");

            $HTML = '<select id="bdt_options_2" value="on" name="bdt_options_2[default_sorting_order]"/>';
            $HTML .= '<option value="-1">Default</option>';

            foreach ($sortOrderOptions as $sortOrderOption) {
                $selected = isset( $this->options_2['default_sorting_order']) && $this->options_2['default_sorting_order'] == $sortOrderOption;
                $HTML .= '<option value="' . $sortOrderOption . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $sortOrderOption . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function vehiclecard_prop_one_callback()
        {
            $api = new ApiController();

            $HTML = '<select id="bdt_options_2" value="on" name="bdt_options_2[vehiclecard_prop_one]"/>';
            $HTML .= '<option value="-1">Default</option>';

            foreach ($api->vehiclCardProperties() as $key=>$value) {
                $selected = isset( $this->options_2['vehiclecard_prop_one']) && $this->options_2['vehiclecard_prop_one'] == $key;
                $HTML .= '<option value="' . $key . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $value . '</option>';
            }
            echo $HTML;
        }

        public function vehiclecard_prop_two_callback()
        {
            $api = new ApiController();

            $HTML = '<select id="bdt_options_2" value="on" name="bdt_options_2[vehiclecard_prop_two]"/>';
            $HTML .= '<option value="-1">Default</option>';

            foreach ($api->vehiclCardProperties() as $key=>$value) {
                $selected = isset( $this->options_2['vehiclecard_prop_two']) && $this->options_2['vehiclecard_prop_two'] == $key;
                $HTML .= '<option value="' . $key . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $value . '</option>';
            }
            echo $HTML;
        }

        public function vehiclecard_prop_three_callback()
        {
            $api = new ApiController();

            $HTML = '<select id="bdt_options_2" value="on" name="bdt_options_2[vehiclecard_prop_three]"/>';
            $HTML .= '<option value="-1">Default</option>';

            foreach ($api->vehiclCardProperties() as $key=>$value) {
                $selected = isset( $this->options_2['vehiclecard_prop_three']) && $this->options_2['vehiclecard_prop_three'] == $key;
                $HTML .= '<option value="' . $key . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $value . '</option>';
            }
            echo $HTML;
        }

        public function bdt_carlite_dealer_label_callback()
        {
            printf(
                '<input type="text" id="bdt_options_2" name="bdt_options_2[carlite_dealer_label]" value="%s" size="20"/>',
                isset( $this->options_2['carlite_dealer_label'] ) ? esc_attr( $this->options_2['carlite_dealer_label']) : ''
            );
        }

        public function bdt_hide_financing_prices_card_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[bdt_hide_financing_prices_card]"%s />',
                isset( $this->options_2['bdt_hide_financing_prices_card'] ) && $this->options_2['bdt_hide_financing_prices_card'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_cashprices_card_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[bdt_hide_cashprices_card]"%s />',
                isset( $this->options_2['bdt_hide_cashprices_card'] ) && $this->options_2['bdt_hide_cashprices_card'] === 'on' ? ' checked="checked"' : ''
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

        public function bdt_hide_carlite_dealer_label_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_carlite_dealer_label_vehicles]"%s />',
                isset( $this->options_2['hide_carlite_dealer_label_vehicles'] ) && $this->options_2['hide_carlite_dealer_label_vehicles'] === 'on' ? ' checked="checked"' : ''
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

        public function bdt_hide_trailer_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_trailer_vehicles]"%s />',
                isset( $this->options_2['hide_trailer_vehicles'] ) && $this->options_2['hide_trailer_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_typecar_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_typecar_vehicles]"%s />',
                isset( $this->options_2['hide_typecar_vehicles'] ) && $this->options_2['hide_typecar_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_typevan_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_typevan_vehicles]"%s />',
                isset( $this->options_2['hide_typevan_vehicles'] ) && $this->options_2['hide_typevan_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_typemotorcycle_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_typemotorcycle_vehicles]"%s />',
                isset( $this->options_2['hide_typemotorcycle_vehicles'] ) && $this->options_2['hide_typemotorcycle_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_typetruck_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_typetruck_vehicles]"%s />',
                isset( $this->options_2['hide_typetruck_vehicles'] ) && $this->options_2['hide_typetruck_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_typebus_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_typebus_vehicles]"%s />',
                isset( $this->options_2['hide_typebus_vehicles'] ) && $this->options_2['hide_typebus_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_brandnew_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[hide_brandnew_vehicles]"%s />',
                isset( $this->options_2['hide_brandnew_vehicles'] ) && $this->options_2['hide_brandnew_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_show_all_labels_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[show_all_labels]"%s />',
                isset( $this->options_2['show_all_labels'] ) && $this->options_2['show_all_labels'] === 'on' ? ' checked="checked"' : ''
            );
        }



        // Sets the filter "PriceTypes"
        public function bdt_pricetypes_callback()
        {
            $priceTypeOptions = ["Kontantpris" => "Kontant", "Finansieringspris" => "Finansiering", "Leasingpris" => "Leasing"];

            $HTML = '<select id="bdt_options_2" value="on" name="bdt_options_2[bdt_pricetypes]"/>';
            $HTML .= '<option value="-1">Vælg pristype</option>';

            foreach ($priceTypeOptions as $key => $value) {
                $selected = isset($this->options_2['bdt_pricetypes']) && $this->options_2['bdt_pricetypes'] == $value;
                $HTML .= '<option value="' . $value . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $key . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function bdt_no_price_label_callback()
        {
            printf(
                '<input type="text" id="bdt_options_2" name="bdt_options_2[bdt_no_price_label]" value="%s" size="30"/>',
                isset( $this->options_2['bdt_no_price_label'] ) ? esc_attr( $this->options_2['bdt_no_price_label']) : ''
            );
        }

        // Hides / shows the secondary price
        public function bdt_hide_secondary_price_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[bdt_hide_secondary_price]"%s />',
                isset( $this->options_2['bdt_hide_secondary_price'] ) && $this->options_2['bdt_hide_secondary_price'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_prioritized_price_callback()
        {
            $priceTypeOptions = ["Kontantpris" => "Kontant", "Finansieringspris" => "Finansiering", "Leasingpris" => "Leasing"];

            $HTML = '<select id="bdt_options_2" value="on" name="bdt_options_2[bdt_prioritized_price]"/>';
            $HTML .= '<option value="-1">Vælg prioritering</option>';

            foreach ($priceTypeOptions as $key => $value) {
                $selected = isset($this->options_2['bdt_prioritized_price']) && $this->options_2['bdt_prioritized_price'] == $value;
                $HTML .= '<option value="' . $value . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $key . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function bdt_vehiclesearch_icon_based_search_callback()
        {
            $headerText = "<p><b>Ikonbaseret søgning (Custom Vehicle Types)</b></p>";
            $subheaderText = "<p>Viser søgeikoner baseret på de typer køretøjer der er til salg.</p><br>";
            $subheaderText .= "<b>Bemærk: Der vises kun ikoner, hvis der er typer der passer i de enkelte kategorier</b></p><br>";

            $header = $headerText . $subheaderText;

            echo $header;

            printf(
                'Aktivér <input type="checkbox" id="bdt_options_2" value="on" name="bdt_options_2[vehiclesearch_activate_iconbased_search]"%s />',
                isset( $this->options_2['vehiclesearch_activate_iconbased_search'] ) && $this->options_2['vehiclesearch_activate_iconbased_search'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_vehiclesearch_icon_based_search_icon_color_callback()
        {
            printf(
                '<input type="text" id="bdt_options_2" name="bdt_options_2[frontpagesearch_iconbased_search_icon_color]" size="30" value="%s" style="margin-bottom: 5px;" placeholder="Skal indeholde # og 6 tegn"/>',
                isset( $this->options_2['frontpagesearch_iconbased_search_icon_color'] ) ? esc_attr($this->options_2['frontpagesearch_iconbased_search_icon_color']) : ''
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

        public function bdt_show_thumbnails_details_callback()
        {
            echo 'Skal der vises thumbnails i slideshowet på bildetaljen?<br>';
            printf(
                '<input type="checkbox" id="bdt_options_3" value="on" name="bdt_options_3[bdt_show_thumbnails_details]"%s />',
                isset( $this->options_3['bdt_show_thumbnails_details'] ) && $this->options_3['bdt_show_thumbnails_details'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_set_data_scale_details_callback()
        {
            $ratios = array(
                "16:9",
            );

            $HTML = '<select id="bdt_options_3" value="on" name="bdt_options_3[bdt_set_data_scale_details]"/>';
            $HTML .= '<option value="4:3">4:3</option>';

            foreach ( $ratios as $ratio) {
                $selected = isset( $this->options_3['bdt_set_data_scale_details']) && $this->options_3['bdt_set_data_scale_details'] == $ratio;
                $HTML .= '<option value="' . $ratio . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $ratio . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function bdt_hidden_field_3_callback()
        {
            printf(
                '<input type="hidden" id="bdt_options_3" value="on"  checked="checked" name="bdt_options_3[bdt_hidden_field_3_callback]"%s />',
                isset( $this->options_3['bdt_hidden_field_3_callback'] ) && $this->options_3['bdt_hidden_field_3_callback'] === 'on'
            );
        }

        /**
         * Options_4 : "Indstillinger for kort" tab
         */

        public function bdt_activate_map_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_4" value="on" name="bdt_options_4[activate_map]"%s />',
                isset( $this->options_4['activate_map'] ) && $this->options_4['activate_map'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_set_view_callback()
        {
            printf(
                '<input type="text" id="bdt_options_4" name="bdt_options_4[bdt_set_view]" value="%s" size="30" placeholder="Latitude, longitude" />',
                isset( $this->options_4['bdt_set_view'] ) ? esc_attr($this->options_4['bdt_set_view']) : ''
            );
        }

        public function bdt_zoom_level_callback()
        {
            printf(
                '<input type="text" id="bdt_options_4" name="bdt_options_4[bdt_zoom_level]" value="%s" size="5" />',
                isset( $this->options_4['bdt_zoom_level'] ) ? esc_attr($this->options_4['bdt_zoom_level']) : ''
            );
        }

        public function bdt_zoom_level_detailspage_callback()
        {
            printf(
                '<input type="text" id="bdt_options_4" name="bdt_options_4[bdt_zoom_level_detailspage]" value="%s" size="5" />',
                isset( $this->options_4['bdt_zoom_level_detailspage'] ) ? esc_attr($this->options_4['bdt_zoom_level_detailspage']) : ''
            );
        }

        public function bdt_tile_layer_callback()
        {
            printf(
                '<input type="text" id="bdt_options_4" name="bdt_options_4[bdt_tile_layer]" value="%s" size="65" />',
                isset( $this->options_4['bdt_tile_layer'] ) ? esc_attr($this->options_4['bdt_tile_layer']) : ''
            );
        }

        public function bdt_marker_color_callback()
        {
            $markerColors = array(
                "Green",
                "Blue",
                "Gold",
                "Orange",
                "Yellow",
                "Violet",
                "Grey",
                "Black"
            );

            $HTML = '<select id="bdt_options_4" value="on" name="bdt_options_4[bdt_marker_color]"/>';
            $HTML .= '<option value="Red">Red</option>';

            foreach ( $markerColors as $colors) {
                $selected = isset( $this->options_4['bdt_marker_color']) && $this->options_4['bdt_marker_color'] == $colors;
                $HTML .= '<option value="' . $colors . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $colors . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function bdt_custom_marker_callback()
        {
            printf(
                '<input type="text" id="bdt_options_4" name="bdt_options_4[bdt_custom_marker]" value="%s" size="65" />',
                isset( $this->options_4['bdt_custom_marker'] ) ? esc_attr($this->options_4['bdt_custom_marker']) : ''
            );
        }

        /*
         * options "Forside søgning" tab
         */

        public function  bdt_frontpagesearch_setcolumn_callback()
        {
            $columsSizes = array("3", "4");

            $HTML = '<select id="bdt_options_5" value="on" name="bdt_options_5[set_frontpagesearch_column]"/>';
            $HTML .= '<option value="Default">Default</option>';

            foreach ($columsSizes as $sizes) {
                $selected = isset( $this->options_5['set_frontpagesearch_column']) && $this->options_5['set_frontpagesearch_column'] == $sizes;
                $HTML .= '<option value="' . $sizes . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $sizes . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function bdt_frontpagesearch_fulltextsearch_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_fulltextsearch]"%s />',
                isset( $this->options_5['frontpagesearch_fulltextsearch'] ) && $this->options_5['frontpagesearch_fulltextsearch'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_quicksearch_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_quicksearch]"%s />',
                isset( $this->options_5['frontpagesearch_quicksearch'] ) && $this->options_5['frontpagesearch_quicksearch'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_company_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_company]"%s />',
                isset( $this->options_5['frontpagesearch_company'] ) && $this->options_5['frontpagesearch_company'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_vehiclestate_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_vehiclestate]"%s />',
                isset( $this->options_5['frontpagesearch_vehiclestate'] ) && $this->options_5['frontpagesearch_vehiclestate'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_makemodel_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_makemodel]"%s />',
                isset( $this->options_5['frontpagesearch_makemodel'] ) && $this->options_5['frontpagesearch_makemodel'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_pricetype_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_pricetype]"%s />',
                isset( $this->options_5['frontpagesearch_pricetype'] ) && $this->options_5['frontpagesearch_pricetype'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_producttype_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_producttype]"%s />',
                isset( $this->options_5['frontpagesearch_producttype'] ) && $this->options_5['frontpagesearch_producttype'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_bodytype_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_bodytype]"%s />',
                isset( $this->options_5['frontpagesearch_bodytype'] ) && $this->options_5['frontpagesearch_bodytype'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_propellants_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_propellants]"%s />',
                isset( $this->options_5['frontpagesearch_propellants'] ) && $this->options_5['frontpagesearch_propellants'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_pricerange_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_pricerange]"%s />',
                isset( $this->options_5['frontpagesearch_pricerange'] ) && $this->options_5['frontpagesearch_pricerange'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_fuelconsumption_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_fuelconsumption]"%s />',
                isset( $this->options_5['frontpagesearch_fuelconsumption'] ) && $this->options_5['frontpagesearch_fuelconsumption'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_icon_based_search_callback()
        {
            $headerText = "<p><b>Ikonbaseret søgning (Custom Vehicle Types)</b></p>";
            $subheaderText = "<p>Viser søgeikoner baseret på de typer køretøjer der er til salg.</p><br>";
            $subheaderText .= "<b>Bemærk: Der vises kun ikoner, hvis der er typer der passer i de enkelte kategorier</b></p><br>";

            $header = $headerText . $subheaderText;

            echo $header;

            printf(
                'Aktivér <input type="checkbox" id="bdt_options_5" value="on" name="bdt_options_5[frontpagesearch_activate_iconbased_search]"%s />',
                isset( $this->options_5['frontpagesearch_activate_iconbased_search'] ) && $this->options_5['frontpagesearch_activate_iconbased_search'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_frontpagesearch_icon_based_search_icon_color_callback()
        {
            printf(
                '<input type="text" id="bdt_options_5" name="bdt_options_5[frontpagesearch_iconbased_search_icon_color]" size="30" value="%s" style="margin-bottom: 5px;" placeholder="Skal indeholde # og 6 tegn"/>',
                isset( $this->options_5['frontpagesearch_iconbased_search_icon_color'] ) ? esc_attr($this->options_5['frontpagesearch_iconbased_search_icon_color']) : ''
            );
        }

        public function bdt_frontpagesearch_icon_based_search_background_color_callback()
        {
            printf(
                '<input type="text" id="bdt_options_5" name="bdt_options_5[frontpagesearch_iconbased_search_background_color]" size="30" value="%s" style="margin-bottom: 5px;" placeholder="Skal indeholde # og 6 tegn"/>',
                isset( $this->options_5['frontpagesearch_iconbased_search_background_color'] ) ? esc_attr($this->options_5['frontpagesearch_iconbased_search_background_color']) : ''
            );
        }

        /*
        * options Departments tab
        */

        public function bdt_departments_list_callback()
        {
            $companiesFeed = $this->biltorvetAPI->GetCompanies();

            echo '<b>Afdelingsvælger:</b><br>';
            echo 'Brug disse navne som valgmuligheder i dropdown afdelingsvælgeren. Det er vigtigt at navne er helt ens.<br>';
            echo 'Indtast den eller de email adresser der skal sendes mails til, når man vælger afdelingen. Der kan indsættes lige så mange som man ønsker (indtast alle email adresser kun adskilt af , og uden mellemrum ). Eks.: <b>test@autoit.dk,test@biltorvet.dk</b><br><br>';

            echo '<b>Google Maps rutevejledning:</b><br>';
            echo 'Indsæt link til Google Maps rutevejledning for at vise det på kortet.<br><br>';

            $i = 0;

            foreach($companiesFeed->companies as $company)
            {
                echo '<input type="text" id="bdt_options_6" name="bdt_options_5[departments_company_name]" value="' . $company->name . '" size="30" readonly style="margin-bottom: 5px; background: #A8A8A8; "/><br>';
                echo '<input type="text" id="bdt_options_6" name="bdt_options_5[departments_company_name]" value="' . $company->address . ' ' . $company->city . '" size="30" readonly style="margin-bottom: 5px; background: #A8A8A8; "/><br>';

                printf(
                    '<input type="text" id="bdt_options_6" name="bdt_options_6[departments_company_id_' . $i . ']" value="' . $company->id .'" size="30" readonly style="margin-bottom: 5px; background: #A8A8A8; "/>',
                    isset( $this->options_6['departments_company_id_' . $i] ) ? esc_attr($this->options_6['departments_company_id_' . $i]) : ''
                );

                echo '<br>';

                printf(
                    '<input type="text" id="bdt_options_6" name="bdt_options_6[departments_company_email_' . $company->id . ']" value="%s" size="30" placeholder="Indtast email / emails for afdeling" style="margin-bottom: 5px;" />',
                    isset( $this->options_6['departments_company_email_' . $company->id] ) ? esc_attr($this->options_6['departments_company_email_' . $company->id]) : ''
                );

                echo '<br>';

                printf(
                    '<input type="text" id="bdt_options_6" name="bdt_options_6[departments_google_directions_' . $company->id . ']" value="%s" size="30" placeholder="Link til Google rutevejledning" style="margin-bottom: 5px;" />',
                    isset( $this->options_6['departments_google_directions_' . $company->id] ) ? esc_attr($this->options_6['departments_google_directions_' . $company->id]) : ''
                );

                $i ++;

                echo '<br><br>';
            }
        }
    }
