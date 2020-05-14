<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * The Plugin init class
 */
class Biltorvet
{
    private $_options;
    private $_options_2;
    private $_options_3;
    private $biltorvetAPI;

    public function __construct()
    {
        // Include helper classes
        require_once plugin_dir_path(__FILE__) . 'helpers/MailFormatter.php';

        add_action('init', array(&$this, 'bdt_register_session'), 1);
        add_action('init', array('Biltorvet', 'bdt_rewriterules'));
        add_action('wp_enqueue_scripts', array(&$this, 'bdt_register_scripts'));
        add_action('wp_enqueue_scripts', array(&$this, 'bdt_register_styles'));
        add_action('plugins_loaded', array(&$this, 'bdt_load_plugin_textdomain'));
        add_filter('query_vars', array(&$this, 'bdt_query_vars'));
        add_action('parse_request', array(&$this, 'bdt_parse_request'), 1);
        add_filter('pre_get_document_title', array(&$this, 'bdt_title'), 1000);
        add_filter('wp_title', array(&$this, 'bdt_title'), 1000);
        add_action('wp_head', array(&$this, 'bdt_meta_tags'), 1000);
        add_action('post_updated', array(&$this, 'bdt_post_updated'), 1000);

        $this->_options = get_option('bdt_options');
        $this->_options_2 = get_option('bdt_options_2');
        $this->_options_3 = get_option('bdt_options_3');

        if ($this->_options['api_key'] === null || trim($this->_options['api_key']) === '') {
           add_action('admin_notices', array(&$this, 'bdt_error_noapikey'));
        } else {
            $this->biltorvetAPI = new BiltorvetAPI($this->_options['api_key']);
            new Ajax($this->biltorvetAPI);
            if (!is_admin()) {
                new BiltorvetShortcodes($this->biltorvetAPI, $this->_options, $this->_options_2);
            }
        }

        if (is_admin()) {
            new BDTSettingsPage($this->_options, $this->_options_2, $this->_options_3);
        }
    }

    public function bdt_parse_request($request)
    {
        // This allows to overwrite the existing page with a Wordpress page, if it exists in WP admin
        if (isset($request->query_vars['bdt_vehicle_id'])) {
            try {
                $vehicle = $this->biltorvetAPI->GetVehicle($request->query_vars['bdt_vehicle_id']);
            } catch (Exception $e) {
                return;
            }
            if (!isset($vehicle) || $vehicle == null) {
                add_action('template_redirect', function () {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header(404);
                    get_template_part(404);
                    exit;
                });
            }
        }

        return $request;
    }

    public function bdt_title($title)
    {
        $vehicleId = get_query_var('bdt_vehicle_id', -1);
        if ($vehicleId === -1) {
            return $title;
        }
        try {
            $vehicle = $this->biltorvetAPI->GetVehicle($vehicleId);
        } catch (Exception $e) {
            return $title;
        }
        return $vehicle->makeName . ' ' . $vehicle->model . ' ' . $vehicle->variant;
    }

    public function bdt_meta_tags()
    {
        global $wp_query;
        global $wp;

        // Check if this is a car search, and if it is, make it non-cacheable
        if (isset($wp_query->post->ID) && $wp_query->post->ID === intval($this->_options['vehiclesearch_page_id'])) {
            // Make sure that the search page, which is volatile,  does not get indexed.
            if (!has_action('wp_no_robots') && !has_action('noindex')) {
                wp_no_robots();
            }
            ?>
            <meta http-equiv="cache-control" content="max-age=0"/>
            <meta http-equiv="cache-control" content="no-cache"/>
            <meta http-equiv="expires" content="0"/>
            <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT"/>
            <meta http-equiv="pragma" content="no-cache"/>
            <?php
            return;
        }

        $vehicleId = get_query_var('bdt_vehicle_id', -1);
        if ($vehicleId === -1) {
            return;
        }
        try {
            $vehicle = $this->biltorvetAPI->GetVehicle($vehicleId);
        } catch (Exception $e) {
            return;
        }
        ?>
        <meta property="og:url" content="<?php echo home_url($wp->request); ?>" />
        <meta property="og:type" content="product"/>
        <meta property="og:title"
              content="<?php echo $vehicle->makeName . ' ' . $vehicle->model . ' ' . $vehicle->variant; ?>"/>
        <meta property="og:description" content="<?php echo strip_tags($vehicle->description); ?>"/>
        <meta property="og:image" content="<?php echo $vehicle->images[0]; ?>"/>
        <meta property="og:image:width" content="1024"/>
        <meta property="og:image:height" content="768"/><?php
    }

        public function bdt_adt_send_lead( $args )
        {
            global $ActivityType;
            $replyTo = '';
            if(array_key_exists('headers', $args))
            {
                foreach($args['headers'] as $header)
                {
                    preg_match('/^Reply-To: ".*" <(.+)>$/', $header, $matches);
                    if(count($matches) > 1)
                    {
                        $replyTo = $matches[1];
                        break;
                    }
                }
            }
            if($replyTo === '')
            {
                wp_die( '<div class="et_pb_contact_error_text">' . sprintf( __('Could not send the lead: %s', 'biltorvet-dealer-tools'), 'No Reply-To header found.') . '</div>' ); 
            }
            $query = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
            parse_str( $query, $queryParams );
            
            if(!isset($queryParams) || !isset($queryParams['bdt_vehicle_id']) || !isset($queryParams['bdt_actiontype']))
            {
                return $args;
            }
            if(!in_array($queryParams['bdt_actiontype'], $ActivityType))
            {
                return sprintf( __('Unrecognized CTA type. Allowed types: %s', 'biltorvet-dealer-tools'), implode(', ', $ActivityType));
            }

            try{
                $vehicle = $this->biltorvetAPI->GetVehicle($queryParams['bdt_vehicle_id']);
            } catch(Exception $e) {
                return $e->getMessage();
            }

           $lead = new LeadInputObject();
            $lead->Model = TextUtils::GetVehicleIdentification($vehicle);


            // Some e-mail clients don't respect the reply-to header, and then we lose the information about sender. For this reason, we are gluing the sender e-mail back to the e-mail body.
            $args['message'] .= "\r\n\r\n" .  sprintf( __('Lead sender: %s', 'biltorvet-dealer-tools'), $replyTo);

            // Append the vehicle info to the WP email.
            $args['message'] .= "\r\n\r\n" .  sprintf( __('Selected vehicle: %s', 'biltorvet-dealer-tools'), $lead->Model . ' (' . $vehicle->id . ')');

            return $args;
        }

        public function bdt_register_scripts()
        {
            // deregister WP's autoloaded Jquery and replace with specified version. Versions below and above 2 will break the video player on the cardetail page
            wp_deregister_script('jquery');
            wp_register_script('jquery', 'https://code.jquery.com/jquery-2.2.4.min.js', '2.2.4', false);
            wp_register_script( 'bootstrap_slider', plugins_url('scripts/bootstrap-slider.min.js',  dirname(__FILE__) ) , array('jquery'), '1.0.1', true );
            wp_register_script( 'bdt_vimeo', 'https://player.vimeo.com/api/player.js', '2.11.0', true );
            wp_register_script( 'hammerjs', 'https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js', null, '2.0.8', true );
            wp_register_script( 'bt_slideshow', 'https://source.autoit.dk/slideshow/v1.0.5/slideshow.min.js', array('hammerjs', 'jquery', 'bdt_vimeo'), '1.0.5', true );
            wp_register_script( 'bdt_script', plugins_url('scripts/biltorvet.min.js',  dirname(__FILE__) ) , array('jquery', 'bootstrap_slider'), '1.0.1', true );
            wp_localize_script( 'bdt_script', 'ajax_config', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
            wp_register_script( 'search_script', plugins_url('scripts/search.js',  dirname(__FILE__) ) , array('jquery'), '1.0.0', true );
            wp_localize_script( 'search_script', 'ajax_config', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

            wp_enqueue_script( 'bdt_widgetconnector', 'https://services.autoit.dk/Embed.js', null, '1.0.0', true);
        }

        public function bdt_register_styles()
        {
            wp_register_style( 'bticons', 'https://source.autoit.dk/fonts/biltorvet/v1.0.2/bticons.css', null, '1.0.2' );
            wp_register_style( 'bt_slideshow', 'https://source.autoit.dk/slideshow/v1.0.5/slideshow.css', array('bticons'), '1.0.5' );
            wp_register_style( 'bdt_style', plugins_url('css/biltorvet.css',  dirname(__FILE__)), array('bticons'), '1.0.1' );
            wp_register_style('bdt_embed_style', 'https://services.autoit.dk/Embed.css', null, '1.0.1');
            if(isset($this->_options['primary_color']) && trim($this->_options['primary_color']) !== '')
            {
                wp_add_inline_style( 'bdt_style', ".bdt_cta:not(.donottint) {color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important;} .bdt .slider:not(.slider-disabled) .slider-selection, .bdt .badge.badge-primary {background-color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important;} .bdt_color{color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important;} .bdt_bgcolor, .et_pb_button.bdt_bgcolor:hover {background:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important;} .bdt .slider-handle.round, .bdt_bordercolor{border-color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important} .bdt .lds-ring div {border-color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " transparent transparent transparent !important}" );
            }
        }

        public function bdt_register_session()
        {
            if (!session_id())
                session_start();
        }
        public function bdt_load_plugin_textdomain() {
            load_plugin_textdomain( 'biltorvet-dealer-tools', FALSE, basename( dirname(dirname( __FILE__ )) ) . '/languages' );
        }

        public function bdt_query_vars( $query_vars )
        {
            $query_vars[] = 'bdt_page';
            $query_vars[] = 'bdt_vehicle_id';
            $query_vars[] = 'bdt_actiontype';

            $query_vars[] = 'filter_make';
            $query_vars[] = 'filter_brand';

            return $query_vars;
        }

        public function bdt_error_noapikey() {
            ?>
            <div class="error notice">
                <p><?php _e( 'No API key specified. Biltorvet plugin functionality is disabled.', 'biltorvet-dealer-tools' ); ?></p>
            </div>
            <?php
        }

        public function bdt_post_updated($postId, $post_after = null, $post_before = null)
        {
            $vehicleSearchPage = $this->_options['vehiclesearch_page_id'];
            $vehicleSearchPageAncestors = get_ancestors($vehicleSearchPage);
            if($postId != $vehicleSearchPage && !in_array($postId, $vehicleSearchPageAncestors))
            {
                return;
            }   
            Biltorvet::bdt_refresh_rewrite_rules();
        }

        public static function bdt_locate_template( $template_name, $template_path = '', $default_path = '' ) {
            // Set variable to search in woocommerce-plugin-templates folder of theme.
            if ( ! $template_path ) :
                $template_path = 'biltorvet-dealer-tools/';
            endif;
            // Set default plugin templates path.
            if ( ! $default_path ) :
                $default_path = plugin_dir_path( __FILE__ ) . '../templates/'; // Path to the template folder
            endif;
            // Search template file in theme folder.
            $template = locate_template( array(
                $template_path . $template_name,
                $template_name
            ) );
            // Get plugins template file.
            if ( ! $template ) :
                $template = $default_path . $template_name;
            endif;
            return apply_filters( 'bdt_locate_template', $template, $template_name, $template_path, $default_path );
        }
        
        /**
         * Get template.
         *
         * Search for the template and include the file.
         *
         * @since 1.0.14
         *
         * @param string 	$template_name			Template to load.
         * @param array 	$args					Args passed for the template file.
         * @param string 	$string $template_path	Path to templates.
         * @param string	$default_path			Default path to template files.
         */
        public static function bdt_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
            if ( is_array( $args ) && isset( $args ) ) :
                extract( $args );
            endif;
            $template_file = Biltorvet::bdt_locate_template( $template_name, $tempate_path, $default_path );
            if ( ! file_exists( $template_file ) ) :
                _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
                return;
            endif;
            return $template_file;
        }

        static function bdt_plugin_activated() {
            if (!get_option('bdt_options')) {
                update_option( 'bdt_options', array('api_key' => 'ce760c3b-2d44-4037-980b-894b79891525'));
            }
            Biltorvet::bdt_refresh_rewrite_rules();
        }

        public static function bdt_refresh_rewrite_rules()
        {
            Biltorvet::bdt_rewriterules();
            Biltorvet::bdt_flushrewriterules();
        }
        
        static function bdt_rewriterules()
        {
            $options = get_option( 'bdt_options' );
            if($options['api_key'] === null || trim($options['api_key']) === '')
            {
                return;
            }
            $vehicledetail = '';
            $vehiclesearchresults = '';
            if(isset($options['detail_template_page_id']) && trim($options['detail_template_page_id']) !== '')
            {
                $vehicledetail = get_page_uri($options['detail_template_page_id']);
                add_rewrite_rule( '^.+((?:AD|BI)[0-9]+)$', 'index.php?pagename=' . $vehicledetail. '&bdt_vehicle_id=$matches[1]', 'top' );
            }
            if(isset($options['vehiclesearch_page_id']) && trim($options['vehiclesearch_page_id']) !== '')
            {
                $vehiclesearchresults = get_page_uri($options['vehiclesearch_page_id']);
                $query = 'index.php?pagename=' . $vehiclesearchresults . '&bdt_page=$matches[1]';
                add_rewrite_rule( '^' . $vehiclesearchresults . '\/([0-9]+)$', $query, 'top' );

                $query = 'index.php?pagename=' . $vehiclesearchresults . '&bdt_page=$matches[1]&filter_make=$matches[2]';
                add_rewrite_rule( '^' . $vehiclesearchresults . '\/([0-9]+)/([^/]*)$', $query, 'top' );

                $query = 'index.php?pagename=' . $vehiclesearchresults . '&bdt_page=$matches[1]&filter_make=$matches[2]&filter_brand=$matches[3]';
                add_rewrite_rule( '^' . $vehiclesearchresults . '\/([0-9]+)/([^/]*)/([^/]*)$', $query, 'top' );
            }
        }

        static function bdt_flushrewriterules()
        {
            flush_rewrite_rules();
        }
    }
