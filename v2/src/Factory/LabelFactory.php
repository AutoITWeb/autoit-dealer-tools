<?php


namespace Biltorvet\Factory;

use Biltorvet\Model\Label;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LabelFactory
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
 * @var Label[] $label
*/
            $labels = [];

            foreach ($data as $label) {
                $labels[] = $serializer->deserialize($serializer->serialize($label, 'json'), Label::class, 'json');
            }

            return $labels;
        }
    }
}
