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
     * Event constructor.
     * @param Session $customerSession
     * @param Category $category
     * @param Search $search
     * @param Checkout $checkout
     */
    public function __construct(
        Session $customerSession,
        Category $category,
        Search $search,
        Checkout $checkout
    )
    {
        $this->customerSession = $customerSession;
        $this->category = $category;
        $this->search = $search;
        $this->checkout = $checkout;
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
                break;
            case 'checkout':
                $pushes .= $this->checkout->getCheckoutSteps();
                break;
            default:
                return $pushes;
        }
        if (isset($impressionsPush)) {
            $pushes .= $this->handleImpressions($impressionsPush);
        }
        return $pushes;
    }

    /**
     * @param $impressions
     * @return string
     */
    public function handleImpressions($impressions)
    {
        //Limited value of impressions in one push
        $limit = 50;
        $pushes = '';
        $chunkedForPush = array_chunk($impressions, $limit);
        foreach ($chunkedForPush as $push) {
            $tmp['ecommerce']['impressions'] = $push;
            $push = json_encode($tmp);
            $pushes .= "<script>dataLayer.push($push)</script>";
        }
        return $pushes;
    }
}