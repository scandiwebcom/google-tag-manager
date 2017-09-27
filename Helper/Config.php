<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Theme\Block\Html\Pager;

/**
 * Class Config
 * @package Scandi\Gtm\Helper
 */
class Config
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Toolbar
     */
    protected $toolbar;

    /**
     * @var Pager
     */
    protected $pager;

    /**
     * @var CategoryRepository
     */
    protected $category;

    const STORE_SCOPE = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    const XML_PATH_MODULE_IS_ENABLED = 'scandi_gtm/general/enable';
    const XML_PATH_SCRIPT_IN_HEAD = 'scandi_gtm/general/store_in_head';
    const XML_PATH_CONT_ID = 'scandi_gtm/general/container_id';
    const XML_PATH_CATEGORY_SELECTOR = 'scandi_gtm/developer/category_wrapper';
    const XML_PATH_CHECKOUT_STEPS = 'scandi_gtm/developer/checkout_steps';
    const XML_PATH_MAXIMUM_PRODUCTS = 'scandi_gtm/developer/pagesize_limit';
    const XML_PATH_BRAND = 'scandi_gtm/developer/brand';

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Pager $pager
     * @param Toolbar $toolbar
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Pager $pager,
        Toolbar $toolbar
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->toolbar = $toolbar;
        $this->pager = $pager;
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_IS_ENABLED, self::STORE_SCOPE);
    }

    /**
     * @return mixed
     */
    public function injectInHead()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SCRIPT_IN_HEAD, self::STORE_SCOPE);
    }

    /**
     * @return mixed
     */
    public function getContId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CONT_ID, self::STORE_SCOPE);
    }

    /**
     * @return mixed
     */
    public function getImpressionsMaximum()
    {
        $config = $this->scopeConfig->getValue(self::XML_PATH_MAXIMUM_PRODUCTS, self::STORE_SCOPE);
        if ($config) {
            return $config;
        }
        if ($this->pager->getLimit() === $this->toolbar->getLimit()) {
            return $this->pager->getLimit();
        } else if ($this->pager->getLimit() < $this->toolbar->getLimit()) {
            return $this->toolbar->getLimit();
        }
        return $this->pager->getLimit();
    }

    /**
     * @return mixed
     */
    public function getStoreCurrency()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    /**
     * @return mixed
     */
    public function getCategoryWrapper()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CATEGORY_SELECTOR, self::STORE_SCOPE);
    }

    /**
     * @return array
     */
    public function getCheckoutSteps()
    {
        return $this->handleCheckoutSteps($this->scopeConfig->getValue(self::XML_PATH_CHECKOUT_STEPS, self::STORE_SCOPE));
    }

    /**
     * @param $steps
     * @return array
     */
    public function handleCheckoutSteps($steps)
    {
        return explode(',', $steps);
    }

    public function getBrand()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BRAND, self::STORE_SCOPE);
    }
}
