<?php


namespace Biltorvet\Model;

class Price
{
    /**
     * Derived from LeasingMonthlyPaymentVAT.
     *
     * @var float|null
     */
    private $leasingVatValue;

    /**
     * Derived from LeasingMonthlyPayment.
     *
     * @var float|null
     */
    private $leasingPriceValue;

    /**
     * Derived from Price.
     *
     * @var float|null
     */
    private $priceValue;

    /**
     * Derived from FinancingMonthlyPrice.
     *
     * @var float|null
     */
    private $financingValue;

    /**
     * Derived from LeasingBusiness.
     *
     * @var boolean|null
     */
    private $isBusinessLeasing;

    /**
     * Derived from LeasingPrivate.
     *
     * @var boolean|null
     */
    private $isPrivateLeasing;

    /**
     * Derived from XVat.
     *
     * @var boolean|null
     */
    private $isBusinessPrice;

    /**
     *
     * @var boolean|null
     */
    private $hasCashPrice;

    /**
     * @var string|null
     */
    private $cashPriceFormatted;

    /**
     * @var string|null
     */
    private $cashPriceLabelVehicleCards;

    /**
     * @var string|null
     */
    private $cashPriceLabelDetailsPage;

    /**
     *
     * @var boolean|null
     */
    private $hasLeasingPrice;

    /**
     * @var string|null
     */
    private $leasingPriceFormatted;

    /**
     *
     * @var string|null
     */
    private $leasingPriceLabelVehicleCards;

    /**
     *
     * @var string|null
     */
    private $leasingPriceLabelDetailsPage;

    /**
     *
     * @var boolean|null
     */
    private $hasFinancingPrice;

    /**
     * @var string|null
     */
    private $financingPriceFormatted;

    /**
     *
     * @var string|null
     */
    private $financingPriceLabelVehicleCards;

    /**
     *
     * @var string|null
     */
    private $financingPriceLabelDetailsPage;

    /**
     * @return string|null
     */
    public function getfinancingPriceFormatted(): ?string
    {
        return $this->financingPriceFormatted;
    }

    /**
     * @param string|null $financingPriceFormatted
     * @return Price
     */
    public function setFinancingPriceFormatted(?string $financingPriceFormatted): Price
    {
        $this->financingPriceFormatted = $financingPriceFormatted;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFinancingPriceLabelVehicleCards(): ?string
    {
        return $this->financingPriceLabelVehicleCards;
    }

    /**
     * @param string|null $financingPriceLabelVehicleCards
     * @return Price
     */
    public function setfinancingPriceLabelVehicleCards(?string $financingPriceLabelVehicleCards): Price
    {
        $this->financingPriceLabelVehicleCards = $financingPriceLabelVehicleCards;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFinancingPriceLabelDetailsPage(): ?string
    {
        return $this->financingPriceLabelVehicleCards;
    }

    /**
     * @param string|null $financingPriceLabelDetailsPage
     * @return Price
     */
    public function setfinancingpriceLabelDetailsPage(?string $financingPriceLabelDetailsPage): Price
    {
        $this->financingPriceLabelDetailsPage = $financingPriceLabelDetailsPage;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLeasingPriceFormatted(): ?string
    {
        return $this->leasingPriceFormatted;
    }

    /**
     * @param string|null $leasingPriceFormatted
     * @return Price
     */
    public function setLeasingPriceFormatted(?string $leasingPriceFormatted): Price
    {
        $this->leasingPriceFormatted = $leasingPriceFormatted;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLeasingPriceLabelVehicleCards(): ?string
    {
        return $this->leasingPriceLabelVehicleCards;
    }

    /**
     * @param string|null $leasingPriceLabelVehicleCards
     * @return Price
     */
    public function setLeasingPriceLabelVehicleCards(?string $leasingPriceLabelVehicleCards): Price
    {
        $this->leasingPriceLabelVehicleCards = $leasingPriceLabelVehicleCards;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLeasingPriceLabelDetailsPage(): ?string
    {
        return $this->leasingPriceLabelDetailsPage;
    }

    /**
     * @param string|null $leasingPriceLabelDetailsPage
     * @return Price
     */
    public function setLeasingPriceLabelDetailsPage(?string $leasingPriceLabelDetailsPage): Price
    {
        $this->leasingPriceLabelDetailsPage = $leasingPriceLabelDetailsPage;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getCashPriceFormatted(): ?string
    {
        return $this->cashPriceFormatted;
    }

    /**
     * @param string|null $cashPriceFormatted
     * @return Price
     */
    public function setCashPriceFormatted(?string $priceFormatted): Price
    {
        $this->cashPriceFormatted = $priceFormatted;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCashPriceLabelVehicleCards(): ?string
    {
        return $this->cashPriceLabelVehicleCards;
    }

    /**
     * @param string|null $cashPriceLabelVehicleCards
     * @return Price
     */
    public function setCashPriceLabelVehicleCards(?string $cashPriceLabelVehicleCards): Price
    {
        $this->cashPriceLabelVehicleCards = $cashPriceLabelVehicleCards;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCashPriceLabelDetailsPage(): ?string
    {
        return $this->cashPriceLabelDetailsPage;
    }

    /**
     * @param string|null $cashPriceLabelDetailsPage
     * @return Price
     */
    public function setCashPriceLabelDetailsPage(?string $cashPriceLabelDetailsPage): Price
    {
        $this->cashPriceLabelDetailsPage = $cashPriceLabelDetailsPage;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLeasingVatValue(): ?float
    {
        return $this->leasingVatValue;
    }

    /**
     * @param float|null $leasingVatValue
     * @return Price
     */
    public function setLeasingVatValue(?float $leasingVatValue): Price
    {
        $this->leasingVatValue = $leasingVatValue;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLeasingPriceValue(): ?float
    {
        return $this->leasingPriceValue;
    }

    /**
     * @param float|null $leasingPriceValue
     * @return Price
     */
    public function setLeasingPriceValue(?float $leasingPriceValue): Price
    {
        $this->leasingPriceValue = $leasingPriceValue;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPriceValue(): ?float
    {
        return $this->priceValue;
    }

    /**
     * @param float|null $priceValue
     * @return Price
     */
    public function setPriceValue(?float $priceValue): Price
    {
        $this->priceValue = $priceValue;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getFinancingValue(): ?float
    {
        return $this->financingValue;
    }

    /**
     * @param float|null $financingValue
     * @return Price
     */
    public function setFinancingValue(?float $financingValue): Price
    {
        $this->financingValue = $financingValue;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsBusinessLeasing(): ?bool
    {
        return $this->isBusinessLeasing;
    }

    /**
     * @param bool|null $isBusinessLeasing
     * @return Price
     */
    public function setIsBusinessLeasing(?bool $isBusinessLeasing): Price
    {
        $this->isBusinessLeasing = $isBusinessLeasing;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPrivateLeasing(): ?bool
    {
        return $this->isPrivateLeasing;
    }

    /**
     * @param bool|null $isPrivateLeasing
     * @return Price
     */
    public function setIsPrivateLeasing(?bool $isPrivateLeasing): Price
    {
        $this->isPrivateLeasing = $isPrivateLeasing;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsBusinessPrice(): ?bool
    {
        return $this->isBusinessPrice;
    }

    /**
     * @param bool|null $isBusinessPricesetIsBusinessPrice
     * @return Price
     */
    public function setIsBusinessPrice(?bool $isBusinessPrice): Price
    {
        $this->isBusinessPrice = $isBusinessPrice;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasCashPrice(): ?bool
    {
        return $this->hasCashPrice;
    }

    /**
     * @param bool|null $hasCashPrice
     * @return Price
     */
    public function setHasCashPrice(?bool $hasCashPrice): Price
    {
        $this->hasCashPrice = $hasCashPrice;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasLeasingPrice(): ?bool
    {
        return $this->hasLeasingPrice;
    }

    /**
     * @param bool|null $hasLeasingPrice
     * @return Price
     */
    public function setHasLeasingPrice(?bool $hasLeasingPrice): Price
    {
        $this->hasLeasingPrice = $hasLeasingPrice;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasfinancingPrice(): ?bool
    {
        return $this->hasFinancingPrice;
    }

    /**
     * @param bool|null $hasFinancingPrice
     * @return Price
     */
    public function setHasfinancingPrice(?bool $hasFinancingPrice): Price
    {
        $this->hasFinancingPrice = $hasFinancingPrice;
        return $this;
    }


}
