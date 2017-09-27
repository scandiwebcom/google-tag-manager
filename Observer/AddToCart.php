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
use Scandi\Gtm\Helper\Config;

/**
 * Class AddToCart
 * @package Scandi\Gtm\Observer
 */
class AddToCart implements ObserverInterface
{

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Config
     */
    protected $config;

    /**
     * AddToCart constructor.
     * @param CustomerSession $customerSession
     * @param Product $product
     * @param Config $config
     */
    public function __construct(
        CustomerSession $customerSession,
        Product $product,
        Config $config
    )
    {
        $this->customerSession = $customerSession;
        $this->product = $product;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $params = $observer->getRequest()->getParams();
        if ($this->config->isEnabled()){
            if (!$observer->getRequest()->isAjax()) {
                $product = $observer->getProduct();
                $this->customerSession->setAddToCart(
                    json_encode($this->product->collectCartEvent($product, 'addToCart')));
            }
        }
    }
}
