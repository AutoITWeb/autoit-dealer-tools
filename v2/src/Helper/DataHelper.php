<?php

namespace Biltorvet\Helper;

use Biltorvet\Factory\VehicleFactory;
use Biltorvet\Model\ApiResponse;
use Biltorvet\Model\Property;
use Biltorvet\Model\Vehicle;
use Biltorvet\Model\VehicleLead;
use Exception;
use stdClass;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DataHelper
{
    /**
     * @param  array $properties
     * @return array
     */
    public static function getVehiclePropertiesAssoc(array $properties) : array
    {
        $propertiesToRender = [];

        /**
*
         *
 * @var Property[] $properties
*/
        foreach ($properties as $property) {
            if ($property->getId() !== null) {
                $propertiesToRender[$property->getId()] = $property;
            }
        }

        return $propertiesToRender;
    }

    /**
     * @param  Vehicle[] $vehicles
     * @param  int       $labelId
     * @return Vehicle[]
     */
    public static function filterVehiclesByLabel(array $vehicles, int $labelId) : array
    {
        $filteredVehicles = [];

        foreach ($vehicles as $vehicle) {
            foreach ($vehicle->getLabels() as $label) {
                if ($label->getKey() === $labelId) {
                    $filteredVehicles[] = $vehicle;
                }
            }
        }

        return $filteredVehicles;
    }

    /**
     * @param  Vehicle[] $vehicles
     * @param  int       $typeId
     * @param  bool       $brandNew
     * @return Vehicle[]
     */
    public static function filterVehiclesByTypeAndState(array $vehicles, int $typeId, bool $brandNew) : array
    {
        $filteredVehicles = [];

        if ($brandNew == null){
            foreach ($vehicles as $vehicle) {
                if ($vehicle->getTypeId() === $typeId) {
                    $filteredVehicles[] = $vehicle;
                }
            }

        return $filteredVehicles;
        }

        foreach ($vehicles as $vehicle) {
                if ($vehicle->getTypeId() === $typeId && $vehicle->getBrandNew() === $brandNew) {
                    $filteredVehicles[] = $vehicle;
            }
        }

        return $filteredVehicles;
    }

    /**
     * @param  Vehicle[] $vehicles
     * @param  int       $typeId
     * @return Vehicle[]
     */
    public static function filterVehiclesByType(array $vehicles, int $typeId) : array
    {
        $filteredVehicles = [];

        foreach ($vehicles as $vehicle) {
            if ($vehicle->getTypeId() === $typeId) {
                $filteredVehicles[] = $vehicle;
            }
        }

        return $filteredVehicles;
    }

    /**
     * @param  ApiResponse $response
     * @return Vehicle[]
     * @throws Exception
     */
    public static function getVehiclesFromApiResponse(ApiResponse $response) : array
    {
        /**
*
         *
 * @var Vehicle[] $vehicles
*/
        $vehicles = [];

        if ($response->getStatus() === 1 && array_key_exists('vehicles', $response->getResult())) {
            foreach ($response->getResult()['vehicles'] as $vehicle) {
                $vehicles[] = VehicleFactory::create($vehicle);
            }
        } else {
            throw new Exception('Unable to locate vehicle array');
        }

        return $vehicles;
    }

    /**
     * Converts a VehicleLead into a Leadobject that can be understood by the API.
     *
     * @param  VehicleLead $lead
     * @param  bool        $json
     * @return mixed
     */
    public static function createLeadObject(VehicleLead $lead, bool $json = true)
    {

        $leadObject = new stdClass();
        $leadObject->Body = $lead->getType() . "\r\n";
        $leadObject->Body .= $lead->getModel() . "\r\n";
        $leadObject->Body .= $lead->getVin() . "\r\n";
        $leadObject->Body .= $lead->getNumberPlate() . "\r\n";
        $leadObject->Body .= $lead->getFirstRegitrationDate() . "\r\n";
        $leadObject->Body .= $lead->getRequestedTestdriveDateTime() . "\r\n";
        $leadObject->Body .= $lead->getMessage() . "\r\n";

        $leadObject->Name = $lead->getName() ?? '';
        $leadObject->PostalCode = $lead->getPostalCode() ?? '';
        $leadObject->City = $lead->getCity() ?? '';
        $leadObject->CellPhoneNumber = $lead->getCellPhoneNumber() ?? '';
        $leadObject->Email = $lead->getEmail() ?? '';
        $leadObject->ActivityType = $lead->getActivityType() ?? '';
        $leadObject->CompanyId = $lead->getCompanyId() ?? '';
        $leadObject->WebsiteURL = $lead->getWebsiteUrl() ?? '';

        return ($json ? json_encode($leadObject) : $leadObject);
    }
}
