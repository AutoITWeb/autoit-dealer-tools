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
    if(isset($this->_options_2['bdt_pricetypes']) && $this->_options_2['bdt_pricetypes'] != "-1")
    {
        if($filterObject === null)
        {
            $filterObject = new BDTFilterObject();
        }
        $filterObject->PriceTypes = array($this->_options_2['bdt_pricetypes']);
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

// Custom Vehicle Types

// Show icons?
$showCustomVehicleTypesSection = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_activate_iconbased_search'])) && ($this->_options_5['frontpagesearch_activate_iconbased_search'] === 'on') ? "" : "display: none;";

$customVehicleTypeIconColor = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_iconbased_search_icon_color'])) && $this->_options_5['frontpagesearch_iconbased_search_icon_color'] !== '' ? $this->_options_5['frontpagesearch_iconbased_search_icon_color'] : (isset($this->_options['primary_color']) && trim($this->_options['primary_color']) !== '' ? $this->_options['primary_color'] : "#00a1b7");
$customVehicleTypeBackgroundColor = (isset($this->_options_5)) && (isset($this->_options_5['frontpagesearch_iconbased_search_background_color'])) && $this->_options_5['frontpagesearch_iconbased_search_background_color'] !== '' ? $this->_options_5['frontpagesearch_iconbased_search_background_color'] : "transparent";

$customVehicleTypeMicro = "style='display: none;'";;
$customVehicleTypeHatchback = "style='display: none;'";;
$customVehicleTypeFamilyCar = "style='display: none;'";;
$customVehicleTypeStationcar = "style='display: none;'";;
$customVehicleTypeSuv = "style='display: none;'";;
$customVehicleTypeElectricAndHybrid = "style='display: none;'";;

if(!empty($initialFilterOptions->customVehicleTypes))
{
    foreach($initialFilterOptions->customVehicleTypes as $cvt)
    {
        switch($cvt->name)
        {
            case "Micro":
                $customVehicleTypeMicro = "";
                break;
            case "Hatchback":
                $customVehicleTypeHatchback = "";
                break;
            case "FamilyCar":
                $customVehicleTypeFamilyCar = "";
                break;
            case "Stationcar":
                $customVehicleTypeStationcar = "";
                break;
            case "SUV":
                $customVehicleTypeSuv = "";
                break;
            case "ElectricAndHybrid":
                $customVehicleTypeElectricAndHybrid = "";
                break;
        }
    }
}

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

            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3" <?= $showCompanies ?>>
                <select name="company" id="company_frontpage">
                    <option value=""><?php _e('- Select department -', 'biltorvet-dealer-tools'); ?></option>
                </select>
            </div>

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

            <div class="col-sm-<?= $setCol ?> mb-1 mb-sm-3" <?= $showPriceTypes ?>>
                <select name="priceType">
                    <option value=""><?php _e('- Select price type -', 'biltorvet-dealer-tools'); ?></option>
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

            <div>
                <span id="custom-vehicle-type-selected" class="custom-vehicle-type-selected" data-custom-vehicle-type-selected=""></span>
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
                <button type="button" data-labelpattern="<?php _e('Show %u vehicles', 'biltorvet-dealer-tools'); ?>" class="et_pb_button bdt_bgcolor" id="vehicle_search_frontpage_button" style="width: 100%;"><?php printf(__('Show %u vehicles', 'biltorvet-dealer-tools'), do_shortcode('[bdt_vehicletotalcount]')); ?></button>
                <button type="button" class="resetFrontpage bdt_color mt-4 mt-sm-2"><?php _e('Reset', 'biltorvet-dealer-tools'); ?></button>
                <div id="root_url" style="display: none;"><?= $bdt_root_url ?></div>
            </div>
        </div>

        <!-- Icon-based search aka Custom Vehicle Types -->

        <div class="car-model-container" style="<?= $showCustomVehicleTypesSection; ?> background-color: <?= $customVehicleTypeBackgroundColor; ?>;">
            <div class="car-icon-container" data-custom-vehicle-type="0" <?= $customVehicleTypeMicro ?>>
                <a href="/" id="cvt-micro"
                ><svg
                            viewBox="0 0 143 105"
                            class="car"
                            xmlns="http://www.w3.org/2000/svg"
                    >
                        <g transform="matrix(1, 0, 0, 1, -212.374512, -83.815002)">
                            <title>Micro</title>
                            <ellipse cx="281.555" cy="186.74" rx="65.541" ry="2.075" />
                            <path style="fill: <?= $customVehicleTypeIconColor; ?>;"
                                    d="M 262.213 171.839 C 262.213 179.741 255.807 186.147 247.905 186.147 C 240.003 186.147 233.597 179.741 233.597 171.839 C 233.597 163.937 240.003 157.531 247.905 157.531 C 255.807 157.531 262.213 163.937 262.213 171.839 Z M 339.036 171.794 C 339.036 179.696 332.63 186.102 324.728 186.102 C 316.826 186.102 310.42 179.696 310.42 171.794 C 310.42 163.892 316.826 157.486 324.728 157.486 C 332.63 157.486 339.036 163.892 339.036 171.794 Z M 272.74 117.309 C 290.153 113.111 331.405 116.009 331.405 116.009 C 333.081 116.065 331.015 119.065 331.015 119.065 C 334.884 124.764 340.964 132.578 343.408 140.153 C 345.555 146.808 345.487 161.027 345.487 161.027 C 345.487 161.027 348.975 161.182 349.669 161.857 C 349.669 161.857 351.087 172.359 349.378 175.309 C 347 179.415 343.523 178.13 343.523 178.13 C 343.523 178.13 343.771 166.532 340.655 161.87 C 337.151 156.626 330.554 152.327 324.248 152.447 C 317.534 152.575 311.28 157.205 307.879 162.855 C 304.633 168.247 305.739 179.706 305.739 179.706 L 266.455 179.906 C 266.455 179.906 267.478 166.815 264.545 161.853 C 261.193 156.181 255.422 152.721 248.836 152.539 C 242.105 152.353 235.118 156.302 231.355 161.886 C 228.424 166.236 229.368 177.537 229.368 177.537 L 221.81 177.593 C 223.246 174.79 222.416 164.13 222.416 164.13 C 222.897 162.933 225.099 161.34 225.099 161.34 C 225.099 161.34 221.862 155.333 226.411 149.73 C 226.411 149.73 258.587 120.721 272.74 117.309 Z M 256.086 136.182 C 256.086 136.182 260.501 135.335 263.505 136.776 C 265.233 137.605 264.076 141.542 264.076 141.542 L 313.867 140.341 C 313.748 133.773 312.588 128.938 309.058 119.953 C 309.058 119.953 281.674 119.395 274.462 121.388 C 265.633 123.828 256.086 136.182 256.086 136.182 Z M 315.261 119.89 C 314.98 126.256 322.078 139.633 322.078 139.633 L 337.573 139.482 C 338.806 134.437 325.635 120.409 325.635 120.409 Z"
                            />
                        </g>
                    </svg>
                    <span class="caption" style="color: <?= $customVehicleTypeIconColor; ?>;">Mikrobil</span>
                </a>
            </div>
            <div class="car-icon-container" data-custom-vehicle-type="1" <?= $customVehicleTypeHatchback ?>>
                <a href="/" id="cvt-hatchback"
                ><svg
                            viewBox="0 0 210 105"
                            class="car"
                            xmlns="http://www.w3.org/2000/svg"
                    >
                        <g transform="matrix(1, 0, 0, 1, -211.265366, -83.815002)">
                            <title>Hatchback</title>
                            <ellipse cx="316.937" cy="186.115" rx="100.922" ry="2.3" />
                            <path style="fill: <?= $customVehicleTypeIconColor; ?>;"
                                    d="M 263.787 170.174 C 263.787 178.942 256.679 186.05 247.911 186.05 C 239.143 186.05 232.035 178.942 232.035 170.174 C 232.035 161.406 239.143 154.298 247.911 154.298 C 256.679 154.298 263.787 161.406 263.787 170.174 Z M 396 170.595 C 396 179.363 388.892 186.471 380.124 186.471 C 371.356 186.471 364.248 179.363 364.248 170.595 C 364.248 161.827 371.356 154.719 380.124 154.719 C 388.892 154.719 396 161.827 396 170.595 Z M 311.058 119.442 C 328.471 115.244 370.915 120.017 370.915 120.017 C 382.304 122.54 400.579 132.384 404.102 137.105 C 407.171 141.218 407.907 162.923 407.907 162.923 C 407.907 162.923 410.023 163.145 410.717 163.82 C 410.717 163.82 412.667 171.68 409.653 175.23 C 406.583 178.847 400.518 177.976 400.518 177.976 C 400.518 177.976 401.143 165.516 397.936 160.628 C 394.114 154.803 387.163 149.631 380.196 149.577 C 372.757 149.519 364.555 155.327 361.072 161.08 C 357.826 166.441 359.206 179.79 359.206 179.79 L 269.04 179.127 C 269.04 179.127 269.782 165.798 266.595 160.729 C 262.784 154.668 256.214 150.098 248.817 149.565 C 241.288 149.023 233.786 153.936 229.466 160.126 C 226.176 164.841 227.022 176.978 227.022 176.978 C 227.022 176.978 218.707 178.44 216.012 175.925 C 212.144 172.315 213.751 160.267 213.751 160.267 C 214.395 159.557 216.448 159.522 216.448 159.522 C 216.448 159.522 217.744 153.052 221.172 150.147 C 229.362 143.207 271.666 138.303 271.666 138.303 C 271.666 138.303 296.905 122.854 311.058 119.442 Z M 287.535 136.8 C 287.535 136.8 293.759 135.815 295.483 137.924 C 296.696 139.408 295.174 143.147 295.174 143.147 L 331.758 142.122 C 331.639 135.554 334.693 122.784 334.693 122.546 C 334.693 122.308 319.464 121.704 312.252 123.697 C 303.423 126.137 287.535 136.8 287.535 136.8 Z M 340.9 122.307 C 337.774 128.025 338.035 142.122 338.035 142.122 L 378.897 139.885 C 378.505 134.628 367.789 125.464 367.789 125.464 C 359.122 120.815 340.9 122.307 340.9 122.307 Z"
                            />
                        </g>
                    </svg>
                    <span class="caption" style="color: <?= $customVehicleTypeIconColor; ?>;">Hatchback</span>
                </a>
            </div>
            <div class="car-icon-container" data-custom-vehicle-type="2" <?= $customVehicleTypeFamilyCar ?>>
                <a href="/" id="cvt-familycar">
                    <svg
                            viewBox="0 0 219 105"
                            class="car"
                            xmlns="http://www.w3.org/2000/svg"
                    >
                        <g transform="matrix(1, 0, 0, 1, -210.975739, -83.815002)">
                            <title>Sedan</title>
                            <ellipse cx="320.879" cy="186.539" rx="104.865" ry="2.276" />
                            <path style="fill: <?= $customVehicleTypeIconColor; ?>;"
                                    d="M 263.787 170.174 C 263.787 178.942 256.679 186.05 247.911 186.05 C 239.143 186.05 232.035 178.942 232.035 170.174 C 232.035 161.406 239.143 154.298 247.911 154.298 C 256.679 154.298 263.787 161.406 263.787 170.174 Z M 311.058 119.442 C 328.471 115.244 364.269 118.179 364.269 118.179 C 375.658 120.702 386.196 127.878 397.032 133.429 C 405.214 137.621 416.497 136.847 421.171 141.406 C 424.665 144.814 423.602 160.519 423.602 160.519 C 423.602 160.519 425.436 160.883 426.13 161.558 C 426.13 161.558 427.373 170.549 424.359 174.099 C 421.289 177.716 406.315 177.694 406.315 177.694 C 406.315 177.694 407.224 164.092 403.45 159.073 C 399.127 153.325 391.048 149.505 383.873 150.001 C 376.98 150.478 370.105 155.361 366.445 161.221 C 363.218 166.387 364.297 179.366 364.297 179.366 L 269.04 179.127 C 269.04 179.127 269.782 165.798 266.595 160.729 C 262.784 154.668 256.214 150.098 248.817 149.565 C 241.288 149.023 233.786 153.936 229.466 160.126 C 226.176 164.841 227.022 176.978 227.022 176.978 C 227.022 176.978 218.707 178.44 216.012 175.925 C 212.144 172.315 213.751 160.267 213.751 160.267 C 214.395 159.557 216.448 159.522 216.448 159.522 C 216.448 159.522 216.754 152.204 220.182 149.299 C 228.372 142.359 271.666 138.303 271.666 138.303 C 271.666 138.303 296.905 122.854 311.058 119.442 Z M 287.535 136.8 C 287.535 136.8 293.759 135.815 295.483 137.924 C 296.696 139.408 295.174 143.147 295.174 143.147 L 331.758 142.122 C 331.639 135.554 334.693 122.784 334.693 122.546 C 334.693 122.308 319.464 121.704 312.252 123.697 C 303.423 126.137 287.535 136.8 287.535 136.8 Z M 340.9 122.307 C 337.774 128.025 338.035 142.122 338.035 142.122 L 367.161 140.451 C 366.769 135.194 368.355 125.888 368.355 125.888 C 359.688 121.239 340.9 122.307 340.9 122.307 Z M 370.912 126.647 C 369.668 131.238 369.689 140.451 369.689 140.451 L 380.011 140.072 C 380.011 140.072 383.654 135.864 381.808 131.602 Z M 401 170.595 C 401 179.363 393.892 186.471 385.124 186.471 C 376.356 186.471 369.248 179.363 369.248 170.595 C 369.248 161.827 376.356 154.719 385.124 154.719 C 393.892 154.719 401 161.827 401 170.595 Z"
                            />
                        </g>
                    </svg>
                    <span class="caption" style="color: <?= $customVehicleTypeIconColor; ?>;">Familiebil</span>
                </a>
            </div>
            <div class="car-icon-container" data-custom-vehicle-type="3" <?= $customVehicleTypeStationcar ?>>
                <a href="/" id="cvt-stationwagon">
                    <svg style="fill: <?= $customVehicleTypeIconColor; ?>;"
                            viewBox="0 0 220 105"
                            class="car"
                            xmlns="http://www.w3.org/2000/svg"
                    >
                        <g transform="matrix(1, 0, 0, 1, -209.585358, -83.815002)">
                            <title>Station wagon</title>
                            <ellipse cx="320.879" cy="186.539" rx="104.865" ry="2.276" />
                            <path
                                    d="M 263.787 170.174 C 263.787 178.942 256.679 186.05 247.911 186.05 C 239.143 186.05 232.035 178.942 232.035 170.174 C 232.035 161.406 239.143 154.298 247.911 154.298 C 256.679 154.298 263.787 161.406 263.787 170.174 Z M 401 170.595 C 401 179.363 393.892 186.471 385.124 186.471 C 376.356 186.471 369.248 179.363 369.248 170.595 C 369.248 161.827 376.356 154.719 385.124 154.719 C 393.892 154.719 401 161.827 401 170.595 Z M 311.058 119.442 C 328.471 115.244 399.758 115.936 409.028 121.562 C 418.314 127.198 415.584 136.065 420.258 140.624 C 423.752 144.032 423.602 160.519 423.602 160.519 C 423.602 160.519 425.436 160.883 426.13 161.558 C 426.13 161.558 427.373 170.549 424.359 174.099 C 421.289 177.716 406.315 177.694 406.315 177.694 C 406.315 177.694 407.224 164.092 403.45 159.073 C 399.127 153.325 391.048 149.505 383.873 150.001 C 376.98 150.478 370.105 155.361 366.445 161.221 C 363.218 166.387 364.297 179.366 364.297 179.366 L 269.04 179.127 C 269.04 179.127 269.782 165.798 266.595 160.729 C 262.784 154.668 256.214 150.098 248.817 149.565 C 241.288 149.023 233.786 153.936 229.466 160.126 C 226.176 164.841 227.022 176.978 227.022 176.978 C 227.022 176.978 218.707 178.44 216.012 175.925 C 212.144 172.315 213.751 160.267 213.751 160.267 C 214.395 159.557 216.448 159.522 216.448 159.522 C 216.448 159.522 216.754 152.204 220.182 149.299 C 228.372 142.359 271.666 138.303 271.666 138.303 C 271.666 138.303 296.905 122.854 311.058 119.442 Z M 287.535 136.8 C 287.535 136.8 293.759 135.815 295.483 137.924 C 296.696 139.408 295.174 143.147 295.174 143.147 L 331.758 142.122 C 331.639 135.554 334.693 122.784 334.693 122.546 C 334.693 122.308 319.464 121.704 312.252 123.697 C 303.423 126.137 287.535 136.8 287.535 136.8 Z M 340.9 122.307 C 337.774 128.025 338.035 142.122 338.035 142.122 L 371.382 140.367 C 370.99 135.11 368.777 121.92 368.777 121.92 Z M 374.457 122.426 C 374.732 128.452 377.371 139.776 377.371 139.776 L 412.849 138.89 C 412.849 138.89 410.888 128.582 406.542 125.862 C 398.608 120.896 374.457 122.426 374.457 122.426 Z"
                            />
                        </g>
                    </svg>
                    <span class="caption" style="color: <?= $customVehicleTypeIconColor; ?>;">Stationcar</span>
                </a>
            </div>
            <div class="car-icon-container" data-custom-vehicle-type="4" <?= $customVehicleTypeSuv ?>>
                <a href="/" id="cvt-suv">
                    <svg style="fill: <?= $customVehicleTypeIconColor; ?>;"
                            viewBox="0 0 250 105"
                            class="car"
                            xmlns="http://www.w3.org/2000/svg"
                    >
                        <g
                                transform="matrix(1.133006, 0, 0, 1.268357, -237.856247, -139.941116)"
                        >
                            <title>SUV</title>
                            <ellipse cx="320.879" cy="190.994" rx="104.865" ry="2.276" />
                            <path
                                    d="M 412.776 175.131 C 412.776 183.94 404.781 191.082 394.919 191.082 C 385.057 191.082 377.062 183.94 377.062 175.131 C 377.062 166.322 385.057 159.18 394.919 159.18 C 404.781 159.18 412.776 166.322 412.776 175.131 Z M 269.522 175.131 C 269.522 183.94 261.527 191.082 251.665 191.082 C 241.803 191.082 233.808 183.94 233.808 175.131 C 233.808 166.322 241.803 159.18 251.665 159.18 C 261.527 159.18 269.522 166.322 269.522 175.131 Z M 309.96 119.152 C 327.373 114.954 399.758 115.936 409.028 121.562 C 418.314 127.198 415.584 136.065 420.258 140.624 C 423.752 144.032 421.979 158.634 421.979 158.634 C 421.979 158.634 425.111 159.143 425.805 159.818 C 425.805 159.818 428.571 172.927 425.333 176.274 C 423.237 178.441 418.003 176.969 418.003 176.969 C 418.003 176.969 416.965 168.297 413.191 163.278 C 408.868 157.53 401.135 154.856 394.262 155.077 C 387.436 155.296 379.187 157.961 375.527 163.821 C 372.3 168.987 371.753 177.478 371.753 177.478 L 275.371 177.387 C 275.371 177.387 273.637 166.598 270.166 162.759 C 265.929 158.072 260.435 155.174 253.038 154.641 C 245.509 154.099 237.033 157.561 232.713 163.751 C 229.423 168.466 229.295 177.558 229.295 177.558 C 229.295 177.558 218.707 178.44 216.012 175.925 C 212.144 172.315 213.751 160.267 213.751 160.267 C 214.395 159.557 216.448 159.522 216.448 159.522 C 216.448 159.522 215.618 150.174 219.046 147.269 C 227.236 140.329 271.666 138.303 271.666 138.303 C 271.666 138.303 295.807 122.564 309.96 119.152 Z M 282.644 137.146 C 282.644 137.146 291.065 135.816 292.789 137.925 C 293.932 139.324 293.187 142.726 293.097 143.111 C 297.107 142.93 330.168 142.122 331.758 142.122 C 331.639 135.554 334.254 122.392 334.254 122.154 C 334.254 121.916 317.172 121.312 309.96 123.305 C 301.131 125.745 282.644 137.146 282.644 137.146 Z M 340.9 122.307 C 337.774 128.025 338.035 142.122 338.035 142.122 L 371.382 140.367 C 370.99 135.11 368.777 121.92 368.777 121.92 Z M 374.457 122.426 C 374.732 128.452 377.371 139.776 377.371 139.776 L 412.849 138.89 C 412.849 138.89 410.888 128.582 406.542 125.862 C 398.608 120.896 374.457 122.426 374.457 122.426 Z"
                            />
                        </g>
                    </svg>
                    <span class="caption" style="color: <?= $customVehicleTypeIconColor; ?>;">SUV</span>
                </a>
            </div>
            <div class="car-icon-container" data-custom-vehicle-type="5" <?= $customVehicleTypeElectricAndHybrid ?>>
                <a href="" id="cvt-electricandhybrid" >
                    <svg style="fill: <?= $customVehicleTypeIconColor; ?>;"
                            viewBox="0 0 236 105"
                            class="car"
                            xmlns="http://www.w3.org/2000/svg"
                    >
                        <g transform="matrix(1, 0, 0, 1, -213.815369, -92.225006)">
                            <title>El og hybrid</title>
                            <ellipse cx="320.879" cy="194.539" rx="104.865" ry="2.276" />
                            <path
                                    d="M 267.787 178.174 C 267.787 186.942 260.679 194.05 251.911 194.05 C 243.143 194.05 236.035 186.942 236.035 178.174 C 236.035 169.406 243.143 162.298 251.911 162.298 C 260.679 162.298 267.787 169.406 267.787 178.174 Z M 405 178.595 C 405 187.363 397.892 194.471 389.124 194.471 C 380.356 194.471 373.248 187.363 373.248 178.595 C 373.248 169.827 380.356 162.719 389.124 162.719 C 397.892 162.719 405 169.827 405 178.595 Z M 311.058 119.442 C 315.974 118.257 324.47 117.567 334.335 117.242 C 336.8 112.308 347.665 94.225 374.456 94.225 C 388.558 94.225 397.585 110.905 411.562 112.78 C 418.663 113.733 422.543 113.448 424.598 112.998 L 423.016 108.358 C 422.475 106.77 423.323 105.044 424.911 104.502 L 433.641 101.525 L 435.226 105.946 L 443.351 103.175 C 443.759 103.036 444.203 103.255 444.342 103.663 L 444.841 105.125 C 444.98 105.533 444.762 105.977 444.354 106.116 L 436.228 108.887 L 437.667 113.106 L 445.793 110.336 C 446.201 110.197 446.645 110.415 446.784 110.823 L 447.283 112.285 C 447.422 112.693 447.203 113.137 446.795 113.276 L 438.67 116.047 L 440.068 120.37 L 431.316 123.283 C 429.728 123.825 428.001 122.976 427.46 121.388 L 425.761 116.405 C 423.269 116.951 418.616 117.493 411.351 116.575 C 397.72 114.852 387.773 97.989 374.034 98.022 C 350.481 98.078 342.062 112.273 339.88 117.099 C 364.155 116.637 393.798 118.065 399.818 119.657 C 402.339 120.324 397.074 122.341 399.234 123.885 C 406.535 129.105 416.497 136.847 421.171 141.406 C 424.665 144.814 423.602 160.519 423.602 160.519 C 423.602 160.519 426.317 160.589 427.011 161.264 C 427.011 161.264 428.841 175.248 425.827 178.798 C 422.757 182.415 410.279 180.337 410.279 180.337 C 410.279 180.337 409.007 169.272 404.918 164.506 C 400.742 159.639 394.719 157.581 387.544 158.077 C 380.651 158.554 376.419 160.353 371.878 166.213 C 368.147 171.028 368.115 180.541 368.115 180.541 L 274.179 180.302 C 274.179 180.302 273.125 171.058 269.678 166.162 C 265.72 160.541 261.206 157.733 253.809 157.2 C 246.28 156.658 241.274 159.222 235.486 165.412 C 231.559 169.611 231.28 179.915 231.28 179.915 C 231.28 179.915 222.671 181.23 219.976 178.715 C 216.108 175.105 217.715 161.588 217.715 161.588 C 218.359 160.878 221.587 159.522 221.587 159.522 C 221.587 159.522 220.278 152.204 223.706 149.299 C 231.896 142.359 273.428 139.918 273.428 139.918 C 273.428 139.918 296.905 122.854 311.058 119.442 Z M 287.535 136.8 C 287.535 136.8 293.759 135.815 295.483 137.924 C 296.696 139.408 295.174 143.147 295.174 143.147 L 331.758 142.122 C 331.639 135.554 334.693 122.784 334.693 122.546 C 334.693 122.308 319.464 121.704 312.252 123.697 C 303.423 126.137 287.535 136.8 287.535 136.8 Z M 340.9 122.307 C 337.774 128.025 338.035 142.122 338.035 142.122 L 368.336 140.451 C 368.972 131.817 366.153 125.594 366.153 125.594 C 357.486 120.945 340.9 122.307 340.9 122.307 Z M 370.766 126.794 C 372.605 132.119 372.772 140.451 372.772 140.451 L 380.892 140.366 C 380.892 140.366 381.049 136.575 380.193 134.979 C 378.226 131.312 370.766 126.794 370.766 126.794 Z M 412.583 138.41 L 395.845 125.782 L 390.412 125.636 C 390.412 125.929 400.645 139.544 412.583 138.41 Z"
                            />
                        </g>
                    </svg>
                    <span class="caption" style="color: <?= $customVehicleTypeIconColor; ?>;">El og hybrid</span>
                </a>
            </div>
        </div>
        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>
</div>