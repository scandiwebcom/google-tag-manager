<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper\Collectors;

use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Registry;
use Scandi\Gtm\Helper\Config;
use Scandi\Gtm\Helper\Configurable;

/**
 * Class Product
 * @package Scandi\Gtm\Helper\Collectors
 */
class Product
{

    /**
     * @var View
     */
    protected $view;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * Product constructor.
     * @param View $view
     * @param Registry $registry
     * @param Category $category
     * @param ProductRepository $productRepository
     * @param Config $config
     * @param Data $jsonHelper
     * @param Configurable $configurable
     */
    public function __construct(
        View $view,
        Registry $registry,
        Category $category,
        ProductRepository $productRepository,
        Config $config,
        Data $jsonHelper,
        Configurable $configurable
    )
    {
        $this->view = $view;
        $this->registry = $registry;
        $this->category = $category;
        $this->productRepository = $productRepository;
        $this->config = $config;
        $this->jsonHelper = $jsonHelper;
        $this->configurable = $configurable;
    }

    /**
     * @return mixed
     */
    public function createDetails()
    {
        return $this->collectProductData($this->view->getProduct(), 'product');
    }

    /**
     * @param $product
     * @param null $pageType
     * @return string
     */
    public function collectProductData($product, $pageType = null)
    {
        $productData['id'] = $product->getSku();
        $productData['name'] = $product->getName();
        if (!$this->registry->registry('current_category')) {
            if ($product->getCategoryIds()) {
                $productData['category'] = $this->category->getCategoryName($product->getCategoryIds());
            } else {
                $categories = $this->productRepository->get($product->getSku())->getCategoryIds();
                $productData['category'] = $this->category->getCategoryName($categories);
            }
        } else {
            $productData['category'] = $this->registry->registry('current_category')->getName();
        }
        $productData['currencyCode'] = $this->config->getStoreCurrency();
        $productData['price'] = $this->config->price->collectProductPrice($product);
        $productData['brand'] = $this->config->getBrand();
        $productData = $this->configurable->addAttributesToData($product, $productData);
        if ($product->getQty()) {
            $productData['qty'] = $product->getQty();
        }
        if ($pageType) {
            $productData['list'] = $pageType;
        }
        if ($pageType !== 'product') {
            return $productData;
        }
        return $this->handleDetailsPush($productData);
    }

    /**
     * @param $product
     * @param $eventName
     * @return mixed
     */
    public function collectCartEvent($product, $eventName)
    {
        $addData['event'] = $eventName;
        switch ($eventName) {
            case 'addToCart':
                $addData['ecommerce']['add']['products'] = array($this->collectProductData($product));
                break;
            case 'removeFromCart':
                $addData['ecommerce']['remove']['products'] = array($this->collectProductData($product));
                break;
            default:
                break;
        }
        return $addData;
    }

    /**
     * @param $id
     * @param $eventName
     * @return mixed
     */
    public function getProductPushById($id, $eventName)
    {
        $product = $this->productRepository->getById($id);
        return $this->collectCartEvent($product, $eventName);
    }


    private function handleDetailsPush($productsDetails)
    {
        $push['event'] = 'detail';
        $push['ecommerce']['detail']['products'] = array($productsDetails);
        return "dataLayer.push(" . $this->jsonHelper->jsonEncode($push) . ");";
    }
}
