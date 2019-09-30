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
        $searchFilter->setMakes([$atts['make']]);

        // Fetches the wanted make
        $fetchMake = $this->apiController->getVehicles($searchFilter);

        if (isset($atts['models'])) {
            $searchFilter->setModels([$atts['model']]);
        }

        wp_enqueue_style("bdt_style");

        if($fetchMake == null)
        {
            return 'Vi har desværre ingen' . ' ' . ($atts['make']) . 'er' . ' ' . 'på lager.';
        }
        return $this->templateController->load(
            'vehicleCardWrapper.php',
            [
                'vehicles' => $this->apiController->getVehicles($searchFilter),
                'basePage' => WordpressHelper::getOptions()['vehiclesearch_page_id']
            ],
            true
        );
        }

    public function get_sold_vehicles_shortcode()
    {
        $searchFilter = new SearchFilter();

        wp_enqueue_style("bdt_style");
        return $this->templateController->load(
            'vehicleCardWrapper.php',
            [
                'vehicles' => DataHelper::filterVehiclesByLabel($this->apiController->getVehicles($searchFilter), LABEL_SOLD),
                'basePage' => WordpressHelper::getOptions()['vehiclesearch_page_id'],
            ],
            true
        );


    }

    public function get_featured_vehicles_shortcode()
    {

        $searchFilter = New SearchFilter();

        // Count amount of vehicless with the "I fokus" label checked
        $getAllVehicles = DataHelper::filterVehiclesByLabel($this->apiController->getVehicles($searchFilter), LABEL_FEATURED);

        $featuredLabelCount = [];

        foreach($getAllVehicles as $featuredLabel){

            array_push($featuredLabelCount, $featuredLabel);
        }

        $featuredCount = count($featuredLabelCount);

        wp_enqueue_style("bdt_style");

        if($featuredCount >0) {
            return $this->templateController->load(
                'vehicleCardWrapperFeatured.php', [
                'vehicles' => DataHelper::filterVehiclesByLabel($this->apiController->getVehicles($searchFilter), LABEL_FEATURED),
                'basePage' => WordpressHelper::getOptions()['vehiclesearch_page_id'],
            ],
                true
            );
        }
        else
        {
            return $this->templateController->load(
                'vehicleCardWrapperFill.php', [
                'vehicles' => DataHelper::getVehiclePropertiesAssoc($this->apiController->getVehicles($searchFilter)),
                'basePage' => WordpressHelper::getOptions()['vehiclesearch_page_id'],
            ],
                true
            );
        }

    }



    /**
     * @TODO: Refactor
     *
     * @param  array $args
     * @return array
     * @throws Exception
     */
    public function sendLead(array $args)
    {

        if (WordpressHelper::isActivityType(WordpressHelper::getQueryParameter('bdt_actiontype'))) {
            $vehicle = $this->apiController->getVehicleDetails(WordpressHelper::getQueryParameter('bdt_vehicle_id'));

            if ($vehicle === null) {
                return $args;
            }

            // Append the vehicle info to the WP email.
            $args['message'] .= "\r\n\r\n" .  sprintf(__('Selected vehicle: %s', 'biltorvet-dealer-tools'), $vehicle->getModel() . ' (' . $vehicle->getId() . ')');
            // Some e-mail clients don't respect the reply-to header, and then we lose the information about sender. For this reason, we are gluing the sender e-mail back to the e-mail body.
            $args['message'] .= "\r\n\r\n" .  sprintf(__('Lead sender: %s', 'biltorvet-dealer-tools'), WordpressHelper::getReplyTo($args));

            $this->apiController->sendLead(VehicleLeadFactory::create($vehicle, $args, WordpressHelper::getQueryParameters()));
        }

        $args['message'] = MailFormatter::MakeLabelsUppercase($args['message']);

        return $args;
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
