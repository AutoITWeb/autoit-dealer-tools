<?php


namespace Biltorvet\Factory;

use Biltorvet\Model\Price;
use Biltorvet\Model\Property;
use Biltorvet\Model\Vehicle;

class PriceFactory
{
    public static function create(Vehicle $vehicle)
    {
        $price = new Price();

        /** @var Property[] $relatedPriceProperties */
        $relatedPriceProperties = self::getPriceProperties($vehicle->getProperties());

        if (isset($relatedPriceProperties['FinancingMonthlyPrice'])) {
            $price->setFinancingValue($relatedPriceProperties['FinancingMonthlyPrice']->getValue());
        }
        if (isset($relatedPriceProperties['LeasingBusiness'])) {
            $price->setIsBusinessLeasing($relatedPriceProperties['LeasingBusiness']->getValue() == 'Ja' ? true : false);
            $price->setIsPrivateLeasing(false);
        }
        if (isset($relatedPriceProperties['LeasingPrivate'])) {
            $price->setIsPrivateLeasing($relatedPriceProperties['LeasingPrivate']->getValue() == 'Ja' ? true : false);
            $price->setIsBusinessLeasing(false);
        }
        if (isset($relatedPriceProperties['VAT'])) {
            $price->setIsBusinessPrice($relatedPriceProperties['VAT']->getValue() == 'Ja' ? true : false);
        }
        if (isset($relatedPriceProperties['LeasingMonthlyPayment'])) {
            $price->setLeasingPriceValue((float)$relatedPriceProperties['LeasingMonthlyPayment']->getValue());
        }
        if (isset($relatedPriceProperties['LeasingMonthlyPaymentVAT'])) {
            $price->setLeasingVatValue($relatedPriceProperties['LeasingMonthlyPaymentVAT']->getValue());
        }
        if (isset($relatedPriceProperties['Price'])) {
            $price->setPriceValue($relatedPriceProperties['Price']->getValue());
        }

        $price = self::GetVehiclePrices($price, $vehicle);

        return $price;
    }

    /**
     * @param Price $price
     * @param Vehicle $vehicle
     * @return Price     */
    private static function GetVehiclePrices(price $price, Vehicle $vehicle): Price
    {
        $price = self::getCashPrice($vehicle->getCashPrice(), $price);
        $price = self::getLeasingPrice($vehicle->getLeasingPrice(), $price);
        $price = self::getFinancingPrice($vehicle->getFinancingPrice(), $price);
        return $price;
    }

    /**
     * @param Price $cashPrice
     * @return Price     */
    private static function getCashPrice(?array $cashPrice, Price $price): Price
    {
        if($cashPrice !== null || !empty($cashPrice))
        {
            $price->setCashPriceFormatted($cashPrice['priceFormatted']);
            $price->setCashPriceLabelVehicleCards($cashPrice['priceLabelVehicleCards']);
            $price->setCashPriceLabelDetailsPage($cashPrice['priceLabelDetailsPage']);
            $price->setHasCashPrice(true);

            return $price;
        }

        $price->setHasCashPrice(false);

        return $price;
    }

    /**
     * @param Price $leasingPrice
     * @return Price
     */
    private static function getLeasingPrice(?array $leasingPrice, Price $price): Price
    {
        if($leasingPrice !== null || !empty($leasingPrice))
        {
            $price->setLeasingPriceFormatted($leasingPrice['priceFormatted']);
            $price->setLeasingPriceLabelVehicleCards($leasingPrice['priceLabelVehicleCards']);
            $price->setLeasingPriceLabelDetailsPage($leasingPrice['priceLabelDetailsPage']);
            $price->setHasLeasingPrice(true);

            return $price;
        }

        $price->setHasLeasingPrice(false);

        return $price;
    }

    /**
     * @param Price $financingPrice
     * @return Price     */
    private static function getFinancingPrice(?array $financingPrice, Price $price): Price
    {
        if($financingPrice !== null || !empty($financingPrice))
        {
            $price->setFinancingPriceFormatted($financingPrice['priceFormatted']);
            $price->setfinancingPriceLabelVehicleCards($financingPrice['priceLabelVehicleCards']);
            $price->setfinancingpriceLabelDetailsPage($financingPrice['priceLabelDetailsPage']);
            $price->setHasfinancingPrice(true);

            return $price;
        }

        $price->setHasFinancingPrice(false);

        return $price;
    }

    /**
     * @param Property[] $properties
     * @return array
     */
    private static function getPriceProperties(array $properties): array
    {
        $priceProperties =  [];

        foreach ($properties as $property) {
            if (in_array($property->getId(), RELATED_PRICE_PROPERTY_KEYS)) {
                $priceProperties[$property->getId()] = $property;
            }
        }

        return $priceProperties;
    }
}
