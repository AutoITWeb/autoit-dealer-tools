<?php
/**
 * Vehicle frontpage search (search parameters and filters) template.
 *
 * This template can be overriden by copying this file to your-theme/biltorvet-dealer-tools/VehicleSearchFrontpage.php
 *
 * @author 		Auto IT A/S
 * @package 	Auto IT Dealer Tools
 * @version     2.2.8
 */

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

global $wp;
$root = dirname(dirname(dirname(plugin_dir_url( __FILE__ ))));

$bdt_root_url = rtrim(get_page_link($this->_options['vehiclesearch_page_id']),'/');

$makeIds = null;
if(isset($atts) && isset($atts['makeids']) && trim($atts['makeids']) !== '')
{
    $makeIds = $atts['makeids'];
}

// The initial filter is only here to hide dropdowns with one value only, so they don't "flash" before Ajax executes.
// All filter logic should be in JS.

try{
    $filterObject = null;

    if(isset($this->_options['hide_sold_vehicles']) && $this->_options['hide_sold_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideSoldVehicles = 'true';
    }
    if(isset($this->_options_2['hide_leasing_vehicles']) && $this->_options_2['hide_leasing_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideLeasingVehicles = 'true';
    }
    if(isset($this->_options_2['hide_flexleasing_vehicles']) && $this->_options_2['hide_flexleasing_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideFlexLeasingVehicles = 'true';
    }
    if(isset($this->_options_2['hide_warehousesale_vehicles']) && $this->_options_2['hide_warehousesale_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideWarehousesaleVehicles = 'true';
    }
    if(isset($this->_options_2['hide_carlite_dealer_label_vehicles']) && $this->_options_2['hide_carlite_dealer_label_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideCarLiteDealerLabelVehicles = 'true';
    }
    if(isset($this->_options_2['hide_export_vehicles']) && $this->_options_2['hide_export_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideExportVehicles = 'true';
    }
    if(isset($this->_options_2['hide_upcoming_vehicles']) && $this->_options_2['hide_upcoming_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideUpcomingVehicles = 'true';
    }
    if(isset($this->_options_2['hide_rental_vehicles']) && $this->_options_2['hide_rental_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideRentalVehicles = 'true';
    }
    if(isset($this->_options_2['hide_commission_vehicle']) && $this->_options_2['hide_commission_vehicle'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideCommissionVehicles = 'true';
    }
    if(isset($this->_options_2['hide_wholesale_vehicle']) && $this->_options_2['hide_wholesale_vehicle'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideWholesaleVehicles = 'true';
    }
    if(isset($this->_options_2['hide_trailer_vehicle']) && $this->_options_2['hide_trailer_vehicle'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideTrailerVehicles = 'true';
    }
    if(isset($this->_options_2['hide_typecar_vehicles']) && $this->_options_2['hide_typecar_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideByTypeCar = 'true';
    }
    if(isset($this->_options_2['hide_typevan_vehicles']) && $this->_options_2['hide_typevan_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideByTypeVan = 'true';
    }
    if(isset($this->_options_2['hide_typemotorcycle_vehicles']) && $this->_options_2['hide_typemotorcycle_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideByTypeMotorcycle = 'true';
    }
    if(isset($this->_options_2['hide_typetruck_vehicles']) && $this->_options_2['hide_typetruck_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideByTypeTruck = 'true';
    }
    if(isset($this->_options_2['hide_typebus_vehicles']) && $this->_options_2['hide_typebus_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideByTypeBus = 'true';
    }
    if(isset($this->_options_2['hide_brandnew_vehicles']) && $this->_options_2['hide_brandnew_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideBrandNewVehicles = 'true';
    }
    if(isset($this->_options['hide_ad_vehicles']) && $this->_options['hide_ad_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideADVehicles = 'true';
    }
    if(isset($this->_options['hide_bi_vehicles']) && $this->_options['hide_bi_vehicles'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideBIVehicles = 'true';
    }
    $initialFilterOptions = $this->biltorvetAPI->GetFilterOptions($filterObject);
} catch(Exception $e) {
    die($e->getMessage());
}

/*
 * As multiple dealers have the frontpage search active a fallback method is needed to avoid
 * having to set it up manually via the plugin settings. If the frontpage search options haven't been touch
 * the col size will be as before the new frontpage search features were added.
 */

$setCol =  (isset($this->_options_5['set_frontpagesearch_column'])) && $this->_options_5['set_frontpagesearch_column'] == "4" ? "4" : "3";

/*
 * The vehicle search is dependent on the sliders so we have to set display none if they aren't activated in the settings.
 * If not the the vehicle search won't load.
 */

$showPriceRange = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_pricerange'])) ? "" : "style='display: none;'";
$showConsumption = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_fuelconsumption'])) ? "" : "style='display: none;'";
$showCompanies = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_company'])) && ($this->_options_5['frontpagesearch_company'] === 'on') || (!isset($this->_options_5['set_frontpagesearch_column'])) ? "" : "style='display: none;'";
$showMakeModel = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_makemodel'])) && ($this->_options_5['frontpagesearch_makemodel'] === 'on') || (!isset($this->_options_5['set_frontpagesearch_column'])) ? "" : "style='display: none;'";
$showVehicleStates = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_vehiclestate'])) && ($this->_options_5['frontpagesearch_vehiclestate'] === 'on') ? "" : "style='display: none;'";
$showProductTypes = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_producttype'])) && ($this->_options_5['frontpagesearch_producttype'] === 'on') ? "" : "style='display: none;'";
$showBodyTypes = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_bodytype'])) && ($this->_options_5['frontpagesearch_bodytype'] === 'on') ? "" : "style='display: none;'";
$showPropellants = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_propellants'])) && ($this->_options_5['frontpagesearch_propellants'] === 'on') ? "" : "style='display: none;'";

?>

<div class="bdt">
    <div id="frontpage_vehicle_search" class="vehicle_search"<?php echo $makeIds !== null ? ' data-makeids="'.$makeIds.'"' : '';  ?>>
        <div class="row">
        <?php if(isset($this->_options_5['frontpagesearch_fulltextsearch'])) : ?>
            <div class="col-sm-12 mb-1 mb-sm-3">
                <input type="text" class="fullTextSearch" name="fullTextSearch" id="fullTextSearch">
            </div>
        <?php endif; ?>
        </div>
        <div class="row justify-content-between">

            <?php if(count($initialFilterOptions->companies) > 1) : ?>
                <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3" <?= $showCompanies ?>>
                    <select name="company" id="company_frontpage">
                        <option value=""><?php _e('- Select department -', 'biltorvet-dealer-tools'); ?></option>
                    </select>
                </div>
            <?php endif; ?>

                <div class="col-sm-<?= $setCol ?> mt-3 mt-sm-0 mb-3" <?= $showVehicleStates ?>>
                    <select name="vehicleState">
                        <option value=""><?php _e('- Select vehicle state -', 'biltorvet-dealer-tools'); ?></option>
                    </select>
                </div>

                <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3" <?= $showMakeModel ?>>
                <select name="make" id="make_frontpage">
                    <option value=""><?php _e('- Select make -', 'biltorvet-dealer-tools'); ?></option>
                </select>
            </div>
            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3" <?= $showMakeModel ?>>
                <select name="model" id="model_frontpage">
                    <option value=""><?php _e('- Select model -', 'biltorvet-dealer-tools'); ?></option>
                </select>
            </div>

                <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3" <?= $showProductTypes ?>>
                    <select name="productType">
                        <option value=""><?php _e('- Select vehicle type -', 'biltorvet-dealer-tools'); ?></option>
                    </select>
                </div>

                <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3" <?= $showBodyTypes ?>>
                    <select name="bodyType">
                        <option value=""><?php _e('- Select body type -', 'biltorvet-dealer-tools'); ?></option>
                    </select>
                </div>

                <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3" <?= $showPropellants ?>>
                    <select name="propellant">
                        <option value=""><?php _e('- Select propellant -', 'biltorvet-dealer-tools'); ?></option>
                    </select>
                </div>

            <div class="col-sm-<?= $setCol ?> mt-3 mt-sm-0 mb-3" <?= $showPriceRange ?>>
                <div class="bdtSliderContainer" >
                    <label for="priceRange" class="float-left"><?php _e('Price', 'biltorvet-dealer-tools'); ?></label>
                    <span class="float-right"><span class="bdtSliderMinVal"></span> - <span class="bdtSliderMaxVal"></span></span>
                    <span class="clearfix"></span>
                    <input class="bdtSlider" id="priceRange" type="text" />
                </div>
            </div>

            <div class="col-sm-<?= $setCol ?> mt-3 mt-sm-0 mb-3" <?= $showConsumption ?>>
                <div class="bdtSliderContainer" >
                    <label for="consumptionRange" class="float-left"><?php _e('Consumption', 'biltorvet-dealer-tools'); ?></label>
                    <span class="float-right"><span class="bdtSliderMinVal"></span> - <span class="bdtSliderMaxVal"></span>km/l</span>
                    <span class="clearfix"></span>
                    <input class="bdtSlider" id="consumptionRange" type="text" />
                </div>
            </div>

            <div class="col-sm-<?= $setCol ?> text-center text-sm-right">
                <button type="button" data-labelpattern="<?php _e('Show %u vehicles', 'biltorvet-dealer-tools'); ?>" class="et_pb_button search bdt_bgcolor" id="vehicle_search_frontpage_button"><?php printf(__('Show %u vehicles', 'biltorvet-dealer-tools'), do_shortcode('[bdt_vehicletotalcount]')); ?></button>
                <button type="button" class="resetFrontpage bdt_color mt-4 mt-sm-2" style="display: none"><?php _e('Reset', 'biltorvet-dealer-tools'); ?></button>
                <div id="root_url" style="display: none;"><?= $bdt_root_url ?></div>
            </div>
        </div>
        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>
</div>