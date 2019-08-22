<?php


namespace Biltorvet\Controller;

use Biltorvet\Model\Vehicle;

class DebugController
{
    private $apiController;

    public function __construct()
    {
        $this->renderPage();

        $this->apiController = new ApiController();

        if (isset($_GET['debug_action'])) {
            $this->handleAction($_GET['debug_action']);
        }
    }

    private function handleAction(string $action)
    {

        switch ($action) {
            case 'vehicle_raw':
                if (isset($_GET['vid'])) {
                    $this->renderRawVehicle($this->apiController->getVehicleDetails($_GET['vid']));
                }
                break;
            case 'vehicle_price':
                if (isset($_GET['vid'])) {
                    $this->renderVehiclePrice($this->apiController->getVehicleDetails($_GET['vid']));
                }
                break;
            default:
                throw new \Exception('Unhandled action');
        }
    }

    private function renderRawVehicle(Vehicle $vehicle)
    {
        var_dump($vehicle);
    }

    private function renderVehiclePrice(Vehicle $vehicle)
    {

        $priceController = new PriceController($vehicle);

        var_dump($priceController->getDetailsPrioritizedPrices());
    }

    private function renderPage()
    {

        $tplcontroller = new TemplateController();

        return $tplcontroller->load(
            'admin/debug.php',
            [],
            false
        );
    }
}
