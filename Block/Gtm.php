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
use Magento\Framework\App\Request\Http;
use Magento\Theme\Block\Html\Header\Logo;

/**
 * Class Gtm
 * @package Scandi\Gtm\Block
 */
class Gtm extends Template
{

    /**
     * @var Http
     */
    public $request;

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
     * @var Logo
     */
    public $logo;

    /**
     * Gtm constructor.
     * @param Context $context
     * @param DataLayer $dataLayer
     * @param Config $configHelper
     * @param Script $script
     * @param Http $request
     * @param Logo $logo
     */
    public function __construct(
        Context $context,
        DataLayer $dataLayer,
        Config $configHelper,
        Script $script,
        Http $request,
        Logo $logo
    )
    {
        parent::__construct($context);
        $this->configHelper = $configHelper;
        $this->script = $script;
        $this->dataLayer = $dataLayer;
        $this->request = $request;
        $this->logo = $logo;
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
                return "<script>var dataLayer = [];</script>" .
                    $this->injectScript();
                break;
            case 'body':
                if ($this->configHelper->injectInHead()) {
                    return '';
                }
                return "<script>var dataLayer = [];</script>" .
                    $this->injectScript();
                break;
            default:
                break;
        }
    }
}
