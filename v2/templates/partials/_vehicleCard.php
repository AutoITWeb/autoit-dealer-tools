<?php
/**
 * Reused the search result markup.
 * A partial template that shows one vehicle in the search results. Can be used also as a standalone.
 *
 * This template can be overriden by copying this file to your-theme/biltorvet-dealer-tools/v2/templates/_vehicleCard.php
 *
 * @author 		Biltorvet A/S
 * @package 	Biltorvet Dealer Tools
 * @version     1.0.0
 */

use Biltorvet\Controller\PriceController;
use Biltorvet\Model\Property;
use Biltorvet\Model\Vehicle;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

/** @var string $basePage */
/** @var Vehicle $vehicle */
/** @var Property[] $vehicleProperties */
/** @var string $primaryPriceType */

// Has as primary price type been selected (via a shortcode)
if(!isset($primaryPriceType))
{
    $primaryPriceType = null;
}

$options_two = get_option('bdt_options_2');
$basePage = rtrim(get_permalink(get_option('bdt_options')['vehiclesearch_page_id']),'/');

// Sorted labels for use on the vehiclecards
$vehicleLabels = Vehicle::sortVehicleLabels($vehicle->getLabels(), isset($options_two['show_all_labels']) ?? null);

if(count($vehicleLabels) > 5) {
    $vehicleLabels = array_slice($vehicleLabels, 0, 5);
}

/*
 * Handle which props to show on the vehicle card
 */

if(isset($options_two['vehiclecard_prop_one'])) {
    $paramValueColumnOne = $options_two['vehiclecard_prop_one'] != '-1' ? $options_two['vehiclecard_prop_one'] : '0';
}
else {
    $paramValueColumnOne = '0';
}
if(isset($options_two['vehiclecard_prop_two'])) {
    $paramValueColumnTwo = $options_two['vehiclecard_prop_two'] != '-1' ? $options_two['vehiclecard_prop_two'] : '4';
}
else {
    $paramValueColumnTwo = '4';
}
if(isset($options_two['vehiclecard_prop_three'])) {
    $paramValueColumnThree = $options_two['vehiclecard_prop_three'] != '-1' ? $options_two['vehiclecard_prop_three'] : '6';
}
else {
    $paramValueColumnThree = '6';
}

/*
 * Is the special carlite dealer label in use?
 */

$carliteDealerLabel = isset($options_two['carlite_dealer_label']) ? $options_two['carlite_dealer_label'] : null;

$hasVideo = $vehicle->getHasVideo() === true ? ' hasVideo' : '';

?>

<div class="col-sm-6 col-md-6 col-lg-4">
    <div class="bdt">
        <div class="vehicleCard animate__animated animate__fadeIn animate__slow">
            <a href="<?= $basePage . "/" . $vehicle->getUri() ?>">
                <span class="vehicleThumb<?= $hasVideo; ?>">
                    <img src="<?= $vehicle->getVehicleCardImage() ?? $vehicle->getImages()[0] ?>" class="img-responsive" loading="lazy" alt="<?= $vehicle->getMakeName() .' '. $vehicle->getModel() .' '. $vehicle->getVariant() ?>"/>
                        <?php if ($vehicleLabels) : ?>

                            <?php foreach ($vehicleLabels as $label) : ?>
                                <?php if($label == 'Carlite Forhandler Label' && $carliteDealerLabel != null) : ?>

                                    <?php $dealerSpecificLabel = str_replace("Carlite Forhandler Label", $carliteDealerLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel DealerSpecificLabel"><?= $dealerSpecificLabel; ?></span></p><br>

                                <?php elseif($label == 'Carlite Dealer Label' && $carliteDealerLabel == null) : ?>

                                    <?php unset($vehicleLabels[$label]); ?>

                                <?php else: ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $label; ?></span></p><br>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        <?php endif; ?>
                </span>
                <span class="vehicleDescription">
                    <span class="vehicleTitle">
                        <span class="make-model"><?= $vehicle->getMakeName() .' '. $vehicle->getModel() ?></span>
                        <span class="variant"><?= $vehicle->getVariant() ?></span>
                    </span>

                    <?php

                    $priceController = new PriceController($vehicle);
                    require PLUGIN_ROOT . 'templates/partials/_vehicleCardPrice.php';

                    ?>
                    <span class="row">
                        <span class="col-4">
                            <?= Vehicle::getVehicleParam($paramValueColumnOne, $vehicleProperties, $vehicle) ?>
                        </span>
                        <span class="col-4">
                           <?= Vehicle::getVehicleParam($paramValueColumnTwo, $vehicleProperties, $vehicle) ?>
                        </span>
                        <span class="col-4">
                             <?= Vehicle::getVehicleParam($paramValueColumnThree, $vehicleProperties, $vehicle) ?>
                        </span>
                    </span>
                </span>
            </a>
        </div>
    </div>
</div>

