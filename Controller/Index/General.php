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
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Customer\Model\Session as CustomerSession;
use Scandi\Gtm\Block\Gtm;
use Scandi\Gtm\Helper\Config;
use Scandi\Gtm\Helper\Name;

/**
 * Class General
 * @package Scandi\Gtm\Controller\Index
 */
class General extends Action
{

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var Gtm
     */
    protected $gtm;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Config
     */
    protected $config;

    /**
     * General constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param JsonHelper $jsonHelper
     * @param CustomerSession $customerSession
     * @param Name $name
     * @param Gtm $gtm
     * @param Config $config
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        JsonHelper $jsonHelper,
        CustomerSession $customerSession,
        Name $name,
        Gtm $gtm,
        Config $config
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->jsonHelper = $jsonHelper;
        $this->customerSession = $customerSession;
        $this->name = $name;
        $this->gtm = $gtm;
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
            throw new NotFoundException(__('Module is disabled'));
        }
        if (!$this->getRequest()->isAjax()) {
            throw new NotFoundException(__('Usage is incorrect'));
        }
        $result = $this->jsonFactory->create();
        $dataLayer = $this->gtm->dataLayer->collectLayer();

        if ($this->customerSession->getAddToCart()) {
            $event = $this->customerSession->getAddToCart();
            $this->customerSession->unsAddToCart();
        } else if ($this->customerSession->getRemoveFromCart()) {
            $event = $this->customerSession->getRemoveFromCart();
            $this->customerSession->unsRemoveFromCart();
        }
        if (!isset($event)) {
            $result->setData($this->jsonHelper->jsonEncode(array($dataLayer)));
            return $result;
        }

        $result->setData($this->jsonHelper->jsonEncode(array($dataLayer, $this->jsonHelper->jsonDecode($event))));
        return $result;
    }
}
