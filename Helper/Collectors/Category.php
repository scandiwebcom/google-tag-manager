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
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Registry;
use Magento\Theme\Block\Html\Pager;
use Scandi\Gtm\Helper\Config;
use Scandi\Gtm\Helper\Price;

/**
 * Class Category
 * @package Scandi\Gtm\Helper\Collectors
 */
class Category
{

    /**
     * @var ListProduct
     */
    protected $listProduct;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Pager
     */
    protected $pager;

    /**
     * @var CategoryRepository
     */
    protected $category;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Category constructor.
     * @param ListProduct $listProduct
     * @param Registry $registry
     * @param Pager $pager
     * @param CategoryRepository $category
     * @param Config $config
     * @param Price $price
     */
    public function __construct(
        ListProduct $listProduct,
        Registry $registry,
        Pager $pager,
        CategoryRepository $category,
        Config $config,
        Price $price
    )
    {
        $this->listProduct = $listProduct;
        $this->registry = $registry;
        $this->config = $config;
        $this->pager = $pager;
        $this->category = $category;
        $this->price = $price;
    }

    /**
     * @return array|null
     */
    public function createImpressions()
    {
        $pageType = 'category';
        $products = $this->listProduct->getLoadedProductCollection();
        $products->setPageSize($this->config->getImpressionsMaximum())
            ->setCurPage($this->pager->getCurrentPage());
        $categoryName = $this->registry->registry('current_category')->getName();
        if (count($products->getAllIds()) === 0) {
            return null;
        }
        $i = 1;
        foreach ($products as $product) {
            $impressions[] = [
                "id" => $product->getSku(),
                "name" => $product->getName(),
                "price" => $this->price->collectProductPrice($product),
                "category" => $categoryName,
                "position" => $i,
                "list" => $pageType
            ];
            $i++;
        }
        return isset($impressions) ? $impressions : null;
    }

    /**
     * @param $ids
     * @return bool|string
     */
    public function getCategoryName($ids)
    {
        $categories = '';
        foreach ($ids as $id) {
            $categories .= $this->category->get($id)->getName() . ', ';
        }
        return substr($categories, 0, -2);
    }
}
