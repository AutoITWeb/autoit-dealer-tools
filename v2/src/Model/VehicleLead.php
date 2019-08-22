<?php


namespace Biltorvet\Model;

use DateTime;

class VehicleLead
{
    /**
     * @var string|null
     */
    private $type;
    /**
     * @var string|null
     */
    private $model;
    /**
     * @var string|null
     */
    private $vin;
    /**
     * @var string|null
     */
    private $numberPlate;
    /**
     * @var string|null
     */
    private $firstRegitrationDate;
    /**
     * @var string|null
     */
    private $RequestedTestdriveDateTime;
    /**
     * @var string|null
     */
    private $message;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var integer|null
     */
    private $postalCode;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var string|null
     */
    private $cellPhoneNumber;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $activityType;

    /**
     * @var integer|null
     */
    private $companyId;

    /**
     * @var string|null
     */
    private $websiteUrl;

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param  string|null $type
     * @return VehicleLead
     */
    public function setType(?string $type): VehicleLead
    {
        $this->type = $type;
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
     * @return VehicleLead
     */
    public function setModel(?string $model): VehicleLead
    {
        $this->model = $model;
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
     * @return VehicleLead
     */
    public function setVin(?string $vin): VehicleLead
    {
        $this->vin = $vin;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNumberPlate(): ?string
    {
        return $this->numberPlate;
    }

    /**
     * @param  string|null $numberPlate
     * @return VehicleLead
     */
    public function setNumberPlate(?string $numberPlate): VehicleLead
    {
        $this->numberPlate = $numberPlate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstRegitrationDate(): ?string
    {
        return $this->firstRegitrationDate;
    }

    /**
     * @param  string|null $firstRegitrationDate
     * @return VehicleLead
     */
    public function setFirstRegitrationDate(?string $firstRegitrationDate): VehicleLead
    {
        $this->firstRegitrationDate = $firstRegitrationDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRequestedTestdriveDateTime(): ?string
    {
        return $this->RequestedTestdriveDateTime;
    }

    /**
     * @param  string|null $RequestedTestdriveDateTime
     * @return VehicleLead
     */
    public function setRequestedTestdriveDateTime(?string $RequestedTestdriveDateTime): VehicleLead
    {
        $this->RequestedTestdriveDateTime = $RequestedTestdriveDateTime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param  string|null $message
     * @return VehicleLead
     */
    public function setMessage(?string $message): VehicleLead
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param  string|null $name
     * @return VehicleLead
     */
    public function setName(?string $name): VehicleLead
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    /**
     * @param  int|null $postalCode
     * @return VehicleLead
     */
    public function setPostalCode(?int $postalCode): VehicleLead
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param  string|null $city
     * @return VehicleLead
     */
    public function setCity(?string $city): VehicleLead
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCellPhoneNumber(): ?string
    {
        return $this->cellPhoneNumber;
    }

    /**
     * @param  string|null $cellPhoneNumber
     * @return VehicleLead
     */
    public function setCellPhoneNumber(?string $cellPhoneNumber): VehicleLead
    {
        $this->cellPhoneNumber = $cellPhoneNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param  string|null $email
     * @return VehicleLead
     */
    public function setEmail(?string $email): VehicleLead
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getActivityType(): ?string
    {
        return $this->activityType;
    }

    /**
     * @param  string|null $activityType
     * @return VehicleLead
     */
    public function setActivityType(?string $activityType): VehicleLead
    {
        $this->activityType = $activityType;
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
     * @return VehicleLead
     */
    public function setCompanyId(?int $companyId): VehicleLead
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    /**
     * @param  string|null $websiteUrl
     * @return VehicleLead
     */
    public function setWebsiteUrl(?string $websiteUrl): VehicleLead
    {
        $this->websiteUrl = $websiteUrl;
        return $this;
    }
}
