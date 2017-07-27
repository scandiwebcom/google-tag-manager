<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Block;

use Magento\Framework\View\Element\Template\Context;
use Scandi\Gtm\Block\DataLayer\DataLayerCollector;
use Scandi\Gtm\Helper\Collectors\Success as GTMSuccess;

class Success extends DataLayerCollector
{

    /**
     * @var GTMSuccess
     */
    protected $success;

    /**
     * @var DataLayer
     */
    protected $dataLayer;

    /**
     * Success constructor.
     * @param Context $context
     * @param GTMSuccess $success
     * @param DataLayer $dataLayer
     */
    public function __construct(
        Context $context,
        GTMSuccess $success,
        DataLayer $dataLayer
    )
    {
        parent::__construct($context);
        $this->success = $success;
        $this->dataLayer = $dataLayer;
    }

    /**
     * @return array
     */
    public function collectLayer()
    {
        $layer = $this->dataLayer->collectLayer();
        $layerEnhanced['ecommerce'] = $this->success->collectSuccess();
        return array_merge_recursive($layer, $layerEnhanced);
    }
}