<?php
// phpcs:ignoreFile -- this is not a core file

namespace Biltorvet\Utility;

use Biltorvet\Controller\ApiController;
use Biltorvet\Controller\DebugController;
use Biltorvet\Controller\PriceController;
use Biltorvet\Controller\TemplateController;
use Biltorvet\Factory\VehicleLeadFactory;
use Biltorvet\Helper\DataHelper;
use Biltorvet\Helper\MailFormatter;
use Biltorvet\Helper\WordpressHelper;
use Biltorvet\Model\SearchFilter;
use Exception;
use TextUtils;


class Callbacks
{
    protected $apiController;
    protected $templateController;

    public function __construct()
    {
        $this->apiController = new ApiController();

        $this->templateController = new TemplateController();
    }

    /**
     * @param array $atts
     *
     * @return bool|false|string
     */
    public function get_vehicles_shortcode(array $atts)
    {
        $searchFilter = new SearchFilter();

        if(isset($atts['make'])){
            $searchFilter->setMakes([ucfirst($atts['make'])]);
        }
        if (isset($atts['model'])) {
            $searchFilter->setModels([ucfirst($atts['model'])]);
        }
        if (isset($atts['propellant'])){
            $searchFilter->setPropellants([ucfirst($atts['propellant'])]);
        }
        if(isset($atts['companyid'])) {
            $searchFilter->setCompanyIds([ucfirst($atts['companyid'])]);
        }

        $option = get_option('bdt_options');
        $vehicleSearchPageId = $option['vehiclesearch_page_id'];

        // Error: No Cars
        if(count($this->apiController->getVehicles($searchFilter)) == null)
        {
            return 'Vi har solgt alle biler af denne type' . '<br><br>' . 'Se alle vores biler ' . '<a href="' . get_site_url() . '/' . get_page_uri($page = $vehicleSearchPageId) . '">her</a>';
        }

        wp_enqueue_style("bdt_style");
        return $this->templateController->load(
            'vehicleCardWrapper.php',
            [
                'vehicles' => $this->apiController->getVehicles($searchFilter),
                'basePage' => WordpressHelper::getOptions(1)['vehiclesearch_page_id']
            ],
            true
        );
    }

    public function get_vehicles_by_status_code_shortcode($atts)
    {
        $searchFilter = new SearchFilter();

        if(!isset($atts['status']))
        {
            return 'No status code set - Please set a valid status code.' . '<br><br>' . 'Check the documentation for valid status codes.';
        }

        $setStatusCode = ucfirst($atts['status']);

        // Remember to add the labels to the list of valid statuscodes AND the switch case: else nothing will be returned!
        $validStatusCodes = array("Sold", "New", "Leasing", "Warehousesale", "Flexleasing", "Export", "Upcoming", "Rental", "Commission", "Wholesale", "Bus", "NewCar", "Demo", "Carlite Dealer Label");

        if(!in_array($setStatusCode, $validStatusCodes))
        {
            return '<b>"' . $setStatusCode . '"</b>' . ' is not a valid status code - Please set a valid status code.' . '<br><br>' . 'Check the documentation for valid status codes.';
        }

        switch ($setStatusCode) {
            case 'Sold': $label = 5; break;
            case 'New': $label = 11; break;
            case 'Leasing': $label = 12; break;
            case 'Warehousesale' : $label = 26; break;
            case 'Flexleasing' : $label = 198; break;
            case 'Export' : $label = 382; break;
            case 'Upcoming' : $label = 4; break;
            case 'Rental' : $label = 2; break;
            case 'Commission' : $label = 27; break;
            case 'Wholesale' : $label = 9; break;
            case "Bus" : $label = 416; break;
            case "NewCar" : $label = 99999; break;
            case "Demo" : $label = 1; break;
            case "Carlite Dealer Label" : $label = 427; break;
        }

        wp_enqueue_style("bdt_style");
        return $this->templateController->load(
            'vehicleCardWrapper.php',
            [
                'vehicles' => DataHelper::filterVehiclesByLabel($this->apiController->getVehicles($searchFilter), $label),
                'basePage' => WordpressHelper::getOptions(1)['vehiclesearch_page_id'],
            ],
            true
        );
    }

    public function get_vehicles_by_type_shortcode(array $atts)
    {
        $searchFilter = new SearchFilter();

        // Error: No type set
        if(!isset($atts['type']))
        {
            return 'Please set a type.' . '<br><br>' . 'Check the documentation for valid types.';
        }

        $setType = ucfirst($atts['type']);

        $validTypes = array("Car", "Van", "Motorcycle", "Truck");

        // Error: Invalid type set
        if(!in_array($setType, $validTypes)){

            return '<b>"' . $setType . '"</b>' . ' is not a valid type - Please set a valid type.' . '<br><br>' . 'Check the documentation for valid types.';
        }

        switch ($setType) {
            case 'Car': $typeId = 1; break;
            case 'Van': $typeId = 2; break;
            case 'Motorcycle': $typeId = 7; break;
            case 'Truck' : $typeId = 4; break;
        }

        if(isset($atts['state'])){

            $setState = ucfirst($atts['state']);

            $validStates = array("BrandNew", "Used");

            switch ( ($setState)) {
                case 'BrandNew' : $brandNew = true; break;
                case 'Used' : $brandNew = false; break;
            }

            if (!in_Array($setState, $validStates))
            {
                return '<b>"' . $setState . '"</b>' . ' is not a valid state - Please set a valid state.' . '<br><br>' . 'Check the documentation for valid states.';
            }

            wp_enqueue_style("bdt_style");
            return $this->templateController->load(
                'vehicleCardWrapper.php',
                [
                    'vehicles' => DataHelper::filterVehiclesByTypeAndState($this->apiController->getVehicles($searchFilter), $typeId, $brandNew),
                    'basePage' => WordpressHelper::getOptions(1)['vehiclesearch_page_id'],
                ],
                true
            );
        }

        wp_enqueue_style("bdt_style");
        return $this->templateController->load(
            'vehicleCardWrapper.php',
            [
                'vehicles' => DataHelper::filterVehiclesByType($this->apiController->getVehicles($searchFilter), $typeId),
                'basePage' => WordpressHelper::getOptions(1)['vehiclesearch_page_id'],
            ],
            true
        );
    }

    /**
     * @TODO: Refactor
     *
     * @param  array $args
     * @return array
     * @throws Exception
     */
//    public function sendLead(array $args)
//    {
//        $args['message'] .= "\r\n" . "Email afsendt fra: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//
//        if (WordpressHelper::isActivityType(WordpressHelper::getQueryParameter('bdt_actiontype'))) {
//            $vehicle = $this->apiController->getVehicleDetails(WordpressHelper::getQueryParameter('bdt_vehicle_id'));
//
//            if ($vehicle === null) {
//                $args['message'] = MailFormatter::MakeLabelsUppercase($args['message']);
//                return $args;
//            }
//
//            // Append the vehicle info to the WP email.
//            $args['message'] .= "\r\n\r\n" .  sprintf(__('Selected vehicle: %s', 'biltorvet-dealer-tools'), $vehicle->getModel() . ' (' . $vehicle->getId() . ')');
//            // Some e-mail clients don't respect the reply-to header, and then we lose the information about sender. For this reason, we are gluing the sender e-mail back to the e-mail body.
//            $args['message'] .= "\r\n\r\n" .  sprintf(__('Lead sender: %s', 'biltorvet-dealer-tools'), WordpressHelper::getReplyTo($args));
//
//            $this->apiController->sendLead(VehicleLeadFactory::create($vehicle, $args, WordpressHelper::getQueryParameters()));
//        }
//
//        $args['message'] = MailFormatter::MakeLabelsUppercase($args['message']);
//
//        return $args;
//    }

    public function debug_page_menu()
    {
        add_menu_page('Debug page', 'Debug', 'manage_options', 'debug-page', [$this,'debugPage']);
    }

    public function debugPage()
    {
        new DebugController();
    }
}
