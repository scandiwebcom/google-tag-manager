<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper;

/**
 * Class Script
 * @package Scandi\Gtm\Helper
 */
class Script
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * Script constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function buildScript()
    {
        return $this->config->getScript();
    }

    /**
     * @return string
     */
    public function buildNoScript()
    {
        return $this->config->getNoScript();
    }
}
