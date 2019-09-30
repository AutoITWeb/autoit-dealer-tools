<?php


namespace Biltorvet\Helper;


use Biltorvet\Model\ApiResponse;

class ProductHelper
{
    /**
     * @param string $productName
     * @param array $products
     * @return bool
     */
    public static function hasAccess(string $productName, ApiResponse $products) : bool {

        foreach ($products->getResult() as $product) {
            if (isset($product['name']) && $product['name'] === 'Vehicle Module') {
                return true;
            }
        }

        return false;
    }
}
