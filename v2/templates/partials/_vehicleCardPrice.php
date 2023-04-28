<?php

use Biltorvet\Controller\PriceController;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

/** @var PriceController $priceController */
/** @var string $primaryPriceType */

$options_two = get_option('bdt_options_2');

$hideSecondaryPrice = isset($options_two['bdt_hide_secondary_price']) && $options_two['bdt_hide_secondary_price'] === 'on'? true : false;

?>

<span class="vehiclePriceArea">
    <?php if($priceController->GetPrimaryPrice('card', $primaryPriceType)) { echo("<span class='primary-price'>" . $priceController->GetPrimaryPrice('card', $primaryPriceType) . "</span>"); }?>
    
    <?php if($priceController->GetSecondaryPrice('card', $hideSecondaryPrice)) { echo("<span class='secondary-price'>" . $priceController->GetSecondaryPrice('card', $hideSecondaryPrice) . "</span>"); }?>

    <?php if($priceController->GetTertiaryPrice('card')) { echo("<span class='tertiary-price'>" . $priceController->GetTertiaryPrice('card') . "</span>"); }?>
</span>

