<?php
/**
 * Reused the search result markup.
 * A partial template that shows one vehicle in the search results. Can be used also as a standalone.
 *
 * This template can be overriden by copying this file to your-theme/biltorvet-dealer-tools/_VehicleCard.php
 *
 * @author 		Biltorvet A/S
 * @package 	Biltorvet Dealer Tools
 * @version     1.0.0
 */

use Biltorvet\Controller\PriceController;
use Biltorvet\Helper\DataHelper;
use Biltorvet\Helper\WordpressHelper;
use Biltorvet\Model\Property;
use Biltorvet\Model\Vehicle;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

?>

<div class="bdt">
    <div class="vehicle_search_results">
        <div class="results">
            <div class="row">

                <?php
                /** @var  Vehicle[] $vehicles */
                foreach (array_slice($vehicles, 0, 3) as $vehicle)  {
                    /** @var Property[] $vehicleProperties */
                    $vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
                    $priceController = new PriceController($vehicle);
                    include 'partials/_vehicleCard.php';
                }
                ?>

            </div>
        </div>
    </div>
</div>