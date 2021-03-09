<?php

$additionalEquipment = $this->currentVehicle->additionalEquipment;

?>

<div class="results">
    <div class="row">
        <?php foreach($additionalEquipment as $a) : ?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <div class="bdt">
                    <div class="vehicleCard">
                        <span class="vehicleThumb">
                            <img src="<?= $a->images[0] ?>" class="img-responsive" loading="lazy" alt="<?= $a->publicName; ?> "/>
                        </span>
                        <span class="vehicleDescription">
                            <span class="vehicleTitle"><?= $a->publicName; ?></span>
                            <span class="price bdt_color"><?= (isset($a->valueFormatted) && $a->valueFormatted != '' ? $a->valueFormatted : "-") ?></span>
                            <span class="price bdt_colo"><?= $a->vatIncluded; ?></span>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>