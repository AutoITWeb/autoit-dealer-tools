<?php
    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly
    
    class Ajax {
        private $biltorvetAPI;
        private $_options;

        public function __construct($biltorvetAPI)
        {
            if($biltorvetAPI === null)
            {
                throw new Exception( __('No Biltorvet API instance provided.', 'biltorvet-dealer-tools') );
            }
            $this->_options = get_option( 'bdt_options' );
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

            $filterObject->HideSoldVehicles = isset($this->_options['hide_sold_vehicles']) && $this->_options['hide_sold_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideADVehicles = isset($this->_options['hide_ad_vehicles']) && $this->_options['hide_ad_vehicles'] === 'on' ? 'true' : null;
            $filterObject->HideBIVehicles = isset($this->_options['hide_bi_vehicles']) && $this->_options['hide_bi_vehicles'] === 'on' ? 'true' : null;

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