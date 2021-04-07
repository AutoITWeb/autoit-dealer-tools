<?php

$additionalEquipment = $this->currentVehicle->additionalEquipment;

?>
<div class="bdt">
    <div class="results">
        <div class="row">
            <?php foreach($additionalEquipment as $a) : ?>
                <div class="col-12 col-sm-4 col-md-3">
                    <div class="bdt">
                        <div class="additionalEquipmentCard">
                        <span class="additionalEquipmentThumb">
                            <img src="<?= $a->images[0] ?>" class="img-responsive" loading="lazy" alt="<?= $a->publicName; ?> "/>
                        </span>
                            <span class="additionalEquipmentDescription">
                            <span class="additionalEquipmentTitle"><?= $a->publicName; ?></span>
                            <span class="price bdt_color"><?= (isset($a->valueFormatted) && $a->valueFormatted != '' ? $a->valueFormatted : "-") ?></span>
                            <span class="vat"><?= $a->vatIncluded; ?></span>
                        </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>