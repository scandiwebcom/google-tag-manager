<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper\Collectors;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Framework\App\Request\Http;
use Magento\Search\Helper\Data;
use Magento\Theme\Block\Html\Pager;
use Scandi\Gtm\Helper\Config;
use Scandi\Gtm\Helper\Price;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Search
{

    /**
     * @var ListProduct
     */
    protected $listProduct;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var Pager
     */
    protected $pager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Data
     */
    protected $searchHelper;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    const PAGE_TYPE = 'Search Result';

    /**
     * Search constructor.
     * @param ListProduct $listProduct
     * @param Pager $pager
     * @param Data $searchHelper
     * @param Price $price
     * @param Config $config
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        ListProduct $listProduct,
        Pager $pager,
        Data $searchHelper,
        Price $price,
        Config $config,
        JsonHelper $jsonHelper
    )
    {
        $this->listProduct = $listProduct;
        $this->pager = $pager;
        $this->searchHelper = $searchHelper;
        $this->price = $price;
        $this->config = $config;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @return string
     */
    public function getKeywordPush()
    {
        $search['ecommerce']['keyword'] = $this->searchHelper->getEscapedQueryText();
        return "<script>dataLayer.push(" . $this->jsonHelper->jsonEncode($search) . ")</script>";
    }

    /**
     * @return array|bool
     */
    public function createImpressions()
    {
        $products = $this->listProduct->getLoadedProductCollection();
        $products->setPageSize($this->config->getImpressionsMaximum())
            ->setCurPage($this->pager->getCurrentPage());
        $i = 1;
        //TODO implement push of all data
        foreach ($products as $key => $product) {
            $impressions[] = [
                "id" => $product->getSku(),
                "name" => $product->getName(),
                "price" => $this->price->collectProductPrice($product),
                "list" => self::PAGE_TYPE,
                "position" => $i
            ];
            $i++;
        }
        return isset($impressions) ? $impressions : null;
    }
}
