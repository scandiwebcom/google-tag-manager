<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper\Collectors;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;


/**
 * Class Attributes
 * @package Scandi\Gtm\Helper\Collectors
 */
class Attributes
{

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * Attributes constructor.
     */
    public function __construct(
    )
    {
    }

    /**
     * @param $product
     * @param $params
     * @return int
     */
    public function getAttributes($product, $params)
    {
        foreach($this->getAttributesIds($params) as $key=>$value) {

        }
        return $product;
    }

    /**
     * @param $params
     * @return array
     */
    public function getAttributesIds($params)
    {
        if (!key_exists('super_attribute', $params)) {
            return array();
        }
        return $params['super_attribute'];
    }

}
