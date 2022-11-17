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
use phpDocumentor\Reflection\Types\Integer;
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
        $errors = "";

        if(isset($atts['make'])){
            $searchFilter->setMakes([ucfirst($atts['make'])]);
        }
        if (isset($atts['model'])) {
            $searchFilter->setModels([($atts['model'])]);
        }
        if (isset($atts['propellant'])){
            $searchFilter->setPropellants([ucfirst($atts['propellant'])]);
        }
        if(isset($atts['companyid'])) {
            $searchFilter->setCompanyIds([ucfirst($atts['companyid'])]);
        }
        if(isset($atts['bodytype'])) {
            $searchFilter->setBodyTypes([ucfirst($atts['bodytype'])]);
        }
        if(isset($atts['vehicletype'])) {
            $searchFilter->setProductTypes([ucfirst($atts['vehicletype'])]);
        }
        if(isset($atts['vehiclestate'])) {

            $vehicleStates = array();

            switch ($atts['vehiclestate']) {
                case "Fabriksny":
                    $vehicleStates = array("Fabriksny");
                    break;
                case "Brugt":
                    $vehicleStates = array("Brugt");
                    break;
            }

            $searchFilter->setVehicleStates($vehicleStates);
        }
        if(isset($atts["hidesoldvehicles"]) && $atts["hidesoldvehicles"] == 'true') {
            $searchFilter->setHideSoldVehicles(true);
        }
        if(isset($atts['minprice']) && isset($atts['maxprice'])) {

            if(TextUtils::VehiclePriceFormatter($atts['minprice']) == null && TextUtils::VehiclePriceFormatter($atts['maxprice']) == null) {
                $errors .= '<b>' . 'Error: ' . '</b>' . 'Error: Incorrect values  (min/max price).' . '<br>';
            }

            $searchFilter->setPriceMin($atts['minprice']);
            $searchFilter->setPriceMax($atts['maxprice']);
        }
        if(isset($atts['orderby'])) {

            $orderByValues = array("DateEdited", "Mileage", "FirstRegistrationYear", "Consumption", "Make", "Price", "LeasingPrice");

            if(in_array($atts['orderby'], $orderByValues)) {
                $searchFilter->setOrderBy($atts['orderby']);
                $searchFilter->setAscending(false);
            } else {
                $errors .= '<b>' . 'Error: ' . '</b>' . 'Incorrect orderby value - Must be one of the following: "DateEdited", "Mileage", "FirstRegistrationYear", "Consumption", "Make", "Price" or "LeasingPrice".' . '<br>';
            }
        }
        if(isset($atts['ascending'])) {

            if($atts['ascending'] == 'true') {
                $searchFilter->setAscending(true);
            } else {
                $errors .= '<b>' . 'Error: ' . '</b>' . 'Incorrect ascending value: Must be true (defaults to false if attribute is not set' . '<br>';
            }
        }

        if($errors != "") {
            return $errors;
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

    /**
     * Filters vehicles by vehicle type (Car, van, motorcycle or truck)
     *
     * This is about to be obsolete! Everything will be moved to the above shortcode
     *
     * @param atts   'status', 'make', 'state'
     *
     * @return Status
     */
    public function get_vehicles_by_status_code_shortcode($atts)
    {
        $searchFilter = new SearchFilter();

        if(!isset($atts['status']))
        {
            return 'No status code set - Please set a valid status code.' . '<br><br>' . 'Check the documentation for valid status codes.';
        }

        $setStatusCode = ucfirst($atts['status']);

        // Set make
        if(isset($atts['make'])) {
            $searchFilter->setSt(array(ucfirst($atts['make'])));
        }

        // Set vehicle state
        if(isset($atts['state'])) {

            if($atts['state'] == 'Fabriksny' || $atts['state'] == 'Brugt')
            {
                $searchFilter->setVehicleStates(array(ucfirst($atts['state'])));
            }
            else {
                return '<b>"' . $atts['state'] . '"</b>' . ' is not a valid state - Please set a valid state.' . '<br><br>' . 'Check the documentation for valid status codes.';
            }
        }

        if(isset($atts['orderby'])) {

            $orderByValues = array("DateEdited", "Mileage", "FirstRegistrationYear", "Consumption", "Make", "Price", "LeasingPrice");

            if(in_array($atts['orderby'], $orderByValues)) {
                $searchFilter->setOrderBy($atts['orderby']);
                $searchFilter->setAscending(false);
            } else {
                return '<b>' . 'Error: ' . '</b>' . 'Incorrect orderby value - Must be one of the following: "DateEdited", "Mileage", "FirstRegistrationYear", "Consumption", "Make", "Price" or "LeasingPrice".' . '<br>';
            }
        }
        if(isset($atts['ascending'])) {

            if($atts['ascending'] == 'true') {
                $searchFilter->setAscending(true);
            } else {
                return '<b>' . 'Error: ' . '</b>' . 'Incorrect ascending value: Must be true (defaults to false if attribute is not set' . '<br>';
            }
        }

        // Set status code

        // Remember to add the labels to the list of valid statuscodes AND the switch case: else nothing will be returned!
        $validStatusCodes = array("Sold", "New", "Leasing", "Warehousesale", "Flexleasing", "Export", "Upcoming", "Rental", "Commission", "Wholesale", "Bus", "NewCar", "Demo", "Carlite Dealer Label", "Trailer", "NoTax");

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
            case "Trailer" : $label = 471; break;
            case "NoTax" : $label = 359; break;
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

    /**
     * Filters vehicles by vehicle type (Car, van, motorcycle or truck)
     *
     * @param atts   'type', 'state'
     *
     * @return Status
     */
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

    public function debug_page_menu()
    {
        add_menu_page('Debug page', 'Debug', 'manage_options', 'debug-page', [$this,'debugPage']);
    }

    public function debugPage()
    {
        new DebugController();
    }
}
