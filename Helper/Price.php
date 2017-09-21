<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper;

/**
 * Class Price
 * @package Scandi\Gtm\Helper
 */
class Price
{

    /**
     * @param $product
     * @return string
     */
    public function collectProductPrice($product)
    {
        if ($product->getPriceInfo()) {
            return number_format($product->getPriceInfo()->getPrice('final_price')->getValue(), 2);
        } else {
            //TODO need to load product from the repository and get PriceInfo to get price in current store.
            // price_incl_tax can play a role
            return number_format($product->getData('price'), 2);
        }
    }
}
