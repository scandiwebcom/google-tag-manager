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
     * General constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param JsonHelper $jsonHelper
     * @param Name $name
     * @param Gtm $gtm
     * @param Config $config
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        JsonHelper $jsonHelper,
        Name $name,
        Gtm $gtm,
        Config $config
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->jsonHelper = $jsonHelper;
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
        $dataLayer = $this->jsonHelper->jsonEncode($this->gtm->dataLayer->collectLayer());
        $result->setData($dataLayer);
        return $result;
    }
}
