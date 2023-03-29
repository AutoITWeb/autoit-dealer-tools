<?php

use Biltorvet\Controller\PriceController;
use Biltorvet\Factory\VehicleFactory;
use Biltorvet\Model\Property;
use Biltorvet\Helper\DataHelper;
use Biltorvet\Model\Vehicle;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly
    
    class BiltorvetShortcodes {
        public $biltorvetAPI;
        public $_options;
        public $_options_2;
        public $_options_3;
        public $_options_4;
        public $_options_5;
        public $currentVehicle;

        public function __construct($biltorvetAPI, $options, $options_2, $options_3, $options_4, $options_5)
        {
            if ($options === null) {
                throw new Exception(__('No options provided.', 'biltorvet-dealer-tools'));
            }
            if ($biltorvetAPI === null) {
                throw new Exception(__('No Biltorvet API instance provided.', 'biltorvet-dealer-tools'));
            }
            $this->_options = $options;
            $this->_options_2 = $options_2;
            $this->_options_3 = $options_3;
            $this->_options_4 = $options_4;
            $this->_options_5 = $options_5;
            $this->biltorvetAPI = $biltorvetAPI;

            add_action('parse_query', array(&$this, 'bdt_get_current_vehicle'), 1000);
            add_shortcode('bdt_cta', array($this, 'bdt_shortcode_detail_cta'));
            add_shortcode('bdt_prop', array($this, 'bdt_shortcode_detail_property'));
            add_shortcode('bdt_specifications', array($this, 'bdt_shortcode_specifications'));
            add_shortcode('bdt_additional_equipment', array($this, 'bdt_shortcode_additional_equipment'));
            add_shortcode('bdt_equipment', array($this, 'bdt_shortcode_equipment'));
            add_shortcode('bdt_recommendedvehicles', array($this, 'bdt_shortcode_recommendedvehicles'));
            add_shortcode('bdt_featuredvehicles', array($this, 'bdt_shortcode_featuredvehicles'));
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
            add_shortcode('bdt_sharethis', array($this, 'bdt_shortcode_sharethis'));
            add_shortcode('bdt_map', array($this, 'bdt_shortcode_map'));
            add_shortcode('bdt_get_vehicleid', array($this, 'bdt_shortcode_vehicleid'));
            add_shortcode('bdt_findleasing_calculator', array($this, 'bdt_shortcode_findleasing_calculator'));
            add_shortcode('bdt_amount_of_biltorvet_ads', array($this, 'bdt_shortcode_amount_of_biltorvet_ads'));
            add_shortcode('bdt_jyffi_calculator', array($this, 'bdt_jyffi_calculator_dev'));

            add_action('wp_head', array(&$this, 'bdt_insert_map_dependencies'), 1000);
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

        public function bdt_shortcode_sharethis() {

            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

            $make = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'makeName', true);
            $model = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'model', true);
            $company = $this->currentVehicle->company->name;

            $company = str_replace("&", "og", $company);

            $array = array($make, $model, $company);

            $subject = vsprintf(__('Take a look at this %s %s from %s', 'biltorvet-dealer-tools'), $array);
            $body = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            return '<a href="http://www.facebook.com/sharer.php?u=' . $body . '" onclick="window.open(this.href, \'facebookwindow\',\'left=20,top=20,width=600,height=700,toolbar=0,resizable=1\'); return false;"><img src="https://www.autoit.dk/media/autoit-dealer-tools/facebook.svg" class="bdt_sharethis" height="30" width="30" /></a><a href="mailto:indsæt_email_adresse@her.dk?subject=' . $subject . '&body=' . $body . '"><img src="https://www.autoit.dk/media/autoit-dealer-tools/email.svg" class="bdt_sharethis" height="30" width="30" /></a><a href="#" onclick="window.print();"><img src="https://www.autoit.dk/media/autoit-dealer-tools/print.svg" class="bdt_sharethis" height="30" width="30" /></a>';

        }

        public function bdt_insert_map_dependencies()
        {
            if(isset($this->_options_4['activate_map']) && $this->_options_4['activate_map'] === 'on') {

                ?>

                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
                <script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>

                <script type="application/javascript" src="https://unpkg.com/leaflet-providers@1.13.0/leaflet-providers.js"></script>

                <link rel="stylesheet" href="https://unpkg.com/leaflet-gesture-handling@1.2.2/dist/leaflet-gesture-handling.min.css" />
                <script type="application/javascript" src="https://unpkg.com/leaflet-gesture-handling@1.2.2/dist/leaflet-gesture-handling.min.js"></script>

                <?php

            }
        }

        public function bdt_shortcode_amount_of_biltorvet_ads($atts)
        {
            $get_dealer_info = $this->biltorvetAPI->GetBiltorvetBmsDealerInfo();

            if(isset($get_dealer_info))
            {
                $amount_of_ads = $get_dealer_info->amountOfAdsOnBiltorvet;

                return $amount_of_ads;
            }

            return "Dealer doesn't have any active ads on Biltorvet.dk";
        }

        public function bdt_shortcode_map( $atts)
        {
            extract(shortcode_atts(array(
                    'detailspage' => 'detailspage'
            ), $atts));

            ob_start();
            require Biltorvet::bdt_get_template("Map.php");
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        
        public function bdt_shortcode_vehicle_search( $atts ){
            wp_enqueue_style("bdt_style");
            wp_enqueue_script("select2");
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
            wp_enqueue_script("select2");
            wp_enqueue_script("bdt_script");
            wp_enqueue_script("search_script");

            ob_start();
            require Biltorvet::bdt_get_template("VehicleSearchFrontPage.php");
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }

        public function bdt_shortcode_vehicle_search_results( $atts ){
            wp_enqueue_style("animate");
            wp_enqueue_style("bdt_style");
            wp_enqueue_script("bdt_script");
            wp_enqueue_script("lazy_load");
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

       public function bdt_jyffi_calculator_dev($atts) {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

            $validVehicleTypes = array('Personbil', 'Varebil');

           $price = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'Price', true);

            if((int) $price <= 49999)
            {
                return '<div id="bdt-jyffi-calculator-error" style="display:none;">Vehicle missing cash price</div>';
            }

            if(!in_array($this->currentVehicle->type, $validVehicleTypes))
            {
                return '<div id="bdt-jyffi-calculator-error" style="display:none;">Invalid vehicle type</div>';
            }

           $getFirstRegistrationDate = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'FirstRegistrationDate', true);

           $currenteDateMinusOneMonth = date("Y-m-d", strtotime("-1 month"));

           $firstRegDateFormatted = $getFirstRegistrationDate !== null ? "'$getFirstRegistrationDate'" : "'$currenteDateMinusOneMonth'";

           $dealerId = isset($atts['dealer-id']) ? $atts['dealer-id'] : 1615285056784;

           $jyffiWidget = '<div data-btcontentid="C61662EE-238C-46D7-943B-0CCE15D20181" data-btsettings-price="' . $price . '" data-btsettings-first-registration-date="' . $firstRegDateFormatted . '" data-btsettings-dealer-id="' . $dealerId . '" class="btEmbeddedWidget"></div>';

           return $jyffiWidget;
        }

        public function bdt_shortcode_slideshow() {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

            wp_enqueue_style('bdt_style');
            wp_enqueue_script('bt_slideshow');
            $slideCount = count($this->currentVehicle->images);
            $slides = '';
            $i = 0;

            $showThumbnails = isset($this->_options_3['bdt_show_thumbnails_details']) ? true : false;

            $setDataScale = isset($this->_options_3['bdt_set_data_scale_details']) ? $this->_options_3['bdt_set_data_scale_details'] : "4:3";

            $setDataShowThumbnails = $showThumbnails == true? 1 : 0;

            if(isset($this->currentVehicle->videos))
            {
                $slideCount += count($this->currentVehicle->videos);
                foreach($this->currentVehicle->videos as $video)
                {
                    $slides .= '<div class="bt-slideshow-video' . ($i == 0 ? ' bt-slideshow-active' : '') . '" >' . '<div class="bt-videoplaceholder" data-vimeo-background="0" data-vimeo-autoplay="0" data-vimeo-loop="0" data-vimeo-muted="0" data-vimeo-id="https://vimeo.com/' . $video->vimeoId . '" id="bdt' . $video->vimeoId . '"></div><a class="bt-slideshow-playpause"><span class="bt-slideshow-centericon"><span class="bticon bticon-Play"></span></span></a></div>';
                    $i++;
                }
            }

            $altTag = "Billede af " . $this->currentVehicle->makeName . " " . $this->currentVehicle->model . " " . $this->currentVehicle->variant;

            foreach($this->currentVehicle->images as $image)
            {
                $slides .= '<img loading=lazy src="' . $image . '"' . ($i == 0 ? ' class="bt-slideshow-active"' : '') . ' alt="' . $altTag . '">';
                $i++;
            }
            $buildSlideShow = '';

            $buildSlideShow .= '<section class="bt-slideshow mb-4" data-scale="' . $setDataScale . '" data-showthumbnails="' . $setDataShowThumbnails .'">';
            $buildSlideShow .= '<div class="bt-slideshow-viewport" style="display:none;">';
            $buildSlideShow .= $slides;
            $buildSlideShow .= '<span><span class="bt-slideshow-current">1</span>/<span class="bt-slideshow-count">' . $slideCount .'</span></span>';
            $buildSlideShow .= '</span></div>';

            // Thumbnails
            if($showThumbnails)
            {
                $j = 0;

                $thumbnails = '';

                if(isset($this->currentVehicle->thumbnails->videos))
                {
                    foreach($this->currentVehicle->thumbnails->videos as $video)
                    {
                        $thumbnails  .= '<li class="' . ($j == 0 ? 'bt-slideshow-thumbnail-active' : '') .'"><a href="#"><img loading=lazy src="' . $video . '" width="80" alt="' . $altTag . '"></a></li>';
                        $j++;
                    }
                }

                foreach($this->currentVehicle->thumbnails->images as $image)
                {
                    $thumbnails  .= '<li class="' . ($j == 0 ? 'bt-slideshow-thumbnail-active' : '') .'"><a href="#"><img loading=lazy src="' . $image . '" width="80" alt="' . $altTag . '"></a></li>';
                    $j++;
                }

                $buildSlideShow .= '<div class="bt-slideshow-thumbnails" style="display: none;"><ul>';
                $buildSlideShow .= $thumbnails;
                $buildSlideShow .= '</ul></div>';
            }

            $buildSlideShow .= '</section>';

            return $buildSlideShow;
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
                    // "Fabriksny"
                    case 99999: $badgeType = 'badge-purple'; break;
                    // "Lagersalg"
                    case 26: $badgeType = 'badge-secondary'; break;
                    // "I fokus"
                    case 10: $badgeType = 'badge-orange'; break;
                    // "Kun engros
                    case 9: $badgeType = 'badge-lightblue'; break;
                }
                $labels .= '<span class="badge ' . $badgeType. ' mr-2 mb-1">' . $label->value . '</span>';
            }

            return '<div class="bdt">' . $labels . '</div>';
        }

        public function bdt_shortcode_vehicletotalcount()
        {
            try {
                $filterObject = null;
                if(isset($this->_options_2['hide_sold_vehicles']) && $this->_options_2['hide_sold_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideSoldVehicles = 'true';
                }
                if(isset($this->_options_2['hide_leasing_vehicles']) && $this->_options_2['hide_leasing_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideLeasingVehicles = 'true';
                }
                if(isset($this->_options_2['hide_flexleasing_vehicles']) && $this->_options_2['hide_flexleasing_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideFlexLeasingVehicles = 'true';
                }
                if(isset($this->_options_2['hide_warehousesale_vehicles']) && $this->_options_2['hide_warehousesale_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideWarehousesaleVehicles = 'true';
                }
                if(isset($this->_options_2['hide_carlite_dealer_label_vehicles']) && $this->_options_2['hide_carlite_dealer_label_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideCarLiteDealerLabelVehicles = 'true';
                }
                if(isset($this->_options_2['hide_export_vehicles']) && $this->_options_2['hide_export_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideExportVehicles = 'true';
                }
                if(isset($this->_options_2['hide_upcoming_vehicles']) && $this->_options_2['hide_upcoming_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideUpcomingVehicles = 'true';
                }
                if(isset($this->_options_2['hide_rental_vehicles']) && $this->_options_2['hide_rental_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideRentalVehicles = 'true';
                }
                if(isset($this->_options_2['hide_commission_vehicle']) && $this->_options_2['hide_commission_vehicle'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideCommissionVehicles = 'true';
                }
                if(isset($this->_options_2['hide_wholesale_vehicles']) && $this->_options_2['hide_wholesale_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideWholesaleVehicles = 'true';
                }
                if(isset($this->_options_2['hide_trailer_vehicles']) && $this->_options_2['hide_trailer_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideTrailerVehicles = 'true';
                }
                if(isset($this->_options_2['hide_typecar_vehicles']) && $this->_options_2['hide_typecar_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideByTypeCar = 'true';
                }
                if(isset($this->_options_2['hide_typevan_vehicles']) && $this->_options_2['hide_typevan_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideByTypeVan = 'true';
                }
                if(isset($this->_options_2['hide_typemotorcycle_vehicles']) && $this->_options_2['hide_typemotorcycle_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideByTypeMotorcycle = 'true';
                }
                if(isset($this->_options_2['hide_typetruck_vehicles']) && $this->_options_2['hide_typetruck_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideByTypeTruck = 'true';
                }
                if(isset($this->_options_2['hide_typebus_vehicles']) && $this->_options_2['hide_typebus_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideByTypeBus = 'true';
                }
                if(isset($this->_options_2['hide_brandnew_vehicles']) && $this->_options_2['hide_brandnew_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideBrandNewVehicles = 'true';
                }
                if(isset($this->_options_2['hide_ad_vehicles']) && $this->_options_2['hide_ad_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideADVehicles = 'true';
                }
                if(isset($this->_options_2['hide_bi_vehicles']) && $this->_options_2['hide_bi_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideBIVehicles = 'true';
                }
                if(isset($this->_options_2['bdt_pricetypes']) && $this->_options_2['bdt_pricetypes'] != "-1")
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->PriceTypes = array($this->_options_2['bdt_pricetypes']);
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
         * This function will redirect to the 404 template if currentVehicle is null
         */
        public function bdt_shortcode_vehicleprice()
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                global $wp_query;
                $wp_query->set_404();
                status_header( 404 );
                get_template_part( 404 );
                exit();
            }

            /**
             * Tracking of hits on the detailspage - sends a call to our influx db endpoint
             */
            if(isset($_SERVER['HTTP_USER_AGENT']) && !preg_match('/bot|crawl|slurp|spider|facebook|semrush|bing|ecosia|yandex|duckduck|AdsBot|slack|twitter|whatsapp|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])) {

                $this->biltorvetAPI->SendInfluxDbVehicleData($this->currentVehicle->documentId);
            }

            $priceController = new PriceController(VehicleFactory::create(json_decode(json_encode($this->currentVehicle), true)));

            $showPrice = '<span class="bdt_price_container">';
            if ($priceController->GetPrimaryPrice('details')) {
                $showPrice .='<span class="primary-price">' .$priceController->GetPrimaryPrice('details') .'</span>';
            }
            if ($priceController->GetSecondaryPrice('details')) {
                $showPrice .= '<span class="secondary-price">'. $priceController->GetSecondaryPrice('details') .'</span>';
            }
            if ($priceController->GetTertiaryPrice('details')) {
                $showPrice .= '<span class="tertiary-price">' . $priceController->GetTertiaryPrice('details') .'</span>';
            }
            $showPrice .= '</span>';

            return $showPrice;
        }

        public function bdt_shortcode_vehicleid($atts)
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

            $vehicleId = $this->currentVehicle->id;

            if(isset($atts['int'])) {
                return (int)$vehicleId;
            } else if (isset($atts['string'])) {
                return strval($vehicleId);
            } else {
                return $this->currentVehicle->id;
            }
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

            if($propertyName == 'companyAddress') {
                return nl2br('<span><span class="bdt_vehicle_company_street">' . $this->currentVehicle->company->address . '</span><span class="bdt_vehicle_company_postalcode_and_city">' . $this->currentVehicle->company->postNumber . " " . $this->currentVehicle->company->city . '</span></span>');
            }

            $value = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, $propertyName, isset($atts['raw']));

            // Vehicledetail breadcrumb
            if($propertyName == 'uri')
            {
                $vehicleDetailBreadCrumb = str_replace("/", " ", $value);

                ?>

                <script>
                    $( document ).ready(function() {

                        try {
                            var x = document.getElementsByClassName("current");
                            x[0].innerHTML = '<?= $vehicleDetailBreadCrumb; ?>';
                        }
                        catch (err) {
                            console.log(err);
                        }
                    });
                </script>

                <?php

                return;
            }

            if($propertyName == 'BilInfoVideo')
            {
                if(!is_null($this->currentVehicle->bilInfoVideos) || !empty($this->currentVehicle->bilInfoVideos))
                {
                    $bilInfoVideo = '<div class="bilInfoVideo">';

                    $bilInfoVideo .= '<iframe id="vzvd-infoVid" name="vzvd-infoVid" title="video player" class="video-player" type="text/html" width="100%" height="400px" style="max-width:100%; max-height:auto;" frameborder="0" allowFullScreen allowTransparency="true" mozallowfullscreen webkitAllowFullScreen src="'. $this->currentVehicle->bilInfoVideos[0] . '"></iframe>';
                    $bilInfoVideo .= '<script src="https://player.vzaar.com/libs/flashtakt/client.js" type="text/javascript"></script>';
                    $bilInfoVideo .= '<script> var vzp = new vzPlayer("vzvd-infoVid"); $(".bilInfoVideo").on("click", (el) => { vzp.pause() });</script>';

                    $bilInfoVideo .= '</div>';

                    return $bilInfoVideo;
                }

                return "";
            }

            return isset($value) && trim($value) !== '' ? nl2br($value) : (isset($atts['nona']) ? $atts['nona'] : __('N/A', 'biltorvet-dealer-tools'));
        }

        public function bdt_shortcode_additional_equipment() {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

            ob_start();
            require Biltorvet::bdt_get_template("AdditionalEquipment.php");
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
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

            ob_start();
            ?>
            <div class="bdt">
                <div class="vehicle_search_results">
                    <div class="row">
                        <?php

                        $bdt_root_url = rtrim(get_permalink($this->_options['vehiclesearch_page_id']),'/');

                        $iVehicle = 1;
                            foreach($vehicleFeed->vehicles as $oVehicle)
                            {
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

        public function bdt_shortcode_featuredvehicles( $atts )
        {
            $atts = shortcode_atts( array(
                'show' => 3,
                'type' => null
            ), $atts, 'bdt_featuredvehicles');

            $amount = esc_attr($atts['show']);
            $vehicleType = esc_attr($atts['type']);

            try{
                $vehicleFeed = $this->biltorvetAPI->GetFeaturedVehicles($amount, $vehicleType);
            } catch(Exception $e) {
                return $e->getMessage();
            }
            wp_enqueue_style("bdt_style");

            if(!isset($this->_options) && !isset($this->_options['vehiclesearch_page_id'])) {
                return '<!-- Cannot load Biltorvet featured vehicles: no root page (Vehicle search) has been set! -->';
            }

            $bdt_root_url = rtrim(get_permalink($this->_options['vehiclesearch_page_id']),'/');

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
                $content = __('Back to search results', 'biltorvet-dealer-tools');
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

            if(isset($this->_options) && isset($this->_options['vehiclesearch_page_id'])) {
                $bdt_root_url = rtrim(get_permalink($this->_options['vehiclesearch_page_id']), '/');
            } else {
                return '<!-- Cannot load Biltorvet featured vehicles: no root page (Vehicle search) has been set! -->';
            }

            ob_start();

            // @TODO: Refactor.
            // For new we convert the old vehicle object to the new, so it works with the new templates
            // PLUGIN_ROOT refers to the v2 root.

            /** @var Vehicle $vehicle */
            $vehicle = VehicleFactory::create(json_decode(json_encode($vehicle), true));
            /** @var Property[] $vehicleProperties */
            $vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
            $priceController = new PriceController($vehicle);
            $basePage = $bdt_root_url;

            require PLUGIN_ROOT . 'templates/partials/_vehicleCard.php';
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        public function bdt_shortcode_findleasing_calculator()
        {
            if(isset($this->currentVehicle) && $this->currentVehicle !== null)
            {
                $vehicle = $this->currentVehicle;

                $findLeasingExternalId = $this->biltorvetAPI->GetPropertyValue($vehicle, 'FindLeasingExternalId');

                if($findLeasingExternalId !== null)
                {
                    $findLeasingCalculator = "<h2 class='findleasing-beregner'>Beregn leasingpris</h2>";
                    $findLeasingCalculator .= '<div id="findleasing-sliders-embed-div" data-findleasing data-width="100%" data-id="' . $findLeasingExternalId . '"></div>';
                    $findLeasingCalculator .= '<script src="https://www.findleasing.nu/static/javascript/embed-sliders.js"></script>';

                    return $findLeasingCalculator;
                }
            }
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
                                $getFirstRegistrationDate = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'FirstRegistrationDate', true);
                                //$paymentTerms = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'FinancingRunTime', true) ?? '';

                                $firstRegistrationDate = $getFirstRegistrationDate != null && $this->currentVehicle->brandNew == false ? $getFirstRegistrationDate : strval(date("Y-m-d"));

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
                                    ($this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'FinancingRunTime', true) !== null ? 'data-btsettings-paymentTerms="' . intval($this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'FinancingRunTime', true)) . '" ' : '') .
                                    ($sold ? 'data-btsettings-isVehicleSold="true" ' : '') .
                                    (isset($atts['brandingid']) ? 'data-btsettings-brandingId="' . intval($atts['brandingid']) . '" ' : '') .
                                    (isset($atts['hidevehicleprice']) ? 'data-btsettings-hideVehiclePrice="true" ' : '') .
                                    (isset($atts['downpaymentratio']) ?  'data-btsettings-dataDownPayment="' . (intval($atts['downpaymentratio'])*intval($price)) . '" ' : '' );
                            }
                        }
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
