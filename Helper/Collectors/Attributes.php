<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper\Collectors;

use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\AttributeRepository;
use Scandi\Gtm\Helper\Config;
use Scandi\Gtm\Helper\Configurable;

/**
 * Class Attributes
 * @package Scandi\Gtm\Helper\Collectors
 */
class Attributes
{

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var ProductFactory
     */
    protected $attributeLoading;

    /**
     * @var Config
     */
    private $config;

    /**
     * Attributes constructor.
     * @param AttributeRepository $attributeRepository
     * @param ProductFactory $attributeLoading
     * @param Config $config
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        ProductFactory $attributeLoading,
        Config $config
    )
    {
        $this->attributeLoading = $attributeLoading;
        $this->attributeRepository = $attributeRepository;
        $this->config = $config;
    }

    /**
     * @param $product
     * @param $params
     * @return int
     */
    public function handleAttributes($product, $params)
    {
        $attributes = [];
        if ($product->getTypeId() !== Configurable::CONFIGURABLE_TYPE_ID) {
            return $product;
        }
        foreach ($this->getAttributesIds($params) as $key => $value) {
            $attributes[] = $this->getAttributeLabel($value);
        }
        if (sizeof($attributes) > 0) {
            $product = $this->setAttributes($product, $attributes);
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

    /**
     * @param $optionId
     * @param null $attribute
     * @return array
     */
    public function getAttributeLabel($optionId, $attribute = null)
    {
        $productResource = $this->getProductFactory();
        $resource = $productResource->getResource();
        if (!$attribute) {
            $attributes = $this->config->getActiveVariables();
        } else {
            $attributes[] = $attribute;
        }
        foreach ($attributes as $attribute) {
            $label = $resource->getAttribute($attribute);
            if ($label->usesSource()) {
                $attributeLabel = $label->getSource()->getOptionText($optionId);
            }
            if (!isset($attributeLabel)) {
                return array();
            }
            if ($attributeLabel) {
                return array($attribute => $attributeLabel);
            }
        }
        return array();
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductFactory()
    {
        return $this->attributeLoading->create();
    }

    /**
     * @param $product
     * @param $attributes
     * @return mixed
     */
    public function setAttributes($product, $attributes)
    {
        if (!is_array($attributes)) {
            return $product;
        }
        foreach ($attributes as $attribute) {
            foreach ($attribute as $key => $value) {
                $product->setData($key, $value);
            }
        }
        return $product;
    }
}
