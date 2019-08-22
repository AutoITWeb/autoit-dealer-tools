<?php

namespace Biltorvet\Factory;

use Biltorvet\Model\Company;
use Biltorvet\Model\Equipment;
use Biltorvet\Model\Vehicle;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class VehicleFactory
{
    /**
     * @param  array $data
     * @return Vehicle
     */
    public static function create(array $data): Vehicle
    {

        /**
*
         *
 * @var Serializer $serializer
*/
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        /**
*
         *
 * @var Vehicle $vehicle
*/
        $vehicle = $serializer
            ->deserialize(
                $serializer->serialize($data, 'json'),
                Vehicle::class,
                'json',
                ['ignored_attributes' => ['company', 'equipment', 'properties', 'labels']]
            );
        $vehicle
            ->setCompany(
                $serializer->deserialize(
                    $serializer->serialize($data['company'], 'json'),
                    Company::class,
                    'json'
                )
            )
            ->setEquipment(EquipmentFactory::create($data['equipment']))
            ->setProperties(PropertyFactory::create($data['properties']))
            ->setLabels(LabelFactory::create($data['labels']));

        return $vehicle;
    }
}
