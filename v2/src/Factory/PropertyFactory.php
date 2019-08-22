<?php


namespace Biltorvet\Factory;

use Biltorvet\Model\Property;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PropertyFactory
{
    public static function create($data)
    {
        if ($data == null) {
            return $data;
        } else {

            /**
*
             *
 * @var Serializer $serializer
*/
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

            /**
*
             *
 * @var Property[] $property
*/
            $properties = [];

            foreach ($data as $property) {
                $properties[] = $serializer
                    ->deserialize($serializer->serialize($property, 'json'), Property::class, 'json');
            }

            return $properties;
        }
    }
}
