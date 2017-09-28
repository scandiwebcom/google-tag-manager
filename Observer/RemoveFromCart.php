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
use Scandi\Gtm\Helper\Configurable;

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
     * @var Config
     */
    protected $config;

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * RemoveFromCart constructor.
     * @param CustomerSession $customerSession
     * @param Product $product
     * @param Config $config
     * @param Configurable $configurable
     */
    public function __construct(
        CustomerSession $customerSession,
        Product $product,
        Config $config,
        Configurable $configurable
    )
    {
        $this->customerSession = $customerSession;
        $this->product = $product;
        $this->config = $config;
        $this->configurable = $configurable;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {

        if ($this->config->isEnabled()) {
            $product = $observer->getEvent()->getQuoteItem();
            if ($product->getProductType() === Configurable::CONFIGURABLE_TYPE_ID) {
                $product = $this->configurable->extendConfigurable($product);
            }
            $addData['event'] = 'removeFromCart';
            $addData['ecommerce']['remove']['products'] = array($this->product->collectProductData($product));
            $this->customerSession->setRemoveFromCart(json_encode($addData));
        }
    }
}
