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
use Scandi\Gtm\Helper\Collectors\Search as GTMSearch;

class Search extends DataLayerCollector
{

    /**
     * @var GTMSearch
     */
    protected $search;

    /**
     * @var DataLayer
     */
    protected $dataLayer;

    /**
     * Search constructor.
     * @param Context $context
     * @param GTMSearch $search
     * @param DataLayer $dataLayer
     */
    public function __construct(
        Context $context,
        GTMSearch $search,
        DataLayer $dataLayer
    )
    {
        parent::__construct($context);
        $this->search = $search;
        $this->dataLayer = $dataLayer;
    }

    /**
     * @return array
     */
    public function collectLayer()
    {
        $layer = $this->dataLayer->collectLayer();
        $layerEnhanced['ecommerce'] = $this->search->collectSearch();
        return array_merge_recursive($layer, $layerEnhanced);
    }
}
