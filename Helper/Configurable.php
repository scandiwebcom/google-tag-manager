<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper;

use Magento\Catalog\Model\Product\Interceptor;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableType;
use Magento\Framework\Exception\NotFoundException;
use Scandi\Gtm\Helper\Collectors\Attributes;

/**
 * Class Cart
 * @package Scandi\Gtm\Helper\Collectors
 */
class Configurable
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ConfigurableType
     */
    protected $configurableType;

    /**
     * @var Interceptor
     */
    public $parent;

    /**
     * @var Interceptor
     */
    public $child;

    /**
     * @var Config
     */
    public $config;

    /**
     * @var Attributes
     */
    private $attributes;

    const CONFIGURABLE_TYPE_ID = 'configurable';

    /**
     * Configurable constructor.
     * @param ProductRepository $productRepository
     * @param ConfigurableType $configurableType
     * @param Config $config
     * @param Attributes $attributes
     */
    public function __construct(
        ProductRepository $productRepository,
        ConfigurableType $configurableType,
        Config $config,
        Attributes $attributes
    )
    {
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
        $this->config = $config;
        $this->attributes = $attributes;
    }

    /**
     * @param $product
     * @return array|bool
     */
    public function getProductData($product)
    {
        if (!$this->getChildAndParent($product)) {
            return array();
        }
        $productData['id'] = $this->parent->getSku();
        $productData['name'] = $this->child->getName();
        $productData['price'] = number_format($product->getData("price_incl_tax"), 2);
        $productData['qty'] = (string)$product->getQty();
        if ($this->child->getColor()) {
            $product = $this->retrieveAttribute('color', $product);
            $productData['dimension1'] = $product->getData('color');
        }
        $productData['dimension2'] = $this->child->getSku();
        if ($this->child->getSize()) {
            $product = $this->retrieveAttribute('size', $product);
            $productData['variant'] = $product->getData('size');
        }
        $productData['brand'] = $this->config->getBrand();
        return $productData;
    }

    /**
     * @param $product
     * @return bool|$this
     */
    public function getChildAndParent($product)
    {
        $parentByChild = $this->configurableType->getParentIdsByChild($product->getItemId());

        // For the case if quote did not initialise the parent of the product
        if (sizeof($parentByChild) <= 0) {
            $this->parent = $product;
        } else {
            $this->parent = $this->productRepository->getById($parentByChild[0]);
        }
        $this->child = $this->productRepository->get($product->getSku());
        return $this;
    }

    /**
     * @param $product
     * @return array
     */
    public function extendConfigurable($product)
    {
        if (!$this->getChildAndParent($product)) {
            return $product;
        }
        $attributes = ['color', 'size'];
        foreach ($attributes as $attributeClass) {
            if ($this->child->getData($attributeClass)) {
                $product = $this->retrieveAttribute($attributeClass, $product);
            }
        }
        return $product;
    }

    /**
     * @param $attributeClass
     * @param $product
     * @return mixed
     */
    protected function retrieveAttribute($attributeClass, $product)
    {
        $attribute = $this->attributes->getAttributeLabel($this->child->getData($attributeClass), $attributeClass);
        if (sizeof($attribute) === 1 && key_exists($attributeClass, $attribute)) {
            $product->setData($attributeClass, $attribute[$attributeClass]);
        }
        return $product;
    }
}
