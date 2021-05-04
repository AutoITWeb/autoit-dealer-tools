<?php


namespace Biltorvet\Factory;

use Biltorvet\Helper\DataHelper;
use Biltorvet\Factory\EquipmentFactory;


class StructuredDataFactory
{
    public static function VehicleDetails($vehicle, $vehiclePrice, $options) : string
    {
        global $wp;
        $brandUrl = rtrim(get_page_link($options['vehiclesearch_page_id']),'/') . "/1/" . ucfirst($vehicle->getMakeName());
        $vehicleCondition = $vehicle->getBrandNew() == false ? "Brugt" : "Fabriksny";
        $vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
        $vehicleDescription = $vehicle->getDescription() != null? $vehicle->getDescription() : $vehicle->getMakeName() . $vehicle->getModel() . $vehicle->getVariant();

        //$vehicleEquipment = EquipmentFactory::create($vehicle->getEquipment());

        $data = "";
        $data .= '{"@context": "http://schema.org","@type": "Vehicle","url": "' . home_url($wp->request) . '","image": "' . $vehicle->getImages()[0] .'","sku": "' . $vehicle->getDocumentId() . '","itemCondition": "' . $vehicleCondition . '",';
        $data .= '"brand": {"@context": "https://schema.org","@type": "Brand","logo": "https://picture.biltorvet.dk/img/maerkelogo/' . $vehicle->getMakeName() . '.png?height=80","name": "' . $vehicle->getMakeName() .'","url": "' . $brandUrl . '"},';
        $data .= '"offers": {"@context": "https://schema.org","@type": "Offer","priceCurrency": "DKK","price": "' . $vehiclePrice .'"},';
        $data .= '"vehicleEngine": {"@context": "https://schema.org","@type": "EngineSpecification","enginePower": {"@type": "QuantitativeValue","value": "' . $vehicleProperties['MaxHorsepower']->getValue() .'","unitCode": "N12"},"fuelType" : "' . $vehicleProperties['PropellantType']->getValue() . '"},';
        $data .= '"name" : "' . $vehicle->getMakeName() . $vehicle->getModel() . $vehicle->getVariant() . '",';
        $data .= '"description" : "' . $vehicleDescription . '",';
        $data .= '"manufacturer": {"@context": "https://schema.org","@type": "Organization","address": {"@context": "https://schema.org","@type": "PostalAddress","addressCountry": "DK","addressLocality": "' . $vehicle->getCompany()->getCity() .'","postalCode": "' . $vehicle->getCompany()->getPostNumber() .'","streetAddress": "' . $vehicle->getCompany()->getAddress() . '"}}';

        if($vehicleProperties['TopSpeed']->getValue() != null) {
            $data .= ',"speed": {"@type": "QuantitativeValue","minValue": "0","maxValue": "' . $vehicleProperties['TopSpeed']->getValue() .'","unitCode": "KMH"}';
        }

        if($vehicleProperties['Mileage']->getValue() != null) {
            $data .= ',"mileageFromOdometer" : {"@type" : "QuantitativeValue","value" : "' . $vehicleProperties['Mileage']->getValue() . '","unitCode" : "KMT"}';
        }

        if($vehicleProperties['Color']->getValue() != null) {
            $data .= ',"color" : "' . $vehicleProperties['Color']->getValue() . '>"';
        }

        if($vehicleProperties['BodyType']->getValue() != null) {
            $data .= ',"bodyType" : "' . $vehicleProperties['BodyType']->getValue() .'"';
        }

        if($vehicleProperties['SeatCount']->getValue() != null) {
            $data .= ',"vehicleSeatingCapacity" : "' . $vehicleProperties['SeatCount']->getValue() .'"';
        }

        if($vehicleProperties['AirbagCount']->getValue() != null) {
            $data .= ',"numberOfAirbags" : "' . $vehicleProperties['AirbagCount']->getValue() . '"';
        }

        if($vehicleProperties['DoorCount']->getValue() != null) {
            $data .= ',"numberOfDoors" : "' . $vehicleProperties['DoorCount']->getValue() . '"';
        }

        if($vehicleProperties['ModelYear']->getValue() != null) {
            $data .= ',"modelDate" : "' . $vehicleProperties['ModelYear']->getValue() . '"';
        }

        $data .= '}';

        return $data;
    }

    public static function VehicleSearchPage() : string
    {

    }
}