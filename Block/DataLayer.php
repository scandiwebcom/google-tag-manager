<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template\Context;
use Scandi\Gtm\Block\DataLayer\DataLayerCollector;
use Scandi\Gtm\Helper\Customer;
use Scandi\Gtm\Helper\Collectors\Cart;
use Scandi\Gtm\Helper\Collectors\Event;
use Scandi\Gtm\Helper\Name;

class DataLayer extends DataLayerCollector
{

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var Name
     */
    protected $nameHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Customer
     */
    protected $customerHelper;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var Event
     */
    protected $event;

    /**
     * DataLayer constructor.
     * @param Context $context
     * @param Http $request
     * @param Name $nameHelper
     * @param Session $customerSession
     * @param Customer $customerHelper
     * @param Cart $cart
     * @param Event $event
     */
    public function __construct(
        Context $context,
        Http $request,
        Name $nameHelper,
        Session $customerSession,
        Customer $customerHelper,
        Cart $cart,
        Event $event
    )
    {
        parent::__construct($context);
        $this->request = $request;
        $this->nameHelper = $nameHelper;
        $this->customerSession = $customerSession;
        $this->customerHelper = $customerHelper;
        $this->cart = $cart;
        $this->event = $event;
    }

    /**
     * @return mixed|string
     */
    private function getPageName()
    {
        $frontAction = $this->request->getFullActionName();
        return $this->nameHelper->getEccomPageName($frontAction);
    }

    /**
     * @return mixed
     */
    public function collectLayer()
    {
        $id = $this->customerHelper->getCustomerId($this->customerSession);
        $layer['pageType'] = $this->getPageName();
        $layer['customerId'] = $id;
        $layer['ecommerce']['cart'] = $this->cart->collectCart();
        if (!$layer['ecommerce']['cart']) {
            unset($layer['ecommerce']['cart']);
        }
        return $layer;
    }

    /**
     * @return string
     */
    public function gatherPushes()
    {
        return $this->event->gatherPushes($this->getPageName());
    }

    /**
     * @return mixed
     */
    public function collectEventPush()
    {
        return $this->event->getEventPushData();
    }
}
