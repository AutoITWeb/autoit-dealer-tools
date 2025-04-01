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

$vehiclePropellant = $vehicle->getPropellant();
if (($vehiclePropellant == "EL" || $vehiclePropellant == "El") && !isset($options_two['hide_elbil_label']))
{
  array_unshift($vehicleLabels, "Elbil");
}
else if ($vehiclePropellant == "Hybrid (B/EL)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Hybrid");
}
else if ($vehiclePropellant == "Hybrid (D/EL)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Hybrid");
}
/* jlk fuld løsning
else if ($vehiclePropellant == "Hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Hybrid (Benzin / El)");
}
else if ($vehiclePropellant == "Hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Hybrid (Diesel / El)");
}
else if ($vehiclePropellant == "Mild hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Mild hybrid (Benzin / El)");
}
else if ($vehiclePropellant == "Mild hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Mild hybrid (Diesel / El)");
}
else if ($vehiclePropellant == "Plug-in hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Plug-in hybrid (Benzin / El)");
}
else if ($vehiclePropellant == "Plug-in hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Plugin-in hybrid (Diesel / El)");
}
*/
//jlk tilpasset
else if ($vehiclePropellant == "Hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Hybrid");
}
else if ($vehiclePropellant == "Hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Hybrid");
}
else if ($vehiclePropellant == "Mild hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Mild hybrid");
}
else if ($vehiclePropellant == "Mild hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Mild hybrid");
}
else if ($vehiclePropellant == "Plug-in hybrid (Benzin / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Plug-in hybrid");
}
else if ($vehiclePropellant == "Plug-in hybrid (Diesel / El)" && !isset($options_two['hide_hybrid_label']))
{
  array_unshift($vehicleLabels, "Plugin-in hybrid");
}
else if ($vehiclePropellant == "Diesel" && isset($options_two['show_diesel_label']) ? $options_two['show_diesel_label'] : null)
{
  array_unshift($vehicleLabels, "Diesel");
}
else if ($vehiclePropellant == "Benzin" && isset($options_two['show_benzin_label']) ? $options_two['show_benzin_label'] : null)
{
  array_unshift($vehicleLabels, "Benzin");
}
else 
{
	//do nothing
}

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
 * Is the special carlite dealer label in use or the other special label fields?
 */

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
$skjulLeasingLabel = isset($options_two['hide_leasing_label']);

$hasVideo = $vehicle->getHasVideo() === true ? ' hasVideo' : '';

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

//jlk
$vehicle_image_small = $vehicle->getVehicleCardImage() ?? $vehicle->getImages()[0];
// Replace "class=S1600X1600" with "class=S640X640" in the image URL
$vehicle_image_small = str_replace("class=S1600X1600", "class=S640X640", $vehicle_image_small);

?>

<div class="col-sm-6 col-md-6 col-lg-4">
    <div class="bdt">
        <div class="vehicleCard animate__animated animate__fadeIn animate__slow">
            <a href="<?= $basePage . "/" . $vehicle->getUri() ?>">
                <span class="vehicleThumb<?= $hasVideo; ?>">
                    <img src="<?= $vehicle_image_small ?>" class="img-responsive" loading="lazy" alt="<?= $vehicle->getMakeName() .' '. $vehicle->getModel() .' '. $vehicle->getVariant() ?>"/>
                        <?php if ($vehicleLabels) : ?>
                            <span class="labelContainer">
                            <?php foreach ($vehicleLabels as $label) : ?>
                                <?php if($label == 'Carlite Forhandler Label' && $carliteDealerLabel != null) : ?>

                                    <?php $dealerSpecificLabel = str_replace("Carlite Forhandler Label", $carliteDealerLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel DealerSpecificLabel"><?= $dealerSpecificLabel; ?></span></p><br>

                                <?php elseif($label == 'Carlite Dealer Label' && $carliteDealerLabel == null) : ?>

                                    <?php unset($vehicleLabels[$label]); ?>
									
                                <?php elseif($label == 'Online køb' && $carliteOnlineKoebLabel != null) : ?>

                                    <?php $OnlineKoebLabel = str_replace("Online køb", $carliteOnlineKoebLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $OnlineKoebLabel; ?></span></p><br>

                                <?php elseif($label == 'Nyhed' && $carliteNyhedLabel != null) : ?>

                                    <?php $NyhedLabel = str_replace("Nyhed", $carliteNyhedLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $NyhedLabel; ?></span></p><br>

                                <?php elseif($label == 'Solgt' && $carliteSolgtLabel != null) : ?>

                                    <?php $SolgtLabel = str_replace("Solgt", $carliteSolgtLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $SolgtLabel; ?></span></p><br>

                                <?php elseif($label == 'Fabriksny' && $carliteFabriksnyLabel != null) : ?>

                                    <?php $FabriksnyLabel = str_replace("Fabriksny", $carliteFabriksnyLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $FabriksnyLabel; ?></span></p><br>
									
                                <?php elseif($label == 'Leasing' && $carliteLeasingLabel != null) : ?>

                                    <?php $LeasingLabel = str_replace("Leasing", $carliteLeasingLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $LeasingLabel; ?></span></p><br>

                                <?php elseif($label == 'Kun engros' && $carliteKunEngrosLabel != null) : ?>

                                    <?php $KunEngrosLabel = str_replace("Kun engros", $carliteKunEngrosLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $KunEngrosLabel; ?></span></p><br>

                                <?php elseif($label == 'Eksport' && $carliteEksportLabel != null) : ?>

                                    <?php $EksportLabel = str_replace("Eksport", $carliteEksportLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $EksportLabel; ?></span></p><br>

                                <?php elseif($label == 'Lagersalg' && $carliteLagersalgLabel != null) : ?>

                                    <?php $LagersalgLabel = str_replace("Lagersalg", $carliteLagersalgLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $LagersalgLabel; ?></span></p><br>

                                <?php elseif($label == 'Demonstration' && $carliteDemonstrationLabel != null) : ?>

                                    <?php $DemonstrationLabel = str_replace("Demonstration", $carliteDemonstrationLabel, $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $DemonstrationLabel; ?></span></p><br>
									
                                <?php elseif($label == 'Demonstration' && $carliteDemonstrationLabel == null) : ?>

                                    <?php $DemonstrationLabel = str_replace("Demonstration", "Demo", $label); ?>
                                    <?php unset($vehicleLabels[$label]); ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $DemonstrationLabel; ?></span></p><br>									

                                <?php elseif (in_array($label, $hybridTypes)) : ?>

                                <p><span class="vehicleLabel Hybrid"><?= $label; ?></span></p><br>
								
                                <?php elseif($label == 'Leasing' && $skjulLeasingLabel) : ?>

                                    <?php unset($vehicleLabels[$label]); ?>							

                                <?php else: ?>

                                    <p><span class="vehicleLabel <?= $label; ?>"><?= $label; ?></span></p><br>

                                <?php endif; ?>
                                
                            <?php endforeach; ?>
                            </span>
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

