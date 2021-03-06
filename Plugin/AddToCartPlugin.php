<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Plugin;

use Magento\Catalog\Model\Category\AttributeRepository;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Controller\Cart\Add;
use Magento\Framework\Json\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Scandi\Gtm\Helper\Collectors\Attributes;
use Scandi\Gtm\Helper\Collectors\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Scandi\Gtm\Helper\Config;

/**
 * Class AddToCartPlugin
 * @package Scandi\Gtm\Plugin
 */
class AddToCartPlugin
{

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Attributes
     */
    protected $attributes;

    /**
     * AddToCartPlugin constructor.
     * @param Product $product
     * @param Data $jsonHelper
     * @param ProductRepository $productRepository
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param Attributes $attributes
     */
    public function __construct(
        Product $product,
        Data $jsonHelper,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManager,
        Config $config,
        Attributes $attributes
    )
    {
        $this->product = $product;
        $this->jsonHelper = $jsonHelper;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->attributes = $attributes;
    }

    /**
     * @param Add $subject
     * @param $result
     * @return bool
     */
    public function afterExecute(Add $subject, $result)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }
        if (!$subject->getResponse()->getStatusCode() === 200) {
            return $result;
        }
        $product = $this->productInit($subject);
        if (!$product) {
            return $result;
        }
        $content["eventPush"] = $this->product->collectCartEvent($product, 'addToCart');
        $subject->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($content)
        );
        return $result;
    }

    /**
     * @param $subject
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface|mixed
     */
    public function productInit($subject)
    {
        $productId = (int)$subject->getRequest()->getParam('product');
        $content = $subject->getResponse()->getContent();
        if ($content !== '') {
            $content = $this->jsonHelper->jsonDecode($content);
        } else {
            $content = array();
        }
        if ($productId && !array_key_exists("statusText", $content)) {
            $storeId = $this->storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
                $product = $this->attributes->handleAttributes($product, $subject->getRequest()->getParams());
                return $product;

            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}

?>
