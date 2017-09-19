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
use Scandi\Gtm\Helper\Config;

/**
 * Class AddToCart
 * @package Scandi\Gtm\Observer
 */
class Success implements ObserverInterface
{

    /** @var CheckoutSession */
    protected $checkoutSession;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Success constructor.
     * @param CheckoutSession $checkoutSession
     * @param Config $config
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Config $config
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            $orderData['order_id'] = $observer->getEvent()->getOrderIds()[0];
            $this->checkoutSession->setGtmSuccess(json_encode($orderData));
        }
    }
}
