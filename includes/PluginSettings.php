<?php

use Biltorvet\Controller\ApiController;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

    class BDTSettingsPage
    {
        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;

        /**
         * Start up
         */
        public function __construct($options)
        {
            if($options === null)
            {
                throw new Exception( __('No Options specified', 'biltorvet-dealer-tools') );
            }
            $this->options = $options;

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
            add_options_page(
                __( 'Biltorvet Settings', 'biltorvet-dealer-tools' ), 
                'Biltorvet', 
                'manage_options', 
                'bdt-settings', 
                array( $this, 'bdt_create_admin_page' )
            );
        }

        /**
         * Options page callback
         */
        public function bdt_create_admin_page()
        {
            ?>

            <div class="wrap">
                <h1><?php _e('Biltorvet Settings', 'biltorvet-dealer-tools'); ?></h1>
                <form method="post" action="options.php">
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'bdt_settings_group' );
                    do_settings_sections( 'bdt-settings' );
                    submit_button();
                ?>
                </form>
            </div>
            <?php
        }

        /**
         * Register and add settings
         */
        public function bdt_page_init()
        {        
            register_setting(
                'bdt_settings_group', // Option group
                'bdt_options' // Option name
            );

            add_settings_section(
                'bdt_settings_section',
                __( 'Plugin Settings', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_print_section_info' ), // Callback
                'bdt-settings' // Page
            );  

            add_settings_field(
                'api_key',
                __( 'API key', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_api_key_callback' ), // Callback
                'bdt-settings', // Page
                'bdt_settings_section' // Section           
            );   

            add_settings_field(
                'primary_color',
                __( 'Primary color', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_primary_color_callback' ), // Callback
                'bdt-settings', // Page
                'bdt_settings_section' // Section           
            );    

            add_settings_field(
                'hide_vehicles_ad',
                sprintf(__( 'Hide %s vehicles', 'biltorvet-dealer-tools' ), 'AutoDesktop'),
                array( $this, 'bdt_hide_ad_vehicles_callback' ), // Callback
                'bdt-settings', // Page
                'bdt_settings_section' // Section           
            );  
            
            add_settings_field(
                'hide_vehicles_bi',
                sprintf(__( 'Hide %s vehicles', 'biltorvet-dealer-tools' ), 'Bilinfo'),
                array( $this, 'bdt_hide_bi_vehicles_callback' ), // Callback
                'bdt-settings', // Page
                'bdt_settings_section' // Section           
            );  

            add_settings_field(
                'hide_sold_vehicles',
                __( 'Hide sold vehicles', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_sold_vehicles_callback' ), // Callback
                'bdt-settings', // Page
                'bdt_settings_section' // Section           
            );    

            add_settings_field(
                'contact_page',
                __( 'Contact page to receive leads', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_contact_page_callback' ), 
                'bdt-settings', 
                'bdt_settings_section'
            );      

            add_settings_field(
                'booking_page',
                __( 'Booking page (with date and time picker) to receive leads', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_booking_page_callback' ), 
                'bdt-settings', 
                'bdt_settings_section'
            );        

            add_settings_field(
                'vehiclesearch_page',
                __( 'Vehicle search page', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_vehiclesearch_page_callback' ), 
                'bdt-settings', 
                'bdt_settings_section'
            );    

            // add_settings_field(
            //     'make_template_page',
            //     __( 'Make template page', 'biltorvet-dealer-tools' ),
            //     array( $this, 'bdt_make_template_page_callback' ), 
            //     'bdt-settings', 
            //     'bdt_settings_section'
            // );   

            add_settings_field(
                'detail_template_page',
                __( 'Vehicle detail template page', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_detail_template_page_callback' ), 
                'bdt-settings', 
                'bdt_settings_section'
            );

            add_settings_field(
                'bdt_default_sorting_value',
                __( 'Default sorting', 'biltorvet-dealer-tools' ),
                array( $this, 'default_sorting_value_callback' ),
                'bdt-settings',
                'bdt_settings_section'
            );

            add_settings_field(
                'bdt_asc_sorting_value',
                __('Order by ascending (Default is descending) ', 'biltorvet-dealer-tools' ),
                array( $this, 'ascending_value_callback' ),
                'bdt-settings',
                'bdt_settings_section'
            );

            add_settings_field(
                'adt_email_receipt',
                __( 'Send AutoDesktop receipts by e-mail', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_adt_email_receipt_callback' ), 
                'bdt-settings', 
                'bdt_settings_section'
            );

            add_settings_field(
            'bdt_hide_leasing_prices_cards',
            __( 'Do not show leasing prices on vehicle cards', 'biltorvet-dealer-tools' ),
            array( $this, 'bdt_hide_leasing_prices_card_callback' ),
            'bdt-settings',
            'bdt_settings_section'
            );

            add_settings_field(
                'bdt_hide_financing_prices_cards',
                __( 'Do not show financing prices on vehicle cards', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_financing_prices_card_callback' ),
                'bdt-settings',
                'bdt_settings_section'
            );

            add_settings_field(
                'bdt_hide_leasing_prices_details',
                __( 'Do not show leasing prices on vehicle details', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_leasing_prices_details_callback' ),
                'bdt-settings',
                'bdt_settings_section'
            );

            add_settings_field(
                'bdt_hide_financing_prices_details',
                __( 'Do not show financing prices on vehicle details', 'biltorvet-dealer-tools' ),
                array( $this, 'bdt_hide_financing_prices_details_callback' ),
                'bdt-settings',
                'bdt_settings_section'
            );

        }

        /** 
         * Print the Section text
         */

        public function default_sorting_value_callback()
        {
            $api = new ApiController();

            $HTML = '<select id="bdt_options" value="on" name="bdt_options[default_sorting_value]"/>';
            $HTML .= '<option value="-1">None</option>';

            foreach ($api->GetOrderByValues() as $orderByValue) {
                $selected = isset( $this->options['default_sorting_value']) && $this->options['default_sorting_value'] == $orderByValue;
                $HTML .= '<option value="' . $orderByValue . '"';
                $HTML .= $selected ? 'selected="selected"' : '';
                $HTML .= '>' . $orderByValue . '</option>';
            }

            $HTML .= '</select>';

            echo $HTML;
        }

        public function ascending_value_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options" value="on" name="bdt_options[bdt_asc_sorting_value]"%s />',
                isset( $this->options['bdt_asc_sorting_value'] ) && $this->options['bdt_asc_sorting_value'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_financing_prices_details_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options" value="on" name="bdt_options[bdt_hide_financing_prices_details]"%s />',
                isset( $this->options['bdt_hide_financing_prices_details'] ) && $this->options['bdt_hide_financing_prices_details'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_leasing_prices_details_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options" value="on" name="bdt_options[bdt_hide_leasing_prices_details]"%s />',
                isset( $this->options['bdt_hide_leasing_prices_details'] ) && $this->options['bdt_hide_leasing_prices_details'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_financing_prices_card_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options" value="on" name="bdt_options[bdt_hide_financing_prices_card]"%s />',
                isset( $this->options['bdt_hide_financing_prices_card'] ) && $this->options['bdt_hide_financing_prices_card'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_leasing_prices_card_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options" value="on" name="bdt_options[bdt_hide_leasing_prices_card]"%s />',
                isset( $this->options['bdt_hide_leasing_prices_card'] ) && $this->options['bdt_hide_leasing_prices_card'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_print_section_info()
        {
            print __( 'Enter your Biltorvet API key to activate the service. To obtain your own API key, please contact a Biltorvet sales representative.', 'biltorvet-dealer-tools' );
        }

        public function bdt_api_key_callback()
        {
            printf(
                '<input type="text" id="bdt_options" name="bdt_options[api_key]" value="%s" />',
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
        
        public function bdt_hide_ad_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options" value="on" name="bdt_options[hide_ad_vehicles]"%s />',
                isset( $this->options['hide_ad_vehicles'] ) && $this->options['hide_ad_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_bi_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options" value="on" name="bdt_options[hide_bi_vehicles]"%s />',
                isset( $this->options['hide_bi_vehicles'] ) && $this->options['hide_bi_vehicles'] === 'on' ? ' checked="checked"' : ''
            );
        }

        public function bdt_hide_sold_vehicles_callback()
        {
            printf(
                '<input type="checkbox" id="bdt_options" value="on" name="bdt_options[hide_sold_vehicles]"%s />',
                isset( $this->options['hide_sold_vehicles'] ) && $this->options['hide_sold_vehicles'] === 'on' ? ' checked="checked"' : ''
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

        // public function bdt_make_template_page_callback()
        // {
        //     wp_dropdown_pages(array(
        //         'depth'                 => 2,
        //         'echo'                  => 1,
        //         'name'                  => 'bdt_options[make_template_page_id]',
        //         'selected'              => isset($this->options['make_template_page_id']) ? intval($this->options['make_template_page_id']) : 0
        //     ));
        // }

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
    }
