<?php

use Biltorvet\Controller\PriceController;
use Biltorvet\Factory\VehicleFactory;
use Biltorvet\Helper\DataHelper;
use Biltorvet\Helper\WordpressHelper;
use Biltorvet\Model\Vehicle;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly
    
    class BiltorvetShortcodes {
        public $biltorvetAPI;
        public $_options;
        public $currentVehicle;

        public function __construct($biltorvetAPI, $options)
        {
            if ($options === null) {
                throw new Exception(__('No options provided.', 'biltorvet-dealer-tools'));
            }
            if ($biltorvetAPI === null) {
                throw new Exception(__('No Biltorvet API instance provided.', 'biltorvet-dealer-tools'));
            }
            $this->_options = $options;
            $this->biltorvetAPI = $biltorvetAPI;

            add_action('parse_query', array(&$this, 'bdt_get_current_vehicle'), 1000);
            add_shortcode('bdt_cta', array($this, 'bdt_shortcode_detail_cta'));
            add_shortcode('bdt_prop', array($this, 'bdt_shortcode_detail_property'));
            add_shortcode('bdt_specifications', array($this, 'bdt_shortcode_specifications'));
            add_shortcode('bdt_equipment', array($this, 'bdt_shortcode_equipment'));
            add_shortcode('bdt_recommendedvehicles', array($this, 'bdt_shortcode_recommendedvehicles'));
//            add_shortcode('bdt_featuredvehicles', array($this, 'bdt_shortcode_featuredvehicles'));
            add_shortcode('bdt_slideshow', array($this, 'bdt_shortcode_slideshow'));
            add_shortcode('bdt_vehicle_price', array($this, 'bdt_shortcode_vehicleprice'));
            add_shortcode('bdt_vehicle_labels', array($this, 'bdt_shortcode_vehiclelabels'));
            add_shortcode('bdt_vehicletotalcount', array($this, 'bdt_shortcode_vehicletotalcount'));
            add_shortcode('bdt_vehicle_search', array($this, 'bdt_shortcode_vehicle_search'));
            add_shortcode('bdt_vehicle_search_frontpage', array($this, 'bdt_shortcode_vehicle_search_frontpage'));
            add_shortcode('bdt_vehicle_search_results', array($this, 'bdt_shortcode_vehicle_search_results'));
            add_shortcode('bdt_vehicle_card', array($this, 'bdt_shortcode_vehicle_card'));
            add_shortcode('bdt_vehicle_search_backtoresults', array($this, 'bdt_shortcode_vehicle_search_backtoresults'));
            add_shortcode('bdt_widget', array($this, 'bdt_shortcode_widget'));
        }

        public function bdt_get_current_vehicle()
        {
            try{
                $vehicleId = $this->ResolveVehicleId();
                if($vehicleId !== -1)
                {
                    $this->currentVehicle = $this->biltorvetAPI->GetVehicle($vehicleId);
                }
            } catch(Exception $e) {
                return $e->getMessage();
            }
        }
        
        public function bdt_shortcode_vehicle_search( $atts ){
            wp_enqueue_style("bdt_style");
            wp_enqueue_script("bdt_script");
            wp_enqueue_script("search_script");
            ob_start();
            require Biltorvet::bdt_get_template("VehicleSearch.php");
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }

        public function bdt_shortcode_vehicle_search_frontpage( $atts ){
            wp_enqueue_style("bdt_style");
            wp_enqueue_script("bdt_script");
            wp_enqueue_script("search_script");
            ob_start();
            require Biltorvet::bdt_get_template("VehicleSearchFrontPage.php");
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }

        public function bdt_shortcode_vehicle_search_results( $atts ){
            wp_enqueue_style("bdt_style");
            wp_enqueue_script("bdt_script");
            ob_start();
            require Biltorvet::bdt_get_template("VehicleSearchResults.php");
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }

        public function bdt_vehicle_not_found() {
            status_header( '404' );
            locate_template( array ( '404.php', 'index.php ' ), TRUE, TRUE );
            exit;
        }

        public function bdt_shortcode_slideshow() {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }
            wp_enqueue_style('bdt_style');
            wp_enqueue_style('bt_slideshow');
            wp_enqueue_script('bt_slideshow');
            $root = dirname(plugin_dir_url( __FILE__ ));
            $slideCount = count($this->currentVehicle->images);
            $slides = '';
            $i = 0;
            if(isset($this->currentVehicle->videos))
            {
                $slideCount += count($this->currentVehicle->videos);
                foreach($this->currentVehicle->videos as $video)
                {
                    $slides .= '<div class="bt-slideshow-video' . ($i == 0 ? ' bt-slideshow-active' : '') . '" data-vimeo-background="1" data-vimeo-id="' . $video->vimeoId . '" data-vimeo-width="640" id="bdt' . $video->vimeoId . '"><a class="bt-slideshow-play"><span class="bticon bticon-Play"></span></a></div>';
                    $i++;
                }
            }
            foreach($this->currentVehicle->images as $image)
            {
                $slides .= '<img src="' . $image . '"' . ($i == 0 ? ' class="bt-slideshow-active"' : '') . ' alt="">';
                $i++;
            }
            $iconGalleryArrowLeft = '<span class="bticon bticon-GalleryArrowLeft"></span>';
            $iconGalleryArrowRight = '<span class="bticon bticon-GalleryArrowRight"></span>';
            $iconGalleryFullscreen = '<span class="bticon bticon-GalleryFullscreen"></span>';
            return '<div class="bdt"><section class="bt-slideshow bt-slideshow-4to3"><div class="bt-slideshow-skidboard d-none"></div><a href="#" class="bt-slideshow-prev">' .$iconGalleryArrowLeft. '</a><a href="#" class="bt-slideshow-next">' .$iconGalleryArrowRight. '</a><div class="bt-slideshow-viewport">' . $slides . '</div><div class="bt-slideshow-controls"><span class="bt-slideshow-bg"><a href="#" class="bt-slideshow-open-fullscreen">' .$iconGalleryFullscreen. '</a> <a href="#" class="bt-slideshow-pause-video d-none"><span class="bticon bticon-Pause"></span></a> <span><span class="bt-slideshow-current">1</span>/<span class="bt-slideshow-count">' . $slideCount . '</span></span></span></div></section></div>';
        }

        public function bdt_shortcode_vehiclelabels( $atts ) 
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }
            wp_enqueue_style("bdt_style");
            $allowedLabels = null;
            if(isset($atts['allowed']) && trim($atts['allowed']) !== '')
            {
                $allowedLabels = explode(',', $atts['allowed']);
            }
            $labels = '';
            foreach($this->currentVehicle->labels as $label)
            {
                if($allowedLabels !== null)
                {
                    if(!in_array($label->value, $allowedLabels))
                    {
                        continue;
                    }
                }
                $badgeType = 'badge-primary';
                switch($label->key)
                {
                    // "Solgt"
                    case 5: $badgeType = 'badge-danger'; break;
                    // "Nyhed"
                    case 11: $badgeType = 'badge-success'; break;
                    // "Leasing"
                    case 12: $badgeType = 'badge-info'; break;
                    // "Eksport"
                    case 382: $badgeType = 'badge-warning'; break;
                    // "Uden afgift"
                    case 359: $badgeType = 'badge-dark'; break;
                    // "Udlejning"
                    case 2: $badgeType = 'badge-purple'; break;
                    // "Lagersalg"
                    case 26: $badgeType = 'badge-secondary'; break;
                    // "I fokus"
                    case 10: $badgeType = 'badge-orange'; break;
                }
                $labels .= '<span class="badge ' . $badgeType. ' mr-2 mb-1">' . $label->value . '</span>';
            }
            return '<div class="bdt">' . $labels . '</div>';
        }

        public function bdt_shortcode_vehicletotalcount()
        {
            try {
                $filterObject = null;
                if(isset($this->_options['hide_sold_vehicles']) && $this->_options['hide_sold_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideSoldVehicles = 'true';
                }
                if(isset($this->_options['hide_ad_vehicles']) && $this->_options['hide_ad_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideADVehicles = 'true';
                }
                if(isset($this->_options['hide_bi_vehicles']) && $this->_options['hide_bi_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideBIVehicles = 'true';
                }
                return $this->biltorvetAPI->GetVehicleTotalCount($filterObject);
            } catch(Exception $e) {
                return $e->getMessage();
            }
        }

        public function bdt_shortcode_detail_cta( $atts, $content = null )
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

            global $ActivityType;
            $root = dirname(plugin_dir_url( __FILE__ ));
            if(!isset($atts['type']))
            {
                return __('Shortcode\'s CTA type has not been set.', 'biltorvet-dealer-tools');
            }

            if(!in_array($atts['type'], $ActivityType))
            {
                return sprintf( __('Unrecognized CTA type. Allowed types: %s', 'biltorvet-dealer-tools'), implode(', ', $ActivityType));
            }
            $customColor = null;
            if(isset($atts['color']))
            {
                $customColor = TextUtils::SanitizeHTMLColor($atts['color']);
            }



            if($content == null)
            {
                switch($atts['type']):
                    case 'TestDrive':
                        $content = '<span class="bticon bticon-TestDrive"></span><br>' . __('Testdrive', 'biltorvet-dealer-tools');
                    break;
                    case 'Email':
                        $content = '<span class="bticon bticon-WriteUs"></span><br>' . __('Write us', 'biltorvet-dealer-tools');
                    break;
                    case 'Purchase':
                        $content = '<span class="bticon bticon-BuyCar"></span><br>' . __('Buy this car', 'biltorvet-dealer-tools');
                    break;
                    case 'Contact':
                        if($this->currentVehicle->company->phone != null) {
                            $content = '<span class="bticon bticon-CallUs"></span><br>' . $this->currentVehicle->company->phone;
                        }
                        else {
                            $content = '<span class="bticon bticon-CallUs"></span><br>' . __('Call us', 'biltorvet-dealer-tools');
                        }
                    break;
                    case 'PhoneCall':
                        $content = '<span class="bticon bticon-CallBack"></span><br>' . __('Let us call you back', 'biltorvet-dealer-tools');
                    break;
                endswitch;
            }
            $id = TextUtils::Sanitize($atts['type']);
            $root = get_home_url();
            $bdt_contact_page_id = null;
            if(isset($this->_options['contact_page_id']))
            {
                $bdt_contact_page_id = $this->_options['contact_page_id'];
            }
            if(($atts['type'] === 'TestDrive' || $atts['type'] === 'Purchase') && isset($this->_options['booking_page_id']))
            {
                $bdt_contact_page_id = $this->_options['booking_page_id'];
            }
            if(isset($bdt_contact_page_id))
            {
                $root = get_permalink($bdt_contact_page_id);
            }

            if(($atts['type']) === 'Contact')
            {
                if($this->currentVehicle->company->phone != null) {
                    return (isset($style) ? $style : '') . '<a id="'. $id .'" href="tel:' . $this->currentVehicle->company->phone . '" class="bdt_cta '.(isset($customColor) && $customColor !== null ? 'donottint ' : '') . (isset($atts['class']) ? ' ' . esc_attr($atts['class']) : '') .'">' . $content . '</a>';
                }
            }

            return (isset($style) ? $style : '') . '<a id="'. $id .'" href="'. $root . '?'. http_build_query(array('bdt_actiontype' => $atts['type'], 'bdt_vehicle_id' => $this->currentVehicle->documentId)) . '" class="bdt_cta '.(isset($customColor) && $customColor !== null ? 'donottint ' : '') . (isset($atts['class']) ? ' ' . esc_attr($atts['class']) : '') .'">' . $content . '</a>';
        }

        /**
         * Automaticaly show kontant or leasing + kontant price.
         */
        public function bdt_shortcode_vehicleprice()
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }
            $showPrice = '';
            // @TODO: refactor
            $priceController = new PriceController(VehicleFactory::create(json_decode(json_encode($this->currentVehicle), true)));
            $i = 0;
            foreach ($priceController->getDetailsPrioritizedPrices() as $price) {
                if ($i === 0) {
                    $showPrice = '<span class="bdt_price_mainLabel">' . $price['label'] . '</span>';
                    $showPrice .= '<br/><big class="bdt_price_big">' . $price['price'] . '</big>';
                } else {
                    $showPrice .= '<br/><span class="bdt_price_small">' . $price['label'] . ': ' . $price['price'] . '</span>';
                }
                    $i++;
            }

            return '<span class="bdt_price_container">'. $showPrice .'</span>';
        }

        public function bdt_shortcode_detail_property( $atts )
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }
            $propertyName = $atts['p'];
            if(!isset($propertyName))
            {
                return __('Shortcode\'s vehicle property has not been set.', 'biltorvet-dealer-tools');
            }
            // Company is its own object, so let's create human-friendly string representation of it.
            if($propertyName == 'company')
            {
                return nl2br('<span class="bdt_vehicle_company_name">'. $this->currentVehicle->company->name . "</span>\r\n" . $this->currentVehicle->company->address . "\r\n" . $this->currentVehicle->company->postNumber . " " . $this->currentVehicle->company->city);
            }
            $value = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, $propertyName, isset($atts['raw']));
            return isset($value) && trim($value) !== '' ? nl2br($value) : (isset($atts['nona']) ? $atts['nona'] : __('N/A', 'biltorvet-dealer-tools'));
        }

        public function bdt_shortcode_specifications() {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }
            if(count($this->currentVehicle->properties) === 0)
            {
                return __('N/A', 'biltorvet-dealer-tools');
            }
            $properties = $this->currentVehicle->properties;

            $exclMomsPropIndex = -1;

            for($i = 0; $i < count($properties); $i++)
            {
                if($properties[$i]->id === 'VAT')
                {
                    $exclMomsPropIndex = $i;
                    break;
                }
            }

            if($exclMomsPropIndex !== -1)
            {
                array_splice($properties, $exclMomsPropIndex, 1);
            }

            foreach($properties as $prop)
            {
                // TODO: Refactor - Change the publicName in the API
                // publicName of "Price" changed to show "ink. devcost"
                if($prop->id === 'Price')
                {
                    $prop->publicName = "Pris (ink. lev. omkostninger)";

                }
            }

            return '<div class="bdt">'.TextUtils::GenerateSpecificationsTable($properties).'</div>';
        }

        public function bdt_shortcode_equipment( $atts ) {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }
            $equipmentList = array();
            foreach($this->currentVehicle->equipment as $equipment)
            {
                array_push($equipmentList, array('id' => $equipment->id, 'key' => $equipment->publicName, 'value' => $equipment->valueFormatted));
            }

            if(count($equipmentList) === 0)
            {
                return __('N/A', 'biltorvet-dealer-tools');
            }
            return '<div class="bdt">'.TextUtils::GenerateEquipmentTable($equipmentList).'</div>';
        }

        public function bdt_shortcode_recommendedvehicles( $atts ) {
            $atts = shortcode_atts( array(
                'show' => 3
            ), $atts, 'bdt_recommendedvehicles' );

            $vehicleId = $this->ResolveVehicleId();
            try{
                $vehicleFeed = $this->biltorvetAPI->GetRecommendedVehicles($vehicleId === -1 ? null : $vehicleId, intval($atts['show']));
            } catch(Exception $e) {
                return $e->getMessage();
            }
            $bdt_root_url = rtrim($_SERVER['REQUEST_URI'], '/') . '../..'; // take care of trailing slash, that can be disabled or enabled on the server.
            
            ob_start();
            ?>
            <div class="bdt">
                <div class="vehicle_search_results">
                    <div class="row">
                        <?php
                            $iVehicle = 1;
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

                                if($iVehicle % 3 === 0)
                                {

                                ?></div><div class="row"><?php
                                }
                                $iVehicle++;
                            }
                        ?>
                    </div>
                </div>
            </div>
            <?php
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        // Unused -> New featured vehicles function in v2/src/utility/callbacks.php
        public function bdt_shortcode_featuredvehicles( $atts )
        {
            $atts = shortcode_atts( array(
                'show' => 3
            ), $atts, 'bdt_featuredvehicles' );

            try{
                $vehicleFeed = $this->biltorvetAPI->GetFeaturedVehicles(intval($atts['show']));
            } catch(Exception $e) {
                return $e->getMessage();
            }
            wp_enqueue_style("bdt_style");

            if(isset($this->_options) && isset($this->_options['vehiclesearch_page_id'])) {
                $bdt_root_url = rtrim(get_permalink($this->_options['vehiclesearch_page_id']), '/');
            } else {
                return '<!-- Cannot load Biltorvet featured vehicles: no root page (Vehicle search) has been set! -->';
            }

            ob_start();
            ?>
            <div class="bdt">
                <div class="vehicle_search_results">
                    <div class="row justify-content-center">
                        <?php
                            $iVehicle = 1;
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

                            if($iVehicle % 3 === 0)
                            {

                            ?></div><div class="row"><?php
                            }
                            $iVehicle++;
                            }
                        ?>
                    </div>
                </div>
            </div>
            <?php
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        public function bdt_shortcode_vehicle_search_backtoresults($atts, $content = null)
        {
            $link = get_home_url();
            $content = __('Back to homepage', 'biltorvet-dealer-tools');

            if(isset($this->_options) && isset($this->_options['vehiclesearch_page_id'])) {
                $link = get_permalink($this->_options['vehiclesearch_page_id']);
                $content = __('Back to vehicle search page', 'biltorvet-dealer-tools');
            }
            if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], get_home_url()) !== false)
            {
                $content = __('Back to vehicle search page', 'biltorvet-dealer-tools');
                if(preg_match('/\/\d+\/?$/', $_SERVER['HTTP_REFERER']) === 1) // if there is a page number at the end of the url, it is search results
                {
                    $link = $_SERVER['HTTP_REFERER'];
                    $content = __('Back to search results', 'biltorvet-dealer-tools');
                }
            }

            return '&lt; <a href="' . $link . '"' .(isset($atts) && isset($atts['class']) ? ' class="'.$atts['class'].'"' : ''). '>' . $content .'</a>';
        }

        public function bdt_shortcode_vehicle_card( $atts )
        {
            if(isset($atts) && isset($atts['vehicle']))
            {
                try{
                    $vehicleId = $atts['vehicle'];
                    $vehicle = $this->biltorvetAPI->GetVehicle($vehicleId);
                } catch(Exception $e) {
                    return $e->getMessage();
                }
            } elseif(isset($this->currentVehicle)) {
                $vehicle = $this->currentVehicle;
            }
            
            if(!isset($vehicle) || $vehicle == null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

            wp_enqueue_style("bdt_style");
            $link = null;
            ob_start();
            require Biltorvet::bdt_get_template("_VehicleCard.php");
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        public function bdt_shortcode_widget( $atts, $content = null )
        {
            global $WidgetAutodesktopLeadsActivityTypesEnum;
            global $WidgetType;
            if(!isset($atts['type']))
            {
                return __('No widget type set.', 'biltorvet-dealer-tools');
            }
            if(!in_array($atts['type'], $WidgetType))
            {
                return sprintf( '<!-- BDT: "%s" is an unrecognized Widget type. Allowed types: %s -->', TextUtils::Sanitize($atts['type']), implode(', ', $WidgetType));
            }
            try{
                $products = $this->biltorvetAPI->GetProducts();
            } catch(Exception $e) {
                return $e->getMessage();
            }

            $santanderWidgets = [];

            foreach ($products as $product)
            {
                if($product->name == "Santander")
                {
                    array_push($santanderWidgets, $product);
                }
            }

            foreach ($products as $product)
            {
                if($product->type === 'Widget' && $product->name === TextUtils::Sanitize($atts['type']))
                {
                    // https://santander.autoit.dk/demo.html
                    if($product->name == "Santander")
                    {
                        if(!isset($this->currentVehicle) || $this->currentVehicle === null)
                        {
                            return "<!-- BDT: Santander widget not loaded: no valid vehicle found -->";
                        }

                        foreach ($santanderWidgets as $santander)
                        {
                            if(in_array(intval($this->currentVehicle->company->id), $santander->companyIds, true))
                            {
                                $productKeyAttribute = 'data-btcontentid="' . $santander->key . '"';

                                $make = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'makeName', true);
                                $model = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'model', true);
                                $price = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'Price', true);
                                $variant = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'variant', true);
                                $mileage = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'Mileage', true);
                                $firstRegistrationDate = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'FirstRegistrationDate', true);
                                $sold = false;
                                foreach($this->currentVehicle->labels as $label)
                                {
                                    if($label->key === 5)
                                    {
                                        $sold = true;
                                        break;
                                    }
                                }
                                if(!isset($price))
                                {
                                    return "<!-- BDT: Santander widget not loaded: price not set -->";
                                }
                                if(intval($price) < 50000)
                                {
                                    return "<!-- BDT: Santander widget not loaded: price too low (below 50000dkk) -->";
                                }
                                if(!isset($make))
                                {
                                    return "<!-- BDT: Santander widget not loaded: make not set -->";
                                }
                                if(!isset($model))
                                {
                                    return "<!-- BDT: Santander widget not loaded: model not set -->";
                                }
                                if(!isset($variant))
                                {
                                    return "<!-- BDT: Santander widget not loaded: variant not set -->";
                                }
                                if(!isset($mileage))
                                {
                                    return "<!-- BDT: Santander widget not loaded: mileage not set -->";
                                }
                                if(!isset($firstRegistrationDate))
                                {
                                    return "<!-- BDT: Santander widget not loaded: firstRegistrationDate not set -->";
                                }
                                $widgetAttributes = $productKeyAttribute . ' ' .
                                    (isset($atts['color']) && trim($atts['color']) !== '' ? ' data-btsettings-color="' . TextUtils::SanitizeHTMLColor($atts['color']) . '" ' : (isset($this->_options['primary_color']) && trim($this->_options['primary_color']) !== '' ? ' data-btsettings-color="' . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . '" ' : '')) .
                                    'data-btsettings-price="' . $price . '" ' .
                                    'data-btsettings-make="' . $make . '" ' .
                                    'data-btsettings-model="' . $model . '" ' .
                                    'data-btsettings-variant="' . $variant . '" ' .
                                    'data-btsettings-mileage="' . $mileage . '" ' .
                                    'data-btsettings-firstRegistrationDate="' . $firstRegistrationDate . '" ' .
                                    ($sold ? 'data-btsettings-isVehicleSold="true" ' : '') .
                                    (isset($atts['brandingid']) ? 'data-btsettings-brandingId="' . intval($atts['brandingid']) . '" ' : '') .
                                    (isset($atts['hidevehicleprice']) ? 'data-btsettings-hideVehiclePrice="true" ' : '') .
                                    (isset($atts['downpaymentratio']) ?  'data-btsettings-dataDownPayment="' . (intval($atts['downpaymentratio'])*intval($price)) . '" ' : '' ) .
                                    (isset($atts['paymentterms']) ? 'data-btsettings-paymentTerms="' . intval($atts['paymentterms']) . '" ' : '');
                            }

                        }
                    }

                    if($product->name == "Consent")
                    {
                        $productKeyAttribute = 'data-btcontentid="' . $product->key . '"';

                        // https://services.autoit.dk/Demo
                        $widgetAttributes = $productKeyAttribute . ' ' .
                            (isset($atts['color']) && trim($atts['color']) !== '' ? ' data-btsettings-color="' . TextUtils::SanitizeHTMLColor($atts['color']) . '" ' : (isset($this->_options['primary_color']) && trim($this->_options['primary_color']) !== '' ? ' data-btsettings-color="' . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . '" ' : '')) .
                            (isset($atts['consentcategory']) ?  'data-btsettings-samtykkecategory="' . TextUtils::Sanitize($atts['consentcategory']) . '" ' : '') .
                            (isset($atts['requiredconsenttype']) ?  'data-btsettings-requiredsamtykketype="' . TextUtils::Sanitize($atts['requiredconsenttype']) . '" ' : '') .
                            (isset($atts['name']) ?  'data-btsettings-name="' . TextUtils::Sanitize($atts['name']) . '" ' : '') .
                            (isset($atts['address']) ?  'data-btsettings-address="' . TextUtils::Sanitize($atts['address']) . '" ' : '') .
                            (isset($atts['postalcode']) ?  'data-btsettings-postalcode="' . TextUtils::Sanitize($atts['postalcode']) . '" ' : '') .
                            (isset($atts['city']) ?  'data-btsettings-city="' . TextUtils::Sanitize($atts['city']) . '" ' : '') .
                            (isset($atts['email']) ?  'data-btsettings-email="' . TextUtils::Sanitize($atts['email']) . '" ' : '') .
                            (isset($atts['mobilephone']) ?  'data-btsettings-mobilephone="' . TextUtils::Sanitize($atts['mobilephone']) . '" ' : '');
                    }

                    if($product->name == "ExchangePrice")
                    {
                        $productKeyAttribute = 'data-btcontentid="' . $product->key . '"';

                        // https://services.autoit.dk/?type=VehicleAppraisal&contentId=f7ff6274-1794-4c15-ba4a-27ebc1399bdd&contentData=3a2c05e5-51ac-4fb2-917a-217761f08fd0
                        $externalId = null;
                        if(isset($product->instances) && isset($product->instances[0]))
                        {
                            $externalId = $product->instances[0];
                        }
                        if(isset($atts['externalid']))
                        {
                            $externalId = TextUtils::Sanitize($atts['externalid']);
                        }
                        if($externalId === null)
                        {
                            continue;
                        }

                        $widgetAttributes = $productKeyAttribute . ' ' . ' data-btsettings-settingsguid="' . $externalId . '" ' .
                            (isset($atts['color']) && trim($atts['color']) !== '' ? ' data-btsettings-color="' . TextUtils::SanitizeHTMLColor($atts['color']) . '" ' : (isset($this->_options['primary_color']) && trim($this->_options['primary_color']) !== '' ? ' data-btsettings-color="' . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . '" ' : '')) .
                            (isset($atts['logourl']) ?  'data-btsettings-logourl="' . TextUtils::Sanitize($atts['logourl']) . '" ' : '') .
                            (isset($atts['fontcolor']) ?  'data-btsettings-fontcolor="' . TextUtils::Sanitize($atts['fontcolor']) . '" ' : '');
                    }

                    if($product->name == "AutoDesktopLeads")
                    {
                        $productKeyAttribute = 'data-btcontentid="' . $product->key . '"';

                        // http://services.autoit.dk/?type=AutoDesktopLeads&contentId=371096a5-69c6-4c4b-9df9-4863959cebb9&btSettingsSettingsGuid=12c9310a-751b-4dfe-b9c8-04a7f0b85743
                        $externalId = null;
                        // if(isset($product->instances) && isset($product->instances[0]))
                        // {
                        //     $externalId = $product->instances[0];
                        // }
                        if(isset($atts['guid']))
                        {
                            $externalId = TextUtils::Sanitize($atts['guid']);
                        }
                        if($externalId === null)
                        {
                            return "<!-- guid attribute is currently required for AutoDesktopLeads widget type. -->";
                            continue;
                        }

                        $actiontype = isset($atts['actiontype']) ? TextUtils::Sanitize($atts['actiontype']) : null;
                        if(isset($actiontype) && $actiontype !== '' && !in_array($actiontype, $ActivityType))
                        {
                            return sprintf( __('Unrecognized lead type. Allowed types: %s', 'biltorvet-dealer-tools'), implode(', ',$WidgetAutodesktopLeadsActivityTypesEnum));
                        }
                        $selectedvehicletype = isset($atts['selectedvehicletype']) ? TextUtils::Sanitize($atts['selectedvehicletype']) : null;
                        $allowedmakes = isset($atts['allowedmakes']) ? explode(';', TextUtils::SanitizeText($atts['allowedmakes'])) : null;
                        $filterpersonalmodels = isset($atts['filterpersonalmodels']) ? explode(';', TextUtils::SanitizeText($atts['filterpersonalmodels'])) : null;
                        $filterbusinessmodels = isset($atts['filterbusinessmodels']) ? explode(';', TextUtils::SanitizeText($atts['filterbusinessmodels'])) : null;
                        $selectedmodel = isset($atts['selectedmodel']) ? TextUtils::Sanitize($atts['selectedmodel']) : null;
                        $openingtimes = isset($atts['openingtimes']) ? TextUtils::SanitizeJSON($atts['openingtimes']) : null;
                        // $variant = isset($atts['variant']) ? TextUtils::Sanitize($atts['variant']) : null;
                        // $engineSize =  isset($atts['enginesize']) ? TextUtils::Sanitize($atts['enginesize']) : null;
                        // $month = isset($atts['month']) ? TextUtils::Sanitize($atts['month']) : null;
                        // $year = isset($atts['year']) ? TextUtils::Sanitize($atts['year']) : null;

                        if(isset($this->currentVehicle) && $this->currentVehicle !== null)
                        {
                            $selectedmake = array($this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'makeName', true));
                            $selectedmodel = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'model', true);
                            $selectedvehicletype = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'type', true);
                            // $variant = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'variant', true);
                            // $engineSize = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'EngineSize', true);
                            // $firstRegistrationDate = date_parse($this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'FirstRegistrationDate', true));
                            // $month = $firstRegistrationDate['month'];
                            // $year = $firstRegistrationDate['year'];
                        }

                        $widgetAttributes = $productKeyAttribute . ' ' . ' data-btsettings-guid="' . $externalId . '" ' .
                            (isset($atts['color']) && trim($atts['color']) !== '' ? ' data-btsettings-color="' . TextUtils::SanitizeHTMLColor($atts['color']) . '" ' : (isset($this->_options['primary_color']) && trim($this->_options['primary_color']) !== '' ? ' data-btsettings-color="' . TextUtils::SanitizeHTMLColor($this->_options['primary_color']) . '" ' : '')) .
                            (isset($actiontype) ?  'data-btsettings-actiontype="' . $actiontype . '" ' : '') .
                            (isset($atts['logourl']) ?  'data-btsettings-logourl="' . TextUtils::Sanitize($atts['logourl']) . '" ' : '') .
                            (isset($atts['fontcolor']) ?  'data-btsettings-fontcolor="' . TextUtils::Sanitize($atts['fontcolor']) . '" ' : '') .
                            (isset($selectedvehicletype) ?  'data-btsettings-selectedvehicletype="' . $selectedvehicletype . '" ' : '') .
                            (isset($allowedmakes) ?  'data-btsettings-allowedmakes=\'["' . implode('","', $allowedmakes) . '"]\' ' : '') .
                            (isset($filterpersonalmodels) ?  'data-btsettings-filterpersonalmodels=\'["' . implode('","', $filterpersonalmodels) . '"]\' ' : '') .
                            (isset($filterbusinessmodels) ?  'data-btsettings-filterbusinessmodels=\'["' . implode('","', $filterbusinessmodels) . '"]\' ' : '') .
                            (isset($openingtimes) ?  'data-btsettings-openingtimes=\'' . $openingtimes . '\' ' : '') .
                            (isset($selectedmake) ?  'data-btsettings-selectedmake="' . TextUtils::SanitizeText($selectedmake) . '" ' : '') .
                            (isset($selectedmodel) ?  'data-btsettings-selectedmodel="' . $selectedmodel . '" ' : '') .
                            (isset($atts['vehicletypehide']) ?  'data-btsettings-vehicletypehide="true" ' : '') .
                            (isset($atts['makehide']) ?  'data-btsettings-makehide="true" ' : '') .
                            (isset($atts['modelhide']) ?  'data-btsettings-modelhide="true" ' : '') .
                            (isset($atts['title']) ?  'data-btsettings-title="' . TextUtils::SanitizeText($atts['title']) . '" ' : '');
                            (isset($atts['GTMID']) ?  'data-btsettings-GTMID="' . TextUtils::Sanitize($atts['GTMID']) . '" ' : '');
                            // (isset($variant) ?  'data-btsettings-variant="' . $variant . '" ' : '') .
                            // (isset($engineSize) ?  'data-btsettings-enginesize="' . $engineSize . '" ' : '') .
                            // (isset($month) ?  'data-btsettings-month="' . $month . '" ' : '') .
                            // (isset($year) ?  'data-btsettings-year="' . $year . '" ' : '');
                    }

                    if(!isset($widgetAttributes))
                    {
                        return "<!-- BDT: Widget of type " . TextUtils::Sanitize($atts['type']) . " not found... -->" . $product->key;
                    }

                    if(isset($content) && $content !== null && trim($content) !== '')
                    {
                        return '<a href="#" ' . $widgetAttributes . ' class="btOpenWidget">' . $content . '</a>';
                    }
                    return '<div ' . $widgetAttributes . ' class="btEmbeddedWidget"></div>';
                }
            }
            return "<!-- BDT: Widget of type " . TextUtils::Sanitize($atts['type']) . " not found. -->";
        }

        private function ResolveVehicleId()
        {
            return get_query_var('bdt_vehicle_id', -1);
        }
    }