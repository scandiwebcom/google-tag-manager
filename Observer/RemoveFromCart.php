<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Scandi\Gtm\Helper\Collectors\Product;

/**
 * Class AddToCart
 * @package Scandi\Gtm\Observer
 */
class RemoveFromCart implements ObserverInterface
{

    /** @var CustomerSession */
    protected $customerSession;

    /**
     * @var Product
     */
    protected $product;

    /**
     * RemoveFromCart constructor.
     * @param CustomerSession $customerSession
     * @param Product $product
     */
    public function __construct(
        CustomerSession $customerSession,
        Product $product
    )
    {
        $this->customerSession = $customerSession;
        $this->product = $product;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getQuoteItem();
        $addData['ecommerce']['event'] = 'removeFromCart';
        $addData['ecommerce']['remove'] = $this->product->collectProductData($product);
        $this->customerSession->setRemoveFromCart(json_encode($addData));
    }
}
