<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper\Collectors;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session;
use Scandi\Gtm\Helper\Configurable;

/**
 * Class Cart
 * @package Scandi\Gtm\Helper\Collectors
 */
class Cart
{

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * Cart constructor.
     * @param Session $checkoutSession
     * @param ProductRepository $productRepository
     * @param Configurable $configurable
     */
    public function __construct(
        Session $checkoutSession,
        ProductRepository $productRepository,
        Configurable $configurable
    )
    {
        $this->quote = $checkoutSession->getQuote();
        $this->productRepository = $productRepository;
        $this->configurable = $configurable;
    }

    /**
     * @return null
     */
    public function collectCart()
    {
        if ($this->quote->getItemsQty() == 0) {
            return null;
        }
        //Value will include discount if any exist
        $cartData["total"] = number_format($this->quote->getGrandTotal(), 2);
        $cartData["qty"] = (int)$this->quote->getItemsQty();
        $cartData["qty"] = (string)($cartData['qty']);
        $cartData["products"] = $this->collectProducts($this->quote);
        return $cartData;
    }

    /**
     * @param $quote
     * @return array|bool
     */
    public function collectProducts($quote)
    {
        $brand = $this->configurable->config->getBrand();
        foreach ($quote->getAllItems() as $product) {
            // Sort out simple products in the configurable or bundled
            if ($product->getData("price_incl_tax") == 0) {
                continue;
            }
            if ($product->getProductType() === Configurable::CONFIGURABLE_TYPE_ID) {
                $productsData[] = $this->configurable->getProductData($product);
                continue;
            }
            $productsData[] = [
                "id" => $product->getSku(),
                "name" => $product->getName(),
                "price" => number_format($product->getData("price_incl_tax"), 2),
                "qty" => (string)$product->getQty(),
                "dimension1" => (string) $product->getColor(),
                "dimension2" => $product->getSku(),
                "variant" => (string) $product->getSize(),
                "brand" => $brand
            ];
        }
        return isset($productsData) ? $productsData : false;
    }

}
