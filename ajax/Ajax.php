<?php
    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly
    
    class Ajax {
        private $biltorvetAPI;
        private $_options;
        private $_options_2;
        private $_options_3;
        private $_options_4;

        public function __construct($biltorvetAPI)
        {
            if($biltorvetAPI === null)
            {
                throw new Exception( __('No Biltorvet API instance provided.', 'biltorvet-dealer-tools') );
            }
            $this->_options = get_option( 'bdt_options' );
            $this->_options_2 = get_option( 'bdt_options_2' );
            $this->_options_3 = get_option( 'bdt_options_3' );
            $this->_options_4 = get_option( 'bdt_options_4' );
            $this->biltorvetAPI = $biltorvetAPI;

            add_action( 'wp_ajax_get_filter_options', array($this, 'bdt_get_filter_options') );
            add_action( 'wp_ajax_nopriv_get_filter_options',  array($this, 'bdt_get_filter_options') );

            add_action( 'wp_ajax_save_filter', array($this, 'bdt_save_filter') );
            add_action( 'wp_ajax_nopriv_save_filter',  array($this, 'bdt_save_filter') );
        }
        
        public function bdt_get_filter_options() {
            $filterObject = new BDTFilterObject();
            
            if(isset($_SESSION['bdt_filter']))
            {
                $filterObject = new BDTFilterObject(json_decode($_SESSION['bdt_filter'], true));
            }

            if(isset($_POST['filter']) && $_POST['filter'] != null)
            {
                $filterObject = new BDTFilterObject(sanitize_post($_POST['filter']));
            }

            $filterObject->HideSoldVehicles = isset($this->_options_2['hide_sold_vehicles']) && $this->_options_2['hide_sold_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideLeasingVehicles = isset($this->_options_2['hide_leasing_vehicles']) && $this->_options_2['hide_leasing_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideFlexLeasingVehicles = isset($this->_options_2['hide_flexleasing_vehicles']) && $this->_options_2['hide_flexleasing_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideWarehousesaleVehicles = isset($this->_options_2['hide_flexleasing_vehicles']) && $this->_options_2['hide_flexleasing_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideCarLiteDealerLabelVehicles = isset($this->_options_2['hide_carlite_dealer_label_vehicles']) && $this->_options_2['hide_carlite_dealer_label_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideRentalVehicles = isset($this->_options_2['hide_rental_vehicles']) && $this->_options_2['hide_rental_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideUpcomingVehicles = isset($this->_options_2['hide_upcoming_vehicles']) && $this->_options_2['hide_upcoming_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideWholesaleVehicles = isset($this->_options_2['hide_wholesale_vehicles']) && $this->_options_2['hide_wholesale_vehicles'] === 'on' ? 'true' : null;
			//jlk
			$filterObject->HideOnlyWholesaleVehicles = isset($this->_options_2['hide_only_wholesale_vehicles']) && $this->_options_2['hide_only_wholesale_vehicles'] === 'on' ? 'true' : null;
			$filterObject->ShowOnlyWholesaleVehicles = isset($this->_options_2['show_only_wholesale_vehicles']) && $this->_options_2['show_only_wholesale_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideWTrailerVehicles = isset($this->_options_2['hide_trailer_vehicles']) && $this->_options_2['hide_trailer_vehicles'] === 'on' ? 'true' : null;
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
            $filterObject->PriceTypes = isset($this->_options_2['bdt_pricetypes']) && $this->_options_2['bdt_pricetypes'] !== '-1' ?  array($this->_options_2['bdt_pricetypes']) : null;
			$filterObject->Propellants = isset($this->_options_2['bdt_propellanttypes']) && $this->_options_2['bdt_propellanttypes'] !== '-1' ?  array($this->_options_2['bdt_propellanttypes']) : null;
			//jlk
			$filterObject->HideInternalVehiclesBilInfo = isset($this->_options_2['hide_internal_vehicles_bilinfo']) && $this->_options_2['hide_internal_vehicles_bilinfo'] === 'on' ? 'true' : null;

            try {
                $filterObjectOptions = $this->biltorvetAPI->GetFilterOptions($filterObject);
            } catch(Exception $e) {
                return $e->getMessage();
            }

            echo json_encode($filterObjectOptions);

            wp_die();
        }

        public function bdt_save_filter()
        {
            $_SESSION['bdt_filter'] = json_encode($_POST['filter']);
            echo json_encode(array('status' =>'ok'));
            wp_die();
        }
    }