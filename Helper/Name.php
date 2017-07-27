<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper;

use Magento\Theme\Block\Html\Header\Logo;

class Name
{
    /**
     * @var Logo
     */
    protected $logo;

    /**
     * Name constructor.
     * @param Logo $logo
     */
    public function __construct(
        Logo $logo
    )
    {
        $this->logo = $logo;
    }

    /**
     * @param $pageName
     * @return mixed|string
     */
    public function getEccomPageName($pageName)
    {
        $pageNames = [
            'cms_index_index' => 'homepage',
            'catalog_category_view' => 'category',
            'catalog_product_view' => 'product',
            'checkout_index_index' => 'checkout',
            'customer_account_login' => 'login',
            'checkout_onepage_success' => 'success',
            'catalogsearch_result_index' => 'search_result',
            'checkout_cart_index' => 'cart'
        ];
        return $this->isPageInList($pageNames, $pageName) ? $pageNames[$pageName] : 'default';
    }

    /**
     * @param $pageNames
     * @param $pageName
     * @return bool
     */
    private function isPageInList($pageNames, $pageName)
    {
        if (!array_key_exists($pageName, $pageNames)) {
            return false;
        }
        return $pageName !== 'cms_index_index' || $this->logo->isHomePage();
    }
}
