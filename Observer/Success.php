<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Observer;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\OrderRepository;

/**
 * Class AddToCart
 * @package Scandi\Gtm\Observer
 */
class Success implements ObserverInterface
{
    /** @var CheckoutSession */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    /**
     * Success constructor.
     * @param OrderRepository $order
     * @param CheckoutSession $checkoutSession ]
     */
    public function __construct(
        OrderRepository $order,
        CheckoutSession $checkoutSession
    )
    {
        $this->order = $order;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $orderData['order_id'] = $observer->getEvent()->getOrderIds()[0];
        $this->checkoutSession->setGtmSuccess(json_encode($orderData));
    }
}
