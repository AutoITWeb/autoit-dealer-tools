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
use Biltorvet\Model\Property;
use Biltorvet\Model\Vehicle;

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

/** @var string $primaryPriceType */
/** @var bool $isCarousel */
/** @var int $autoplayInterval */
/** @var bool $showNav */
/** @var string $arrowPosition */

// Default values if not set
$isCarousel = isset($isCarousel) ? $isCarousel : false;
$autoplayInterval = isset($autoplayInterval) ? $autoplayInterval : 0;
$showNav = isset($showNav) ? $showNav : true;
$arrowPosition = isset($arrowPosition) ? $arrowPosition : 'sides';

if($isCarousel) {
    if (empty($vehicles)) {
        return;
    }
    // Enqueue carousel styles and scripts
    wp_enqueue_style('bdt-carousel-style');
    wp_enqueue_script('bdt-carousel-script');
    $carouselId = 'bdt-carousel-' . uniqid();

    // Add inline script to initialize this specific carousel
    $inline_script = "
        if(typeof initBDTCarousel === 'function') {
            initBDTCarousel('{$carouselId}', {$autoplayInterval});
        }
    ";
    wp_add_inline_script('bdt-carousel-script', $inline_script);
    ?>
    <div class="bdt bdt-carousel-wrapper bdt-carousel-arrows-<?php echo $arrowPosition; ?>" id="<?php echo $carouselId; ?>" data-show-nav="<?php echo $showNav ? 'true' : 'false'; ?>">
        <div class="bdt-carousel-container">
            <?php if ($showNav && $arrowPosition === 'sides'): ?>
                <button class="bdt-carousel-btn bdt-carousel-prev" aria-label="Previous">‹</button>
            <?php endif; ?>
            <div class="bdt-carousel-track-container">
                <div class="bdt-carousel-track">
                    <?php
                        /** @var  Vehicle[] $vehicles */
                        foreach ($vehicles as $vehicle)  {
                            /** @var Property[] $vehicleProperties */
                            $vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
                            //$priceController = new PriceController($vehicle);

                            echo '<div class="bdt-carousel-slide">';
                            include 'partials/_vehicleCard.php';
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
            <?php if ($showNav && $arrowPosition === 'sides'): ?>
                <button class="bdt-carousel-btn bdt-carousel-next" aria-label="Next">›</button>
            <?php endif; ?>
        </div>
        <?php if ($showNav && $arrowPosition === 'bottom'): ?>
            <div class="bdt-carousel-bottom-nav">
                <button class="bdt-carousel-btn bdt-carousel-prev" aria-label="Previous">‹</button>
                <div class="bdt-carousel-dots"></div>
                <button class="bdt-carousel-btn bdt-carousel-next" aria-label="Next">›</button>
            </div>
        <?php else: ?>
            <?php if ($showNav): ?>
                <div class="bdt-carousel-dots"></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
} else {
    // Regular grid view
    ?>
    <div class="bdt">
        <div class="vehicle_search_results">
            <div class="results">
                <div class="row">

                    <?php
                        /** @var  Vehicle[] $vehicles */
                        foreach ($vehicles as $vehicle)  {
                            /** @var Property[] $vehicleProperties */
                            $vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
                            //$priceController = new PriceController($vehicle);
                            include 'partials/_vehicleCard.php';
                        }
                    ?>

                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
