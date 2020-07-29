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
