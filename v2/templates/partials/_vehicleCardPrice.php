<?php

use Biltorvet\Controller\PriceController;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

/** @var PriceController $priceController */
/** @var string $primaryPriceType */

$options_two = get_option('bdt_options_2');

$hideSecondaryAndTertiaryPrice = isset($options_two['bdt_hide_secondary_and_tertiary_price']) && $options_two['bdt_hide_secondary_and_tertiary_price'] === 'on'? true : false;

?>

<span class="vehiclePriceArea">
    <?php if($priceController->GetPrimaryPrice('card', $primaryPriceType)) { echo("<span class='primary-price'>" . $priceController->GetPrimaryPrice('card', $primaryPriceType) . "</span>"); }?>
    
    <?php if($priceController->GetSecondaryPrice('card', $hideSecondaryAndTertiaryPrice)) { echo("<span class='secondary-price'>" . $priceController->GetSecondaryPrice('card', $hideSecondaryAndTertiaryPrice) . "</span>"); }?>

    <?php if($priceController->GetTertiaryPrice('card', $hideSecondaryAndTertiaryPrice)) { echo("<span class='tertiary-price'>" . $priceController->GetTertiaryPrice('card', $hideSecondaryAndTertiaryPrice) . "</span>"); }?>
</span>