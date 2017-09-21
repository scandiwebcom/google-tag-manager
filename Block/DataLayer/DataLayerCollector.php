<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Block\DataLayer;

use Magento\Framework\View\Element\Template;

/**
 * Class DataLayerCollector
 * @package Scandi\Gtm\Block\DataLayer
 */
abstract class DataLayerCollector extends Template
{
    /**
     * @return mixed
     */
    abstract public function collectLayer();
}