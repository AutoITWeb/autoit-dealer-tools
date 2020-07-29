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


}
