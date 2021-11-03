<?php


namespace Biltorvet\Factory;

use Biltorvet\Controller\PriceController;
use Biltorvet\Helper\DataHelper;
use TextUtils;


class StructuredDataFactory
{
    public static function VehicleDetails($vehicle, $vehiclePrice, $vehicleEquipment, $options) : string
    {
        global $wp;
        $brandUrl = rtrim(get_page_link($options['vehiclesearch_page_id']),'/') . "/1/Maerke/" . ucfirst($vehicle->getMakeName());
        $vehicleCondition = $vehicle->getBrandNew() == false ? "Brugt" : "Fabriksny";
        $vehicleProperties = DataHelper::getVehiclePropertiesAssoc($vehicle->getProperties());
        $vehicleDescription = $vehicle->getDescription() != null? TextUtils::Sanitize($vehicle->getDescription()) : $vehicle->getMakeName() . $vehicle->getModel() . $vehicle->getVariant();

        $equipment = null;

        if($vehicleEquipment != null) {

            $equipment .= ',"additionalProperty": [';

            foreach ($vehicleEquipment as $key => $value) {
                if(count($vehicleEquipment) -1 == $key) {
                    $equipment .= '{"@context": "http://schema.org","@type": "PropertyValue","name": "' . TextUtils::Sanitize($value->publicName) . '"}]';
                } else {
                    $equipment .= '{"@context": "http://schema.org","@type": "PropertyValue","name": "' . TextUtils::Sanitize($value->publicName) . '"},';
                }
            }
        }

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

        if($vehicle->getVin() != null) {
            $data .= ',"identifier": "' . $vehicle->getVin() . '"';
        }

        if($equipment != null) {
            $data .= $equipment;
        }

        $data .= '}';

        return $data;
    }

    public static function VehicleSearchPage($vehicleFeed, $start, $end, $options) : string
    {
        $vehicleCount = $end - $start;
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $vehicleBaseUrl = rtrim(get_permalink($options['vehiclesearch_page_id']),'/');

        $data = '';
        $data .= '{"@context": "https://schema.org","@type": "ItemList","url": "' . $url . '","numberOfItems": "' . $vehicleCount . '","itemListElement": [';

        foreach ($vehicleFeed->vehicles as $key => $value) {

            $vehicle = VehicleFactory::create(json_decode(json_encode($value), true));
            $priceController = new PriceController($vehicle);
            $vehiclePrice = $priceController->getStructuredDataPrice() ?? 0;
            $vehicleUrl = $vehicle->getUri();
            $vehicle->getDescription() != null? TextUtils::Sanitize($vehicle->getDescription()) :
            $vehicleDescription = $vehicle->getMakeName() . $vehicle->getModel() . $vehicle->getVariant();
            $brandUrl = rtrim(get_page_link($options['vehiclesearch_page_id']),'/') . "/1/" . ucfirst($vehicle->getMakeName());
            $vehicleLabels = array();

            foreach ($vehicle->getLabels() as $label) {
                array_push($vehicleLabels, $label->getKey());
            }

            $data .= '{"@type": "ListItem",';
            $data .= '"position": ' . ($key +1) . ',';
            $data .= '"item": {"@type": "Product",';
            $data .= '"image": "' . $vehicle->getImages()[0] . '",';
            $data .= '"url": "' . $vehicleBaseUrl . '/' . $vehicleUrl . '",';
            $data .= '"name": "' . $vehicle->getMakeName() . '",';
            $data .= '"offers": {"@type": "Offer",';

            if(!in_array(5, $vehicleLabels)) {

                // Does not have the sold status label
                $data .= '"availability": "http://schema.org/InStock",';
            } else {

                // Has the sold status label
                $data .= '"availability": "http://schema.org/OutOfStock",';
            }

            $data .= '"price": "' . $vehiclePrice . '",';
            $data .= '"priceCurrency": "DKK"},';
            $data .= '"brand": {"@context": "https://schema.org","@type": "Brand","logo": "https://picture.biltorvet.dk/img/maerkelogo/' . $vehicle->getMakeName() . '.png?height=80","name": "' . $vehicle->getMakeName() .'","url": "' . $brandUrl . '"},';
            $data .= '"sku": "' . $vehicle->getDocumentId() . '",';
            if((int)$vehicleCount -1 == $key) {
                $data .= '"description": "' . $vehicleDescription . '"}}';
            } else {
                $data .= '"description": "' . $vehicleDescription . '"}},';
            }
        }

        $data .= ']}';

        return $data;
    }
}