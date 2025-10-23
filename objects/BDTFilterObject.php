<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class BDTFilterObject {
    // Existing properties
    public $CompanyIds;
    public $Propellants;
    public $Makes;
    public $Models;
    public $MakesAndModels;
    public $BodyTypes;
    public $ProductTypes;
    public $PriceMin;
    public $PriceMax;
    public $ConsumptionMin;
    public $ConsumptionMax;
    // jlk
    public $ElectricRangeMin;
    public $ElectricRangeMax;
    public $VehicleStates;
    public $FullTextSearch;
    public $PriceTypes;
    public $CustomVehicleTypes;
    public $Start;
    public $Limit;
    public $OrderBy;
    public $HideSoldVehicles; // Bool
    public $HideLeasingVehicles; // Bool
    public $HideFlexLeasingVehicles; // Bool
    public $HideWarehousesaleVehicles; // Bool
    public $HideCarLiteDealerLabelVehicles; // Bool
    public $HideExportVehicles; // Bool
    public $HideRentalVehicles; // Bool
    public $HideCommissionVehicles; // Bool
    public $HideUpcomingVehicles; // Bool
    public $HideWholesaleVehicles; // Bool
    public $HideTrailerVehicles; // Bool
    public $HideClassicVehicles; // Bool
    public $HideTractorVehicles; // Bool
    public $HideByTypeCar; // Bool
    public $HideByTypeVan; // Bool
    public $HideByTypeMotorcycle; // Bool
    public $HideByTypeTruck; // Bool
    public $HideByTypeBus; // Bool
    public $HideBrandNewVehicles; // Bool
    public $HideADVehicles; // Bool
    public $HideBIVehicles; // Bool
    public $Ascending; // Bool
    public $BrandNew; // Bool
    public $TotalResults; // Read only

    // Added properties to avoid deprecation warnings
    public $HideOnlyWholesaleVehicles; // Bool
    public $ShowOnlyWholesaleVehicles; // Bool
    public $HideInternalVehiclesBilInfo; // Bool
    public $GearTypes; // Array - Filter by transmission type (values: "Automatic", "Manual")

    /**
     * Constructor optionally initializes from an array
     *
     * @param array|null $data
     */
    public function __construct(array $data = null) 
    {
        if ($data === null) {
            return;
        }

        foreach ($data as $key => $val) {
            if (property_exists(__CLASS__, $key)) {
                $this->$key = $val;
            }
        }
    }
}
