<?php

    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

use Biltorvet\Controller\PriceController;
use Biltorvet\Factory\VehicleFactory;
use Biltorvet\Helper\DataHelper;
use Biltorvet\Model\Vehicle;

    class CustomApiRoutes {
        private $biltorvetAPI;
        private $_options;
        private $_options_2;
        private $bdt_root_url;

        public function __construct($biltorvetAPI)
        {
            if($biltorvetAPI === null)
            {
                throw new Exception( __('No Biltorvet API instance provided.', 'biltorvet-dealer-tools') );
            }

            $this->_options = get_option( 'bdt_options' );
            $this->_options_2 = get_option( 'bdt_options_2' );
            $this->biltorvetAPI = $biltorvetAPI;

            add_action( 'rest_api_init', [$this, 'init'] );
        }

        function init() {
            register_rest_route( 'autoit-dealer-tools/v1', '/filteroptions', [
                'methods' => 'POST',
                'callback' => array($this, 'get_filter_options'),
                'permission_callback' => '__return_true',
            ] );

            register_rest_route( 'autoit-dealer-tools/v1', '/resetfilteroptions', [
                'methods' => 'POST',
                'callback' => array($this, 'reset_filter_options'),
                'permission_callback' => '__return_true',
            ] );

            register_rest_route( 'autoit-dealer-tools/v1', '/filteroptions/savefilter', [
                'methods' => 'POST',
                'callback' => array($this, 'bdt_save_filter'),
                'permission_callback' => '__return_true',
            ] );

            register_rest_route( 'autoit-dealer-tools/v1', '/vehiclesearch/search', [
                'methods' => 'POST',
                'callback' => array($this, 'bdt_vehicle_search'),
                'permission_callback' => '__return_true',
            ] );

            register_rest_route( 'autoit-dealer-tools/v1', '/vehiclesearch/search_paging', [
                'methods' => 'POST',
                'callback' => array($this, 'bdt_vehicle_search_paging'),
                'permission_callback' => '__return_true',
            ] );

            register_rest_route( 'autoit-dealer-tools/v1', '/vehiclesearch/quicksearch', [
                'methods' => 'POST',
                'callback' => array($this, 'bdt_vehicle_quicksearch'),
                'permission_callback' => '__return_true',
            ] );

            register_rest_route( 'autoit-dealer-tools/v1', '/filteroptions/resetsession', [
                'methods' => 'POST',
                'callback' => array($this, 'bdt_reset_session'),
                'permission_callback' => '__return_true',
            ] );

            register_rest_route( 'autoit-dealer-tools/v1', '/cache/clear', [
                'methods' => 'POST',
                'callback' => array($this, 'bdt_clear_cache'),
                'permission_callback' => '__return_true',
            ] );

            $this->bdt_root_url = rtrim(get_permalink($this->_options['vehiclesearch_page_id']),'/');
        }

        public function get_all_pages_and_posts()
        {
            // Get all Pages
            $listOfPagesAndPosts = array();

            $allPages = get_pages();

            foreach($allPages as $page)
            {
                array_push($listOfPagesAndPosts, get_permalink($page->ID));
            }

            // Get all posts
            $allPosts = get_posts();

            foreach($allPosts as $post)
            {
                array_push($listOfPagesAndPosts, get_permalink($post->ID));
            }

            return $listOfPagesAndPosts;
        }

        // Clears cache of a given list of pages
        public function bdt_clear_cache(WP_REST_Request $request){

            // Fetch params
            $getApiKey = $request->get_param('a');
            $getCacheType = $request->get_param('type');

            // A valid api key is needed - for the moment the Forhandler Web demo key will work on very CarLite page
            if($getApiKey !== null && $getApiKey === $this->_options['api_key'] || $getApiKey === "ce760c3b-2d44-4037-980b-894b79891525")
            {
                // "type" parameter is required
                if($getCacheType !== null)
                {
                    // instantiate arrays needed
                    $pages_to_clear = array();
                    $pages_to_preload = array();

                    // Vehicle details page
                    if($getCacheType === 'detail')
                    {
                        // Get pageId of the vehicledetailspage and the vehicle search page
                        $detailsPageId = isset($this->_options['detail_template_page_id']) ? intval($this->_options['detail_template_page_id']) : 0;

                        $vehicleDetailsPageUrl = get_permalink($detailsPageId);

                        array_push($pages_to_clear, $vehicleDetailsPageUrl);

                        // Pages that'll be preloaded by WPRocket or cached by the CarLite Api
                        $preloadPages = $this->biltorvetAPI->GetKeyEndpointsForCachePreload();

                        foreach($preloadPages as $preloadPage)
                        {
                            array_push($pages_to_preload, $preloadPage);
                        }
                    }

                    /*
                        All pages
                        Array of pages and posts will be used both for clearing the cache and preloading
                    */
                    if($getCacheType === 'all')
                    {
                        $pagesAndPostsToClearAndPreload = $this->get_all_pages_and_posts();

                        // Add all pages and posts to arrays
                        foreach($pagesAndPostsToClearAndPreload as $pageAndPost)
                        {
                            array_push($pages_to_clear, $pageAndPost); // To be cleared
                            array_push($pages_to_preload, $pageAndPost); // To be preloaded
                        }
                    }

                    // Clear cache and preload pages / posts
                    if(count($pages_to_clear) > 0)
                    {
                        try
                        {
                            // Clear cache
                            if ( function_exists( 'rocket_clean_post' ) )
                            {
                                foreach( $pages_to_clear as $page_to_clear) {
                                    rocket_clean_post( url_to_postid ( $page_to_clear ) );
                                }
                            }

                            // Preload WP Rocket Cache for pages added to $pages_to_clean_preload

                            // Response message (Mostly for debugging)
                            $apiResponse = array();

                            if ( function_exists( 'get_rocket_option' ) )
                            {
                                $args = array();

                                if( 1 == get_rocket_option( 'cache_webp' ) ) {
                                    $args[ 'headers' ][ 'Accept' ]      	= 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
                                    $args[ 'headers' ][ 'HTTP_ACCEPT' ] 	= 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
                                }

                                // Preload desktop pages/posts.
                                $apiResponse = $this->rocket_preload_page($pages_to_preload, $args, $getCacheType);

                                // It's nothing we are currently doing - we'll keep it here in case we want to do it
                                if( 1 == get_rocket_option( 'do_caching_mobile_files' ) ) {
                                    $args[ 'headers' ][ 'user-agent' ] 	= 'Mozilla/5.0 (Linux; Android 8.0.0;) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36';

                                    // Preload mobile pages/posts.
                                    $this->rocket_preload_page(  $pages_to_preload, $args, $getCacheType );
                                }
                                // Manuel Prelaod = "Activate Preloading"
                                //if( 1 == get_rocket_option( 'manual_preload' ) ) {
                                //}
                            }

                            http_response_code(200);
                            print_r($apiResponse);
                            exit;
                        }
                        catch (Exception $e)
                        {
                            http_response_code(400);
                            exit;
                        }
                    }
                }
            }

            // Api key not valid or params missing
            http_response_code(403);
            exit;
        }

        // Resets filter options (UI)
        public function reset_filter_options()
        {
            if (session_status() == PHP_SESSION_NONE)
            {
                session_start();
            }
            $_SESSION['bdt_filter'] = '';
            session_write_close();

            $filterObject = new BDTFilterObject();

            $filterObject = $this->UpdateFilterObjectWithValuesFromPluginOptions($filterObject);

            try {
                $filterObjectOptions = $this->biltorvetAPI->GetFilterOptions($filterObject);
            } catch(Exception $e) {
                return $e->getMessage();
            }

            echo json_encode($filterObjectOptions);

            die;
        }

        // Fetches Filter Options from the API
        public function get_filter_options()
        {
            $filterObject = new BDTFilterObject();

            if (session_status() == PHP_SESSION_NONE)
            {
                session_start();
            }

            if(isset($_SESSION['bdt_filter']) && !empty($_SESSION['bdt_filter']))
            {
                $filterObject = new BDTFilterObject(json_decode($_SESSION['bdt_filter'], true));
            }

            session_write_close();

            if(isset($_POST['filter']) && $_POST['filter'] != null) {

                $filterObject = new BDTFilterObject(sanitize_post($_POST['filter']));
            }

            $filterObject = $this->UpdateFilterObjectWithValuesFromPluginOptions($filterObject);

            try {
                $filterObjectOptions = $this->biltorvetAPI->GetFilterOptions($filterObject);
            } catch(Exception $e) {
                return $e->getMessage();
            }

            echo json_encode($filterObjectOptions);

            die;
        }

        // Saves the current filter
        public function bdt_save_filter()
        {
            if (session_status() == PHP_SESSION_NONE)
            {
                session_start();
            }
            $_SESSION['bdt_filter'] = json_encode($_POST['filter']);
            session_write_close();

            echo json_encode(array('status' =>'ok'));

            die;
        }

        // Fetches a list of vehicles from the API
        public function bdt_vehicle_search()
        {
            $currentPage = 1;
			$limit = intval($this->biltorvetAPI->GetVehicleResultsPageLimit());
			
			$setUserChoosenLimit =  $this->_options_2['dynamic_scroll_pagination_cars_pr_page'];
			if($setUserChoosenLimit !== 'Default' && $setUserChoosenLimit !== null)
			{
				$limit = $setUserChoosenLimit;
			}
			
            $start = ($currentPage -1) * $limit;

            $filterObject = new BDTFilterObject();

            if(isset($_SESSION['bdt_filter']) && $_SESSION['bdt_filter'] !== '')
            {
                $filterObject = new BDTFilterObject(json_decode($_SESSION['bdt_filter'], true));
            }

            if(isset($_POST['filter']) && $_POST['filter'] != null) {
                $filterObject = new BDTFilterObject(sanitize_post($_POST['filter']));
            }

            try {

                $filterObject->Start = $start;
                $filterObject->Limit = $limit;

                $filterObject = $this->UpdateFilterObjectWithValuesFromPluginOptions($filterObject);

                $vehicleFeed = $this->biltorvetAPI->GetVehicles($filterObject);

                $orderByValues = $this->biltorvetAPI->GetOrderByValues();

            } catch(Exception $e) {
                return $e->getMessage();
            }

            $amountOfPages = ceil($vehicleFeed->totalResults / $limit);

            ob_start();
            ?>

            <div id="vehicle_search_results" class="vehicle_search_results" data-totalResults="<?= $vehicleFeed->totalResults ?>">
                <div class="row resultsTitle">
                    <div class="col-md-6">
                        <h4>
                            <?php printf(__('Your search returned <span class="bdt_color">%d cars</span>', 'biltorvet-dealer-tools'), $vehicleFeed->totalResults); ?>
                        </h4>
                    </div>

                    <div class="col-md-6 searchFilter">
                        <div class="row">
                            <div class="col">
                                <select name="orderBy" id="select-orderby">
                                    <option value=""><?php _e('- Order by -', 'biltorvet-dealer-tools'); ?></option>
                                    <?php
                                    foreach($orderByValues as $orderBy) : ?>
                                        <option value="<?php echo $orderBy; ?>"
                                            <?php if (isset($filterObject->OrderBy) && $filterObject->OrderBy === $orderBy) : ?>
                                                selected="selected"
                                            <?php endif; ?>
                                        ><?= _e($orderBy, 'biltorvet-dealer-tools'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
                                <select name="ascDesc" id="select-asc-desc">
                                    <option value="desc"<?php echo $filterObject->Ascending !== 'true' || isset($this->_options_2['default_sorting_order']) && isset($this->_options_2['default_sorting_order']) === "Descending" ? ' selected="selected"' : '';  ?>><?php _e('Descending', 'biltorvet-dealer-tools'); ?></option>
                                    <option value="asc"<?php echo $filterObject->Ascending === 'true' || isset($this->_options_2['default_sorting_order']) && isset($this->_options_2['default_sorting_order']) === "Ascending" ? ' selected="selected"' : '';  ?>><?php _e('Ascending', 'biltorvet-dealer-tools'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="lds-ring-paging d-done" style="display: none; opacity: 0;"><div></div><div></div><div></div><div></div></div>
                    <div class="clearfix"></div>
                </div>
                <div class="results">
                    <div id="vehicle-row" class="row vehicle-row">
                        <?php

                            //$bdt_root_url = rtrim(get_permalink($this->_options['vehiclesearch_page_id']),'/');

                            foreach($vehicleFeed->vehicles as $oVehicle)
                            {
                                $link = $this->bdt_root_url . '/' . $oVehicle->uri;

                                // @TODO: Refactor.
                                // For new we convert the old vehicle object to the new, so it works with the new templates
                                // PLUGIN_ROOT refers to the v2 root.

                                /** @var Vehicle $vehicle */
                                $vehicle = VehicleFactory::create(json_decode(json_encode($oVehicle), true));
                                $vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
                                $priceController = new PriceController($vehicle);
                                $basePage = $this->bdt_root_url;
                                require PLUGIN_ROOT . 'templates/partials/_vehicleCard.php';
                            }
                        ?>
                    </div>
                        </div>
                        <div class="paging">
                            <?php
                                $buttonCurrentPage = $currentPage;

                                $newEnd = ($currentPage) * $limit;
								
								$dynamic_scroll_pagination = $this->_options_2['bdt_dynamic_scroll_pagination'];								

                                if($buttonCurrentPage < $amountOfPages)
                                {
                                    if($dynamic_scroll_pagination)
									{
										echo '<button class="paging-button-scroll et_pb_button bdt_bgcolor" id="paging-button" data-current-page="' . $buttonCurrentPage . '" data-amount-of-pages="' . $amountOfPages . '" data-end="' . $newEnd .'" data-limit="' . $limit .'">Indlæs flere...</button>';
									}
									else
									{
										echo '<button class="paging-button et_pb_button bdt_bgcolor" id="paging-button" data-current-page="' . $buttonCurrentPage . '" data-amount-of-pages="' . $amountOfPages . '" data-end="' . $newEnd .'" data-limit="' . $limit .'">Indlæs flere...</button>';
									}
                                }
                            ?>
                            <div class="lds-ring-paging d-done" style="display: none; opacity: 0;"><div></div><div></div><div></div><div></div></div>
                        </div>
                    </div>
            <?php
            $content = ob_get_contents();
            ob_end_clean();

            // Save filter in session
            if (session_status() == PHP_SESSION_NONE)
            {
                session_start();
            }

            $_SESSION['bdt_filter'] = json_encode($filterObject);
            session_write_close();

            return $content;

            die;
        }

        // Fetches vehicle used by the quicksearch (The new FullTextSearch filter)
        public function bdt_vehicle_quicksearch()
        {
            $filterObject = new BDTFilterObject();

            if(isset($_POST['q']) && $_POST['q'] != null) {
                $filterObject->FullTextSearch = $_POST['q'];
            }

            try {

                $filterObject = $this->UpdateFilterObjectWithValuesFromPluginOptions($filterObject);

                $vehicleFeed = $this->biltorvetAPI->GetVehiclesQuickSearch($filterObject);

            } catch(Exception $e) {
                return $e->getMessage();
            }

            // There must be a better way to enrich the vehicle data with the complete vehicle url
            foreach($vehicleFeed->vehicles as $vehicle)
            {
                $vehicle->uri = $this->bdt_root_url . '/' . $vehicle->uri;
            }

            return $vehicleFeed;

            die;
        }

        // Fetches a more vehicles when a user clicks on "Indlæse flere..."
        public function bdt_vehicle_search_paging()
        {
            $filterObject = new BDTFilterObject();

            if(isset($_SESSION['bdt_filter']) && !empty($_SESSION['bdt_filter']))
            {
                $filterObject = new BDTFilterObject(json_decode($_SESSION['bdt_filter'], true));
            }

            if(isset($_POST['filter']) && $_POST['filter'] != null) {
                $filterObject = new BDTFilterObject(sanitize_post($_POST['filter']));
            }
            $filterObject->Start = $_POST['start'];
            $filterObject->Limit = $_POST['limit'];

            try {

                $filterObject = $this->UpdateFilterObjectWithValuesFromPluginOptions($filterObject);

                $vehicleFeed = $this->biltorvetAPI->GetVehicles($filterObject);
            } catch(Exception $e) {
                return $e->getMessage();
            }

            ob_start();
            $bdt_root_url = rtrim(get_permalink($this->_options['vehiclesearch_page_id']),'/');

            foreach($vehicleFeed->vehicles as $oVehicle)
            {
                $link = $bdt_root_url . '/' . $oVehicle->uri;

                // @TODO: Refactor.
                // For new we convert the old vehicle object to the new, so it works with the new templates
                // PLUGIN_ROOT refers to the v2 root.

                /** @var Vehicle $vehicle */
                $vehicle = VehicleFactory::create(json_decode(json_encode($oVehicle), true));
                $vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
                $priceController = new PriceController($vehicle);
                $basePage = $bdt_root_url;
                require PLUGIN_ROOT . 'templates/partials/_vehicleCard.php';
            }

            $content = ob_get_contents();
            ob_end_clean();

            return $content;

            die;
        }

        // All the current filters we need to account for when calling the API
        // These filters are set in the plugin settings
        public function UpdateFilterObjectWithValuesFromPluginOptions(BDTFilterObject $filterObject)
        {
            if ($filterObject->OrderBy === null && isset($this->_options_2['default_sorting_value'])) {
                $filterObject->OrderBy = $this->_options_2['default_sorting_value'];
            }

            if($filterObject->Ascending === null && isset($this->_options_2['default_sorting_order'])) {
                $filterObject->Ascending = $this->_options_2['default_sorting_order'] === 'Ascending' ? 'true' : 'false';
            }

            $filterObject->HideSoldVehicles = isset($this->_options_2['hide_sold_vehicles']) && $this->_options_2['hide_sold_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideLeasingVehicles = isset($this->_options_2['hide_leasing_vehicles']) && $this->_options_2['hide_leasing_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideFlexLeasingVehicles = isset($this->_options_2['hide_flexleasing_vehicles']) && $this->_options_2['hide_flexleasing_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideWarehousesaleVehicles = isset($this->_options_2['hide_warehousesale_vehicles']) && $this->_options_2['hide_warehousesale_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideCarLiteDealerLabelVehicles = isset($this->_options_2['hide_carlite_dealer_label_vehicles']) && $this->_options_2['hide_carlite_dealer_label_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideRentalVehicles = isset($this->_options_2['hide_rental_vehicles']) && $this->_options_2['hide_rental_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideUpcomingVehicles = isset($this->_options_2['hide_upcoming_vehicles']) && $this->_options_2['hide_upcoming_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideWholesaleVehicles = isset($this->_options_2['hide_wholesale_vehicles']) && $this->_options_2['hide_wholesale_vehicles'] === 'on' ? 'true' : null;
			//jlk
			$filterObject->HideOnlyWholesaleVehicles = isset($this->_options_2['hide_only_wholesale_vehicles']) && $this->_options_2['hide_only_wholesale_vehicles'] === 'on' ? 'true' : null;
			$filterObject->ShowOnlyWholesaleVehicles = isset($this->_options_2['show_only_wholesale_vehicles']) && $this->_options_2['show_only_wholesale_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideTrailerVehicles = isset($this->_options_2['hide_trailer_vehicles']) && $this->_options_2['hide_trailer_vehicles'] === 'on' ? 'true' : null;
			$filterObject->HideClassicVehicles = isset($this->_options_2['hide_classic_vehicles']) && $this->_options_2['hide_classic_vehicles'] === 'on' ? 'true' : null;
			$filterObject->HideTractorVehicles = isset($this->_options_2['hide_tractor_vehicles']) && $this->_options_2['hide_tractor_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideCommissionVehicles = isset($this->_options_2['hide_commission_vehicles']) && $this->_options_2['hide_commission_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideExportVehicles = isset($this->_options_2['hide_export_vehicles']) && $this->_options_2['hide_export_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideByTypeCar = isset($this->_options_2['hide_typecar_vehicles']) && $this->_options_2['hide_typecar_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideByTypeVan = isset($this->_options_2['hide_typevan_vehicles']) && $this->_options_2['hide_typevan_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideByTypeMotorcycle = isset($this->_options_2['hide_typemotorcycle_vehicles']) && $this->_options_2['hide_typemotorcycle_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideByTypeTruck = isset($this->_options_2['hide_typetruck_vehicles']) && $this->_options_2['hide_typetruck_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideByTypeBus = isset($this->_options_2['hide_typebus_vehicles']) && $this->_options_2['hide_typebus_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideBrandNewVehicles = isset($this->_options_2['hide_brandnew_vehicles']) && $this->_options_2['hide_brandnew_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideADVehicles = isset($this->_options_2['hide_ad_vehicles']) && $this->_options_2['hide_ad_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideBIVehicles = isset($this->_options_2['hide_bi_vehicles']) && $this->_options_2['hide_bi_vehicles'] === 'on' ? 'true' : null;
            //jlk
            $filterObject->HideInternalVehiclesBilInfo = isset($this->_options_2['hide_internal_vehicles_bilinfo']) && $this->_options_2['hide_internal_vehicles_bilinfo'] === 'on' ? 'true' : null;			

            if(isset($this->_options_2['bdt_pricetypes']) && $this->_options_2['bdt_pricetypes'] !== '-1')
            {
                $filterObject->PriceTypes = array($this->_options_2['bdt_pricetypes']);
            }

			//jlk
            if(isset($this->_options_2['bdt_propellanttypes']) && $this->_options_2['bdt_propellanttypes'] !== '-1')
            {
                //$filterObject->PropellantTypes = array($this->_options_2['bdt_propellanttypes']);
				$filterObject->Propellants = array($this->_options_2['bdt_propellanttypes']);
            }

            return $filterObject;
        }

        // Preload pages in list
        public function rocket_preload_page ( $pages_to_preload, $args, $type)
        {

            $responseToReturn = array();

            foreach( $pages_to_preload as $page_to_preload )
            {
                $response = wp_remote_get( esc_url_raw ( $page_to_preload ), $args );

                $message = null;

                if(is_wp_error($response) || !isset($response['response']))
                {
                    $message = $response->get_error_message() . " " . $page_to_preload . ".";
                }
                else
                {
                    $message = $response['response']['code'] . " " . $page_to_preload . ".";
                }

                if($message !== null)
                {
                    array_push($responseToReturn, $message);
                }

                // To avoid DDOS'ing ourselves we wait a moment before sending the next preload request (Only for type 'all')
                if($type === 'all')
                {
                    sleep(1);
                }
            }

            return $responseToReturn;
        }
    }

