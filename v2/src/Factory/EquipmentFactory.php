<?php

namespace Biltorvet\Factory;

use Biltorvet\Model\Equipment;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EquipmentFactory
{
    /**
     * @param array $data
     * @return array|null
     */
    public static function create(array $data): ?array
    {

        if ($data == null) {
            return $data;
        } else {

            /** @var Serializer $serializer */
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

            /** @var Equipment[] $equipment */
            $equipment = [];

            foreach ($data as $equipment) {
                $equipment[] = $serializer
                    ->deserialize(
                        $serializer->serialize($equipment, 'json'),
                        Equipment::class,
                        'json'
                    );
            }

            return $equipment;
        }
    }
}
