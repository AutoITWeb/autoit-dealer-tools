<?php

use Biltorvet\Controller\ApiController;
use Biltorvet\Factory\VehicleFactory;
use Biltorvet\Factory\StructuredDataFactory;
use Biltorvet\Controller\PriceController;
use Biltorvet\Helper\DataHelper;
use Biltorvet\Helper\ProductHelper;

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
    private $_options_4;
    private $_options_5;
    private $_options_6;
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
        add_filter('get_canonical_url', array(&$this, 'bdt_vehicledetails_canonical'), 1000);
        add_filter('get_shortlink', array(&$this, 'bdt_vehicledetails_canonical'), 1000);

        // For external users of our plugin
        add_filter('wp_mail', array(&$this, 'bdt_external_user_lead_append'), 1);

        add_action( 'upgrader_process_complete', 'bdt_plugin_updated' );

        $this->_options = get_option('bdt_options');
        $this->_options_2 = get_option('bdt_options_2');
        $this->_options_3 = get_option('bdt_options_3');
        $this->_options_4 = get_option('bdt_options_4');
        $this->_options_5 = get_option('bdt_options_5');
        $this->_options_6 = get_option('bdt_options_6');

        /*
        *  Used in conjuction with our divi child theme.
        *  Tells the divi ContactForm.php in our child theme how to handle form submissions.
        */

        isset($this->_options['bdt_leads']) ? (define ('leads', $this->_options['bdt_leads'])) : (define ('leads', "-1"));
        add_action('call_get_vehicle_data', array($this, 'get_vehicle_data'), 10, 2);
        add_action('call_AutodesktopSendLead', array($this, 'bdt_send_adt_lead'), 10, 6);
        add_action('call_create_lead', array($this, 'bdt_create_lead'), 10, 10);

        if ($this->_options['api_key'] === null || trim($this->_options['api_key']) === '') {
            add_action('admin_notices', array(&$this, 'bdt_error_noapikey'));
        } else {
            $this->biltorvetAPI = new BiltorvetAPI($this->_options['api_key']);

            new Ajax($this->biltorvetAPI);
            new CustomApiRoutes($this->biltorvetAPI);

            if (!is_admin()) {
                new BiltorvetShortcodes($this->biltorvetAPI, $this->_options, $this->_options_2, $this->_options_3, $this->_options_4, $this->_options_5);
            }

            // Getting the list of companies connected to the API key for use with the department selector used on contactforms
            // This feature is only usable by CarLite dealers and not external users.
            // the array of companies is encoded to avoid php warnings.
            try {
                $get_companies = $this->biltorvetAPI->GetCompanies();
                (define('bdt_companies_list', json_encode($get_companies->companies)));
            } catch (exception $e) {

            }
        }

        if (is_admin()) {
            new BDTSettingsPage($this->_options, $this->_options_2, $this->_options_3, $this->_options_4, $this->_options_5, $this->_options_6, $this->biltorvetAPI);
        }
    }

    function bdt_plugin_updated() {

        flush_rewrite_rules();
    }

    /*
     * New function to send leads to Autodesktop
     * This will only send leads to the first company in the GetCompanies() array.
     * At the moment there's no way to determine where a lead should be sent to so the first companyid in the array is chosen (in the API)
     */

    public function get_vehicle_data($vehicleId, $returnValue)
    {
        if($vehicleId != null) {
            try{
                $vehicle = $this->biltorvetAPI->GetVehicle($vehicleId);
            } catch(Exception $e) {
                // If the vehicle isn't found the data won't be added to the lead. Should only happen if the call to the API fails.
            }
        }

        $returnValue->return = $vehicle;
    }

    /*
     * Uses the DataImporter - depricated
     */
    public function bdt_send_adt_lead( $message, $email, $name, $phoneNumber, $query_actiontype, $companyId)
    {
        $getCompanies = $this->biltorvetAPI->GetCompanies();

        $lead = new LeadInputObject();

        $lead->CompanyId = $companyId != 0 ? $companyId : $getCompanies->companies[0]->id;
        $lead->ActivityType = $query_actiontype ?? "Contact";
        $lead->Email = $email;
        $lead->Name = $name;
        $lead->Body = $message;
        $lead->CellPhoneNumber = $phoneNumber;

        try{
            $sendLead = $this->biltorvetAPI->AutodesktopSendLead($lead);
        } catch(Exception $e) {
            /*
             * Do nothing if it fails - the API will catch the error.
             * Divi DB will catch the post call and save the lead in the Wordpress Database.
             * Perhaps the form submission should fail if the call fails? This is only relevant if the dealer only recieves leads to Autodesktop
             */
        }
    }

    public function bdt_create_lead($message, $email, $name, $phoneNumber, $address, $postalcode, $city, $companyId, $externalId, $query_source)
    {

        $getCompanies = $this->biltorvetAPI->GetCompanies();

        $newLead = new NewLeadInputObject();
        $lead = $newLead->CreateLead($newLead, $message, $email, $name, $phoneNumber, $address, $postalcode, $city, $externalId, $query_source);

        $sendLeadTo = $companyId != 0 ? $companyId : $getCompanies->companies[0]->id;

        try {
            $sendLead = $this->biltorvetAPI->CreateLead($lead, $sendLeadTo);

        } catch (Exception $e) {
            error_log(date('Y-m-d H:i:s') . '' . $e->getMessage(), $this->errLogFile);

            // the api handles all exceptions (more or less....) Check the api log if something fails
            // the user should still get a success message and the lead will be saved in Divi DB - But why would it ever fail? ;-)
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
        $post = get_post();

        $vehicledetail = get_post($this->_options['detail_template_page_id']);

        if($post->ID === $vehicledetail->ID)
        {
            $vehicleId = get_query_var('bdt_vehicle_id', -1);
            if ($vehicleId === -1) {
                return;
            }
            try {
                $vehicle = $this->biltorvetAPI->GetVehicle($vehicleId);
            } catch (Exception $e) {
                return;
            }

            global $wp;
            $oVehicle = VehicleFactory::create(json_decode(json_encode($vehicle), true));
            $priceController = new PriceController($oVehicle);
            $product = new ApiController()

            //$priceController->getStructuredDataPrice();

            ?>
            <meta property="og:url" content="<?php echo home_url($wp->request); ?>" />
            <meta property="og:type" content="product"/>
            <meta property="og:title"content="<?php echo $oVehicle->getMakeName() . ' ' . $oVehicle->getVariant(); ?>"/>
            <meta property="og:description" content="<?php echo strip_tags($oVehicle->getDescription()); ?>"/>
            <meta property="og:image" content="<?php echo $oVehicle->getImages()[0]; ?>"/>
            <meta property="og:image:width" content="1920"/>
            <meta property="og:image:height" content="1080"/>
            <?php

            ?>
            <meta name="description" content="Hos <?= $oVehicle->getCompany()->getName() ?> har vi altid et stort udvalg af brugte biler til omgående levering. Kig forbi, eller kontakt os i dag for at høre mere.">
            <?php

            // Structured data - requires the product "Structured Data" in the dashboard
            if(ProductHelper::hasAccess("Structured Data", $product->getCompanyProducts()) && $priceController->getStructuredDataPrice() != null)
            {
                ?>
                <script type="application/ld+json">
                <?= StructuredDataFactory::VehicleDetails($oVehicle, $priceController->getStructuredDataPrice(), $vehicle->equipment, $this->_options); ?>
            </script>
                <?php
            }
        }
    }

    public function bdt_vehicledetails_canonical()
    {
        global $wp;

        $vehicleId = get_query_var('bdt_vehicle_id', -1);
        if ($vehicleId === -1) {
            return;
        }

        return home_url($wp->request);
    }

    public function bdt_register_scripts()
    {
        wp_register_script( 'bootstrap_slider', plugins_url('scripts/bootstrap-slider.min.js',  dirname(__FILE__) ) , array('jquery'), '1.0.1', true );

        //wp_register_script( 'bdt_script', plugins_url('v3/scripts/vehiclesearchv3.min.js',  dirname(__FILE__) ) , array('jquery', 'bootstrap_slider'), '1.0.1', true );
        wp_register_script( 'bdt_script', plugins_url('v3/scripts/vehiclesearchmultiselect.js',  dirname(__FILE__) ) , array('jquery', 'bootstrap_slider'), '1.0.1', true );
        //wp_register_script( 'bdt_script', plugins_url('v3/scripts/vehiclesearchmultiselect.min.js',  dirname(__FILE__) ) , array('jquery', 'bootstrap_slider'), '1.0.1', true );

        wp_register_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', true);

        // A specific version of the gallery (Very safe)
        wp_register_script( 'bt_slideshow', 'https://gallery.autoit.dk/versions/1.0.2/gallery.js', true );
        wp_register_script( 'search_script', plugins_url('scripts/search.js',  dirname(__FILE__) ) , array('jquery'), '1.0.0', true );

        // Good old widget connector - prim. needed for our exchangepricewidget
        wp_enqueue_script( 'bdt_widgetconnector', 'https://services.autoit.dk/Embed.js', null, '1.0.0', true);

        // Rest Api config
        wp_localize_script( 'search_script', 'ajax_config', array (
            'restUrl' => get_rest_url()
        ));
        wp_localize_script( 'bdt_script', 'ajax_config', array(
            'restUrl' => get_rest_url()
        ));
    }

    public function bdt_register_styles()
    {
        wp_register_style( 'bticons', 'https://source.autoit.dk/fonts/biltorvet/v1.0.2/bticons.css', null, '1.0.2' );
        //wp_register_style( 'bdt_style', plugins_url('css/biltorvet.min.css',  dirname(__FILE__)), array('bticons'), '1.0.1' );
        wp_register_style( 'bdt_style', plugins_url('css/biltorvet.css',  dirname(__FILE__)), array('bticons'), '1.0.1' );
        wp_register_style('bdt_embed_style', 'https://services.autoit.dk/Embed.css', null, '1.0.1');
        wp_register_style('animate', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', null, '4.1.1');

        if(isset($this->_options['primary_color']) && trim($this->_options['primary_color']) !== '')
        {
            wp_add_inline_style( 'bdt_style', ".bdt_cta:not(.donottint) {color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important;} .bdt .slider:not(.slider-disabled) .slider-selection, .bdt .badge.badge-primary {background-color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important;} .bdt_color{color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important;} .bdt_bgcolor, .et_pb_button.bdt_bgcolor:hover {background:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important;} .bdt .slider-handle.round, .bdt_bordercolor{border-color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " !important} .bdt .lds-ring div {border-color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " transparent transparent transparent !important} .bdt .lds-ring-paging div {border-color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . " transparent transparent transparent !important}" );

            // Select2 multiselect (vehiclesearch)
            wp_add_inline_style( 'bdt_style', ".select2-container--default .select2-results__option--highlighted.select2-results__option--selectable{ background-color:" . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . "!important; color:white }" );
        }
    }

    public function bdt_register_session()
    {
        if (session_id() == '')
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

        $query_vars[] = 'bdt_filter_type';
        $query_vars[] = 'bdt_filter';
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

    public static function bdt_locate_template( $template_name, $template_path, $default_path) {

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
            $vehiclesearchresults = get_page_uri($options['vehiclesearch_page_id']);

            $vehicledetail = get_page_uri($options['detail_template_page_id']);
            $query = 'index.php?pagename=' . $vehicledetail. '&bdt_vehicle_id=$matches[1]';
            //add_rewrite_rule( '^' . $vehiclesearchresults . '.+((?:AD|BI)[0-9]+)$', $query , 'top' );
            add_rewrite_rule( '^' . $vehiclesearchresults . '.+((?:AD|BI)([A-Za-z0-9\-]+))$', $query , 'top' );
        }
        if(isset($options['vehiclesearch_page_id']) && trim($options['vehiclesearch_page_id']) !== '')
        {
            $vehiclesearchresults = get_page_uri($options['vehiclesearch_page_id']);
            $query = 'index.php?pagename=' . $vehiclesearchresults . '&bdt_page=$matches[1]';
            add_rewrite_rule( '^' . $vehiclesearchresults . '\/([0-9]+)$', $query, 'top' );

            $query = 'index.php?pagename=' . $vehiclesearchresults . '&bdt_page=$matches[1]&bdt_filter_type=$matches[2]&bdt_filter=$matches[3]';
            add_rewrite_rule( '^' . $vehiclesearchresults . '\/([0-9]+)/([^/]*)/([^/]*)$', $query, 'top' );

            $query = 'index.php?pagename=' . $vehiclesearchresults . '&bdt_page=$matches[1]&bdt_filter_type=$matches[2]&bdt_filter=$matches[3]&filter_brand=$matches[4]';
            add_rewrite_rule( '^' . $vehiclesearchresults . '\/([0-9]+)/([^/]*)/([^/]*)/([^/]*)$', $query, 'top' );
        }
    }

    static function bdt_flushrewriterules()
    {
        flush_rewrite_rules();
    }

    /**
     * WP_MAIL filter for external users*
     * Adds vehicle data to leads for external users
     *
     * @param string 	$args			Mail args.
     */
    public function bdt_external_user_lead_append( $args )
    {
        $product = new ApiController();

        if(ProductHelper::hasAccess("External User", $product->getCompanyProducts()))
        {
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

            $query = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
            parse_str( $query, $queryParams );

            if(!isset($queryParams) || !isset($queryParams['bdt_vehicle_id']) || !isset($queryParams['bdt_actiontype']))
            {
                return $args;
            }

            try{
                $vehicle = $this->biltorvetAPI->GetVehicle($queryParams['bdt_vehicle_id']);
            } catch(Exception $e) {
                return $e->getMessage();
            }

            // Append the vehicle info to the WP email.
            $args['message'] .= "\r\n\r\n" .  sprintf( __('Selected vehicle: %s', 'biltorvet-dealer-tools'), 'En kunde har udvist interesse for bilen ' . $vehicle->makeName . ' ' . $vehicle->model . ' med ID ' . $vehicle->id);

            return $args;
        }
    }
}
