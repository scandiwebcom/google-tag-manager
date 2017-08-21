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
use Scandi\Gtm\Helper\Collectors\Product as GTMProduct;

class Product extends DataLayerCollector
{

    /**
     * @var GTMProduct
     */
    protected $product;

    /**
     * @var DataLayer
     */
    protected $dataLayer;

    /**
     * Product constructor.
     * @param Context $context
     * @param GTMProduct $product
     * @param DataLayer $dataLayer
     */
    public function __construct(
        Context $context,
        GTMProduct $product,
        DataLayer $dataLayer
    )
    {
        parent::__construct($context);
        $this->product = $product;
        $this->dataLayer = $dataLayer;
    }

    /**
     * @return mixed
     */
    public function collectLayer()
    {
        $layer = $this->dataLayer->collectLayer();
//        $layer['ecommerce']['details'] = $this->product->createDetails();
        return $layer;
    }
}
