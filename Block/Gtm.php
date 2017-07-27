<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Scandi\Gtm\Helper\Config;
use Scandi\Gtm\Helper\Script;

class Gtm extends Template
{

    /**
     * @var DataLayer
     */
    public $dataLayer;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Script
     */
    protected $script;

    /**
     * Gtm constructor.
     * @param Context $context
     * @param DataLayer $dataLayer
     * @param Config $configHelper
     * @param Script $script
     */
    public function __construct(
        Context $context,
        DataLayer $dataLayer,
        Config $configHelper,
        Script $script
    )
    {
        parent::__construct($context);
        $this->configHelper = $configHelper;
        $this->script = $script;
        $this->dataLayer = $dataLayer;
    }

    /**
     * @param null $scriptType
     * @return string
     */
    public function injectScript($scriptType = null)
    {
        if ($scriptType === null) {
            return $this->script->buildScript();
        } else {
            return $this->script->buildNoScript();
        }
    }

    /**
     * @param $templateName
     * @return string
     */
    public function getGtm($templateName)
    {
        if (!$this->configHelper->isEnabled()) {
            return '';
        }
        switch ($templateName) {
            case 'head':
                if (!$this->configHelper->injectInHead()) {
                    return '';
                }
                $dataLayer = $this->getChildHtml("gtm_head");
                if (empty($dataLayer)) {
                    $dataLayer = $this->getChildHtml("gtm_head_prime");
                }
                return "<script>var dataLayer = [" . $dataLayer . "];</script>" .
                    $this->dataLayer->gatherPushes() . $this->injectScript();
                break;
            case 'body':
                if ($this->configHelper->injectInHead()) {
                    return '';
                }
                $dataLayer = $this->getChildHtml("gtm_body");
                if (empty($dataLayer)) {
                    $dataLayer = $this->getChildHtml("gtm_body_prime");
                }
                return "<script>var dataLayer = [" . $dataLayer . "];</script>" .
                    $this->dataLayer->gatherPushes() . $this->injectScript();
                break;
            default:
                break;
        }
    }
}
