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
/** @var PriceController $priceController */

$options_two = get_option('bdt_options_2');

// Sorted labels for use on the vehiclecards
$vehicleLabels = Vehicle::sortVehicleLabels($vehicle->getLabels());

$carliteDealerLabel = isset($options_two['carlite_dealer_label']) ? $options_two['carlite_dealer_label'] : null;

?>

<div class="col-sm-6 col-md-6 col-lg-4">
    <div class="bdt">
        <div class="vehicleCard">
            <a href="<?= get_permalink($basePage) . $vehicle->getUri() ?>">
                <span class="vehicleThumb">
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
                    <span class="vehicleTitle"><?= $vehicle->getMakeName() .' '. $vehicle->getModel() .' '. $vehicle->getVariant() ?></span>
                    <span class="price bdt_color"><?= $priceController->getCardPrioritizedPrice() ?></span>
                    <span class="priceLabel bdt_color"><?= $priceController->getCardLabel() ?></span>
                    <span class="bdt_price_small_cashprice_vehicle_card"><?= $priceController->showCashPriceFinanceAndLeasing() ?></span>
                    <span class="row">
                        <span class="col-4">
                            <span class="vehicleParamValue">

                                <?php if ($vehicleProperties['ModelYear']->getValue() == $vehicleProperties['FirstRegYear']->getValue()) : ?>
                                    <?= $vehicleProperties['ModelYear']->getValue() ? $vehicleProperties['ModelYear']->getValue() : '-'; ?>
                                <?php endif; ?>

                                <?php if ($vehicleProperties['ModelYear']->getValue() != $vehicleProperties['FirstRegYear']->getValue()) : ?>
                                    <?= $vehicleProperties['ModelYear']->getValue() ? $vehicleProperties['ModelYear']->getValue() : '-'; ?><?= $vehicleProperties['FirstRegYear']->getValue() ? " / " . $vehicleProperties['FirstRegYear']->getValue() : ''; ?>
                                <?php endif; ?>

                            </span>
                            <span class="vehicleParamLabel"><?php _e('ModelYear', 'biltorvet-dealer-tools'); ?></span>
                        </span>
                        <span class="col-4">
                            <span class="vehicleParamValue"><?= $vehicleProperties['Mileage']->getValueFormatted() ? $vehicleProperties['Mileage']->getValueFormatted() : '-'; ?></span>
                            <span class="vehicleParamLabel"><?php _e('Mileage', 'biltorvet-dealer-tools'); ?></span>
                        </span>
                        <span class="col-4">
                            <span class="vehicleParamValue"><?= $vehicle->getPropellant() ?? '-' ?></span>
                            <span class="vehicleParamLabel"><?php _e('Propellant', 'biltorvet-dealer-tools'); ?></span>
                        </span>
                    </span>
                </span>
            </a>
        </div>
    </div>
</div>
