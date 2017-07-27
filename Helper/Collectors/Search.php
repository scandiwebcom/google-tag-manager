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
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Search\Helper\Data;
use Magento\Theme\Block\Html\Pager;
use Scandi\Gtm\Helper\Price;

class Search
{

    /**
     * @var ListProduct
     */
    protected $listProduct;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var Pager
     */
    protected $pager;

    /**
     * @var Toolbar
     */
    protected $toolbar;

    /**
     * @var Data
     */
    protected $searchHelper;

    /**
     * @var Price
     */
    protected $price;

    const PAGE_TYPE = 'Search Result';

    /**
     * Search constructor.
     * @param ListProduct $listProduct
     * @param Pager $pager
     * @param Toolbar $toolbar
     * @param Data $searchHelper
     * @param Price $price
     */
    public function __construct(
        ListProduct $listProduct,
        Pager $pager,
        Toolbar $toolbar,
        Data $searchHelper,
        Price $price
    )
    {
        $this->listProduct = $listProduct;
        $this->pager = $pager;
        $this->toolbar = $toolbar;
        $this->searchHelper = $searchHelper;
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function collectSearch()
    {
        $search['keyword'] = $this->searchHelper->getEscapedQueryText();
        return $search;
    }

    /**
     * @return array|bool
     */
    public function createImpressions()
    {
        $products = $this->listProduct->getLoadedProductCollection();
        $products->setPageSize($this->getLimit())
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
        return isset($impressions) ? $impressions : false;
    }

    /**
     * @return int|string
     */
    private function getLimit()
    {
        if ($this->pager->getLimit() === $this->toolbar->getLimit()) {
            return $this->pager->getLimit();
        } else if ($this->pager->getLimit() < $this->toolbar->getLimit()) {
            return $this->toolbar->getLimit();
        }
        return $this->pager->getLimit();
    }
}
