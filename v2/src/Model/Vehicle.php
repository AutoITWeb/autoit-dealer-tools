<?php

namespace Biltorvet\Model;

class Vehicle
{
    /**
     * @var Equipment[]|null
     */
    private $equipment;
    /**
     * @var Company|null
     */
    private $company;

    /**
     * @var array|null
     */
    private $videos;

    /**
     * @var string|null
     */
    private $documentId;

    /**
     * @var integer|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $makeName;

    /**
     * @var string|null
     */
    private $model;
    /**
     * @var string|null
     */
    private $modelId;
    /**
     * @var string|null
     */
    private $variant;
    /**
     * @var boolean|null
     */
    private $automatic;
    /**
     * @var bool|null
     */
    private $brandNew;
    /**
     * @var string|null
     */
    private $propellant;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var string|null
     */
    private $energyClass;
    /**
     * @var string|null
     */
    private $type;
    /**
     * @var integer|null
     */
    private $typeId;
    /**
     * @var string[]|null
     */
    private $images;
    /**
     * @var string|null
     */
    private $vehicleCardImage;
    /**
     * @var Property[]|null
     */
    private $properties;
    /**
     * @var Label[]|null
     */
    private $labels;
    /**
     * @var string|null
     */
    private $updated;
    /**
     * @var string|null
     */
    private $created;
    /**
     * @var string|null
     */
    private $url;
    /**
     * @var string|null
     */
    private $uri;
    /**
     * @var integer|null
     */
    private $companyId;
    /**
     * @var string|null
     */
    private $vin;
    /**
     * @var array|null
     */
    private $cashPrice;
    /**
     * @var array|null
     */
    private $leasingPrice;
    /**
     * @var array|null
     */
    private $financingPrice;
    /**
     * @var bool|null
     */
    private $hasVideo;

    /**
     * @return string[]|null
     */
    public function getCashPrice(): ?array
    {
        return $this->cashPrice;
    }

    /**
     * @param  string[]|null $cashPrice
     * @return Vehicle
     */
    public function setCashPrice(?array $cashPrice): Vehicle
    {
        $this->cashPrice = $cashPrice;
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLeasingPrice(): ?array
    {
        return $this->leasingPrice;
    }

    /**
     * @param  string[]|null $leasingPrice
     * @return Vehicle
     */
    public function setleasingPrice(?array $leasingPrice): Vehicle
    {
        $this->leasingPrice = $leasingPrice;
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getFinancingPrice(): ?array
    {
        return $this->financingPrice;
    }

    /**
     * @param  string[]|null $financingPrice
     * @return Vehicle
     */
    public function setFinancingPrice(?array $financingPrice): Vehicle
    {
        $this->financingPrice = $financingPrice;
        return $this;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @param  Company|null $company
     * @return Vehicle
     */
    public function setCompany(?Company $company): Vehicle
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return Equipment[]|null
     */
    public function getEquipment(): ?array
    {
        return $this->equipment;
    }

    /**
     * @param  Equipment[]|null $equipment
     * @return Vehicle
     */
    public function setEquipment(?array $equipment): Vehicle
    {
        $this->equipment = $equipment;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getVideos(): ?array
    {
        return $this->videos;
    }

    /**
     * @param  array|null $videos
     * @return Vehicle
     */
    public function setVideos(?array $videos): Vehicle
    {
        $this->videos = $videos;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentId(): ?string
    {
        return $this->documentId;
    }

    /**
     * @param  string|null $documentId
     * @return Vehicle
     */
    public function setDocumentId(?string $documentId): Vehicle
    {
        $this->documentId = $documentId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVin(): ?string
    {
        return $this->vin;
    }

    /**
     * @param  string|null $vin
     * @return Vehicle
     */
    public function setVin(?string $vin): Vehicle
    {
        $this->vin = $vin;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param  int|null $id
     * @return Vehicle
     */
    public function setId(?int $id): Vehicle
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMakeName(): ?string
    {
        return $this->makeName;
    }

    /**
     * @param  string|null $makeName
     * @return Vehicle
     */
    public function setMakeName(?string $makeName): Vehicle
    {
        $this->makeName = $makeName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @param  string|null $model
     * @return Vehicle
     */
    public function setModel(?string $model): Vehicle
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getModelId(): ?string
    {
        return $this->modelId;
    }

    /**
     * @param  string|null $modelId
     * @return Vehicle
     */
    public function setModelId(?string $modelId): Vehicle
    {
        $this->modelId = $modelId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVariant(): ?string
    {
        return $this->variant;
    }

    /**
     * @param  string|null $variant
     * @return Vehicle
     */
    public function setVariant(?string $variant): Vehicle
    {
        $this->variant = $variant;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAutomatic(): ?bool
    {
        return $this->automatic;
    }

    /**
     * @param  bool|null $automatic
     * @return Vehicle
     */
    public function setAutomatic(?bool $automatic): Vehicle
    {
        $this->automatic = $automatic;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasVideo(): ?bool
    {
        return $this->hasVideo;
    }

    /**
     * @param  bool|null $hasVideo
     * @return Vehicle
     */
    public function setHasVideo(?bool $hasVideo): Vehicle
    {
        $this->hasVideo = $hasVideo;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getBrandNew(): ?bool
    {
        return $this->brandNew;
    }

    /**
     * @param  bool|null $automatic
     * @return Vehicle
     */
    public function setBrandNew(?bool $brandNew): Vehicle
    {
        $this->brandNew = $brandNew;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPropellant(): ?string
    {
        return $this->propellant;
    }

    /**
     * @param  string|null $propellant
     * @return Vehicle
     */
    public function setPropellant(?string $propellant): Vehicle
    {
        $this->propellant = $propellant;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param  string|null $description
     * @return Vehicle
     */
    public function setDescription(?string $description): Vehicle
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEnergyClass(): ?string
    {
        return $this->energyClass;
    }

    /**
     * @param  string|null $energyClass
     * @return Vehicle
     */
    public function setEnergyClass(?string $energyClass): Vehicle
    {
        $this->energyClass = $energyClass;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param  string|null $type
     * @return Vehicle
     */
    public function setType(?string $type): Vehicle
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    /**
     * @param  int|null $typeId
     * @return Vehicle
     */
    public function setTypeId(?int $typeId): Vehicle
    {
        $this->typeId = $typeId;
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * @param  string[]|null $images
     * @return Vehicle
     */
    public function setImages(?array $images): Vehicle
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return Property[]|null
     */
    public function getProperties(): ?array
    {
        return $this->properties;
    }

    /**
     * @param  Property[]|null $properties
     * @return Vehicle
     */
    public function setProperties(?array $properties): Vehicle
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @return Label[]|null
     */
    public function getLabels(): ?array
    {
        return $this->labels;
    }

    /**
     * @param  Label[]|null $labels
     * @return Vehicle
     */
    public function setLabels(?array $labels): Vehicle
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUpdated(): ?string
    {
        return $this->updated;
    }

    /**
     * @param  string|null $updated
     * @return Vehicle
     */
    public function setUpdated(?string $updated): Vehicle
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreated(): ?string
    {
        return $this->created;
    }

    /**
     * @param  string|null $created
     * @return Vehicle
     */
    public function setCreated(?string $created): Vehicle
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param  string|null $url
     * @return Vehicle
     */
    public function setUrl(?string $url): Vehicle
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVehicleCardImage(): ?string
    {
        return $this->vehicleCardImage;
    }

    /**
     * @param  string|null $vehicleCardImage
     * @return Vehicle
     */
    public function setVehicleCardImage(?string $vehicleCardImage): Vehicle
    {
        $this->vehicleCardImage = $vehicleCardImage;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @param  string|null $uri
     * @return Vehicle
     */
    public function setUri(?string $uri): Vehicle
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    /**
     * @param  int|null $companyId
     * @return Vehicle
     */
    public function setCompanyId(?int $companyId): Vehicle
    {
        $this->companyId = $companyId;
        return $this;
    }

    // Sort labels for better use on vehiclecards
    public static function sortVehicleLabels(?array $labels, ?string $showAllLabes) : array
    {
        $vehicleLabels = array();

        if ($labels) {
            foreach($labels as $label) {

                // DealerSpecificLabel
                if($label->getKey() == 427)
                {
                    $vehicleLabels[1] = $label->getValue();
                }
				
                if($label->getKey() == 443)
                {
                    $vehicleLabels[2] = 'Online køb';
                }				

                if($label->getKey() == 11)
                {
                    $vehicleLabels[3] = 'Nyhed';
                }

                if($label->getKey() == 5)
                {
                    $vehicleLabels[4] = 'Solgt';
                }

                if($label->getKey() == 99999)
                {
                    $vehicleLabels[5] = 'Fabriksny';
                }

                if($label->getKey() == 12)
                {
                    $vehicleLabels[6] = 'Leasing';
                }

                if($label->getKey() == 9)
                {
                    $vehicleLabels[7] = 'Kun engros';
                }

                if($label->getKey() == 382)
                {
                    $vehicleLabels[8] = 'Eksport';
                }

                if($label->getKey() == 26)
                {
                    $vehicleLabels[9] = 'Lagersalg';
                }
                if($label->getKey() == 1)
                {
                    $vehicleLabels[10] = 'Demonstration';
                }

                else {
                    /*
                     * Is the show all labels setting on?
                     */

                    if($showAllLabes != null) {

                        if(!in_array($label->getValue(), $vehicleLabels)) {
                            array_push($vehicleLabels, $label->getValue());

                        }
                    }
                }

            }
        }

        // We need the array in ascending order
        ksort($vehicleLabels);

        return $vehicleLabels;
    }

    // Vehicle properties shown on vehicle card
    public static function getVehicleParam(string $propertiesValue, Array $vehicleProperties, Vehicle $vehicle) : string
    {
        $HTML = '<span class="vehicleParamValue' . ($vehicle->getPropellant() === "El" ? ' electricIcon' : '') .'">';

        switch ($propertiesValue) {
            case '0':
                if($vehicleProperties['ModelYear']->getValue() == $vehicleProperties['FirstRegYear']->getValue()) {
                    $HTML .= ' ' . $vehicleProperties['ModelYear']->getValue() ? $vehicleProperties['ModelYear']->getValue() : '-' . '';
                }

                if($vehicleProperties['ModelYear']->getValue() != $vehicleProperties['FirstRegYear']->getValue()) {
                    $HTML .= '' . $vehicleProperties['ModelYear']->getValue() ? $vehicleProperties['ModelYear']->getValue() : '-' . '';
                    $HTML .= '' . $vehicleProperties['FirstRegYear']->getValue() ? " / " . $vehicleProperties['FirstRegYear']->getValue() : '' . '';
                }

                $HTML .= '</span><span class="vehicleParamLabel">Årgang</span>';
                break;
            case '1':
                $HTML .= '' . $vehicleProperties['FirstRegYear']->getValue() != "" ? $vehicleProperties['FirstRegYear']->getValue() : '-' . '';
                $HTML .= '</span><span class="vehicleParamLabel">Årgang</span>';
                break;
            case '2':
                $HTML .= '' . $vehicleProperties['ModelYear']->getValue() ?? '-' . '';
                $HTML .= '</span><span class="vehicleParamLabel">Modelår</span>';
                break;
            case '3':
                $HTML .= '' . $vehicle->getCompany()->getCity() ?? '-' . '';
                $HTML .= '</span><span class="vehicleParamLabel">Placering</span>';
                break;
            case '4':
                $HTML .= '' . $vehicleProperties['Mileage']->getValueFormatted() != "" ? $vehicleProperties['Mileage']->getValueFormatted() : '-' . '';
                $HTML .= '</span><span class="vehicleParamLabel">KM</span>';
                break;
            case '5':
                // Check if WLTP data is available, otherwise use NEDC
                $kmLabel = 'Km/L';
                if (isset($vehicleProperties['Kmx1lWltp']) && $vehicleProperties['Kmx1lWltp']->getValueFormatted() != "" && $vehicleProperties['Kmx1lWltp']->getValueFormatted() != "Ikke angivet") {
                    $HTML .= '' . $vehicleProperties['Kmx1lWltp']->getValueFormatted() . '';
                    $kmLabel = 'Km/L (WLTP)';
                } else if (isset($vehicleProperties['Kmx1l']) && $vehicleProperties['Kmx1l']->getValueFormatted() != "" && $vehicleProperties['Kmx1l']->getValueFormatted() != "Ikke angivet") {
                    $HTML .= '' . $vehicleProperties['Kmx1l']->getValueFormatted() . '';
                    $kmLabel = 'Km/L (NEDC)';
                } else {
                    $HTML .= '-';
                }
                $HTML .= '</span><span class="vehicleParamLabel">' . $kmLabel . '</span>';
                break;
            case '6':
                $HTML .= '' . $vehicle->getPropellant() ?? '-' . '';
                $HTML .= '</span><span class="vehicleParamLabel">Drivmiddel</span>';
                break;
            case '7':
                $HTML .= '' . $vehicleProperties['ElectricReach']->getValueFormatted() != "" ? $vehicleProperties['ElectricReach']->getValueFormatted() : '-' . '';
                $HTML .= '</span><span class="vehicleParamLabel">Rækkevidde</span>';
                break;				
        }

        return $HTML;
    }
}
