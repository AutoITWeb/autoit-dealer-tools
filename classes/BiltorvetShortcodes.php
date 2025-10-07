<?php

use Biltorvet\Controller\PriceController;
use Biltorvet\Factory\VehicleFactory;
use Biltorvet\Model\Property;
use Biltorvet\Helper\DataHelper;
use Biltorvet\Model\Vehicle;
//JLK
use Biltorvet\Helper\WordpressHelper;

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
            add_shortcode('bdt_print_gallery', array($this, 'bdt_shortcode_print_gallery'));
			add_shortcode('bdt_get_status_sold', array($this, 'bdt_shortcode_status_sold'));
			add_shortcode('bdt_get_status_onlinekoeb', array($this, 'bdt_shortcode_status_onlinekoeb'));
			add_shortcode('bdt_car_tracking_using_datalayer', array($this, 'bdt_shortcode_car_tracking_using_datalayer'));
			//JLK nye
			add_shortcode('bdt_has_cashPrice', array($this, 'bdt_shortcode_has_cashPrice'));
			add_shortcode('bdt_has_financingPrice', array($this, 'bdt_shortcode_has_financingPrice'));
			add_shortcode('bdt_has_leasingPrice', array($this, 'bdt_shortcode_has_leasingPrice'));
			add_shortcode('bdt_vehicle_price_type', array($this, 'bdt_shortcode_vehicleprice_type'));
			add_shortcode('bdt_GenerateKontantprisTabContent', array($this, 'bdt_shortcode_GenerateKontantprisTabContent'));
			//jlk ny benyttes i ElbesparelsesWidget hvis man har mere end en afdeling.
			add_shortcode('bdt_CompanyId', array($this, 'bdt_shortcode_CompanyId'));
			//jlk forbrug/rækkevidde ændringer
			add_shortcode('bdt_forbrug_eller_raekkevidde_label', array($this, 'bdt_shortcode_forbrug_eller_raekkevidde_label'));
			add_shortcode('bdt_forbrug_eller_raekkevidde_data', array($this, 'bdt_shortcode_forbrug_eller_raekkevidde_data'));
			

            add_action('wp_head', array(&$this, 'bdt_insert_map_dependencies'), 1000);
        }
		
		//JLK ny forbrug/rækkevidde ændringer
		public function bdt_shortcode_forbrug_eller_raekkevidde_label( $atts )
		{
			$vehiclePropellant = $this->currentVehicle->propellant;
		
			if ($vehiclePropellant == "EL" || $vehiclePropellant == "El") 
			{
				if(($atts['design']) === 'v1')
				{
					return ('<p>Rækkevidde</p>');
				}
				else if(($atts['design']) === 'v2')
				{
					return ('Rækkevidde');
				}
				else 
				{
					return ('Ukendt rækkevidde');
				}
			}
			else
			{
				if(($atts['design']) === 'v1')
				{
					return ('<p>Forbrug</p>');
				}
				else if(($atts['design']) === 'v2')
				{
					return ('Forbrug');
				}
				else 
				{
					return ('Ukendt Forbrug');
				}
			}
		}
		
		//JLK ny forbrug/rækkevidde ændringer
		public function bdt_shortcode_forbrug_eller_raekkevidde_data()
		{
			$vehiclePropellant = $this->currentVehicle->propellant;
		
			if ($vehiclePropellant == "EL" || $vehiclePropellant == "El") 
			{
				return do_shortcode("[bdt_prop p='ElectricReach']");
			}
			else
			{
				return do_shortcode("[bdt_prop p='Kmx1l']");
			}
		}		
		
		//JLK ny
        public function bdt_shortcode_has_cashPrice()
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

			$cashPrice = $this->currentVehicle->cashPrice;

			if (isset($this->currentVehicle->cashPrice) && json_encode($this->currentVehicle->cashPrice) !== '{}') {
				return true;
			} else {
				return false;
			}
        }
		
		//JLK ny
        public function bdt_shortcode_has_financingPrice()
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

			$financingPrice = $this->currentVehicle->financingPrice;

			if (isset($this->currentVehicle->financingPrice) && json_encode($this->currentVehicle->financingPrice) !== '{}') {
				return true;
			} else {
				return false;
			}
        }
		
		//JLK ny
        public function bdt_shortcode_has_leasingPrice()
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

			$leasingPrice = $this->currentVehicle->leasingPrice;
			
			if (isset($this->currentVehicle->leasingPrice) && json_encode($this->currentVehicle->leasingPrice) !== '{}') {
				return true;
			} else {
				return false;
			}
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

//jlk old
/*
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
*/
//jlk new
public function bdt_insert_map_dependencies() {
    if (isset($this->_options_4['activate_map']) && $this->_options_4['activate_map'] === 'on') {
        // Enqueue Leaflet CSS
        wp_enqueue_style(
            'leaflet-css',
            'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css',
            array(),
            '1.9.4'
        );

        // Enqueue Leaflet JavaScript
        wp_enqueue_script(
            'leaflet-js',
            'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js',
            array(),
            '1.9.4',
            true
        );

        // Enqueue Leaflet Providers JavaScript
        wp_enqueue_script(
            'leaflet-providers-js',
            'https://unpkg.com/leaflet-providers@1.13.0/leaflet-providers.js',
            array('leaflet-js'),
            '1.13.0',
            true
        );

        // Enqueue Leaflet Gesture Handling CSS
        wp_enqueue_style(
            'leaflet-gesture-handling-css',
            'https://unpkg.com/leaflet-gesture-handling@1.2.2/dist/leaflet-gesture-handling.min.css',
            array('leaflet-css'),
            '1.2.2'
        );

        // Enqueue Leaflet Gesture Handling JavaScript
        wp_enqueue_script(
            'leaflet-gesture-handling-js',
            'https://unpkg.com/leaflet-gesture-handling@1.2.2/dist/leaflet-gesture-handling.min.js',
            array('leaflet-js'),
            '1.2.2',
            true
        );
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
		
		public static function sortVehicleLabelsBTS(?array $labels, ?string $showAllLabes) : array
		{
			$vehicleLabels = array();
			
			if ($labels) {
				foreach($labels as $labelBTS) {
					
					// DealerSpecificLabel
					if($labelBTS->key == 427)
					{
						$vehicleLabels[1] = $labelBTS->value;
					}
					
					if($labelBTS->key == 443)
					{
						$vehicleLabels[2] = 'Online køb';
					}

					if($labelBTS->key == 11)
					{
						$vehicleLabels[3] = 'Nyhed';
					}

					if($labelBTS->key == 5)
					{
						$vehicleLabels[4] = 'Solgt';
					}

					if($labelBTS->key == 99999)
					{
						$vehicleLabels[5] = 'Fabriksny';
					}

					if($labelBTS->key == 12)
					{
						$vehicleLabels[6] = 'Leasing';
					}

					if($labelBTS->key == 9)
					{
						$vehicleLabels[7] = 'Kun engros';
					}

					if($labelBTS->key == 382)
					{
						$vehicleLabels[8] = 'Eksport';
					}

					if($labelBTS->key == 26)
					{
						$vehicleLabels[9] = 'Lagersalg';
					}
					if($labelBTS->key == 1)
					{
						$vehicleLabels[10] = 'Demonstration';
					}
					else {
						/*
						 * Is the show all labels setting on?
						 */

						if($showAllLabes != null) {

							if(!in_array($labelBTS->value, $vehicleLabels)) {
								array_push($vehicleLabels, $labelBTS->value);

							}
						}
					}				

				}
			}

			// We need the array in ascending order
			ksort($vehicleLabels);

			return $vehicleLabels;
		}		
		
        public function bdt_shortcode_vehiclelabels( $atts ) 
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }
			
			$options_two = get_option('bdt_options_2');

			$vehicleLabelsBTS = self::sortVehicleLabelsBTS($this->currentVehicle->labels, isset($options_two['show_all_labels']) ?? null);

			$vehiclePropellant = $this->currentVehicle->propellant;
			if (($vehiclePropellant == "EL" || $vehiclePropellant == "El") && !isset($options_two['hide_elbil_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Elbil");
			}
			else if ($vehiclePropellant == "Hybrid (B/EL)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Hybrid");
			}
			else if ($vehiclePropellant == "Hybrid (D/EL)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Hybrid");
			}
			/* jlk fuld løsning
			else if ($vehiclePropellant == "Hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Hybrid (Benzin / El)");
			}
			else if ($vehiclePropellant == "Hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Hybrid (Diesel / El)");
			}
			else if ($vehiclePropellant == "Mild hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Mild hybrid (Benzin / El)");
			}
			else if ($vehiclePropellant == "Mild hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Mild hybrid (Diesel / El)");
			}
			else if ($vehiclePropellant == "Plug-in hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Plug-in hybrid (Benzin / El)");
			}
			else if ($vehiclePropellant == "Plug-in hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Plug-in hybrid (Diesel / El)");
			}			
			*/
			//jlk tilpasset
			else if ($vehiclePropellant == "Hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Hybrid");
			}
			else if ($vehiclePropellant == "Hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Hybrid");
			}
			else if ($vehiclePropellant == "Mild hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Mild hybrid");
			}
			else if ($vehiclePropellant == "Mild hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Mild hybrid");
			}
			else if ($vehiclePropellant == "Plug-in hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Plug-in hybrid");
			}
			else if ($vehiclePropellant == "Plug-in hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
			{
			  array_unshift($vehicleLabelsBTS, "Plug-in hybrid");
			}			
			else if ($vehiclePropellant == "Diesel" && isset($options_two['show_diesel_label']) ? $options_two['show_diesel_label'] : null)
			{
			  array_unshift($vehicleLabelsBTS, "Diesel");
			}
			else if ($vehiclePropellant == "Benzin" && isset($options_two['show_benzin_label']) ? $options_two['show_benzin_label'] : null)
			{
			  array_unshift($vehicleLabelsBTS, "Benzin");
			}
			else 
			{
				//do nothing
			}			

			if(count($vehicleLabelsBTS) > 5) {
				$vehicleLabelsBTS = array_slice($vehicleLabelsBTS, 0, 5);
			}		
			
            wp_enqueue_style("bdt_style");
            $allowedLabels = null;
            if(isset($atts['allowed']) && trim($atts['allowed']) !== '')
            {
                $allowedLabels = explode(',', $atts['allowed']);
            }
            $labels = '';
			
			//jlk
			$hybridTypes = [
				"Hybrid (Benzin / El)", 
				"Hybrid (Diesel / El)", 
				"Mild hybrid (Benzin / El)", 
				"Mild hybrid (Diesel / El)", 
				"Plug-in hybrid (Benzin / El)", 
				"Plug-in hybrid (Diesel / El)",
				"Hybrid",
				"Mild hybrid",
				"Plug-in hybrid"
			];			

            foreach($vehicleLabelsBTS as $label)
            {
                if($allowedLabels !== null)
                {
                    if(!in_array($label, $allowedLabels))
                    {
                        continue;
                    }
                }
				//Is the special carlite dealer label in use or the other special label fields?
				$carliteDealerLabel = isset($options_two['carlite_dealer_label']) ? $options_two['carlite_dealer_label'] : null;
				$carliteOnlineKoebLabel = isset($options_two['carlite_onlinekoeb_label']) ? $options_two['carlite_onlinekoeb_label'] : null;
				$carliteNyhedLabel = isset($options_two['carlite_nyhed_label']) ? $options_two['carlite_nyhed_label'] : null;
				$carliteSolgtLabel = isset($options_two['carlite_solgt_label']) ? $options_two['carlite_solgt_label'] : null;
				$carliteFabriksnyLabel = isset($options_two['carlite_fabriksny_label']) ? $options_two['carlite_fabriksny_label'] : null;
				$carliteLeasingLabel = isset($options_two['carlite_leasing_label']) ? $options_two['carlite_leasing_label'] : null;
				$carliteKunEngrosLabel = isset($options_two['carlite_kun_engros_label']) ? $options_two['carlite_kun_engros_label'] : null;
				$carliteEksportLabel = isset($options_two['carlite_eksport_label']) ? $options_two['carlite_eksport_label'] : null;
				$carliteLagersalgLabel = isset($options_two['carlite_lagersalg_label']) ? $options_two['carlite_lagersalg_label'] : null;
				$carliteDemonstrationLabel = isset($options_two['carlite_demonstration_label']) ? $options_two['carlite_demonstration_label'] : null;
				$skjulLeasingLabel = isset($options_two['hide_leasing_label']) ? $options_two['hide_leasing_label'] : null;

				if($label == 'Carlite Forhandler Label' && $carliteDealerLabel != null) 
				{
					$dealerSpecificLabel = str_replace("Carlite Forhandler Label", $carliteDealerLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $dealerSpecificLabel . '</span>';
					
				}
				else if($label == 'Online køb' && $carliteOnlineKoebLabel != null) 
				{
					$OnlineKoebLabel = str_replace("Online køb", $carliteOnlineKoebLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $OnlineKoebLabel . '</span>';
					
				}
				else if($label == 'Nyhed' && $carliteNyhedLabel != null) 
				{
					$NyhedLabel = str_replace("Nyhed", $carliteNyhedLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $NyhedLabel . '</span>';
					
				}
				else if($label == 'Solgt' && $carliteSolgtLabel != null) 
				{
					$SolgtLabel = str_replace("Solgt", $carliteSolgtLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $SolgtLabel . '</span>';
					
				}
				else if($label == 'Fabriksny' && $carliteFabriksnyLabel != null)
				{
					$FabriksnyLabel = str_replace("Fabriksny", $carliteFabriksnyLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $FabriksnyLabel . '</span>';
					
				}
				else if($label == 'Leasing' && $carliteLeasingLabel != null) 
				{
					$LeasingLabel = str_replace("Leasing", $carliteLeasingLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $LeasingLabel . '</span>';
					
				}
				else if($label == 'Kun engros' && $carliteKunEngrosLabel != null) 
				{
					$KunEngrosLabel = str_replace("Kun engros", $carliteKunEngrosLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $KunEngrosLabel . '</span>';
					
				}
				else if($label == 'Eksport' && $carliteEksportLabel != null) 
				{
					$EksportLabel = str_replace("Eksport", $carliteEksportLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $EksportLabel . '</span>';
					
				}
				else if($label == 'Lagersalg' && $carliteLagersalgLabel != null) 
				{
					$LagersalgLabel = str_replace("Lagersalg", $carliteLagersalgLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $LagersalgLabel . '</span>';
					
				}
				else if($label == 'Demonstration' && $carliteDemonstrationLabel != null) 
				{
					$DemonstrationLabel = str_replace("Demonstration", $carliteDemonstrationLabel, $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $DemonstrationLabel . '</span>';
					
				}
				else if($label == 'Demonstration' && $carliteDemonstrationLabel == null) 
				{
					$DemonstrationLabel = str_replace("Demonstration", "Demo", $label);
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $DemonstrationLabel . '</span>';
					
				}
				else if (in_array($label, $hybridTypes)) {
					$labels .= '<span class="badge Hybrid mr-2 mb-1">' . $label . '</span>';
				}
				else if($label == 'Leasing' && $skjulLeasingLabel) 
				{
					$labels .= '';		
				}
				
				else 
				{
					$labels .= '<span class="badge ' . $label. ' mr-2 mb-1">' . $label . '</span>';
				}
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
				//JLK
                if(isset($this->_options_2['hide_only_wholesale_vehicles']) && $this->_options_2['hide_only_wholesale_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideOnlyWholesaleVehicles = 'true';
                }
                if(isset($this->_options_2['show_only_wholesale_vehicles']) && $this->_options_2['show_only_wholesale_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->ShowOnlyWholesaleVehicles = 'true';
                }				
                if(isset($this->_options_2['hide_trailer_vehicles']) && $this->_options_2['hide_trailer_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideTrailerVehicles = 'true';
                }
                if(isset($this->_options_2['hide_classic_vehicles']) && $this->_options_2['hide_classic_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideClassicVehicles = 'true';
                }
                if(isset($this->_options_2['hide_tractor_vehicles']) && $this->_options_2['hide_tractor_vehicles'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideTractorVehicles = 'true';
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
				//JLK
                if(isset($this->_options_2['hide_internal_vehicles_bilinfo']) && $this->_options_2['hide_internal_vehicles_bilinfo'] === 'on')
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->HideInternalVehiclesBilInfo = 'true';
                }				
                if(isset($this->_options_2['bdt_pricetypes']) && $this->_options_2['bdt_pricetypes'] != "-1")
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
                    $filterObject->PriceTypes = array($this->_options_2['bdt_pricetypes']);
                }
				//jlk
                if(isset($this->_options_2['bdt_propellanttypes']) && $this->_options_2['bdt_propellanttypes'] != "-1")
                {
                    if($filterObject === null)
                    {
                        $filterObject = new BDTFilterObject();
                    }
					$filterObject->Propellants = array($this->_options_2['bdt_propellanttypes']);
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
			
			$statusSold = $this->bdt_shortcode_status_sold();
			$statusOnlinekoeb = $this->bdt_shortcode_status_onlinekoeb();

			//Use apiKey to check if customer is Jørgen Hansen Biler to handle TestDrive/Purchace differently from everyone else. This is a temp solution and should be refined if other customer need same solution in the future 
			$apiKey = WordpressHelper::getApiKey();
			
			//jlk test start midlertidig løsning
			$vehicleRental = false;
			//print_r($this->currentVehicle->labels);
			// Use apiKey to check if customer is Leonhard Biler
			if ($apiKey === "cca3fe21-fa7c-449a-8dfa-a67f6af1e4d5")
			{
				foreach($this->currentVehicle->labels as $label)
				{
					if($label->key == 2)
					{
						$vehicleRental = true;
					}
				}
			}			
			
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
						if(!$statusSold)
						{
							$content = '<span class="bticon bticon-TestDrive"></span><br>' . __('Testdrive', 'biltorvet-dealer-tools');
						}
						else
						{
							$content = '<span class="bticon bticon-TestDrive disable-grey"></span><br>' . __('Testdrive', 'biltorvet-dealer-tools');
						}
                    break;					
                    case 'Email':
                        $content = '<span class="bticon bticon-WriteUs"></span><br>' . __('Write us', 'biltorvet-dealer-tools');
                    break;
					case 'Purchase':
						if(!$statusSold)
						{					
							$content = '<span class="bticon bticon-BuyCar"></span><br>' . __('Buy this car', 'biltorvet-dealer-tools');
						}
						else
						{
							$content = '<span class="bticon bticon-BuyCar disable-grey"></span><br>' . __('Buy this car', 'biltorvet-dealer-tools');
						}
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
                    return (isset($style) ? $style : '') . '<a rel="nofollow" id="'. $id .'" href="tel:' . $this->currentVehicle->company->phone . '" class="bdt_cta '.(isset($customColor) && $customColor !== null ? 'donottint ' : '') . (isset($atts['class']) ? ' ' . esc_attr($atts['class']) : '') .'">' . $content . '</a>';
                }
            }
			
			if($atts['type'] === 'TestDrive' || $atts['type'] === 'Purchase')
			{
				if ($apiKey === "780a56f2-25c4-4793-b895-8fa1bf427cbd")//Jørgen Hansen Biler
				{
					if(!$statusSold)
					{
						return (isset($style) ? $style : '') . '<a rel="nofollow" id="'. $id .'" href="#" class="bdt_cta '.(isset($customColor) && $customColor !== null ? 'donottint ' : '') . (isset($atts['class']) ? ' ' . esc_attr($atts['class']) : '') .'">' . $content . '</a>';
					}
					else
					{
						return (isset($style) ? $style : '') . '<button class="disable-grey button-priceTabs">Book en prøvetur</button>';
					}
				}
				else
				{
					//jlk test
					if(!$statusSold && !$vehicleRental)
					{
						return (isset($style) ? $style : '') . '<a rel="nofollow" id="'. $id .'" href="#" class="bdt_cta '.(isset($customColor) && $customColor !== null ? 'donottint ' : '') . (isset($atts['class']) ? ' ' . esc_attr($atts['class']) : '') .'">' . $content . '</a>';
					}
					else
					{
						return (isset($style) ? $style : '') . '<a rel="nofollow" id="'. $id .'-disabled" href="#" class="bdt_cta disable-grey '.(isset($customColor) && $customColor !== null ? 'donottint ' : '') . (isset($atts['class']) ? ' ' . esc_attr($atts['class']) : '') .'">' . $content . '</a>';
					}
				}
			}

            return (isset($style) ? $style : '') . '<a rel="nofollow" id="'. $id .'" href="#" class="bdt_cta '.(isset($customColor) && $customColor !== null ? 'donottint ' : '') . (isset($atts['class']) ? ' ' . esc_attr($atts['class']) : '') .'">' . $content . '</a>';
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
			
			//Use apiKey to check if customer is Kinnerup AutoKommision OR PeterPetersen OR Mikroleje to handle price differently from everyone else
			$apiKey = WordpressHelper::getApiKey();

            if ($apiKey === "7260fb0b-553e-48cd-8ac2-57c58eb88979" || $apiKey === "b16cbf16-dfda-45e4-a073-87956c7a37a9" || $apiKey === "d073aef5-6a13-4a9b-ad6f-7b3288f4de4d")//kinnerup & peterpetersen & mikroleje
			{
				$showPrice = '<span class="bdt_price_container">';
				if ($priceController->GetPrimaryPrice('details')) {
					$showPrice .= '<span class="primary-price">' . str_replace(" (inkl. lev. omkostninger)", "", $priceController->GetPrimaryPrice('details')) . '</span>';
				}
				if ($priceController->GetSecondaryPrice('details')) {
					$showPrice .= '<span class="primary-price">' . str_replace(" (inkl. lev. omkostninger)", "", $priceController->GetSecondaryPrice('details')) . '</span>';
				}
				if ($priceController->GetTertiaryPrice('details')) {
					$showPrice .= '<span class="primary-price">' . str_replace(" (inkl. lev. omkostninger)", "", $priceController->GetTertiaryPrice('details')) . '</span>';
				}
				$showPrice .= '</span>';
			}
			else //everyone else
			{
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

			//jlk test start midlertidig løsning
			$vehicleRental = false;
			$vehicleSold = false;
			//print_r($this->currentVehicle->labels);
			// Use apiKey to check if customer is Leonhard Biler
			if ($apiKey === "cca3fe21-fa7c-449a-8dfa-a67f6af1e4d5")
			{
				foreach($this->currentVehicle->labels as $label)
				{
					if($label->key == 2)
					{
						$vehicleRental = true;
					}
				}
			}
			// Check if vehicle is sold (label key 5)
			foreach($this->currentVehicle->labels as $label)
			{
				if($label->key == 5)
				{
					$vehicleSold = true;
					break;
				}
			}
			//jlk test slut
            // Use apiKey to check if customer is NOT Neltoft gruppen OR Mikroleje
            if ($apiKey !== "c36f9c6d-cf10-49b3-ad2d-b875b6610d7a" && $apiKey !== "d073aef5-6a13-4a9b-ad6f-7b3288f4de4d" && $vehicleRental !== true && $vehicleSold !== true)
            {
                $showPrice .= '<div class="bdt_price_more-info">';
                $showPrice .= '<input id="priceInfo" class="bdt_price_more-info_checkbox" type="checkbox">';
                $showPrice .= '<label for="priceInfo" class="bdt_price_more-info_drop bdt"><span class="bdt_price_more-info_icon d-print-none"></span></label>';
                $showPrice .= '<div class="bdt_price_more-info_content">';
                $showPrice .= TextUtils::GenerateSpecificationsTable($properties, true);
                $showPrice .= '</div>';
                $showPrice .= '</div>';
            }

            return $showPrice;
        }

		/* JLK ny function til tab price feature */
		public function bdt_shortcode_vehicleprice_type( $atts )
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                global $wp_query;
                $wp_query->set_404();
                status_header( 404 );
                get_template_part( 404 );
                exit();
            }
			
            $priceController = new PriceController(VehicleFactory::create(json_decode(json_encode($this->currentVehicle), true)));	

            $PriceTypeParameter = null;
            if(isset($atts['pricetype']) && trim($atts['pricetype']) !== '')
            {
                $PriceTypeParameter = $atts['pricetype'];
            }

			if ($PriceTypeParameter == "kontant") {
			   $showPriceType = '<span class="bdt_price_container">';
				if ($priceController->GetPrimaryPrice('details')) {
					$showPriceType .='<h3>Kontant</h3><span class="primary-price">' .$priceController->GetPrimaryPrice('details', 'cashPrice') .'</span>';
				}
				$showPriceType .= '</span>';
			}
			else if ($PriceTypeParameter == "finansiering") {
			   $showPriceType = '<span class="bdt_price_container">';
				if ($priceController->GetPrimaryPrice('details')) {
					$showPriceType .='<h3>Finansiering</h3><span class="primary-price">' .$priceController->GetPrimaryPrice('details', 'financingPrice') .'</span>';
				}
				$showPriceType .= '</span>';
			}
			else if ($PriceTypeParameter == "leasing") {
			   $showPriceType = '<span class="bdt_price_container">';
				if ($priceController->GetPrimaryPrice('details')) {
					$showPriceType .='<h3>Leasing</h3><span class="primary-price">' .$priceController->GetPrimaryPrice('details', 'leasingPrice') .'</span>';
				}
				$showPriceType .= '</span>';
			}			

            return $showPriceType;
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

			//jlk FA-10756 start shortcode property til at returnere maxChargingEffectKW + homeChargerMaxChargingEffectKW as onlynumbers (kun numre) benyttes i forbindelse med elbil besparelseswidget (rækkevidde)
			if(($propertyName == 'maxChargingEffectKW' || $propertyName == 'homeChargerMaxChargingEffectKW') && isset($atts['onlynumber']) && $atts['onlynumber'] === 'true')
			{
				// Remove all non-numeric characters
				$onlynumber = preg_replace('/\D/', '', $value);
				return $onlynumber;
			}
			//jlk FA-10756 slut

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

            return '<div class="bdt">'.TextUtils::GenerateSpecificationsTable($properties, false).'</div>';
        }

		//jlk
        public function bdt_shortcode_GenerateKontantprisTabContent()
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }
			
			//$number = 12345;
			//print_r($this->currentVehicle);
			$statusSold = $this->bdt_shortcode_status_sold();
			$priceFormatted = $statusSold ? '-' : $this->currentVehicle->cashPrice->priceFormatted;
			$priceLabelDetailsPage = $this->currentVehicle->cashPrice->priceLabelDetailsPage;
			$content = 
			'<div class="price_tabs-container">
				<!-- First Row -->
				<div class="price_tabs-row">
					<div class="price_tabs-col">
						<!--<h3 class="price_tabs-h3-center">Kontant betaling</h3>-->
						<h2 class="price_tabs-h2-center">Kontant betaling</h2>
					</div>
				</div>

				<!-- Second Row -->
				<div class="price_tabs-row">
					<div class="price_tabs-col price_tabs-col-border-bottom">
						<div class="price_tabs-text-left">' . $priceLabelDetailsPage . '</div>
						<div class="price_tabs-text-right">' . $priceFormatted . '</div>
					</div>
				</div>
			</div>';

            return $content;
        }
		
		//jlk ny benyttes i ElbesparelsesWidget hvis man har mere end en afdeling.
        public function bdt_shortcode_CompanyId()
        {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }
			
			$companyId = $this->currentVehicle->company->id;

            return $companyId;
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
                'show' => 3,
                'hideinternalvehiclesbilinfo' => false,
				'hideonlywholesalevehicles' => false,
				'showonlywholesalevehicles' => false
            ), $atts, 'bdt_recommendedvehicles' );

            $vehicleId = $this->ResolveVehicleId();
			
			//jlk
			$hideInternalVehiclesBilinfo = esc_attr($atts['hideinternalvehiclesbilinfo']);
			$hideOnlyWholesaleVehicles = esc_attr($atts['hideonlywholesalevehicles']);
			$showOnlyWholesaleVehicles = esc_attr($atts['showonlywholesalevehicles']);
			
            try{
                $vehicleFeed = $this->biltorvetAPI->GetRecommendedVehicles($vehicleId === -1 ? null : $vehicleId, intval($atts['show']), $hideInternalVehiclesBilinfo, $hideOnlyWholesaleVehicles, $showOnlyWholesaleVehicles);
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
                'type' => null,
                'hideinternalvehiclesbilinfo' => false,
				'hideonlywholesalevehicles' => false
            ), $atts, 'bdt_featuredvehicles');

            $amount = esc_attr($atts['show']);
            $vehicleType = esc_attr($atts['type']);
			//jlk
			$hideInternalVehiclesBilinfo = esc_attr($atts['hideinternalvehiclesbilinfo']);
			$hideOnlyWholesaleVehicles = esc_attr($atts['hideonlywholesalevehicles']);
			
            try{
				$vehicleFeed = $this->biltorvetAPI->GetFeaturedVehicles($amount, $vehicleType, $hideInternalVehiclesBilinfo, $hideOnlyWholesaleVehicles);
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

        public function bdt_shortcode_print_gallery() {
            if(!isset($this->currentVehicle) || $this->currentVehicle === null)
            {
                return __('Vehicle not found', 'biltorvet-dealer-tools');
            }

            $slides = '';
            $i = 0;

            $altTag = "Billede af " . $this->currentVehicle->makeName . " " . $this->currentVehicle->model . " " . $this->currentVehicle->variant;

            foreach($this->currentVehicle->images as $image )
            {
                if ($i < 2) {
                    $slides .= '<img loading=lazy src="' . $image . '" alt="' . $altTag . '">';
                    $i++;
                }
               
            }
            $buildSlideShow = '';
            $buildSlideShow .= $slides;

            return $buildSlideShow;
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
		
		public function bdt_shortcode_status_sold()
		{
			$sold = false;
			foreach($this->currentVehicle->labels as $label)
			{
				if($label->key === 5)
				{
					$sold = true;
					break;
				}
			}
			return $sold;
		}

		public function bdt_shortcode_status_onlinekoeb()
		{
			$onlinekoeb = false;
			foreach($this->currentVehicle->labels as $label)
			{
				if($label->key === 443)
				{
					$onlinekoeb = true;
					break;
				}
			}
			return $onlinekoeb;
		}		
		
		public function bdt_shortcode_car_tracking_using_datalayer()
		{
			$vehicle = VehicleFactory::create(json_decode(json_encode($this->currentVehicle), true));
			$vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
			$transmissionvalue = $this->currentVehicle->automatic;
			if ($transmissionvalue) 
			{
				$transmission = 'Automatic';
			} 
			else 
			{
				$transmission = 'Manual';
			}
			
			$brandNewvalue = $this->currentVehicle->brandNew;
			if ($brandNewvalue) 
			{
				$brandNewOrUsed = 'Ny';
			} 
			else 
			{
				$brandNewOrUsed = 'Brugt';
			}

			?>
                <script>
                    jQuery( document ).ready(function() {
						window.dataLayer.push({
								event: 'show_vehicle',
								eventCategory: 'car details showings',
								eventAction: "car-details show",
								content_type: 'vehicle',
								content_ids: ['<?php echo($this->currentVehicle->documentId); ?>'],
								postal_code: '<?php echo($this->currentVehicle->company->postNumber); ?>',
								country: 'Denmark',
								make: '<?php echo($this->currentVehicle->makeName); ?>',
								model: '<?php echo($this->currentVehicle->model); ?>',
								variant: '<?php echo($this->currentVehicle->variant); ?>',
								year: '<?php echo($vehicleProperties['ModelYear']->getValue()); ?>',
								state_of_vehicle: '<?php echo($brandNewOrUsed); ?>',
								exterior_color: '<?php echo($vehicleProperties['Color']->getValue()); ?>',
								transmission: '<?php echo($transmission); ?>',
								body_style: '<?php echo($vehicleProperties['BodyType']->getValue()); ?>',
								fuel_type: '<?php echo($this->currentVehicle->propellant); ?>',
								drivetrain: 'n/a',
								price: <?php $price = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'Price', true); echo is_numeric($price) ? $price : "'n/a'"; ?>,
								leasingpricepermonth: <?php $leasingprice = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'LeasingMonthlyPayment', true); echo is_numeric($leasingprice) ? $leasingprice : "'n/a'"; ?>,
								financingpricepermonth: <?php $financingprice = $this->biltorvetAPI->GetPropertyValue($this->currentVehicle, 'FinancingMonthlyPrice', true); echo is_numeric($financingprice) ? $financingprice : "'n/a'"; ?>,
								currency: 'DKK',
								preferred_price_range: 'n/a',
								companyid: '<?php echo($this->currentVehicle->company->id); ?>',
								created: '<?php echo($this->currentVehicle->created); ?>',
								updated: '<?php echo($this->currentVehicle->updated); ?>'
							});
                    });
                </script>

            <?php

			return;
		}			
    }
