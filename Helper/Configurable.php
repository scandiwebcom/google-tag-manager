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
     * Configurable constructor.
     * @param ProductRepository $productRepository
     * @param ConfigurableType $configurableType
     * @param Config $config
     */
    public function __construct(
        ProductRepository $productRepository,
        ConfigurableType $configurableType,
        Config $config
    )
    {
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
        $this->config = $config;
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
        return [
            "id" => $this->parent->getSku(),
            "name" => $this->child->getName(),
            "price" => number_format($product->getData("price_incl_tax"), 2),
            "qty" => (string)$product->getQty(),
            "dimension1" => (string)$this->child->getColor(),
            "dimension2" => $this->child->getSku(),
            "variant" => (string)$this->child->getSize(),
            "brand" => $this->config->getBrand()
        ];
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

}
