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
use Magento\Search\Helper\Data;
use \Magento\Framework\Locale\Resolver as LocaleResolver;

/**
 * Class DataLayer
 * @package Scandi\Gtm\Block
 */
class DataLayer extends DataLayerCollector
{

    const GENERAL_EVENT_NAME = 'general';

    /**
     * @var Context
     */
    protected $context;

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
     * @var LocaleResolver
     */
    private $localeResolver;

    /**
     * DataLayer constructor.
     * @param Context $context
     * @param Http $request
     * @param Name $nameHelper
     * @param Session $customerSession
     * @param Customer $customerHelper
     * @param Cart $cart
     * @param Event $event
     * @param LocaleResolver $localeResolver
     */
    public function __construct(
        Context $context,
        Http $request,
        Name $nameHelper,
        Session $customerSession,
        Customer $customerHelper,
        Cart $cart,
        Event $event,
        LocaleResolver $localeResolver
    )
    {
        parent::__construct($context);
        $this->request = $request;
        $this->nameHelper = $nameHelper;
        $this->customerSession = $customerSession;
        $this->customerHelper = $customerHelper;
        $this->cart = $cart;
        $this->event = $event;
        $this->context = $context;
        $this->localeResolver = $localeResolver;
    }

    /**
     * @return mixed|string
     */
    private function getPageName()
    {
        $frontAction = $this->request->getFullActionName();
        if ($frontAction === 'gtm_index_general') {
            return $this->nameHelper->getEccomPageName($this->getRequest()->getParam('page'),
                $this->getRequest()->getParam('isMain'));
        }
        return $this->nameHelper->getEccomPageName($frontAction);
    }

    /**
     * @return mixed
     */
    public function collectLayer()
    {
        $id = $this->customerHelper->getCustomerId($this->customerSession);
        $layer['pageType'] = $this->getPageName();
        $layer['storeView'] = $this->context->getStoreManager()->getStore()->getCode();
        $layer['language'] = $this->localeResolver->getLocale();
        $layer['event'] = $this::GENERAL_EVENT_NAME;
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
