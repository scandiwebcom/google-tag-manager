<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Scandi\Gtm\Helper\Config;

/**
 * Class Cart
 * @package Scandi\Gtm\Controller\Index
 */
class Cart extends Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Cart constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param JsonFactory $jsonFactory
     * @param JsonHelper $jsonHelper
     * @param Config $config
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        JsonFactory $jsonFactory,
        JsonHelper $jsonHelper,
        Config $config
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->customerSession = $customerSession;
        $this->jsonHelper = $jsonHelper;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json|null
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->config->isEnabled()) {
            throw new NotFoundException(__('Extension is disabled'));
        }
        if (!$this->getRequest()->isAjax()) {
            throw new NotFoundException(__('Usage is incorrect'));
        }
        if (sizeof($this->getRequest()->getPost()) !== 0) {
            $postData = $this->getRequest()->getPost();
            throw new NotFoundException(__("Injection of data happened. The data was $postData"));
        }
        $result = $this->jsonFactory->create();
        if ($this->customerSession->getAddToCart()) {
            $result->setData($this->customerSession->getAddToCart());
            $this->customerSession->unsAddToCart();
            return $result;
        } else if ($this->customerSession->getRemoveFromCart()) {
            $result->setData($this->customerSession->getRemoveFromCart());
            $this->customerSession->unsRemoveFromCart();
            return $result;
        }
        return null;
    }
}
