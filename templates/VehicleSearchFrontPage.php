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

// The initial filter is only here to hide dropdowns with one value only, so they don't "flash" before Ajax executes.
// All filter logic should be in JS.

try{
    //$filterObject = null;
    $filterObject = new BDTFilterObject();

    if(isset($this->_options_2['hide_sold_vehicles']) && $this->_options_2['hide_sold_vehicles'] === 'on')
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
    if(isset($this->_options_2['hide_classic_vehicle']) && $this->_options_2['hide_classic_vehicle'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideClassicVehicles = 'true';
    }
    if(isset($this->_options_2['hide_tractor_vehicle']) && $this->_options_2['hide_tractor_vehicle'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideTractorVehicles = 'true';
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
	//jlk
    if(isset($this->_options_2['hide_internal_vehicles_bilinfo']) && $this->_options_2['hide_internal_vehicles_bilinfo'] === 'on')
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->HideInternalVehiclesBilInfo = 'true';
    }	
    if(isset($this->_options_2['bdt_pricetypes']) && $this->_options_2['bdt_pricetypes'] != "-1")
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->PriceTypes = array($this->_options_2['bdt_pricetypes']);
    }
	//jlk
    if(isset($this->_options_2['bdt_propellanttypes']) && $this->_options_2['bdt_propellanttypes'] != "-1")
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        //$filterObject->PropellantTypes = array($this->_options_2['bdt_propellanttypes']);
		$filterObject->Propellants = array($this->_options_2['bdt_propellanttypes']);
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
$showVehicleStates = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_vehiclestate'])) && ($this->_options_5['frontpagesearch_vehiclestate'] === 'on') ? "" : "style='display: none;'";
$showMakeModel = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_makemodel'])) && ($this->_options_5['frontpagesearch_makemodel'] === 'on') || (!isset($this->_options_5['set_frontpagesearch_column'])) ? "" : "style='display: none;'";
$showProductTypes = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_producttype'])) && ($this->_options_5['frontpagesearch_producttype'] === 'on') ? "" : "style='display: none;'";
$showPriceTypes = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_pricetype'])) && ($this->_options_5['frontpagesearch_pricetype'] === 'on') ? "" : "style='display: none;'";
$showBodyTypes = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_bodytype'])) && ($this->_options_5['frontpagesearch_bodytype'] === 'on') ? "" : "style='display: none;'";
$showPropellants = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_propellants'])) && ($this->_options_5['frontpagesearch_propellants'] === 'on') ? "" : "style='display: none;'";
//jlk
$showElectricRange = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_electricrange'])) ? "" : "style='display: none;'";

// Custom Vehicle Types - variables only related to the frontpage search
$showCustomVehicleTypesSection = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_activate_iconbased_search'])) && ($this->_options_5['frontpagesearch_activate_iconbased_search'] === 'on') ? "" : "display: none;";
$customVehicleTypeBackgroundColor = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_iconbased_search_background_color'])) && $this->_options_5['frontpagesearch_iconbased_search_background_color'] !== '' ? $this->_options_5['frontpagesearch_iconbased_search_background_color'] : "transparent";

// Custom color selected?
$customVehicleTypeIconColor = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_iconbased_search_icon_color'])) && $this->_options_5['frontpagesearch_iconbased_search_icon_color'] !== '' ? $this->_options_5['frontpagesearch_iconbased_search_icon_color'] : (isset($this->_options['primary_color']) && trim($this->_options['primary_color']) !== '' ? $this->_options['primary_color'] : "#00a1b7");

?>

<div class="bdt">
    <div id="frontpage_vehicle_search" class="vehicle_search">
        <span class="hide-bdt animate__animated animate__fadeIn" id="bdt-loading-filters">
        
        <!-- Icon-based search aka Custom Vehicle Types -->
        <div class="car-model-container" style="<?= $showCustomVehicleTypesSection; ?> background-color: <?= $customVehicleTypeBackgroundColor; ?>;">
            <?php
            require Biltorvet::bdt_get_template("/partials/_customVehicleTypes.php");
            ?>
        </div>

        <div class="row">

        <?php if(isset($this->_options_5['frontpagesearch_fulltextsearch'])) : ?>
        <div class="col-sm-12 mb-1 mb-sm-3">
            <input type="text" class="fullTextSearch" name="fullTextSearch" id="fullTextSearch">
        </div>
        <?php endif; ?>

        <?php if(isset($this->_options_5['frontpagesearch_quicksearch'])) : ?>
            <div class="col-sm-12 mb-1 mb-sm-3" id="quicksearch-input">
                <input class="quicksearch multiple-quicksearch" name="quicksearch" id="quicksearch" multiple="">
            </div>
        <?php endif; ?>
        </div>

        <div class="row justify-content-between">

            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3 multiple-select" <?= $showCompanies ?>>
                <select class="company multiple" multiple="multiple" name="company" id="company" data-contenttype="afdelinger"></select>
                <label class="selectDropDownLabel">
                    <span class="placeholder-text"><?php _e('- Select department -', 'biltorvet-dealer-tools'); ?></span>
                </label>
            </div>

            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3 multiple-select" <?= $showMakeModel ?>>
                <select class="make multiple" multiple="multiple" name="make" id="make" data-contenttype="mærker"></select>
                <label class="selectDropDownLabel">
                    <span class="placeholder-text"><?php _e('- Select make -', 'biltorvet-dealer-tools'); ?></span>
                </label>
            </div>

            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3 multiple-select" <?= $showMakeModel ?>>
                <select class="model multiple" multiple="multiple" name="model" id="model" data-contenttype="modeller"></select>
                <label class="selectDropDownLabel">
                    <span class="placeholder-text"><?php _e('- Select model -', 'biltorvet-dealer-tools'); ?></span>
                </label>
            </div>

            <div class="col-sm-<?= $setCol ?> mt-3 mt-sm-0 mb-3 multiple-select" <?= $showVehicleStates ?>>
                <select class="vehiclestate multiple" multiple="multiple" name="vehicleState" id="vehicleState" data-contenttype="stande"></select>
                <label class="selectDropDownLabel">
                    <span class="placeholder-text"><?php _e('- Select vehicle state -', 'biltorvet-dealer-tools'); ?></span>
                </label>
            </div>

            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3 multiple-select" <?= $showPriceTypes ?>>
				<?php if(isset($this->_options_2['bdt_price_label_leasing_searchfilter']) && $this->_options_2['bdt_price_label_leasing_searchfilter'] != null) {
                        echo '<span id="LeasingAlternativeName" hidden>' . $this->_options_2['bdt_price_label_leasing_searchfilter'] . '</span>';
                        }; ?>
                <select class="pricetype multiple" multiple="multiple" name="priceType" id="priceType" data-contenttype="pristyper"></select>
                <label class="selectDropDownLabel">
                    <span class="placeholder-text"><?php _e('- Select price type -', 'biltorvet-dealer-tools'); ?></span>
                </label>
            </div>

            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3 multiple-select" <?= $showProductTypes ?>>
                <select class="producttype multiple" multiple="multiple" name="productType" id="productType" data-contenttype="køretøjstyper"></select>
                <label class="selectDropDownLabel">
                    <span class="placeholder-text"><?php _e('- Select vehicle type -', 'biltorvet-dealer-tools'); ?></span>
                </label>
            </div>

            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3 multiple-select" <?= $showBodyTypes ?>>
                <select class="bodytype multiple" multiple="multiple" name="bodyType" id="bodyType" data-contenttype="karosserityper"></select>
                <label class="selectDropDownLabel">
                    <span class="placeholder-text"><?php _e('- Select body type -', 'biltorvet-dealer-tools'); ?></span>
                </label>
            </div>

            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3 multiple-select" <?= $showPropellants ?>>
                <select class="propellant multiple" multiple="multiple" name="propellant" id="propellant" data-contenttype="drivmiddeltyper"></select>
                <label class="selectDropDownLabel">
                    <span class="placeholder-text"><?php _e('- Select propellant -', 'biltorvet-dealer-tools'); ?></span>
                </label>
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

			<!--jlk-->			
            <div class="col-sm-<?= $setCol ?> mt-3 mt-sm-0 mb-3" <?= $showElectricRange ?>>
                <div class="bdtSliderContainer" >
                    <label for="electricRange" class="float-left"><?php _e('ElectricRange', 'biltorvet-dealer-tools'); ?></label>
                    <span class="float-right"><span class="bdtSliderMinVal"></span> - <span class="bdtSliderMaxVal"></span>km</span>
                    <span class="clearfix"></span>
                    <input class="bdtSlider" id="electricRange" type="text" />
                </div>
            </div>			

            <div class="col-sm-<?= $setCol ?> text-center text-sm-right">
                <button type="button" data-labelpattern="<?php _e('Show %u vehicles', 'biltorvet-dealer-tools'); ?>" class="et_pb_button bdt_bgcolor" id="vehicle_search_frontpage_button" style="width: 100%;"><?php printf(__('Show %u vehicles', 'biltorvet-dealer-tools'), do_shortcode('[bdt_vehicletotalcount]')); ?></button>
                <button type="button" class="reset bdt_color mt-4 mt-sm-2"><?php _e('Reset', 'biltorvet-dealer-tools'); ?></button>
                <div id="root_url" style="display: none;"><?= $bdt_root_url ?></div>
            </div>
        </div>
            
        </span>
        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>
</div>
