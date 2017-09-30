<?php

/**
 * @category ScandiModule
 * @package ScandiModule\Gtm
 * @author Oleksii Tsebinoga <aleksejt@scandiweb.com>
 * @copyright Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace Scandi\Gtm\Helper\Collectors;

use Magento\Customer\Model\Session;
use Scandi\Gtm\Helper\Collectors\Product as GTMProduct;

/**
 * Class Event
 * @package Scandi\Gtm\Helper\Collectors
 */
class Event
{

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Search
     */
    protected $search;

    /**
     * @var Checkout
     */
    protected $checkout;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var GTMProduct
     */
    protected $product;

    /**
     * @var Success
     */
    protected $success;

    /**
     * Value of imressions per push from the GTM documentation
     */
    const IMPRESSIONS_LIMIT = 20;

    /**
     * Event constructor.
     * @param Session $customerSession
     * @param Category $category
     * @param Search $search
     * @param Checkout $checkout
     * @param Product $product
     * @param Success $success
     */
    public function __construct(
        Session $customerSession,
        Category $category,
        Search $search,
        Checkout $checkout,
        GTMProduct $product,
        Success $success
    )
    {
        $this->customerSession = $customerSession;
        $this->category = $category;
        $this->search = $search;
        $this->checkout = $checkout;
        $this->product = $product;
        $this->success = $success;
    }

    /**
     * Method to handle all possible pushes
     * @param null $pageName
     * @return string
     */
    public function gatherPushes($pageName = null)
    {
        $pushes = "<script>dataLayer.push(" . $this->getEventPushData() . ")</script>";
        switch ($pageName) {
            case 'category':
                $impressionsPush = $this->category->createImpressions();
                break;
            case 'search_result':
                $impressionsPush = $this->search->createImpressions();
                $keywordPush = $this->search->getKeyWordPush();
                break;
            case 'checkout':
                $pushes .= $this->checkout->getCheckoutSteps();
                $pushes .= $this->checkout->getOptionWrappers();
                $pushes .= $this->checkout->getCart();
                break;
            case 'product':
                $pushes .= $this->product->createDetails();
                break;
            case 'success':
                $pushes .= $this->success->collectSuccess();
                break;
            default:
                return $pushes;
        }
        if (isset($impressionsPush)) {
            $pushes .= $this->handleImpressions($impressionsPush);
        }
        if (isset($keywordPush)) {
            $pushes .= $keywordPush;
        }
        return $pushes;
    }

    /**
     * @return bool
     */
    public function getEventPushData()
    {
        if ($this->customerSession->getAddToCart()) {
            $addPush = $this->customerSession->getAddToCart();
            $this->customerSession->unsAddToCart();
            return $addPush;
        } else if ($this->customerSession->getRemoveFromCart()) {
            $removePush = $this->customerSession->getRemoveFromCart();
            $this->customerSession->unsRemoveFromCart();
            return $removePush;
        }
        return false;
    }

    /**
     * @param $impressions
     * @return string
     */
    public function handleImpressions($impressions)
    {
        $pushes = '';
        $chunkedForPush = array_chunk($impressions, self::IMPRESSIONS_LIMIT);
        foreach ($chunkedForPush as $push) {
            $tmp['ecommerce']['impressions'] = $push;
            $tmp['ecommerce']['event'] = 'impressions';
            $push = json_encode($tmp);
            $pushes .= "<script>dataLayer.push($push)</script>";
        }
        return $pushes;
    }
}
