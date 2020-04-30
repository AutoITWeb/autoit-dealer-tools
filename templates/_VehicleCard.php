<?php
    /**
     * A partial template that shows one vehicle in the search results. Can be used also as a standalone.
     *
     * This template can be overriden by copying this file to your-theme/biltorvet-dealer-tools/_VehicleCard.php
     *
     * @author 		Biltorvet A/S
     * @package 	Biltorvet Dealer Tools
     * @version     1.0.0
     */
    if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

    try {
        $cashPrice = $this->biltorvetAPI->GetPropertyValue($vehicle, 'price');
        $leasingMonthlyPayment = $this->biltorvetAPI->GetPropertyValue($vehicle, 'LeasingMonthlyPayment');
        $leasingMonthlyPaymentTotal = $this->biltorvetAPI->GetPropertyValue($vehicle, 'LeasingMonthlyPaymentTotal');
        $leasingMonthlyPaymentRaw = $this->biltorvetAPI->GetPropertyValue($vehicle, 'LeasingMonthlyPayment', true);
        $leasingMonthlyPaymentVAT = $this->biltorvetAPI->GetPropertyValue($vehicle, 'LeasingMonthlyPaymentVAT');
        $financingMonthlyPayment = $this->biltorvetAPI->GetPropertyValue($vehicle, 'FinancingMonthlyPrice');
        $leasingBusiness = $this->biltorvetAPI->GetPropertyValue($vehicle, 'LeasingBusiness');

        $showPrice = $cashPrice;
        $priceLabel = null;
        if(intval($leasingMonthlyPaymentRaw) > 0)
        {
            if ($this->_options['bdt_hide_leasing_prices'] != 'on') {


                if($leasingBusiness === 'Ja')
                {
                    $showPrice = $leasingMonthlyPayment;
                } else {
                    $showPrice = $leasingMonthlyPayment + $leasingMonthlyPaymentVAT;
                }

            }
            $priceLabel = __('Leasing price', 'biltorvet-dealer-tools');
        }
        if(intval($financingMonthlyPayment) > 0 && $this->_options['bdt_hide_financing_prices'] != 'on')
        {
            $showPrice = $financingMonthlyPayment;
            $priceLabel = __('Financing price', 'biltorvet-dealer-tools');
        }

        $modelYear = $this->biltorvetAPI->GetPropertyValue($vehicle, 'firstRegYear');
        $mileage = $this->biltorvetAPI->GetPropertyValue($vehicle, 'mileage');
        $xVat = $this->biltorvetAPI->GetPropertyValue($vehicle, 'XVat') === 'Ja';
    } catch(Exception $e) {
        die($e->getMessage());
    }
?>
<div class="bdt"><div class="vehicleCard">
    <?php if(isset($link)) { ?><a href="<?php echo $link; ?>"><?php } ?>
        <span class="vehicleThumb"><img src="<?php echo $vehicle->images[0]; ?>" class="img-responsive"/></span>
        <?php
            if(isset($vehicle->labels))
            {
                $vehiclesold = false;
                $vehiclenew = false;
                foreach($vehicle->labels as $label)
                {
                    if($label->key == 5)
                    {
                        $vehiclesold = $label->value;
                    }
                    if($label->key == 11)
                    {
                        $vehiclenew = $label->value;
                    }
                }

                if($vehiclesold)
                {
                    ?><span class="vehicleLabel sold"><?php echo $vehiclesold; ?></span><?php
                } elseif($vehiclenew)
                {
                    ?><span class="vehicleLabel new"><?php echo $vehiclenew; ?></span><?php
                }
            }
        ?>
        <span class="vehicleDescription">
            <span class="vehicleTitle">
                <?php echo $vehicle->makeName .' ' . $vehicle->model . ' ' . $vehicle->variant; ?>
            </span>
            <span class="price bdt_color">
                <?php echo isset($showPrice) && trim($showPrice) !== '' ? $showPrice : "-"; ?>
            </span>
            <?php
                if($xVat || $leasingBusiness === 'Ja')
                {
                    ?>
                        <span class="exVat">
                            <?php _e('Excl. VAT', 'biltorvet-dealer-tools'); ?>
                        </span>
                    <?php
                }
            ?>
            <?php
                if($priceLabel !== null)
                {
                    ?>
                        <span class="priceLabel bdt_color">
                            <?php echo $priceLabel; ?>
                        </span>
                    <?php
                }
            ?>
            <span class="row">
                <span class="col-4">
                    <span class="vehicleParamValue"><?php echo isset($modelYear) && trim($modelYear) !== '' ? $modelYear : "-"; ?></span>
                    <span class="vehicleParamLabel"><?php _e('Year of the car', 'biltorvet-dealer-tools'); ?></span>
                </span>
                <span class="col-4">
                    <span class="vehicleParamValue"><?php echo isset($mileage) && trim($mileage) !== '' ? str_replace("km", "", $mileage) : "-"; ?></span>
                    <span class="vehicleParamLabel"><?php _e('Mileage', 'biltorvet-dealer-tools'); ?></span>
                </span>
                <span class="col-4">
                    <span class="vehicleParamValue"><?php echo isset($vehicle->propellant) && trim($vehicle->propellant) !== '' ? $vehicle->propellant : "-"; ?></span>
                    <span class="vehicleParamLabel"><?php _e('Propellant', 'biltorvet-dealer-tools'); ?></span>
                </span>
            </span>
        </span>
    <?php if(isset($link)) { ?></a><?php } ?>
</div></div>