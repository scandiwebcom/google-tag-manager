<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper\Collectors;

use Magento\Checkout\Model\Session;

class Cart
{

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Cart constructor.
     * @param Session $checkoutSession
     */
    public function __construct(
        Session $checkoutSession
    )
    {
        $this->quote = $checkoutSession->getQuote();
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
        $cartData["products"] = $this->collectProducts($this->quote);
        return $cartData;
    }

    /**
     * @param $quote
     * @return array|bool
     */
    public function collectProducts($quote)
    {
        foreach ($quote->getAllItems() as $product) {
            // Sort out simple products in the configurable or bundled
            if ($product->getData("price_incl_tax") == 0) {
                continue;
            }
            $productsData[] = [
                "id" => $product->getSku(),
                "name" => $product->getName(),
                "price" => number_format($product->getData("price_incl_tax"), 2),
                "qty" => $product->getQty()
            ];
        }
        return isset($productsData) ? $productsData : false;
    }
}
