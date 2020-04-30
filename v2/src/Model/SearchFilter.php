<?php

namespace Biltorvet\Model;

class SearchFilter
{
    /**
     * @var string[]
     */
    public $CompanyIds;

    /**
     * @var string[]
     */
    public $Propellants;

    /**
     * @var string[]
     */
    public $Makes;

    /**
     * @var string[]
     */
    public $Models;

    /**
     * @var string[]
     */
    public $BodyTypes;

    /**
     * @var string[]
     */
    public $ProductTypes;

    /**
     * @var int
     */
    public $PriceMin;

    /**
     * @var int
     */
    public $PriceMax;

    /**
     * @var int
     */
    public $ConsumptionMin;

    /**
     * @var int
     */
    public $ConsumptionMax;

    /**
     * @var int
     */
    public $Start;

    /**
     * @var int
     */
    public $Limit;

    /**
     * @var string
     */
    public $OrderBy;

    /**
     * @var boolean
     */
    public $HideSoldVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideLeasingVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideFlexLeasingVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideWarehousesaleVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideExportVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideRentalVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideCommissionVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideUpcomingVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideWholesaleVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideByTypeCar; // Bool

    /**
     * @var boolean
     */
    public $HideByTypeVan; // Bool

    /**
     * @var boolean
     */
    public $HideByTypeMotorcycle; // Bool

    /**
     * @var boolean
     */
    public $HideByTypeTruck; // Bool

    /**
     * @var boolean
     */
    public $HideByTypeBus; // Bool

    /**
     * @var boolean
     */
    public $HideADVehicles; // Bool

    /**
     * @var boolean
     */
    public $HideBIVehicles; // Bool

    /**
     * @var boolean
     */
    public $Ascending; // Bool

    /**
     * @var boolean
     */
    public $BrandNew; // Bool

    /**
     * @var int
     */
    public $TotalResults;

    /**
     * @return string[]
     */
    public function getCompanyIds(): array
    {
        return $this->CompanyIds;
    }

    /**
     * @param  string[] $CompanyIds
     * @return SearchFilter
     */
    public function setCompanyIds(array $CompanyIds): SearchFilter
    {
        $this->CompanyIds = $CompanyIds;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getPropellants(): array
    {
        return $this->Propellants;
    }

    /**
     * @param  string[] $Propellants
     * @return SearchFilter
     */
    public function setPropellants(array $Propellants): SearchFilter
    {
        $this->Propellants = $Propellants;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getMakes(): array
    {
        return $this->Makes;
    }

    /**
     * @param  string[] $Makes
     * @return SearchFilter
     */
    public function setMakes(array $Makes): SearchFilter
    {
        $this->Makes = $Makes;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getModels(): array
    {
        return $this->Models;
    }

    /**
     * @param  string[] $Models
     * @return SearchFilter
     */
    public function setModels(array $Models): SearchFilter
    {
        $this->Models = $Models;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getBodyTypes(): array
    {
        return $this->BodyTypes;
    }

    /**
     * @param  string[] $BodyTypes
     * @return SearchFilter
     */
    public function setBodyTypes(array $BodyTypes): SearchFilter
    {
        $this->BodyTypes = $BodyTypes;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getProductTypes(): array
    {
        return $this->ProductTypes;
    }

    /**
     * @param  string[] $ProductTypes
     * @return SearchFilter
     */
    public function setProductTypes(array $ProductTypes): SearchFilter
    {
        $this->ProductTypes = $ProductTypes;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriceMin(): int
    {
        return $this->PriceMin;
    }

    /**
     * @param  int $PriceMin
     * @return SearchFilter
     */
    public function setPriceMin(int $PriceMin): SearchFilter
    {
        $this->PriceMin = $PriceMin;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriceMax(): int
    {
        return $this->PriceMax;
    }

    /**
     * @param  int $PriceMax
     * @return SearchFilter
     */
    public function setPriceMax(int $PriceMax): SearchFilter
    {
        $this->PriceMax = $PriceMax;
        return $this;
    }

    /**
     * @return int
     */
    public function getConsumptionMin(): int
    {
        return $this->ConsumptionMin;
    }

    /**
     * @param  int $ConsumptionMin
     * @return SearchFilter
     */
    public function setConsumptionMin(int $ConsumptionMin): SearchFilter
    {
        $this->ConsumptionMin = $ConsumptionMin;
        return $this;
    }

    /**
     * @return int
     */
    public function getConsumptionMax(): int
    {
        return $this->ConsumptionMax;
    }

    /**
     * @param  int $ConsumptionMax
     * @return SearchFilter
     */
    public function setConsumptionMax(int $ConsumptionMax): SearchFilter
    {
        $this->ConsumptionMax = $ConsumptionMax;
        return $this;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->Start;
    }

    /**
     * @param  int $Start
     * @return SearchFilter
     */
    public function setStart(int $Start): SearchFilter
    {
        $this->Start = $Start;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->Limit;
    }

    /**
     * @param  int $Limit
     * @return SearchFilter
     */
    public function setLimit(int $Limit): SearchFilter
    {
        $this->Limit = $Limit;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->OrderBy;
    }

    /**
     * @param  string $OrderBy
     * @return SearchFilter
     */
    public function setOrderBy(string $OrderBy): SearchFilter
    {
        $this->OrderBy = $OrderBy;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideSoldVehicles(): bool
    {
        return $this->HideSoldVehicles;
    }

    /**
     * @param  bool $HideSoldVehicles
     * @return SearchFilter
     */
    public function setHideSoldVehicles(bool $HideSoldVehicles): SearchFilter
    {
        $this->HideSoldVehicles = $HideSoldVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideLeasingVehicles(): bool
    {
        return $this->HideLeasingVehicles;
    }

    /**
     * @param  bool $HideLeasingVehicles
     * @return SearchFilter
     */
    public function setHideLeasingVehicles(bool $HideLeasingVehicles): SearchFilter
    {
        $this->HideLeasingVehicles = $HideLeasingVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideFlexLeasingVehicles(): bool
    {
        return $this->HideFlexLeasingVehicles;
    }

    /**
     * @param  bool $HideFlexLeasingVehicles
     * @return SearchFilter
     */
    public function setHideFlexLeasingVehicles(bool $HideFlexLeasingVehicles): SearchFilter
    {
        $this->HideFlexLeasingVehicles = $HideFlexLeasingVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideWarehousesaleVehicles(): bool
    {
        return $this->HideWarehousesaleVehicles;
    }

    /**
     * @param  bool $HideWarehousesaleVehicles
     * @return SearchFilter
     */
    public function setHideWarehousesaleVehicles(bool $HideWarehousesaleVehicles): SearchFilter
    {
        $this->HideWarehousesaleVehicles = $HideWarehousesaleVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideExportVehicles(): bool
    {
        return $this->HideExportVehicles;
    }

    /**
     * @param  bool $HideExportVehicles
     * @return SearchFilter
     */
    public function setHideExportVehicles(bool $HideExportVehicles): SearchFilter
    {
        $this->HideExportVehicles = $HideExportVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideRentalVehicles(): bool
    {
        return $this->HideRentalVehicles;
    }

    /**
     * @param  bool $HideRentalVehicles
     * @return SearchFilter
     */
    public function setHideRentalVehicles(bool $HideRentalVehicles): SearchFilter
    {
        $this->HideRentalVehicles = $HideRentalVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideCommissionVehicles(): bool
    {
        return $this->HideCommissionVehicles;
    }

    /**
     * @param  bool $HideCommissionVehicles
     * @return SearchFilter
     */
    public function setHideCommissionVehicles(bool $HideCommissionVehicles): SearchFilter
    {
        $this->HideCommissionVehicles = $HideCommissionVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideUpcomingVehicles(): bool
    {
        return $this->HideUpcomingVehicles;
    }

    /**
     * @param  bool $HideUpcomingVehicles
     * @return SearchFilter
     */
    public function setHideUpcomingVehicles(bool $HideUpcomingVehicles): SearchFilter
    {
        $this->HideUpcomingVehicles = $HideUpcomingVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideWholesaleVehicles(): bool
    {
        return $this->HideWholesaleVehicles;
    }

    /**
     * @param  bool $HideWholesaleVehicles
     * @return SearchFilter
     */
    public function setHideWholesaleVehicles(bool $HideWholesaleVehicles): SearchFilter
    {
        $this->HideWholesaleVehicles = $HideWholesaleVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideByTypeCar(): bool
    {
        return $this->HideByTypeCar;
    }

    /**
     * @param  bool $HideByTypeCar
     * @return SearchFilter
     */
    public function setHideByTypeCar(bool $HideByTypeCar): SearchFilter
    {
        $this->HideByTypeCar = $HideByTypeCar;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideByTypeVan(): bool
    {
        return $this->HideByTypeVan;
    }

    /**
     * @param  bool $HideByTypeVan
     * @return SearchFilter
     */
    public function setHideByTypeVan(bool $HideByTypeVan): SearchFilter
    {
        $this->HideByTypeVan = $HideByTypeVan;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideByTypeMotorcycle(): bool
    {
        return $this->HideByTypeMotorcycle;
    }

    /**
     * @param  bool $HideByTypeMotorcycle
     * @return SearchFilter
     */
    public function setHideByTypeMotorcycle(bool $HideByTypeMotorcycle): SearchFilter
    {
        $this->HideByTypeMotorcycle = $HideByTypeMotorcycle;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideByTypeTruck(): bool
    {
        return $this->HideByTypeTruck;
    }

    /**
     * @param  bool $HideByTypeTruck
     * @return SearchFilter
     */
    public function setHideByTypeTruck(bool $HideByTypeTruck): SearchFilter
    {
        $this->HideByTypeTruck = $HideByTypeTruck;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideByTypeBus(): bool
    {
        return $this->HideByTypeBus;
    }

    /**
     * @param  bool $HideByTypeBus
     * @return SearchFilter
     */
    public function setHideByTypeBus(bool $HideByTypeBus): SearchFilter
    {
        $this->HideByTypeBus = $HideByTypeBus;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideADVehicles(): bool
    {
        return $this->HideADVehicles;
    }

    /**
     * @param  bool $HideADVehicles
     * @return SearchFilter
     */
    public function setHideADVehicles(bool $HideADVehicles): SearchFilter
    {
        $this->HideADVehicles = $HideADVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideBIVehicles(): bool
    {
        return $this->HideBIVehicles;
    }

    /**
     * @param  bool $HideBIVehicles
     * @return SearchFilter
     */
    public function setHideBIVehicles(bool $HideBIVehicles): SearchFilter
    {
        $this->HideBIVehicles = $HideBIVehicles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAscending(): bool
    {
        return $this->Ascending;
    }

    /**
     * @param  bool $Ascending
     * @return SearchFilter
     */
    public function setAscending(bool $Ascending): SearchFilter
    {
        $this->Ascending = $Ascending;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBrandNew(): bool
    {
        return $this->BrandNew;
    }

    /**
     * @param  bool $BrandNew
     * @return SearchFilter
     */
    public function setBrandNew(bool $BrandNew): SearchFilter
    {
        $this->BrandNew = $BrandNew;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalResults(): int
    {
        return $this->TotalResults;
    } // Read only
}
